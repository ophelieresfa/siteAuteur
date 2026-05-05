<?php if (!defined('ABSPATH')) exit; ?>

<?php
$current_user = wp_get_current_user();
$user_name    = $current_user->first_name ?: $current_user->display_name;

$displayed_invoices = array_slice($invoices, 0, 3);
$has_more_than_3_invoices = count($invoices) > 3;

$subscription_status       = !empty($subscription['status']) ? $subscription['status'] : '';
$subscription_status_label = sa_get_stripe_subscription_status_label($subscription_status);

/**
 * Statut interne de la commande SiteAuteur
 */
$order_state = '';

if (!empty($order) && !empty($order->id) && !empty($user_id) && function_exists('sa_get_order_state')) {
    $order_state = sa_get_order_state((int) $user_id, (int) $order->id);
}

$cancel_at_period_end = !empty($subscription['cancel_at_period_end']);

$is_order_paused = ($order_state === 'paused');

$is_subscription_stopped = in_array(
    $subscription_status,
    ['canceled', 'unpaid', 'paused', 'incomplete_expired'],
    true
);

/**
 * Badge d'état visible côté client.
 *
 * - Si l'abonnement est vraiment terminé : Résilié
 * - Si la résiliation est programmée : Résiliation programmée
 * - Sinon : statut Stripe normal, souvent Actif
 */
if ($is_subscription_stopped) {
    $subscription_status_label = 'Résilié';
    $subscription_badge_state  = 'canceled';
} elseif ($cancel_at_period_end || $is_order_paused) {
    $subscription_status_label = 'Résiliation programmée';
    $subscription_badge_state  = 'scheduled';
} else {
    $subscription_badge_state = 'active';
}

$is_active = ($subscription_badge_state === 'active');

$start_date_ts   = sa_get_stripe_subscription_start_date($subscription);
$period_end_ts   = sa_get_stripe_subscription_period_end($subscription);
$period_start_ts = sa_get_stripe_subscription_period_start($subscription);
$price_display   = sa_get_stripe_subscription_price_display($subscription);

$display_start_date = $start_date_ts ? date_i18n('d F Y', $start_date_ts) : '—';
$display_next_date  = $period_end_ts ? date_i18n('d F Y', $period_end_ts) : '—';
$cancel_end_date    = $period_end_ts ? date_i18n('d F Y', $period_end_ts) : '—';

$included_limit = function_exists('sa_get_included_modifications_limit')
    ? (int) sa_get_included_modifications_limit()
    : 5;

$notice = !empty($_GET['subscription_notice'])
    ? sanitize_text_field($_GET['subscription_notice'])
    : '';

