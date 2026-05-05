<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', 'siteauteur_enqueue_assets', 30);

function siteauteur_enqueue_assets() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/custom.css')
    );

    if (is_front_page()) {
        wp_enqueue_style(
            'sa-home',
            get_stylesheet_directory_uri() . '/assets/css/home.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/home.css')
        );
    }

    if (is_page('tarif')) {
        wp_enqueue_style(
            'sa-tarif',
            get_stylesheet_directory_uri() . '/assets/css/tarif.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/tarif.css')
        );
    }

    if (is_page('faq')) {
        wp_enqueue_style(
            'sa-faq',
            get_stylesheet_directory_uri() . '/assets/css/faq.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/faq.css')
        );
    }

    if (is_page('recapitulatif-commande')) {
        wp_enqueue_style(
            'sa-recapitulatif-commande',
            get_stylesheet_directory_uri() . '/assets/css/recapitulatif-commande.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/recapitulatif-commande.css')
        );
    }

    if (is_page('login')) {
        wp_enqueue_style(
            'sa-login',
            get_stylesheet_directory_uri() . '/assets/css/login.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/login.css')
        );
    }

    if (is_page('register')) {
        wp_enqueue_style(
            'sa-register',
            get_stylesheet_directory_uri() . '/assets/css/register.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/register.css')
        );
    }

    if (is_page('mentions-legales')) {
        wp_enqueue_style(
            'sa-mentions-legales',
            get_stylesheet_directory_uri() . '/assets/css/mentions-legales.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/mentions-legales.css')
        );
    }

    if (is_page('politique-de-confidentialite')) {
        wp_enqueue_style(
            'sa-politique-confidentialite',
            get_stylesheet_directory_uri() . '/assets/css/politique-confidentialite.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/politique-confidentialite.css')
        );
    }

    if (is_page('cgv')) {
        wp_enqueue_style(
            'sa-cgv',
            get_stylesheet_directory_uri() . '/assets/css/cgv.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/cgv.css')
        );
    }

    if (is_page('cookies')) {
        wp_enqueue_style(
            'sa-cookies',
            get_stylesheet_directory_uri() . '/assets/css/cookies.css',
            array('parent-style', 'child-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/cookies.css')
        );
    }

    if (function_exists('um_is_core_page') && um_is_core_page('password-reset')) {
        wp_enqueue_style(
            'sa-password-reset',
            get_stylesheet_directory_uri() . '/assets/css/sa-password-reset.css',
            array('parent-style'),
            filemtime(get_stylesheet_directory() . '/assets/css/sa-password-reset.css')
        );
    }

    wp_enqueue_script(
        'child-custom-js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js',
        array(),
        filemtime(get_stylesheet_directory() . '/assets/js/custom.js'),
        true
    );

    wp_localize_script(
        'child-custom-js',
        'SA_ONBOARDING',
        array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('sa_onboarding_nonce'),
            'session_id' => isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '',
        )
    );
}