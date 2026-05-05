<?php
if (!defined('ABSPATH')) exit;

/**
 * =========================
 * STRIPE API
 * =========================
 */

function sa_stripe_request($method, $endpoint, $body = []) {
    $url = 'https://api.stripe.com/v1/' . ltrim($endpoint, '/');

    $args = [
        'method'  => strtoupper($method),
        'timeout' => 30,
        'headers' => [
            'Authorization' => 'Bearer ' . SA_STRIPE_SECRET_KEY,
        ],
    ];

    if (!empty($body)) {
        $args['body'] = $body;
    }

    $response = wp_remote_request($url, $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $status_code = wp_remote_retrieve_response_code($response);
    $raw_body    = wp_remote_retrieve_body($response);
    $data        = json_decode($raw_body, true);

    if ($status_code < 200 || $status_code >= 300) {
        return new WP_Error(
            'sa_stripe_api_error',
            'Erreur API Stripe',
            [
                'status_code' => $status_code,
                'body'        => $data,
                'raw_body'    => $raw_body,
            ]
        );
    }

    return is_array($data) ? $data : [];
}

/**
 * =========================
 * CHECKOUT SESSION
 * =========================
 */

function sa_get_stripe_session($session_id) {
    if (!$session_id) {
        return false;
    }

    $result = sa_stripe_request('GET', 'checkout/sessions/' . rawurlencode($session_id));

    if (is_wp_error($result)) {
        return false;
    }

    return $result;
}

function sa_get_valid_paid_session() {
    if (empty($_GET['session_id'])) {
        return false;
    }

    $session_id = sanitize_text_field($_GET['session_id']);
    $session    = sa_get_stripe_session($session_id);

    if (!$session || empty($session['id'])) {
        return false;
    }

    if (($session['payment_status'] ?? '') !== 'paid') {
        return false;
    }

    return $session;
}

/**
 * =========================
 * SUBSCRIPTION
 * =========================
 */

function sa_get_stripe_subscription($subscription_id) {
    if (!$subscription_id) {
        return false;
    }

    $result = sa_stripe_request('GET', 'subscriptions/' . rawurlencode($subscription_id));

    if (is_wp_error($result)) {
        return false;
    }

    return $result;
}

function sa_get_stripe_subscription_start_date($subscription) {
    if (empty($subscription['start_date'])) {
        return null;
    }

    return (int) $subscription['start_date'];
}

function sa_get_stripe_subscription_period_end($subscription) {
    if (!empty($subscription['current_period_end'])) {
        return (int) $subscription['current_period_end'];
    }

    if (!empty($subscription['items']['data'][0]['current_period_end'])) {
        return (int) $subscription['items']['data'][0]['current_period_end'];
    }

    return null;
}

function sa_get_stripe_subscription_period_start($subscription) {
    if (!empty($subscription['current_period_start'])) {
        return (int) $subscription['current_period_start'];
    }

    if (!empty($subscription['items']['data'][0]['current_period_start'])) {
        return (int) $subscription['items']['data'][0]['current_period_start'];
    }

    return null;
}

function sa_get_stripe_subscription_status_label($status) {
    switch ($status) {
        case 'active':
            return 'Actif';

        case 'trialing':
            return 'Essai';

        case 'past_due':
            return 'Paiement en retard';

        case 'canceled':
            return 'Résilié';

        case 'incomplete':
            return 'Incomplet';

        case 'unpaid':
            return 'Impayé';

        case 'paused':
            return 'En pause';

        default:
            return ucfirst((string) $status);
    }
}

function sa_get_stripe_subscription_price_display($subscription) {
    $amount   = null;
    $currency = 'eur';
    $interval = 'month';

    if (isset($subscription['items']['data'][0]['price']['unit_amount'])) {
        $amount = (int) $subscription['items']['data'][0]['price']['unit_amount'];
    } elseif (isset($subscription['items']['data'][0]['plan']['amount'])) {
        $amount = (int) $subscription['items']['data'][0]['plan']['amount'];
    }

    if (!empty($subscription['items']['data'][0]['price']['currency'])) {
        $currency = strtolower($subscription['items']['data'][0]['price']['currency']);
    } elseif (!empty($subscription['currency'])) {
        $currency = strtolower($subscription['currency']);
    }

    if (!empty($subscription['items']['data'][0]['price']['recurring']['interval'])) {
        $interval = $subscription['items']['data'][0]['price']['recurring']['interval'];
    } elseif (!empty($subscription['items']['data'][0]['plan']['interval'])) {
        $interval = $subscription['items']['data'][0]['plan']['interval'];
    }

    if ($amount === null) {
        return '—';
    }

    $symbol = ($currency === 'eur') ? '€' : strtoupper($currency);
    $value  = number_format($amount / 100, 2, ',', ' ');

    $suffix = '/mois';
    if ($interval === 'year') {
        $suffix = '/an';
    } elseif ($interval === 'week') {
        $suffix = '/semaine';
    } elseif ($interval === 'day') {
        $suffix = '/jour';
    }

    return $value . ' ' . $symbol . ' ' . $suffix;
}

/**
 * =========================
 * INVOICES
 * =========================
 */

function sa_get_stripe_invoices($customer_id, $limit = 100) {
    if (!$customer_id) {
        return [];
    }

    $query = http_build_query([
        'customer' => sanitize_text_field($customer_id),
        'limit'    => absint($limit),
    ]);

    $result = sa_stripe_request('GET', 'invoices?' . $query);

    if (is_wp_error($result)) {
        return [];
    }

    if (empty($result['data']) || !is_array($result['data'])) {
        return [];
    }

    return $result['data'];
}

function sa_get_stripe_invoice($invoice_id) {
    if (!$invoice_id) {
        return false;
    }

    $result = sa_stripe_request('GET', 'invoices/' . rawurlencode($invoice_id));

    if (is_wp_error($result)) {
        return false;
    }

    return $result;
}

/**
 * =========================
 * PAYMENT INTENT
 * =========================
 */

function sa_get_stripe_payment_intent($payment_intent_id) {
    if (!$payment_intent_id) {
        return false;
    }

    $endpoint = 'payment_intents/' . rawurlencode($payment_intent_id) . '?expand[]=latest_charge';

    $result = sa_stripe_request('GET', $endpoint);

    if (is_wp_error($result)) {
        return false;
    }

    return $result;
}

function sa_get_stripe_charge($charge_id) {
    if (!$charge_id) {
        return false;
    }

    $result = sa_stripe_request('GET', 'charges/' . rawurlencode($charge_id));

    if (is_wp_error($result)) {
        return false;
    }

    return $result;
}

/**
 * =========================
 * CUSTOMER CHECKOUT SESSIONS
 * =========================
 */

function sa_get_stripe_checkout_sessions_for_customer($customer_id, $limit = 100) {
    if (!$customer_id) {
        return [];
    }

    $query = http_build_query([
        'customer' => $customer_id,
        'limit'    => absint($limit),
    ]);

    $result = sa_stripe_request('GET', 'checkout/sessions?' . $query);

    if (is_wp_error($result)) {
        return [];
    }

    if (empty($result['data']) || !is_array($result['data'])) {
        return [];
    }

    return $result['data'];
}

/**
 * =========================
 * NORMALIZATION
 * =========================
 */

function sa_normalize_subscription_invoice_for_account($invoice) {
    return [
        'source'      => 'subscription_invoice',
        'type'        => 'Abonnement',
        'created'     => !empty($invoice['created']) ? (int) $invoice['created'] : 0,
        'amount_paid' => isset($invoice['amount_paid']) ? (int) $invoice['amount_paid'] : 0,
        'status'      => !empty($invoice['status']) ? $invoice['status'] : '',
        'invoice_pdf' => !empty($invoice['invoice_pdf']) ? $invoice['invoice_pdf'] : '',
        'hosted_url'  => !empty($invoice['hosted_invoice_url']) ? $invoice['hosted_invoice_url'] : '',
        'stripe_id'   => !empty($invoice['id']) ? $invoice['id'] : '',
    ];
}

function sa_normalize_extra_modification_session_for_account($session) {
    $created      = !empty($session['created']) ? (int) $session['created'] : 0;
    $amount_total = isset($session['amount_total']) ? (int) $session['amount_total'] : 0;
    $invoice_pdf  = '';
    $hosted_url   = '';
    $status       = 'paid';

    if (!empty($session['invoice']) && is_string($session['invoice'])) {
        $invoice = sa_get_stripe_invoice($session['invoice']);

        if ($invoice && !is_wp_error($invoice)) {
            $invoice_pdf = !empty($invoice['invoice_pdf']) ? $invoice['invoice_pdf'] : '';
            $hosted_url  = !empty($invoice['hosted_invoice_url']) ? $invoice['hosted_invoice_url'] : '';
            $status      = !empty($invoice['status']) ? $invoice['status'] : 'paid';

            if (!empty($invoice['created'])) {
                $created = (int) $invoice['created'];
            }

            if (isset($invoice['amount_paid'])) {
                $amount_total = (int) $invoice['amount_paid'];
            }
        }
    }

    if (!$invoice_pdf && !empty($session['payment_intent']) && is_string($session['payment_intent'])) {
        $payment_intent = sa_get_stripe_payment_intent($session['payment_intent']);

        if ($payment_intent && !is_wp_error($payment_intent)) {
            if (!empty($payment_intent['latest_charge']['receipt_url'])) {
                $invoice_pdf = $payment_intent['latest_charge']['receipt_url'];
            }

            if (!$hosted_url && !empty($payment_intent['latest_charge']['receipt_url'])) {
                $hosted_url = $payment_intent['latest_charge']['receipt_url'];
            }

            if (
                (!$invoice_pdf || !$hosted_url) &&
                !empty($payment_intent['latest_charge']) &&
                is_string($payment_intent['latest_charge']) &&
                function_exists('sa_get_stripe_charge')
            ) {
                $charge = sa_get_stripe_charge($payment_intent['latest_charge']);

                if ($charge && !is_wp_error($charge) && !empty($charge['receipt_url'])) {
                    if (!$invoice_pdf) {
                        $invoice_pdf = $charge['receipt_url'];
                    }

                    if (!$hosted_url) {
                        $hosted_url = $charge['receipt_url'];
                    }
                }
            }

            if (!empty($payment_intent['status'])) {
                $status = ($payment_intent['status'] === 'succeeded') ? 'paid' : $payment_intent['status'];
            }

            if (isset($payment_intent['amount_received'])) {
                $amount_total = (int) $payment_intent['amount_received'];
            }
        }
    }

    return [
        'source'      => 'extra_modification',
        'type'        => 'Modification supplémentaire',
        'created'     => $created,
        'amount_paid' => $amount_total,
        'status'      => $status,
        'invoice_pdf' => $invoice_pdf,
        'hosted_url'  => $hosted_url,
        'stripe_id'   => !empty($session['id']) ? $session['id'] : '',
    ];
}

/**
 * =========================
 * EXTRA MODIFICATIONS
 * =========================
 */

function sa_get_extra_modification_documents($customer_id, $limit = 100) {
    if (!$customer_id) {
        return [];
    }

    $sessions = sa_get_stripe_checkout_sessions_for_customer($customer_id, $limit);

    if (empty($sessions)) {
        return [];
    }

    $documents = [];

    /*
     * Par défaut, on identifie les paiements ponctuels
     * de modification supplémentaire par un montant à 12,00 €.
     * Tu peux ajuster via filtre si besoin.
     */
    $allowed_amounts = apply_filters('sa_extra_modification_amounts', [1200]);

    foreach ($sessions as $session) {
        if (!is_array($session)) {
            continue;
        }

        $mode           = !empty($session['mode']) ? $session['mode'] : '';
        $payment_status = !empty($session['payment_status']) ? $session['payment_status'] : '';
        $amount_total   = isset($session['amount_total']) ? (int) $session['amount_total'] : 0;
        $subscription   = !empty($session['subscription']) ? $session['subscription'] : '';

        if ($mode !== 'payment') {
            continue;
        }

        if ($payment_status !== 'paid') {
            continue;
        }

        if (!empty($subscription)) {
            continue;
        }

        if (!in_array($amount_total, $allowed_amounts, true)) {
            continue;
        }

        $documents[] = sa_normalize_extra_modification_session_for_account($session);
    }

    return $documents;
}

/**
 * =========================
 * ALL BILLING DOCUMENTS
 * =========================
 */

function sa_get_all_account_billing_documents($subscription_id, $limit = 100) {
    if (!$subscription_id) {
        return [];
    }

    $subscription = sa_get_stripe_subscription($subscription_id);

    if (!$subscription || is_wp_error($subscription)) {
        return [];
    }

    $customer_id = !empty($subscription['customer'])
        ? sanitize_text_field($subscription['customer'])
        : '';

    if (!$customer_id) {
        return [];
    }

    /*
     * Important :
     * on récupère toutes les factures du CLIENT Stripe,
     * pas seulement celles de l'abonnement actuel.
     *
     * Comme ça, si l'ancien abonnement a été résilié puis remplacé
     * par un nouvel abonnement, les anciennes factures restent visibles.
     */
    $subscription_invoices = sa_get_stripe_invoices($customer_id, $limit);
    $subscription_docs     = [];

    foreach ($subscription_invoices as $invoice) {
        if (is_array($invoice)) {
            $subscription_docs[] = sa_normalize_subscription_invoice_for_account($invoice);
        }
    }

    $extra_docs = sa_get_extra_modification_documents($customer_id, $limit);

    $all_docs = array_merge($subscription_docs, $extra_docs);

    /*
     * Évite les doublons si un document remonte deux fois.
     */
    $unique_docs = [];

    foreach ($all_docs as $doc) {
        $source    = !empty($doc['source']) ? $doc['source'] : '';
        $stripe_id = !empty($doc['stripe_id']) ? $doc['stripe_id'] : '';
        $created   = !empty($doc['created']) ? (int) $doc['created'] : 0;
        $amount    = isset($doc['amount_paid']) ? (int) $doc['amount_paid'] : 0;

        $key = $source . '|' . $stripe_id . '|' . $created . '|' . $amount;

        if (!$stripe_id) {
            $key = $source . '|' . $created . '|' . $amount;
        }

        $unique_docs[$key] = $doc;
    }

    $all_docs = array_values($unique_docs);

    usort($all_docs, function ($a, $b) {
        return ((int) ($b['created'] ?? 0)) <=> ((int) ($a['created'] ?? 0));
    });

    return $all_docs;
}

/**
 * =========================
 * LABELS
 * =========================
 */

function sa_get_invoice_status_label($status) {
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

        case 'succeeded':
            return 'Payée';

        default:
            return ucfirst((string) $status);
    }
}

