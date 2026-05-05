<?php
if (!defined('ABSPATH')) exit;

get_header();

$hero_image = '/wp-content/uploads/2026/03/ChatGPT-Image-5-mars-2026-14_47_01.png';

$demo_1 = '/wp-content/uploads/2026/03/Screenshot-2026-03-19-at-11-59-06-Page-accueil-–-demo-thriller-–-SiteAuteur.png';
$demo_2 = '/wp-content/uploads/2026/03/Screenshot-2026-03-19-at-12-05-02-Page-accueil-–-demo-thriller-–-SiteAuteur.png';
$demo_3 = '/wp-content/uploads/2026/03/Screenshot-2026-03-19-at-12-00-14-Tous-les-romans-–-demo-thriller-–-SiteAuteur.png';
?>

<main class="sa-home">

    <section class="sa-home-hero">
        <div class="sa-home-container sa-home-hero-grid">

            <div class="sa-home-hero-content">
                <div class="sa-home-badge">
                    <span>Nouvelle offre</span>
                    Création incluse
                </div>

                <h1>Crée ton site officiel d’auteur en <strong>48h</strong></h1>

                <p class="sa-home-hero-text">
                    Un site professionnel pour présenter ton livre, récupérer les emails de tes lecteurs
                    et développer ton audience — sans gérer la technique.
                </p>

                <div class="sa-home-price-box">
                    <div class="sa-home-price">
                        <span>39€</span>
                        <small>/mois</small>
                    </div>

                    <ul>
                        <li>Création incluse</li>
                        <li>Sans frais de création</li>
                        <li>Sans engagement</li>
                    </ul>
                </div>

                <div class="sa-home-actions">
                    <a href="<?php echo esc_url(home_url('/recapitulatif-commande/')); ?>" class="sa-home-btn sa-home-btn-primary">
                        Créer mon site d’auteur
                    </a>

                    <a target="_blank" href="<?php echo esc_url(home_url('/demo/')); ?>" class="sa-home-btn sa-home-btn-secondary">
                        Voir une démo
                    </a>
                </div>

                <p class="sa-home-delay">
                    Site livré sous <strong>48h</strong> après réception du formulaire complet.
                </p>
            </div>

            <div class="sa-home-hero-visual">
                <img src="<?php echo esc_url($hero_image); ?>" alt="Exemple de site officiel d’auteur créé avec SiteAuteur">
            </div>

        </div>
    </section>

    <section class="sa-home-problem">
        <div class="sa-home-container">

            <div class="sa-home-section-title">
                <h2>Aujourd’hui, trop d’auteurs dépendent uniquement des plateformes</h2>
                <p>
                    Amazon vend ton livre, mais ne construit pas ton audience.
                    Sans site officiel, tu ne récupères pas les emails de tes lecteurs,
                    tu ne maîtrises pas ton image et tu restes dépendant des algorithmes.
                </p>
            </div>

            <div class="sa-home-three-cols">
                <article class="sa-home-soft-card">
                    <div class="sa-home-icon">
                        <img src="/wp-content/uploads/2026/03/amazon.png" alt="">
                    </div>
                    <h3>Dépendance aux plateformes</h3>
                    <p>Tu dépends des algorithmes pour être visible et vendre tes livres.</p>
                </article>

                <article class="sa-home-soft-card">
                    <div class="sa-home-icon">
                        <img src="/wp-content/uploads/2026/03/vecteezy_icons-business-card-contact-information-symbols-vector_29816448-1024x828.png" alt="">
                    </div>
                    <h3>Aucune liste de lecteurs</h3>
                    <p>Tu ne récupères pas les emails des personnes intéressées par ton univers.</p>
                </article>

                <article class="sa-home-soft-card">
                    <div class="sa-home-icon">
                        <img src="/wp-content/uploads/2026/03/security.png" alt=""> 
                    </div>
                    <h3>Image moins professionnelle</h3>
                    <p>Un site officiel renforce ta crédibilité et ton image d’auteur.</p>
                </article>
            </div>

        </div>
    </section>

    <section class="sa-home-service">
        <div class="sa-home-container">

            <div class="sa-home-section-title">
                <h2><span>SiteAuteur</span> crée ton site officiel d’auteur</h2>
                <p>Un site professionnel pensé pour présenter ton livre, capter tes lecteurs et développer ton audience.</p>
            </div>

            <div class="sa-home-four-cols">
                <article class="sa-home-feature-card">
                    <div class="sa-home-feature-icon">
                        <img src="/wp-content/uploads/2026/04/open-book.png" alt="">
                    </div>
                    <h3>Page dédiée au livre</h3>
                    <p>Présente ton roman avec une page claire, immersive et convaincante.</p>
                </article>

                <article class="sa-home-feature-card">
                    <div class="sa-home-feature-icon">
                        <img src="/wp-content/uploads/2026/04/email.png" alt="">
                    </div>
                    <h3>Capture d’emails lecteurs</h3>
                    <p>Crée une relation directe avec tes lecteurs grâce à une inscription simple.</p>
                </article>

                <article class="sa-home-feature-card">
                    <div class="sa-home-feature-icon">
                        <img src="/wp-content/uploads/2026/04/user.png" alt="">
                    </div>
                    <h3>Image d’auteur professionnelle</h3>
                    <p>Une page qui te présente, ton parcours, ton univers et ton actualité.</p>
                </article>

                <article class="sa-home-feature-card">
                    <div class="sa-home-feature-icon">
                        <img src="/wp-content/uploads/2026/04/eclat.png" alt="">
                    </div>
                    <h3>Rapide et sans technique</h3>
                    <p>On s’occupe de tout. Tu reçois ton site prêt à l’emploi sous 48h.</p>
                </article>
            </div>

        </div>
    </section>

    <section class="sa-home-demo">
        <div class="sa-home-container">

            <div class="sa-home-section-title">
                <h2>Un site d’auteur moderne, clair et efficace</h2>
                <p>Des pages pensées pour présenter ton univers, convertir tes lecteurs et vendre avec plus de crédibilité.</p>
            </div>

            <div class="sa-home-demo-grid">
                <article>
                    <img src="<?php echo esc_url($demo_1); ?>" alt="Exemple de page livre">
                    <h3>Présentation du roman</h3>
                </article>

                <article>
                    <img src="<?php echo esc_url($demo_2); ?>" alt="Exemple de capture email">
                    <h3>Capture d’emails lecteurs</h3>
                </article>

                <article>
                    <img src="<?php echo esc_url($demo_3); ?>" alt="Exemple de catalogue de romans">
                    <h3>Catalogue de romans</h3>
                </article>
            </div>

            <div class="sa-home-center">
                <a target="_blank" href="<?php echo esc_url(home_url('/demo/')); ?>" class="sa-home-btn sa-home-btn-secondary">
                    Voir une démo complète
                </a>
            </div>

        </div>
    </section>

    <section class="sa-home-process">
        <div class="sa-home-container">

            <div class="sa-home-section-title">
                <h2>Comment ça fonctionne ?</h2>
                <p>Un processus simple, rapide et 100% clé en main.</p>
            </div>

            <div class="sa-home-steps">
                <article>
                    <div class="sa-home-step-icon">
                        <img src="/wp-content/uploads/2026/04/letter.png" alt="">
                    </div>
                    <h3>1. Tu remplis le formulaire</h3>
                    <p>Tu nous envoies les informations de ton livre et de ton univers.</p>
                </article>

                <div class="sa-home-arrow">→</div>

                <article>
                    <div class="sa-home-step-icon">
                        <img src="/wp-content/uploads/2026/04/computer-settings.png" alt="">
                    </div>
                    <h3>2. Nous créons ton site</h3>
                    <p>Nous intégrons ton contenu et configurons ton site à ton image.</p>
                </article>

                <div class="sa-home-arrow">→</div>

                <article>
                    <div class="sa-home-step-icon">
                        <img src="/wp-content/uploads/2026/04/shuttle.png" alt="">
                    </div>
                    <h3>3. Ton site est en ligne</h3>
                    <p>Ton site officiel est prêt en 48h après réception du formulaire complet.</p>
                </article>
            </div>

            <div class="sa-home-center">
                <a href="<?php echo esc_url(home_url('/recapitulatif-commande/')); ?>" class="sa-home-btn sa-home-btn-primary">
                    Créer mon site d’auteur
                </a>

                <p class="sa-home-small-note">
                    Sans engagement. Résiliable à tout moment.
                </p>
            </div>

        </div>
    </section>

    <section class="sa-home-pricing" id="tarif">
        <div class="sa-home-container">

            <div class="sa-home-pricing-card">
                <div class="sa-home-offer-badge">Offre unique</div>

                <h2>Site officiel d’auteur clé en main</h2>

                <div class="sa-home-big-price">
                    <span>39€</span>
                    <small>/mois</small>
                </div>

                <p class="sa-home-pricing-subtitle">
                    Création du site incluse · Sans frais de création · Sans engagement
                </p>

                <div class="sa-home-pricing-list">
                    <ul>
                        <li>Site professionnel</li>
                        <li>Page dédiée au livre</li>
                        <li>Capture d’emails</li>
                        <li>Page auteur</li>
                    </ul>

                    <ul>
                        <li>Formulaire de contact</li>
                        <li>Hébergement sécurisé</li>
                        <li>Maintenance technique</li>
                        <li>5 modifications / mois</li>
                    </ul>
                </div>

                <a href="<?php echo esc_url(home_url('/recapitulatif-commande/')); ?>" class="sa-home-btn sa-home-btn-primary">
                    Créer mon site maintenant
                </a>

                <p class="sa-home-delay sa-home-delay-center">
                    Site livré sous <strong>48h</strong> après réception de ton formulaire complet.
                </p>
            </div>

        </div>
    </section>

    <section class="sa-home-faq" id="faq">
        <div class="sa-home-container">

            <div class="sa-home-section-title">
                <h2>Questions fréquemment posées</h2>
            </div>

            <div class="sa-home-faq-grid">

                <details>
                    <summary>Combien de temps faut-il pour créer un site d’auteur ?</summary>
                    <p>Le site est livré sous 48h après réception de tous les éléments nécessaires via le formulaire.</p>
                </details>

                <details>
                    <summary>Pourquoi un auteur a-t-il besoin d’un site internet ?</summary>
                    <p>Un site officiel permet de présenter son univers, de renforcer sa crédibilité et de construire une relation directe avec ses lecteurs.</p>
                </details>

                <details>
                    <summary>Comment récupérer les emails de ses lecteurs ?</summary>
                    <p>Le site inclut une zone d’inscription permettant aux visiteurs de rejoindre ta liste de lecteurs.</p>
                </details>

                <details>
                    <summary>Combien coûte un site d’auteur ?</summary>
                    <p>L’offre SiteAuteur coûte 39€/mois, création incluse, sans frais de création et sans engagement.</p>
                </details>

                <details>
                    <summary>Peut-on ajouter d’autres livres plus tard ?</summary>
                    <p>Oui, tu peux faire évoluer ton site et ajouter de nouveaux livres selon tes besoins.</p>
                </details>

                <details>
                    <summary>Un site d’auteur est-il important pour vendre ses livres ?</summary>
                    <p>Oui. Il aide à professionnaliser ton image, à centraliser tes liens et à développer ton audience en dehors des plateformes.</p>
                </details>

            </div>

        </div>
    </section>

</main>

<?php
get_footer();