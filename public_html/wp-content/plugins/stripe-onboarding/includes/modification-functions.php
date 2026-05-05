<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * MODIFICATION INCLUSE
 * =========================
 */

function sa_is_modification_request_mode() {
    return !empty($_POST['sa_form_mode']) && sanitize_text_field($_POST['sa_form_mode']) === 'modification_request';
}

function sa_get_included_modifications_limit() {
    return 5;
}

function sa_get_order_modifications_remaining($user_id, $order_id) {
    $remaining = sa_get_order_meta($user_id, $order_id, 'modifications_remaining', '');

    if ($remaining === '' || $remaining === null) {
        return sa_get_included_modifications_limit();
    }

    return max(0, (int) $remaining);
}

function sa_set_order_modifications_remaining($user_id, $order_id, $count) {
    sa_update_order_meta($user_id, $order_id, 'modifications_remaining', max(0, (int) $count));
}

function sa_get_order_unlimited_modifications_until($user_id, $order_id) {
    return (int) sa_get_order_meta($user_id, $order_id, 'unlimited_modifications_until', 0);
}

function sa_set_order_unlimited_modifications_until($user_id, $order_id, $timestamp) {
    sa_update_order_meta($user_id, $order_id, 'unlimited_modifications_until', max(0, (int) $timestamp));
}

function sa_order_has_unlimited_modifications($user_id, $order_id) {
    $until = sa_get_order_unlimited_modifications_until($user_id, $order_id);

    return ($until > time());
}

function sa_get_order_unlimited_modifications_label($user_id, $order_id) {
    $until = sa_get_order_unlimited_modifications_until($user_id, $order_id);

    if ($until <= time()) {
        return '';
    }

    return 'Illimité jusqu’au ' . wp_date('d/m/Y', $until);
}

/**
 * ==========================================
 * HELPERS D’AFFICHAGE DES MODIFICATIONS
 * ==========================================
 */

/**
 * Détermine le "total affiché" pour le stock de modifications.
 *
 * Règles voulues :
 * - inclus abonnement seul : 1/5, 5/5, etc.
 * - si achat supplémentaire visible en stock :
 *   10 restantes => 10/10
 * - illimité => texte dédié
 */
function sa_get_modification_display_total($remaining, $included_limit = null) {
    $remaining = max(0, (int) $remaining);

    if ($included_limit === null) {
        $included_limit = sa_get_included_modifications_limit();
    }

    $included_limit = max(1, (int) $included_limit);

    if ($remaining > $included_limit) {
        return $remaining;
    }

    return $included_limit;
}

/**
 * Ex :
 * - 1/5
 * - 5/5
 * - 10/10
 * - Modification : illimité
 */
function sa_get_modification_stock_label($remaining, $included_limit = null, $is_unlimited = false) {
    if ($is_unlimited) {
        return 'Modification : illimité';
    }

    $remaining = max(0, (int) $remaining);

    if ($included_limit === null) {
        $included_limit = sa_get_included_modifications_limit();
    }

    $display_total = sa_get_modification_display_total($remaining, $included_limit);

    return $remaining . '/' . $display_total;
}

/**
 * Ex :
 * - 1 / 5 restantes
 * - 5 / 5 restantes
 * - 10 / 10 restantes
 * - Modifications illimitées
 */
function sa_get_modification_remaining_sentence($remaining, $included_limit = null, $is_unlimited = false) {
    if ($is_unlimited) {
        return 'Modifications illimitées';
    }

    $remaining = max(0, (int) $remaining);

    if ($included_limit === null) {
        $included_limit = sa_get_included_modifications_limit();
    }

    $display_total = sa_get_modification_display_total($remaining, $included_limit);

    return $remaining . ' / ' . $display_total . ' restantes';
}

/**
 * Texte de la carte "Utiliser mes modifications"
 */
function sa_get_modification_action_label($remaining, $included_limit = null, $is_unlimited = false) {
    if ($is_unlimited) {
        return 'Utiliser mes modifications illimitées';
    }

    $remaining = max(0, (int) $remaining);

    if ($included_limit === null) {
        $included_limit = sa_get_included_modifications_limit();
    }

    $display_total = sa_get_modification_display_total($remaining, $included_limit);

    return 'Utiliser une de mes ' . $display_total . ' modifications disponibles';
}

/**
 * Petit texte explicatif de la page "utiliser mes modifications"
 * On garde bien la distinction :
 * - 5 incluses dans l’abonnement
 * - + éventuel stock acheté en plus
 */
