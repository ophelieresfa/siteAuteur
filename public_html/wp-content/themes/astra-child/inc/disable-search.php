<?php
if (!defined('ABSPATH')) exit;

/**
 * Désactive la recherche WordPress front-end.
 */
function sa_disable_frontend_search() {
    if (is_admin()) {
        return;
    }

    if (is_search()) {
        wp_redirect(home_url('/'), 301);
        exit;
    }
}
add_action('template_redirect', 'sa_disable_frontend_search');