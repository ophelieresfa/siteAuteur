<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * RÉINJECTION DES UPLOADS DANS FLUENT FORMS
 * =========================
 */

add_filter('fluentform/insert_response_data', 'sa_inject_uploaded_files_into_fluentform_entry', 10, 3);

/**
 * =========================
 * BYPASS VALIDATION COUVERTURE EN MODE MODIFICATION
 * =========================
 */

function sa_is_current_modification_group($group_or_field) {
    $selected_group = !empty($_POST['sa_selected_edit_group'])
        ? sanitize_text_field(wp_unslash($_POST['sa_selected_edit_group']))
        : '';

    $selected_field = !empty($_POST['sa_selected_edit_field'])
        ? sanitize_text_field(wp_unslash($_POST['sa_selected_edit_field']))
        : '';

    return $selected_group === $group_or_field || $selected_field === $group_or_field;
}

/**
 * IMPORTANT :
 * Ici, on suppose que la couverture du livre correspond au champ Fluent Forms
 * "image-upload_2", car ton plugin mappe déjà :
 * sa_uploaded_image_upload_2 => image-upload_2
 *
 * Si ton vrai champ couverture a un autre input_key, remplace "image-upload_2".
 */
add_filter('fluentform/validate_input_item_image-upload_2', 'sa_skip_cover_required_validation_in_modification', 10, 5);
add_filter('fluentform/validate_input_item_file-upload_2', 'sa_skip_cover_required_validation_in_modification', 10, 5);

function sa_skip_cover_required_validation_in_modification($errorMessage, $field, $formData, $fields, $form) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return $errorMessage;
    }

    if (!sa_is_modification_request_mode()) {
        return $errorMessage;
    }

    /**
     * Si on est en train de modifier la couverture elle-même,
     * on garde la validation normale.
     */
    if (sa_is_current_modification_group('image-upload_2') || sa_is_current_modification_group('cover')) {
        return $errorMessage;
    }

    /**
     * Sinon, on ignore l'erreur "couverture requise"
     * pendant une modification d'un autre champ.
     */
    return '';
}

function sa_inject_uploaded_files_into_fluentform_entry($form_data, $form, $insert_data) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return $form_data;
    }

    if (sa_is_modification_request_mode()) {
        $order_id = !empty($_POST['sa_order_id']) ? (int) $_POST['sa_order_id'] : 0;
        $selected_field = !empty($_POST['sa_selected_edit_field']) ? sanitize_text_field($_POST['sa_selected_edit_field']) : '';
        $selected_group = !empty($_POST['sa_selected_edit_group']) ? sanitize_text_field($_POST['sa_selected_edit_group']) : sa_get_modification_group_from_field($selected_field);
        if ($order_id > 0 && is_user_logged_in()) {
            $user_id = get_current_user_id();
            $orders  = sa_get_user_paid_orders($user_id);
            $order   = false;

            foreach ($orders as $candidate) {
                if ((int) $candidate->id === $order_id) {
                    $order = $candidate;
                    break;
                }
            }

            if ($order) {
                $remaining     = sa_get_order_modifications_remaining($user_id, $order_id);
                $original_data = sa_get_order_original_onboarding_data($order);

                if ($remaining > 0 && !empty($original_data) && is_array($original_data)) {
                $allowed_group_fields = sa_get_modification_group_fields($selected_group, $original_data);

                foreach ($original_data as $key => $original_value) {
                    if (in_array($key, $allowed_group_fields, true)) {
                        continue;
                    }

                    $form_data[$key] = $original_value;
                }
                }
            }
        }
    }

    $upload_map = [
        'sa_uploaded_file_upload_2'  => 'file-upload_2',
        'sa_uploaded_file_upload_3'  => 'file-upload_3',
        'sa_uploaded_image_upload_2' => 'image-upload_2',
        'sa_uploaded_file_upload_4'  => 'file-upload_4',
        'sa_uploaded_file_upload'    => 'file-upload',
        'sa_uploaded_file_upload_6'  => 'file_upload_6',
    ];

    foreach ($upload_map as $hidden_key => $real_field_key) {
        if (empty($_POST[$hidden_key])) {
            continue;
        }

        $raw_value = wp_unslash($_POST[$hidden_key]);
        $raw_value = trim((string) $raw_value);

        if ($raw_value === '') {
            continue;
        }

        $decoded = json_decode($raw_value, true);

        if (is_array($decoded)) {
            $urls = array_values(array_filter(array_map('esc_url_raw', $decoded)));
        } else {
            $urls = [esc_url_raw($raw_value)];
        }

        if (empty($urls)) {
            continue;
        }

        $form_data[$real_field_key] = $urls;
    }

    return $form_data;
}

