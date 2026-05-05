<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * HELPERS
 * =========================
 */

function sa_account_login_url() {
    return home_url('/login/');
}

function sa_account_dashboard_url() {
    return home_url('/account/');
}

/**
 * =========================
 * PROTECTION DES PAGES COMPTE
 * =========================
 */

add_action('template_redirect', function () {
    if (
        is_page('modifier-mon-mot-de-passe') ||
        is_page('supprimer-mon-compte')
    ) {
        if (!is_user_logged_in()) {
            wp_safe_redirect(sa_account_login_url());
            exit;
        }
    }
}, 20);

/**
 * =========================
 * SHORTCODE - MODIFIER MOT DE PASSE
 * =========================
 */

add_shortcode('siteauteur_account_change_password', function () {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à cette page.</p>';
    }

    $data = [
        'error'   => '',
        'success' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sa_change_password_nonce'])) {
        if (!wp_verify_nonce($_POST['sa_change_password_nonce'], 'sa_change_password_action')) {
            $data['error'] = 'Session invalide. Rechargez la page.';
        } else {
            $current_password = isset($_POST['current_password']) ? (string) wp_unslash($_POST['current_password']) : '';
            $new_password     = isset($_POST['new_password']) ? (string) wp_unslash($_POST['new_password']) : '';
            $confirm_password = isset($_POST['confirm_password']) ? (string) wp_unslash($_POST['confirm_password']) : '';

            $user = wp_get_current_user();

            if ($current_password === '' || $new_password === '' || $confirm_password === '') {
                $data['error'] = 'Veuillez remplir tous les champs.';
            } elseif (!wp_check_password($current_password, $user->user_pass, $user->ID)) {
                $data['error'] = 'Le mot de passe actuel est incorrect.';
            } elseif ($current_password === $new_password) {
                $data['error'] = 'Le nouveau mot de passe doit être différent du mot de passe actuel.';
            } elseif (strlen($new_password) < 8) {
                $data['error'] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
            } elseif ($new_password !== $confirm_password) {
                $data['error'] = 'Les mots de passe ne correspondent pas.';
            } else {
                wp_set_password($new_password, $user->ID);

                if (function_exists('sa_send_password_changed_email')) {
                    sa_send_password_changed_email($user);
                }

                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, true);

                $data['success'] = 'Ton mot de passe a bien été modifié.';
            }
        }
    }

    return sa_render_template('account-change-password', $data);
});

/**
 * =========================
 * SHORTCODE - PAGE SUPPRESSION COMPTE
 * =========================
 */

add_shortcode('siteauteur_account_delete', function () {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à cette page.</p>';
    }

    $data = [
        'error' => isset($_GET['error']) ? sanitize_text_field(wp_unslash($_GET['error'])) : '',
    ];

    return sa_render_template('account-delete', $data);
});

/**
 * =========================
 * SHORTCODE - PAGE COMPTE SUPPRIMÉ
 * =========================
 */

add_shortcode('siteauteur_account_deleted', function () {
    return sa_render_template('account-deleted-success');
});

/**
 * =========================
 * ACTION - SUPPRESSION COMPTE
 * =========================
 */

add_action('admin_post_sa_delete_account', 'sa_handle_delete_account');

function sa_handle_delete_account() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(sa_account_login_url());
        exit;
    }

    if (
        !isset($_POST['sa_delete_account_nonce']) ||
        !wp_verify_nonce($_POST['sa_delete_account_nonce'], 'sa_delete_account_action')
    ) {
        wp_die('Vérification de sécurité échouée.');
    }

    if (empty($_POST['confirm_delete'])) {
        wp_safe_redirect(home_url('/supprimer-mon-compte/?error=confirm'));
        exit;
    }

    $user_id = get_current_user_id();

    require_once ABSPATH . 'wp-admin/includes/user.php';

    wp_logout();

    /*
    COOKIE TEMPORAIRE (autorise accès à /compte-supprime/)
    */
    setcookie(
        'sa_account_deleted',
        '1',
        time() + 120, // 2 minutes
        COOKIEPATH,
        COOKIE_DOMAIN,
        is_ssl(),
        true
    );

    wp_delete_user($user_id);

    wp_safe_redirect(home_url('/compte-supprime/'));
    exit;
}