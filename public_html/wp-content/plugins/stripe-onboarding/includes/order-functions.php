<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * UTILISATEURS / COMMANDES
 * =========================
 */

function sa_find_or_create_user($email, $first_name = '', $last_name = '') {
    $email = sanitize_email($email);

    if (!$email) {
        return new WP_Error('invalid_email', 'Email invalide.');
    }

    $user = get_user_by('email', $email);

    /*
    |--------------------------------------------------------------------------
    | Utilisateur déjà existant
    |--------------------------------------------------------------------------
    | Si le compte existe déjà avant la commande, on considère que le mot de passe
    | est déjà créé. Le client doit donc accéder directement à l’onboarding.
    */
    if ($user) {
        if ($first_name) {
            update_user_meta($user->ID, 'first_name', $first_name);
        }

        if ($last_name) {
            update_user_meta($user->ID, 'last_name', $last_name);
        }

        /*
        Important :
        - Si la meta n'existe pas encore, c'est un ancien compte ou un compte créé
          avant cette logique.
        - Comme l'utilisateur existe déjà, on le considère comme compte actif.
        */
        $password_created = get_user_meta($user->ID, 'sa_password_created', true);

        if ($password_created === '' || $password_created === null) {
            update_user_meta($user->ID, 'sa_password_created', 1);
        }

        return $user->ID;
    }

    /*
    |--------------------------------------------------------------------------
    | Nouvel utilisateur
    |--------------------------------------------------------------------------
    | WordPress exige un mot de passe techniquement.
    | On crée donc un mot de passe aléatoire, mais le client devra définir
    | son vrai mot de passe via le lien envoyé par email.
    */
    $password = wp_generate_password(32, true, true);

    $user_id = wp_insert_user([
        'user_login' => $email,
        'user_email' => $email,
        'user_pass'  => $password,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'role'       => 'subscriber',
    ]);

    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'sa_password_created', 0);
    }

    return $user_id;
}

function sa_save_order($data) {
    global $wpdb;

    $table = $wpdb->prefix . 'siteauteur_orders';

    $existing_order = $wpdb->get_row($wpdb->prepare(
        "SELECT id, wp_user_id FROM $table WHERE stripe_session_id = %s LIMIT 1",
        $data['stripe_session_id']
    ));

    if ($existing_order) {
        $order_id = (int) $existing_order->id;
        $user_id  = (int) $existing_order->wp_user_id;

        if ($order_id > 0 && $user_id > 0) {
            $current_state = sa_get_order_meta($user_id, $order_id, 'state', '');

            if ($current_state === '') {
                sa_update_order_meta($user_id, $order_id, 'state', 'paid_no_onboarding_started');
            }
        }

        return $order_id;
    }

    $inserted = $wpdb->insert($table, $data);

    if (!$inserted) {
        return false;
    }

    $order_id = (int) $wpdb->insert_id;
    $user_id  = isset($data['wp_user_id']) ? (int) $data['wp_user_id'] : 0;

    if ($order_id > 0 && $user_id > 0) {
        sa_update_order_meta($user_id, $order_id, 'state', 'paid_no_onboarding_started');
    }

    return $order_id;
}

function sa_get_confirmation_order() {
    if (empty($_GET['session_id'])) {
        return false;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'siteauteur_orders';
    $session_id = sanitize_text_field($_GET['session_id']);

    return $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM {$table} WHERE stripe_session_id = %s", $session_id)
    );
}

function sa_get_order_by_session_id($session_id) {
    if (!$session_id) {
        return false;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'siteauteur_orders';

    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table} WHERE stripe_session_id = %s LIMIT 1",
            $session_id
        )
    );
}

function sa_get_user_paid_orders($user_id) {
    if (!$user_id) {
        return [];
    }

    global $wpdb;
    $table = $wpdb->prefix . 'siteauteur_orders';

    $orders = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE wp_user_id = %d
             AND payment_status = %s
             ORDER BY id DESC",
            $user_id,
            'paid'
        )
    );

    return is_array($orders) ? $orders : [];
}

/**
 * =========================
 * ETAT DES COMMANDES
 * =========================
 */

function sa_get_order_meta_key($key, $order_id) {
    return 'sa_order_' . sanitize_key($key) . '_' . absint($order_id);
}

function sa_get_order_meta($user_id, $order_id, $key, $default = '') {
    $meta_key = sa_get_order_meta_key($key, $order_id);
    $value = get_user_meta($user_id, $meta_key, true);

    if ($value === '' || $value === null) {
        return $default;
    }

    return $value;
}

function sa_update_order_meta($user_id, $order_id, $key, $value) {
    $meta_key = sa_get_order_meta_key($key, $order_id);
    return update_user_meta($user_id, $meta_key, $value);
}

function sa_delete_order_meta($user_id, $order_id, $key) {
    $meta_key = sa_get_order_meta_key($key, $order_id);
    return delete_user_meta($user_id, $meta_key);
}

function sa_get_order_state($user_id, $order_id) {
    $orders = sa_get_user_paid_orders($user_id);
    $current_order = null;

    foreach ($orders as $order) {
        if (!empty($order->id) && (int) $order->id === (int) $order_id) {
            $current_order = $order;
            break;
        }
    }

    if (!$current_order) {
        return 'paid';
    }

    $state = sa_get_order_meta($user_id, $order_id, 'state', '');

    if ($state === 'submitted') {
        return 'building';
    }

    if ($state === 'building') {
        return 'building';
    }

    if ($state === 'site_live') {
        return 'site_live';
    }

    if ($state === 'paused') {
        return 'paused';
    }

    if ($state === 'paid_onboarding_incomplete') {
        return 'onboarding_started';
    }

    if ($state === 'paid_no_onboarding_started') {
        return 'paid';
    }

    if (!empty($current_order->stripe_session_id)) {
        $session_draft = sa_get_onboarding_draft('session_' . $current_order->stripe_session_id);
        $user_draft    = sa_get_onboarding_draft('user_' . $user_id);

        $session_has_data = sa_draft_has_meaningful_data($session_draft);
        $user_has_data    = sa_draft_has_meaningful_data($user_draft);

        if ($session_has_data || $user_has_data) {
            return 'onboarding_started';
        }
    }

    return 'paid';
}

function sa_get_current_order_from_session_or_user() {
    $session_id = !empty($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';

    if ($session_id) {
        $order = sa_get_order_by_session_id($session_id);
        if ($order) {
            return $order;
        }
    }

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        if (function_exists('sa_get_account_orders_sorted')) {
            $orders = sa_get_account_orders_sorted($user_id);
        } else {
            $orders = sa_get_user_paid_orders($user_id);
        }

        if (!empty($orders) && is_array($orders)) {
            return $orders[0];
        }
    }

    return false;
}

function sa_order_has_completed_onboarding($order) {
    if (!$order || empty($order->wp_user_id)) {
        return false;
    }

    $entry_id = sa_get_order_meta((int) $order->wp_user_id, (int) $order->id, 'onboarding_entry_id', 0);

    if (!empty($entry_id)) {
        return true;
    }

    return false;
}