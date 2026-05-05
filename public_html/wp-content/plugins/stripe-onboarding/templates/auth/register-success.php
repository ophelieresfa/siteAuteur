<?php
if (!defined('ABSPATH')) exit;

$current_user = wp_get_current_user();

$user_name  = '';
$user_email = '';

if ($current_user && $current_user->exists()) {
    $user_name  = $current_user->first_name ?: $current_user->display_name;
    $user_email = $current_user->user_email;
}

$login_url   = home_url('/login/');
$contact_url = home_url('/contact/');
?>

<section class="sa-register-success-page">

    <div class="sa-register-success-container">

        <div class="sa-register-success-hero">

            <div class="sa-register-success-left">

                <div class="sa-register-success-badge">
                    <span>
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                    </span>
                    Inscription confirmée
                </div>

                <h1>
                    <?php if (!empty($user_name)) : ?>
                        Bienvenue <?php echo esc_html($user_name); ?>,<br>
                        votre compte est <strong>prêt.</strong>
                    <?php else : ?>
                        Votre compte est <strong>prêt.</strong>
                    <?php endif; ?>
                </h1>

                <p class="sa-register-success-lead">
                    Votre inscription a bien été prise en compte. Il ne vous reste plus qu’à vérifier votre email pour finaliser l’accès à votre espace client.
                </p>

                <?php if (!empty($user_email)) : ?>
                    <div class="sa-register-success-email">
                        <div class="sa-register-success-email-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16v12H4z"></path>
                                <path d="M4 7l8 6 8-6"></path>
                            </svg>
                        </div>
                        <div>
                            <span>Email utilisé</span>
                            <strong><?php echo esc_html($user_email); ?></strong>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="sa-register-success-steps">

                    <div class="sa-register-success-step">
                        <div class="sa-register-success-step-number">1</div>
                        <div class="sa-register-success-step-content">
                            <h2>Ouvrez votre boîte mail</h2>
                            <p>
                                Un email SiteAuteur vient de vous être envoyé avec le lien nécessaire pour finaliser votre accès.
                            </p>
                        </div>
                        <div class="sa-register-success-step-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16v12H4z"></path>
                                <path d="M4 7l8 6 8-6"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="sa-register-success-step">
                        <div class="sa-register-success-step-number">2</div>
                        <div class="sa-register-success-step-content">
                            <h2>Créez votre mot de passe</h2>
                            <p>
                                Cliquez sur le lien reçu pour définir votre mot de passe et activer votre compte.
                            </p>
                        </div>
                        <div class="sa-register-success-step-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M7 11V8a5 5 0 0 1 10 0v3"></path>
                                <path d="M5 11h14v10H5z"></path>
                                <path d="M12 15v3"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="sa-register-success-step">
                        <div class="sa-register-success-step-number">3</div>
                        <div class="sa-register-success-step-content">
                            <h2>Remplissez le formulaire</h2>
                            <p>
                                Une fois connecté, vous pourrez transmettre les informations nécessaires à la création de votre site.
                            </p>
                        </div>
                        <div class="sa-register-success-step-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M7 3h7l5 5v13H7z"></path>
                                <path d="M14 3v5h5"></path>
                                <path d="M10 13h6"></path>
                                <path d="M10 17h4"></path>
                            </svg>
                        </div>
                    </div>

                </div>

                <div class="sa-register-success-actions">
                    <a href="<?php echo esc_url($login_url); ?>" class="sa-register-success-btn sa-register-success-btn-primary">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path>
                                <path d="M4 21a8 8 0 0 1 16 0"></path>
                            </svg>
                        </span>
                        Accéder à mon compte
                    </a>

                    <a href="<?php echo esc_url($contact_url); ?>" class="sa-register-success-btn sa-register-success-btn-secondary">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 18v-5a7 7 0 0 1 14 0v5"></path>
                                <path d="M5 18h4v-6H5z"></path>
                                <path d="M15 18h4v-6h-4z"></path>
                            </svg>
                        </span>
                        Contacter le support
                    </a>
                </div>

                <p class="sa-register-success-note">
                    Pensez à vérifier vos spams ou courriers indésirables si vous ne trouvez pas l’email.
                </p>

            </div>

            <div class="sa-register-success-right">

                <div class="sa-register-success-bg-bubble sa-register-success-bg-bubble-1"></div>
                <div class="sa-register-success-bg-bubble sa-register-success-bg-bubble-2"></div>
                <div class="sa-register-success-bg-bubble sa-register-success-bg-bubble-3"></div>

                <div class="sa-register-success-card">

                    <div class="sa-register-success-main-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 6h16v12H4z"></path>
                            <path d="M4 7l8 6 8-6"></path>
                        </svg>

                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17l-5-5"></path>
                            </svg>
                        </span>
                    </div>

                    <h3>Email de confirmation envoyé</h3>

                    <p>
                        Activez votre compte pour accéder à votre espace client et commencer la création de votre site d’auteur.
                    </p>

                    <div class="sa-register-success-progress">

                        <div class="sa-register-success-progress-item is-done">
                            <span></span>
                            Compte créé
                        </div>

                        <div class="sa-register-success-progress-item is-current">
                            <span></span>
                            Email à valider
                        </div>

                        <div class="sa-register-success-progress-item">
                            <span></span>
                            Formulaire à remplir
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>