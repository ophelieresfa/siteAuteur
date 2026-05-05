<?php if (!defined('ABSPATH')) exit; ?>

<div class="sa-account-premium sa-account-no-project-page">

    <div class="sa-account-no-project-hero">
        <div class="sa-account-hero-left">
            <span class="sa-account-kicker">Espace client</span>

            <div class="sa-user-hello">
                <h1>
                    Bonjour
                    <?php echo esc_html(wp_get_current_user()->first_name ?: wp_get_current_user()->display_name); ?>
                </h1>
            </div>

            <p class="sa-account-hero-text">Bienvenue sur ton espace SiteAuteur</p>
            <span class="sa-account-hero-subtext">Commence par créer ton premier site d'auteur</span>
        </div>

        <div class="sa-account-help-card">
            <h3>Besoin d’aide ?</h3>
            <p>Notre équipe est là pour t’accompagner dans la création de ton premier site.</p>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="sa-account-help-btn">
                Contacter le support
            </a>
        </div>
    </div>

    <div class="sa-account-body">

        <?php echo sa_render_template('partials/account-sidebar'); ?>

        <main class="sa-account-main-premium">
            <div class="sa-dashboard">

                <div class="sa-dashboard-card">
                    <div class="sa-card-left">
                        <h2>Crée ton premier site</h2>
                        <p class="sa-main-text">Tu n'as encore aucun projet.</p>
                        <p class="sa-muted">Commence maintenant pour lancer ton site.</p>

                        <a href="<?php echo esc_url(home_url('/recapitulatif-commande/')); ?>" class="sa-btn-primary">
                            Créer mon site
                        </a>

                        <ul class="sa-check">
                            <li><img src="/wp-content/uploads/2026/03/accept.png" alt="">Mise en ligne en 7 jours</li>
                            <li><img src="/wp-content/uploads/2026/03/accept.png" alt="">Aucune compétence technique</li>
                            <li><img src="/wp-content/uploads/2026/03/accept.png" alt="">Support inclus</li>
                        </ul>
                    </div>

                    <div class="sa-card-right">
                        <img src="/wp-content/uploads/2026/04/4ad29232-e0f7-4153-8516-1b2136abfd332.png" alt="Illustration SiteAuteur">
                    </div>
                </div>

                <h2 class="sa-section-title">Comment ça marche</h2>

                <div class="sa-steps">
                    <div class="sa-step">
                        <div class="sa-block-left">
                            <div class="sa-step-number">1</div>
                            <div class="sa-step-content">
                                <h3>Remplis le formulaire</h3>
                                <p>Complète le formulaire de création de site avec les informations demandées.</p>
                            </div>
                        </div>
                        <div class="sa-block-right">
                            <img src="/wp-content/uploads/2026/04/letter.png" alt="">
                        </div>
                    </div>

                    <div class="sa-step">
                        <div class="sa-block-left">
                            <div class="sa-step-number">2</div>
                            <div class="sa-step-content">
                                <h3>Nous créons ton site</h3>
                                <p>Nous développons ton site professionnel à partir de tes contenus.</p>
                            </div>
                        </div>
                        <div class="sa-block-right">
                            <img src="/wp-content/uploads/2026/04/computer-settings.png" alt="">
                        </div>
                    </div>

                    <div class="sa-step">
                        <div class="sa-block-left">
                            <div class="sa-step-number">3</div>
                            <div class="sa-step-content">
                                <h3>Ton site est en ligne</h3>
                                <p>Ton site est prêt à accueillir tes lecteurs.</p>
                            </div>
                        </div>
                        <div class="sa-block-right">
                            <img src="/wp-content/uploads/2026/04/shuttle.png" alt="">
                        </div>
                    </div>
                </div>

                <h2 class="sa-section-title">Tes fonctionnalités</h2>

                <div class="sa-features">
                    <div class="sa-feature">
                        <div class="sa-feature-icon"><img src="/wp-content/uploads/2026/04/site-book.png" alt=""></div>
                        <h3>Page livre</h3>
                        <p>Présente ton livre clairement.</p>
                    </div>

                    <div class="sa-feature">
                        <div class="sa-feature-icon"><img src="/wp-content/uploads/2026/04/email.png" alt=""></div>
                        <h3>Capture emails</h3>
                        <p>Récupère les emails de tes lecteurs.</p>
                    </div>

                    <div class="sa-feature">
                        <div class="sa-feature-icon"><img src="/wp-content/uploads/2026/04/user.png" alt=""></div>
                        <h3>Page auteur</h3>
                        <p>Crée un univers d’auteur professionnel.</p>
                    </div>

                    <div class="sa-feature">
                        <div class="sa-feature-icon"><img src="/wp-content/uploads/2026/04/app-development.png" alt=""></div>
                        <h3>Site mobile</h3>
                        <p>Compatible smartphone et tablette.</p>
                    </div>
                </div>

    <h2 class="sa-section-title">Gestion du compte</h2>

    <div class="sa-account-management">
        <div class="sa-account-box">
            <div class="sa-account-box__top">
                <div class="sa-account-icon sa-account-icon--password">
                    <div class="sa-password-icon">
                        <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <defs>
                                <linearGradient id="saShadowFade" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#111111" stop-opacity="0.42"/>
                                    <stop offset="100%" stop-color="#111111" stop-opacity="0"/>
                                </linearGradient>

                                <filter id="saSoftShadow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feDropShadow dx="0" dy="12" stdDeviation="10" flood-color="#000000" flood-opacity="0.16"/>
                                </filter>
                            </defs>

                            <!-- cercle même taille que la corbeille -->
                            <circle cx="256" cy="256" r="210" fill="#5f6060"/>

                            <!-- ombre diagonale -->
                            <polygon
                                points="300,196 404,300 404,404 256,404 170,318 170,286 218,286 218,196"
                                fill="url(#saShadowFade)"
                                opacity="0.90"
                            />

                            <!-- anse cadenas -->
                            <path
                                d="M208 198
                                C208 154, 228 128, 256 128
                                C284 128, 304 154, 304 198
                                L304 214
                                L284 214
                                L284 198
                                C284 166, 273 152, 256 152
                                C239 152, 228 166, 228 198
                                L228 214
                                L208 214
                                Z"
                                fill="#ffffff"
                            />

                            <!-- corps cadenas -->
                            <rect x="190" y="202" width="132" height="92" rx="16" fill="#ffffff"/>

                            <!-- trou serrure -->
                            <circle cx="256" cy="250" r="18" fill="#1e1f21"/>
                            <rect x="247" y="266" width="18" height="32" rx="8" fill="#1e1f21"/>

                            <!-- bloc mot de passe -->
                            <rect
                                x="160"
                                y="304"
                                width="192"
                                height="72"
                                rx="20"
                                fill="#1e1f21"
                                stroke="#ffffff"
                                stroke-width="8"
                                filter="url(#saSoftShadow)"
                            />

                            <!-- étoiles -->
                            <g fill="#ffffff">
                                <text x="192" y="355" font-size="48" font-family="Arial, Helvetica, sans-serif" font-weight="700">*</text>
                                <text x="228" y="355" font-size="48" font-family="Arial, Helvetica, sans-serif" font-weight="700">*</text>
                                <text x="264" y="355" font-size="48" font-family="Arial, Helvetica, sans-serif" font-weight="700">*</text>
                            </g>

                            <!-- tiret -->
                            <rect x="300" y="343" width="28" height="8" rx="4" fill="#ffffff"/>
                        </svg>
                    </div>
                </div>

                <div class="sa-account-box__content">
                    <h3>Mot de passe</h3>
                    <p>
                        Modifie ton mot de passe pour sécuriser ton compte SiteAuteur.
                    </p>
                </div>
            </div>

            <div class="sa-account-box__bottom">
                <a href="<?php echo esc_url(home_url('/modifier-mon-mot-de-passe/')); ?>" class="sa-btn-primary sa-btn-full">
                    Changer mon mot de passe
                </a>
            </div>
        </div>

        <div class="sa-account-box sa-account-box--danger">
            <div class="sa-account-box__top">
                <div class="sa-account-icon sa-account-icon--trash">
                    <div class="sa-trash-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true">
                            <defs>
                                <linearGradient id="bgGradientRed" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#fdeaea"/>
                                    <stop offset="100%" stop-color="#f7d6d6"/>
                                </linearGradient>

                                <linearGradient id="trashGradientRed" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#f06a6a"/>
                                    <stop offset="100%" stop-color="#d94c4c"/>
                                </linearGradient>
                            </defs>

                            <circle cx="256" cy="256" r="210" fill="url(#bgGradientRed)"/>

                            <rect x="170" y="170" width="172" height="26" rx="13" fill="url(#trashGradientRed)"/>
                            <rect x="220" y="140" width="72" height="24" rx="12" fill="url(#trashGradientRed)"/>
                            <rect x="190" y="200" width="132" height="190" rx="20" fill="url(#trashGradientRed)"/>

                            <rect x="220" y="240" width="10" height="110" rx="5" fill="#ffffff" opacity="0.9"/>
                            <rect x="251" y="240" width="10" height="110" rx="5" fill="#ffffff" opacity="0.9"/>
                            <rect x="282" y="240" width="10" height="110" rx="5" fill="#ffffff" opacity="0.9"/>
                        </svg>
                    </div>
                </div>

                <div class="sa-account-box__content">
                    <h3>Suppression définitive du compte</h3>
                    <p>
                        Cette action est irréversible. Ton compte et tes données personnelles seront supprimés définitivement.
                    </p>
                </div>
            </div>

            <div class="sa-account-box__bottom">
                <a href="<?php echo esc_url(home_url('/supprimer-mon-compte/')); ?>" class="sa-btn-danger sa-btn-full">
                    Supprimer mon compte
                </a>
            </div>
        </div>
    </div>

            </div>
        </main>
    </div>
</div>