<?php
/*
Plugin Name: Comic Glot
Plugin URI: http://geek.ryanhellyer.net/products/comic-glot/
Description: Comic Glot
Author: Ryan Hellyer
Version: 1.0
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


define( 'COMIC_JET_URL', 'http://dev.comicjet.com/' );
define( 'COMIC_VIEWS_URL', COMIC_JET_URL . 'views/' );
define( 'COMIC_ASSETS_URL', COMIC_VIEWS_URL . 'assets/' );
define( 'COMIC_JET_ROOT_DIR', dirname( __FILE__ ) .'/' );
define( 'COMIC_JET_STRIPS_DIR', COMIC_JET_ROOT_DIR . 'strips/' );
define( 'COMIC_NONCE', 'comic-edit' );


require( 'inc/functions.php' );
require( 'inc/class-comicjet-redis.php' );

require( 'inc/class-comicjet-setup.php' );
