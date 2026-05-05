<?php
if (!defined('ABSPATH')) exit;

get_header();

$checkout_url = home_url('/recapitulatif-commande/');
?>

<main class="sa-tarif-page">

    <section class="sa-tarif-hero">
        <div class="sa-tarif-container sa-tarif-hero-grid">

            <div class="sa-tarif-hero-content">

                <div class="sa-tarif-badge">
                    <span>Nouvelle offre</span>
                    Création incluse
                </div>

                <h1>
                    Ton site officiel d’auteur pour
                    <strong>39€/mois</strong>
                </h1>

                <p class="sa-tarif-hero-text">
                    Un site professionnel clé en main pour présenter ton livre,
                    récupérer les emails de tes lecteurs et développer ton audience —
                    sans frais de création, sans engagement.
                </p>

                <div class="sa-tarif-pills">
                    <div class="sa-tarif-pill">
                        <span class="sa-tarif-pill-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <span>Création incluse</span>
                    </div>

                    <div class="sa-tarif-pill">
                        <span class="sa-tarif-pill-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20.59 13.41L12 22L2 12V2H12L20.59 10.59C21.37 11.37 21.37 12.63 20.59 13.41Z" />
                                <path d="M7 7H7.01" />
                            </svg>
                        </span>
                        <span>0€ de frais de création</span>
                    </div>

                    <div class="sa-tarif-pill">
                        <span class="sa-tarif-pill-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7V12L15 14" />
                            </svg>
                        </span>
                        <span>Site livré sous 48h</span>
                    </div>
                </div>

                <div class="sa-tarif-info-box">
                    <div class="sa-tarif-info-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" />
                            <path d="M12 7V12L15 14" />
                        </svg>
                    </div>

                    <p>
                        <strong>La création démarre uniquement</strong> après réception de tous les éléments
                        de ton formulaire. Ton site est livré sous 48h après formulaire complet.
                    </p>
                </div>

            </div>

            <aside class="sa-tarif-summary-card">

                <div class="sa-tarif-summary-badge">
                    Offre unique
                </div>

                <h2>Site officiel d’auteur</h2>

                <div class="sa-tarif-main-price">
                    <span>39€</span>
                    <small>/mois</small>
                </div>

                <p class="sa-tarif-price-note">
                    Création incluse · Sans frais de création · Sans engagement
                </p>

                <div class="sa-tarif-summary-lines">

                    <div class="sa-tarif-summary-line">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                            Création du site
                        </span>
                        <strong>Incluse</strong>
                    </div>

                    <div class="sa-tarif-summary-line">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20.59 13.41L12 22L2 12V2H12L20.59 10.59C21.37 11.37 21.37 12.63 20.59 13.41Z" />
                                <path d="M7 7H7.01" />
                            </svg>
                            Frais de création
                        </span>
                        <strong>0€</strong>
                    </div>

                    <div class="sa-tarif-summary-line">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" />
                                <path d="M12 7V12L15 14" />
                            </svg>
                            Livraison
                        </span>
                        <strong>48h</strong>
                    </div>

                    <div class="sa-tarif-summary-line">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" />
                                <path d="M9 12L11 14L15 10" />
                            </svg>
                            Engagement
                        </span>
                        <strong>Aucun</strong>
                    </div>

                </div>

                <div class="sa-tarif-total">
                    <span>Total aujourd’hui</span>
                    <strong>39€</strong>
                </div>

                <p class="sa-tarif-renewal">
                    Puis 39€/mois. Résiliable à tout moment.
                </p>

                <a href="<?php echo esc_url($checkout_url); ?>" class="sa-tarif-btn">
                    Créer mon site
                </a>
                <div class="cart-reassure">
                    <img src="/wp-content/uploads/2026/04/149772.png" alt="">
                    <p class="sa-tarif-secure">
                        Paiement sécurisé · Offre sans frais cachés
                    </p>
                </div>  

            </aside>

        </div>
    </section>

    <section class="sa-tarif-included-section">
        <div class="sa-tarif-container">

            <div class="sa-tarif-section-title">
                <h2>Tout est inclus dans ton abonnement</h2>
                <p>
                    SiteAuteur s’occupe de la création, de l’hébergement et de la maintenance.
                    Tu remplis le formulaire, nous créons ton site.
                </p>
            </div>

            <div class="sa-tarif-features-grid">

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M12 17.5C12 15.567 13.567 14 15.5 14H27C29.7614 14 32 16.2386 32 19V50C32 47.2386 29.7614 45 27 45H15.5C13.567 45 12 43.433 12 41.5V17.5Z" />
                            <path d="M52 17.5C52 15.567 50.433 14 48.5 14H37C34.2386 14 32 16.2386 32 19V50C32 47.2386 34.2386 45 37 45H48.5C50.433 45 52 43.433 52 41.5V17.5Z" />
                            <path d="M32 19V50" />
                        </svg>
                    </div>
                    <h3>Page dédiée au livre</h3>
                    <p>Une page claire pour présenter ton livre, sa couverture, son résumé et ses liens d’achat.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M12 20L32 34L52 20" />
                            <path d="M14 18H50C52.2091 18 54 19.7909 54 22V44C54 46.2091 52.2091 48 50 48H14C11.7909 48 10 46.2091 10 44V22C10 19.7909 11.7909 18 14 18Z" />
                        </svg>
                    </div>
                    <h3>Capture d’emails</h3>
                    <p>Un formulaire pour récupérer les emails de tes lecteurs et créer ta propre audience.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M32 33C38.0751 33 43 28.0751 43 22C43 15.9249 38.0751 11 32 11C25.9249 11 21 15.9249 21 22C21 28.0751 25.9249 33 32 33Z" />
                            <path d="M14 53C16.2 43.8 23.1 39 32 39C40.9 39 47.8 43.8 50 53" />
                        </svg>
                    </div>
                    <h3>Page auteur</h3>
                    <p>Une présentation professionnelle de ton univers, de ton parcours et de ton image d’auteur.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M18 18H46C48.2091 18 50 19.7909 50 22V42C50 44.2091 48.2091 46 46 46H18C15.7909 46 14 44.2091 14 42V22C14 19.7909 15.7909 18 18 18Z" />
                            <path d="M22 26H42" />
                            <path d="M22 34H36" />
                        </svg>
                    </div>
                    <h3>Formulaire de contact</h3>
                    <p>Permets aux lecteurs, blogueurs, journalistes ou partenaires de te contacter facilement.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M18 42C14.6863 42 12 39.3137 12 36C12 32.9314 14.304 30.4013 17.272 30.0456C18.5864 23.7313 24.1792 19 30.875 19C36.0286 19 40.5281 21.7965 42.9374 25.9543C47.9876 26.4261 52 30.6844 52 35.875C52 41.3942 47.5192 46 42 46H20" />
                            <path d="M27 38L32 33L37 38" />
                            <path d="M32 33V49" />
                        </svg>
                    </div>
                    <h3>Hébergement inclus</h3>
                    <p>Ton site est hébergé et accessible en ligne, sans configuration technique de ta part.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M32 12V18" />
                            <path d="M32 46V52" />
                            <path d="M17.86 17.86L22.1 22.1" />
                            <path d="M41.9 41.9L46.14 46.14" />
                            <path d="M12 32H18" />
                            <path d="M46 32H52" />
                            <path d="M17.86 46.14L22.1 41.9" />
                            <path d="M41.9 22.1L46.14 17.86" />
                            <circle cx="32" cy="32" r="9" />
                        </svg>
                    </div>
                    <h3>Maintenance incluse</h3>
                    <p>Nous assurons les mises à jour, la sécurité et le bon fonctionnement de ton site.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M43 13L51 21L25 47L15 50L18 40L43 13Z" />
                            <path d="M38 18L46 26" />
                        </svg>
                    </div>
                    <h3>5 modifications / mois</h3>
                    <p>Tu peux demander jusqu’à 5 modifications par mois incluses dans ton abonnement.</p>
                </article>

                <article class="sa-tarif-feature-card">
                    <div class="sa-tarif-feature-icon">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M35 6L16 35H30L25 58L48 25H34L35 6Z" />
                        </svg>
                    </div>
                    <h3>Performance optimale</h3>
                    <p>Ton site est rapide, sécurisé et optimisé pour une expérience lecteur fluide.</p>
                </article>

            </div>

        </div>
    </section>

    <section class="sa-tarif-process-section">
        <div class="sa-tarif-container">

            <div class="sa-tarif-section-title">
                <h2>Comment ça fonctionne ?</h2>
                <p>Un processus simple, rapide et sans compétence technique.</p>
            </div>

            <div class="sa-tarif-process-line">

                <article class="sa-tarif-process-step">
                    <div class="sa-tarif-process">
                        <div class="sa-tarif-process-number">1</div>
                        <div class="sa-tarif-process-icon">
                            <img src="/wp-content/uploads/2026/04/shopping-cart.png" alt="">
                        </div>
                    </div>
                    <h3>Tu commandes</h3>
                    <p>Tu valides ton abonnement à 39€/mois en quelques clics.</p>
                </article>

                <article class="sa-tarif-process-step">
                    <div class="sa-tarif-process">
                        <div class="sa-tarif-process-number">2</div>
                        <div class="sa-tarif-process-icon">
                            <img src="/wp-content/uploads/2026/04/contact-form.png" alt="">
                        </div>
                    </div>
                    <h3>Tu remplis le formulaire</h3>
                    <p>Tu nous fournis ton livre, ta bio, tes textes, tes images et tes liens.</p>
                </article>

                <article class="sa-tarif-process-step">
                    <div class="sa-tarif-process">
                        <div class="sa-tarif-process-number">3</div>
                        <div class="sa-tarif-process-icon">
                            <img src="/wp-content/uploads/2026/04/hammer.png" alt="">
                        </div>
                    </div>
                    <h3>Nous créons ton site</h3>
                    <p>La création démarre après réception de tous les éléments nécessaires.</p>
                </article>

                <article class="sa-tarif-process-step">
                    <div class="sa-tarif-process">
                        <div class="sa-tarif-process-number">4</div>
                        <div class="sa-tarif-process-icon">
                            <img src="/wp-content/uploads/2026/04/delivery.png" alt="">
                        </div>
                    </div>
                    <h3>Ton site est livré sous 48h</h3>
                    <p>Ton site officiel est mis en ligne sous 48h après formulaire complet.</p>
                </article>

            </div>

        </div>
    </section>

    <section class="sa-tarif-faq-section">
        <div class="sa-tarif-container">

            <div class="sa-tarif-section-title">
                <h2>Questions fréquentes</h2>
            </div>

            <div class="sa-tarif-faq-list">

                <details>
                    <summary>Y a-t-il des frais de création ?</summary>
                    <p>Non. La création du site est incluse dans l’abonnement mensuel de 39€.</p>
                </details>

                <details>
                    <summary>Le site est-il vraiment livré sous 48h ?</summary>
                    <p>Oui. Le délai de 48h commence après réception de ton formulaire complet avec tous les éléments nécessaires.</p>
                </details>

                <details>
                    <summary>Quand commence le délai de 48h ?</summary>
                    <p>Le délai démarre uniquement lorsque ton formulaire est complet : couverture, résumé, bio, images, liens et contenus demandés.</p>
                </details>

                <details>
                    <summary>L’abonnement est-il sans engagement ?</summary>
                    <p>Oui. L’abonnement est sans engagement et peut être annulé à tout moment selon les conditions prévues.</p>
                </details>

                <details>
                    <summary>Que comprend l’abonnement à 39€/mois ?</summary>
                    <p>Il comprend la création du site, l’hébergement, la maintenance, la page livre, la page auteur, la capture d’emails, le formulaire de contact et 5 modifications par mois.</p>
                </details>

                <details>
                    <summary>Dois-je avoir des compétences techniques ?</summary>
                    <p>Non. Tu remplis simplement le formulaire et SiteAuteur s’occupe de la création technique du site.</p>
                </details>

            </div>

        </div>
    </section>

</main>

<?php
get_footer();