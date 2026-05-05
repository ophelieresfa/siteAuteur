<?php if (!defined('ABSPATH')) exit; ?>

<?php
$current_user = wp_get_current_user();
$user_name    = $current_user->first_name ?: $current_user->display_name;

$order_id = !empty($order->id) ? (int) $order->id : 0;

$order_state = 'site_live';

if (!empty($user_id) && !empty($order_id) && function_exists('sa_get_order_state')) {
    $order_state = sa_get_order_state((int) $user_id, (int) $order_id);
}

/**
 * =========================
 * ABONNEMENT STRIPE
 * =========================
 */

$subscription = false;

if (!empty($order->stripe_subscription_id) && function_exists('sa_get_stripe_subscription')) {
    $subscription = sa_get_stripe_subscription(sanitize_text_field($order->stripe_subscription_id));
}

$subscription_status = '';

if (is_array($subscription) && !empty($subscription['status'])) {
    $subscription_status = sanitize_text_field($subscription['status']);
}

$cancel_at_period_end = is_array($subscription) && !empty($subscription['cancel_at_period_end']);

$is_subscription_stopped = in_array(
    $subscription_status,
    ['canceled', 'unpaid', 'paused', 'incomplete_expired'],
    true
);

/*
 * Important :
 * - Résiliation programmée = site encore en ligne.
 * - Projet suspendu = seulement si l’abonnement est réellement arrêté.
 */
$is_cancel_scheduled = (!$is_subscription_stopped && $cancel_at_period_end);

$is_paused = $is_subscription_stopped || ($order_state === 'paused' && !$is_cancel_scheduled);

/**
 * Date de fin de période Stripe.
 */
$period_end_ts = 0;

if (is_array($subscription) && function_exists('sa_get_stripe_subscription_period_end')) {
    $period_end_ts = sa_get_stripe_subscription_period_end($subscription);
}

$display_period_end = $period_end_ts ? date_i18n('d/m/Y', $period_end_ts) : '';

/**
 * Date d’affichage du projet.
 */
$display_date = date_i18n('d/m/Y');

if (!empty($order) && function_exists('sa_get_order_display_date')) {
    $display_date = sa_get_order_display_date($order);
}

/**
 * =========================
 * MODIFICATIONS
 * =========================
 */

$included_limit = function_exists('sa_get_included_modifications_limit')
    ? (int) sa_get_included_modifications_limit()
    : 5;

$remaining_modifications      = 0;
$has_unlimited_modifications  = false;
$modification_stock_label     = sa_get_modification_stock_label(0, $included_limit, false);
$modification_action_label    = sa_get_modification_action_label(0, $included_limit, false);
$unlimited_days_left          = 0;

if (!empty($order) && !empty($order->wp_user_id) && !empty($order->id)) {
    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    $remaining_modifications = sa_get_order_modifications_remaining($user_id, $order_id);

    $has_unlimited_modifications = sa_order_has_unlimited_modifications($user_id, $order_id);

    $modification_stock_label = sa_get_modification_stock_label(
        $remaining_modifications,
        $included_limit,
        $has_unlimited_modifications
    );

    $modification_action_label = sa_get_modification_action_label(
        $remaining_modifications,
        $included_limit,
        $has_unlimited_modifications
    );

    if ($has_unlimited_modifications && function_exists('sa_get_unlimited_modifications_days_left')) {
        $unlimited_days_left = (int) sa_get_unlimited_modifications_days_left($user_id, $order_id);

        $modification_stock_label = 'Modification : illimité pendant 30 jours';

        if ($unlimited_days_left > 0) {
            $modification_stock_label .= ' (' . $unlimited_days_left . ' jours restants)';
        }
    }
}

$modification_url      = !empty($order) ? sa_get_modification_page_url($order) : '#';
$has_modification_left = ($has_unlimited_modifications || ((int) $remaining_modifications > 0));

/**
 * =========================
 * BADGES
 * =========================
 */

$main_badge_class = '';

if ($is_paused) {
    $main_badge_class = 'sa-live-badge--paused';
} elseif ($is_cancel_scheduled) {
    $main_badge_class = 'sa-live-badge--scheduled';
}

$subscription_badge_class = '';
$subscription_badge_label = 'Actif';

