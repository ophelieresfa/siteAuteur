<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * FACTURES MAISON
 * =========================
 */

function sa_get_order_by_id_for_user($user_id, $order_id) {
    $orders = sa_get_user_paid_orders($user_id);

    if (empty($orders) || !is_array($orders)) {
        return false;
    }

    foreach ($orders as $order) {
        if (!empty($order->id) && (int) $order->id === (int) $order_id) {
            return $order;
        }
    }

    return false;
}

function sa_get_company_invoice_data() {
    return [
        'name'    => 'SiteAuteur',
        'address' => 'France',
        'email'   => 'admin@siteauteur.fr',
    ];
}

function sa_format_price_cents($amount) {
    return number_format(((int) $amount) / 100, 2, ',', ' ') . ' €';
}

function sa_get_account_invoice_download_url($order_id, $invoice) {
    $source = !empty($invoice['source']) ? sanitize_key($invoice['source']) : 'subscription_invoice';
    $doc_id = !empty($invoice['stripe_id'])
        ? (string) $invoice['stripe_id']
        : md5(wp_json_encode($invoice));

    return add_query_arg([
        'sa_download_invoice' => 1,
        'order_id'            => (int) $order_id,
        'source'              => $source,
        'doc_id'              => rawurlencode($doc_id),
    ], home_url('/mes-factures/'));
}

function sa_guess_extra_modification_label($doc) {
    $amount = isset($doc['amount_paid']) ? (int) $doc['amount_paid'] : 0;

    switch ($amount) {
        case 1200:
            return 'Modification supplémentaire - 1 modification';
        case 4900:
            return 'Modification supplémentaire - Pack 5 modifications';
        case 8900:
            return 'Modification supplémentaire - Pack 10 modifications';
        case 14900:
            return 'Modification supplémentaire - Pack illimité 30 jours';
        default:
            return 'Modification supplémentaire';
    }
}

function sa_get_extra_modification_invoice_number($doc) {
    if (!empty($doc['invoice_number'])) {
        return (string) $doc['invoice_number'];
    }

    if (!empty($doc['stripe_id'])) {
        return 'SA-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', (string) $doc['stripe_id']), 0, 12));
    }

    return 'SA-' . strtoupper(substr(md5(wp_json_encode($doc)), 0, 12));
}

function sa_find_extra_modification_invoice_doc($user_id, $order_id, $doc_id) {
    $items = sa_get_order_extra_modification_invoices($user_id, $order_id);

    if (empty($items) || !is_array($items)) {
        return false;
    }

    foreach ($items as $item) {
        $candidate = !empty($item['stripe_id'])
            ? (string) $item['stripe_id']
            : md5(wp_json_encode($item));

        if ((string) $candidate === (string) $doc_id) {
            return $item;
        }
    }

    return false;
}

function sa_normalize_invoice_text($text) {
    $text = remove_accents((string) $text);
    $text = wp_strip_all_tags($text);
    $text = strtolower($text);
    $text = preg_replace('/\s+/', ' ', $text);

    return trim((string) $text);
}

function sa_is_site_creation_with_subscription_purchase($invoice_data) {
    if (empty($invoice_data) || !is_array($invoice_data)) {
        return false;
    }

    $total = isset($invoice_data['total']) ? (int) $invoice_data['total'] : 0;

    if ($total === 28800) {
        return true;
    }

    if (empty($invoice_data['lines']) || !is_array($invoice_data['lines'])) {
        return false;
    }

    $has_creation = false;
    $has_subscription = false;

    foreach ($invoice_data['lines'] as $line) {
        $description = sa_normalize_invoice_text($line['description'] ?? '');

        if ($description === '') {
            continue;
        }

        if (
            strpos($description, 'creation') !== false ||
            strpos($description, 'site internet') !== false ||
            strpos($description, 'creation de site') !== false
        ) {
            $has_creation = true;
        }

        if (
            strpos($description, 'abonnement') !== false ||
            strpos($description, '1er mois') !== false ||
            strpos($description, 'premier mois') !== false ||
            strpos($description, 'mensuel') !== false
        ) {
            $has_subscription = true;
        }
    }

    return ($has_creation && $has_subscription);
}

