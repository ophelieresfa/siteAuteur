<?php
if (!defined('ABSPATH')) exit;

add_shortcode('siteauteur_register_logged_in', 'siteauteur_register_logged_in_shortcode');
add_shortcode('siteauteur_register_page', 'siteauteur_register_page_shortcode');

function siteauteur_register_logged_in_shortcode() {
    if (!is_user_logged_in()) {
        return '';
    }

    $dashboard_url = home_url('/account/');
    $demo_url      = home_url('/demo-auteur-thriller/');
    $tarif_url     = home_url('/tarif/');
    $emails_url    = home_url('/account/');
    $faq_url       = home_url('/faq/');

    ob_start();
    ?>
    <section class="sa-logged-register">
        <div class="sa-logged-register__hero">
            <h1>Vous êtes déjà inscrit(e) et connecté(e).</h1>

            <a class="sa-btn sa-btn--primary sa-logged-register__maincta" href="<?php echo esc_url($dashboard_url); ?>">
                Accéder à mon tableau de bord
            </a>
        </div>

        <div class="sa-logged-register__grid">
            <article class="sa-logged-card">
                <div class="sa-logged-card__icon"><img class="img-pencil" src="/wp-content/uploads/2026/03/pencil_7174929.png" alt=""></div>
                <h3>Personnaliser son site</h3>
                <p>Accède à ton tableau de bord et modifie ton site facilement.</p>
                <a class="sa-btn sa-btn--primary" href="<?php echo esc_url($dashboard_url); ?>">
                    Tableau de bord
                </a>
            </article>

            <article class="sa-logged-card">
                <div class="sa-logged-card__icon"><img class="img-book" src="/wp-content/uploads/2026/03/book_13969946.png" alt=""></div>
                <h3>Voir la démo</h3>
                <p>Visualise une démo d’un site officiel d’auteur en ligne.</p>
                <a class="sa-btn sa-btn--secondary" href="<?php echo esc_url($demo_url); ?>">
                    Voir la démo
                </a>
            </article>

            <article class="sa-logged-card">
                <div class="sa-logged-card__icon"><img class="img-money" src="/wp-content/uploads/2026/03/money_14774658.png" alt=""></div>
                <h3>Voir le tarif</h3>
                <p>Découvre le tarif proposé pour utiliser SiteAuteur.</p>
                <a class="sa-btn sa-btn--secondary" href="<?php echo esc_url($tarif_url); ?>">
                    Tarif
                </a>
            </article>

            <article class="sa-logged-card">
                <div class="sa-logged-card__icon"><img class="img-enveloppe" src="/wp-content/uploads/2026/03/open-envelope_16275118.png" alt=""></div>
                <h3>Gérer sa mailing list</h3>
                <p>Récupère les emails de tes lecteurs et gère ta liste de diffusion.</p>
                <a class="sa-btn sa-btn--secondary" href="<?php echo esc_url($emails_url); ?>">
                    Gérer mes emails
                </a>
            </article>
        </div>

        <div class="sa-logged-register__bottom">
            <a class="sa-btn sa-btn--primary" href="<?php echo esc_url($faq_url); ?>">
                Consulter la FAQ
            </a>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function siteauteur_register_page_shortcode() {
    if (is_user_logged_in()) {
        return siteauteur_register_logged_in_shortcode();
    }

    return do_shortcode('[ultimatemember form_id="1074"]');
}