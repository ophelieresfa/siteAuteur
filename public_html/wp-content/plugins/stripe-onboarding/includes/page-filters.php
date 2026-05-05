<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * HELPERS
 * =========================
 */

function sa_page_requires_paid_order() {
    return (
        is_page('mon-abonnement') ||
        is_page('mes-factures') ||
        is_page(SA_PAGE_USE_MODIFICATION) ||
        is_page('acheter-une-modification') ||
        is_page('merci-modification')
    );
}

function sa_redirect_no_order_page() {
    return home_url('/account/');
}

function sa_redirect_login_page() {
    return home_url('/login/');
}

/**
 * =========================
 * REDIRECTION LOGIN / REGISTER / PASSWORD RESET SI CONNECTÉ
 * =========================
 */
add_action('template_redirect', function () {

    if (is_admin()) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    /*
     * Si l'utilisateur est connecté, il ne doit plus accéder
     * aux pages connexion / inscription.
     */
    if (is_page('login') || is_page('register')) {
        wp_safe_redirect(home_url('/account/'));
        exit;
    }

    /*
     * Si l'utilisateur est connecté et va sur "mot de passe oublié",
     * on l'envoie vers la page de modification du mot de passe.
     */
    if (is_page('password-reset')) {
        wp_safe_redirect(home_url('/modifier-mon-mot-de-passe/'));
        exit;
    }

}, 1);

/**
 * =========================
 * PREPARATION PAGE CONFIRMATION
 * =========================
 */

function sa_get_confirmation_customer_names_from_session($session) {
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

    return [
        'first_name' => $first_name,
        'last_name'  => $last_name,
    ];
}

function sa_get_confirmation_password_reset_url($user) {
    if (!$user || empty($user->ID)) {
        return '';
    }

    $session_id = !empty($_GET['session_id'])
        ? sanitize_text_field($_GET['session_id'])
        : '';

    $cache_key = 'sa_confirmation_reset_url_' . (int) $user->ID . '_' . md5($session_id);

    if (!empty($GLOBALS[$cache_key])) {
        return $GLOBALS[$cache_key];
    }

    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        return '';
    }

    $reset_url = home_url(
        '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login) .
        '&session_id=' . rawurlencode($session_id)
    );

    $GLOBALS[$cache_key] = $reset_url;

    return $reset_url;
}

function sa_send_welcome_email_after_order($user_id, $order_id) {
    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return;
    }

    $sent_meta_key = 'sa_welcome_order_email_sent_' . (int) $order_id;

    if (get_user_meta($user_id, $sent_meta_key, true)) {
        return;
    }

    $reset_url = sa_get_confirmation_password_reset_url($user);

    if (!$reset_url) {
        return;
    }

    /**
     * Version affichée du lien.
     * On insère des <wbr> pour permettre à Gmail de couper le lien
     * sans élargir toute la largeur du mail.
     */
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

    wp_mail($user->user_email, $subject, $message, $headers);

    update_user_meta($user_id, $sent_meta_key, 1);
}

function sa_send_order_confirmation_email_existing_user($user_id, $order_id) {
    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return false;
    }

    $sent_meta_key = 'sa_order_confirmation_existing_email_sent_' . (int) $order_id;

    if (get_user_meta($user_id, $sent_meta_key, true)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $account_url = home_url('/account/');

    $subject = 'Votre commande SiteAuteur est confirmée';

    $message = '
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f6f7f9;margin:0;padding:0;border-collapse:collapse;">
<tr>
<td align="center" style="padding:20px 10px;margin:0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:520px;background:#ffffff;border-radius:10px;font-family:Arial,Helvetica,sans-serif;border-collapse:collapse;table-layout:fixed;">
<tr>
<td style="padding:28px 20px;">

<h1 style="margin:0 0 18px 0;font-size:24px;line-height:1.25;color:#1f2a44;text-align:center;font-weight:700;">
Votre commande est confirmée
</h1>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Bonjour ' . esc_html($display_name) . ',
</p>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Votre commande SiteAuteur a bien été confirmée.
</p>

<p style="margin:0 0 24px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Vous pouvez maintenant accéder à votre espace client pour suivre votre projet et remplir le formulaire de création de site.
</p>

<p style="text-align:center;margin:26px 0;">
<a href="' . esc_url($account_url) . '" style="background:#56c7ab;color:#ffffff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;font-size:15px;line-height:1.3;">
Accéder à mon espace client
</a>
</p>

<hr style="border:none;border-top:1px solid #eee;margin:28px 0;">

<p style="margin:0 0 10px 0;font-size:14px;line-height:1.5;color:#343434;">
Depuis votre espace client vous pourrez :
</p>

<ul style="margin:0 0 0 18px;padding:0;color:#343434;line-height:1.8;font-size:14px;">
<li>Remplir le formulaire de création</li>
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

    $sent = wp_mail($user->user_email, $subject, $message, $headers);

    if ($sent) {
        update_user_meta($user_id, $sent_meta_key, 1);
    }

    return $sent;
}

