<?php
if (!defined('ABSPATH')) exit;

$is_logged_in = is_user_logged_in();
$current_user = wp_get_current_user();

/**
 * Remplace l'ID si ton formulaire contact n'est pas le 5.
 */
$contact_form_shortcode = '[fluentform id="5"]';

get_header();
?>

<?php if ($is_logged_in) : ?>

    <main class="sa-contact-account-page">

        <div class="sa-contact-account-container">

            <section class="sa-contact-account-hero">
                <div>
                    <span class="sa-contact-account-kicker">Support</span>

                    <h1>
                        Contacter le support
                    </h1>

                    <p>
                        Une question sur ton site, ton abonnement ou ton compte&nbsp;?
                        Écris-nous, notre équipe te répond rapidement.
                    </p>
                </div>
            </section>

            <div class="sa-contact-account-body">

                <?php echo sa_render_template('partials/account-sidebar'); ?>

                <main class="sa-contact-account-main">

                    <section class="sa-contact-account-card">

                        <div class="sa-contact-account-card-header">
                            <div class="sa-contact-account-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 15.5H7a3 3 0 0 1-3-3v-5A3 3 0 0 1 7 4.5h10a3 3 0 0 1 3 3v5a3 3 0 0 1-3 3h-3.8L9 19v-3.5H7.5Z"/>
                                    <path d="M8.5 9h7"/>
                                    <path d="M8.5 12h4.5"/>
                                </svg>
                            </div>

                            <div>
                                <h2>Envoyer un message</h2>
                                <p>Complète le formulaire ci-dessous pour contacter l’équipe SiteAuteur.</p>
                            </div>
                        </div>

                        <div class="sa-contact-form-wrap">
                            <?php echo do_shortcode($contact_form_shortcode); ?>
                        </div>

                    </section>

                </main>

            </div>

        </div>

    </main>

<?php else : ?>

    <main class="sa-contact-page sa-contact-page--guest">

        <section class="sa-contact-hero">
            <div class="sa-contact-container sa-contact-hero-inner">

                <div class="sa-contact-badge">
                    <span class="sa-contact-badge-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M7 8h10M7 12h7M5.5 19.5v-3.2H5a2.5 2.5 0 0 1-2.5-2.5V6A2.5 2.5 0 0 1 5 3.5h14A2.5 2.5 0 0 1 21.5 6v7.8A2.5 2.5 0 0 1 19 16.3h-8.2l-5.3 3.2Z"/>
                        </svg>
                    </span>
                    Contact
                </div>

                <h1>Contactez-nous</h1>

                <p>
                    Une question, besoin d’aide&nbsp;? Écrivez-nous,<br>
                    notre équipe te répond sous 48h en moyenne.
                </p>

            </div>
        </section>

        <section class="sa-contact-main-section">
            <div class="sa-contact-container">

                <div class="sa-contact-grid">

                    <aside class="sa-contact-info">

                        <article class="sa-contact-info-card">
                            <div class="sa-contact-info-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/>
                                    <path d="M12 7v5l3.2 2"/>
                                </svg>
                            </div>
                            <div>
                                <h2>Réponse sous 48h</h2>
                                <p>Notre équipe s’engage à te répondre sous 48h en moyenne.</p>
                            </div>
                        </article>

                        <article class="sa-contact-info-card">
                            <div class="sa-contact-info-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M4 6.5h16v11H4z"/>
                                    <path d="m4 7 8 6 8-6"/>
                                </svg>
                            </div>
                            <div>
                                <h2>E-mail</h2>
                                <a href="mailto:admin@siteauteur.fr">admin@siteauteur.fr</a>
                                <p>Écris-nous directement, nous te répondrons rapidement.</p>
                            </div>
                        </article>

                        <article class="sa-contact-info-card">
                            <div class="sa-contact-info-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M9.3 9a3 3 0 1 1 4.9 2.3c-.9.7-1.7 1.3-1.7 2.7"/>
                                    <path d="M12 18h.01"/>
                                </svg>
                            </div>
                            <div>
                                <h2>FAQ</h2>
                                <p>Consulte notre FAQ : la réponse à ta question s’y trouve peut-être déjà.</p>
                                <a class="sa-contact-text-link" href="<?php echo esc_url(home_url('/faq/')); ?>">
                                    Voir la FAQ <span>→</span>
                                </a>
                            </div>
                        </article>

                        <article class="sa-contact-author-card">
                            <div class="sa-contact-info-icon">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.8 5.6a5.1 5.1 0 0 0-7.2 0L12 7.2l-1.6-1.6a5.1 5.1 0 0 0-7.2 7.2L12 21l8.8-8.2a5.1 5.1 0 0 0 0-7.2Z"/>
                                </svg>
                            </div>

                            <div class="sa-contact-author-content">
                                <h2>Un support dédié<br>aux auteurs</h2>
                                <p>
                                    SiteAuteur accompagne les auteurs dans chaque étape de leur projet.
                                    Tu n’es jamais seul.
                                </p>
                            </div>
                        </article>

                    </aside>

                    <section class="sa-contact-form-card">
                        <div class="sa-contact-form-wrap">
                            <?php echo do_shortcode($contact_form_shortcode); ?>
                        </div>
                    </section>

                </div>

                <section class="sa-contact-cta">
                    <div class="sa-contact-cta-icon">
                        <img src="/wp-content/uploads/2026/05/startup.png" alt="">
                    </div>

                    <div class="sa-contact-cta-text">
                        <h2>Tu préfères démarrer ton site&nbsp;?</h2>
                        <p>Crée ton site d’auteur en quelques minutes et concentre-toi sur l’essentiel&nbsp;: ton écriture.</p>
                    </div>

                    <div class="sa-contact-cta-actions">
                        <a class="sa-contact-btn sa-contact-btn-primary" href="<?php echo esc_url(home_url('/recapitulatif-commande/')); ?>">
                            Créer mon site
                        </a>
                        <a class="sa-contact-btn sa-contact-btn-secondary" href="<?php echo esc_url(home_url('/faq/')); ?>">
                            Voir la FAQ
                        </a>
                    </div>
                </section>

            </div>
        </section>

    </main>

<?php endif; ?>

<?php get_footer(); ?>