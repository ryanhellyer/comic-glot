<?php
/*
Plugin Name: Comic Glot
Plugin URI: http://geek.ryanhellyer.net/products/comic-glot/
Description: Comic Glot
Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2014 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


define( 'COMIC_GLOT_URL', 'http://local.wordpress-trunk.dev/wp-content/plugins/comic-glot/' );
define( 'COMIC_VIEWS_URL', COMIC_GLOT_URL . 'views/' );
define( 'COMIC_ASSETS_URL', COMIC_VIEWS_URL . 'assets/' );
define( 'COMIC_NONCE', 'comic-edit' );


require( 'inc/class-comicjet-redis.php' );
require( 'inc/class-comicjet-setup.php' );


if ( ! function_exists( '__' ) ) {
	require( 'inc/functions.php' );
}