/**
 * =========================
 * PATCH FINAL DES UPLOADS DANS LA SOUMISSION FLUENT FORMS
 * =========================
 */

add_action('fluentform/submission_inserted', 'sa_patch_uploaded_files_into_submission_response', 5, 3);

function sa_patch_uploaded_files_into_submission_response($entry_id, $form_data, $form) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return;
    }

    $session_id = !empty($_REQUEST['session_id']) ? sanitize_text_field($_REQUEST['session_id']) : '';
    $owner_key  = sa_get_onboarding_owner_key();

    $draft = [
        'fields'  => [],
        'uploads' => []
    ];

    if ($owner_key) {
        $draft = sa_get_onboarding_draft($owner_key);
    }

    if (empty($draft['uploads']) && $session_id) {
        $draft = sa_get_onboarding_draft('session_' . $session_id);
    }

    if (empty($draft['uploads']) && is_user_logged_in()) {
        $draft = sa_get_onboarding_draft('user_' . get_current_user_id());
    }

    if (empty($draft['uploads']) || !is_array($draft['uploads'])) {
        return;
    }

    $upload_map = [
        'file-upload_2'  => 'file-upload_2',
        'file-upload_3'  => 'file-upload_3',
        'image-upload_2' => 'image-upload_2',
        'file-upload_4'  => 'file-upload_4',
        'file-upload'    => 'file-upload',
        'file_upload_6'  => 'file_upload_6',
    ];

    global $wpdb;
    $table = $wpdb->prefix . 'fluentform_submissions';

    $response_json = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT response FROM {$table} WHERE id = %d",
            $entry_id
        )
    );

    $response = json_decode($response_json, true);
    if (!is_array($response)) {
        $response = [];
    }

    foreach ($upload_map as $draft_key => $response_key) {
        if (empty($draft['uploads'][$draft_key]['url'])) {
            continue;
        }

        $url = esc_url_raw($draft['uploads'][$draft_key]['url']);
        if (!$url) {
            continue;
        }

        $response[$response_key] = [$url];
    }

    $wpdb->update(
        $table,
        [
            'response' => wp_json_encode($response)
        ],
        [
            'id' => $entry_id
        ],
        [
            '%s'
        ],
        [
            '%d'
        ]
    );
}

add_action('fluentform/submission_inserted', 'sa_store_onboarding_snapshot_after_submission', 6, 3);

function sa_store_onboarding_snapshot_after_submission($entry_id, $form_data, $form) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return;
    }

    if (sa_is_modification_request_mode()) {
        return;
    }

    $order = false;

    $session_id = !empty($_REQUEST['session_id'])
        ? sanitize_text_field($_REQUEST['session_id'])
        : '';

    if ($session_id) {
        $order = sa_get_order_by_session_id($session_id);
    }

    if ((!$order || empty($order->id)) && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $orders = sa_get_user_paid_orders($user_id);

        if (!empty($orders) && !empty($orders[0]->id)) {
            $order = $orders[0];
        }
    }

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        return;
    }

    $response = sa_get_fluentform_submission_response((int) $entry_id);

    if (!empty($response) && is_array($response)) {
        sa_update_order_meta((int) $order->wp_user_id, (int) $order->id, 'onboarding_snapshot', $response);
    }
}

/**
 * =========================
 * ETAT COMMANDE APRÈS SOUMISSION FINALE
 * =========================
 */

add_action('fluentform/submission_inserted', 'sa_mark_order_submitted_after_submission', 8, 3);

function sa_mark_order_submitted_after_submission($entry_id, $form_data, $form) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return;
    }

    if (sa_is_modification_request_mode()) {
        return;
    }

    $order = false;

    $session_id = !empty($_REQUEST['session_id'])
        ? sanitize_text_field($_REQUEST['session_id'])
        : '';

    if ($session_id) {
        $order = sa_get_order_by_session_id($session_id);
    }

    if ((!$order || empty($order->id)) && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $orders = sa_get_user_paid_orders($user_id);

        if (!empty($orders) && !empty($orders[0]->id)) {
            $order = $orders[0];
        }
    }

    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        return;
    }

    $order_id = (int) $order->id;
    $user_id  = (int) $order->wp_user_id;

    $current_state = sa_get_order_state($user_id, $order_id);

    sa_update_order_meta($user_id, $order_id, 'state', 'building');
    sa_update_order_meta($user_id, $order_id, 'onboarding_started', 1);
    sa_update_order_meta($user_id, $order_id, 'onboarding_complete', 1);
    sa_update_order_meta($user_id, $order_id, 'onboarding_entry_id', (int) $entry_id);

    if ($current_state !== 'building' && function_exists('sa_send_project_status_email')) {
        sa_send_project_status_email($user_id, $order_id, 'building');
    }
}