function sa_get_invoice_confirmation_text($invoice_data) {
    if (sa_is_site_creation_with_subscription_purchase($invoice_data)) {
        return "Cette facture confirme l'achat et l'abonnement de ton site sur SiteAuteur.";
    }

    return "Cette facture confirme l'achat de ton option sur SiteAuteur.";
}

function sa_get_invoice_status_label_for_pdf($status) {
    $status = (string) $status;

    if (function_exists('sa_get_invoice_status_label')) {
        return sa_get_invoice_status_label($status);
    }

    switch ($status) {
        case 'paid':
            return 'Payée';
        case 'open':
            return 'Ouverte';
        case 'draft':
            return 'Brouillon';
        case 'void':
            return 'Annulée';
        case 'uncollectible':
            return 'Irrécouvrable';
        default:
            return ucfirst($status);
    }
}

function sa_prepare_subscription_invoice_data($stripe_invoice, $user) {
    if (empty($stripe_invoice) || !is_array($stripe_invoice)) {
        return false;
    }

    $lines = [];

    if (!empty($stripe_invoice['lines']['data']) && is_array($stripe_invoice['lines']['data'])) {
        foreach ($stripe_invoice['lines']['data'] as $line) {
            $description = !empty($line['description']) ? (string) $line['description'] : 'Prestation SiteAuteur';
            $quantity    = isset($line['quantity']) ? max(1, (int) $line['quantity']) : 1;
            $amount      = isset($line['amount']) ? (int) $line['amount'] : 0;
            $unit_amount = $quantity > 0 ? (int) round($amount / $quantity) : $amount;

            $lines[] = [
                'description' => $description,
                'quantity'    => $quantity,
                'unit_amount' => $unit_amount,
                'amount'      => $amount,
            ];
        }
    }

    if (empty($lines)) {
        $amount_paid = isset($stripe_invoice['amount_paid']) ? (int) $stripe_invoice['amount_paid'] : 0;

        $lines[] = [
            'description' => 'Abonnement SiteAuteur',
            'quantity'    => 1,
            'unit_amount' => $amount_paid,
            'amount'      => $amount_paid,
        ];
    }

    $customer_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    if ($customer_name === '') {
        $customer_name = $user->display_name;
    }

    $subtotal = isset($stripe_invoice['subtotal'])
        ? (int) $stripe_invoice['subtotal']
        : (isset($stripe_invoice['amount_paid']) ? (int) $stripe_invoice['amount_paid'] : 0);

    $total = isset($stripe_invoice['amount_paid']) ? (int) $stripe_invoice['amount_paid'] : 0;

    $data = [
        'number'         => !empty($stripe_invoice['number']) ? $stripe_invoice['number'] : 'SA-' . strtoupper(substr((string) $stripe_invoice['id'], 0, 12)),
        'issued_at'      => !empty($stripe_invoice['created']) ? (int) $stripe_invoice['created'] : time(),
        'status'         => !empty($stripe_invoice['status']) ? (string) $stripe_invoice['status'] : 'paid',
        'type'           => 'Facture',
        'customer_name'  => $customer_name,
        'customer_email' => $user->user_email,
        'lines'          => $lines,
        'subtotal'       => $subtotal,
        'total'          => $total,
    ];

    $data['confirmation_text'] = sa_get_invoice_confirmation_text($data);

    return $data;
}

function sa_prepare_extra_modification_invoice_data($doc, $user) {
    if (empty($doc) || !is_array($doc)) {
        return false;
    }

    $amount = isset($doc['amount_paid']) ? (int) $doc['amount_paid'] : 0;
    $label  = !empty($doc['label']) ? (string) $doc['label'] : sa_guess_extra_modification_label($doc);

    $customer_name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
    if ($customer_name === '') {
        $customer_name = $user->display_name;
    }

    $data = [
        'number'         => sa_get_extra_modification_invoice_number($doc),
        'issued_at'      => !empty($doc['created']) ? (int) $doc['created'] : time(),
        'status'         => !empty($doc['status']) ? (string) $doc['status'] : 'paid',
        'type'           => 'Facture',
        'customer_name'  => $customer_name,
        'customer_email' => $user->user_email,
        'lines'          => [
            [
                'description' => $label,
                'quantity'    => 1,
                'unit_amount' => $amount,
                'amount'      => $amount,
            ]
        ],
        'subtotal'       => $amount,
        'total'          => $amount,
    ];

    $data['confirmation_text'] = sa_get_invoice_confirmation_text($data);

    return $data;
}

