<?php if (!defined('ABSPATH')) exit; ?>

<section class="sa-set-password-page">

    <div class="sa-set-password-container">

        <section class="sa-set-password-hero">
            <div class="sa-set-password-hero-left">

                <h1>Définir mon mot de passe</h1>

                <p>
                    Créez un mot de passe sécurisé pour accéder à votre espace client SiteAuteur.
                </p>
            </div>
        </section>

        <?php if (!empty($error)) : ?>
            <div class="sa-account-alert sa-account-alert-error sa-set-password-error">
                <?php echo esc_html($error); ?>
            </div>
        <?php endif; ?>

        <section class="sa-set-password-main-card">

            <aside class="sa-set-password-intro">

                <div class="sa-set-password-intro-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M12 3L18.5 5.5V11.5C18.5 15.5 15.9 19.1 12 20.5C8.1 19.1 5.5 15.5 5.5 11.5V5.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M9.7 12.2L11.2 13.7L14.6 10.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h2>Bienvenue sur votre compte</h2>

                <p>
                    Votre compte a été créé avec succès.<br>
                    Il ne vous reste plus qu’à définir un mot de passe sécurisé pour commencer.
                </p>

                <div class="sa-set-password-steps">
                    <div class="sa-set-password-step">
                        <span>1</span>
                        <p>Choisissez un mot de passe sécurisé</p>
                    </div>

                    <div class="sa-set-password-step">
                        <span>2</span>
                        <p>Confirmez votre mot de passe</p>
                    </div>

                    <div class="sa-set-password-step">
                        <span>3</span>
                        <p>Accédez à votre tableau de bord</p>
                    </div>
                </div>

                <div class="sa-set-password-secure-box">
                    <span aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 3L18.5 5.5V11.5C18.5 15.5 15.9 19.1 12 20.5C8.1 19.1 5.5 15.5 5.5 11.5V5.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9.7 12.2L11.2 13.7L14.6 10.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>

                    <div>
                        <strong>Lien sécurisé et valable 24h</strong>
                        <p>Pour votre sécurité, ce lien expirera dans 24 heures.</p>
                    </div>
                </div>

            </aside>

            <div class="sa-set-password-form-panel">

                <form method="post" class="sa-password-form sa-set-password-form">
                    <?php wp_nonce_field('sa_reset_password_action', 'sa_reset_password_nonce'); ?>

                    <div class="sa-password-field">
                        <label for="sa_new_password">Nouveau mot de passe</label>

                        <div class="sa-password-input-wrap">
                            <span class="sa-password-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <input
                                type="password"
                                id="sa_new_password"
                                name="pass1"
                                placeholder="Saisis ton nouveau mot de passe"
                                autocomplete="new-password"
                                required
                            >

                            <button type="button" class="sa-password-toggle" aria-label="Afficher ou masquer le mot de passe">
                                <svg class="sa-eye-closed" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 4L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>

                                <svg class="sa-eye-open" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>

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
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    Minimum 8 caractères
                                </li>

                                <li data-rule="uppercase">
                                    <span class="sa-rule-check">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    1 lettre majuscule
                                </li>

                                <li data-rule="digit">
                                    <span class="sa-rule-check">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    1 chiffre
                                </li>

                                <li data-rule="special">
                                    <span class="sa-rule-check">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.7"/>
                                            <path d="M8.7 12.2L10.9 14.4L15.3 10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    1 caractère spécial (ex. ! @ # $ %)
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="sa-password-field">
                        <label for="sa_confirm_password">Confirmer le mot de passe</label>

                        <div class="sa-password-input-wrap">
                            <span class="sa-password-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <input
                                type="password"
                                id="sa_confirm_password"
                                name="pass2"
                                placeholder="Confirme ton mot de passe"
                                autocomplete="new-password"
                                required
                            >

                            <button type="button" class="sa-password-toggle" aria-label="Afficher ou masquer le mot de passe">
                                <svg class="sa-eye-closed" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M4 4L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>

                                <svg class="sa-eye-open" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                                    <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="sa-set-password-actions">
                        <button type="submit" class="sa-set-password-btn sa-set-password-btn-primary">
                            Enregistrer mon mot de passe
                        </button>
                    </div>

                </form>

            </div>

        </section>

        <section class="sa-set-password-security-card">

            <div class="sa-set-password-security-item">
                <span aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 3L18.5 5.5V11.5C18.5 15.5 15.9 19.1 12 20.5C8.1 19.1 5.5 15.5 5.5 11.5V5.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9.7 12.2L11.2 13.7L14.6 10.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                </span>

                <div>
                    <strong>Sécurité</strong>
                    <p>Connexion sécurisée</p>
                </div>
            </div>

            <div class="sa-set-password-security-item">
                <span aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>

                <div>
                    <strong>Confidentialité</strong>
                    <p>Vos données sont protégées</p>
                </div>
            </div>

            <div class="sa-set-password-security-item">
                <span aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M5.8 19C6.7 15.8 9 14.2 12 14.2C15 14.2 17.3 15.8 18.2 19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>

                <div>
                    <strong>Accès</strong>
                    <p>Vous pourrez ensuite accéder à votre tableau de bord</p>
                </div>
            </div>

        </section>

    </div>

</section>