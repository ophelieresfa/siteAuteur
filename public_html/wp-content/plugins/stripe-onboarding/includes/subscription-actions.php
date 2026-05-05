<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * ACTIONS ABONNEMENT
 * =========================
 */

function sa_subscription_notice_url($notice = '') {
    $url = home_url('/mon-abonnement/');

    if ($notice) {
        $url = add_query_arg('subscription_notice', $notice, $url);
    }

    return $url . '#resiliation-abonnement';
}

add_action('admin_post_sa_cancel_subscription', 'sa_handle_cancel_subscription');
add_action('admin_post_sa_resume_subscription', 'sa_handle_resume_subscription');
add_action('admin_post_sa_resume_canceled_subscription', 'sa_handle_resume_canceled_subscription');

function sa_handle_cancel_subscription() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    if (
        empty($_POST['sa_cancel_subscription_nonce']) ||
        !wp_verify_nonce($_POST['sa_cancel_subscription_nonce'], 'sa_cancel_subscription')
    ) {
        wp_safe_redirect(sa_subscription_notice_url('invalid_nonce'));
        exit;
    }

    $order = sa_get_current_order_from_session_or_user();

    if (!$order) {
        wp_safe_redirect(sa_subscription_notice_url('order_not_found'));
        exit;
    }

    $current_user_id = get_current_user_id();

    if ((int) $order->wp_user_id !== (int) $current_user_id) {
        wp_safe_redirect(sa_subscription_notice_url('forbidden'));
        exit;
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    $current_state = sa_get_order_state($user_id, $order_id);

    $subscription_id = !empty($order->stripe_subscription_id)
        ? sanitize_text_field($order->stripe_subscription_id)
        : '';

    if (!$subscription_id) {
        wp_safe_redirect(sa_subscription_notice_url('subscription_missing'));
        exit;
    }

    $subscription = sa_get_stripe_subscription($subscription_id);

    if (!$subscription || is_wp_error($subscription)) {
        wp_safe_redirect(sa_subscription_notice_url('subscription_not_found'));
        exit;
    }

    /*
     * Si la résiliation était déjà programmée,
     * on ne renvoie pas un deuxième email.
     */
    if (!empty($subscription['cancel_at_period_end'])) {
        sa_update_order_meta($user_id, $order_id, 'state', 'paused');
        sa_update_order_meta($user_id, $order_id, 'subscription_cancel_requested_at', time());
        sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 1);

        wp_safe_redirect(sa_subscription_notice_url('already_scheduled'));
        exit;
    }

    $result = sa_cancel_stripe_subscription_at_period_end($subscription_id);

    if (is_wp_error($result) || empty($result['id'])) {
        wp_safe_redirect(sa_subscription_notice_url('cancel_error'));
        exit;
    }

    /*
     * Ici, la résiliation est seulement programmée.
     * Le site n’est pas encore réellement suspendu côté Stripe.
     */
    sa_update_order_meta($user_id, $order_id, 'state', 'paused');
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_requested_at', time());
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 1);

    $already_sent = sa_get_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);

    if (!$already_sent && function_exists('sa_send_subscription_cancel_scheduled_email')) {
        $sent = sa_send_subscription_cancel_scheduled_email($user_id, $order_id);

        if ($sent) {
            sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', time());
        }
    }

    wp_safe_redirect(sa_subscription_notice_url('cancel_scheduled'));
    exit;
}

