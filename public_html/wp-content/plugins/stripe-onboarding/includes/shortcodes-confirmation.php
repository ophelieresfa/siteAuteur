<?php
if (!defined('ABSPATH')) exit;

/**
 * Empêche l'accès à la page "Définir mon mot de passe"
 * si le mot de passe du compte a déjà été créé.
 */
add_action('template_redirect', function () {
    if (is_admin()) {
        return;
    }

    if (!is_page('definir-mon-mot-de-passe')) {
        return;
    }

    /*
     * Si l'utilisateur est déjà connecté, il n'a rien à faire ici.
     */
    if (is_user_logged_in()) {
        wp_safe_redirect(home_url('/account/'));
        exit;
    }

    /*
     * Si l'URL contient un login, on vérifie si ce compte
     * a déjà défini son mot de passe.
     */
    $login = !empty($_GET['login'])
        ? sanitize_text_field(wp_unslash($_GET['login']))
        : '';

    if (!$login) {
        return;
    }

    $user = get_user_by('login', $login);

    if (!$user || empty($user->ID)) {
        return;
    }

    $password_created = get_user_meta($user->ID, 'sa_password_created', true);

    $is_forgot_password_mode = (
        !empty($_GET['mode']) &&
        sanitize_key(wp_unslash($_GET['mode'])) === 'forgot_password'
    );

    if ((string) $password_created === '1' && !$is_forgot_password_mode) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }
}, 1);

/**
 * Envoie un nouveau lien pour définir le mot de passe.
 */
function sa_send_new_set_password_link_email($user, $session_id = '') {
    if (!$user || empty($user->ID) || empty($user->user_email)) {
        return false;
    }

    if ((string) get_user_meta($user->ID, 'sa_password_created', true) === '1') {
        return false;
    }

    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        return false;
    }

    $reset_url = home_url(
        '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login) .
        (!empty($session_id) ? '&session_id=' . rawurlencode($session_id) : '')
    );

    $reset_url_text = esc_url($reset_url);

    $reset_url_display = str_replace(
        ['/', '?', '&', '=', '-', '_', '.'],
        ['/<wbr>', '?<wbr>', '&<wbr>', '=<wbr>', '-<wbr>', '_<wbr>', '.<wbr>'],
        $reset_url_text
    );

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $subject = 'Votre commande SiteAuteur est confirmée';

    $message = '
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f6f7f9;margin:0;padding:0;border-collapse:collapse;">
<tr>
<td align="center" style="padding:20px 10px;margin:0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:520px;background:#ffffff;border-radius:10px;font-family:Arial,Helvetica,sans-serif;border-collapse:collapse;table-layout:fixed;">
<tr>
<td style="padding:28px 20px;">

<h1 style="margin:0 0 18px 0;font-size:24px;line-height:1.25;color:#1f2a44;text-align:center;font-weight:700;">
Bienvenue sur SiteAuteur
</h1>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Bonjour ' . esc_html($display_name) . ',
</p>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Votre commande a bien été confirmée.
</p>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Votre compte SiteAuteur a été créé avec succès.
</p>

<p style="margin:0 0 24px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Pour accéder à votre espace client, vous devez maintenant définir votre mot de passe.
</p>

<p style="text-align:center;margin:26px 0;">
<a href="' . esc_url($reset_url) . '" style="background:#56c7ab;color:#ffffff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;font-size:15px;line-height:1.3;">
Créer mon mot de passe
</a>
</p>

<p style="margin:0 0 10px 0;font-size:13px;line-height:1.5;color:#343434;text-align:center;">
Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :
</p>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;table-layout:fixed;">
<tr>
<td style="width:100%;max-width:100%;font-size:12px;line-height:1.5;color:#343434;text-align:center;word-break:break-all;overflow-wrap:anywhere;white-space:normal;">
<p style="color:#343434;text-decoration:none;word-break:break-all;overflow-wrap:anywhere;white-space:normal;">
' . $reset_url_display . '
</p>
</td>
</tr>
</table>

<hr style="border:none;border-top:1px solid #eee;margin:28px 0;">

<p style="margin:0 0 10px 0;font-size:14px;line-height:1.5;color:#343434;">
Depuis votre espace client vous pourrez :
</p>

<ul style="margin:0 0 0 18px;padding:0;color:#343434;line-height:1.8;font-size:14px;">
<li>Créer votre site d\'auteur</li>
<li>Suivre votre projet</li>
<li>Demander des modifications</li>
<li>Gérer votre abonnement</li>
<li>Télécharger vos factures</li>
</ul>

<hr style="border:none;border-top:1px solid #eee;margin:28px 0;">

<p style="margin:0;font-size:12px;line-height:1.5;color:#777;">
Cet email est automatique — SiteAuteur<br>
contact@siteauteur.fr
</p>

</td>
</tr>
</table>

</td>
</tr>
</table>
    ';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: SiteAuteur <noreply@siteauteur.fr>',
    ];

    return wp_mail($user->user_email, $subject, $message, $headers);
}