function sa_get_modification_intro_text($remaining, $included_limit = null, $is_unlimited = false) {
    if ($included_limit === null) {
        $included_limit = sa_get_included_modifications_limit();
    }

    $included_limit = max(1, (int) $included_limit);
    $remaining      = max(0, (int) $remaining);

    if ($is_unlimited) {
        return 'Ton abonnement inclut ' . $included_limit . ' modifications par période. Tu bénéficies actuellement de modifications illimitées. Chaque demande te permet de modifier une seule information ou un seul bloc lié du formulaire. Tous les autres champs resteront verrouillés.';
    }

    if ($remaining > $included_limit) {
        $extra = $remaining - $included_limit;

        return 'Ton abonnement inclut ' . $included_limit . ' modifications par période. Tu disposes actuellement de ' . $remaining . ' modifications au total, dont ' . $extra . ' modification' . ($extra > 1 ? 's' : '') . ' supplémentaire' . ($extra > 1 ? 's' : '') . ' achetée' . ($extra > 1 ? 's' : '') . '. Chaque demande te permet de modifier une seule information ou un seul bloc lié du formulaire. Tous les autres champs resteront verrouillés.';
    }

    return 'Ton abonnement inclut ' . $included_limit . ' modifications par période. Tu disposes actuellement de ' . $remaining . ' modification' . ($remaining > 1 ? 's' : '') . ' disponible' . ($remaining > 1 ? 's' : '') . '. Chaque demande te permet de modifier une seule information ou un seul bloc lié du formulaire. Tous les autres champs resteront verrouillés.';
}

function sa_get_modification_page_url($order = null) {
    $url = home_url('/' . SA_PAGE_USE_MODIFICATION . '/');

    if ($order && !empty($order->id)) {
        $url = add_query_arg('order_id', (int) $order->id, $url);
    }

    return $url;
}

function sa_get_requested_order_for_modification() {
    if (!is_user_logged_in()) {
        return false;
    }

    $user_id = get_current_user_id();
    $order_id = !empty($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

    if ($order_id <= 0) {
        $order = sa_get_current_order_from_session_or_user();
    } else {
        $order = false;
        $orders = sa_get_user_paid_orders($user_id);

        foreach ($orders as $candidate) {
            if ((int) $candidate->id === (int) $order_id) {
                $order = $candidate;
                break;
            }
        }
    }

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        return false;
    }

    if ((int) $order->wp_user_id !== (int) $user_id) {
        return false;
    }

    return $order;
}

function sa_get_order_onboarding_entry_id($order) {
    if (!$order || empty($order->wp_user_id) || empty($order->id)) {
        return 0;
    }

    return (int) sa_get_order_meta((int) $order->wp_user_id, (int) $order->id, 'onboarding_entry_id', 0);
}

function sa_get_fluentform_submission_response($entry_id) {
    global $wpdb;

    $entry_id = (int) $entry_id;
    if ($entry_id <= 0) {
        return [];
    }

    $table = $wpdb->prefix . 'fluentform_submissions';

    $response_json = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT response FROM {$table} WHERE id = %d LIMIT 1",
            $entry_id
        )
    );

    if (!$response_json) {
        return [];
    }

    $response = json_decode($response_json, true);

    return is_array($response) ? $response : [];
}

function sa_find_latest_onboarding_entry_for_user($user_email) {
    global $wpdb;

    $user_email = trim((string) $user_email);
    if (!$user_email) {
        return 0;
    }

    $table = $wpdb->prefix . 'fluentform_submissions';

    $entry_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id
             FROM {$table}
             WHERE form_id = %d
               AND response LIKE %s
             ORDER BY id DESC
             LIMIT 1",
            (int) SA_ONBOARDING_FORM_ID,
            '%' . $wpdb->esc_like($user_email) . '%'
        )
    );

    return (int) $entry_id;
}

