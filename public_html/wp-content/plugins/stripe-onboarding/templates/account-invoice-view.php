<?php if (!defined('ABSPATH')) exit; ?>

<div class="invoice-actions">
    <button
        type="button"
        id="sa-download-invoice-pdf"
        class="invoice-download-btn"
        data-filename="<?php echo esc_attr($safe_filename); ?>"
    >
        Télécharger la facture en PDF
    </button>
</div>

<div id="sa-invoice-capture" class="page page-invoice">
    <table class="header-table">
        <tr>
            <td class="header-left">
                <p class="kicker">Facture</p>
                <h1 class="title">Facture<br>SiteAuteur</h1>
            </td>
            <td class="header-right">
                <p class="company-name"><?php echo esc_html($company['name']); ?></p>
                <p class="company-line"><?php echo esc_html($company['address']); ?></p>
                <p class="company-line"><?php echo esc_html($company['email']); ?></p>
            </td>
        </tr>
    </table>

    <div class="status-wrap">
        <span class="status-pill">
            <span class="status-pill-inner"><?php echo esc_html($status_label); ?></span>
        </span>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-gap-left">
                <div class="info-card">
                    <p class="info-card-title">Informations facture</p>
                    <p class="info-line"><strong>Numéro :</strong> <?php echo esc_html($invoice_data['number']); ?></p>
                    <p class="info-line"><strong>Date d’émission :</strong> <?php echo esc_html($date_display); ?></p>
                    <p class="info-line status-row">
                        <span class="status-label-inline">Statut :</span>
                        <span class="inline-status">
                            <span class="inline-status-inner"><?php echo esc_html($status_label); ?></span>
                        </span>
                    </p>
                </div>
            </td>
            <td class="info-gap-right">
                <div class="info-card">
                    <p class="info-card-title">Facturé à</p>
                    <p class="customer-name"><?php echo esc_html($invoice_data['customer_name']); ?></p>
                    <p class="info-line"><?php echo esc_html($invoice_data['customer_email']); ?></p>
                    <p class="info-line">France</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="items-wrap">
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-desc">Description</th>
                    <th class="col-qty">Qté</th>
                    <th class="col-unit">Prix unitaire</th>
                    <th class="col-total">Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($invoice_data['lines'] as $line) : ?>
                    <tr>
                        <td class="col-desc"><?php echo esc_html($line['description']); ?></td>
                        <td class="col-qty"><?php echo esc_html($line['quantity']); ?></td>
                        <td class="col-unit"><?php echo esc_html(sa_format_price_cents($line['unit_amount'])); ?></td>
                        <td class="col-total"><?php echo esc_html(sa_format_price_cents($line['amount'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="totals-wrap">
        <div class="totals-box">
            <table class="totals-table">
                <tr>
                    <td class="totals-label">Sous-total</td>
                    <td class="totals-value"><?php echo esc_html(sa_format_price_cents($invoice_data['subtotal'])); ?></td>
                </tr>
                <tr class="total-divider grand-total">
                    <td class="totals-label">Total</td>
                    <td class="totals-value"><?php echo esc_html(sa_format_price_cents($invoice_data['total'])); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="note-box">
        <table class="note-layout">
            <tr>
                <td class="note-icon-cell">
                    <span class="note-circle">
                        <span class="note-circle-inner">
                            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#3fc095" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </span>
                    </span>
                </td>
                <td class="note-content-cell">
                    <p class="note-title">Merci pour ta confiance.</p>
                    <p class="note-text"><?php echo esc_html($confirmation_text); ?></p>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-layout">
        <table class="footer-table">
            <tr>
                <td class="footer-icon-cell">
                    <span class="footer-badge">
                        <span class="footer-badge-inner">
                            <svg width="15" height="15" viewBox="0 0 20 20" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                                <path fill="#7c8aa2" d="M7.6 14.2L3.8 10.4l1.4-1.4 2.4 2.4 6-6 1.4 1.4-7.4 7.4z"/>
                            </svg>
                        </span>
                    </span>
                </td>
                <td class="footer-text-cell">
                    Facture générée automatiquement par SiteAuteur
                </td>
            </tr>
        </table>
    </div>
</div>