<?php
/**
 * Template 404 personnalisé SiteAuteur
 */

if (!defined('ABSPATH')) exit;

get_header();

$image_404_url = '/wp-content/uploads/2026/04/image_sans_fond_quadrille.png';
?>

<main id="primary" class="site-main sa-404-page">

    <section class="sa-404-section">

        <div class="sa-404-card">

            <div class="sa-404-content">

                <div class="sa-404-left">

                    <div class="sa-404-eyebrow">
                        <span>404</span>
                    </div>

                    <h1>Oups, cette page<br>est introuvable</h1>

                    <p class="sa-404-text">
                        Le lien que vous avez suivi semble incorrect ou la page a peut-être été déplacée.
                        Pas d’inquiétude, vous pouvez revenir à l’accueil ou nous contacter.
                    </p>

                    <div class="sa-404-actions">

                        <a class="sa-404-btn sa-404-btn-primary" href="<?php echo esc_url(home_url('/')); ?>">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M3 10.8L12 3L21 10.8V21H15.5V14.5H8.5V21H3V10.8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                            Retour à l’accueil
                        </a>

                        <a class="sa-404-btn sa-404-btn-outline" href="<?php echo esc_url(home_url('/contact/')); ?>">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Nous contacter
                        </a>

                    </div>

                    <div class="sa-404-useful-links">
                        <span>Pages utiles :</span>
                        <a href="<?php echo esc_url(home_url('/tarif/')); ?>">Tarifs</a>
                        <b>·</b>
                        <a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a>
                        <b>·</b>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
                    </div>

                </div>

                <div class="sa-404-right">
                    <img src="<?php echo esc_url($image_404_url); ?>" alt="Page introuvable">
                </div>

            </div>

        </div>

    </section>

</main>

<?php
get_footer();