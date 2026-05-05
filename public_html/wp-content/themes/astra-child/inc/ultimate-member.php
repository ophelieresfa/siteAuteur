<?php
if (!defined('ABSPATH')) exit;

add_action('init', 'siteauteur_override_um_login_layout', 20);
add_action('init', 'siteauteur_override_um_register_layout', 20);

add_filter('um_get_form_fields', 'siteauteur_customize_register_form_fields', 20, 2);

function siteauteur_site_register_form_id() {
    return 1074;
}

function siteauteur_override_um_login_layout() {
    if (is_admin()) {
        return;
    }

    remove_action('um_after_login_fields', 'um_add_submit_button_to_login', 1000);
    remove_action('um_after_login_fields', 'um_after_login_submit', 1001);

    add_action('um_after_login_fields', 'siteauteur_custom_login_actions', 1000);
}

function siteauteur_custom_login_actions() {
    ?>
    <div class="sa-auth-actions sa-auth-actions-login">
        <div class="sa-auth-remember">
            <div class="um-field um-field-c">
                <div class="um-field-area">
                    <label class="um-field-checkbox" for="rememberme-custom">
                        <input type="checkbox" name="rememberme" id="rememberme-custom" value="1">
                        <span class="um-field-checkbox-state">
                            <i class="um-icon-android-checkbox-outline-blank"></i>
                        </span>
                        <span class="um-field-checkbox-option">Se souvenir de moi</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="sa-auth-primary">
            <input type="submit" value="Connexion" class="um-button" id="um-submit-btn">
        </div>

        <div class="sa-auth-forgot">
            <a href="<?php echo esc_url(home_url('/password-reset/')); ?>" class="um-link-alt">
                Mot de passe oublié ?
            </a>
        </div>

        <div class="sa-auth-separator">
            <span>ou</span>
        </div>

        <div class="sa-auth-secondary">
            <a href="<?php echo esc_url(home_url('/register/')); ?>" class="um-button um-alt">
                Créer mon compte
            </a>
        </div>
    </div>
    <?php
}

function siteauteur_override_um_register_layout() {
    if (is_admin()) {
        return;
    }

    remove_action('um_after_register_fields', 'um_add_submit_button_to_register', 1000);
    add_action('um_after_register_fields', 'siteauteur_custom_register_actions', 1000);
}

/**
 * Force le champ de confirmation du mot de passe
 * + personnalise les labels/placeholders du formulaire d'inscription.
 */
function siteauteur_customize_register_form_fields($fields, $form_id) {
    if ((int) $form_id !== (int) siteauteur_site_register_form_id()) {
        return $fields;
    }

    if (!is_array($fields) || empty($fields['user_password'])) {
        return $fields;
    }

    $fields['user_password']['force_confirm_pass']   = 1;
    $fields['user_password']['label']                = 'Mot de passe';
    $fields['user_password']['label_confirm_pass']   = 'Confirmer le mot de passe';
    $fields['user_password']['placeholder']          = 'Ex : Password123!';
    $fields['user_password']['required']             = 1;

    if (!empty($fields['confirm_user_password']) && is_array($fields['confirm_user_password'])) {
        $fields['confirm_user_password']['label']       = 'Confirmer le mot de passe';
        $fields['confirm_user_password']['placeholder'] = 'Ex : Password123!';
        $fields['confirm_user_password']['required']    = 1;
    }

    return $fields;
}

function siteauteur_custom_register_actions() {
    ?>
    <div class="sa-auth-actions sa-auth-actions-register">
        <div class="sa-auth-primary">
            <input type="submit" value="S’inscrire" class="um-button" id="um-submit-btn">
        </div>

        <div class="sa-auth-separator">
            <span>ou</span>
        </div>

        <div class="sa-auth-secondary">
            <a href="<?php echo esc_url(home_url('/login/')); ?>" class="um-button um-alt">
                J’ai déjà un compte
            </a>
        </div>
    </div>
    <?php
}

add_action('um_after_form_fields', 'siteauteur_add_password_strength_block', 20);

