<?php if (!defined('ABSPATH')) exit; ?>

<?php
$current_user = wp_get_current_user();
$user_name    = $current_user->first_name ?: $current_user->display_name;

$order              = !empty($orders) ? $orders[0] : null;
$order_id           = $order ? (int) $order->id : 0;
$state              = $order ? sa_get_order_state($user_id, $order_id) : 'paid';
$state_label        = sa_get_order_state_label($state);
$state_description  = sa_get_order_state_description($state);
$display_date       = $order ? sa_get_order_display_date($order) : date_i18n('d/m/Y');
$action_url         = $order ? sa_get_order_primary_action_url($order, $state) : home_url('/onboarding/');
$action_text        = $order ? sa_get_order_primary_action_text($state) : 'Commencer le formulaire';

$project_name       = 'Projet 1';
$project_count_text = '1 projet en cours';

$next_step_title = 'Prochaine étape : ton formulaire';
$next_step_text  = 'Plus tu nous donnes d’infos, plus ton site te ressemble.';

$step_1_class   = 'active';
$step_2_class   = '';
$step_3_class   = '';

$step_1_content = '1';
$step_2_content = '2';
$step_3_content = '3';

$hide_button = false;

if ($state === 'onboarding_started') {
    $next_step_title = 'Prochaine étape : poursuivre ton formulaire';
    $next_step_text  = 'Reprends là où tu t’es arrêtée pour finaliser les informations de ton site.';
    $step_1_class    = 'active is-hourglass';
    $step_1_content  = '<img src="/wp-content/uploads/2026/04/hourglass.png" alt="Étape en cours">';
}

if ($state === 'submitted' || $state === 'building') {
    $next_step_title = 'Création de ton site en cours';
    $next_step_text  = 'Nous avons bien reçu tes informations. Nous avançons maintenant sur la création de ton site.';
    $step_1_class    = 'completed';
    $step_2_class    = 'active is-hourglass';
    $step_2_content  = '<img src="/wp-content/uploads/2026/04/hourglass.png" alt="Étape en cours">';
    $hide_button     = true;
}

if ($state === 'site_live') {
    $next_step_title = 'Ton site est en ligne';
    $next_step_text  = 'Ton site est maintenant accessible. Tu peux le consulter et suivre son évolution depuis ton espace.';
    $step_1_class    = 'completed';
    $step_2_class    = 'completed';
    $step_3_class    = 'active';
}

if ($state === 'paused') {
    $next_step_title = 'Projet actuellement suspendu';
    $next_step_text  = 'Ton projet est temporairement suspendu. Consulte ton espace pour voir la suite.';
    $step_1_class    = 'completed';
    $step_2_class    = 'completed';
    $hide_button     = true;
}
?>

<div class="sa-account-premium">
    <span class="sa-account-kicker">Espace client</span>

    <section class="sa-account-hero">
        <div class="sa-account-hero-left">
            <h1>Bonjour <?php echo esc_html($user_name); ?></h1>

            <p class="sa-account-hero-text">
                Bienvenue dans ton espace SiteAuteur.<br>
                Retrouve ici l’avancement de ton projet.
            </p>
        </div>
    </section>

    <div class="sa-account-body">

        <?php echo sa_render_template('partials/account-sidebar'); ?>

        <main class="sa-account-main-premium">

            <article class="sa-project-focus-card">
                <div class="sa-project-heading">
                    <h2>Ton projet</h2>
                    <span><?php echo esc_html($project_count_text); ?></span>
                </div>
                
                <div class="sa-project-focus-top">
                    <div class="sa-project-focus-left">
                        <div class="sa-project-book-icon">
                            📖
                        </div>

                        <div>
                            <h3><?php echo esc_html($project_name); ?></h3>
                            <p><?php echo esc_html($state_description); ?></p>
                        </div>
                    </div>

                    <div class="sa-project-status-badge">
                        <?php echo esc_html($state_label); ?>
                    </div>
                </div>

                <div class="sa-project-steps">
                    <div class="sa-step-item <?php echo esc_attr($step_1_class); ?>">
                        <div class="sa-step-circle">
                            <?php echo wp_kses($step_1_content, ['img' => ['src' => [], 'alt' => []]]); ?>
                        </div>
                        <div class="sa-step-label">
                            <strong>Informations</strong>
                        </div>
                    </div>

                    <div class="sa-step-line"></div>

                    <div class="sa-step-item <?php echo esc_attr($step_2_class); ?>">
                        <div class="sa-step-circle">
                            <?php echo wp_kses($step_2_content, ['img' => ['src' => [], 'alt' => []]]); ?>
                        </div>
                        <div class="sa-step-label">
                            <strong>Création</strong>
                        </div>
                    </div>

                    <div class="sa-step-line"></div>

                    <div class="sa-step-item <?php echo esc_attr($step_3_class); ?>">
                        <div class="sa-step-circle">
                            <?php echo esc_html($step_3_content); ?>
                        </div>
                        <div class="sa-step-label">
                            <strong>Mise en ligne</strong>
                        </div>
                    </div>
                </div>

                <div class="sa-next-step-box">
                    <div class="sa-next-step-left">
                        <div class="sa-next-step-icon">📝</div>
                        <div>
                            <h4><?php echo esc_html($next_step_title); ?></h4>
                            <p><?php echo esc_html($next_step_text); ?></p>
                        </div>
                    </div>

                    <?php if (!$hide_button) : ?>
                        <a href="<?php echo esc_url($action_url); ?>" class="sa-btn-primary sa-next-step-btn">
                            <?php echo esc_html($action_text); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="sa-project-infos-grid">
                    <div class="sa-info-box">
                        <div class="sa-info-icon">📅</div>
                        <div>
                            <strong>Créé le</strong>
                            <span><?php echo esc_html($display_date); ?></span>
                        </div>
                    </div>

                    <div class="sa-info-box">
                        <div class="sa-info-icon">🕒</div>
                        <div>
                            <strong>Délai estimé</strong>
                            <span>7 jours ouvrés</span>
                        </div>
                    </div>

                    <div class="sa-info-box">
                        <div class="sa-info-icon">🎧</div>
                        <div>
                            <strong>Besoin d’aide ?</strong>
                            <span>On est là pour toi.</span>
                        </div>
                    </div>
                </div>

            </article>

            <div class="sa-project-note">
                <div class="sa-project-note-icon">💡</div>
                <div>
                    <strong>Bon à savoir</strong>
                    <p>
                        Tu seras notifiée à chaque avancée. Tu peux nous écrire à tout moment
                        si tu as une question.
                    </p>
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

        </main>
    </div>
</div>