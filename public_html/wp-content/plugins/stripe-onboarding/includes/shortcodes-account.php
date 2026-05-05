<?php
if (!defined('ABSPATH')) exit;

add_shortcode('siteauteur_account_dashboard', function () {
    return sa_render_account_dashboard();
});

add_shortcode('siteauteur_account_subscription', function () {
    return sa_render_account_subscription();
});

add_shortcode('siteauteur_account_invoices', function () {
    return sa_render_account_invoices();
});

add_shortcode('siteauteur_use_modification', function () {
    return sa_render_use_modification_form();
});

add_shortcode('siteauteur_buy_extra_modification', function () {
    return sa_render_buy_extra_modification();
});

add_shortcode('siteauteur_merci_modification', function () {
    return sa_render_template('merci-modification');
});

add_shortcode('sa_register_success', 'sa_render_register_success_page');

function sa_render_register_success_page() {
    ob_start();

    $template = plugin_dir_path(dirname(__FILE__)) . 'templates/auth/register-success.php';

    if (file_exists($template)) {
        include $template;
    }

    return ob_get_clean();
}