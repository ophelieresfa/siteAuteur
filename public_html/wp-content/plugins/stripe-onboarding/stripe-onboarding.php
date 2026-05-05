<?php
/**
 * Plugin Name: SiteAuteur Stripe Onboarding
 */

if (!defined('ABSPATH')) exit;

if (!defined('SA_STRIPE_SECRET_KEY')) {
    exit('SA_STRIPE_SECRET_KEY non définie dans wp-config.php');
}

require_once plugin_dir_path(__FILE__) . 'admin/admin-clients.php';

require_once plugin_dir_path(__FILE__) . 'includes/constants.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/assets.php';
require_once plugin_dir_path(__FILE__) . 'includes/stripe-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/local-invoices.php';
require_once plugin_dir_path(__FILE__) . 'includes/subscription-actions.php';
require_once plugin_dir_path(__FILE__) . 'includes/order-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/state-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/draft-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/ajax-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/fluentform-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/modification-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/account-functions.php';
require_once plugin_dir_path(__FILE__) . 'includes/account-actions.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes-account.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes-confirmation.php';
require_once plugin_dir_path(__FILE__) . 'includes/page-filters.php';
require_once plugin_dir_path(__FILE__) . 'includes/stripe-webhook.php';
require_once plugin_dir_path(__FILE__) . 'includes/contact-page.php';