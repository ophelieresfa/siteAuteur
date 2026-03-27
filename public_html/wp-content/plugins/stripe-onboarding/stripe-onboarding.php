<?php
/**
 * Plugin Name: SiteAuteur Stripe Onboarding
 */

if (!defined('ABSPATH')) exit;

if (!defined('SA_STRIPE_SECRET_KEY')) {
    exit('SA_STRIPE_SECRET_KEY non définie dans wp-config.php');
}

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

/**
 * =========================
 * STRIPE
 * =========================
 */

function sa_get_stripe_session($session_id) {
    $response = wp_remote_get(
        "https://api.stripe.com/v1/checkout/sessions/" . $session_id,
        [
            'headers' => [
                'Authorization' => 'Bearer ' . SA_STRIPE_SECRET_KEY
            ]
        ]
    );

    if (is_wp_error($response)) return false;

    $body = json_decode(wp_remote_retrieve_body($response), true);

    return $body;
}

function sa_get_valid_paid_session() {
    if (empty($_GET['session_id'])) {
        return false;
    }

    $session_id = sanitize_text_field($_GET['session_id']);
    $session = sa_get_stripe_session($session_id);

    if (!$session || empty($session['id'])) {
        return false;
    }

    if (($session['payment_status'] ?? '') !== 'paid') {
        return false;
    }

    return $session;
}

/**
 * =========================
 * UTILISATEURS / COMMANDES
 * =========================
 */

function sa_find_or_create_user($email, $first_name = '', $last_name = '') {
    $user = get_user_by('email', $email);

    if ($user) {
        if (get_user_meta($user->ID, 'sa_password_created', true) === '') {
            update_user_meta($user->ID, 'sa_password_created', 1);
        }

        return $user->ID;
    }

    $password = wp_generate_password();

    $user_id = wp_insert_user([
        'user_login' => $email,
        'user_email' => $email,
        'user_pass'  => $password,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'role'       => 'subscriber'
    ]);

    if (!is_wp_error($user_id)) {
        update_user_meta($user_id, 'sa_password_created', 0);
    }

    return $user_id;
}

