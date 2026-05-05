<?php if (!defined('ABSPATH')) exit; ?>

<?php
$current_user = wp_get_current_user();
$user_name    = $current_user->first_name ?: $current_user->display_name;

function sa_invoices_page_url($page) {
    return add_query_arg('factures_page', max(1, (int) $page), get_permalink());
}
?>

<div class="sa-live-dashboard sa-invoices-page">
    <div class="sa-live-header-row">
        <div class="sa-live-hello">
            <span class="sa-live-kicker">Espace client</span>
            <h1>Mes factures</h1>
            <p>Retrouve ici toutes tes factures.</p>
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

            <section class="sa-subscription-invoices-card">
                <div class="sa-invoices-page-topbar">
                    <div>
                        <h3>Toutes mes factures</h3>
                        <p class="sa-invoices-page-count">
                            <?php echo esc_html($total_items); ?> facture<?php echo $total_items > 1 ? 's' : ''; ?>
                        </p>
                    </div>

                    <a href="<?php echo esc_url(home_url('/mon-abonnement/')); ?>" class="sa-invoices-back-link">
                        ← Retour à mon abonnement
                    </a>
                </div>

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
                            <?php if (!empty($invoices)) : ?>
                            <?php foreach ($invoices as $invoice) : ?>
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

                <?php if ($total_pages > 1) : ?>
                    <div class="sa-invoices-pagination">
                        <div class="sa-invoices-pagination-left">
                            <?php if ($current_page > 1) : ?>
                                <a href="<?php echo esc_url(sa_invoices_page_url($current_page - 1)); ?>" class="sa-invoices-pagination-btn">
                                    ← Précédent
                                </a>
                            <?php else : ?>
                                <span class="sa-invoices-pagination-btn is-disabled">← Précédent</span>
                            <?php endif; ?>
                        </div>

                        <div class="sa-invoices-pagination-center">
                            Page <?php echo esc_html($current_page); ?> sur <?php echo esc_html($total_pages); ?>
                        </div>

                        <div class="sa-invoices-pagination-right">
                            <?php if ($current_page < $total_pages) : ?>
                                <a href="<?php echo esc_url(sa_invoices_page_url($current_page + 1)); ?>" class="sa-invoices-pagination-btn">
                                    Suivant →
                                </a>
                            <?php else : ?>
                                <span class="sa-invoices-pagination-btn is-disabled">Suivant →</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </section>

        </main>
    </div>
</div>