function sa_handle_resume_subscription() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    if (
        empty($_POST['sa_resume_subscription_nonce']) ||
        !wp_verify_nonce($_POST['sa_resume_subscription_nonce'], 'sa_resume_subscription')
    ) {
        wp_safe_redirect(sa_subscription_notice_url('invalid_nonce'));
        exit;
    }

    $order = sa_get_current_order_from_session_or_user();

    if (!$order) {
        wp_safe_redirect(sa_subscription_notice_url('order_not_found'));
        exit;
    }

    $current_user_id = get_current_user_id();

    if ((int) $order->wp_user_id !== (int) $current_user_id) {
        wp_safe_redirect(sa_subscription_notice_url('forbidden'));
        exit;
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    $current_state = sa_get_order_state($user_id, $order_id);

    $subscription_id = !empty($order->stripe_subscription_id)
        ? sanitize_text_field($order->stripe_subscription_id)
        : '';

    if (!$subscription_id) {
        wp_safe_redirect(sa_subscription_notice_url('subscription_missing'));
        exit;
    }

    $subscription = sa_get_stripe_subscription($subscription_id);

    if (!$subscription || is_wp_error($subscription)) {
        wp_safe_redirect(sa_subscription_notice_url('subscription_not_found'));
        exit;
    }

    /*
     * Si Stripe indique déjà que l’abonnement n’est plus en résiliation programmée,
     * mais que la commande est encore en "Projet suspendu",
     * on remet quand même la commande en "Site en ligne".
     */
    if (empty($subscription['cancel_at_period_end'])) {
        sa_update_order_meta($user_id, $order_id, 'state', 'site_live');
        sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 0);
        sa_update_order_meta($user_id, $order_id, 'subscription_resumed_at', time());
        sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);
        sa_update_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', 0);

        if ($current_state !== 'site_live' && function_exists('sa_send_project_reactivated_email')) {
            sa_send_project_reactivated_email($user_id, $order_id);
        }

        wp_safe_redirect(sa_subscription_notice_url('resume_success'));
        exit;
    }

    /*
     * Sinon, on demande à Stripe d’annuler la résiliation programmée.
     */
    $result = sa_resume_stripe_subscription($subscription_id);

    if (is_wp_error($result) || empty($result['id'])) {
        wp_safe_redirect(sa_subscription_notice_url('resume_error'));
        exit;
    }

    /*
     * Quand Stripe confirme la reprise,
     * on remet la commande en "Site en ligne" dans le back-office.
     */
    sa_update_order_meta($user_id, $order_id, 'state', 'site_live');
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_resumed_at', time());
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', 0);

    if ($current_state !== 'site_live' && function_exists('sa_send_project_reactivated_email')) {
        sa_send_project_reactivated_email($user_id, $order_id);
    }

    wp_safe_redirect(sa_subscription_notice_url('resume_success'));
    exit;
}

function sa_handle_resume_canceled_subscription() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    if (
        empty($_POST['sa_resume_canceled_subscription_nonce']) ||
        !wp_verify_nonce($_POST['sa_resume_canceled_subscription_nonce'], 'sa_resume_canceled_subscription')
    ) {
        wp_safe_redirect(sa_subscription_notice_url('invalid_nonce'));
        exit;
    }

    $order = sa_get_current_order_from_session_or_user();

    if (!$order) {
        wp_safe_redirect(sa_subscription_notice_url('order_not_found'));
        exit;
    }

    $current_user_id = get_current_user_id();

    if ((int) $order->wp_user_id !== (int) $current_user_id) {
        wp_safe_redirect(sa_subscription_notice_url('forbidden'));
        exit;
    }

    $session = sa_create_resume_canceled_subscription_checkout_session($order);

    if (is_wp_error($session)) {
        wp_safe_redirect(sa_subscription_notice_url($session->get_error_code()));
        exit;
    }

    if (empty($session['url'])) {
        wp_safe_redirect(sa_subscription_notice_url('resume_checkout_error'));
        exit;
    }

    wp_redirect(esc_url_raw($session['url']));
    exit;
}

add_shortcode('sa_billing_redirect', 'sa_billing_redirect_shortcode');

function sa_billing_redirect_shortcode() {
    if (is_admin()) {
        return '';
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    $order = sa_get_current_order_from_session_or_user();

    if (!$order) {
        wp_safe_redirect(add_query_arg('subscription_notice', 'order_not_found', home_url('/mon-abonnement/')));
        exit;
    }

    $current_user_id = get_current_user_id();

    if ((int) $order->wp_user_id !== (int) $current_user_id) {
        wp_safe_redirect(add_query_arg('subscription_notice', 'forbidden', home_url('/mon-abonnement/')));
        exit;
    }

    $customer_id = !empty($order->stripe_customer_id)
        ? sanitize_text_field($order->stripe_customer_id)
        : '';

    if (!$customer_id && !empty($order->stripe_subscription_id)) {
        $subscription = sa_get_stripe_subscription(sanitize_text_field($order->stripe_subscription_id));

        if ($subscription && !is_wp_error($subscription) && !empty($subscription['customer'])) {
            $customer_id = sanitize_text_field($subscription['customer']);
        }
    }

    if (!$customer_id) {
        wp_safe_redirect(add_query_arg('subscription_notice', 'order_not_found', home_url('/mon-abonnement/')));
        exit;
    }

    $portal = sa_create_stripe_billing_portal_session(
        $customer_id,
        home_url('/mon-abonnement/')
    );

    if (is_wp_error($portal) || empty($portal['url'])) {
        wp_safe_redirect(add_query_arg('subscription_notice', 'order_not_found', home_url('/mon-abonnement/')));
        exit;
    }

    wp_redirect(esc_url_raw($portal['url']));
    exit;
}