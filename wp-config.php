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
define( 'DB_NAME', 'E-commerce' );

/** Database username */
define( 'DB_USER', 'Antris' );

/** Database password */
define( 'DB_PASSWORD', '12345' );

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
define( 'AUTH_KEY',         'w&V-5iCV!-Vsq())y8ug`u:(53bFQ/jnlcjZjpK#!$p3Xi)BpGKso5CQspZ,rkM!' );
define( 'SECURE_AUTH_KEY',  'zVT`<xr[}4P0Fi;2M%p4fg$L8v&>~4fCwXmdRCiG)>d+[k6jp5 a)|Ykf_i#rCLe' );
define( 'LOGGED_IN_KEY',    'pp]._P+~7-fT[,Hy`fMbd^<wO}T*V{N{{a4wO_wm2OJ[Zi+>H?YkRKwmd/HKI,-I' );
define( 'NONCE_KEY',        'W_y>C<Ki956]SY1xsyG/T%=JRM>|9w!g }9Y80C~xFFG<9XeS@9%a.8SH;,i8}Z{' );
define( 'AUTH_SALT',        'EXu}Q^C@SsbK:Weh`zF/4V_(PT_K`lGPrCp29@.G#Ie|x87nqX#G8%Z+~rK^rU3z' );
define( 'SECURE_AUTH_SALT', 'KL4!x<1:EFF/mU%*b,KC!0c3A_1,>Y5+9O&b0]_!E.`%w}kV*mm/($gt|*6<7rFl' );
define( 'LOGGED_IN_SALT',   'rjPL5Z:c*gcW9r#K]WH&&> C{(J[Q(hf#5;l!hd!m6/>._R~[2JRghQQ&tRKvb~q' );
define( 'NONCE_SALT',       ']Olwsy@Ed+P4]FBsE)5GO[-,]:Fa^t%Z]{9!*f8EC8U:f<z:(2w#=GIh-NE^j>2f' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_1';

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
