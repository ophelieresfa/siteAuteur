<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * OUTILS SESSION / DRAFT
 * =========================
 */

function sa_get_onboarding_owner_key() {
    $user_id = get_current_user_id();

    if ($user_id) {
        return 'user_' . $user_id;
    }

    if (!empty($_REQUEST['session_id'])) {
        return 'session_' . sanitize_text_field($_REQUEST['session_id']);
    }

    return '';
}

function sa_get_onboarding_draft_key($owner_key) {
    return 'sa_onboarding_draft_' . md5($owner_key);
}

function sa_get_onboarding_draft($owner_key = '') {
    if (!$owner_key) {
        $owner_key = sa_get_onboarding_owner_key();
    }

    if (!$owner_key) {
        return [
            'fields'  => [],
            'uploads' => []
        ];
    }

    $draft = get_transient(sa_get_onboarding_draft_key($owner_key));

    if (!is_array($draft)) {
        $draft = [
            'fields'  => [],
            'uploads' => []
        ];
    }

    if (empty($draft['fields']) || !is_array($draft['fields'])) {
        $draft['fields'] = [];
    }

    if (empty($draft['uploads']) || !is_array($draft['uploads'])) {
        $draft['uploads'] = [];
    }

    return $draft;
}

function sa_save_onboarding_draft($draft, $owner_key = '') {
    if (!$owner_key) {
        $owner_key = sa_get_onboarding_owner_key();
    }

    if (!$owner_key) return false;

    if (!is_array($draft)) {
        $draft = [
            'fields'  => [],
            'uploads' => []
        ];
    }

    set_transient(
        sa_get_onboarding_draft_key($owner_key),
        $draft,
        30 * DAY_IN_SECONDS
    );

    return true;
}

function sa_delete_onboarding_draft($owner_key = '') {
    if (!$owner_key) {
        $owner_key = sa_get_onboarding_owner_key();
    }

    if (!$owner_key) return false;

    return delete_transient(sa_get_onboarding_draft_key($owner_key));
}

function sa_draft_has_meaningful_data($draft) {
    if (!is_array($draft)) {
        return false;
    }

    if (!empty($draft['uploads']) && is_array($draft['uploads'])) {
        foreach ($draft['uploads'] as $upload) {
            if (!empty($upload)) {
                return true;
            }
        }
    }

    if (!empty($draft['fields']) && is_array($draft['fields'])) {
        foreach ($draft['fields'] as $value) {
            if (is_array($value)) {
                $filtered = array_filter($value, function ($item) {
                    return trim((string) $item) !== '';
                });

                if (!empty($filtered)) {
                    return true;
                }
            } else {
                if (trim((string) $value) !== '') {
                    return true;
                }
            }
        }
    }

    return false;
}

function sa_delete_existing_upload_for_field($owner_key, $field_name) {
    if (!$owner_key || !$field_name) {
        return false;
    }

    $draft = sa_get_onboarding_draft($owner_key);

    if (empty($draft['uploads'][$field_name])) {
        return false;
    }

    $existing_upload = $draft['uploads'][$field_name];

    if (!empty($existing_upload['attachment_id'])) {
        $attachment_id = (int) $existing_upload['attachment_id'];

        if ($attachment_id > 0) {
            wp_delete_attachment($attachment_id, true);
        }
    } elseif (!empty($existing_upload['file'])) {
        $file_path = $existing_upload['file'];

        if (is_string($file_path) && file_exists($file_path)) {
            @unlink($file_path);
        }
    }

    unset($draft['uploads'][$field_name]);
    sa_save_onboarding_draft($draft, $owner_key);

    return true;
}

function sa_migrate_session_draft_to_user($user_id, $session_id) {
    if (!$user_id || !$session_id) return false;

    $session_key = 'session_' . sanitize_text_field($session_id);
    $user_key = 'user_' . intval($user_id);

    $session_draft = sa_get_onboarding_draft($session_key);
    $user_draft = sa_get_onboarding_draft($user_key);

    $has_session_data = !empty($session_draft['fields']) || !empty($session_draft['uploads']);
    $has_user_data = !empty($user_draft['fields']) || !empty($user_draft['uploads']);

    if ($has_session_data && !$has_user_data) {
        sa_save_onboarding_draft($session_draft, $user_key);
    }

    return true;
}