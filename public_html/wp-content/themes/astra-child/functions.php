<?php

// Styles CSS / JS
add_action('wp_enqueue_scripts', function() {

    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    wp_enqueue_style(
        'astra-child-custom',
        get_stylesheet_directory_uri() . '/assets/css/style.css',
        array('parent-style'),
        wp_get_theme()->get('Version')
    );

    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/assets/css/custom.css',
        array('parent-style'),
        filemtime(get_stylesheet_directory() . '/assets/css/custom.css')
    );

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
        [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('sa_onboarding_nonce'),
            'session_id' => isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : ''
        ]
    );
});

// Connexion / Inscription / Déconnexion -- Header
add_action('template_redirect', 'siteauteur_start_header_buffer');

function siteauteur_start_header_buffer() {
    if (is_admin() || !is_user_logged_in()) {
        return;
    }

    ob_start('siteauteur_replace_header_links');
}

function siteauteur_replace_header_links($html) {

    $account_url = esc_url(home_url('/account/'));
    $logout_url  = esc_url(wp_logout_url(home_url('/')));

    // LOGIN → MON COMPTE + class
    $html = preg_replace_callback(
        '#<a([^>]*?)href=("|\')(https?://siteauteur\.fr/login/?|/login/?)\2([^>]*)>(.*?)</a>#is',
        function ($matches) use ($account_url) {

            $attrs = $matches[1] . $matches[4];

            // Ajouter classe
            if (strpos($attrs, 'class=') !== false) {
                $attrs = preg_replace('/class=("|\')(.*?)\1/', 'class=$1$2 sa-account-link$1', $attrs);
            } else {
                $attrs .= ' class="sa-account-link"';
            }

            return '<a' . $attrs . ' href="' . $account_url . '">Mon compte</a>';
        },
        $html
    );

    // REGISTER → LOGOUT + class
    $html = preg_replace_callback(
        '#<a([^>]*?)href=("|\')(https?://siteauteur\.fr/register/?|/register/?)\2([^>]*)>(.*?)</a>#is',
        function ($matches) use ($logout_url) {

            $attrs = $matches[1] . $matches[4];

            // Ajouter classe
            if (strpos($attrs, 'class=') !== false) {
                $attrs = preg_replace('/class=("|\')(.*?)\1/', 'class=$1$2 sa-logout-link$1', $attrs);
            } else {
                $attrs .= ' class="sa-logout-link"';
            }

            return '<a' . $attrs . ' href="' . $logout_url . '">Se déconnecter</a>';
        },
        $html
    );

    return $html;
}