if ($is_paused) {
    $subscription_badge_class = 'sa-live-soft-badge--danger';
    $subscription_badge_label = 'Résilié';
} elseif ($is_cancel_scheduled) {
    $subscription_badge_class = 'sa-live-soft-badge--scheduled';
    $subscription_badge_label = 'Résiliation programmée';
}
?>

<div class="sa-live-dashboard">
    <div class="sa-live-header-row">
        <div class="sa-live-hello">
            <span class="sa-live-kicker">Espace client</span>
            <h1>Bonjour <?php echo esc_html($user_name); ?></h1>
            <p>Gère ton site et ton abonnement depuis ton espace.</p>
        </div>

        <div class="sa-live-help-card">
            <h3>Besoin d’aide ?</h3>
            <p>Notre équipe est là pour toi.</p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contacter le support</a>
        </div>
    </div>

    <div class="sa-live-layout">

        <?php echo sa_render_template('partials/account-sidebar'); ?>

        <main class="sa-live-main">

            <section class="sa-live-status-card">
                <div class="sa-live-badge <?php echo esc_attr($main_badge_class); ?>">
                    <span class="sa-live-badge-dot"></span>
                    <span>
                        <?php if ($is_paused) : ?>
                            Projet suspendu
                        <?php elseif ($is_cancel_scheduled) : ?>
                            Résiliation programmée
                        <?php else : ?>
                            Site en ligne
                        <?php endif; ?>
                    </span>
                </div>

                <h2 class="sa-live-domain">monsiteauteur.fr</h2>

                <div class="sa-live-date">
                    <span class="sa-live-date-icon">📅</span>
                    <span>
                        <?php if ($is_paused) : ?>
                            Projet suspendu depuis le <?php echo esc_html($display_date); ?>
                        <?php elseif ($is_cancel_scheduled && $display_period_end) : ?>
                            Ton site reste en ligne jusqu’au <?php echo esc_html($display_period_end); ?>
                        <?php else : ?>
                            Ton site est en ligne depuis le <?php echo esc_html($display_date); ?>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="sa-live-divider"></div>

                <div class="sa-live-meta-grid">
                    <div class="sa-live-meta-card <?php echo $is_paused ? 'sa-live-meta-card--disabled' : ''; ?>">
                        <div class="sa-live-meta-icon sa-gold">✏️</div>

                        <div>
                            <strong>Modifications</strong>
                            <span>
                                <?php echo $is_paused ? 'Indisponible tant que le projet est suspendu.' : esc_html($modification_stock_label); ?>
                            </span>
                        </div>

                        <?php if ($is_paused) : ?>
                            <span class="sa-live-lock">🔒</span>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="sa-live-section">
                <h3>Actions rapides</h3>

                <div class="sa-live-actions-grid">

                    <?php if ($is_paused) : ?>

                        <div class="sa-live-action-card sa-live-action-card--locked">
                            <span class="sa-live-card-lock">🔒</span>
                            <div class="sa-live-action-icon">👁</div>
                            <strong>Voir mon site</strong>
                            <span>Indisponible tant que le projet est suspendu.</span>
                        </div>

                    <?php else : ?>

                        <a href="#" class="sa-live-action-card">
                            <div class="sa-live-action-icon">👁</div>
                            <strong>Voir mon site</strong>
                            <span>Accéder à ton site en ligne</span>
                        </a>

                    <?php endif; ?>

                    <?php if ($is_paused) : ?>

                        <div class="sa-live-action-card sa-live-action-card--locked">
                            <span class="sa-live-card-lock">🔒</span>
                            <div class="sa-live-action-icon">✏️</div>
                            <strong>Utiliser mes modifications</strong>
                            <span>Indisponible tant que le projet est suspendu.</span>
                        </div>

                    <?php elseif ($has_modification_left) : ?>

                        <a href="<?php echo esc_url($modification_url); ?>" class="sa-live-action-card sa-live-action-card--modification">
                            <div class="sa-live-action-icon">✏️</div>
                            <strong>Utiliser mes modifications</strong>
                            <span><?php echo esc_html($modification_action_label); ?></span>
                        </a>

                    <?php else : ?>

                        <div class="sa-live-action-card sa-live-action-card--disabled">
                            <div class="sa-live-action-icon sa-live-action-icon--disabled">⛔</div>
                            <strong>Aucune modification disponible</strong>
                            <span>Tes prochaines modifications seront disponibles le mois prochain</span>
                        </div>

                    <?php endif; ?>

                    <a href="<?php echo esc_url(home_url('/mon-abonnement/')); ?>" class="sa-live-action-card">
                        <div class="sa-live-action-icon">💳</div>
                        <strong>Gérer l’abonnement</strong>
                        <span>Voir ton abonnement et tes factures</span>
                    </a>

                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="sa-live-action-card">
                        <div class="sa-live-action-icon">🎧</div>
                        <strong>Contacter le support</strong>
                        <span>Nous répondons sous 24h en moyenne</span>
                    </a>
                </div>

                <?php if (!$is_paused && !$has_modification_left) : ?>
                    <div class="sa-live-purchase-banner">
                        <div class="sa-live-purchase-banner__content">
                            <strong>Besoin d’une modification supplémentaire avant la prochaine période ?</strong>
                            <p>Tu peux acheter une modification supplémentaire pour mettre à jour ton site dès maintenant.</p>
                        </div>

                        <a href="<?php echo esc_url(home_url('/acheter-une-modification/')); ?>" class="sa-live-purchase-banner__btn">
                            Acheter une modification
                        </a>
                    </div>
                <?php endif; ?>
            </section>

            <section class="sa-live-subscription-card">
                <div class="sa-live-subscription-top">
                    <h3>Ton abonnement</h3>

                    <span class="sa-live-soft-badge <?php echo esc_attr($subscription_badge_class); ?>">
                        <?php echo esc_html($subscription_badge_label); ?>
                    </span>
                </div>

                <div class="sa-live-price">39€ <span>/mois</span></div>

                <p class="sa-live-next-billing <?php echo ($is_paused || $is_cancel_scheduled) ? 'sa-live-next-billing--danger' : ''; ?>">
                    <?php if ($is_paused) : ?>

                        📅 Abonnement résilié

                    <?php elseif ($is_cancel_scheduled && $display_period_end) : ?>

                        📅 Résiliation prévue le <?php echo esc_html($display_period_end); ?>

                    <?php else : ?>

                        📅 Prochaine facture : 15 mai 2026

                    <?php endif; ?>
                </p>

                <div class="sa-live-includes">
                    <h4>Inclus dans ton abonnement :</h4>

                    <div class="sa-live-includes-grid">
                        <div>✓ Hébergement sécurisé</div>
                        <div>✓ Support prioritaire</div>
                        <div>✓ Maintenance continue</div>
                        <div>✓ <?php echo (int) $included_limit; ?> modifications par mois</div>
                        <div>✓ Sauvegardes quotidiennes</div>
                    </div>
                </div>

                <?php if ($is_paused) : ?>

                    <a href="<?php echo esc_url(home_url('/mon-abonnement/')); ?>" class="sa-live-secondary-btn sa-live-secondary-btn--danger">
                        Réactiver mon abonnement
                    </a>

                <?php elseif ($is_cancel_scheduled) : ?>

                    <a href="<?php echo esc_url(home_url('/mon-abonnement/')); ?>" class="sa-live-secondary-btn sa-live-secondary-btn--scheduled">
                        Gérer la résiliation
                    </a>

                <?php else : ?>

                    <a href="<?php echo esc_url(home_url('/mes-factures/')); ?>" class="sa-live-secondary-btn">
                        Voir mes factures
                    </a>

                <?php endif; ?>
            </section>

            <div class="sa-live-note">
                <div class="sa-live-note-icon">💡</div>

                <div>
                    <strong>Bon à savoir</strong>

                    <p>
                        <?php if ($is_paused) : ?>

                            Ton projet est actuellement suspendu. Certaines fonctionnalités sont indisponibles.
                            Réactive ton abonnement à tout moment pour retrouver l’accès complet à ton site et à tes modifications.

                        <?php elseif ($is_cancel_scheduled && $display_period_end) : ?>

                            Ta résiliation est programmée. Ton site reste en ligne et tes fonctionnalités restent accessibles jusqu’au <?php echo esc_html($display_period_end); ?>.

                        <?php else : ?>

                            Après la mise en ligne, tu peux continuer à faire vivre ton site grâce aux modifications incluses dans ton abonnement.

                        <?php endif; ?>
                    </p>
                </div>
            </div>

        </main>
    </div>
</div>