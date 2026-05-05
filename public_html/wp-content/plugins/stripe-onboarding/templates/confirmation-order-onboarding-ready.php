<?php if (!defined('ABSPATH')) exit; ?>

<?php
$email      = !empty($email) ? $email : '';
$first_name = !empty($first_name) ? $first_name : '';
$session_id = !empty($session_id) ? $session_id : '';

$image_url = '/wp-content/uploads/2026/04/ChatGPT-Image-24-avr.-2026-12_15_17.png';

$onboarding_url = home_url('/onboarding/');

if ($session_id) {
    $onboarding_url = add_query_arg(
        ['session_id' => $session_id],
        $onboarding_url
    );
}

$account_url = home_url('/account/');
$contact_url = home_url('/contact/');
?>

<section class="sa-confirm-onboarding-page">

    <div class="sa-confirm-onboarding-container">

        <section class="sa-confirm-onboarding-hero">

            <div class="sa-confirm-onboarding-hero-left">
                <h1>
                    <?php if ($first_name) : ?>
                        Félicitations <?php echo esc_html($first_name); ?>, votre compte est prêt&nbsp;!
                    <?php else : ?>
                        Félicitations, votre compte est prêt&nbsp;!
                    <?php endif; ?>
                </h1>

                <p>
                    Votre commande est confirmée et votre mot de passe a bien été créé.
                    Il ne vous reste plus qu’à remplir le formulaire de création pour lancer votre site d’auteur.
                </p>

                <?php if ($email) : ?>
                    <div class="sa-confirm-onboarding-email-card">
                        <span aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 6.75C4 6.336 4.336 6 4.75 6H19.25C19.664 6 20 6.336 20 6.75V17.25C20 17.664 19.664 18 19.25 18H4.75C4.336 18 4 17.664 4 17.25V6.75Z" stroke="currentColor" stroke-width="1.9"/>
                                <path d="M4.8 7.2L11.18 12.18C11.66 12.56 12.34 12.56 12.82 12.18L19.2 7.2" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>

                        <div>
                            <small>Compte associé</small>
                            <strong><?php echo esc_html($email); ?></strong>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="sa-confirm-onboarding-hero-visual" aria-hidden="true">
                <img src="/wp-content/uploads/2026/04/image_sans_fond.png" alt="illustration">
            </div>

        </section>

        <section class="sa-confirm-onboarding-main-card">

            <div class="sa-confirm-onboarding-card-header">
                <span aria-hidden="true">
                    <!-- Nouvelle icône : formulaire / document -->
                        <svg width="40" height="40" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="8" y="7.5" width="18" height="19" rx="1.8" stroke="currentColor" stroke-width="1.8"></rect>
                            <path d="M13 12.5H15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                            <path d="M13 17H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                            <path d="M13 21.5H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"></path>
                            <path d="M10.5 12.5H10.52" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"></path>
                        </svg>
                </span>

                <div>
                    <h2>Complétez maintenant le formulaire de création</h2>
                    <p>
                        Ces informations nous permettent de préparer votre site, vos pages, vos textes
                        et les éléments liés à votre livre.
                    </p>
                </div>
            </div>

            <div class="sa-confirm-onboarding-steps">

                <div class="sa-confirm-onboarding-step">
                    <span>1</span>
                    <div>
                        <h3>Votre livre</h3>
                        <p>Titre, résumé, couverture, liens d’achat et informations importantes.</p>
                    </div>
                </div>

                <div class="sa-confirm-onboarding-step">
                    <span>2</span>
                    <div>
                        <h3>Votre univers d’auteur</h3>
                        <p>Biographie, photos, ambiance graphique et éléments de confiance.</p>
                    </div>
                </div>

                <div class="sa-confirm-onboarding-step">
                    <span>3</span>
                    <div>
                        <h3>Votre site</h3>
                        <p>Nous utilisons vos réponses pour créer votre site officiel clé en main.</p>
                    </div>
                </div>

            </div>

            <div class="sa-confirm-onboarding-actions">

                <a class="sa-confirm-onboarding-primary" href="<?php echo esc_url($onboarding_url); ?>">
                    <span aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                            <path d="M13 6L19 12L13 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    Remplir le formulaire
                </a>

                <a class="sa-confirm-onboarding-secondary" href="<?php echo esc_url($account_url); ?>">
                    Aller à mon tableau de bord
                </a>

            </div>

        </section>

        <section class="sa-confirm-onboarding-help">

            <div class="sa-confirm-onboarding-help-item">
                <span aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3.5L18.5 6.3V11C18.5 15.2 15.9 18.8 12 20.3C8.1 18.8 5.5 15.2 5.5 11V6.3L12 3.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M9.7 12.2L11.2 13.7L14.6 10.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>

                <div>
                    <strong>Accès sécurisé</strong>
                    <p>Votre compte est protégé et votre mot de passe est enregistré.</p>
                </div>
            </div>

            <div class="sa-confirm-onboarding-help-item">
                <span aria-hidden="true">
                    <!-- Nouvelle icône : formulaire guidé / checklist -->
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="5" y="4" width="14" height="16" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M8.2 8.3L9.2 9.3L10.8 7.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.5 8.5H15.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8.2 12.3L9.2 13.3L10.8 11.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.5 12.5H15.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M8.2 16.3L9.2 17.3L10.8 15.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12.5 16.5H15.8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>

                <div>
                    <strong>Formulaire guidé</strong>
                    <p>Vous pouvez remplir les informations étape par étape.</p>
                </div>
            </div>

            <div class="sa-confirm-onboarding-help-item">
                <span aria-hidden="true">
                    <!-- Nouvelle icône : aide / question -->
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 20C16.4183 20 20 16.6421 20 12.5C20 8.35786 16.4183 5 12 5C7.58172 5 4 8.35786 4 12.5C4 14.1283 4.55377 15.6354 5.5 16.8571C5.5 16.8571 5.1 18.4 4.5 19.5C6.13896 19.1672 7.3898 18.6025 8.24794 18.1192C9.37495 19.3244 10.9583 20 12 20Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M10.6 10.4C10.6 9.51333 11.2909 8.9 12.2 8.9C13.0962 8.9 13.8 9.47633 13.8 10.28C13.8 10.9 13.4709 11.2821 12.9387 11.6468C12.4183 12.0035 12.1 12.358 12.1 12.95" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12.1" cy="15.8" r="0.9" fill="currentColor"/>
                    </svg>
                </span>

                <div>
                    <strong>Besoin d’aide&nbsp;?</strong>
                    <p><a href="<?php echo esc_url($contact_url); ?>">Contactez-nous</a> si vous avez une question.</p>
                </div>
            </div>

        </section>

    </div>

</section>