function sa_get_order_original_onboarding_data($order) {
    if (!$order || empty($order->wp_user_id) || empty($order->id)) {
        return [];
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    // 1) snapshot déjà sauvegardé
    $saved_snapshot = sa_get_order_meta($user_id, $order_id, 'onboarding_snapshot', []);

    if (is_array($saved_snapshot) && !empty($saved_snapshot)) {
        return $saved_snapshot;
    }

    // 2) entry id déjà connu
    $entry_id = (int) sa_get_order_meta($user_id, $order_id, 'onboarding_entry_id', 0);

    // 3) fallback auto pour anciennes commandes
    if ($entry_id <= 0) {
        $user = get_user_by('id', $user_id);

        if ($user && !empty($user->user_email)) {
            $entry_id = sa_find_latest_onboarding_entry_for_user($user->user_email);

            if ($entry_id > 0) {
                sa_update_order_meta($user_id, $order_id, 'onboarding_entry_id', $entry_id);
            }
        }
    }

    if ($entry_id <= 0) {
        return [];
    }

    $response = sa_get_fluentform_submission_response($entry_id);

    if (is_array($response) && !empty($response)) {
        sa_update_order_meta($user_id, $order_id, 'onboarding_snapshot', $response);
        return $response;
    }

    return [];
}

function sa_get_modification_allowed_fields() {
    return [
        'input_text',
        'input_text_1',
        'input_text_2',
        'input_text_3',
        'input_text_4',
        'input_text_5',
        'input_text_6',
        'input_text_7',
        'input_text_8',
        'input_text_9',
        'input_text_10',
        'description',
        'textarea',
        'text_area',
        'input_email',
        'numeric-field',
        'dropdown',
        'select',
    ];
}

function sa_get_comparable_modification_data($posted_data, $original_data) {
    $clean = [];

    if (!is_array($posted_data) || !is_array($original_data)) {
        return $clean;
    }

    foreach ($original_data as $key => $original_value) {
        if (!array_key_exists($key, $posted_data)) {
            continue;
        }

        $value = $posted_data[$key];

        if (is_array($value)) {
            $clean[$key] = array_map('sanitize_text_field', wp_unslash($value));
        } else {
            $clean[$key] = sanitize_text_field(wp_unslash($value));
        }
    }

    return $clean;
}

function sa_normalize_modification_value($value) {
    if (is_array($value)) {
        ksort($value);

        foreach ($value as $key => $subValue) {
            $value[$key] = sa_normalize_modification_value($subValue);
        }

        return $value;
    }

    if (is_bool($value)) {
        return $value ? '1' : '0';
    }

    if ($value === null) {
        return '';
    }

    return trim((string) $value);
}

function sa_get_modification_group_fields($group_key, $original_data = []) {
    $groups = [
        // Bloc coordonnées
        'contact_details' => ['names', 'names_1', 'email'],

        // Bloc couverture
        'book_cover' => ['file-upload_2'],

        // Fallbacks anciens / autres groupes si besoin
        'book_title' => ['input_text'],
        'author_name' => ['input_text_1'],
        'book_description' => ['description'],
        'author_bio' => ['textarea'],
        'email_capture' => ['input_email'],
        'cta_button' => ['input_text_2'],
        'cta_link' => ['input_text_3'],
        'primary_color' => ['input_text_4'],
        'secondary_color' => ['input_text_5'],
        'social_links' => ['input_text_6', 'input_text_7', 'input_text_8', 'input_text_9'],
    ];

    if (!empty($groups[$group_key]) && is_array($groups[$group_key])) {
        return $groups[$group_key];
    }

    // Fallback : si le group_key correspond directement à un champ existant
    if ($group_key && is_array($original_data) && array_key_exists($group_key, $original_data)) {
        return [$group_key];
    }

    return [];
}

function sa_get_changed_modification_fields($new_data, $original_data, $allowed_fields = []) {
    $changed = [];

    if (!is_array($new_data) || !is_array($original_data)) {
        return $changed;
    }

    if (empty($allowed_fields)) {
        $allowed_fields = sa_get_modification_allowed_fields();
    }

    foreach ($allowed_fields as $field_key) {
        if (!array_key_exists($field_key, $original_data) && !array_key_exists($field_key, $new_data)) {
            continue;
        }

        $old_value = array_key_exists($field_key, $original_data) ? sa_normalize_modification_value($original_data[$field_key]) : '';
        $new_value = array_key_exists($field_key, $new_data) ? sa_normalize_modification_value($new_data[$field_key]) : '';

        if ($old_value !== $new_value) {
            $changed[] = $field_key;
        }
    }

    return $changed;
}

function sa_send_modification_admin_email($user_id, $order_id, $group_key, $changed_fields, $original_data, $new_response) {
    $admin_email = get_option('admin_email');
    if (!$admin_email) {
        return;
    }

    $user = get_user_by('id', $user_id);
    $user_email = ($user && !empty($user->user_email)) ? $user->user_email : '';

    $subject = 'Nouvelle demande de modification SiteAuteur';

    $message  = "Une nouvelle demande de modification a été envoyée.\n\n";
    $message .= "Utilisateur ID : " . (int) $user_id . "\n";
    $message .= "Email : " . $user_email . "\n";
    $message .= "Commande ID : " . (int) $order_id . "\n";
    $message .= "Bloc modifié : " . $group_key . "\n";
    $message .= "Champs modifiés : " . implode(', ', (array) $changed_fields) . "\n\n";
    $message .= "Anciennes données :\n" . print_r($original_data, true) . "\n\n";
    $message .= "Nouvelles données :\n" . print_r($new_response, true);

    wp_mail($admin_email, $subject, $message);
}

function sa_log_modification_request($user_id, $order_id, $group_key, $changed_fields, $original_data, $new_response, $entry_id) {
    $history = sa_get_order_meta($user_id, $order_id, 'modification_history', []);

    if (!is_array($history)) {
        $history = [];
    }

    $history[] = [
        'date'           => current_time('mysql'),
        'group_key'      => sanitize_text_field($group_key),
        'changed_fields' => array_map('sanitize_text_field', (array) $changed_fields),
        'entry_id'       => (int) $entry_id,
        'old_data'       => $original_data,
        'new_data'       => $new_response,
    ];

    sa_update_order_meta($user_id, $order_id, 'modification_history', $history);

    sa_update_order_meta($user_id, $order_id, 'last_modification_request', [
        'date'           => current_time('mysql'),
        'group_key'      => sanitize_text_field($group_key),
        'changed_fields' => array_map('sanitize_text_field', (array) $changed_fields),
        'entry_id'       => (int) $entry_id,
    ]);

    sa_update_order_meta($user_id, $order_id, 'last_modification_entry_id', (int) $entry_id);
}

function sa_update_order_onboarding_snapshot_after_modification($user_id, $order_id, $new_response) {
    if (!is_array($new_response) || empty($new_response)) {
        return;
    }

    sa_update_order_meta($user_id, $order_id, 'onboarding_snapshot', $new_response);
}

add_action('wp_ajax_sa_save_modification', 'sa_ajax_save_modification');

function sa_ajax_save_modification() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Accès refusé.'], 403);
    }

    check_ajax_referer('sa_save_modification', 'nonce');

    $user_id  = get_current_user_id();
    $order_id = !empty($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
    $group_key = !empty($_POST['group_key']) ? sanitize_text_field($_POST['group_key']) : '';

    if (!$order_id || !$group_key) {
        wp_send_json_error(['message' => 'Requête invalide.'], 400);
    }

    $order = sa_get_requested_order_for_modification();

    if (!$order || (int) $order->id !== $order_id || (int) $order->wp_user_id !== $user_id) {
        wp_send_json_error(['message' => 'Commande introuvable.'], 404);
    }

    if (sa_order_has_unlimited_modifications($user_id, $order_id)) {
        $remaining = PHP_INT_MAX;
    } else {
        $remaining = sa_get_order_modifications_remaining($user_id, $order_id);
        if ($remaining < 1) {
            wp_send_json_error(['message' => 'Aucune modification restante.'], 403);
        }
    }

    $original_data = sa_get_order_original_onboarding_data($order);
    if (empty($original_data) || !is_array($original_data)) {
        wp_send_json_error(['message' => 'Impossible de charger les données initiales.'], 500);
    }

    $raw_payload = !empty($_POST['payload']) ? wp_unslash($_POST['payload']) : '';
    $decoded_payload = json_decode($raw_payload, true);

    if (!is_array($decoded_payload)) {
        wp_send_json_error(['message' => 'Données de modification invalides.'], 400);
    }

    $new_data = sa_get_comparable_modification_data($decoded_payload, $original_data);
    $group_fields = sa_get_modification_group_fields($group_key, $original_data);
    
    if (empty($group_fields)) {
        wp_send_json_error(['message' => 'Bloc de modification invalide.'], 400);
    }

    $changed_fields = sa_get_changed_modification_fields($new_data, $original_data, $group_fields);

    if (empty($changed_fields)) {
        wp_send_json_error([
            'message' => 'Aucune modification détectée.',
        ], 400);
    }

    $entry_id = time(); // placeholder

    // On fusionne les nouvelles valeurs avec le snapshot d'origine
    $updated_snapshot = $original_data;

    foreach ($new_data as $key => $value) {
        $updated_snapshot[$key] = $value;
    }

    sa_update_order_onboarding_snapshot_after_modification($user_id, $order_id, $updated_snapshot);

    sa_log_modification_request(
        $user_id,
        $order_id,
        $group_key,
        $changed_fields,
        $original_data,
        $updated_snapshot,
        $entry_id
    );

    sa_send_modification_admin_email(
        $user_id,
        $order_id,
        $group_key,
        $changed_fields,
        $original_data,
        $updated_snapshot
    );

    if (!sa_order_has_unlimited_modifications($user_id, $order_id)) {
        $new_remaining = max(0, ((int) sa_get_order_modifications_remaining($user_id, $order_id)) - 1);
        sa_set_order_modifications_remaining($user_id, $order_id, $new_remaining);
    } else {
        $new_remaining = sa_get_order_modifications_remaining($user_id, $order_id);
    }

    wp_send_json_success([
        'message' => 'Ta demande de modification a bien été envoyée.',
        'remaining' => $new_remaining,
        'changed_fields' => $changed_fields,
        'data' => $updated_snapshot,
    ]);
}

