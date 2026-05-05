<?php
if (!defined('ABSPATH')) exit;

get_header();

function sa_login_get_um_login_form_id() {
    $forms = get_posts([
        'post_type'      => 'um_form',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'meta_query'     => [
            [
                'key'   => '_um_mode',
                'value' => 'login',
            ],
        ],
        'fields' => 'ids',
    ]);

    if (!empty($forms[0])) {
        return (int) $forms[0];
    }

    return 1075;
}

$login_form_id = sa_login_get_um_login_form_id();
?>

<main class="sa-login-page">

    <section class="sa-login-hero">
        <div class="sa-login-container sa-login-grid">

            <div class="sa-login-left">

                <div class="sa-login-badge">
                    <span class="sa-login-badge-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 3L19 6.5V11.5C19 16.2 16.1 19.5 12 21C7.9 19.5 5 16.2 5 11.5V6.5L12 3Z" />
                            <path d="M9.5 12L11.3 13.8L15 10" />
                        </svg>
                    </span>
                    Espace auteur sécurisé
                </div>

                <h1>Retrouve ton espace auteur</h1>

                <p class="sa-login-intro">
                    Accède à ton tableau de bord, reprends la création de ton site
                    et développe ton audience en toute simplicité.
                </p>

                <div class="sa-login-checklist">

                    <div class="sa-login-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Reprendre la création de ton site</p>
                    </div>

                    <div class="sa-login-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Accéder à ton espace client sécurisé</p>
                    </div>

                    <div class="sa-login-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Modifier ton contenu à tout moment</p>
                    </div>

                    <div class="sa-login-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Suivre l’avancement de ton projet</p>
                    </div>

                    <div class="sa-login-check">
                        <span>
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M20 6L9 17L4 12" />
                            </svg>
                        </span>
                        <p>Gérer tes emails lecteurs</p>
                    </div>

                </div>

            </div>

            <aside class="sa-login-card">

                <div class="sa-login-card-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M20 21C20 17.7 16.4 15 12 15C7.6 15 4 17.7 4 21" />
                        <circle cx="12" cy="8" r="4" />
                    </svg>
                </div>

                <div class="sa-login-card-title">
                    <h2>Connexion à ton compte</h2>
                    <p>
                        Accède à ton tableau de bord et modifie ou reprends
                        la création de ton site.
                    </p>
                </div>

                <div class="sa-login-form">
                    <?php echo do_shortcode('[ultimatemember form_id="' . (int) $login_form_id . '"]'); ?>
                </div>

            </aside>

        </div>

        <div class="sa-login-container">
            <div class="sa-login-reassurance">

                <div class="sa-login-reassurance-item">
                    <div class="sa-login-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <rect x="5" y="10" width="14" height="10" rx="2" />
                            <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" />
                        </svg>
                    </div>
                    <div>
                        <h3>Accès sécurisé</h3>
                        <p>Tes données sont protégées et cryptées.</p>
                    </div>
                </div>

                <div class="sa-login-reassurance-item">
                    <div class="sa-login-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13" />
                            <path d="M5 13H8V19H5C4.4 19 4 18.6 4 18V14C4 13.4 4.4 13 5 13Z" />
                            <path d="M19 13H16V19H19C19.6 19 20 18.6 20 18V14C20 13.4 19.6 13 19 13Z" />
                            <path d="M16 19C16 20.1 14.2 21 12 21" />
                        </svg>
                    </div>
                    <div>
                        <h3>Support réactif</h3>
                        <p>Notre équipe est disponible pour t’aider rapidement.</p>
                    </div>
                </div>

                <div class="sa-login-reassurance-item">
                    <div class="sa-login-reassurance-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M13 2L4 14H11L9 22L20 10H13L13 2Z" />
                        </svg>
                    </div>
                    <div>
                        <h3>Aide rapide</h3>
                        <p>Retrouve tes projets et avance sans perdre de temps.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

</main>

<?php
get_footer();