function sa_save_order($data) {
    global $wpdb;

    $table = $wpdb->prefix . 'siteauteur_orders';

    $exists = $wpdb->get_var($wpdb->prepare(
        "SELECT id FROM $table WHERE stripe_session_id = %s",
        $data['stripe_session_id']
    ));

    if (!$exists) {
        $wpdb->insert($table, $data);
    }
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

/**
 * =========================
 * HELPERS UPLOADS
 * =========================
 */

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

    sa_save_onboarding_draft($draft, $owner_key);

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

/**
 * =========================
 * SHORTCODES
 * =========================
 */

add_shortcode('siteauteur_confirmation_email', function () {
    $session = sa_get_valid_paid_session();
    if (!$session) return '';

    $email = sanitize_email($session['customer_details']['email'] ?? '');
    return esc_html($email);
});

add_shortcode('siteauteur_confirmation_first_name', function () {
    $session = sa_get_valid_paid_session();
    if (!$session) return '';

    $first_name = '';

    if (!empty($session['custom_fields']) && is_array($session['custom_fields'])) {
        foreach ($session['custom_fields'] as $field) {
            $label = $field['label']['custom'] ?? '';
            $value = $field['text']['value'] ?? '';

            if ($label === 'Prénom') {
                $first_name = sanitize_text_field($value);
                break;
            }
        }
    }

    return esc_html($first_name);
});

add_shortcode('siteauteur_confirmation_last_name', function () {
    $session = sa_get_valid_paid_session();
    if (!$session) return '';

    $last_name = '';

    if (!empty($session['custom_fields']) && is_array($session['custom_fields'])) {
        foreach ($session['custom_fields'] as $field) {
            $label = $field['label']['custom'] ?? '';
            $value = $field['text']['value'] ?? '';

            if ($label === 'Nom') {
                $last_name = sanitize_text_field($value);
                break;
            }
        }
    }

    return esc_html($last_name);
});

add_shortcode('siteauteur_confirmation_onboarding_url', function () {
    if (empty($_GET['session_id'])) return '';
    $session_id = sanitize_text_field($_GET['session_id']);
    return esc_url(home_url('/onboarding/?session_id=' . rawurlencode($session_id)));
});

add_shortcode('siteauteur_confirmation_reset_url', function () {
    $order = sa_get_confirmation_order();
    if (!$order || empty($order->wp_user_id)) return '';

    $user = get_user_by('id', $order->wp_user_id);
    if (!$user) return '';

    $reset_key = get_password_reset_key($user);
    if (is_wp_error($reset_key)) return '';

    $reset_url = network_site_url(
        'wp-login.php?action=rp&key=' . $reset_key . '&login=' . rawurlencode($user->user_login),
        'login'
    );

    return esc_url($reset_url);
});

add_shortcode('siteauteur_confirmation_onboarding_button', function () {
    $session = sa_get_valid_paid_session();
    if (!$session) {
        return '';
    }

    $session_id = sanitize_text_field($_GET['session_id']);
    $url = home_url('/onboarding/?session_id=' . rawurlencode($session_id));

    return '<a href="' . esc_url($url) . '" 
        style="
            background-color: #3fc095;
            color: #fff;
            padding: 10px 30px;
            border-radius: 5px;
            text-align: center;
            font-weight: 501;
            text-decoration:none;
            display:inline-block;
        ">
        Remplir le formulaire >
    </a>';
});

add_shortcode('siteauteur_confirmation_reset_button', function () {
    $session = sa_get_valid_paid_session();
    if (!$session) {
        return '';
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');
    if (!$email) return '';

    $user = get_user_by('email', $email);
    if (!$user) return '';

    $reset_key = get_password_reset_key($user);
    if (is_wp_error($reset_key)) return '';

    $session_id = sanitize_text_field($_GET['session_id'] ?? '');

    $reset_url = home_url(
        '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login) .
        '&session_id=' . rawurlencode($session_id)
    );
    
    return '<a href="' . esc_url($reset_url) . '" 
        style="
            background-color: #ffffff;
            color: #3fc095;
            padding: 10px 30px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #3fc095;
            font-weight: 501;
            text-decoration:none;
            display:inline-block;
        ">
        Définir mon mot de passe >
    </a>';
});

add_filter('the_content', function ($content) {
    if (is_admin()) {
        return $content;
    }

    if (!is_page('confirmation-commande')) {
        return $content;
    }

    $session = sa_get_valid_paid_session();

    if (!$session) {
        return '
        <div style="max-width:760px;margin:80px auto;padding:40px;text-align:center;">
            <h1 style="font-size:54px;line-height:1.1;margin-bottom:20px;color:#1f2a44;">Commande introuvable</h1>
            <p style="font-size:18px;line-height:1.6;color:#44506b;">
                Cette page n’est accessible qu’après un paiement Stripe validé.
            </p>
            <p style="margin-top:30px;">
                <a href="' . esc_url(home_url('/')) . '" style="font-size:14px;display:inline-block;padding:10px 30px;background:#43c59e;color:#fff;border-radius:5px;text-decoration:none;font-weight:600;">
                    Retour à l’accueil
                </a>
            </p>
        </div>';
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');

    $first_name = '';
    $last_name  = '';

    if (!empty($session['custom_fields']) && is_array($session['custom_fields'])) {
        foreach ($session['custom_fields'] as $field) {
            $label = $field['label']['custom'] ?? '';
            $value = $field['text']['value'] ?? '';

            if ($label === 'Prénom') {
                $first_name = sanitize_text_field($value);
            }

            if ($label === 'Nom') {
                $last_name = sanitize_text_field($value);
            }
        }
    }

    $user_id = sa_find_or_create_user($email, $first_name, $last_name);

    if (!is_wp_error($user_id)) {

        if (!empty($session['id'])) {
            sa_migrate_session_draft_to_user($user_id, $session['id']);
        }

        sa_save_order([
            'wp_user_id'             => $user_id,
            'stripe_session_id'      => sanitize_text_field($session['id'] ?? ''),
            'stripe_customer_id'     => sanitize_text_field($session['customer'] ?? ''),
            'stripe_subscription_id' => sanitize_text_field($session['subscription'] ?? ''),
            'email'                  => $email,
            'first_name'             => $first_name,
            'last_name'              => $last_name,
            'amount_total'           => intval($session['amount_total'] ?? 0),
            'currency'               => sanitize_text_field($session['currency'] ?? ''),
            'payment_status'         => sanitize_text_field($session['payment_status'] ?? ''),
        ]);
    }

    return $content;
}, 20);

add_shortcode('siteauteur_reset_password_form', function () {
    $key   = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

    if (!$key || !$login) {
        return '
        <div class="sa-reset-wrap">
            <h1>Lien invalide</h1>
            <p>Le lien de création de mot de passe est incomplet.</p>
        </div>';
    }

    $user = check_password_reset_key($key, $login);

    if (is_wp_error($user)) {
        return '
        <div class="sa-reset-wrap">
            <h1>Lien expiré ou invalide</h1>
            <p>Ce lien n’est plus valide. Vous pouvez demander un nouveau mot de passe.</p>
            <p class="bloc-reset-secondary"><a class="sa-reset-secondary" href="' . esc_url(wp_lostpassword_url()) . '">Demander un nouveau lien</a></p>
        </div>';
    }

    $error = '';
    $success = false;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sa_reset_password_nonce'])) {
        if (!wp_verify_nonce($_POST['sa_reset_password_nonce'], 'sa_reset_password_action')) {
            $error = 'Session invalide. Rechargez la page.';
        } else {
            $pass1 = $_POST['pass1'] ?? '';
            $pass2 = $_POST['pass2'] ?? '';

            if (!$pass1 || !$pass2) {
                $error = 'Veuillez remplir les deux champs.';
            } elseif ($pass1 !== $pass2) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (strlen($pass1) < 8) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            } else {
                reset_password($user, $pass1);
                update_user_meta($user->ID, 'sa_password_created', 1);

                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID, true);

                $redirect_url = home_url('/confirmation-commande/?password_created=1');

                if (!empty($_GET['session_id'])) {
                    $redirect_url = home_url('/confirmation-commande/?session_id=' . rawurlencode($_GET['session_id']) . '&password_created=1');
                }

                wp_safe_redirect($redirect_url);
                exit;
            }
        }
    }

    ob_start();
    ?>
    <div class="sa-reset-wrap">
        <h1>Définir mon mot de passe</h1>
        <p class="sa-reset-subtitle">Choisissez un mot de passe pour vos prochaines connexions à votre espace SiteAuteur.</p>

        <?php if ($error): ?>
            <div class="sa-reset-error"><?php echo esc_html($error); ?></div>
        <?php endif; ?>

        <form method="post" class="sa-reset-form">
            <?php wp_nonce_field('sa_reset_password_action', 'sa_reset_password_nonce'); ?>

            <div class="sa-reset-field sa-password-field">
                <label for="pass1">Nouveau mot de passe</label>
                <div class="sa-password-wrap">
                    <input type="password" id="pass1" name="pass1" required>
                    <button type="button" class="sa-toggle-password" data-target="pass1">
                        Afficher
                    </button>
                </div>
            </div>

            <div class="sa-reset-field sa-password-field">
                <label for="pass2">Confirmer le mot de passe</label>
                <div class="sa-password-wrap">
                    <input type="password" id="pass2" name="pass2" required>
                    <button type="button" class="sa-toggle-password" data-target="pass2">
                        Afficher
                    </button>
                </div>
            </div>

            <button type="submit" class="sa-reset-primary">Enregistrer mon mot de passe</button>
        </form>
    </div>
    <?php
    return ob_get_clean();
});

