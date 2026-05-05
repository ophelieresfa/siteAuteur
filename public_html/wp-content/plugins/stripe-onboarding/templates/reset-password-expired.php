<?php if (!defined('ABSPATH')) exit; ?>

<?php
$can_resend   = !empty($can_resend);
$resend_sent  = !empty($resend_sent);
$resend_error = !empty($resend_error) ? $resend_error : '';
$login        = !empty($login) ? $login : '';
$session_id   = !empty($session_id) ? $session_id : '';
$account_url  = !empty($account_url) ? $account_url : home_url('/account/');
$contact_url  = !empty($contact_url) ? $contact_url : home_url('/contact/');
?>

<section class="sa-invalid-page">
    
    <?php if ($resend_sent) : ?>
        <div class="sa-invalid-notice sa-invalid-notice-success">
            Un nouveau lien vient d’être envoyé par email.
        </div>
    <?php endif; ?>

    <?php if ($resend_error) : ?>
        <div class="sa-invalid-notice sa-invalid-notice-error">
            <?php echo esc_html($resend_error); ?>
        </div>
    <?php endif; ?>

    <div class="sa-invalid-container">

        <div class="sa-invalid-hero">
            <div class="sa-invalid-hero__content">
                <h1>Lien invalide ou expiré</h1>

                <p>
                    Le lien de définition de mot de passe est invalide, incomplet ou expiré.
                </p>

                <p>
                    Pour des raisons de sécurité, ce lien n’est valable qu’un temps limité.
                </p>
            </div>

            <div class="sa-invalid-hero__image">
                <img
                    src="/wp-content/uploads/2026/04/ChatGPT-Image-14-avr.-2026-10_31_58.png"
                    alt="Lien invalide ou expiré"
                />
            </div>
        </div>

        <div class="sa-invalid-card">
            <div class="sa-invalid-card__inner">

                <div class="sa-invalid-head">
                    <div class="sa-invalid-head__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M12 3L21 19H3L12 3Z"
                                fill="#FFD66B"
                                stroke="#E1B23C"
                                stroke-width="1.2"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M12 8V13"
                                stroke="#FFFFFF"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                            <circle cx="12" cy="16.5" r="1.1" fill="#FFFFFF" />
                        </svg>
                    </div>

                    <h2>Vous ne pouvez plus définir votre mot de passe avec ce lien.</h2>
                </div>

                <p class="sa-invalid-intro">Cela peut arriver si :</p>

                <ul class="sa-invalid-list">
                    <li>Le lien a déjà été utilisé</li>
                    <li>Le lien a expiré</li>
                    <li>Le lien est incomplet</li>
                </ul>

                <div class="sa-invalid-security-box">
                    <span class="sa-invalid-security-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path
                                d="M7 10V8a5 5 0 0110 0v2"
                                stroke="#97A6BA"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                            <rect
                                x="5"
                                y="10"
                                width="14"
                                height="10"
                                rx="2"
                                fill="#EEF1F5"
                                stroke="#C9D1DB"
                                stroke-width="1.2"
                            />
                        </svg>
                    </span>

                    <p>
                        Pour votre sécurité, les liens de création de mot de passe expirent
                        automatiquement après un certain délai.
                    </p>
                </div>

                <div class="sa-invalid-actions">

                    <?php if ($can_resend && $login) : ?>
                        <form method="post" class="sa-invalid-resend-form">
                            <?php wp_nonce_field('sa_resend_reset_link_action', 'sa_resend_reset_link_nonce'); ?>

                            <input type="hidden" name="sa_resend_login" value="<?php echo esc_attr($login); ?>">
                            <input type="hidden" name="sa_resend_session_id" value="<?php echo esc_attr($session_id); ?>">

                            <button type="submit" class="sa-invalid-btn">
                                Demander un nouveau lien
                            </button>
                        </form>
                    <?php else : ?>
                        <a href="<?php echo esc_url($contact_url); ?>" class="sa-invalid-btn">
                            Contacter le support
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url($account_url); ?>" class="sa-invalid-back-link">
                        Mon compte
                    </a>

                </div>

            </div>
        </div>

    </div>
</section>