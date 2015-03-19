<?php

class ComicJet_Setup {

	public $error; // Current error
	public $error_messages; // Array of possible error messages
	public $file_size = 10014158; // Max file size for uploads
	public $comic; // The comic slug
	public $page_type; // The current page type (view, edit or 404
	public $page_number; // The current page number

	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->current_page = $this->get_current_page_info();

		$this->db = comicjet_db();

		$this->error_messages = array(
			'file-type-not-supported'  => __( 'Sorry, but that file type is not supported' ),
			'file-too-large'           => __( 'Sorry, that file was too large.' ),
			'invalid-window-dimension' => __( 'Sorry, but we detected an invalid window dimension.' ),
			'invalid-nonce'            => __( 'Sorry, an error occured.' ),
			'user-not-admin'           => __( 'Sorry, but you are need to be admin to do that.' ),
		);

		$this->available_languages = array(
			'en' => array(
				'name' => 'English',
				'iso'  => 'en_US',
			),
			'de' => array(
				'name' => 'Deutsch',
				'iso'  => 'de_DE',
			),
			'nb' => array(
				'name' => 'Norsk Bokmål',
				'iso'  => 'nb_NO',
			),
		);

		$this->save_data();
		$this->set_vars();

		// Output page
		$this->output_page();

	}

	/**
	 * Get current page info from URL.
	 */
	public function get_current_page_info() {

		// Parse which page we are on
		$uri = $_SERVER['REQUEST_URI'];
		$uri = trim( $uri, '/' ); // Strip preceding and trailing slashes
		$uri_bits = explode( '/', $uri ); // Split

		// Work out comic page information
		if ( __( 'comic' ) == $uri_bits[0] ) {

			if ( isset( $uri_bits[1] ) ) {
				$current_page['slug'] = filter_var( $uri_bits[1], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );

				// Calculate the current page number
				if ( isset( $uri_bits[2] ) ) {

					// Set different page types and page numbers where apppropriate
					if ( __( 'edit' ) == $uri_bits[2] ) {
						// Editing a comic
						$current_page['type'] = 'edit_comic';

					} elseif( 3 == count( $uri_bits ) ) {

						// This is a page number without language specification. So default to site language.
						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/
							$current_page['type'] = 'view_comic';
							$current_page['page_number'] = (int) $uri_bits[2]; // Grab current page number
							$current_page['current_languages'][] = 'en'; // Get language #1

						} else {
							// domain.com/comic/slug/lang/
							$current_page['type'] = '404';

						}

					} elseif( 4 == count( $uri_bits ) ) {

						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/en/
							$current_page['type'] = 'view_comic';
							$current_page['page_number'] = (int) $uri_bits[2]; // Grab current page number
							$current_page['current_languages'][] = $uri_bits[3]; // Get language #1

						} else {
							// domain.com/comic/slug/lang/lang/
							$current_page['type'] = '404';

						}

					} elseif( 5 == count( $uri_bits ) ) {
						// domain.com/comic/slug/page_number/lang/lang/
						$current_page['type'] = 'view_comic';
						$current_page['page_number'] = (int) $uri_bits[2]; // Grab current page number
						$current_page['current_languages'][] = $uri_bits[3]; // Get language #1
						$current_page['current_languages'][] = $uri_bits[4]; // Get language #2

					} else {
						$current_page['type'] = '404';

					}
				} else {
					// No languages or page numbers set, so defaulting to site language
					// domain.com/comic/slug/    - no language selected
					$current_page['type'] = '404';
				}
			} else {
				// No comic slug set, so 404 it
				$current_page['type'] = '404';

			}
		} elseif( '' == $uri_bits[0] ) {
			// At the root, so set to home page
			$current_page['type'] = 'home';

		} else {
			// Not home page or a comic, so 404 it (if we add static pages, then they'll be set here)
			$current_page['type'] = '404';

		}

		return $current_page;
	}

	/**
	 * Save submtited data.
	 */
	public function set_vars() {

		if ( isset( $this->current_page['slug'] ) ) {
			$this->current_page['title'] = $this->db->get( 'title', $this->current_page['slug'] );
		}
		$this->strip_list = $this->db->get( 'strip_list' );

		if (
			'edit_comic' == $this->current_page['type']
			||
			'view_comic' == $this->current_page['type']
		) {

			// Get languages
			$this->current_page['used_languages'] = $this->db->get( 'languages', $this->current_page['slug'] );

			// Get strips
			$this->current_page['strips'] = $this->db->get( 'strips', $this->current_page['slug'] );

			// Get next and previous pages
			$this->current_page['strips'] = $this->db->get( 'strips', $this->current_page['slug'] );

			// If a comic is on a page which does not exist, then 404 it
			if (
				'view_comic' == $this->current_page['type'] 
				&&
				! isset( $this->current_page['strips'][$this->current_page['page_number'] - 1] )
			) {
				$this->current_page['type'] = '404';
			}

			// Set next and previous page numbers
			if ( isset( $this->current_page['page_number'] ) ) {
				if ( isset( $this->current_page['strips'][$this->current_page['page_number']] ) ) {
					$this->current_page['next_page'] = $this->current_page['page_number'] + 1;
				}
				if ( isset( $this->current_page['strips'][$this->current_page['page_number'] - 2] ) ) {
					$this->current_page['previous_page'] = $this->current_page['page_number'] - 1;
				}
			}

			// If current page is invalid number,then switch to 404 error page
			if ( isset( $this->current_page['page_number'] ) && count( $this->current_page['strips'] ) < $this->current_page['page_number'] ) {
				$this->current_page['type'] = '404';
			}

/**********************************************************************************
 ** Check current languages /en/de/ and serve 404 error if they don't make sense **
 **********************************************************************************/

			// Work out what the first image is, so that it can be displayed as current image
			if ( ! isset( $this->current_page['current_image'] ) ) {

				foreach( $this->available_languages as $lang => $language ) {

					// Set whether selected or not
					if ( array_key_exists( $lang, $this->current_page['used_languages'] ) ) {
						if ( ! empty( $this->current_page['strips'][0][$lang] ) ) {
							if ( isset( $this->current_page['strips'][0]['current_background'] ) ) {
								$this->current_page['current_background'] = COMIC_JET_URL . 'strips/' . $this->current_page['strips'][0]['current_background'];
							}
							$this->current_page['current_image'] = COMIC_JET_URL . 'strips/' . $this->current_page['strips'][0][$lang];
						}
						break;
					}

				}

				if ( empty( $this->current_page['current_image'] ) ) {
					$this->current_page['current_background'] = COMIC_ASSETS_URL . 'default-strip.jpg';
					$this->current_page['current_image'] = COMIC_ASSETS_URL . 'default-strip.jpg';
				}

			}

		}

	}

	/**
	 * Save submtited data.
	 */
	public function save_data() {

		// Bail out now if no post data set
		if ( empty( $_POST ) ) {
			return;
		}

		// Bail out now if current user isn't an admin
		if ( ! current_user_is_admin() ) {
			$this->error = 'user-not-admin';
			return;
		}

		// Bail out if nonce not set
		if (
			! isset( $_POST['nonce'] ) 
			|| ! verify_nonce( $_POST['nonce'] ) 
		) {
			$this->error['invalid-nonce'];
			return;
		}


		// Save the comic title
		if ( isset( $_POST['title'] ) ) {
			$title = filter_var( $_POST['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );
			$this->db->write( 'title', $title, $this->current_page['slug'] );
		}

		// Save language selections
		if ( isset( $_POST['language'] ) ) {
			$this->current_page['languages'] = array();
			foreach( $_POST['language'] as $key => $language ) {
				if ( array_key_exists( $key, $this->available_languages ) ) {
					$this->current_page['languages'][$key] = true;
				}
			}
			$this->db->write( 'languages', $this->current_page['languages'], $this->current_page['slug'] );
		}


		// Set each comic page, including their order
		if ( isset( $_POST['strip_image'] ) ) {

			$count = 0;
			$this->current_page['strips'] = array();
			foreach( $_POST['strip_image'] as $page => $strip ) {

				// Process pages
				foreach( $strip as $lang => $file_name ) {

					// Process window dimensions
					if ( 'window' == $lang ) {
						foreach( $file_name as $window_id => $window_dimensions ) {
							if ( '' != $window_dimensions ) {
								$new_window_dimensions = sanitize_window_dimensions( $window_dimensions );
								if ( false != $new_window_dimensions ) {
									$this->current_page['strips'][$count]['window'][$window_id] = $new_window_dimensions;
								}
							}
						}
					} else {
						$this->current_page['strips'][$count][$lang] = sanitize_file_name( $file_name );
					}

				}
				$count++;

			}

		}

		// Add a page
		if ( isset( $_POST['add-new-page'] ) ) {

			if ( '' == $this->current_page['strips'] ) {
				$this->current_page['strips'] = array();
			}

			$count = count( $this->current_page['strips'] );
			foreach( $this->current_page['languages'] as $lang => $name ) {
				$this->current_page['strips'][$count][$lang] = '';
			}
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

// Check if $lang is valid or if it's 'main'

							// Get file name
							$file_name = $files['name'][$page][$lang];
							$chunked_name = explode( '.', $file_name );
							$name = $chunked_name[0];

							// Store in directory
							$file_name = $name . '.' . $extension;
							$file_name = sanitize_file_name( $file_name ); // Sanitizing file name
							$new_path = COMIC_JET_STRIPS_DIR . $file_name;
							$count = 1;
							while ( file_exists( $new_path ) ) {
								$file_name = $name . '-' . $count . '.' . $extension;
								$new_path = COMIC_JET_STRIPS_DIR . $file_name;
								$count++;
							}
							copy( $tmp_path, $new_path );

							// Update data
							$file_name = sanitize_file_name( $file_name ); // Sanitizing file name
							$this->current_page['strips'][$page][$lang] = $file_name;

						}
					}
				}

			}

		}

		// Get current image
		if ( isset( $_POST['view-page'] ) ) {
			foreach( $_POST['view-page'] as $page => $language ) {
				foreach( $language as $lang => $x ) {
					if ( isset( $this->current_page['strips'][$page]['current_background'] ) ) {
						$this->current_page['current_background'] = COMIC_JET_URL . 'strips/' . $this->current_page['strips'][$page]['current_background'];
					}
					$this->current_page['current_image'] = COMIC_JET_URL . 'strips/' . $this->current_page['strips'][$page][$lang];
				}
			}
		}

		// Remove a page
		if ( isset( $_POST['remove-page'] ) ) {

			foreach( $_POST['remove-page'] as $page_to_remove => $x ) {}

			unset( $this->current_page['strips'][$page_to_remove] );
			$this->current_page['strips'] = array_values( $this->current_page['strips'] );
		}

		// Save this strip to the list of strips
		$this->strip_list = $this->db->get( 'strip_list' );
		$this->strip_list[$this->current_page['slug']] = true;
		$this->db->write( 'strip_list', $this->strip_list );

		// Finally, save the data
		// It's important to save everything last, as some items are modified multiple times (no point in saving the same thing multiple times)
		$this->db->write( 'strips', $this->current_page['strips'], $this->current_page['slug'] );

		/*
		echo '<textarea style="position:absolute;left:0;bottom:0;width:600px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
		echo "STRIPS FROM REDIS:\n";
		print_r( $this->db->get( 'strips', $this->current_page['slug'] ) );echo "\n";
//		echo "\nSTRIPS:\n";
//		print_r( $this->strips );
		echo "\nPOST:\n";
		print_r( $_POST );
		echo '</textarea>';
		*/

	}

	/**
	 * Output the page.
	 */
	public function output_page() {

		require( 'views/header.php' );

		switch( $this->current_page['type'] ) {
			case 'edit_comic':
				require( 'views/edit.php' );
				break;
			case '404':
				require( 'views/404.php' );
				break;
			case 'view_comic':
				require( 'views/comic.php' );
				break;
			case 'home':
				require( 'views/home.php' );
				break;
			default:
				require( 'views/404.php' );
		}

		// Load views
		exit;

	}

}
new ComicJet_Setup();
