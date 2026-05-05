<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * ASSETS
 * =========================
 */

/*
=========================
PROTECTION PAGE COMPTE SUPPRIMÉ
=========================
*/
add_action('template_redirect', function () {

    if (is_page('compte-supprime')) {

        // accès interdit si pas de cookie
        if (empty($_COOKIE['sa_account_deleted'])) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        // suppression du cookie après usage
        setcookie(
            'sa_account_deleted',
            '',
            time() - 3600,
            COOKIEPATH,
            COOKIE_DOMAIN
        );
    }
});

add_action('wp_enqueue_scripts', function () {

/*
=========================
PAGE INSCRIPTION CONFIRMÉE
=========================
*/
if (is_page('register-success')) {

    $register_success_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/register-success.css';

    if (file_exists($register_success_css)) {
        wp_enqueue_style(
            'sa-register-success',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/register-success.css',
            [],
            filemtime($register_success_css)
        );
    }

    return;
}

/*
=========================
PAGE CONFIRMATION COMMANDE
=========================
*/
if (is_page('confirmation-commande')) {

    /*
    CSS page commande introuvable / session Stripe invalide
    */
    $confirmation_not_found_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/confirmation-not-found.css';

    if (file_exists($confirmation_not_found_css)) {
        wp_enqueue_style(
            'sa-confirmation-not-found',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/confirmation-not-found.css',
            [],
            filemtime($confirmation_not_found_css)
        );
    }

    /*
    CSS page commande confirmée / mot de passe à créer
    */
    $confirmation_order_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/confirmation-order.css';

    if (file_exists($confirmation_order_css)) {
        wp_enqueue_style(
            'sa-confirmation-order',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/confirmation-order.css',
            ['sa-confirmation-not-found'],
            filemtime($confirmation_order_css)
        );
    }

    /*
    CSS page commande confirmée / onboarding prêt
    */
    $confirmation_onboarding_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/confirmation-order-onboarding.css';

    if (file_exists($confirmation_onboarding_css)) {
        wp_enqueue_style(
            'sa-confirmation-order-onboarding',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/confirmation-order-onboarding.css',
            ['sa-confirmation-order'],
            filemtime($confirmation_onboarding_css)
        );
    }

    return;
}

/*
=========================
PAGE DEFINIR MON MOT DE PASSE
=========================
*/
if (is_page('definir-mon-mot-de-passe')) {

    /*
    CSS de la refonte
    */
    $reset_form_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/reset-password-form.css';

    if (file_exists($reset_form_css)) {
        wp_enqueue_style(
            'sa-reset-password-form',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/reset-password-form.css',
            [],
            filemtime($reset_form_css)
        );
    }

    /*
    JS EXISTANT pour :
    - afficher / masquer le mot de passe
    - jauge dynamique
    - règles dynamiques
    */
    $account_actions_js = plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-actions.js';

    if (file_exists($account_actions_js)) {
        wp_enqueue_script(
            'sa-account-actions',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/account-actions.js',
            [],
            filemtime($account_actions_js),
            true
        );
    }

    /*
    RESET INVALID
    */
    $reset_invalid_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/reset-password-invalid.css';

    if (file_exists($reset_invalid_css)) {
        wp_enqueue_style(
            'sa-reset-password-invalid',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/reset-password-invalid.css',
            ['sa-reset-password-form'],
            filemtime($reset_invalid_css)
        );
    }

    /*
    RESET EXPIRED
    */
    $reset_expired_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/reset-password-expired.css';

    if (file_exists($reset_expired_css)) {
        wp_enqueue_style(
            'sa-reset-password-expired',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/reset-password-expired.css',
            ['sa-reset-password-invalid'],
            filemtime($reset_expired_css)
        );
    }

    return;
}

        /*
    =========================
    PAGES ACTIONS COMPTE
    =========================
    */
    if (is_page('modifier-mon-mot-de-passe') || is_page('supprimer-mon-compte')) {

        $sidebar_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-sidebar.css';

        if (file_exists($sidebar_css)) {
            wp_enqueue_style(
                'sa-account-sidebar',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-sidebar.css',
                [],
                filemtime($sidebar_css)
            );
        }

        $account_actions_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-actions.css';

        if (file_exists($account_actions_css)) {
            wp_enqueue_style(
                'sa-account-actions',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-actions.css',
                ['sa-account-sidebar'],
                filemtime($account_actions_css)
            );
        }

        $account_actions_js = plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-actions.js';

        if (file_exists($account_actions_js)) {
            wp_enqueue_script(
                'sa-account-actions',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/account-actions.js',
                [],
                filemtime($account_actions_js),
                true
            );
        }

        return;
    }

    if (is_page('compte-supprime')) {

        $deleted_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-deleted-success.css';

        if (file_exists($deleted_css)) {
            wp_enqueue_style(
                'sa-account-deleted-success',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-deleted-success.css',
                [],
                filemtime($deleted_css)
            );
        }

        return;
    }

    /*
    =========================
    PAGE MERCI MODIFICATION
    =========================
    */
    if (is_page('merci-modification')) {

        $merci_modification_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/merci-modification.css';

        if (file_exists($merci_modification_css)) {
            wp_enqueue_style(
                'sa-merci-modification',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/merci-modification.css',
                [],
                filemtime($merci_modification_css)
            );
        }

        return;
    }

    if (
        !is_page(SA_PAGE_ACCOUNT) &&
        !is_page('mon-abonnement') &&
        !is_page('mes-factures') &&
        !is_page(SA_PAGE_USE_MODIFICATION) &&
        !is_page(SA_PAGE_ONBOARDING) &&
        !is_page('acheter-une-modification')
    ) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    /*
    =========================
    PAGE UTILISER MA MODIFICATION
    =========================
    */
    if (is_page(SA_PAGE_USE_MODIFICATION)) {

        $modification_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-modification.css';
        $modification_js  = plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-modification.js';

        if (file_exists($modification_css)) {
            wp_enqueue_style(
                'sa-account-modification',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-modification.css',
                [],
                filemtime($modification_css)
            );
        }

        if (file_exists($modification_js)) {
            wp_enqueue_script(
                'sa-account-modification',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/account-modification.js',
                [],
                filemtime($modification_js),
                true
            );

            $order = function_exists('sa_get_requested_order_for_modification')
                ? sa_get_requested_order_for_modification()
                : false;

            $original_data = $order ? sa_get_order_original_onboarding_data($order) : [];
            $remaining     = $order ? sa_get_order_modifications_remaining(get_current_user_id(), (int) $order->id) : 0;

            wp_localize_script('sa-account-modification', 'SA_MODIFICATION', [
                'formId'       => (int) SA_ONBOARDING_FORM_ID,
                'orderId'      => $order ? (int) $order->id : 0,
                'remaining'    => (int) $remaining,
                'includedLimit' => function_exists('sa_get_included_modifications_limit') ? (int) sa_get_included_modifications_limit() : 5,
                'originalData' => is_array($original_data) ? $original_data : [],
                'debug'        => [
                    'hasOrder'      => $order ? 1 : 0,
                    'orderId'       => $order ? (int) $order->id : 0,
                    'originalCount' => is_array($original_data) ? count($original_data) : 0,
                    'originalKeys'  => is_array($original_data) ? array_keys($original_data) : [],
                ],
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('sa_save_modification'),
            ]);
        }

        return;
    }

    /*
    =========================
    PAGE ONBOARDING
    =========================
    */
    if (is_page(SA_PAGE_ONBOARDING)) {

        $onboarding_fix_js = plugin_dir_path(dirname(__FILE__)) . 'assets/js/onboarding-validation-fix.js';

        if (file_exists($onboarding_fix_js)) {
            wp_enqueue_script(
                'sa-onboarding-validation-fix',
                plugin_dir_url(dirname(__FILE__)) . 'assets/js/onboarding-validation-fix.js',
                [],
                filemtime($onboarding_fix_js),
                true
            );
        }

        return;
    }

    /*
    =========================
    PAGE ACHETER UNE MODIFICATION
    =========================
    */
    if (is_page('acheter-une-modification')) {

        $buy_mod_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/buy-extra-modification.css';

        if (file_exists($buy_mod_css)) {
            wp_enqueue_style(
                'sa-buy-extra-modification',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/buy-extra-modification.css',
                [],
                filemtime($buy_mod_css)
            );
        }

        return;
    }

    /*
    =========================
    SIDEBAR COMMUN
    =========================
    */
    $sidebar_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-sidebar.css';

    if (file_exists($sidebar_css)) {
        wp_enqueue_style(
            'sa-account-sidebar',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-sidebar.css',
            [],
            filemtime($sidebar_css)
        );
    }

    $account_actions_js = plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-actions.js';

    if (file_exists($account_actions_js)) {
        wp_enqueue_script(
            'sa-account-actions',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/account-actions.js',
            [],
            filemtime($account_actions_js),
            true
        );
    }

    /*
    =========================
    PAGE ABONNEMENT
    =========================
    */
    if (is_page('mon-abonnement')) {

        $subscription_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-subscription.css';

        if (file_exists($subscription_css)) {
            wp_enqueue_style(
                'sa-account-subscription',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-subscription.css',
                ['sa-account-sidebar'],
                filemtime($subscription_css)
            );
        }
        return;
    }

    /*
    =========================
    PAGE FACTURES
    =========================
    */
    if (is_page('mes-factures')) {

        $invoices_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-invoices.css';

        if (file_exists($invoices_css)) {
            wp_enqueue_style(
                'sa-account-invoices',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-invoices.css',
                ['sa-account-sidebar'],
                filemtime($invoices_css)
            );
        }

        return;
    }

    $user_id = get_current_user_id();

    if (!function_exists('sa_get_user_paid_orders')) {
        return;
    }

    $orders = sa_get_user_paid_orders($user_id);

    /*
    =========================
    AUCUN PROJET
    =========================
    */
    if (empty($orders)) {

        $no_project_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-no-project.css';

        if (file_exists($no_project_css)) {
            wp_enqueue_style(
                'sa-account-no-project',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-no-project.css',
                ['sa-account-sidebar'],
                filemtime($no_project_css)
            );
        }

        return;
    }

    /*
    =========================
    AVEC PROJETS
    =========================
    */
    $current_order = function_exists('sa_get_current_order_from_session_or_user')
        ? sa_get_current_order_from_session_or_user()
        : null;

    $state = '';

    if ($current_order && function_exists('sa_get_order_state')) {
        $state = sa_get_order_state($user_id, (int) $current_order->id);
    }

    if (in_array($state, ['site_live', 'paused'], true)) {
        $site_live_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-site-live.css';

        if (file_exists($site_live_css)) {
            wp_enqueue_style(
                'sa-account-site-live',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-site-live.css',
                ['sa-account-sidebar'],
                filemtime($site_live_css)
            );
        }

        return;
    }

    $projects_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-projects.css';

    if (file_exists($projects_css)) {
        wp_enqueue_style(
            'sa-account-projects',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-projects.css',
            ['sa-account-sidebar'],
            filemtime($projects_css)
        );
    }

});