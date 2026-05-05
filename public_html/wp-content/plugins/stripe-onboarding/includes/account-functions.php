<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * ACCOUNT FUNCTIONS
 * =========================
 */

function sa_get_account_orders_sorted($user_id) {

    $orders = sa_get_user_paid_orders($user_id);

    if (empty($orders)) {
        return [];
    }

    usort($orders, function ($a, $b) use ($user_id) {

        $state_a = sa_get_order_state($user_id, (int) $a->id);
        $state_b = sa_get_order_state($user_id, (int) $b->id);

        $prio_a = sa_get_order_state_priority($state_a);
        $prio_b = sa_get_order_state_priority($state_b);

        if ($prio_a === $prio_b) {
            return ((int) $b->id) <=> ((int) $a->id);
        }

        return $prio_a <=> $prio_b;
    });

    return $orders;
}

function sa_render_account_no_project() {
    return sa_render_template('account-no-project');
}

function sa_render_account_projects($orders, $user_id) {
    return sa_render_template('account-projects', [
        'orders'  => $orders,
        'user_id' => $user_id
    ]);
}

function sa_render_account_site_live($order, $user_id) {
    return sa_render_template('account-site-live', [
        'order'   => $order,
        'user_id' => $user_id
    ]);
}

function sa_render_account_subscription() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à votre abonnement.</p>';
    }

    $user_id = get_current_user_id();
    $order   = sa_get_current_order_from_session_or_user();

    if (!$order) {
        return '<p>Aucun abonnement trouvé.</p>';
    }

    if ((int) $order->wp_user_id !== (int) $user_id) {
        return '<p>Accès refusé.</p>';
    }

    $subscription_id = !empty($order->stripe_subscription_id)
        ? sanitize_text_field($order->stripe_subscription_id)
        : '';

    if (!$subscription_id) {
        return '<p>Aucun identifiant d’abonnement Stripe trouvé pour cette commande.</p>';
    }

    $subscription = sa_get_stripe_subscription($subscription_id);

    if (!$subscription || is_wp_error($subscription)) {
        return '<p>Impossible de récupérer les données de l’abonnement Stripe.</p>';
    }

    $invoices = sa_get_all_account_billing_documents($subscription_id, 100);

    if (!is_array($invoices)) {
        $invoices = [];
    }

    $extra_local_docs = [];
    if (function_exists('sa_get_order_extra_modification_documents_for_account')) {
        $extra_local_docs = sa_get_order_extra_modification_documents_for_account($user_id, (int) $order->id);
    }

    if (!is_array($extra_local_docs)) {
        $extra_local_docs = [];
    }

    $invoices = array_merge($invoices, $extra_local_docs);

    $deduped = [];
    $seen    = [];

    foreach ($invoices as $doc) {
        $key = !empty($doc['stripe_id'])
            ? 'stripe_' . $doc['stripe_id']
            : md5(wp_json_encode($doc));

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $deduped[]  = $doc;
    }

    usort($deduped, function ($a, $b) {
        return ((int) ($b['created'] ?? 0)) <=> ((int) ($a['created'] ?? 0));
    });

    $invoices = $deduped;

    return sa_render_template('account-subscription', [
        'order'        => $order,
        'user_id'      => $user_id,
        'subscription' => $subscription,
        'invoices'     => $invoices,
    ]);
}

function sa_render_account_invoices() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à vos factures.</p>';
    }

    $user_id = get_current_user_id();
    $order   = sa_get_current_order_from_session_or_user();

    if (!$order) {
        return '<p>Aucune commande trouvée.</p>';
    }

    if ((int) $order->wp_user_id !== (int) $user_id) {
        return '<p>Accès refusé.</p>';
    }

    $subscription_id = !empty($order->stripe_subscription_id)
        ? sanitize_text_field($order->stripe_subscription_id)
        : '';

    $subscription = $subscription_id ? sa_get_stripe_subscription($subscription_id) : false;
    $all_invoices = [];

    if ($subscription_id) {
        $all_invoices = sa_get_all_account_billing_documents($subscription_id, 100);
    }

    if (!is_array($all_invoices)) {
        $all_invoices = [];
    }

    $extra_local_docs = [];
    if (function_exists('sa_get_order_extra_modification_documents_for_account')) {
        $extra_local_docs = sa_get_order_extra_modification_documents_for_account($user_id, (int) $order->id);
    }

    if (!is_array($extra_local_docs)) {
        $extra_local_docs = [];
    }

    $all_invoices = array_merge($all_invoices, $extra_local_docs);

    $deduped = [];
    $seen    = [];

    foreach ($all_invoices as $doc) {
        $key = !empty($doc['stripe_id'])
            ? 'stripe_' . $doc['stripe_id']
            : md5(wp_json_encode($doc));

        if (isset($seen[$key])) {
            continue;
        }

        $seen[$key] = true;
        $deduped[]  = $doc;
    }

    usort($deduped, function ($a, $b) {
        return ((int) ($b['created'] ?? 0)) <=> ((int) ($a['created'] ?? 0));
    });

    $all_invoices = $deduped;

    $per_page       = 10;
    $current_page   = isset($_GET['factures_page']) ? max(1, (int) $_GET['factures_page']) : 1;
    $total_items    = count($all_invoices);
    $total_pages    = max(1, (int) ceil($total_items / $per_page));
    $current_page   = min($current_page, $total_pages);
    $offset         = ($current_page - 1) * $per_page;
    $paged_invoices = array_slice($all_invoices, $offset, $per_page);

    return sa_render_template('account-invoices', [
        'order'          => $order,
        'user_id'        => $user_id,
        'subscription'   => (!$subscription || is_wp_error($subscription)) ? false : $subscription,
        'all_invoices'   => $all_invoices,
        'invoices'       => $paged_invoices,
        'current_page'   => $current_page,
        'total_pages'    => $total_pages,
        'total_items'    => $total_items,
        'per_page'       => $per_page,
    ]);
}

function sa_render_account_dashboard() {

    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à votre espace.</p>';
    }

    $user_id = get_current_user_id();
    $orders  = sa_get_account_orders_sorted($user_id);

    if (empty($orders)) {
        return sa_render_account_no_project();
    }

    $primary_order = $orders[0];
    $primary_state = sa_get_order_state($user_id, (int) $primary_order->id);

    if (in_array($primary_state, ['site_live', 'paused'], true)) {
        return sa_render_account_site_live($primary_order, $user_id);
    }

    return sa_render_account_projects($orders, $user_id);
}

function sa_render_buy_extra_modification() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à cette page.</p>';
    }

    return sa_render_template('buy-extra-modification');
}

function sa_render_account_contact($content = '') {
    return sa_render_template('account-contact', [
        'content' => $content,
    ]);
}