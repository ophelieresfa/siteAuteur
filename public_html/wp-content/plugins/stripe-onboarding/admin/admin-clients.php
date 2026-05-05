<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * ADMIN MENU
 * =========================
 */

add_action('admin_menu', 'sa_register_admin_clients_menu');

function sa_register_admin_clients_menu() {
    add_menu_page(
        'Commandes',
        'Commandes',
        'manage_options',
        'siteauteur-commandes',
        'sa_render_admin_clients_page',
        'dashicons-list-view',
        25
    );
}

/**
 * =========================
 * HELPERS ADMIN
 * =========================
 */

function sa_get_admin_available_states() {
    return [
        'paid'               => 'Paiement confirmé',
        'onboarding_started' => 'Formulaire commencé',
        'building'           => 'Création en cours',
        'site_live'          => 'Site en ligne',
        'paused'             => 'Projet suspendu',
    ];
}

function sa_get_all_orders_for_admin() {
    global $wpdb;

    $table = $wpdb->prefix . 'siteauteur_orders';

    $orders = $wpdb->get_results(
        "SELECT * FROM {$table} ORDER BY id DESC"
    );

    return is_array($orders) ? $orders : [];
}

function sa_handle_admin_order_status_update() {
    if (!is_admin()) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    if (empty($_POST['sa_update_order_status'])) {
        return;
    }

    check_admin_referer('sa_update_order_status_action', 'sa_update_order_status_nonce');

    $order_id  = isset($_POST['order_id']) ? absint($_POST['order_id']) : 0;
    $user_id   = isset($_POST['user_id']) ? absint($_POST['user_id']) : 0;
    $new_state = isset($_POST['new_state']) ? sanitize_text_field($_POST['new_state']) : '';

    $allowed_states = array_keys(sa_get_admin_available_states());

    if (!$order_id || !$user_id || !in_array($new_state, $allowed_states, true)) {
        wp_safe_redirect(admin_url('admin.php?page=siteauteur-commandes&sa_notice=error'));
        exit;
    }

    $current_state = sa_get_order_state($user_id, $order_id);

    $site_url = isset($_POST['site_url']) ? esc_url_raw($_POST['site_url']) : '';

    if ($new_state === 'site_live') {
        if (empty($site_url)) {
            $site_url = sa_get_order_meta($user_id, $order_id, 'site_url', '');
        }

        if (empty($site_url)) {
            wp_safe_redirect(admin_url('admin.php?page=siteauteur-commandes&sa_notice=missing_site_url'));
            exit;
        }

        sa_update_order_meta($user_id, $order_id, 'site_url', $site_url);
    }

    sa_update_order_meta($user_id, $order_id, 'state', $new_state);

    $mail_sent = false;

    if ($current_state !== $new_state && function_exists('sa_send_project_status_email')) {
        $mail_sent = sa_send_project_status_email($user_id, $order_id, $new_state, $site_url);
    }

    $notice = $mail_sent ? 'updated_mail_sent' : 'updated';

    wp_safe_redirect(admin_url('admin.php?page=siteauteur-commandes&sa_notice=' . $notice));
    exit;
}

add_action('admin_init', 'sa_handle_admin_order_status_update');

/**
 * =========================
 * STATS ADMIN
 * =========================
 */

function sa_get_admin_orders_stats($orders) {
    $stats = [
        'total'              => 0,
        'paid'               => 0,
        'onboarding_started' => 0,
        'building'           => 0,
        'site_live'          => 0,
        'paused'             => 0,
    ];

    if (!is_array($orders)) {
        return $stats;
    }

    $stats['total'] = count($orders);

    foreach ($orders as $order) {
        if (empty($order->id) || empty($order->wp_user_id)) {
            continue;
        }

        $state = sa_get_order_state((int) $order->wp_user_id, (int) $order->id);

        if (isset($stats[$state])) {
            $stats[$state]++;
        }
    }

    return $stats;
}

/**
 * =========================
 * BADGE STATUT
 * =========================
 */

