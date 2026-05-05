<?php if (!defined('ABSPATH')) exit; ?>

<?php
$account_url          = home_url('/account/');
$use_modification_url = home_url('/utiliser-ma-modification/');
?>

<div class="sa-thanks-modification-page">

    <section class="sa-thanks-modification-section">
        <div class="sa-thanks-modification-container">

            <div class="sa-thanks-modification-card">

                <div class="sa-thanks-decoration sa-thanks-decoration-left"></div>
                <div class="sa-thanks-decoration sa-thanks-decoration-right"></div>
                <div class="sa-thanks-decoration sa-thanks-decoration-bottom"></div>

                <div class="sa-thanks-check-wrap">
                    <div class="sa-thanks-check">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6.5 12.5L10.3 16.3L17.8 8.7" />
                        </svg>
                    </div>
                </div>

                <h1>
                    Votre modification supplémentaire
                    <span>a bien été enregistrée&nbsp;!</span>
                </h1>

                <div class="sa-thanks-separator"></div>

                <p>
                    Vous pouvez maintenant utiliser votre modification depuis votre espace client.
                </p>

                <div class="sa-thanks-actions">
                    <a href="<?php echo esc_url($use_modification_url); ?>" class="sa-thanks-btn sa-thanks-btn-primary">
                        <span class="sa-thanks-btn-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M14.5 5.5L18.5 9.5" />
                                <path d="M6 18L9.8 17.2L17.7 9.3C18.5 8.5 18.5 7.2 17.7 6.4L17.6 6.3C16.8 5.5 15.5 5.5 14.7 6.3L6.8 14.2L6 18Z" />
                            </svg>
                        </span>
                        Utiliser ma modification
                    </a>

                    <a href="<?php echo esc_url($account_url); ?>" class="sa-thanks-btn sa-thanks-btn-secondary">
                        Retour à mon espace client
                    </a>
                </div>

            </div>

        </div>
    </section>

</div>