/**
 * =========================
 * CLEAR DRAFT APRÈS SOUMISSION FINALE
 * =========================
 */

add_action('fluentform/submission_inserted', 'sa_clear_onboarding_draft_after_submission', 10, 3);

function sa_clear_onboarding_draft_after_submission($entry_id, $form_data, $form) {
    if (empty($form) || empty($form->id)) {
        return;
    }

    if ((int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return;
    }

    if (sa_is_modification_request_mode()) {
        return;
    }

    $owner_key = sa_get_onboarding_owner_key();
    $session_id = !empty($_REQUEST['session_id']) ? sanitize_text_field($_REQUEST['session_id']) : '';

    if ($owner_key) {
        sa_delete_onboarding_draft($owner_key);
    }

    if ($session_id) {
        sa_delete_onboarding_draft('session_' . $session_id);
    }

    $user_id = get_current_user_id();
    if ($user_id) {
        sa_delete_onboarding_draft('user_' . $user_id);
    }
}

add_action('fluentform/submission_inserted', 'sa_handle_modification_request_submission', 12, 3);

function sa_handle_modification_request_submission($entry_id, $form_data, $form) {
    if (empty($form) || empty($form->id) || (int) $form->id !== SA_ONBOARDING_FORM_ID) {
        return;
    }

    if (!sa_is_modification_request_mode()) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    $user_id = get_current_user_id();
    $order_id = !empty($_POST['sa_order_id']) ? (int) $_POST['sa_order_id'] : 0;
    $selected_field = !empty($_POST['sa_selected_edit_field']) ? sanitize_text_field($_POST['sa_selected_edit_field']) : '';
    $selected_group = !empty($_POST['sa_selected_edit_group']) ? sanitize_text_field($_POST['sa_selected_edit_group']) : sa_get_modification_group_from_field($selected_field);

    if ($order_id <= 0 || !$selected_group) {
        return;
    }

    $orders = sa_get_user_paid_orders($user_id);
    $order  = false;

    foreach ($orders as $candidate) {
        if ((int) $candidate->id === $order_id) {
            $order = $candidate;
            break;
        }
    }

    if (!$order) {
        return;
    }

    $remaining = sa_get_order_modifications_remaining($user_id, $order_id);
    $has_unlimited_modifications = sa_order_has_unlimited_modifications($user_id, $order_id);

    if (!$has_unlimited_modifications && $remaining < 1) {
        return;
    }

    $original_data = sa_get_order_original_onboarding_data($order);
    if (empty($original_data) || !is_array($original_data)) {
        return;
    }

    $new_response = sa_get_fluentform_submission_response((int) $entry_id);
    if (empty($new_response) || !is_array($new_response)) {
        return;
    }

    $group_fields = sa_get_modification_group_fields($selected_group, $original_data);
    if (empty($group_fields)) {
        return;
    }

    $changed_fields = sa_get_changed_fields_for_group($original_data, $new_response, $group_fields);

    // Rien n’a changé => on ne décrémente pas
    if (empty($changed_fields)) {
        return;
    }

    // Vérification de sécurité :
    // on refuse si des champs hors groupe ont changé
    $all_changed_fields = sa_get_changed_fields_from_data($original_data, $new_response);
    $unauthorized_changes = array_diff($all_changed_fields, $group_fields);

    if (!empty($unauthorized_changes)) {
        return;
    }

    sa_log_modification_request(
        $user_id,
        $order_id,
        $selected_group,
        $changed_fields,
        $original_data,
        $new_response,
        $entry_id
    );

    sa_send_modification_admin_email(
        $order,
        $selected_group,
        $changed_fields,
        $original_data,
        $new_response
    );

    sa_update_order_onboarding_snapshot_after_modification(
        $user_id,
        $order_id,
        $new_response
    );

    if (!$has_unlimited_modifications) {
        sa_set_order_modifications_remaining($user_id, $order_id, max(0, $remaining - 1));
    }
}
