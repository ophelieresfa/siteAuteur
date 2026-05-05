<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * AJAX DRAFT / UPLOADS
 * =========================
 */

add_action('wp_ajax_sa_get_onboarding_draft', 'sa_ajax_get_onboarding_draft');
add_action('wp_ajax_nopriv_sa_get_onboarding_draft', 'sa_ajax_get_onboarding_draft');

function sa_ajax_get_onboarding_draft() {
    check_ajax_referer('sa_onboarding_nonce', 'nonce');

    $owner_key = sa_get_onboarding_owner_key();

    if (!$owner_key) {
        wp_send_json_error(['message' => 'Aucun identifiant de brouillon disponible'], 400);
    }

    $draft = sa_get_onboarding_draft($owner_key);

    wp_send_json_success($draft);
}

add_action('wp_ajax_sa_save_onboarding_fields', 'sa_ajax_save_onboarding_fields');
add_action('wp_ajax_nopriv_sa_save_onboarding_fields', 'sa_ajax_save_onboarding_fields');

function sa_ajax_save_onboarding_fields() {
    check_ajax_referer('sa_onboarding_nonce', 'nonce');

    $owner_key = sa_get_onboarding_owner_key();

    if (!$owner_key) {
        wp_send_json_error(['message' => 'Aucun identifiant de brouillon disponible'], 400);
    }

    $incoming_fields = isset($_POST['fields']) ? wp_unslash($_POST['fields']) : [];

    if (!is_array($incoming_fields)) {
        $incoming_fields = [];
    }

    $clean_fields = [];

    foreach ($incoming_fields as $key => $value) {
        $clean_key = sanitize_text_field($key);

        if (is_array($value)) {
            $clean_fields[$clean_key] = array_map('sanitize_text_field', $value);
        } else {
            $clean_fields[$clean_key] = sanitize_text_field($value);
        }
    }

    $draft = sa_get_onboarding_draft($owner_key);
    $draft['fields'] = $clean_fields;

    $has_meaningful_fields = sa_draft_has_meaningful_data($draft);

    if ($has_meaningful_fields) {
        sa_save_onboarding_draft($draft, $owner_key);
    } else {
        sa_delete_onboarding_draft($owner_key);
    }

    $session_id = !empty($_POST['session_id'])
        ? sanitize_text_field($_POST['session_id'])
        : '';

    if ($session_id) {
        $order = sa_get_order_by_session_id($session_id);

        if ($order && !empty($order->id) && !empty($order->wp_user_id)) {
            $order_id = (int) $order->id;
            $user_id  = (int) $order->wp_user_id;

            if ($has_meaningful_fields) {
                sa_update_order_meta($user_id, $order_id, 'state', 'paid_onboarding_incomplete');
                sa_update_order_meta($user_id, $order_id, 'onboarding_started', 1);
            } else {
                sa_update_order_meta($user_id, $order_id, 'state', 'paid_no_onboarding_started');
                sa_delete_order_meta($user_id, $order_id, 'onboarding_started');
            }
        }
    }

    wp_send_json_success([
        'message' => 'Brouillon enregistré'
    ]);
}

add_action('wp_ajax_sa_upload_onboarding_file', 'sa_ajax_upload_onboarding_file');
add_action('wp_ajax_nopriv_sa_upload_onboarding_file', 'sa_ajax_upload_onboarding_file');

