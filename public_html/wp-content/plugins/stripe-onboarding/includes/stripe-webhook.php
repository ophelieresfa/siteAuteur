<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * HELPERS
 * =========================
 */

function sa_get_latest_user_order_id_for_webhook($user_id) {
    if (!$user_id || !function_exists('sa_get_user_paid_orders')) {
        return 0;
    }

    if (function_exists('sa_get_account_orders_sorted')) {
        $orders = sa_get_account_orders_sorted($user_id);
    } else {
        $orders = sa_get_user_paid_orders($user_id);
    }

    if (empty($orders) || !is_array($orders)) {
        return 0;
    }

    foreach ($orders as $order) {
        if (!empty($order->id)) {
            return (int) $order->id;
        }
    }

    return 0;
}

function sa_has_processed_stripe_checkout_session($session_id) {
    if (!$session_id) {
        return true;
    }

    return (bool) get_option('sa_processed_checkout_' . $session_id, false);
}

function sa_mark_stripe_checkout_session_processed($session_id) {
    if (!$session_id) {
        return;
    }

    update_option('sa_processed_checkout_' . $session_id, time(), false);
}

function sa_get_order_by_stripe_subscription_id_for_webhook($subscription_id, $customer_id = '', $order_id = 0, $user_id = 0) {
    global $wpdb;

    $table = $wpdb->prefix . 'siteauteur_orders';

    $subscription_id = sanitize_text_field($subscription_id);
    $customer_id     = sanitize_text_field($customer_id);
    $order_id        = absint($order_id);
    $user_id         = absint($user_id);

    /*
     * 1. Recherche la plus fiable : metadata Stripe sa_order_id.
     */
    if ($order_id) {
        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE id = %d LIMIT 1",
                $order_id
            )
        );

        if ($order) {
            return $order;
        }
    }

    /*
     * 2. Recherche normale par ID d'abonnement Stripe.
     */
    if ($subscription_id) {
        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table} WHERE stripe_subscription_id = %s LIMIT 1",
                $subscription_id
            )
        );

        if ($order) {
            return $order;
        }
    }

    /*
     * 3. Fallback par client Stripe.
     */
    if ($customer_id) {
        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 WHERE stripe_customer_id = %s
                 ORDER BY id DESC
                 LIMIT 1",
                $customer_id
            )
        );

        if ($order) {
            return $order;
        }
    }

    /*
     * 4. Dernier fallback par utilisateur, si Stripe a la metadata sa_user_id.
     */
    if ($user_id) {
        $order = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table}
                 WHERE wp_user_id = %d
                 ORDER BY id DESC
                 LIMIT 1",
                $user_id
            )
        );

        if ($order) {
            return $order;
        }
    }

    error_log('[SiteAuteur Stripe Webhook] Aucune commande trouvée. subscription_id=' . $subscription_id . ' customer_id=' . $customer_id . ' order_id=' . $order_id . ' user_id=' . $user_id);

    return false;
}

function sa_get_webhook_subscription_context($subscription_object) {
    $subscription_id = !empty($subscription_object->id)
        ? sanitize_text_field($subscription_object->id)
        : '';

    $customer_id = !empty($subscription_object->customer)
        ? sanitize_text_field($subscription_object->customer)
        : '';

    $order_id = 0;
    $user_id  = 0;

    if (!empty($subscription_object->metadata)) {
        if (!empty($subscription_object->metadata->sa_order_id)) {
            $order_id = absint($subscription_object->metadata->sa_order_id);
        }

        if (!empty($subscription_object->metadata->sa_user_id)) {
            $user_id = absint($subscription_object->metadata->sa_user_id);
        }
    }

    return [
        'subscription_id' => $subscription_id,
        'customer_id'     => $customer_id,
        'order_id'        => $order_id,
        'user_id'         => $user_id,
    ];
}