function sa_process_confirmation_order_before_display() {
    static $processed = false;

    if ($processed) {
        return;
    }

    $processed = true;

    if (!is_page(SA_PAGE_CONFIRMATION)) {
        return;
    }

    $session = sa_get_valid_paid_session();

    if (!$session) {
        return;
    }

    $purchase_type = !empty($session['metadata']['sa_purchase_type'])
        ? sanitize_key($session['metadata']['sa_purchase_type'])
        : '';

    if ($purchase_type === 'extra_modification') {
        return;
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');

    if (!$email) {
        return;
    }

    $names = sa_get_confirmation_customer_names_from_session($session);

    $existing_user_before_order = get_user_by('email', $email);
    $is_existing_user_before_order = $existing_user_before_order ? true : false;

    $existing_order_before_save = !empty($session['id'])
        ? sa_get_order_by_session_id(sanitize_text_field($session['id']))
        : false;

    $user_id = sa_find_or_create_user(
        $email,
        $names['first_name'],
        $names['last_name']
    );

    if (is_wp_error($user_id) || !$user_id) {
        return;
    }

    if (is_user_logged_in() && get_current_user_id() === (int) $user_id) {
        update_user_meta($user_id, 'sa_password_created', 1);
    }

    if (!empty($session['id'])) {
        sa_migrate_session_draft_to_user($user_id, $session['id']);
    }

    $order_id = sa_save_order([
        'wp_user_id'             => $user_id,
        'stripe_session_id'      => sanitize_text_field($session['id'] ?? ''),
        'stripe_customer_id'     => sanitize_text_field($session['customer'] ?? ''),
        'stripe_subscription_id' => sanitize_text_field($session['subscription'] ?? ''),
        'email'                  => $email,
        'first_name'             => $names['first_name'],
        'last_name'              => $names['last_name'],
        'amount_total'           => intval($session['amount_total'] ?? 0),
        'currency'               => sanitize_text_field($session['currency'] ?? ''),
        'payment_status'         => sanitize_text_field($session['payment_status'] ?? ''),
    ]);

    if ($order_id) {
        /*
         * On mémorise si la commande a été créée pour un compte déjà existant
         * AVANT la commande Stripe.
         *
         * Important :
         * Lorsqu'un client crée son mot de passe après achat, il repasse sur la page
         * confirmation-commande. Sans cette meta, il serait considéré à tort comme
         * un compte existant.
         */
        $created_for_existing_user_meta = sa_get_order_meta(
            (int) $user_id,
            (int) $order_id,
            'created_for_existing_user',
            ''
        );

        if ($created_for_existing_user_meta === '') {
            sa_update_order_meta(
                (int) $user_id,
                (int) $order_id,
                'created_for_existing_user',
                $is_existing_user_before_order ? '1' : '0'
            );

            $created_for_existing_user_meta = $is_existing_user_before_order ? '1' : '0';
        }

        $password_created = get_user_meta((int) $user_id, 'sa_password_created', true);

        /*
         * Cas 1 : compte créé automatiquement après commande
         * => uniquement le mail avec lien de création du mot de passe.
         */
        if ($created_for_existing_user_meta !== '1' && (string) $password_created !== '1') {
            sa_send_welcome_email_after_order((int) $user_id, (int) $order_id);
        }

        /*
         * Cas 2 : le compte existait déjà AVANT la commande
         * => mail confirmation commande avec accès tableau de bord.
         */
        if ($created_for_existing_user_meta === '1') {
            sa_send_order_confirmation_email_existing_user((int) $user_id, (int) $order_id);
        }
    }
}

add_action('template_redirect', function () {
    if (!is_page(SA_PAGE_CONFIRMATION)) {
        return;
    }

    $session = sa_get_valid_paid_session();

    if (!$session) {
        return;
    }

    $purchase_type = !empty($session['metadata']['sa_purchase_type'])
        ? sanitize_key($session['metadata']['sa_purchase_type'])
        : '';

    if ($purchase_type === 'extra_modification') {
        wp_safe_redirect(home_url('/merci-modification/?session_id=' . rawurlencode($session['id'] ?? '')));
        exit;
    }

    sa_process_confirmation_order_before_display();
}, 5);

/**
 * =========================
 * PROTECTION PAGES AVEC COMMANDE
 * =========================
 */
add_action('template_redirect', function () {

    /*
    =========================
    PAGE ONBOARDING
    =========================
    */
    if (is_page(SA_PAGE_ONBOARDING)) {

        if (!is_user_logged_in()) {
            wp_safe_redirect(sa_redirect_login_page());
            exit;
        }

        $user_id = get_current_user_id();
        $order   = sa_get_current_order_from_session_or_user();

        if (!$order || empty($order->id) || empty($order->wp_user_id)) {
            wp_safe_redirect(sa_redirect_no_order_page());
            exit;
        }

        if ((int) $order->wp_user_id !== (int) $user_id) {
            wp_safe_redirect(sa_redirect_no_order_page());
            exit;
        }

        $state = sa_get_order_state($user_id, (int) $order->id);

        $allowed_states = ['paid', 'onboarding_started'];

        if (!in_array($state, $allowed_states, true)) {
            wp_safe_redirect(sa_redirect_no_order_page());
            exit;
        }

        return;
    }

    /*
    =========================
    AUTRES PAGES LIEES A UNE COMMANDE
    =========================
    */
    if (!sa_page_requires_paid_order()) {
        return;
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(sa_redirect_login_page());
        exit;
    }

    $user_id = get_current_user_id();

    if (!function_exists('sa_get_user_paid_orders')) {
        wp_safe_redirect(sa_redirect_no_order_page());
        exit;
    }

    $orders = sa_get_user_paid_orders($user_id);

    if (empty($orders) || !is_array($orders)) {
        wp_safe_redirect(sa_redirect_no_order_page());
        exit;
    }

    /*
    Si une commande courante est nécessaire pour certaines pages,
    on essaie de la récupérer. Si elle n'existe pas, on redirige.
    */
    if (
        is_page('mon-abonnement') ||
        is_page('mes-factures') ||
        is_page(SA_PAGE_USE_MODIFICATION) ||
        is_page('acheter-une-modification') ||
        is_page('merci-modification')
    ) {
        $current_order = function_exists('sa_get_current_order_from_session_or_user')
            ? sa_get_current_order_from_session_or_user()
            : null;

        if (!$current_order) {
            $current_order = reset($orders);
        }

        if (!$current_order || empty($current_order->id)) {
            wp_safe_redirect(sa_redirect_no_order_page());
            exit;
        }

        if (!empty($current_order->wp_user_id) && (int) $current_order->wp_user_id !== (int) $user_id) {
            wp_safe_redirect(sa_redirect_no_order_page());
            exit;
        }
    }
}, 20);

/**
 * =========================
 * PAGE CONFIRMATION COMMANDE
 * =========================
 */
add_filter('the_content', function ($content) {
    if (is_admin()) {
        return $content;
    }

    if (!is_page(SA_PAGE_CONFIRMATION)) {
        return $content;
    }

    if (!is_main_query() || !in_the_loop()) {
        return $content;
    }

    $session = sa_get_valid_paid_session();

    if (!$session) {
        return sa_render_template('confirmation-not-found', [
            'home_url' => home_url('/')
        ]);
    }

    $purchase_type = !empty($session['metadata']['sa_purchase_type'])
        ? sanitize_key($session['metadata']['sa_purchase_type'])
        : '';

    if ($purchase_type === 'extra_modification') {
        wp_safe_redirect(home_url('/merci-modification/?session_id=' . rawurlencode($session['id'] ?? '')));
        exit;
    }

    $email = sanitize_email($session['customer_details']['email'] ?? '');

    if (!$email) {
        return sa_render_template('confirmation-not-found', [
            'home_url' => home_url('/')
        ]);
    }

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

    if (is_wp_error($user_id) || !$user_id) {
        return sa_render_template('confirmation-not-found', [
            'home_url' => home_url('/')
        ]);
    }

    if (!empty($session['id'])) {
        sa_migrate_session_draft_to_user($user_id, $session['id']);
    }

    $order_id = sa_save_order([
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

    /*
    |--------------------------------------------------------------------------
    | Si l'utilisateur revient après avoir créé son mot de passe
    |--------------------------------------------------------------------------
    */
    if (!empty($_GET['password_created']) && (string) $_GET['password_created'] === '1') {
        update_user_meta($user_id, 'sa_password_created', 1);

        return sa_render_template('confirmation-order-onboarding-ready', [
            'email'      => $email,
            'first_name' => $first_name,
            'last_name'  => $last_name,
            'session_id' => sanitize_text_field($session['id'] ?? ''),
        ]);
    }

/*
|--------------------------------------------------------------------------
| État du compte / mot de passe
|--------------------------------------------------------------------------
*/
$user = get_user_by('id', $user_id);
$password_created = get_user_meta($user_id, 'sa_password_created', true);

/*
|--------------------------------------------------------------------------
| Savoir si cette commande a été faite par un compte déjà existant
|--------------------------------------------------------------------------
*/
$created_for_existing_user = '';

if ($order_id) {
    $created_for_existing_user = sa_get_order_meta(
        (int) $user_id,
        (int) $order_id,
        'created_for_existing_user',
        ''
    );
}

/*
|--------------------------------------------------------------------------
| Cas compte déjà existant AVANT la commande
|--------------------------------------------------------------------------
| Le client ne doit JAMAIS voir la page "définir mon mot de passe".
| Même s'il n'est pas connecté, il doit voir la page de commande confirmée
| avec accès à son espace client / formulaire.
|--------------------------------------------------------------------------
*/
if ($created_for_existing_user === '1') {
    update_user_meta((int) $user_id, 'sa_password_created', 1);

    return sa_render_template('confirmation-order-onboarding-ready', [
        'email'      => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'session_id' => sanitize_text_field($session['id'] ?? ''),
    ]);
}

/*
|--------------------------------------------------------------------------
| Si le mot de passe existe déjà ET que le bon utilisateur est connecté
|--------------------------------------------------------------------------
*/
if (
    (string) $password_created === '1'
    && is_user_logged_in()
    && (int) get_current_user_id() === (int) $user_id
) {
    return sa_render_template('confirmation-order-onboarding-ready', [
        'email'      => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'session_id' => sanitize_text_field($session['id'] ?? ''),
    ]);
}

/*
|--------------------------------------------------------------------------
| Renvoyer l'email de création du mot de passe
|--------------------------------------------------------------------------
*/
$resend_sent = false;

if (
    !empty($_GET['sa_resend_welcome'])
    && (string) $_GET['sa_resend_welcome'] === '1'
    && $user
    && (string) $password_created !== '1'
) {
    $resend_sent = sa_send_new_set_password_link_email(
        $user,
        sanitize_text_field($session['id'] ?? '')
    );
}

/*
|--------------------------------------------------------------------------
| Mot de passe pas encore créé
|--------------------------------------------------------------------------
| C'est l'état normal juste après une commande sans compte client.
| On affiche la page qui demande d'aller créer le mot de passe via l'email.
|--------------------------------------------------------------------------
*/
if ((string) $password_created !== '1') {
    if ($order_id) {
        sa_send_welcome_email_after_order((int) $user_id, (int) $order_id);
    }

    $display_name = trim($first_name . ' ' . $last_name);

    if (!$display_name && $user) {
        $display_name = $user->display_name;
    }

    return sa_render_template('confirmation-order-password-pending', [
        'email'        => $email,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $display_name,
        'session_id'   => sanitize_text_field($session['id'] ?? ''),
        'resend_sent'  => $resend_sent,
    ]);
}

/*
|--------------------------------------------------------------------------
| Mot de passe déjà créé mais utilisateur non connecté
|--------------------------------------------------------------------------
| On ne doit pas afficher "définir mon mot de passe".
| Le client a déjà un compte : il doit simplement se connecter.
|--------------------------------------------------------------------------
*/
if ((string) $password_created === '1') {
    return sa_render_template('confirmation-order-onboarding-ready', [
        'email'      => $email,
        'first_name' => $first_name,
        'last_name'  => $last_name,
        'session_id' => sanitize_text_field($session['id'] ?? ''),
    ]);
}

/*
|--------------------------------------------------------------------------
| Sécurité : si vraiment aucun mot de passe n'existe, on garde la page email.
|--------------------------------------------------------------------------
*/
return sa_render_template('confirmation-order-password-pending', [
    'email'        => $email,
    'first_name'   => $first_name,
    'last_name'    => $last_name,
    'display_name' => trim($first_name . ' ' . $last_name),
    'session_id'   => sanitize_text_field($session['id'] ?? ''),
    'resend_sent'  => $resend_sent,
]);

}, 20);

/**
 * =========================
 * BODY CLASS PAGE CONFIRMATION
 * =========================
 */
add_filter('body_class', function ($classes) {
    if (!is_page(SA_PAGE_CONFIRMATION)) {
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
 * PAGE CONTACT
 * Sidebar à gauche seulement pour utilisateur connecté
 * =========================
 */
add_filter('the_content', function ($content) {
    if (is_admin()) {
        return $content;
    }

    if (!is_page('contact')) {
        return $content;
    }

    if (!is_main_query() || !in_the_loop()) {
        return $content;
    }

    if (!is_user_logged_in()) {
        return $content;
    }

    return sa_render_account_contact($content);
}, 20);