function sa_subscription_notice_message($notice) {
    switch ($notice) {
        case 'cancel_scheduled':
            return [
                'type'    => 'success',
                'title'   => 'Résiliation programmée',
                'message' => 'La résiliation de ton abonnement a bien été enregistrée. Ton site restera actif jusqu’à la fin de la période déjà payée.',
            ];

        case 'already_scheduled':
            return [
                'type'    => 'info',
                'title'   => 'Résiliation déjà programmée',
                'message' => 'Cet abonnement est déjà configuré pour s’arrêter à la fin de la période en cours.',
            ];

        case 'resume_success':
            return [
                'type'    => 'success',
                'title'   => 'Abonnement repris',
                'message' => 'Le renouvellement automatique de ton abonnement a bien été réactivé.',
            ];

        case 'resume_not_needed':
            return [
                'type'    => 'info',
                'title'   => 'Aucune reprise nécessaire',
                'message' => 'Cet abonnement est déjà actif en renouvellement automatique.',
            ];

        case 'resume_error':
            return [
                'type'    => 'error',
                'title'   => 'Erreur de reprise',
                'message' => 'Une erreur est survenue lors de la reprise de l’abonnement.',
            ];

        case 'cancel_error':
            return [
                'type'    => 'error',
                'title'   => 'Erreur de résiliation',
                'message' => 'Une erreur est survenue lors de la résiliation.',
            ];

        case 'subscription_missing':
            return [
                'type'    => 'error',
                'title'   => 'Abonnement introuvable',
                'message' => 'Aucun abonnement Stripe n’a été trouvé pour cette commande.',
            ];

        case 'subscription_not_found':
            return [
                'type'    => 'error',
                'title'   => 'Abonnement Stripe introuvable',
                'message' => 'Impossible de récupérer cet abonnement Stripe.',
            ];

        case 'order_not_found':
            return [
                'type'    => 'error',
                'title'   => 'Commande introuvable',
                'message' => 'Commande introuvable.',
            ];

        case 'forbidden':
            return [
                'type'    => 'error',
                'title'   => 'Accès refusé',
                'message' => 'Accès refusé.',
            ];

        case 'invalid_nonce':
            return [
                'type'    => 'error',
                'title'   => 'Requête invalide',
                'message' => 'Requête invalide.',
            ];

                case 'resume_checkout_success':
            return [
                'type'    => 'success',
                'title'   => 'Paiement confirmé',
                'message' => 'Ton abonnement a bien été repris. Ton site est de nouveau actif.',
            ];

        case 'resume_checkout_cancel':
            return [
                'type'    => 'info',
                'title'   => 'Paiement annulé',
                'message' => 'La reprise de l’abonnement a été annulée. Aucun paiement n’a été effectué.',
            ];

        case 'resume_checkout_error':
            return [
                'type'    => 'error',
                'title'   => 'Erreur de paiement',
                'message' => 'Impossible d’ouvrir la page de paiement Stripe.',
            ];

        case 'sa_subscription_not_stopped':
            return [
                'type'    => 'info',
                'title'   => 'Abonnement non arrêté',
                'message' => 'Cet abonnement n’est pas arrêté. Il n’est donc pas nécessaire de le reprendre avec un nouveau paiement.',
            ];

        case 'sa_missing_price_id':
            return [
                'type'    => 'error',
                'title'   => 'Prix Stripe introuvable',
                'message' => 'Impossible de retrouver le tarif Stripe de l’ancien abonnement.',
            ];

        default:
            return false;
    }
}

$notice_data = sa_subscription_notice_message($notice);

/*
Si l’abonnement est déjà arrêté, on ne doit plus afficher
l’ancien message vert "Résiliation programmée".
*/
if (
    $is_subscription_stopped &&
    in_array($notice, ['cancel_scheduled', 'already_scheduled'], true)
) {
    $notice_data = false;
}
?>

