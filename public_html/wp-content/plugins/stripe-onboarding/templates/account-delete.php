<?php if (!defined('ABSPATH')) exit; ?>

<div class="sa-account-premium sa-delete-page">
    <section class="sa-account-hero sa-password-hero">
        <div class="sa-account-hero-left">
            <span class="sa-account-kicker">ESPACE CLIENT</span>
            <h1>Supprimer mon compte</h1>
            <p class="sa-account-hero-text">
                Cette action est définitive et irréversible.
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
            <article class="sa-password-main-card sa-delete-main-card">
                <div class="sa-password-card-header">
                    <div class="sa-password-card-icon sa-delete-card-icon">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 4.5H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M5 7H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M7 7L7.6 17.2C7.68 18.45 8.72 19.42 9.98 19.42H14.02C15.28 19.42 16.32 18.45 16.4 17.2L17 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10 10.2V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M14 10.2V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <h2>Supprimer mon compte</h2>
                        <p>Ton compte, tes données personnelles et ton accès à l’espace client seront supprimés définitivement.</p>
                    </div>
                </div>

                <?php if (!empty($error) && $error === 'confirm') : ?>
                    <div class="sa-account-alert sa-account-alert-error">
                        Tu dois confirmer la suppression avant de continuer.
                    </div>
                <?php endif; ?>

                <div class="sa-delete-warning-box">
                    <strong>Attention :</strong> cette action est irréversible. Une fois le compte supprimé, il ne pourra pas être restauré.
                </div>

                <div class="sa-delete-info-list">
                    <div class="sa-delete-info-item">
                        <span class="sa-delete-info-bullet"></span>
                        <span>Suppression de ton accès à l’espace client</span>
                    </div>
                    <div class="sa-delete-info-item">
                        <span class="sa-delete-info-bullet"></span>
                        <span>Suppression des données liées à ton compte</span>
                    </div>
                    <div class="sa-delete-info-item">
                        <span class="sa-delete-info-bullet"></span>
                        <span>Action impossible à annuler après validation</span>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="sa-delete-account-form" id="sa-delete-account-form">
                    <input type="hidden" name="action" value="sa_delete_account">
                    <?php wp_nonce_field('sa_delete_account_action', 'sa_delete_account_nonce'); ?>

                    <label class="sa-delete-checkbox">
                        <input type="checkbox" name="confirm_delete" value="1" required>
                        <span>Je confirme vouloir supprimer définitivement mon compte.</span>
                    </label>

                    <div class="sa-form-actions">
                        <a href="<?php echo esc_url(sa_account_dashboard_url()); ?>" class="sa-btn sa-btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="sa-btn sa-btn-danger">
                            Supprimer définitivement mon compte
                        </button>
                    </div>
                </form>
            </article>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sa-delete-account-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const ok = window.confirm('Es-tu sûr(e) de vouloir supprimer définitivement ton compte ? Cette action est irréversible.');
        if (!ok) {
            e.preventDefault();
        }
    });
});
</script>