/**
 * =========================
 * CANCEL SUBSCRIPTION
 * =========================
 */

function sa_cancel_stripe_subscription_at_period_end($subscription_id) {
    if (!$subscription_id) {
        return new WP_Error('sa_missing_subscription_id', 'Abonnement Stripe manquant.');
    }

    $result = sa_stripe_request(
        'POST',
        'subscriptions/' . rawurlencode($subscription_id),
        [
            'cancel_at_period_end' => 'true',
        ]
    );

    if (is_wp_error($result)) {
        return $result;
    }

    if (empty($result['id'])) {
        return new WP_Error(
            'sa_cancel_subscription_failed',
            'Impossible de programmer la résiliation.'
        );
    }

    return $result;
}

function sa_resume_stripe_subscription($subscription_id) {
    if (!$subscription_id) {
        return new WP_Error('sa_missing_subscription_id', 'Abonnement Stripe manquant.');
    }

    $result = sa_stripe_request(
        'POST',
        'subscriptions/' . rawurlencode($subscription_id),
        [
            'cancel_at_period_end' => 'false',
        ]
    );

    if (is_wp_error($result)) {
        return $result;
    }

    if (empty($result['id'])) {
        return new WP_Error(
            'sa_resume_subscription_failed',
            'Impossible de reprendre l’abonnement.'
        );
    }

    return $result;
}

