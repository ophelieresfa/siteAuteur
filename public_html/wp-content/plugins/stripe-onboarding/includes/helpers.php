<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * HELPERS
 * =========================
 */

function sa_render_template($template_name, $args = []) {
    $template_path = plugin_dir_path(dirname(__FILE__)) . 'templates/' . $template_name . '.php';

    if (!file_exists($template_path)) {
        return '';
    }

    if (!empty($args) && is_array($args)) {
        extract($args, EXTR_SKIP);
    }

    ob_start();
    include $template_path;
    return ob_get_clean();
}

/**
 * =========================
 * EMAIL - MOT DE PASSE MODIFIÉ
 * =========================
 */

function sa_send_password_changed_email($user) {
    if (!$user || empty($user->ID) || empty($user->user_email)) {
        return false;
    }

    $template_path = get_stylesheet_directory() . '/ultimate-member/email/changedpw_email.php';

    if (file_exists($template_path)) {
        ob_start();
        include $template_path;
        $message = ob_get_clean();
    } else {
        $message = '
            <p>Bonjour {display_name},</p>
            <p>Votre mot de passe a été modifié avec succès.</p>
            <p>Si vous êtes à l’origine de cette modification, aucune action n’est requise.</p>
        ';
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $message = str_replace('{display_name}', esc_html($display_name), $message);

    $subject = 'Votre mot de passe a été modifié - SiteAuteur';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: SiteAuteur <noreply@siteauteur.fr>',
    ];

    return wp_mail($user->user_email, $subject, $message, $headers);
}

/**
 * =========================
 * EMAIL - MOT DE PASSE CRÉÉ APRÈS COMMANDE
 * =========================
 */

function sa_send_password_created_after_order_email($user) {
    if (!$user || empty($user->ID) || empty($user->user_email)) {
        return false;
    }

    $sent_meta_key = 'sa_password_created_after_order_email_sent';

    if (get_user_meta($user->ID, $sent_meta_key, true)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $account_url = home_url('/account/');

    $subject = 'Votre mot de passe SiteAuteur a bien été créé';

    $message = '
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f6f7f9;margin:0;padding:0;border-collapse:collapse;">
<tr>
<td align="center" style="padding:20px 10px;margin:0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:520px;background:#ffffff;border-radius:10px;font-family:Arial,Helvetica,sans-serif;border-collapse:collapse;table-layout:fixed;">
<tr>
<td style="padding:28px 20px;">

<h1 style="margin:0 0 18px 0;font-size:24px;line-height:1.25;color:#1f2a44;text-align:center;font-weight:700;">
Votre mot de passe a bien été créé
</h1>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Bonjour ' . esc_html($display_name) . ',
</p>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Votre mot de passe SiteAuteur a bien été créé.
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
<li>Remplir le formulaire de création de site</li>
<li>Suivre l’avancement de votre projet</li>
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
        update_user_meta($user->ID, $sent_meta_key, 1);
    }

    return $sent;
}

/**
 * =========================
 * EMAILS - CHANGEMENT STATUT PROJET
 * =========================
 */

function sa_get_project_status_email_headers() {
    return [
        'Content-Type: text/html; charset=UTF-8',
        'From: SiteAuteur <noreply@siteauteur.fr>',
    ];
}

function sa_render_project_status_email($args = []) {
    $defaults = [
        'display_name' => '',
        'title'        => '',
        'intro'        => '',
        'text'         => '',
        'button_label' => '',
        'button_url'   => '',
        'footer_text'  => 'Cet email est automatique — SiteAuteur<br>contact@siteauteur.fr',
    ];

    $args = wp_parse_args($args, $defaults);

    $button_html = '';

    if (!empty($args['button_label']) && !empty($args['button_url'])) {
        $button_html = '
            <p style="text-align:center;margin:26px 0;">
                <a href="' . esc_url($args['button_url']) . '" style="background:#56c7ab;color:#ffffff;padding:12px 20px;border-radius:6px;text-decoration:none;font-weight:bold;display:inline-block;font-size:15px;line-height:1.3;">
                    ' . esc_html($args['button_label']) . '
                </a>
            </p>
        ';
    }

    return '
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f6f7f9;margin:0;padding:0;border-collapse:collapse;">
<tr>
<td align="center" style="padding:20px 10px;margin:0;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:520px;background:#ffffff;border-radius:10px;font-family:Arial,Helvetica,sans-serif;border-collapse:collapse;table-layout:fixed;">
<tr>
<td style="padding:28px 20px;">

<h1 style="margin:0 0 18px 0;font-size:24px;line-height:1.25;color:#1f2a44;text-align:center;font-weight:700;">
' . esc_html($args['title']) . '
</h1>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
Bonjour ' . esc_html($args['display_name']) . ',
</p>

<p style="margin:0 0 14px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
' . esc_html($args['intro']) . '
</p>

<p style="margin:0 0 24px 0;font-size:16px;line-height:1.55;color:#343434;text-align:center;">
' . esc_html($args['text']) . '
</p>

' . $button_html . '

<hr style="border:none;border-top:1px solid #eee;margin:28px 0;">

<p style="margin:0;font-size:12px;line-height:1.5;color:#777;">
' . wp_kses_post($args['footer_text']) . '
</p>

</td>
</tr>
</table>

</td>
</tr>
</table>';
}

