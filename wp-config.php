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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         '.??`9F=[cQ,msBhW|pjqSK8V~b 9 4tar$RGSyfAhtZ:~MPxY@_J/o%X9rr#z}+u' );
define( 'SECURE_AUTH_KEY',  'O{etv^xAOf77r+{OEl,FN]rUge=u|vRNmJsgD6C!0NtV4#/xlX|Bar25B22z|nhV' );
define( 'LOGGED_IN_KEY',    '|wy$3 V.*ez?#0)zZK@@k;t+}z}H:$g1!!&_cYL]^-}N/0Z0=QP;9ef!jTpe`#<P' );
define( 'NONCE_KEY',        'Bbqc(TZ=7U|k9zFAK/,pC1=B^B)z18BT#{lOcxF.c,/%i$K&Y{dJIoZu>N)H+hj5' );
define( 'AUTH_SALT',        '[IC5ot#-QV0<6Wh+3q*,v=_kh(%(2w1*9/a$[CW1OwfLbxWWl2S#HK6sgf:XGjEf' );
define( 'SECURE_AUTH_SALT', 'WeS~Dr9IR2bs8WW{UoQ>&HfSY%.,NZmOa[,;U,@=?}b?b>9Z=Tf$Jm(Fs4D*lDza' );
define( 'LOGGED_IN_SALT',   ']W+ ;R8U~N&5Q8X#y0$EEBIlUEfjloH_VvwsnQN$gN5>QBk>TlFXy=q}~m[BCw;u' );
define( 'NONCE_SALT',       'RDQgV}x=9{{UEY(*<y0tn>SU+UV/P8>) Tq/{dV#G/b3Ikh/m%9ghRNy`saljP}%' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