/**
 * =========================
 * REPRISE ABONNEMENT DÉJÀ ARRÊTÉ
 * =========================
 */

function sa_get_subscription_price_id_for_resume($subscription) {
    if (!empty($subscription['items']['data'][0]['price']['id'])) {
        return sanitize_text_field($subscription['items']['data'][0]['price']['id']);
    }

    if (!empty($subscription['items']['data'][0]['plan']['id'])) {
        return sanitize_text_field($subscription['items']['data'][0]['plan']['id']);
    }

    return '';
}

function sa_create_resume_canceled_subscription_checkout_session($order) {
    if (!$order || empty($order->id) || empty($order->wp_user_id)) {
        return new WP_Error('sa_missing_order', 'Commande introuvable.');
    }

    $user_id  = (int) $order->wp_user_id;
    $order_id = (int) $order->id;

    $user = get_user_by('id', $user_id);

    if (!$user || empty($user->user_email)) {
        return new WP_Error('sa_missing_user', 'Utilisateur introuvable.');
    }

    $old_subscription_id = !empty($order->stripe_subscription_id)
        ? sanitize_text_field($order->stripe_subscription_id)
        : '';

    if (!$old_subscription_id) {
        return new WP_Error('sa_missing_subscription', 'Ancien abonnement Stripe introuvable.');
    }

    $old_subscription = sa_get_stripe_subscription($old_subscription_id);

    if (!$old_subscription || is_wp_error($old_subscription)) {
        return new WP_Error('sa_subscription_not_found', 'Impossible de récupérer l’ancien abonnement Stripe.');
    }

    $status = !empty($old_subscription['status'])
        ? sanitize_text_field($old_subscription['status'])
        : '';

    $stopped_statuses = ['canceled', 'unpaid', 'paused', 'incomplete_expired'];

    if (!in_array($status, $stopped_statuses, true)) {
        return new WP_Error('sa_subscription_not_stopped', 'Cet abonnement n’est pas arrêté.');
    }

    $price_id = sa_get_subscription_price_id_for_resume($old_subscription);

    if (!$price_id) {
        return new WP_Error('sa_missing_price_id', 'Price ID Stripe introuvable.');
    }

    $customer_id = !empty($order->stripe_customer_id)
        ? sanitize_text_field($order->stripe_customer_id)
        : '';

    if (!$customer_id && !empty($old_subscription['customer'])) {
        $customer_id = sanitize_text_field($old_subscription['customer']);
    }

    $success_url = add_query_arg(
        [
            'subscription_notice' => 'resume_checkout_success',
            'session_id'          => '{CHECKOUT_SESSION_ID}',
        ],
        home_url('/mon-abonnement/')
    );

    $cancel_url = add_query_arg(
        [
            'subscription_notice' => 'resume_checkout_cancel',
        ],
        home_url('/mon-abonnement/')
    );

    $body = [
        'mode'                    => 'subscription',
        'success_url'             => $success_url,
        'cancel_url'              => $cancel_url,
        'allow_promotion_codes'   => 'true',
        'line_items[0][price]'    => $price_id,
        'line_items[0][quantity]' => 1,

        'metadata[sa_purchase_type]' => 'resume_canceled_subscription',
        'metadata[sa_user_id]'       => (string) $user_id,
        'metadata[sa_order_id]'      => (string) $order_id,

        'subscription_data[metadata][sa_purchase_type]' => 'resume_canceled_subscription',
        'subscription_data[metadata][sa_user_id]'       => (string) $user_id,
        'subscription_data[metadata][sa_order_id]'      => (string) $order_id,
    ];

    if ($customer_id) {
        $body['customer'] = $customer_id;
    } else {
        $body['customer_email'] = sanitize_email($user->user_email);
    }

    $session = sa_stripe_request('POST', 'checkout/sessions', $body);

    if (is_wp_error($session)) {
        return $session;
    }

    if (empty($session['url'])) {
        return new WP_Error('sa_missing_checkout_url', 'URL Stripe Checkout introuvable.');
    }

    return $session;
}