/**
 * =========================
 * SHORTCODES CONFIRMATION / RESET
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
    sa_process_confirmation_order_before_display();

    $order = sa_get_confirmation_order();

    if (!$order || empty($order->wp_user_id)) {
        return '';
    }

    $user = get_user_by('id', $order->wp_user_id);

    if (!$user) {
        return '';
    }

    if (function_exists('sa_get_confirmation_password_reset_url')) {
        return esc_url(sa_get_confirmation_password_reset_url($user));
    }

    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        return '';
    }

    $reset_url = home_url(
        '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login) .
        '&session_id=' . rawurlencode($_GET['session_id'] ?? '')
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

    return sa_render_template('partials/confirmation-onboarding-button', [
        'url' => $url
    ]);
});

add_shortcode('siteauteur_confirmation_reset_button', function () {
    sa_process_confirmation_order_before_display();

    $session = sa_get_valid_paid_session();

    if (!$session) {
        return '';
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');

    if (!$email) {
        return '';
    }

    $user = get_user_by('email', $email);

    if (!$user) {
        return '';
    }

    if (function_exists('sa_get_confirmation_password_reset_url')) {
        $reset_url = sa_get_confirmation_password_reset_url($user);
    } else {
        $reset_key = get_password_reset_key($user);

        if (is_wp_error($reset_key)) {
            return '';
        }

        $session_id = sanitize_text_field($_GET['session_id'] ?? '');

        $reset_url = home_url(
            '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
            '&login=' . rawurlencode($user->user_login) .
            '&session_id=' . rawurlencode($session_id)
        );
    }

    if (!$reset_url) {
        return '';
    }

    return sa_render_template('partials/confirmation-reset-button', [
        'reset_url' => $reset_url
    ]);
});

add_shortcode('siteauteur_reset_password_form', function () {
    $key        = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $login      = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';
    $session_id = isset($_GET['session_id']) ? sanitize_text_field($_GET['session_id']) : '';

    $resend_sent  = false;
    $resend_error = '';

    /*
    |--------------------------------------------------------------------------
    | Demande de nouveau lien depuis la page "lien expiré"
    |--------------------------------------------------------------------------
    */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['sa_resend_reset_link_nonce'])
    ) {
        if (!wp_verify_nonce($_POST['sa_resend_reset_link_nonce'], 'sa_resend_reset_link_action')) {
            $resend_error = 'Session invalide. Rechargez la page puis réessayez.';
        } else {
            $posted_login      = isset($_POST['sa_resend_login']) ? sanitize_text_field(wp_unslash($_POST['sa_resend_login'])) : '';
            $posted_session_id = isset($_POST['sa_resend_session_id']) ? sanitize_text_field(wp_unslash($_POST['sa_resend_session_id'])) : '';

            if (!$posted_login) {
                $resend_error = 'Impossible d’identifier le compte associé à ce lien.';
            } else {
                $resend_user = get_user_by('login', $posted_login);

                if (!$resend_user) {
                    $resend_error = 'Impossible d’identifier le compte associé à ce lien.';
                } elseif ((string) get_user_meta($resend_user->ID, 'sa_password_created', true) === '1') {
                    wp_safe_redirect(home_url('/login/'));
                    exit;
                } else {
                    $sent = sa_send_new_set_password_link_email($resend_user, $posted_session_id);
                    if ($sent) {
                        $resend_sent = true;
                    } else {
                        $resend_error = 'L’email n’a pas pu être envoyé. Vous pouvez nous contacter via le support.';
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | URL incomplète : pas de key ou pas de login
    |--------------------------------------------------------------------------
    */
    if (!$key || !$login) {
        return sa_render_template('reset-password-invalid', [
            'can_resend'   => false,
            'resend_sent'  => $resend_sent,
            'resend_error' => $resend_error,
            'account_url'  => home_url('/account/'),
            'contact_url'  => home_url('/contact/'),
        ]);
    }

    $user = check_password_reset_key($key, $login);

    /*
    |--------------------------------------------------------------------------
    | Lien expiré ou déjà utilisé
    |--------------------------------------------------------------------------
    */
    if (is_wp_error($user)) {
        return sa_render_template('reset-password-expired', [
            'can_resend'   => true,
            'login'        => $login,
            'session_id'   => $session_id,
            'resend_sent'  => $resend_sent,
            'resend_error' => $resend_error,
            'account_url'  => home_url('/account/'),
            'contact_url'  => home_url('/contact/'),
        ]);
    }

    $error = '';

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
            } elseif (!preg_match('/[A-Z]/', $pass1)) {
                $error = 'Le mot de passe doit contenir au moins une lettre majuscule.';
            } elseif (!preg_match('/[0-9]/', $pass1)) {
                $error = 'Le mot de passe doit contenir au moins un chiffre.';
            } elseif (!preg_match('/[^A-Za-z0-9]/', $pass1)) {
                $error = 'Le mot de passe doit contenir au moins un caractère spécial.';
            } else {
                reset_password($user, $pass1);
                update_user_meta($user->ID, 'sa_password_created', 1);

                $is_forgot_password_mode = (
                    !empty($_GET['mode']) &&
                    sanitize_key(wp_unslash($_GET['mode'])) === 'forgot_password'
                );

                if ($is_forgot_password_mode && function_exists('sa_send_password_changed_email')) {
                    sa_send_password_changed_email($user);
                }

                if (!$is_forgot_password_mode && function_exists('sa_send_password_created_after_order_email')) {
                    sa_send_password_created_after_order_email($user);
                }

                if ($is_forgot_password_mode) {
                    wp_safe_redirect(home_url('/login/?updated=password_changed'));
                    exit;
                }

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

    return sa_render_template('reset-password-form', [
        'error' => $error
    ]);
});