add_filter('body_class', function ($classes) {
    if (!is_page('confirmation-commande')) {
        return $classes;
    }

    $session = sa_get_valid_paid_session();

    if (!$session) {
        $classes[] = 'sa-password-not-created';
        return $classes;
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');

    if (!$email) {
        $classes[] = 'sa-password-not-created';
        return $classes;
    }

    $user = get_user_by('email', $email);

    if (!$user) {
        $classes[] = 'sa-password-not-created';
        return $classes;
    }

    $password_created = get_user_meta($user->ID, 'sa_password_created', true);

    if ((string) $password_created === '1') {
        $classes[] = 'sa-password-created';
    } else {
        $classes[] = 'sa-password-not-created';
    }

    return $classes;
});

/**
 * =========================
 * RÉINJECTION DES UPLOADS DANS FLUENT FORMS
 * =========================
 */

add_filter('fluentform/insert_response_data', 'sa_inject_uploaded_files_into_fluentform_entry', 10, 3);

function sa_inject_uploaded_files_into_fluentform_entry($form_data, $form, $insert_data) {
    if (empty($form) || empty($form->id) || (int) $form->id !== 6) {
        return $form_data;
    }

    // mapping : hidden custom => vrai champ Fluent Forms
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

        // On force un tableau JSON d’URLs, format généralement accepté par FF
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
    if (empty($form) || empty($form->id) || (int) $form->id !== 6) {
        return;
    }

    $session_id = !empty($_REQUEST['session_id']) ? sanitize_text_field($_REQUEST['session_id']) : '';
    $owner_key  = sa_get_onboarding_owner_key();

    $draft = [
        'fields'  => [],
        'uploads' => []
    ];

    // 1) owner_key courant
    if ($owner_key) {
        $draft = sa_get_onboarding_draft($owner_key);
    }

    // 2) fallback session
    if (empty($draft['uploads']) && $session_id) {
        $draft = sa_get_onboarding_draft('session_' . $session_id);
    }

    // 3) fallback user connecté
    if (empty($draft['uploads']) && is_user_logged_in()) {
        $draft = sa_get_onboarding_draft('user_' . get_current_user_id());
    }

    if (empty($draft['uploads']) || !is_array($draft['uploads'])) {
        return;
    }

    $upload_map = [
        'file-upload_2' => 'file-upload_2',
        'file-upload_3' => 'file-upload_3',
        'image-upload_2' => 'image-upload_2',
        'file-upload_4' => 'file-upload_4',
        'file-upload' => 'file-upload',
        'file_upload_6' => 'file_upload_6',
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

        // Fluent Forms stocke généralement les fichiers sous forme de tableau d'URLs
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

    if ((int) $form->id !== 6) {
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