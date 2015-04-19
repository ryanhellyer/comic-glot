<?php
/*
Name: Comic Jet
Author: Ryan Hellyer
Author URI: http://geek.hellyer.kiwi/

Copyright (c) 2015 Ryan Hellyer

*/

define( 'COMIC_JET_START_TIME', microtime( true ) );

require( 'config.php' );

define( 'COMIC_JET_URL', 'http://' . COMIC_JET_DOMAIN . '/' );
define( 'COMIC_VIEWS_URL', COMIC_JET_URL . 'views/' );
define( 'COMIC_ASSETS_URL', COMIC_VIEWS_URL . 'assets/' );
define( 'COMIC_STRIPS_URL', COMIC_JET_URL . 'strips/' );
define( 'COMIC_JET_ROOT_DIR', dirname( __FILE__ ) .'/' );
define( 'COMIC_JET_STRIPS_DIR', COMIC_JET_ROOT_DIR . 'strips/' );
define( 'COMIC_NONCE', 'comic-edit' );
define( 'COMIC_JET_EMAIL', 'ryanhellyer@gmail.com' );

require( 'inc/functions.php' );
require( 'inc/class-comicjet-login.php' );
require( 'inc/class-comicjet-redis.php' );

require( 'inc/class-comicjet-setup.php' );
new ComicJet_Login;