function sa_send_project_status_email($user_id, $order_id, $new_state, $site_url = '') {
    $user_id   = absint($user_id);
    $order_id  = absint($order_id);
    $new_state = sanitize_key($new_state);

    if (!$user_id || !$order_id || !$new_state) {
        return false;
    }

    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $account_url    = home_url('/account/');
    $onboarding_url = home_url('/onboarding/');

    $site_url = $site_url ? esc_url_raw($site_url) : '';

    if (!$site_url) {
        $saved_site_url = sa_get_order_meta($user_id, $order_id, 'site_url', '');
        $site_url = $saved_site_url ? esc_url_raw($saved_site_url) : '';
    }

    $emails = [
        'onboarding_started' => [
            'subject'      => 'Votre formulaire SiteAuteur est en attente',
            'title'        => 'Terminez votre formulaire',
            'intro'        => 'Votre formulaire de création de site a été commencé.',
            'text'         => 'Pour que nous puissions lancer la création de votre site d’auteur, vous devez terminer et envoyer le formulaire depuis votre espace client.',
            'button_label' => 'Terminer mon formulaire',
            'button_url'   => $onboarding_url,
        ],

        'building' => [
            'subject'      => 'Votre site SiteAuteur est en création',
            'title'        => 'Votre site est en création',
            'intro'        => 'Nous avons bien reçu les informations nécessaires à la création de votre site.',
            'text'         => 'Votre site d’auteur est maintenant en cours de création. Il sera disponible d’ici 48h.',
            'button_label' => 'Accéder à mon espace client',
            'button_url'   => $account_url,
        ],

        'site_live' => [
            'subject'      => 'Votre site SiteAuteur est en ligne',
            'title'        => 'Votre site est prêt',
            'intro'        => 'Bonne nouvelle : votre site d’auteur est désormais prêt et en ligne.',
            'text'         => 'Vous pouvez dès maintenant le consulter grâce au lien ci-dessous.',
            'button_label' => 'Voir mon site',
            'button_url'   => $site_url ?: $account_url,
        ],

        'paused' => [
            'subject'      => 'Votre projet SiteAuteur est suspendu',
            'title'        => 'Votre projet est suspendu',
            'intro'        => 'Votre projet SiteAuteur est actuellement suspendu.',
            'text'         => 'Votre site et les services associés ne sont plus actifs pour le moment. Vous pouvez consulter votre espace client ou nous contacter si vous souhaitez régulariser la situation.',
            'button_label' => 'Accéder à mon espace client',
            'button_url'   => $account_url,
        ],
    ];

    if (empty($emails[$new_state])) {
        return false;
    }

    $email = $emails[$new_state];

    $message = sa_render_project_status_email([
        'display_name' => $display_name,
        'title'        => $email['title'],
        'intro'        => $email['intro'],
        'text'         => $email['text'],
        'button_label' => $email['button_label'],
        'button_url'   => $email['button_url'],
    ]);

    return wp_mail(
        $user->user_email,
        $email['subject'],
        $message,
        sa_get_project_status_email_headers()
    );
}

/**
 * =========================
 * EMAIL - RÉSILIATION PROGRAMMÉE
 * =========================
 */

function sa_send_subscription_cancel_scheduled_email($user_id, $order_id) {
    $user_id  = absint($user_id);
    $order_id = absint($order_id);

    if (!$user_id || !$order_id) {
        return false;
    }

    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $message = sa_render_project_status_email([
        'display_name' => $display_name,
        'title'        => 'Votre résiliation est programmée',
        'intro'        => 'Votre demande de résiliation a bien été prise en compte.',
        'text'         => 'Votre abonnement SiteAuteur sera arrêté à la fin de la période déjà payée. Votre site restera accessible jusqu’à cette date, puis il sera suspendu automatiquement.',
        'button_label' => 'Accéder à mon espace client',
        'button_url'   => home_url('/account/'),
    ]);

    return wp_mail(
        $user->user_email,
        'Votre résiliation SiteAuteur est programmée',
        $message,
        sa_get_project_status_email_headers()
    );
}


/**
 * =========================
 * EMAIL - SITE RÉELLEMENT SUSPENDU
 * =========================
 */

function sa_send_project_really_suspended_email($user_id, $order_id) {
    $user_id  = absint($user_id);
    $order_id = absint($order_id);

    if (!$user_id || !$order_id) {
        return false;
    }

    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $message = sa_render_project_status_email([
        'display_name' => $display_name,
        'title'        => 'Votre site est suspendu',
        'intro'        => 'Votre abonnement SiteAuteur est maintenant terminé.',
        'text'         => 'Votre site d’auteur est désormais suspendu. Les services associés ne sont plus actifs. Vous pouvez réactiver votre abonnement depuis votre espace client si vous souhaitez remettre votre site en ligne.',
        'button_label' => 'Accéder à mon espace client',
        'button_url'   => home_url('/account/'),
    ]);

    return wp_mail(
        $user->user_email,
        'Votre site SiteAuteur est suspendu',
        $message,
        sa_get_project_status_email_headers()
    );
}

/**
 * =========================
 * EMAIL - SITE RÉACTIVÉ
 * =========================
 */

function sa_send_project_reactivated_email($user_id, $order_id, $site_url = '') {
    $user_id  = absint($user_id);
    $order_id = absint($order_id);

    if (!$user_id || !$order_id) {
        return false;
    }

    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return false;
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    if (!$site_url) {
        $site_url = sa_get_order_meta($user_id, $order_id, 'site_url', '');
    }

    $site_url = $site_url ? esc_url_raw($site_url) : home_url('/account/');

    $message = sa_render_project_status_email([
        'display_name' => $display_name,
        'title'        => 'Votre site est à nouveau en ligne',
        'intro'        => 'Bonne nouvelle : votre abonnement a bien été réactivé.',
        'text'         => 'Votre site SiteAuteur est de nouveau actif et accessible en ligne.',
        'button_label' => 'Voir mon site',
        'button_url'   => $site_url,
    ]);

    return wp_mail(
        $user->user_email,
        'Votre site SiteAuteur est à nouveau en ligne',
        $message,
        sa_get_project_status_email_headers()
    );
}