function sa_update_order_after_resume_canceled_subscription($user_id, $order_id, $session) {
    if (!$user_id || !$order_id || !$session) {
        return false;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'siteauteur_orders';

    $session_id      = !empty($session->id) ? sanitize_text_field($session->id) : '';
    $customer_id     = !empty($session->customer) ? sanitize_text_field($session->customer) : '';
    $subscription_id = !empty($session->subscription) ? sanitize_text_field($session->subscription) : '';
    $payment_status  = !empty($session->payment_status) ? sanitize_text_field($session->payment_status) : 'paid';
    $amount_total    = isset($session->amount_total) ? (int) $session->amount_total : 0;
    $currency        = !empty($session->currency) ? sanitize_text_field($session->currency) : '';

    if (!$session_id || !$subscription_id) {
        return false;
    }

    $wpdb->update(
        $table,
        [
            'stripe_session_id'      => $session_id,
            'stripe_customer_id'     => $customer_id,
            'stripe_subscription_id' => $subscription_id,
            'payment_status'         => $payment_status,
            'amount_total'           => $amount_total,
            'currency'               => $currency,
        ],
        [
            'id'         => (int) $order_id,
            'wp_user_id' => (int) $user_id,
        ],
        [
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
        ],
        [
            '%d',
            '%d',
        ]
    );

    $current_state = sa_get_order_state($user_id, $order_id);

    sa_update_order_meta($user_id, $order_id, 'state', 'site_live');
    sa_update_order_meta($user_id, $order_id, 'subscription_resumed_at', time());
    sa_update_order_meta($user_id, $order_id, 'previous_subscription_replaced', 1);
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_at_period_end', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_cancel_scheduled_email_sent', 0);
    sa_update_order_meta($user_id, $order_id, 'subscription_stopped_email_sent', 0);

    if ($current_state !== 'site_live' && function_exists('sa_send_project_reactivated_email')) {
        sa_send_project_reactivated_email($user_id, $order_id);
    }

    return true;
}