function sa_pause_order_after_subscription_stopped($subscription_id, $customer_id = '', $order_id = 0, $user_id = 0) {
    $order = sa_get_order_by_stripe_subscription_id_for_webhook($subscription_id, $customer_id, $order_id, $user_id);

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        error_log('[SiteAuteur Stripe Webhook] Suspension impossible : commande introuvable. subscription_id=' . $subscription_id . ' customer_id=' . $customer_id . ' order_id=' . $order_id . ' user_id=' . $user_id);
        return false;
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    sa_update_order_meta($user_id, $order_id, 'state', 'paused');
    sa_update_order_meta($user_id, $order_id, 'subscription_stopped_at', time());
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 0);

    $already_sent = sa_get_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', 0);

    if ($already_sent) {
        error_log('[SiteAuteur Stripe Webhook] Mail suspension déjà envoyé. user_id=' . $user_id . ' order_id=' . $order_id);
        return true;
    }

    if (!function_exists('sa_send_project_really_suspended_email')) {
        error_log('[SiteAuteur Stripe Webhook] Fonction sa_send_project_really_suspended_email introuvable.');
        return false;
    }

    $sent = sa_send_project_really_suspended_email($user_id, $order_id);

    if ($sent) {
        sa_update_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', time());
        error_log('[SiteAuteur Stripe Webhook] Mail suspension envoyé. user_id=' . $user_id . ' order_id=' . $order_id);
        return true;
    }

    error_log('[SiteAuteur Stripe Webhook] Échec wp_mail suspension. user_id=' . $user_id . ' order_id=' . $order_id);

    return false;
}

function sa_mark_order_after_subscription_cancel_scheduled($subscription_id, $customer_id = '', $order_id = 0, $user_id = 0) {
    $order = sa_get_order_by_stripe_subscription_id_for_webhook($subscription_id, $customer_id, $order_id, $user_id);

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        error_log('[SiteAuteur Stripe Webhook] Résiliation programmée impossible : commande introuvable. subscription_id=' . $subscription_id . ' customer_id=' . $customer_id . ' order_id=' . $order_id . ' user_id=' . $user_id);
        return false;
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    sa_update_order_meta($user_id, $order_id, 'state', 'paused');
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_requested_at', time());
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 1);

    $already_sent = sa_get_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);

    if ($already_sent) {
        error_log('[SiteAuteur Stripe Webhook] Mail résiliation programmée déjà envoyé. user_id=' . $user_id . ' order_id=' . $order_id);
        return true;
    }

    if (!function_exists('sa_send_subscription_cancel_scheduled_email')) {
        error_log('[SiteAuteur Stripe Webhook] Fonction sa_send_subscription_cancel_scheduled_email introuvable.');
        return false;
    }

    $sent = sa_send_subscription_cancel_scheduled_email($user_id, $order_id);

    if ($sent) {
        sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', time());
        error_log('[SiteAuteur Stripe Webhook] Mail résiliation programmée envoyé. user_id=' . $user_id . ' order_id=' . $order_id);
        return true;
    }

    error_log('[SiteAuteur Stripe Webhook] Échec wp_mail résiliation programmée. user_id=' . $user_id . ' order_id=' . $order_id);

    return false;
}

function sa_mark_order_after_subscription_resumed_from_stripe($subscription_id, $customer_id = '', $order_id = 0, $user_id = 0) {
    $order = sa_get_order_by_stripe_subscription_id_for_webhook($subscription_id, $customer_id, $order_id, $user_id);

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        error_log('[SiteAuteur Stripe Webhook] Reprise impossible : commande introuvable. subscription_id=' . $subscription_id . ' customer_id=' . $customer_id . ' order_id=' . $order_id . ' user_id=' . $user_id);
        return false;
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    sa_update_order_meta($user_id, $order_id, 'state', 'site_live');
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', 0);

    return true;
}

function sa_get_stripe_webhook_secret_for_request() {
    if (!defined('SA_STRIPE_SECRET_KEY')) {
        return '';
    }

    if (strpos(SA_STRIPE_SECRET_KEY, 'sk_live_') === 0) {
        return defined('SA_STRIPE_WEBHOOK_SECRET_LIVE') ? SA_STRIPE_WEBHOOK_SECRET_LIVE : '';
    }

    return defined('SA_STRIPE_WEBHOOK_SECRET_TEST') ? SA_STRIPE_WEBHOOK_SECRET_TEST : '';
}

