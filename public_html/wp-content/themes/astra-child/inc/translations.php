<?php
if (!defined('ABSPATH')) exit;

/**
 * Traduction des messages Ultimate Member (password)
 */
add_filter('gettext', function ($translated, $text, $domain) {

    $translations = [
        'Your password must contain at least one capital letter'   => 'Votre mot de passe doit contenir au moins une majuscule',
        'Your password must contain at least one lowercase letter' => 'Votre mot de passe doit contenir au moins une minuscule',
        'Your password must contain at least one number'           => 'Votre mot de passe doit contenir au moins un chiffre',
        'Your password must contain at least one special character'=> 'Votre mot de passe doit contenir au moins un caractère spécial',
        'Your password must contain at least 8 characters'         => 'Votre mot de passe doit contenir au moins 8 caractères',
        'Your password must contain less than 30 characters'       => 'Votre mot de passe doit contenir moins de 30 caractères',
    ];

    return $translations[$text] ?? $translated;

}, 20, 3);