function sa_get_admin_state_badge($state, $label) {
    $colors = [
        'paid'               => '#8b949e',
        'onboarding_started' => '#0ea5e9',
        'building'           => '#6366f1',
        'site_live'          => '#22c55e',
        'paused'             => '#ef4444',
    ];

    $color = isset($colors[$state]) ? $colors[$state] : '#999';

    return '<span style="
        background:' . esc_attr($color) . ';
        color:#fff;
        padding:4px 10px;
        border-radius:20px;
        font-size:12px;
        font-weight:600;
        display:inline-block;
    ">' . esc_html($label) . '</span>';
}

/**
 * =========================
 * RENDER ADMIN PAGE
 * =========================
 */

function sa_render_admin_clients_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $orders          = sa_get_all_orders_for_admin();
    $states          = sa_get_admin_available_states();
    $stats           = sa_get_admin_orders_stats($orders);
    $current_filter  = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';
    $search          = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    ?>
    <div class="wrap">
        <h1>Commandes</h1>

        <?php if (!empty($_GET['sa_notice']) && $_GET['sa_notice'] === 'updated') : ?>
            <div class="notice notice-success is-dismissible">
                <p>Le statut du projet a bien été mis à jour.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['sa_notice']) && $_GET['sa_notice'] === 'updated_mail_sent') : ?>
            <div class="notice notice-success is-dismissible">
                <p>Le statut du projet a bien été mis à jour et l’email client a été envoyé.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['sa_notice']) && $_GET['sa_notice'] === 'missing_site_url') : ?>
            <div class="notice notice-error is-dismissible">
                <p>Impossible de passer le projet en “Site en ligne” : vous devez renseigner le lien du site.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['sa_notice']) && $_GET['sa_notice'] === 'error') : ?>
            <div class="notice notice-error is-dismissible">
                <p>Impossible de mettre à jour le statut.</p>
            </div>
        <?php endif; ?>

        <div style="margin:20px 0;">
            <strong>Filtrer :</strong>

            <a href="<?php echo esc_url(admin_url('admin.php?page=siteauteur-commandes')); ?>"
               class="button <?php echo empty($current_filter) ? 'button-primary' : ''; ?>">
                Tous (<?php echo esc_html($stats['total']); ?>)
            </a>

            <?php foreach ($states as $key => $label) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=siteauteur-commandes&status=' . rawurlencode($key))); ?>"
                   class="button <?php echo $current_filter === $key ? 'button-primary' : ''; ?>">
                    <?php echo esc_html($label); ?> (<?php echo esc_html($stats[$key] ?? 0); ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <form method="get" style="margin:20px 0;">
            <input type="hidden" name="page" value="siteauteur-commandes">

            <?php if ($current_filter) : ?>
                <input type="hidden" name="status" value="<?php echo esc_attr($current_filter); ?>">
            <?php endif; ?>

            <input
                type="search"
                name="s"
                placeholder="Rechercher client, email ou commande..."
                value="<?php echo esc_attr($search); ?>"
                style="width:300px;padding:6px;"
            >

            <button type="submit" class="button">Rechercher</button>

            <?php if ($search) : ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=siteauteur-commandes')); ?>" class="button">
                    Réinitialiser
                </a>
            <?php endif; ?>
        </form>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin:20px 0;">
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Total commandes</strong><br><?php echo esc_html($stats['total']); ?>
            </div>
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Paiement confirmé</strong><br><?php echo esc_html($stats['paid']); ?>
            </div>
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Formulaire commencé</strong><br><?php echo esc_html($stats['onboarding_started']); ?>
            </div>
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Création en cours</strong><br><?php echo esc_html($stats['building']); ?>
            </div>
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Site en ligne</strong><br><?php echo esc_html($stats['site_live']); ?>
            </div>
            <div style="background:#fff;padding:14px 18px;border:1px solid #ddd;border-radius:8px;min-width:180px;">
                <strong>Projet suspendu</strong><br><?php echo esc_html($stats['paused']); ?>
            </div>
        </div>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th style="width:80px;">Commande</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Session Stripe</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut actuel</th>
                    <th>Formulaire</th>
                    <th>Client WP</th>
                    <th style="width:260px;">Changer le statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)) : ?>
                    <tr>
                        <td colspan="10">Aucune commande trouvée.</td>
                    </tr>
                <?php else : ?>
                    <?php
                    $has_results = false;
                    foreach ($orders as $order) :
                        $order_id = !empty($order->id) ? (int) $order->id : 0;
                        $user_id  = !empty($order->wp_user_id) ? (int) $order->wp_user_id : 0;

                        $current_state = ($order_id && $user_id)
                            ? sa_get_order_state($user_id, $order_id)
                            : 'paid';

                        if ($current_filter && $current_state !== $current_filter) {
                            continue;
                        }

                        $search_string = strtolower(
                            ($order->email ?? '') . ' ' .
                            ($order->first_name ?? '') . ' ' .
                            ($order->last_name ?? '') . ' ' .
                            $order_id
                        );

                        if ($search && strpos($search_string, strtolower($search)) === false) {
                            continue;
                        }

                        $has_results = true;

                        $current_state_label = $states[$current_state] ?? $current_state;
                        $amount_total        = isset($order->amount_total) ? ((float) $order->amount_total / 100) : 0;
                        $currency            = !empty($order->currency) ? strtoupper($order->currency) : '';
                    ?>
                        <tr>
                            <td>#<?php echo esc_html($order_id); ?></td>

                            <td>
                                <?php echo esc_html(trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '')) ?: '—'); ?>
                            </td>

                            <td><?php echo esc_html($order->email ?? ''); ?></td>

                            <td style="word-break:break-all;">
                                <?php echo esc_html($order->stripe_session_id ?? ''); ?>
                            </td>

                            <td>
                                <?php echo esc_html(number_format_i18n($amount_total, 2) . ' ' . $currency); ?>
                            </td>

                            <td>
                                <?php echo esc_html($order->created_at ?? '—'); ?>
                            </td>

                            <td>
                                <?php echo sa_get_admin_state_badge($current_state, $current_state_label); ?>
                            </td>

                            <td>
                                <?php if (!empty($order->stripe_session_id)) : ?>
                                    <a
                                        href="<?php echo esc_url(home_url('/onboarding/?session_id=' . rawurlencode($order->stripe_session_id))); ?>"
                                        target="_blank"
                                        class="button">
                                        Voir formulaire
                                    </a>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($order->wp_user_id)) : ?>
                                    <a
                                        href="<?php echo esc_url(admin_url('user-edit.php?user_id=' . absint($order->wp_user_id))); ?>"
                                        target="_blank"
                                        class="button">
                                        Voir client
                                    </a>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($order_id && $user_id) : ?>
                                    <form method="post" action="">
                                        <?php wp_nonce_field('sa_update_order_status_action', 'sa_update_order_status_nonce'); ?>
                                        <input type="hidden" name="sa_update_order_status" value="1">
                                        <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
                                        <input type="hidden" name="user_id" value="<?php echo esc_attr($user_id); ?>">

                                        <?php
                                        $saved_site_url = sa_get_order_meta($user_id, $order_id, 'site_url', '');
                                        ?>

                                        <select name="new_state" class="sa-admin-state-select">
                                            <?php foreach ($states as $state_key => $state_label) : ?>
                                                <option value="<?php echo esc_attr($state_key); ?>" <?php selected($current_state, $state_key); ?>>
                                                    <?php echo esc_html($state_label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <div class="sa-admin-site-url-wrap" style="margin-top:8px;<?php echo $current_state === 'site_live' ? '' : 'display:none;'; ?>">
                                            <input
                                                type="url"
                                                name="site_url"
                                                value="<?php echo esc_attr($saved_site_url); ?>"
                                                placeholder="https://site-du-client.fr"
                                                style="width:100%;max-width:250px;"
                                            >
                                        </div>

                                        <button type="submit" class="button button-primary" style="margin-top:8px;">
                                            Enregistrer
                                        </button>
                                    </form>
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (!$has_results) : ?>
                        <tr>
                            <td colspan="10">Aucun résultat pour ce filtre ou cette recherche.</td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sa-admin-state-select').forEach(function (select) {
                function toggleSiteUrlField() {
                    const form = select.closest('form');
                    if (!form) return;

                    const wrap = form.querySelector('.sa-admin-site-url-wrap');
                    if (!wrap) return;

                    if (select.value === 'site_live') {
                        wrap.style.display = 'block';
                    } else {
                        wrap.style.display = 'none';
                    }
                }

                select.addEventListener('change', toggleSiteUrlField);
                toggleSiteUrlField();
            });
        });
        </script>
    <?php
}