/**
 * =========================
 * BILLING PORTAL
 * =========================
 */

function sa_create_stripe_billing_portal_session($customer_id, $return_url = '') {
    if (!$customer_id) {
        return new WP_Error('sa_missing_customer', 'Client Stripe manquant.');
    }

    if (!$return_url) {
        $return_url = home_url('/mon-abonnement/');
    }

    $result = sa_stripe_request('POST', 'billing_portal/sessions', [
        'customer'   => $customer_id,
        'return_url' => esc_url_raw($return_url),
    ]);

    if (is_wp_error($result)) {
        return $result;
    }

    if (empty($result['url'])) {
        return new WP_Error('sa_billing_portal_missing_url', 'URL du portail Stripe introuvable.');
    }

    return $result;
}

/**
 * =========================
 * EXTRA MODIFICATION OFFERS
 * =========================
 */

function sa_is_stripe_test_mode() {
    return (defined('SA_STRIPE_SECRET_KEY') && strpos(SA_STRIPE_SECRET_KEY, 'sk_test_') === 0);
}

function sa_get_extra_modification_offers() {
    $is_test = sa_is_stripe_test_mode();

    return [
        'simple' => [
            'key'             => 'simple',
            'tag'             => 'Offre simple',
            'title'           => '1 modification',
            'price_label'     => '12€',
            'price_cents'     => 1200,
            'quantity'        => 1,
            'is_unlimited'    => false,
            'duration_days'   => 0,
            'note'            => 'Cette option ajoute 1 modification supplémentaire à ton espace client.',
            'subtitle'        => 'Une solution simple pour mettre à jour ton site sans attendre.',
            'features'        => [
                '✓ 1 modification d’un champ du formulaire',
                '✓ Texte, image, lien, couleur ou contenu simple',
                '✓ Demande traitée sous 24 à 48h en moyenne',
                '✓ Ton abonnement reste inchangé',
            ],
            'stripe_price_id' => $is_test
                ? 'price_1TLk7zRqRBNhjLwHwzit9zd9'
                : 'price_1TLk1xRqRBNhjLwH4BRuqarA',
        ],

        'pack_5' => [
            'key'             => 'pack_5',
            'tag'             => 'Pack avantage',
            'title'           => '5 modifications',
            'price_label'     => '49€',
            'price_cents'     => 4900,
            'quantity'        => 5,
            'is_unlimited'    => false,
            'duration_days'   => 0,
            'note'            => 'Cette option ajoute 5 modifications supplémentaires à ton espace client.',
            'subtitle'        => 'Le meilleur choix pour plusieurs ajustements rapides.',
            'features'        => [
                '✓ 5 modifications supplémentaires',
                '✓ Prix plus avantageux que l’achat unitaire',
                '✓ Idéal pour plusieurs mises à jour de contenu',
                '✓ Ton abonnement reste inchangé',
            ],
            'stripe_price_id' => $is_test
                ? 'price_1TOhRWRqRBNhjLwHfDCJn1pr'
                : 'price_1TOhNuRqRBNhjLwHeZAAehKW',
        ],

        'pack_10' => [
            'key'             => 'pack_10',
            'tag'             => 'Pack pro',
            'title'           => '10 modifications',
            'price_label'     => '89€',
            'price_cents'     => 8900,
            'quantity'        => 10,
            'is_unlimited'    => false,
            'duration_days'   => 0,
            'note'            => 'Cette option ajoute 10 modifications supplémentaires à ton espace client.',
            'subtitle'        => 'Parfait pour refaire plusieurs sections du site en une période courte.',
            'features'        => [
                '✓ 10 modifications supplémentaires',
                '✓ Tarif dégressif plus intéressant',
                '✓ Idéal pour gros rafraîchissement de contenu',
                '✓ Ton abonnement reste inchangé',
            ],
            'stripe_price_id' => $is_test
                ? 'price_1TOhRwRqRBNhjLwHyPlHqzQe'
                : 'price_1TOhOnRqRBNhjLwHpjMJJoYf',
        ],

        'unlimited_30d' => [
            'key'             => 'unlimited_30d',
            'tag'             => 'Illimité 30 jours',
            'title'           => 'Modifications illimitées',
            'price_label'     => '149€',
            'price_cents'     => 14900,
            'quantity'        => 0,
            'is_unlimited'    => true,
            'duration_days'   => 30,
            'note'            => 'Cette option active les modifications illimitées pendant 30 jours.',
            'subtitle'        => 'Pour les auteurs qui veulent faire évoluer leur site intensivement pendant un mois.',
            'features'        => [
                '✓ Modifications illimitées pendant 30 jours',
                '✓ Idéal pour refonte ou gros ajustements',
                '✓ Aucun décrément pendant la période',
                '✓ Ton abonnement principal reste inchangé',
            ],
            'stripe_price_id' => $is_test
                ? 'price_1TOhSJRqRBNhjLwHiT7sDziO'
                : 'price_1TOhQbRqRBNhjLwH81cPY2rh',
        ],
    ];
}