function sa_ajax_upload_onboarding_file() {
    check_ajax_referer('sa_onboarding_nonce', 'nonce');

    $owner_key = sa_get_onboarding_owner_key();
    $field_name = isset($_POST['field_name']) ? sanitize_text_field($_POST['field_name']) : '';

    if (!$owner_key) {
        wp_send_json_error(['message' => 'Aucun identifiant de brouillon disponible'], 400);
    }

    if (!$field_name) {
        wp_send_json_error(['message' => 'field_name manquant'], 400);
    }

    if (empty($_FILES['file'])) {
        wp_send_json_error(['message' => 'Aucun fichier reçu'], 400);
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    sa_delete_existing_upload_for_field($owner_key, $field_name);

    $file = $_FILES['file'];

    $overrides = [
        'test_form' => false,
        'mimes' => [
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png'          => 'image/png',
            'webp'         => 'image/webp',
            'gif'          => 'image/gif',
            'pdf'          => 'application/pdf',
            'zip'          => 'application/zip',
            'gz|gzip'      => 'application/gzip',
            'rar'          => 'application/vnd.rar',
            '7z'           => 'application/x-7z-compressed'
        ]
    ];

    $uploaded = wp_handle_upload($file, $overrides);

    if (!empty($uploaded['error'])) {
        wp_send_json_error([
            'message' => $uploaded['error']
        ], 400);
    }

    $attachment = [
        'post_mime_type' => $uploaded['type'],
        'post_title'     => sanitize_file_name(pathinfo($uploaded['file'], PATHINFO_FILENAME)),
        'post_content'   => '',
        'post_status'    => 'inherit'
    ];

    $attachment_id = wp_insert_attachment($attachment, $uploaded['file']);

    if (!is_wp_error($attachment_id)) {
        $metadata = wp_generate_attachment_metadata($attachment_id, $uploaded['file']);
        wp_update_attachment_metadata($attachment_id, $metadata);
    } else {
        $attachment_id = 0;
    }

    $draft = sa_get_onboarding_draft($owner_key);

    $draft['uploads'][$field_name] = [
        'attachment_id' => (int) $attachment_id,
        'url'           => esc_url_raw($uploaded['url']),
        'file'          => sanitize_text_field($uploaded['file']),
        'type'          => sanitize_mime_type($uploaded['type']),
        'name'          => sanitize_file_name($file['name']),
        'uploaded_at'   => current_time('mysql')
    ];

    sa_save_onboarding_draft($draft, $owner_key);

    wp_send_json_success([
        'field_name' => $field_name,
        'file'       => $draft['uploads'][$field_name]
    ]);
}

add_action('wp_ajax_sa_delete_onboarding_file', 'sa_ajax_delete_onboarding_file');
add_action('wp_ajax_nopriv_sa_delete_onboarding_file', 'sa_ajax_delete_onboarding_file');

function sa_ajax_delete_onboarding_file() {
    check_ajax_referer('sa_onboarding_nonce', 'nonce');

    $owner_key = sa_get_onboarding_owner_key();
    $field_name = isset($_POST['field_name']) ? sanitize_text_field($_POST['field_name']) : '';

    if (!$owner_key || !$field_name) {
        wp_send_json_error(['message' => 'Paramètres manquants'], 400);
    }

    $draft = sa_get_onboarding_draft($owner_key);

    if (!empty($draft['uploads'][$field_name]['attachment_id'])) {
        $attachment_id = (int) $draft['uploads'][$field_name]['attachment_id'];
        if ($attachment_id > 0) {
            wp_delete_attachment($attachment_id, true);
        }
    } elseif (!empty($draft['uploads'][$field_name]['file'])) {
        $file_path = $draft['uploads'][$field_name]['file'];
        if (is_string($file_path) && file_exists($file_path)) {
            @unlink($file_path);
        }
    }

    unset($draft['uploads'][$field_name]);
    sa_save_onboarding_draft($draft, $owner_key);

    wp_send_json_success([
        'field_name' => $field_name
    ]);
}

add_action('wp_ajax_sa_clear_onboarding_draft', 'sa_ajax_clear_onboarding_draft');
add_action('wp_ajax_nopriv_sa_clear_onboarding_draft', 'sa_ajax_clear_onboarding_draft');

function sa_ajax_clear_onboarding_draft() {
    check_ajax_referer('sa_onboarding_nonce', 'nonce');

    $owner_key = sa_get_onboarding_owner_key();

    if (!$owner_key) {
        wp_send_json_error(['message' => 'Aucun identifiant de brouillon disponible'], 400);
    }

    sa_delete_onboarding_draft($owner_key);

    if (!empty($_POST['session_id'])) {
        $session_id = sanitize_text_field($_POST['session_id']);
        sa_delete_onboarding_draft('session_' . $session_id);
    }

    if (is_user_logged_in()) {
        sa_delete_onboarding_draft('user_' . get_current_user_id());
    }

    wp_send_json_success([
        'message' => 'Brouillon supprimé'
    ]);
}