<?php
define('TITLE_PREFIX', 'Audio/Video Management / ');

//Savelevels:
//SEVERE (highest value)
//WARNING
//INFO
//CONFIG
//FINE
//FINER
//FINEST (lowest value)

define('LOG_SAVELEVEL', 'INFO');
define('DEBUG', true);

//PATHS
define('PATH_XSLT', 'styles/');
define('PATH_PAGES', 'pages/');
define('PATH_CLASS', 'kernel/');
define('PATH_IMAGE', 'image/');

//USERLEVELS
define('USERLEVEL_ADMIN', 3);
define('USERLEVEL_SUPERUSER', 2);
define('USERLEVEL_USER', 1);
define('USERLEVEL_NONE', 0);

//AUTHENTICATION COOKIE
define('COOKIE_NAME', 'AudioVideoManagement');
define('COOKIE_LIFETIME', 7200);

/*
 * MYSQLi
 * Use persist connections (p:localhost) for php 5.3
 */
define('MYSQL_DATABASE', 'AVMDB');
define('MYSQL_HOSTNAME', 'localhost');
define('MYSQL_USERNAME', 'avmDatabase');
define('MYSQL_PASSWORD', 'avmDatabase');
?>
