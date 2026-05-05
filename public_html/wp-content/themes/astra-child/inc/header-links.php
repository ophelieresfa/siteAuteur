<?php
if (!defined('ABSPATH')) exit;

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

    $html = preg_replace_callback(
        '#<a([^>]*?)href=("|\')(https?://siteauteur\.fr/login/?|/login/?)\2([^>]*)>(.*?)</a>#is',
        function ($matches) use ($account_url) {
            $attrs = $matches[1] . $matches[4];

            if (strpos($attrs, 'class=') !== false) {
                $attrs = preg_replace('/class=("|\')(.*?)\1/', 'class=$1$2 sa-account-link$1', $attrs);
            } else {
                $attrs .= ' class="sa-account-link"';
            }

            return '<a' . $attrs . ' href="' . $account_url . '">Mon compte</a>';
        },
        $html
    );

    $html = preg_replace_callback(
        '#<a([^>]*?)href=("|\')(https?://siteauteur\.fr/register/?|/register/?)\2([^>]*)>(.*?)</a>#is',
        function ($matches) use ($logout_url) {
            $attrs = $matches[1] . $matches[4];

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