function sa_verify_stripe_webhook_signature($payload, $signature_header, $secret) {
    if (!$payload || !$signature_header || !$secret) {
        return false;
    }

    $parts = [];

    foreach (explode(',', $signature_header) as $item) {
        $pair = explode('=', trim($item), 2);

        if (count($pair) === 2) {
            $parts[$pair[0]][] = $pair[1];
        }
    }

    if (empty($parts['t'][0]) || empty($parts['v1'])) {
        return false;
    }

    $timestamp          = $parts['t'][0];
    $signed_payload     = $timestamp . '.' . $payload;
    $expected_signature = hash_hmac('sha256', $signed_payload, $secret);

    $is_valid = false;

    foreach ($parts['v1'] as $signature) {
        if (hash_equals($expected_signature, $signature)) {
            $is_valid = true;
            break;
        }
    }

    if (!$is_valid) {
        return false;
    }

    /*
     * Tolérance de 5 minutes.
     */
    if (abs(time() - (int) $timestamp) > 300) {
        return false;
    }

    return true;
}

/**
 * =========================
 * WEBHOOK
 * =========================
 */

add_action('init', function () {
    if (!isset($_GET['sa_stripe_webhook'])) {
        return;
    }

    $payload = @file_get_contents('php://input');

    $signature_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE'])
        ? wp_unslash($_SERVER['HTTP_STRIPE_SIGNATURE'])
        : '';

    $secret = sa_get_stripe_webhook_secret_for_request();

    if (!sa_verify_stripe_webhook_signature($payload, $signature_header, $secret)) {
        status_header(400);
        echo 'Invalid Stripe signature';
        exit;
    }

    $event = json_decode($payload);

    if (!$event || empty($event->type)) {
        status_header(400);
        echo 'Invalid payload';
        exit;
    }

    /*
     * =========================
     * ABONNEMENT MODIFIÉ / ANNULÉ
     * =========================
     *
     * Cas gérés :
     * - Résiliation programmée depuis Stripe
     * - Résiliation immédiate depuis Stripe
     * - Fin réelle de l'abonnement
     */
    if (in_array($event->type, ['customer.subscription.deleted', 'customer.subscription.updated'], true)) {
        $subscription_object = $event->data->object ?? null;

        if (!$subscription_object || empty($subscription_object->id)) {
            status_header(200);
            echo 'No subscription';
            exit;
        }

        $subscription_id = sanitize_text_field($subscription_object->id);

        $status = !empty($subscription_object->status)
            ? sanitize_text_field($subscription_object->status)
            : '';

        $cancel_at_period_end = !empty($subscription_object->cancel_at_period_end);

        $stopped_statuses = ['canceled', 'unpaid', 'paused', 'incomplete_expired'];

        /*
        * Cas 1 :
        * L'abonnement est réellement terminé.
        * Exemple : annulation immédiate dans Stripe.
        */
        if (in_array($status, $stopped_statuses, true)) {
            sa_pause_order_after_subscription_stopped($subscription_id);

            status_header(200);
            echo 'Order paused and suspended email handled';
            exit;
        }

        /*
        * Cas 2 :
        * La résiliation est programmée.
        * Exemple : annulation à la fin de la période depuis Stripe.
        */
        if ($cancel_at_period_end) {
            sa_mark_order_after_subscription_cancel_scheduled($subscription_id);

            status_header(200);
            echo 'Cancel scheduled email handled';
            exit;
        }

        /*
        * Cas 3 :
        * L’abonnement est actif et aucune résiliation n’est programmée.
        * Cela arrive quand tu annules une résiliation depuis Stripe.
        * On remet donc les marqueurs email à zéro pour permettre un futur email.
        */
        if (in_array($status, ['active', 'trialing', 'past_due'], true) && !$cancel_at_period_end) {
            sa_mark_order_after_subscription_resumed_from_stripe($subscription_id, $customer_id, $order_id, $user_id);

            status_header(200);
            echo 'Subscription active and cancel markers reset';
            exit;
        }

        status_header(200);
        echo 'Subscription not stopped';
        exit;
    }

    /*
     * Les autres traitements de ce fichier concernent uniquement
     * les sessions Checkout terminées.
     */
    if ($event->type !== 'checkout.session.completed') {
        status_header(200);
        echo 'Ignored';
        exit;
    }

    $session = $event->data->object ?? null;

    if (!$session) {
        status_header(200);
        echo 'No session';
        exit;
    }

    $session_id     = !empty($session->id) ? sanitize_text_field($session->id) : '';
    $mode           = !empty($session->mode) ? sanitize_text_field($session->mode) : '';
    $payment_status = !empty($session->payment_status) ? sanitize_text_field($session->payment_status) : '';
    $subscription   = !empty($session->subscription) ? sanitize_text_field($session->subscription) : '';

    if (sa_has_processed_stripe_checkout_session($session_id)) {
        status_header(200);
        echo 'Already processed';
        exit;
    }

    $metadata = !empty($session->metadata) ? $session->metadata : null;

    $purchase_type = !empty($metadata->sa_purchase_type)
        ? sanitize_text_field($metadata->sa_purchase_type)
        : '';

    /**
     * =========================
     * RÉACTIVATION ABONNEMENT ANNULÉ
     * =========================
     */
    if ($mode === 'subscription' && $purchase_type === 'resume_canceled_subscription' && !empty($subscription)) {
        $user_id  = !empty($metadata->sa_user_id) ? (int) $metadata->sa_user_id : 0;
        $order_id = !empty($metadata->sa_order_id) ? (int) $metadata->sa_order_id : 0;

        if ($user_id <= 0) {
            $email = $session->customer_details->email ?? '';

            if ($email) {
                $user = get_user_by('email', $email);

                if ($user) {
                    $user_id = (int) $user->ID;
                }
            }
        }

        if ($user_id <= 0) {
            status_header(200);
            echo 'No user';
            exit;
        }

        if ($order_id <= 0) {
            $order_id = sa_get_latest_user_order_id_for_webhook($user_id);
        }

        if ($order_id <= 0) {
            status_header(200);
            echo 'No order';
            exit;
        }

        sa_update_order_after_resume_canceled_subscription($user_id, $order_id, $session);
        sa_mark_stripe_checkout_session_processed($session_id);

        status_header(200);
        echo 'Subscription resumed';
        exit;
    }

    /**
     * =========================
     * ACHAT MODIFICATION SUPPLÉMENTAIRE
     * =========================
     *
     * Tous les autres cas doivent rester réservés aux achats de modifications.
     */
    if ($mode !== 'payment' || $payment_status !== 'paid' || !empty($subscription)) {
        status_header(200);
        echo 'Ignored';
        exit;
    }

    $metadata = !empty($session->metadata) ? $session->metadata : null;

    $purchase_type = !empty($metadata->sa_purchase_type)
        ? sanitize_text_field($metadata->sa_purchase_type)
        : '';

    if ($purchase_type !== 'extra_modification') {
        status_header(200);
        echo 'Ignored';
        exit;
    }

    $user_id  = !empty($metadata->sa_user_id) ? (int) $metadata->sa_user_id : 0;
    $order_id = !empty($metadata->sa_order_id) ? (int) $metadata->sa_order_id : 0;

    if ($user_id <= 0) {
        $email = $session->customer_details->email ?? '';

        if ($email) {
            $user = get_user_by('email', $email);

            if ($user) {
                $user_id = (int) $user->ID;
            }
        }
    }

    if ($user_id <= 0) {
        status_header(200);
        echo 'No user';
        exit;
    }

    if ($order_id <= 0) {
        $order_id = sa_get_latest_user_order_id_for_webhook($user_id);
    }

    if ($order_id <= 0) {
        status_header(200);
        echo 'No order';
        exit;
    }

    $quantity      = !empty($metadata->sa_modification_quantity) ? (int) $metadata->sa_modification_quantity : 0;
    $is_unlimited  = !empty($metadata->sa_is_unlimited) && $metadata->sa_is_unlimited === '1';
    $duration_days = !empty($metadata->sa_unlimited_days) ? (int) $metadata->sa_unlimited_days : 30;

    if ($is_unlimited) {
        $current_until = (int) sa_get_order_unlimited_modifications_until($user_id, $order_id);
        $base_time     = ($current_until > time()) ? $current_until : time();
        $new_until     = $base_time + ($duration_days * DAY_IN_SECONDS);

        sa_set_order_unlimited_modifications_until($user_id, $order_id, $new_until);
    } else {
        $current = (int) sa_get_order_modifications_remaining($user_id, $order_id);

        sa_set_order_modifications_remaining(
            $user_id,
            $order_id,
            $current + max(0, $quantity)
        );
    }

    $created      = !empty($session->created) ? (int) $session->created : time();
    $status       = $payment_status ?: 'paid';
    $invoice_pdf  = '';
    $hosted_url   = '';
    $stripe_id    = $session_id;
    $amount_total = isset($session->amount_total) ? (int) $session->amount_total : 0;

    if (!empty($session->invoice) && function_exists('sa_get_stripe_invoice')) {
        $invoice = sa_get_stripe_invoice($session->invoice);

        if ($invoice && !is_wp_error($invoice)) {
            $invoice_pdf  = !empty($invoice['invoice_pdf']) ? $invoice['invoice_pdf'] : '';
            $hosted_url   = !empty($invoice['hosted_invoice_url']) ? $invoice['hosted_invoice_url'] : '';
            $status       = !empty($invoice['status']) ? $invoice['status'] : $status;
            $created      = !empty($invoice['created']) ? (int) $invoice['created'] : $created;
            $amount_total = isset($invoice['amount_paid']) ? (int) $invoice['amount_paid'] : $amount_total;
            $stripe_id    = !empty($invoice['id']) ? $invoice['id'] : $stripe_id;
        }
    }

    if (
        !$invoice_pdf &&
        !empty($session->payment_intent) &&
        function_exists('sa_get_stripe_payment_intent')
    ) {
        $payment_intent = sa_get_stripe_payment_intent($session->payment_intent);

        if ($payment_intent && !is_wp_error($payment_intent)) {
            if (!empty($payment_intent['latest_charge']['receipt_url'])) {
                $invoice_pdf = $payment_intent['latest_charge']['receipt_url'];
            }

            if (!$hosted_url && !empty($payment_intent['latest_charge']['receipt_url'])) {
                $hosted_url = $payment_intent['latest_charge']['receipt_url'];
            }

            if (
                (!$invoice_pdf || !$hosted_url) &&
                !empty($payment_intent['latest_charge']) &&
                is_string($payment_intent['latest_charge']) &&
                function_exists('sa_get_stripe_charge')
            ) {
                $charge = sa_get_stripe_charge($payment_intent['latest_charge']);

                if ($charge && !is_wp_error($charge) && !empty($charge['receipt_url'])) {
                    if (!$invoice_pdf) {
                        $invoice_pdf = $charge['receipt_url'];
                    }

                    if (!$hosted_url) {
                        $hosted_url = $charge['receipt_url'];
                    }
                }
            }

            if (!empty($payment_intent['status']) && $payment_intent['status'] === 'succeeded') {
                $status = 'paid';
            }

            if (isset($payment_intent['amount_received'])) {
                $amount_total = (int) $payment_intent['amount_received'];
            }
        }
    }

    if (function_exists('sa_add_order_extra_modification_invoice')) {
        sa_add_order_extra_modification_invoice($user_id, $order_id, [
            'created'     => $created,
            'amount_paid' => $amount_total,
            'status'      => $status,
            'invoice_pdf' => $invoice_pdf,
            'hosted_url'  => $hosted_url,
            'stripe_id'   => $stripe_id,
        ]);
    }

    sa_mark_stripe_checkout_session_processed($session_id);

    status_header(200);
    echo 'OK';
    exit;
});