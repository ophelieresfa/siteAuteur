<?php
if (!defined('ABSPATH')) exit;

get_header();

$home_url    = home_url('/');
$demo_url    = home_url('/demo/');
$tarif_url   = home_url('/tarif/');
$faq_url     = home_url('/faq/');
$contact_url = home_url('/contact/');
$account_url = home_url('/account/');
$logout_url  = wp_logout_url(home_url('/login/'));
?>

<main class="sa-legal-page">

    <section class="sa-legal-hero">
        <div class="sa-legal-container">

            <div class="sa-legal-badge">
                <span class="sa-legal-badge-icon">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" />
                        <path d="M9 12L11 14L15 10" />
                    </svg>
                </span>
                Juridique
            </div>

            <h1>Mentions légales</h1>

            <p>
                Ces mentions légales vous permettent d’identifier l’éditeur du site,
                son hébergeur ainsi que les conditions d’utilisation de nos services.
            </p>

        </div>
    </section>

    <section class="sa-legal-content">
        <div class="sa-legal-container">

            <div class="sa-legal-grid sa-legal-grid-two">

                <article class="sa-legal-card">
                    <div class="sa-legal-card-head">
                        <div class="sa-legal-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 12.2C14.5 12.2 16.5 10.2 16.5 7.7C16.5 5.2 14.5 3.2 12 3.2C9.5 3.2 7.5 5.2 7.5 7.7C7.5 10.2 9.5 12.2 12 12.2Z" />
                                <path d="M4.8 20.8C5.4 16.9 8.4 14.8 12 14.8C15.6 14.8 18.6 16.9 19.2 20.8" />
                            </svg>
                        </div>

                        <h2>Éditeur du site</h2>
                    </div>

                    <p>
                        Le site <strong>SiteAuteur</strong> est édité par :
                    </p>

                    <ul>
                        <li><strong>Nom :</strong> RESFA Ophélie Valérie</li>
                        <li><strong>Statut :</strong> Auto-entrepreneur</li>
                        <li><strong>SIRET :</strong> 849 360 425 00029</li>
                        <li><strong>Adresse :</strong> Sin-le-Noble, France</li>
                        <li><strong>Email :</strong> <a href="mailto:admin@siteauteur.fr">admin@siteauteur.fr</a></li>
                    </ul>

                    <div class="sa-legal-separator"></div>

                    <p>
                        <strong>Directeur de la publication :</strong><br>
                        RESFA Ophélie Valérie
                    </p>
                </article>

                <article class="sa-legal-card">
                    <div class="sa-legal-card-head">
                        <div class="sa-legal-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M7.2 18.5H17.5C20 18.5 22 16.5 22 14C22 11.6 20.1 9.6 17.7 9.5C16.9 6.3 14.1 4 10.8 4C7.2 4 4.2 6.7 3.8 10.2C1.7 10.8 0.5 12.5 0.5 14.4C0.5 16.7 2.4 18.5 4.7 18.5H7.2Z" />
                                <path d="M12 10.6V16.2" />
                                <path d="M9.7 13.4L12 10.6L14.3 13.4" />
                            </svg>
                        </div>

                        <h2>Hébergement</h2>
                    </div>

                    <p>
                        Le site <strong>SiteAuteur</strong> est hébergé par :
                    </p>

                    <ul>
                        <li><strong>o2switch</strong></li>
                        <li>Chemin des Pardiaux</li>
                        <li>63000 Clermont-Ferrand</li>
                        <li>France</li>
                    </ul>

                    <div class="sa-legal-separator"></div>

                    <p>
                        <strong>Site internet :</strong>
                        <a href="https://www.o2switch.fr" target="_blank" rel="noopener noreferrer">
                            https://www.o2switch.fr
                        </a>
                    </p>
                </article>

            </div>

            <article class="sa-legal-card sa-legal-card-wide">
                <div class="sa-legal-card-head">
                    <div class="sa-legal-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 3H14L19 8V21H6V3Z" />
                            <path d="M14 3V8H19" />
                            <path d="M9 13H15" />
                            <path d="M9 16H13.5" />
                        </svg>
                    </div>

                    <h2>Propriété intellectuelle</h2>
                </div>

                <p>
                    L’ensemble des éléments présents sur le site <strong>SiteAuteur</strong>, notamment les textes,
                    images, graphismes, logo, structure et design, sont protégés par le droit de la propriété intellectuelle.
                </p>

                <p>
                    Toute reproduction, représentation, modification, publication, adaptation de tout ou partie des éléments
                    du site, quel que soit le moyen ou le procédé utilisé, est interdite, sauf autorisation écrite préalable.
                </p>

                <p>
                    Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient sera considérée
                    comme constitutive d’une contrefaçon et poursuivie conformément aux dispositions légales.
                </p>
            </article>

            <div class="sa-legal-grid sa-legal-grid-two">

                <article class="sa-legal-card">
                    <div class="sa-legal-card-head">
                        <div class="sa-legal-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M12 2.8L20 6.5V11.5C20 16.2 16.7 20 12 21.4C7.3 20 4 16.2 4 11.5V6.5L12 2.8Z" />
                                <path d="M12 7.4V12.4" />
                                <path d="M12 16.4H12.01" />
                            </svg>
                        </div>

                        <h2>Responsabilité</h2>
                    </div>

                    <p>
                        Les informations diffusées sur le site <strong>SiteAuteur</strong> sont fournies à titre informatif.
                    </p>

                    <p>
                        RESFA Ophélie Valérie s’efforce de fournir des informations aussi précises que possible.
                    </p>

                    <p>
                        Toutefois, elle ne pourra être tenue responsable des omissions, inexactitudes ou carences
                        dans la mise à jour.
                    </p>
                </article>

                <article class="sa-legal-card">
                    <div class="sa-legal-card-head">
                        <div class="sa-legal-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M10 6H6.8C5.8 6 5 6.8 5 7.8V17.2C5 18.2 5.8 19 6.8 19H16.2C17.2 19 18 18.2 18 17.2V14" />
                                <path d="M13 5H19V11" />
                                <path d="M19 5L11 13" />
                            </svg>
                        </div>

                        <h2>Liens externes</h2>
                    </div>

                    <p>
                        Le site <strong>SiteAuteur</strong> peut contenir des liens vers d’autres sites internet.
                    </p>

                    <p>
                        RESFA Ophélie Valérie ne peut être tenue responsable du contenu de ces sites externes.
                    </p>
                </article>

            </div>

            <article class="sa-legal-contact">
                <div class="sa-legal-card-head">
                    <div class="sa-legal-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 6.75A1.75 1.75 0 0 1 4.75 5H19.25A1.75 1.75 0 0 1 21 6.75V17.25A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z" />
                            <path d="M4 7L12 13L20 7" />
                        </svg>
                    </div>

                    <div>
                        <h2>Contact</h2>
                        <p>
                            Pour toute question concernant les mentions légales du site, vous pouvez nous contacter :
                        </p>
                    </div>
                </div>

                <div class="sa-legal-contact-box">
                    <div class="sa-legal-contact-icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 6.75A1.75 1.75 0 0 1 4.75 5H19.25A1.75 1.75 0 0 1 21 6.75V17.25A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z" />
                            <path d="M4 7L12 13L20 7" />
                        </svg>
                    </div>

                    <div>
                        <strong>Email</strong>
                        <a href="mailto:admin@siteauteur.fr">admin@siteauteur.fr</a>
                    </div>
                </div>
            </article>

        </div>
    </section>

</main>

<?php
get_footer();