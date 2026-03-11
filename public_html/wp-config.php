<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'reop7944_wp' );

/** Database username */
define( 'DB_USER', 'reop7944_wp' );

/** Database password */
define( 'DB_PASSWORD', '83ZSsp1!k]' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'k1moyzkjurgdb6psdv2gv3vpxjuctwsigwge3jaq4hk5aancwjd0lpdznsdarkul' );
define( 'SECURE_AUTH_KEY',  '6tcww4vtm3568r5np47b3mpyfllfe4rkhxxcfkflmp7oibcjrenglvozpc3ih1o4' );
define( 'LOGGED_IN_KEY',    'z8vwz0rf0mam0gzhemlu3u3qjk4dzpymkptzkvhcbogd1kj2u5ibfprvlbxt78dp' );
define( 'NONCE_KEY',        'aocvwdydmjewgyvltlsey83jecbv7t2qsoeqidmethegbkmxk5mw0cpfr5ltmvve' );
define( 'AUTH_SALT',        'jx43yr66ndelgynueqbpeaefthdmomdurocu41qk5nxvavfcfqpnhhl3hogbpgd5' );
define( 'SECURE_AUTH_SALT', '1jyd10efxnqwocwcrcqrjbki5ln82j2rk1jnz9wevdipwq85tswouuero6bonjl9' );
define( 'LOGGED_IN_SALT',   'klysuipzbnskpavx4s3q76ejlycn9rf0ibfdzac092jhfikvzinkueuzahl3p6ez' );
define( 'NONCE_SALT',       'tvsomrjxhyycqjtdebjzgeay0cqegz5f0prnru6wsp2rdl04q7c7czjp6rdgqsgk' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'WP_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