<div class="sa-live-dashboard">
    <div class="sa-live-header-row">
        <div class="sa-live-hello">
            <span class="sa-live-kicker">Espace client</span>
            <h1>Mon abonnement</h1>
            <p>Gère ton abonnement et ta facturation.</p>
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

            <?php if ($notice_data) : ?>
                <div class="sa-subscription-notice sa-subscription-notice-<?php echo esc_attr($notice_data['type']); ?>">
                    <div class="sa-subscription-notice-icon">
                        <?php if ($notice_data['type'] === 'success') : ?>
                            ✓
                        <?php elseif ($notice_data['type'] === 'error') : ?>
                            !
                        <?php else : ?>
                            i
                        <?php endif; ?>
                    </div>

                    <div class="sa-subscription-notice-content">
                        <strong><?php echo esc_html($notice_data['title']); ?></strong>
                        <p><?php echo esc_html($notice_data['message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <section class="sa-subscription-hero-card">
                <div class="sa-subscription-hero-left">
                    <div class="sa-subscription-plan-icon">💳</div>

                    <div class="sa-subscription-plan-content">
                        <?php
                        $subscription_badge_class = 'sa-subscription-status-active';

                        if ($subscription_badge_state === 'scheduled') {
                            $subscription_badge_class = 'sa-subscription-status-scheduled';
                        } elseif ($subscription_badge_state === 'canceled') {
                            $subscription_badge_class = 'sa-subscription-status-canceled';
                        }
                        ?>

                        <div class="sa-subscription-status-badge <?php echo esc_attr($subscription_badge_class); ?>">
                            <span class="sa-subscription-status-dot"></span>
                            <span><?php echo esc_html($subscription_status_label); ?></span>
                        </div>

                        <h2>SiteAuteur Pro</h2>

                        <div class="sa-subscription-price">
                            <?php echo esc_html($price_display); ?>
                        </div>

                        <div class="sa-subscription-meta">
                            <?php if ($is_subscription_stopped) : ?>
                                <div class="sa-subscription-ending-box">
                                    <div class="sa-subscription-ending-badge">Abonnement arrêté</div>
                                    <p>
                                        Ton abonnement n’est plus actif.
                                        Tu peux le reprendre avec un nouveau paiement sécurisé Stripe.
                                    </p>
                                </div>
                            <?php elseif ($cancel_at_period_end) : ?>
                                <div class="sa-subscription-ending-box">
                                    <div class="sa-subscription-ending-badge">Résiliation programmée</div>
                                    <p>
                                        Ton abonnement est résilié en fin de période.
                                        Ton site restera actif jusqu’au
                                        <strong><?php echo esc_html($cancel_end_date); ?></strong>.
                                    </p>
                                </div>
                            <?php else : ?>
                                <div class="sa-subscription-meta-line">
                                    <span class="sa-subscription-meta-icon">📅</span>
                                    <span>Prochaine facture : <?php echo esc_html($display_next_date); ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="sa-subscription-meta-line">
                                <span class="sa-subscription-meta-icon">📅</span>
                                <span>Actif depuis : <?php echo esc_html($display_start_date); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sa-subscription-hero-right">
                    <h3>Inclus dans ton abonnement :</h3>

                    <div class="sa-subscription-includes-grid">
                        <div>✓ Hébergement sécurisé</div>
                        <div>✓ Support prioritaire</div>
                        <div>✓ Maintenance continue</div>
                        <div>✓ <?php echo (int) $included_limit; ?> modifications par mois</div>
                        <div>✓ Sauvegardes quotidiennes</div>
                    </div>
                </div>
            </section>

            <section class="sa-live-section">
                <h3>Actions rapides</h3>

                <div class="sa-subscription-actions-grid">
                    <a href="<?php echo esc_url(home_url('/mes-factures/')); ?>" class="sa-subscription-action-card">
                        <div class="sa-subscription-action-icon">📄</div>
                        <strong>Voir mes factures</strong>
                        <span>Accéder à l’historique</span>
                    </a>

                    <a href="<?php echo esc_url(home_url('/modifier-moyen-de-paiement/')); ?>" target="_blank" rel="noopener noreferrer" class="sa-subscription-action-card">
                        <div class="sa-subscription-action-icon">💳</div>
                        <strong>Modifier mon moyen de paiement</strong>
                        <span>Mettre à jour mon moyen de paiement</span>
                    </a>

                    <a href="#resiliation-abonnement" class="sa-subscription-action-card">
                        <div class="sa-subscription-action-icon">🛡️</div>

                        <?php if ($is_subscription_stopped) : ?>
                            <strong>Reprendre l’abonnement</strong>
                            <span>Relancer ton abonnement</span>
                        <?php else : ?>
                            <strong>Résilier l’abonnement</strong>
                            <span>Arrêt en fin de période</span>
                        <?php endif; ?>
                    </a>

                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="sa-subscription-action-card">
                        <div class="sa-subscription-action-icon">🎧</div>
                        <strong>Contacter le support</strong>
                        <span>Nous répondons sous 24h en moyenne</span>
                    </a>
                </div>
            </section>

            <section id="historique-factures" class="sa-subscription-invoices-card">
                <h3>Mes dernières factures</h3>

                <div class="sa-subscription-table-wrap">
                    <table class="sa-subscription-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($displayed_invoices)) : ?>
                                <?php foreach ($displayed_invoices as $invoice) : ?>
                                    <?php
                                    $invoice_date = !empty($invoice['created'])
                                        ? date_i18n('d F Y', (int) $invoice['created'])
                                        : '—';

                                    $invoice_type = !empty($invoice['type'])
                                        ? $invoice['type']
                                        : '—';

                                    $amount_paid = isset($invoice['amount_paid'])
                                        ? number_format(((int) $invoice['amount_paid']) / 100, 2, ',', ' ') . ' €'
                                        : '—';

                                    $invoice_status       = !empty($invoice['status']) ? $invoice['status'] : '';
                                    $invoice_status_label = function_exists('sa_get_invoice_status_label')
                                        ? sa_get_invoice_status_label($invoice_status)
                                        : ucfirst((string) $invoice_status);

                                    $invoice_url = function_exists('sa_get_account_invoice_download_url')
                                    ? sa_get_account_invoice_download_url((int) $order->id, $invoice)
                                    : '';
                                    ?>
                                    <tr>
                                        <td><?php echo esc_html($invoice_date); ?></td>
                                        <td><?php echo esc_html($invoice_type); ?></td>
                                        <td><?php echo esc_html($amount_paid); ?></td>
                                        <td>
                                            <span class="sa-subscription-paid-badge">
                                                ● <?php echo esc_html($invoice_status_label); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($invoice_url) : ?>
                                                <a href="<?php echo esc_url($invoice_url); ?>" target="_blank" rel="noopener noreferrer">
                                                    Télécharger
                                                </a>
                                            <?php else : ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="5">Aucune facture trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($has_more_than_3_invoices) : ?>
                    <div class="sa-subscription-more">
                        <a href="<?php echo esc_url(home_url('/mes-factures/')); ?>">Voir toutes mes factures</a>
                    </div>
                <?php endif; ?>
            </section>

            <section id="resiliation-abonnement" class="sa-subscription-danger-box">
                <div class="sa-subscription-danger-left">
                    <div class="sa-subscription-danger-icon">
                        <?php if (!$is_subscription_stopped) : ?>
                            <div class="sa-subscription-danger-icon">
                                ⚠️
                            </div>
                        <?php endif; ?>
                    </div>

                    <div>
                        <?php if ($is_subscription_stopped) : ?>
                            <h3>Reprendre l’abonnement</h3>
                            <p>Ton abonnement est actuellement arrêté.</p>
                            <p>Pour reprendre, tu vas être redirigé vers Stripe afin de créer un nouvel abonnement sur le même compte client.</p>
                        <?php else : ?>
                            <h3>Résilier l’abonnement</h3>

                            <?php if ($cancel_at_period_end) : ?>
                                <p>La résiliation est déjà programmée pour la fin de la période en cours.</p>
                                <p>Ton site restera actif jusqu’au <strong><?php echo esc_html($cancel_end_date); ?></strong>.</p>
                            <?php else : ?>
                                <p>La résiliation arrêtera le renouvellement automatique.</p>
                                <p>Ton site restera actif jusqu’à la fin de la période déjà payée.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="sa-subscription-danger-right">
                    <?php if ($is_subscription_stopped) : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Confirmer la reprise de l’abonnement ?');">
                            <input type="hidden" name="action" value="sa_resume_canceled_subscription">
                            <?php wp_nonce_field('sa_resume_canceled_subscription', 'sa_resume_canceled_subscription_nonce'); ?>

                            <button type="submit" class="sa-subscription-secondary-btn">
                                Reprendre mon abonnement
                            </button>
                        </form>
                    <?php elseif ($cancel_at_period_end) : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Confirmer la reprise de l’abonnement ?');">
                            <input type="hidden" name="action" value="sa_resume_subscription">
                            <?php wp_nonce_field('sa_resume_subscription', 'sa_resume_subscription_nonce'); ?>

                            <button type="submit" class="sa-subscription-secondary-btn">
                                Reprendre mon abonnement
                            </button>
                        </form>
                    <?php else : ?>
                        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Confirmer la résiliation de l’abonnement en fin de période ?');">
                            <input type="hidden" name="action" value="sa_cancel_subscription">
                            <?php wp_nonce_field('sa_cancel_subscription', 'sa_cancel_subscription_nonce'); ?>

                            <button type="submit" class="sa-subscription-danger-btn">
                                Résilier mon abonnement
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </section>

        </main>
    </div>
</div>