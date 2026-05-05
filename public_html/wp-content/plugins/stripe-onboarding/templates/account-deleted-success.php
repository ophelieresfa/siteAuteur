<?php if (!defined('ABSPATH')) exit; ?>

<section class="sa-account-deleted-page">
    <div class="sa-account-deleted-box">
        <div class="sa-account-deleted-icon">
            <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="32" r="32" fill="#EAF8F3"/>
                <path d="M21 33.5L28.2 40.7L44 24.9" stroke="#41C59A" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <span class="sa-account-deleted-kicker">COMPTE SUPPRIMÉ</span>

        <h1>Ton compte a bien été supprimé</h1>

        <p class="sa-account-deleted-text">
            Ton compte et les données associées ont été supprimés définitivement.
        </p>

        <div class="sa-account-deleted-actions">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="sa-account-deleted-btn sa-account-deleted-btn-primary">
                Retour à l’accueil
            </a>

            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="sa-account-deleted-btn sa-account-deleted-btn-secondary">
                Contacter le support
            </a>
        </div>
    </div>
</section>