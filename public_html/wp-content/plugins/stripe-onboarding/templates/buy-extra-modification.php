<?php if (!defined('ABSPATH')) exit; ?>

<?php
$current_user = wp_get_current_user();
$user_name    = $current_user->first_name ?: $current_user->display_name;

$hero_image_url = '/wp-content/uploads/2026/04/a_clean_and_modern_digital_illustration.png';

$offers = sa_get_extra_modification_offers();
?>

<div class="sa-buy-mod-page">
    <section class="sa-buy-mod-hero">
        <div class="sa-buy-mod-hero__content">
            <span class="sa-buy-mod-kicker">Modifications supplémentaires</span>
            <h1>Acheter des modifications supplémentaires</h1>
            <p>
                Mets à jour ton site quand tu le souhaites grâce à plusieurs offres :
                achat simple, packs économiques ou formule illimitée sur 30 jours.
            </p>

            <div class="sa-buy-mod-hero__badges">
                <span>✓ Achat immédiat</span>
                <span>✓ Compatible avec ton abonnement</span>
                <span>✓ Traitement rapide</span>
            </div>
        </div>

        <div class="sa-buy-mod-hero__visual">
            <img src="<?php echo esc_url($hero_image_url); ?>" alt="Acheter des modifications supplémentaires">
        </div>
    </section>

    <section class="sa-buy-mod-card-wrap">
        <div class="sa-buy-mod-grid">
            <?php foreach ($offers as $offer) : ?>
                <?php
                $buy_url = sa_get_extra_modification_checkout_url($offer['key']);

                $card_classes = 'sa-buy-mod-card';
                if ($offer['key'] === 'pack_5') {
                    $card_classes .= ' sa-buy-mod-card--featured';
                }
                ?>
                <div class="<?php echo esc_attr($card_classes); ?>">
                    <div class="sa-buy-mod-card__top">
                        <span class="sa-buy-mod-card__tag"><?php echo esc_html($offer['tag']); ?></span>
                        <h2><?php echo esc_html($offer['title']); ?></h2>
                        <div class="sa-buy-mod-price"><?php echo esc_html($offer['price_label']); ?></div>
                        <p class="sa-buy-mod-card__subtitle">
                            <?php echo esc_html($offer['subtitle']); ?>
                        </p>
                    </div>

                    <div class="sa-buy-mod-features">
                        <?php foreach ($offer['features'] as $feature) : ?>
                            <div><?php echo esc_html($feature); ?></div>
                        <?php endforeach; ?>
                    </div>

                    <div class="sa-buy-mod-cta">
                        <a href="<?php echo esc_url($buy_url); ?>" class="sa-buy-mod-btn">
                            Acheter cette offre
                        </a>
                    </div>

                    <p class="sa-buy-mod-note">
                        <?php echo esc_html($offer['note']); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="sa-buy-mod-faq">
        <h3>Questions fréquentes</h3>

        <div class="sa-buy-mod-faq__list">
            <div class="sa-buy-mod-faq__item">
                <strong>Que puis-je modifier avec ces offres ?</strong>
                <p>
                    Texte, image, lien, couverture, biographie, ajout d’un livre, correction de contenu
                    ou autre modification simple liée à ton formulaire.
                </p>
            </div>

            <div class="sa-buy-mod-faq__item">
                <strong>Est-ce que cela remplace mon abonnement ?</strong>
                <p>
                    Non. Ces offres sont des achats complémentaires qui s’ajoutent à ton abonnement actuel.
                </p>
            </div>

            <div class="sa-buy-mod-faq__item">
                <strong>En combien de temps ma demande sera-t-elle traitée ?</strong>
                <p>
                    En moyenne sous 24 à 48h ouvrées, selon la charge en cours.
                </p>
            </div>

            <div class="sa-buy-mod-faq__item">
                <strong>Comment fonctionne l’offre illimitée ?</strong>
                <p>
                    Elle active les modifications illimitées pendant 30 jours à partir du paiement validé.
                    Pendant cette période, aucun crédit n’est décrémenté.
                </p>
            </div>
        </div>
    </section>
</div>