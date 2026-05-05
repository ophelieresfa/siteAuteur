<?php if (!defined('ABSPATH')) exit; ?>

<?php
$email        = !empty($email) ? $email : '';
$display_name = !empty($display_name) ? $display_name : '';
$resend_sent  = !empty($resend_sent);

$image_url = '/wp-content/uploads/2026/04/illustration-siteauteur-transparent.png';

$session_id = !empty($_GET['session_id'])
    ? sanitize_text_field($_GET['session_id'])
    : '';

$current_url = add_query_arg(
    [
        'session_id'         => $session_id,
        'sa_resend_welcome'  => 1,
    ],
    home_url('/confirmation-commande/')
);

$mail_url = 'mailto:';

if ($email && strpos($email, '@') !== false) {
    $domain = strtolower(substr(strrchr($email, '@'), 1));

    if (strpos($domain, 'gmail.') !== false) {
        $mail_url = 'https://mail.google.com/';
    } elseif (strpos($domain, 'yahoo.') !== false || strpos($domain, 'ymail.') !== false) {
        $mail_url = 'https://mail.yahoo.com/';
    } elseif (
        strpos($domain, 'outlook.') !== false ||
        strpos($domain, 'hotmail.') !== false ||
        strpos($domain, 'live.') !== false ||
        strpos($domain, 'msn.') !== false
    ) {
        $mail_url = 'https://outlook.live.com/mail/';
    }
}

$contact_url = home_url('/contact/');
?>

<section class="sa-confirmation-page">
<?php if ($resend_sent) : ?>
<div class="sa-confirmation-resend-notice">
    L’email vient d’être renvoyé.
</div>
<?php endif; ?>

    <div class="sa-confirmation-container">

        <div class="sa-confirmation-hero">

            <div class="sa-confirmation-hero-content">
                    <h1>Félicitations, votre commande est confirmée&nbsp;!</h1>

                <p class="sa-confirmation-lead">
                    Votre compte a bien été créé. Pour accéder à votre tableau de bord,
                    rendez-vous dans votre boîte mail afin de définir votre mot de passe.
                </p>

                <?php if ($email) : ?>
                    <div class="sa-confirmation-email-card">
                        <span class="sa-confirmation-email-icon" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6.75C4 6.336 4.336 6 4.75 6H19.25C19.664 6 20 6.336 20 6.75V17.25C20 17.664 19.664 18 19.25 18H4.75C4.336 18 4 17.664 4 17.25V6.75Z" stroke="currentColor" stroke-width="1.9"/>
                                <path d="M4.8 7.2L11.18 12.18C11.66 12.56 12.34 12.56 12.82 12.18L19.2 7.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <span>Compte créé avec&nbsp;:</span>
                        <strong><?php echo esc_html($email); ?></strong>
                    </div>
                <?php endif; ?>

            </div>

            <div class="sa-confirmation-hero-visual" aria-hidden="true">
                <img src="<?php echo esc_url($image_url); ?>" alt="">
            </div>

        </div>

        <div class="sa-confirmation-steps-card">

            <h2>Comment accéder à votre compte en 3 étapes simples</h2>

            <div class="sa-confirmation-steps">

                <div class="sa-confirmation-step">
                    <span class="sa-confirmation-step-number">1</span>

                    <div class="sa-confirmation-step-icon" aria-hidden="true">
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.5 10.25C6.5 9.56 7.06 9 7.75 9H26.25C26.94 9 27.5 9.56 27.5 10.25V23.75C27.5 24.44 26.94 25 26.25 25H7.75C7.06 25 6.5 24.44 6.5 23.75V10.25Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M7.4 10.4L15.58 16.62C16.43 17.27 17.57 17.27 18.42 16.62L26.6 10.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div>
                        <h3>Ouvrez votre email de bienvenue</h3>
                        <p>
                            Consultez votre boîte de réception. L’objet de l’email est :
                            <strong>« Votre commande SiteAuteur est confirmée »</strong>.
                        </p>
                    </div>
                </div>

                <div class="sa-confirmation-step">
                    <span class="sa-confirmation-step-number">2</span>

                    <div class="sa-confirmation-step-icon" aria-hidden="true">
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 15V12.5C11 9.74 13.24 7.5 16 7.5C18.76 7.5 21 9.74 21 12.5V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <rect x="9" y="15" width="14" height="11" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M16 19V22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <h3>Définissez votre mot de passe</h3>
                        <p>
                            Cliquez sur le bouton présent dans l’email pour créer un mot de passe sécurisé.
                        </p>
                    </div>
                </div>

                <div class="sa-confirmation-step">
                    <span class="sa-confirmation-step-number">3</span>

                    <div class="sa-confirmation-step-icon" aria-hidden="true">
                        <svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="8" y="7.5" width="18" height="19" rx="1.8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M13 12.5H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M13 17H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M13 21.5H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M10.5 12.5H10.52" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <h3>Accédez à votre tableau de bord</h3>
                        <p>
                            Connectez-vous ensuite à votre espace client pour suivre votre projet.
                        </p>
                    </div>
                </div>

            </div>

            <div class="sa-confirmation-actions">

                <a class="sa-confirmation-btn sa-confirmation-btn-primary" href="<?php echo esc_url($mail_url); ?>" target="_blank" rel="noopener">
                    <span aria-hidden="true">
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6.75C4 6.336 4.336 6 4.75 6H19.25C19.664 6 20 6.336 20 6.75V17.25C20 17.664 19.664 18 19.25 18H4.75C4.336 18 4 17.664 4 17.25V6.75Z" stroke="currentColor" stroke-width="1.9"/>
                            <path d="M4.8 7.2L11.18 12.18C11.66 12.56 12.34 12.56 12.82 12.18L19.2 7.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    Vérifier mes emails
                </a>

                <div class="sa-confirmation-secure-note">
                    <span aria-hidden="true">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 10V7.8C8 5.7 9.7 4 11.8 4H12.2C14.3 4 16 5.7 16 7.8V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <rect x="7" y="10" width="10" height="9" rx="1.6" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </span>
                    Lien sécurisé, valable 24h.
                </div>

            </div>

        </div>

        <div class="sa-confirmation-help">

            <div class="sa-confirmation-help-left">
                <span class="sa-confirmation-help-icon" aria-hidden="true">?</span>

                <div>
                    <strong>Vous n’avez pas reçu l’email&nbsp;?</strong>
                    <span>Vérifiez vos spams ou courriers indésirables.</span>
                </div>
            </div>

            <div class="sa-confirmation-help-actions">
                <a href="<?php echo esc_url($current_url); ?>">
                    Renvoyer l’email
                </a>

                <span>ou</span>

                <a class="sa-confirmation-help-link" target="_blank" href="<?php echo esc_url($contact_url); ?>">
                    contactez-nous
                </a>
            </div>

        </div>

        <div class="sa-confirmation-security">

            <span aria-hidden="true">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3.8L18.5 6.4V10.9C18.5 15.1 15.87 18.73 12 20.2C8.13 18.73 5.5 15.1 5.5 10.9V6.4L12 3.8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M9.6 12.1L11.2 13.7L14.7 10.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>

            <div>
                <strong>Votre sécurité est notre priorité.</strong>
                <p>Vos données sont protégées et ne seront jamais partagées.</p>
            </div>

        </div>

    </div>

</section>