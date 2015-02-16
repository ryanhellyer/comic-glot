<?php

if ( ! isset( $_GET['comic'] ) ) {
	return;
}

define( 'COMIC_VIEWS_URL', 'http://local.wordpress-trunk.dev/wp-content/mu-plugins/comic/views/' );


class Comic_Setup {

	public function __construct() {

		// Load views
		require( 'views/index.php' );
	}

}
new Comic_Setup();
