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

if ( ! isset( $_GET['comic'] ) ) {
	return;
}


define( 'COMIC_VIEWS_URL', content_url() . '/plugins/comic-glot/views/' );
define( 'COMIC_ASSETS_URL', COMIC_VIEWS_URL . 'assets/' );


class Comic_Setup {

	public function __construct() {

		// Load views
		require( 'views/index.php' );
		exit;

	}

}
new Comic_Setup();
