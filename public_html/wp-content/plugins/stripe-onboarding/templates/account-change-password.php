<?php if (!defined('ABSPATH')) exit; ?>

<div class="sa-account-premium sa-password-page">
    <section class="sa-account-hero sa-password-hero">
        <div class="sa-account-hero-left">
            <span class="sa-account-kicker">ESPACE CLIENT</span>
            <h1>Modifier mon mot de passe</h1>
            <p class="sa-account-hero-text">
                Sécurise ton compte en mettant à jour ton mot de passe.
            </p>
        </div>

        <aside class="sa-account-help-card">
            <h3>Besoin d'aide ?</h3>
            <p>Notre équipe est là pour toi.</p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="sa-account-help-btn">
                Contacter le support
            </a>
        </aside>
    </section>

    <div class="sa-account-body">
        <?php echo sa_render_template('partials/account-sidebar'); ?>

        <main class="sa-account-main-premium">
            <article class="sa-password-main-card">
                <div class="sa-password-card-header">
                    <div class="sa-password-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L18.5 5.5V11.5C18.5 15.5 15.9 19.1 12 20.5C8.1 19.1 5.5 15.5 5.5 11.5V5.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9.7 12.2L11.2 13.7L14.6 10.3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Modifier mon mot de passe</h2>
                        <p>Choisis un mot de passe sécurisé pour protéger ton compte.</p>
                    </div>
                </div>

                <?php if (!empty($error)) : ?>
                    <div class="sa-account-alert sa-account-alert-error">
                        <?php echo esc_html($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)) : ?>
                    <div class="sa-account-alert sa-account-alert-success">
                        <?php echo esc_html($success); ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="sa-password-form">
                    <?php wp_nonce_field('sa_change_password_action', 'sa_change_password_nonce'); ?>

                    <div class="sa-password-field">
                        <label for="sa_current_password">Mot de passe actuel</label>
                        <div class="sa-password-input-wrap">
                            <span class="sa-password-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="password" id="sa_current_password" name="current_password" placeholder="Saisis ton mot de passe actuel" required>
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

                    <div class="sa-password-field">
                        <label for="sa_new_password">Nouveau mot de passe</label>
                        <div class="sa-password-input-wrap">
                            <span class="sa-password-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="password" id="sa_new_password" name="new_password" placeholder="Saisis ton nouveau mot de passe" required>
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
                        <label for="sa_confirm_password">Confirmer le nouveau mot de passe</label>
                        <div class="sa-password-input-wrap">
                            <span class="sa-password-input-icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="5.5" y="10" width="13" height="9" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M8.5 10V8.2C8.5 6.01 10.29 4.25 12.5 4.25C14.71 4.25 16.5 6.01 16.5 8.2V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <input type="password" id="sa_confirm_password" name="confirm_password" placeholder="Confirme ton nouveau mot de passe" required>
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

                    <div class="sa-form-actions">
                        <a href="<?php echo esc_url(sa_account_dashboard_url()); ?>" class="sa-btn sa-btn-secondary">
                            Retour à mon compte
                        </a>
                        <button type="submit" class="sa-btn sa-btn-primary">
                            Enregistrer mon mot de passe
                        </button>
                    </div>
                </form>
            </article>
        </main>
    </div>
</div>