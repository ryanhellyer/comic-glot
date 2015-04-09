<?php
/*
Name: Comic Jet
Author: Ryan Hellyer
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2015 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

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

require( 'inc/functions.php' );
require( 'inc/class-comicjet-login.php' );
require( 'inc/class-comicjet-redis.php' );

require( 'inc/class-comicjet-setup.php' );
new ComicJet_Login;
