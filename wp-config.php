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
define( 'DB_NAME', 'icrhk' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'KHos*oc:Kk4Ue6SHzd(?c{B(Ob%)jF05zL_b!.uT}a60pku%oG?T~ybBNNK`%Y|Q' );
define( 'SECURE_AUTH_KEY',  '6*N/<Z_{z7=UF~hG+h*batgPjN0bwx`%Ha!O3UmEzF gHnzM-K1|{f|$xr#pYOo.' );
define( 'LOGGED_IN_KEY',    '+})(u%Y)m4/zmoC73R3&:g(SdF2H%=dzq%nI(Ol7[DN:Q87.^kJm#4/Qx7a~&@bU' );
define( 'NONCE_KEY',        ']Y(1pGVIl[UDMqM#UkR*!Zn(Flu73zYelIpxnwp*} -.R Ej*3Guf6zt@W]%ii?I' );
define( 'AUTH_SALT',        'w AZW{Rwa=({%8PhT^-+I6YHJUTr~[ ;zPLZ1f@hIcaM.Zj}(^x9J/G{7Dun2)gP' );
define( 'SECURE_AUTH_SALT', '[Xqc.<w?Uo-6~tfq-Y6X>>Aoy]T*(K/<TuUhGBEHO,!m)k7kfY3n$6s.!`J42u&5' );
define( 'LOGGED_IN_SALT',   'EA|KMi0aT%_q{3i@h%T_l#&9pzeL.II18/:gJS:3NcIj2F=/F/9Ur{WMM#Yg{0Y@' );
define( 'NONCE_SALT',       '0:N~),!f2Qx`.b)_K>ePD8YYDSTWC@KlaX2D+m@9I.PwcePC=Agr8f8eGTv];}_m' );

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
$table_prefix = 'wp_';

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