function sa_get_unlimited_modifications_days_left($user_id, $order_id) {
    $until = (int) sa_get_order_unlimited_modifications_until($user_id, $order_id);

    if ($until <= 0) {
        return 0;
    }

    $now = current_time('timestamp');

    if ($until <= $now) {
        return 0;
    }

    return (int) ceil(($until - $now) / DAY_IN_SECONDS);
}

function sa_render_use_modification_form() {
    if (!is_user_logged_in()) {
        return '<p>Vous devez être connecté pour accéder à cette page.</p>';
    }

    $order = sa_get_requested_order_for_modification();

    if (!$order) {
        return '<p>Commande introuvable.</p>';
    }

    $user_id = get_current_user_id();
    $state   = sa_get_order_state($user_id, (int) $order->id);

    if ($state !== 'site_live') {
        return sa_render_template('account-use-modification', [
            'order'             => $order,
            'remaining'         => 0,
            'original_data'     => [],
            'is_unavailable'    => true,
            'unavailable_state' => $state,
        ]);
    }

    $remaining    = sa_get_order_modifications_remaining($user_id, (int) $order->id);
    $is_unlimited = sa_order_has_unlimited_modifications($user_id, (int) $order->id);

    if (!$is_unlimited && $remaining < 1) {
        return '<p>Tu as déjà utilisé toutes tes modifications disponibles pour cette période.</p>';
    }

    $original_data = sa_get_order_original_onboarding_data($order);

    if (empty($original_data) || !is_array($original_data)) {
        return '<p>Impossible de charger les données du formulaire initial.</p>';
    }

    return sa_render_template('account-use-modification', [
        'order'         => $order,
        'remaining'     => $remaining,
        'original_data' => $original_data,
    ]);
}

