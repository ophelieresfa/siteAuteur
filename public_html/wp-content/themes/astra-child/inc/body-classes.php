<?php
if (!defined('ABSPATH')) exit;

add_filter('body_class', 'siteauteur_logged_in_body_class');

function siteauteur_logged_in_body_class($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'sa-user-logged-in';
    }

    return $classes;
}