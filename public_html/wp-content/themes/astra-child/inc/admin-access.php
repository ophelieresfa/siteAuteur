<?php
if (!defined('ABSPATH')) exit;

/**
 * Bloquer l'accès au back-office WordPress pour tous les utilisateurs
 * sauf les administrateurs.
 */
add_action('admin_init', 'sa_block_wp_admin_for_non_admins', 1);

function sa_block_wp_admin_for_non_admins() {

    /**
     * Autoriser admin-ajax.php
     * Important pour Ultimate Member, Stripe, formulaires, etc.
     */
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    /**
     * Autoriser les vraies tâches cron WordPress
     */
    if (defined('DOING_CRON') && DOING_CRON) {
        return;
    }

    /**
     * Si utilisateur non connecté, WordPress gère déjà la connexion.
     */
    if (!is_user_logged_in()) {
        return;
    }

    /**
     * Autoriser uniquement les administrateurs WordPress.
     */
    if (current_user_can('administrator') || current_user_can('manage_options')) {
        return;
    }

    /**
     * Bloquer tous les autres rôles :
     * subscriber, customer, ultimate_member, author, editor, etc.
     */
    wp_safe_redirect(home_url('/account/'));
    exit;
}