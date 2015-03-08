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


define( 'COMIC_VIEWS_URL', content_url() . '/plugins/comic-glot/views/' );
define( 'COMIC_ASSETS_URL', COMIC_VIEWS_URL . 'assets/' );
define( 'COMIC_NONCE', 'comic-edit' );


class Comic_Setup {

	/**
	 * Error message.
	 * 
	 * @var string
	 */
	public $error;

	public $error_messages;


	public $file_size = 10014158; // Max file size for uploads

	public function __construct() {

		$this->error_messages = array(
			'file-type-not-supported' => __( 'Sorry, but that file type is not supported', 'comic-glot' ),
			'file-too-large'          => __( 'Sorry, that file was too large.', 'comic-glot' ),
		);

		add_action( 'init', array( $this, 'save_data' ) );
		add_action( 'template_redirect', array( $this, 'display_editor' ) );

	}

	public function save_data() {
		$this->strips = array(
			0 => array(
				'en' => 'en1.jpg',
				'de' => 'de1.jpg',
			),
			1 => array(
				'en' => 'en2.jpg',
				'de' => 'de2.jpg',
			),
		);

		$this->languages = array(
			'en' => array(
				'name' => 'English',
				'used' => true,
			),
			'de' => array(
				'name' => 'Deutsch',
				'used' => true,
			),
			'nb' => array(
				'name' => 'Norsk Bokmål',
				'used' => false,
			),
		);

		// Get default image to show (from first page)
		foreach( $this->languages as $lang => $name ) {

			// Get file name of first language
			$file_name = $this->strips[0][$lang];
			$this->current_image = COMIC_ASSETS_URL . 'strips/' . $file_name;
			break; // Break free from foreach now, as only wanted first item

		}

		if ( ! empty( $_POST ) ) {

			// Bail out if nonce not set
			if (
				! isset( $_POST[COMIC_NONCE] ) 
				|| ! wp_verify_nonce( $_POST[COMIC_NONCE], COMIC_NONCE ) 
			) {
			}

			// Handle image uploads
			if ( isset( $_FILES ) ) {
				$files = $_FILES['file-upload'];
				foreach( $files['error'] as $page => $data ) {
					foreach( $data as $lang => $error ) {

						// If no error, then set for processing
						if ( 0 == $error ) {

							// Get path and extension
							$tmp_path = $files['tmp_name'][$page][$lang];
							if ( 'image/jpeg' == $files['type'][$page][$lang] ) {
								$extension = 'jpg';
							} elseif ( 'image/png' == $files['type'][$page][$lang] ) {
								$extension = 'png';
							} else {
								$this->error[] = 'file-type-not-supported'; // Invalid extension, so serve error
							}

							// Give error if file is too large
							if ( $this->file_size < $files['size'][$page][$lang] ) {
								$this->error[] = 'file-too-large'; // File too big, so serve error
							}

							// Only allow if extension set
							if ( empty( $this->error ) ) {

								// Get file name
								$file_name = $files['name'][$page][$lang];
								$chunked_name = explode( '.', $file_name );
								$name = $chunked_name[0];

								// Store in directory
								$file_name = $name . '.' . $extension;
								$new_path = dirname( __FILE__ ) . '/strips/' . $file_name;
								$count = 1;
								while ( file_exists( $new_path ) ) {
									$file_name = $name . '-' . $count . $extension;
									$new_path = dirname( __FILE__ ) . '/strips/' . $file_name;
									$count++;
								}
								copy( $tmp_path, $new_path );

								// Update data
								$this->strips[$page][$lang] = $file_name;

							}
						}
					}

				}

			}

			// Remove a page
			if ( isset( $_POST['view-page'] ) ) {
				foreach( $_POST['view-page'] as $key => $language ) {
					foreach( $language as $lang => $x ) {
						$this->current_image = COMIC_ASSETS_URL . 'strips/' . $this->strips[$key][$lang];
					}
//					echo $this->current_image;
//
				}
			}

			// Remove a page
			if ( isset( $_POST['remove-page'] ) ) {

				foreach( $_POST['remove-page'] as $page_to_remove => $x ) {}

				unset( $this->strips[$page_to_remove] );
				$this->strips = array_values( $this->strips );
			}

			// Add a page
			if ( isset( $_POST['add-new-page'] ) ) {
				$count = count( $this->strips );
				foreach( $this->languages as $lang => $name ) {
					$this->strips[$count][$lang] = '';
				}
			}

			// Change languages
			if ( isset( $_POST['language'] ) ) {

				// Loop through possible languages
				foreach( $this->languages as $lang => $language ) {

					// Sub-loop through selected languages
					foreach( $_POST['language'] as $lang_post => $language_post ) {

						// If possible = selected, then set it to selected
						if ( $lang == $lang_post ) {
							$languages[$lang] = $language;
							$this->languages[$lang]['used'] = true; // Set this language to be used
						}

					}
				}
			}

			echo '<textarea style="position:absolute;left:0;bottom:0;width:900px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
			echo "Errors:\n";
			print_r( $this->error );
			echo "\nPOST:\n";
			print_r( $_POST );
			echo "\nSTRIPS:\n";
			print_r( $this->strips );
			echo '</textarea>';
		}

	}

	public function display_editor() {

		if ( ! isset( $_GET['comic'] ) ) {
			return;
		}

		// Load views
		require( 'views/index.php' );
		exit;

	}

}
new Comic_Setup();
