<?php if (!defined('ABSPATH')) exit; ?>

<?php
$is_account_page          = is_page('account');
$is_subscription_page     = is_page('mon-abonnement');
$is_invoices_page         = is_page('mes-factures');
$is_contact_page          = is_page('contact');
$is_change_password_page  = is_page('modifier-mon-mot-de-passe');
$is_delete_account_page   = is_page('supprimer-mon-compte');

$is_account_management_open = $is_change_password_page || $is_delete_account_page;

/*
|--------------------------------------------------------------------------
| Afficher "Mon abonnement" et "Mes factures" seulement si une commande
| a déjà été passée (commande payée).
|--------------------------------------------------------------------------
*/
$show_order_links = false;

if (is_user_logged_in() && function_exists('sa_get_user_paid_orders')) {
    $user_id = get_current_user_id();
    $orders  = sa_get_user_paid_orders($user_id);

    $show_order_links = !empty($orders);
}
?>

<aside class="sa-account-sidebar">
    <div class="sa-account-sidebar-card">
        <div class="sa-account-sidebar-title">Navigation</div>

        <ul class="sa-account-menu">
            <li class="<?php echo $is_account_page ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/account/')); ?>">
                    <span>Tableau de bord</span>
                </a>
            </li>

            <?php if ($show_order_links) : ?>
                <li class="<?php echo $is_subscription_page ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/mon-abonnement/')); ?>">
                        <span>Mon abonnement</span>
                    </a>
                </li>

                <li class="<?php echo $is_invoices_page ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/mes-factures/')); ?>">
                        <span>Mes factures</span>
                    </a>
                </li>
            <?php endif; ?>

            <li class="sa-account-menu-group <?php echo $is_account_management_open ? 'is-open' : ''; ?>">
                <button type="button" class="sa-account-submenu-toggle" aria-expanded="<?php echo $is_account_management_open ? 'true' : 'false'; ?>">
                    <span>Gestion du compte</span>
                    <span class="sa-account-submenu-arrow"></span>
                </button>

                <ul class="sa-account-submenu">
                    <li class="<?php echo $is_change_password_page ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/modifier-mon-mot-de-passe/')); ?>">
                            Modifier mon mot de passe
                        </a>
                    </li>

                    <li class="<?php echo $is_delete_account_page ? 'active' : ''; ?>">
                        <a href="<?php echo esc_url(home_url('/supprimer-mon-compte/')); ?>">
                            Supprimer définitivement mon compte
                        </a>
                    </li>
                </ul>
            </li>

            <li class="<?php echo $is_contact_page ? 'active' : ''; ?>">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>">
                    <span>Support</span>
                </a>
            </li>
        </ul>
    </div>
</aside>