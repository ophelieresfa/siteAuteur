<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * PAGE CONTACT CUSTOM
 * =========================
 */

add_filter('template_include', function ($template) {

    if (is_page('contact')) {
        $custom_template = plugin_dir_path(dirname(__FILE__)) . 'templates/contact-page-template.php';

        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }

    return $template;
});

add_action('wp_enqueue_scripts', function () {

    if (!is_page('contact')) {
        return;
    }

    $contact_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/contact-page.css';

    if (file_exists($contact_css)) {
        wp_enqueue_style(
            'sa-contact-page',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/contact-page.css',
            [],
            filemtime($contact_css)
        );
    }

    if (is_user_logged_in()) {

        $sidebar_css = plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-sidebar.css';

        if (file_exists($sidebar_css)) {
            wp_enqueue_style(
                'sa-account-sidebar',
                plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-sidebar.css',
                ['sa-contact-page'],
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
    }

}, 99);