function sa_render_local_invoice_html($invoice_data) {
    $company = sa_get_company_invoice_data();

    $status_label = sa_get_invoice_status_label_for_pdf($invoice_data['status'] ?? 'paid');

    $date_display = !empty($invoice_data['issued_at'])
        ? date_i18n('d F Y', (int) $invoice_data['issued_at'])
        : '—';

    $confirmation_text = !empty($invoice_data['confirmation_text'])
        ? (string) $invoice_data['confirmation_text']
        : sa_get_invoice_confirmation_text($invoice_data);

    $safe_filename = 'facture-' . preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) $invoice_data['number']) . '.pdf';

    ob_start();
    include plugin_dir_path(dirname(__FILE__)) . 'templates/account-invoice-view.php';
    return ob_get_clean();
}

add_action('wp_enqueue_scripts', function () {
    if (empty($_GET['sa_download_invoice'])) {
        return;
    }

    wp_enqueue_style(
        'siteauteur-account-invoice-view',
        plugin_dir_url(dirname(__FILE__)) . 'assets/css/account-invoice-view.css',
        [],
        file_exists(plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-invoice-view.css')
            ? filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/css/account-invoice-view.css')
            : null
    );

    wp_enqueue_script(
        'html2pdf',
        'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js',
        [],
        '0.10.1',
        true
    );

    wp_enqueue_script(
        'siteauteur-account-invoice-view',
        plugin_dir_url(dirname(__FILE__)) . 'assets/js/account-invoice-view.js',
        ['html2pdf'],
        file_exists(plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-invoice-view.js')
            ? filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/js/account-invoice-view.js')
            : null,
        true
    );
}, 20);

add_action('template_redirect', function () {
    if (empty($_GET['sa_download_invoice'])) {
        return;
    }

    if (!is_user_logged_in()) {
        auth_redirect();
    }

    $user_id  = get_current_user_id();
    $order_id = !empty($_GET['order_id']) ? absint($_GET['order_id']) : 0;
    $source   = !empty($_GET['source']) ? sanitize_key(wp_unslash($_GET['source'])) : '';
    $doc_id   = !empty($_GET['doc_id']) ? sanitize_text_field(wp_unslash($_GET['doc_id'])) : '';

    if (!$order_id || !$source || !$doc_id) {
        wp_die('Facture introuvable.');
    }

    $order = sa_get_order_by_id_for_user($user_id, $order_id);

    if (!$order) {
        wp_die('Accès refusé.');
    }

    $user = wp_get_current_user();
    $invoice_data = false;

    if ($source === 'subscription_invoice') {
        $stripe_invoice = sa_get_stripe_invoice($doc_id);

        if (!$stripe_invoice || is_wp_error($stripe_invoice)) {
            wp_die('Facture Stripe introuvable.');
        }

        $invoice_data = sa_prepare_subscription_invoice_data($stripe_invoice, $user);
    }

    if ($source === 'extra_modification') {
        $doc = sa_find_extra_modification_invoice_doc($user_id, $order_id, $doc_id);

        if (!$doc) {
            wp_die('Facture de modification introuvable.');
        }

        $invoice_data = sa_prepare_extra_modification_invoice_data($doc, $user);
    }

    if (!$invoice_data) {
        wp_die('Facture introuvable.');
    }

    status_header(200);
    nocache_headers();

    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html($invoice_data['number']); ?></title>
        <?php wp_head(); ?>
    </head>
    <body class="siteauteur-invoice-page">
        <?php echo sa_render_local_invoice_html($invoice_data); ?>
        <?php wp_footer(); ?>
    </body>
    </html>
    <?php
    exit;
});