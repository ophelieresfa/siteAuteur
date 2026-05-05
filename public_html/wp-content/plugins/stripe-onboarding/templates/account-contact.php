<?php if (!defined('ABSPATH')) exit; ?>

<div class="sa-account-premium sa-contact-page">
    <span class="sa-account-kicker">Espace client</span>

    <section class="sa-contact-hero">
        <div class="sa-contact-hero-left">
            <h1>Contactez-nous</h1>
            <p class="sa-contact-hero-text">
                Une question, besoin d’aide ? Écris-nous, notre équipe te répond sous 24h en moyenne.
            </p>
        </div>
    </section>

    <div class="sa-contact-body">
        <?php echo sa_render_template('partials/account-sidebar'); ?>

        <main class="sa-contact-main">
            <article class="sa-contact-card">
                <?php echo $content; ?>
            </article>
        </main>
    </div>
</div>