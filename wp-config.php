<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', '7samuraisakeonline' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '$rg-pp;CaS9)IPJv3`;_<9GX%Vu>Tc-/A|OJjR}d>:r,m9-,mq081vBh6Ki)3qqc' );
define( 'SECURE_AUTH_KEY',  'gFmS-Rm8[9Za:)6F:cb1XJ]j[fm:>viF%)OeK5slXNR,x_weBp=f)H|wt^{Y4Qii' );
define( 'LOGGED_IN_KEY',    'sKjbi=*$<OL3M+Hn^+-Psd61Suk|@(*fNk{CP>l7:*Bei3(d]so=4b!k:NX`eO:C' );
define( 'NONCE_KEY',        'n5XW:34n7a(`o&FpJ4cId%^24w>u>,>yQc%#+R>%+<!l=_{_^ZkS-_ d]M*pp/i]' );
define( 'AUTH_SALT',        '7W6l97h|&*MNybsv=Kc&-LBm:BF*}_X;{V Ar/Q9_]J}bl3|-J_X$<J-*}C<oFg_' );
define( 'SECURE_AUTH_SALT', 'vh~;IS0tkX8M;@BgA+6o91se Lh}!*}:)G`*`~QCc;j+x@|!Wf7?_g&w<*F=aSCY' );
define( 'LOGGED_IN_SALT',   '7Vvq4{^WbyS7#:VgKm}((|&jq*}AGKS48$ml5w&Knh_R2wCo^{sL^!YiA2jipt4f' );
define( 'NONCE_SALT',       '0bxzWdq2ym/ULiNR?_2wi32m$$4(q!lb@wErl?0Hqd:GvLqyShZcQVI>.s(?Mm&/' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
