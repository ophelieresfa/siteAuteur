<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * STATE FUNCTIONS
 * =========================
 */

function sa_get_order_state_config() {
    return [
        'paid' => [
            'label' => 'Paiement confirmé',
            'description' => 'Ton paiement a bien été reçu. Tu peux maintenant compléter le formulaire de renseignement pour lancer la création de ton site.',
            'priority' => 1,
            'color' => '#8b949e',
        ],
        'onboarding_started' => [
            'label' => 'Formulaire commencé',
            'description' => 'Tu as déjà commencé à remplir ton formulaire. Tu peux le reprendre à tout moment.',
            'priority' => 2,
            'color' => '#0ea5e9',
        ],
        'submitted' => [
            'label' => 'En cours de création',
            'description' => 'Nous avons bien reçu tes informations. La création de ton site est en cours.',
            'priority' => 3,
            'color' => '#6366f1',
        ],
        'building' => [
            'label' => 'En cours de création',
            'description' => 'Nous avons bien reçu tes informations. La création de ton site est en cours.',
            'priority' => 4,
            'color' => '#6366f1',
        ],
        'site_live' => [
            'label' => 'Site en ligne',
            'description' => 'Ton site est désormais en ligne et accessible.',
            'priority' => 5,
            'color' => '#22c55e',
        ],
        'paused' => [
            'label' => 'Projet suspendu',
            'description' => 'Ton projet est actuellement suspendu.',
            'priority' => 6,
            'color' => '#ef4444',
        ],
    ];
}

function sa_get_order_state_label($state) {
    $config = sa_get_order_state_config();
    return $config[$state]['label'] ?? 'Projet';
}

function sa_get_order_state_description($state) {
    $config = sa_get_order_state_config();
    return $config[$state]['description'] ?? '';
}

function sa_get_order_state_priority($state) {
    $config = sa_get_order_state_config();
    return $config[$state]['priority'] ?? 999;
}

function sa_get_order_state_color($state) {
    $config = sa_get_order_state_config();
    return $config[$state]['color'] ?? '#999';
}

function sa_get_account_state_badge($state, $label = '') {
    $label = $label ?: sa_get_order_state_label($state);
    $color = sa_get_order_state_color($state);

    return '<span style="
        background:' . esc_attr($color) . ';
        color:#fff;
        padding:6px 12px;
        border-radius:999px;
        font-size:13px;
        font-weight:600;
        display:inline-block;
        white-space:nowrap;
    ">' . esc_html($label) . '</span>';
}

function sa_get_order_primary_action_text($state) {
    switch ($state) {
        case 'paid':
            return 'Commencer le formulaire';

        case 'onboarding_started':
            return 'Reprendre le formulaire';

        case 'submitted':
        case 'building':
            return 'Voir le projet';

        case 'site_live':
            return 'Voir mon site';

        case 'paused':
            return 'Voir le projet';

        default:
            return 'Voir le projet';
    }
}

function sa_get_order_primary_action_url($order, $state) {
    if (!$order || empty($order->id)) {
        return home_url('/' . SA_PAGE_ACCOUNT . '/');
    }

    $session_id = !empty($order->stripe_session_id)
        ? sanitize_text_field($order->stripe_session_id)
        : '';

    switch ($state) {
        case 'paid':
        case 'onboarding_started':
            if ($session_id) {
                return home_url('/' . SA_PAGE_ONBOARDING . '/?session_id=' . rawurlencode($session_id));
            }
            return home_url('/' . SA_PAGE_ONBOARDING . '/');

        case 'submitted':
        case 'building':
        case 'paused':
            return home_url('/' . SA_PAGE_ACCOUNT . '/');

        case 'site_live':
            return home_url('/' . SA_PAGE_ACCOUNT . '/');

        default:
            return home_url('/' . SA_PAGE_ACCOUNT . '/');
    }
}

function sa_get_order_display_date($order) {
    if (!empty($order->created_at)) {
        return date_i18n('d/m/Y', strtotime($order->created_at));
    }

    if (!empty($order->date_created)) {
        return date_i18n('d/m/Y', strtotime($order->date_created));
    }

    return date_i18n('d/m/Y');
}

function sa_get_order_project_name($order, $index = 1) {
    return 'Projet ' . (int) $index;
}