function sa_get_extra_modification_offer($offer_key) {
    $offers = sa_get_extra_modification_offers();

    return $offers[$offer_key] ?? false;
}

function sa_create_extra_modification_checkout_session($offer_key, $order) {
    $offer = sa_get_extra_modification_offer($offer_key);

    if (!$offer) {
        return new WP_Error('sa_invalid_offer', 'Offre invalide.');
    }

    if (empty($offer['stripe_price_id'])) {
        return new WP_Error('sa_missing_price_id', 'Price ID Stripe manquant.');
    }

    $user = wp_get_current_user();

    if (!$user || empty($user->user_email)) {
        return new WP_Error('sa_missing_user', 'Utilisateur introuvable.');
    }

    $order_id = !empty($order->id) ? (int) $order->id : 0;
    $user_id  = get_current_user_id();

    // IMPORTANT :
    // un achat de modifications supplémentaires ne doit PAS revenir
    // sur la page de confirmation de commande principale
    $success_url = add_query_arg(
        [
            'session_id' => '{CHECKOUT_SESSION_ID}',
            'order_id'   => $order_id,
        ],
        home_url('/merci-modification/')
    );

    $cancel_url = home_url('/acheter-une-modification/');

    $body = [
        'mode'                    => 'payment',
        'success_url'             => $success_url,
        'cancel_url'              => $cancel_url,
        'customer_email'          => $user->user_email,
        'allow_promotion_codes'   => 'true',
        'line_items[0][price]'    => $offer['stripe_price_id'],
        'line_items[0][quantity]' => 1,

        'metadata[sa_purchase_type]'         => 'extra_modification',
        'metadata[sa_offer_key]'             => $offer['key'],
        'metadata[sa_modification_quantity]' => (string) (int) $offer['quantity'],
        'metadata[sa_is_unlimited]'          => !empty($offer['is_unlimited']) ? '1' : '0',
        'metadata[sa_unlimited_days]'        => (string) (int) $offer['duration_days'],
        'metadata[sa_order_id]'              => (string) $order_id,
        'metadata[sa_user_id]'               => (string) $user_id,
    ];

    $session = sa_stripe_request('POST', 'checkout/sessions', $body);

    if (is_wp_error($session)) {
        return $session;
    }

    if (empty($session['url'])) {
        return new WP_Error('sa_missing_checkout_url', 'URL de paiement Stripe introuvable.');
    }

    return $session;
}