function siteauteur_add_password_strength_block($args) {

    // On cible uniquement le formulaire inscription
    if (empty($args['mode']) || $args['mode'] !== 'register') {
        return;
    }

    ?>
    <div class="sa-password-meta">

        <div class="sa-password-strength" id="sa-password-strength">
            <div class="sa-strength-bars">
                <span data-bar="1"></span>
                <span data-bar="2"></span>
                <span data-bar="3"></span>
                <span data-bar="4"></span>
            </div>
            <div class="sa-strength-text">
                Force du mot de passe : <strong id="sa-password-strength-text">Très faible</strong>
            </div>
        </div>

        <div class="sa-password-rules" id="sa-password-rules">
            <h4>Ton mot de passe doit contenir :</h4>
            <ul>
                <li data-rule="length">
                    <span class="sa-rule-check">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </span>
                    Minimum 8 caractères
                </li>

                <li data-rule="uppercase">
                    <span class="sa-rule-check">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </span>
                    1 lettre majuscule
                </li>

                <li data-rule="digit">
                    <span class="sa-rule-check">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </span>
                    1 chiffre
                </li>

                <li data-rule="special">
                    <span class="sa-rule-check">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7"/>
                        </svg>
                    </span>
                    1 caractère spécial (ex. ! @ # $ %)
                </li>
            </ul>
        </div>

    </div>
    <?php
}

/**
 * =========================
 * MOT DE PASSE OUBLIÉ - ENVOI EMAIL PERSONNALISÉ
 * =========================
 */

add_action('template_redirect', 'siteauteur_handle_custom_password_reset_request', 5);

function siteauteur_handle_custom_password_reset_request() {
    if (is_admin()) {
        return;
    }

    if (!is_page('password-reset')) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (empty($_POST['sa_forgot_password_submit'])) {
        return;
    }

    if (
        empty($_POST['sa_forgot_password_nonce']) ||
        !wp_verify_nonce($_POST['sa_forgot_password_nonce'], 'sa_forgot_password_action')
    ) {
        wp_safe_redirect(home_url('/password-reset/'));
        exit;
    }

    $email = !empty($_POST['username_b'])
        ? sanitize_email(wp_unslash($_POST['username_b']))
        : '';

    if (empty($email) || !is_email($email)) {
        wp_safe_redirect(home_url('/password-reset/?updated=checkemail'));
        exit;
    }

    $user = get_user_by('email', $email);

    /**
     * Sécurité :
     * on affiche toujours le même message,
     * même si aucun compte n'existe avec cet email.
     */
    if ($user && !empty($user->ID)) {
        siteauteur_send_custom_password_reset_email($user);
    }

    wp_safe_redirect(home_url('/password-reset/?updated=checkemail'));
    exit;
}

function siteauteur_send_custom_password_reset_email($user) {
    if (!$user || empty($user->ID) || empty($user->user_email)) {
        return false;
    }

    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        return false;
    }

    $reset_url = home_url(
        '/definir-mon-mot-de-passe/?key=' . rawurlencode($reset_key) .
        '&login=' . rawurlencode($user->user_login) .
        '&mode=forgot_password'
    );

    $template_path = get_stylesheet_directory() . '/ultimate-member/email/resetpw_email.php';

    if (file_exists($template_path)) {
        ob_start();
        include $template_path;
        $message = ob_get_clean();
    } else {
        $message = '
            <p>Bonjour {display_name},</p>
            <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
            <p><a href="{password_reset_link}">Réinitialiser mon mot de passe</a></p>
        ';
    }

    $display_name = !empty($user->display_name)
        ? $user->display_name
        : $user->user_email;

    $message = str_replace('{display_name}', esc_html($display_name), $message);
    $message = str_replace('{password_reset_link}', esc_url($reset_url), $message);

    $subject = 'Réinitialisation de votre mot de passe - SiteAuteur';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: SiteAuteur <noreply@siteauteur.fr>',
    ];

    return wp_mail($user->user_email, $subject, $message, $headers);
}

/**
 * =========================
 * REDIRECTION APRÈS INSCRIPTION ULTIMATE MEMBER
 * =========================
 *
 * Important :
 * - ne pas utiliser wp_safe_redirect() + exit dans um_registration_complete
 * - on laisse Ultimate Member envoyer ses emails
 * - on modifie seulement l'URL finale de redirection
 */

/**
 * Cas classique : inscription auto-approuvée.
 */
add_filter('um_registration_redirect_url', 'siteauteur_um_registration_redirect_url', 20, 2);

function siteauteur_um_registration_redirect_url($url, $user_id) {
    return home_url('/register-success/');
}

/**
 * Cas activation email avec action "redirect_url".
 */
add_filter('um_registration_pending_user_redirect', 'siteauteur_um_pending_registration_redirect_url', 20, 3);

function siteauteur_um_pending_registration_redirect_url($url, $status, $user_id) {
    return home_url('/register-success/');
}

/**
 * Cas activation email avec action "show_message".
 * C'est probablement TON cas actuellement.
 */
add_filter('um_registration_show_message_redirect_url', 'siteauteur_um_show_message_registration_redirect_url', 20, 4);

function siteauteur_um_show_message_registration_redirect_url($url, $status, $user_id, $form_data) {
    return home_url('/register-success/');
}