<?php if (!defined('ABSPATH')) exit; ?>

<?php
$is_unavailable = !empty($is_unavailable);

$account_url      = home_url('/account/');
$subscription_url = home_url('/mon-abonnement/');
$contact_url      = home_url('/contact/');

if ($is_unavailable) :
?>

<div class="sa-modification-unavailable-page">

    <section class="sa-modification-unavailable-hero">

        <div class="sa-modification-unavailable-badge">
            <span></span>
            Site suspendu
        </div>

        <h1>
            Les modifications sont temporairement indisponibles
        </h1>

        <p>
            Cette fonctionnalité est disponible uniquement lorsque votre site est en ligne.
            Tant que votre projet est suspendu, vous ne pouvez pas utiliser vos modifications incluses.
        </p>

    </section>

    <section class="sa-modification-unavailable-card">

        <div class="sa-modification-unavailable-illustration" aria-hidden="true">
            <img src="/wp-content/uploads/2026/05/image_vrai_png_sans_damier.png" alt="">
        </div>

        <div class="sa-modification-unavailable-grid">

            <article>
                <div class="sa-modification-info-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 6.5H20V17.5H4V6.5Z" />
                        <path d="M4 9.5H20" />
                        <path d="M7 8H7.01" />
                        <path d="M10 8H10.01" />
                        <path d="M13 8H13.01" />
                        <circle cx="12" cy="13.5" r="3.5" />
                        <path d="M11 12.2V14.8" />
                        <path d="M13 12.2V14.8" />
                    </svg>
                </div>
                <div>
                    <h2>Site actuellement suspendu</h2>
                    <p>Votre site n’est plus en ligne depuis la suspension.</p>
                </div>
            </article>

            <article>
                <div class="sa-modification-info-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M14.5 5.5L18.5 9.5" />
                        <path d="M6 18L9.8 17.2L17.7 9.3C18.5 8.5 18.5 7.2 17.7 6.4L17.6 6.3C16.8 5.5 15.5 5.5 14.7 6.3L6.8 14.2L6 18Z" />
                        <path d="M4.5 4.5L19.5 19.5" />
                    </svg>
                </div>
                <div>
                    <h2>Modifications indisponibles</h2>
                    <p>Vous ne pouvez pas utiliser vos modifications incluses.</p>
                </div>
            </article>

            <article>
                <div class="sa-modification-info-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M19 11.5C19 15.4 15.9 18.5 12 18.5C8.1 18.5 5 15.4 5 11.5C5 7.6 8.1 4.5 12 4.5C14.3 4.5 16.4 5.6 17.7 7.3" />
                        <path d="M17.7 4.8V7.8H14.7" />
                        <path d="M9.4 11.8L11.2 13.6L14.8 10" />
                    </svg>
                </div>
                <div>
                    <h2>Réactivation nécessaire</h2>
                    <p>Réactivez votre abonnement pour retrouver toutes les fonctionnalités.</p>
                </div>
            </article>

        </div>

    </section>

    <div class="sa-modification-unavailable-actions">
        <a href="<?php echo esc_url($subscription_url); ?>" class="sa-modification-btn sa-modification-btn-primary">
            Voir mon abonnement
        </a>

        <a href="<?php echo esc_url($contact_url); ?>" class="sa-modification-btn sa-modification-btn-secondary">
            Contacter le support
        </a>
    </div>

    <p class="sa-modification-unavailable-note">
        Réactivez votre abonnement pour retrouver l’accès à toutes les fonctionnalités de votre espace client.
    </p>

</div>

<?php
return;
endif;
?>

<?php
$included_limit = function_exists('sa_get_included_modifications_limit')
    ? (int) sa_get_included_modifications_limit()
    : 5;

$user_id = (!empty($order) && !empty($order->wp_user_id)) ? (int) $order->wp_user_id : 0;
$order_id = (!empty($order) && !empty($order->id)) ? (int) $order->id : 0;

$has_unlimited_modifications = false;
if ($user_id > 0 && $order_id > 0 && function_exists('sa_order_has_unlimited_modifications')) {
    $has_unlimited_modifications = sa_order_has_unlimited_modifications($user_id, $order_id);
}

$intro_text = function_exists('sa_get_modification_intro_text')
    ? sa_get_modification_intro_text((int) $remaining, (int) $included_limit, (bool) $has_unlimited_modifications)
    : 'Ton abonnement inclut ' . (int) $included_limit . ' modifications par période.';

$remaining_sentence = function_exists('sa_get_modification_remaining_sentence')
    ? sa_get_modification_remaining_sentence((int) $remaining, (int) $included_limit, (bool) $has_unlimited_modifications)
    : ((int) $remaining . ' / ' . (int) $included_limit . ' restantes');
?>

<div class="sa-modification-page">
    <div class="sa-modification-topbar">
        <div class="sa-modification-topbar-content">
            <h1>Utiliser mes modifications</h1>
            <p><?php echo esc_html($intro_text); ?></p>
        </div>
    </div>

    <div class="sa-modification-status-card">
        <div class="sa-modification-status-left">
            <strong>Modifications disponibles</strong>
        </div>
        <div class="sa-modification-status-right">
            <span class="sa-modification-remaining-value">
                <?php echo esc_html($remaining_sentence); ?>
            </span>
        </div>
    </div>

    <hr>

    <div class="sa-modification-form-card">
        <div class="sa-modification-form-wrap">
            <?php echo do_shortcode('[fluentform id="' . (int) SA_ONBOARDING_FORM_ID . '"]'); ?>
        </div>
    </div>
</div>