function sa_get_extra_modification_allowed_amounts() {
    $offers = sa_get_extra_modification_offers();
    $amounts = [];

    foreach ($offers as $offer) {
        if (!empty($offer['price_cents'])) {
            $amounts[] = (int) $offer['price_cents'];
        }
    }

    return array_values(array_unique($amounts));
}

/**
 * =========================
 * URL ACHAT OFFRE MODIFICATION
 * =========================
 */

function sa_get_extra_modification_checkout_url($offer_key) {
    return add_query_arg([
        'sa_action' => 'buy_modification_offer',
        'offer'     => sanitize_key($offer_key),
    ], home_url('/'));
}

/**
 * =========================
 * REDIRECTION VERS STRIPE
 * =========================
 */

add_action('template_redirect', function () {
    if (empty($_GET['sa_action']) || $_GET['sa_action'] !== 'buy_modification_offer') {
        return;
    }

    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/login/'));
        exit;
    }

    $offer_key = !empty($_GET['offer']) ? sanitize_key($_GET['offer']) : '';
    if (!$offer_key) {
        wp_safe_redirect(home_url('/acheter-une-modification/'));
        exit;
    }

    if (!function_exists('sa_get_user_paid_orders')) {
        wp_safe_redirect(home_url('/account/'));
        exit;
    }

    $user_id = get_current_user_id();

    if (function_exists('sa_get_account_orders_sorted')) {
        $orders = sa_get_account_orders_sorted($user_id);
    } else {
        $orders = sa_get_user_paid_orders($user_id);
    }

    if (empty($orders) || !is_array($orders)) {
        wp_safe_redirect(home_url('/account/'));
        exit;
    }

    $order = $orders[0];

    if (!$order || empty($order->id)) {
        wp_safe_redirect(home_url('/account/'));
        exit;
    }

    $session = sa_create_extra_modification_checkout_session($offer_key, $order);

    if (is_wp_error($session)) {
        wp_die(
            '<strong>Erreur Stripe :</strong><br>' . esc_html($session->get_error_message()),
            'Erreur Stripe'
        );
    }

    if (empty($session['url'])) {
        wp_die('URL Stripe introuvable.', 'Erreur Stripe');
    }

    wp_redirect($session['url']);
    exit;
}, 1);