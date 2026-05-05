<?php

if (!defined('ABSPATH')) exit;

/**
 * Charge les styles spécifiques de la page 404.
 */
add_action('wp_enqueue_scripts', function () {

    if (!is_404()) {
        return;
    }

    wp_enqueue_style(
        'siteauteur-404',
        get_stylesheet_directory_uri() . '/assets/css/404.css',
        [],
        '1.0.0'
    );

});