function sa_get_order_extra_modification_invoices($user_id, $order_id) {
    $items = sa_get_order_meta($user_id, $order_id, 'extra_modification_invoices', []);

    return is_array($items) ? $items : [];
}

function sa_add_order_extra_modification_invoice($user_id, $order_id, $invoice_data) {
    $items = sa_get_order_extra_modification_invoices($user_id, $order_id);

    $stripe_id = !empty($invoice_data['stripe_id']) ? (string) $invoice_data['stripe_id'] : '';

    if ($stripe_id) {
        foreach ($items as $existing) {
            if (!empty($existing['stripe_id']) && $existing['stripe_id'] === $stripe_id) {
                return;
            }
        }
    }

    $items[] = [
        'source'      => 'extra_modification',
        'type'        => 'Modification supplémentaire',
        'created'     => !empty($invoice_data['created']) ? (int) $invoice_data['created'] : time(),
        'amount_paid' => isset($invoice_data['amount_paid']) ? (int) $invoice_data['amount_paid'] : 0,
        'status'      => !empty($invoice_data['status']) ? sanitize_text_field($invoice_data['status']) : 'paid',
        'invoice_pdf' => !empty($invoice_data['invoice_pdf']) ? esc_url_raw($invoice_data['invoice_pdf']) : '',
        'hosted_url'  => !empty($invoice_data['hosted_url']) ? esc_url_raw($invoice_data['hosted_url']) : '',
        'stripe_id'   => $stripe_id,
    ];

    sa_update_order_meta($user_id, $order_id, 'extra_modification_invoices', $items);
}

function sa_get_order_extra_modification_documents_for_account($user_id, $order_id) {
    return sa_get_order_extra_modification_invoices($user_id, $order_id);
}