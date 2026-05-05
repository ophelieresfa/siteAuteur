<?php
if (!defined('ABSPATH')) exit;
?>

<div class="sa-confirmation-not-found">

    <div class="sa-confirmation-card">

        <div class="sa-confirmation-icon">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none">
                <circle cx="12" cy="12" r="10" stroke="#46b692" stroke-width="2"/>
                <path d="M8 8L16 16M16 8L8 16" stroke="#46b692" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="sa-confirmation-title">
            Commande introuvable
        </h1>

        <p class="sa-confirmation-subtitle">
            Cette page est accessible uniquement après validation du paiement.
        </p>

        <div class="sa-confirmation-alert">
            <strong>Votre commande n’a pas été trouvée</strong><br>
            Le paiement n’a pas encore été validé ou la session a expiré.<br>
            Si vous venez d’effectuer un paiement, patientez quelques secondes puis actualisez la page.
        </div>

        <div class="sa-confirmation-actions">
            <a href="<?php echo site_url('/account'); ?>" class="sa-btn sa-btn-primary">
                Voir mon compte
            </a>

            <a href="<?php echo site_url('/'); ?>" class="sa-btn sa-btn-secondary">
                Retour à l'accueil
            </a>
        </div>

        <div class="sa-confirmation-security">
            <span class="sa-security-icon">🔒</span>
            Paiement sécurisé via <strong>Stripe</strong>
        </div>

    </div>

    <div class="sa-support-box">

        <div class="sa-support-text">
            <h3>Besoin d'aide ?</h3>
            <p>Notre équipe peut vérifier votre commande rapidement.</p>
        </div>

        <a href="<?php echo site_url('/contact'); ?>" class="sa-btn sa-btn-contact">
            Contacter le support
        </a>

    </div>

</div>