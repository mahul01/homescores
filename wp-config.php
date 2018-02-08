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
define('DB_NAME', 'homescores');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'TS3q+gdOJs?/6Ac]BA;[igpjvQ4_-U);9CQ`tWoFhzWfEdnwc-9;FvTJFVphrYiP');
define('SECURE_AUTH_KEY',  'WFoE$i3E8sv[p_blHu<y|*,y#RG13s>GsC)8`;[U{5e2O?V;E J5EX%4lA0&CsEi');
define('LOGGED_IN_KEY',    's @uv)&*^a+M0!}mvdtij_7Xb(PXp(_`w8E.@blrb!;(Kf0w_vI51N=?S+j5=tYj');
define('NONCE_KEY',        '%s5::{@]_=M0b/hl?bv^@7GAb-HBeGL* v6&#Pt1OE~`]e_ie`Vt0)7sR*ZZoE<2');
define('AUTH_SALT',        '&:eqZq4Ed(c;~)Q#MJ3c6#pqI|sXqpVMMC_[YiOk8jW;<)-?RFP37BZ$SKvJDLhG');
define('SECURE_AUTH_SALT', '=Xh`574-}}En3/qL6`tT Eq*/zrmI1T53L9b.&}6]goga-]rqa6A%Jt|#d[XZh$l');
define('LOGGED_IN_SALT',   'i[>ML}]`,6>;s},#?I5OM6</mWDORqT1]?7^5flnnJy^A|`cYe`W~f)Qw(PUT81;');
define('NONCE_SALT',       '|xV*}!HB-Ij$Gy>Mjpg|JyYZ#vr:9JPjdObL2e#nT)Ji=fmv.TgfbFd*&X/2+&FA');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_DEBUG', true);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
