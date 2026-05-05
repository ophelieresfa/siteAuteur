<?php
if (!defined('ABSPATH')) exit;

get_header();

function sa_register_get_um_register_form_id() {
    if (function_exists('siteauteur_site_register_form_id')) {
        return (int) siteauteur_site_register_form_id();
    }

    $forms = get_posts([
        'post_type'      => 'um_form',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => [
            [
                'key'   => '_um_mode',
                'value' => 'register',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($forms[0])) {
        return (int) $forms[0];
    }

    return 1074;
}

$register_form_id = sa_register_get_um_register_form_id();
?>

<main class="sa-register-page">

    <section class="sa-register-hero">
        <div class="sa-register-container sa-register-grid">

            <div class="sa-register-left">

                <div class="sa-register-badge">
                    <span class="sa-register-badge-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3L19 6.5V11.5C19 16.2 16.1 19.5 12 21C7.9 19.5 5 16.2 5 11.5V6.5L12 3Z" />
                            <path d="M9.5 12L11.3 13.8L15 10" />
                        </svg>
                    </span>
                    Inscription sécurisée
                </div>

                <h1>Crée ton espace auteur</h1>

                <p class="sa-register-intro">
                    Crée ton compte en quelques secondes et commence immédiatement
                    la création de ton site d’auteur professionnel.
                </p>

                <div class="sa-register-checklist">

                    <div class="sa-register-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Inscription simple et rapide</p>
                    </div>

                    <div class="sa-register-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Protection des données garantie</p>
                    </div>

                    <div class="sa-register-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Accès immédiat à ton espace auteur</p>
                    </div>

                    <div class="sa-register-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Suivi de ton projet depuis ton tableau de bord</p>
                    </div>

                    <div class="sa-register-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Gestion simple de ton site et de tes contenus</p>
                    </div>

                </div>

            </div>

            <aside class="sa-register-card">

                <div class="sa-register-card-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 21C20 17.7 16.4 15 12 15C7.6 15 4 17.7 4 21" />
                        <circle cx="12" cy="8" r="4" />
                        <path d="M19 8V14" />
                        <path d="M16 11H22" />
                    </svg>
                </div>

                <div class="sa-register-card-title">
                    <h2>Inscris-toi maintenant</h2>
                    <p>
                        Remplis les informations ci-dessous pour créer ton compte
                        personnel et accéder à ton tableau de bord.
                    </p>
                </div>

                <div class="sa-register-form">
                    <?php echo do_shortcode('[ultimatemember form_id="' . (int) $register_form_id . '"]'); ?>
                </div>

            </aside>

        </div>

        <div class="sa-register-container">
            <div class="sa-register-reassurance">

                <div class="sa-register-reassurance-item">
                    <div class="sa-register-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2" />
                            <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" />
                        </svg>
                    </div>
                    <div>
                        <h3>Données protégées</h3>
                        <p>Tes informations personnelles restent sécurisées.</p>
                    </div>
                </div>

                <div class="sa-register-reassurance-item">
                    <div class="sa-register-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13" />
                            <path d="M5 13H8V19H5C4.4 19 4 18.6 4 18V14C4 13.4 4.4 13 5 13Z" />
                            <path d="M19 13H16V19H19C19.6 19 20 18.6 20 18V14C20 13.4 19.6 13 19 13Z" />
                            <path d="M16 19C16 20.1 14.2 21 12 21" />
                        </svg>
                    </div>
                    <div>
                        <h3>Accompagnement</h3>
                        <p>Tu peux demander de l’aide si tu bloques.</p>
                    </div>
                </div>

                <div class="sa-register-reassurance-item">
                    <div class="sa-register-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13 2L4 14H11L9 22L20 10H13L13 2Z" />
                        </svg>
                    </div>
                    <div>
                        <h3>Accès rapide</h3>
                        <p>Ton espace auteur est prêt en quelques secondes.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php
get_footer();