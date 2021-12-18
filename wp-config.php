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
define( 'DB_NAME', 'carcafevdos' );

/** MySQL database username */
define( 'DB_USER', 'forge' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Q2b2TQEDvfeD0jiIyF6R' );

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
define( 'AUTH_KEY',         ':2Y>,^-hO.KY#_2iX81d9tvK>OWE3}A+K`d0[~]E@a*BA_alqEzOrBaeLN!`ZI{F' );
define( 'SECURE_AUTH_KEY',  ';hnvI}g!wZL&]ItYfHj?V6~PO]A2YN=DqG5]r:_I#tl5q|ibzng?8<q#8#mclQg3' );
define( 'LOGGED_IN_KEY',    'af MJ3nolN^ ^vw`-? H]I0EgQZu.6)NV=0bT*YcTGS3P=~=c`,{ZM?A^{uP^v)!' );
define( 'NONCE_KEY',        'hk|`st.jW#1h<>o,K%1#y7H!f Y./8~{:cgQVx!7-Yp][bnmE`e-ePpcSE.@}KVX' );
define( 'AUTH_SALT',        'P]QEfnAFf78K@WIskk28g*EHI=UaHhF8MMzH==zv~8|ILvb`G}diL?>5*nm`:x]R' );
define( 'SECURE_AUTH_SALT', 'om fW6TJ+VZd*16l]Plo{P8IkbD|79Y[C.6h(!ogM@hTMO~jb&oS_N4pi(oH Yi<' );
define( 'LOGGED_IN_SALT',   '&=SAn]&G:|V!{_$5C#661yzbG9vR&71??Vg2cwx.)U3o-Vd4ZfOLG!I/So,`BA-.' );
define( 'NONCE_SALT',       'F)NCgW!mxK5T7+rXIYyn}2!l:]{Y4;~}&_mU;]yBm2DF4NckJ%5ERH.A<#O]!!=&' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'cf_';

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

//define('ALLOW_UNFILTERED_UPLOADS', true); para subir archivos

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
