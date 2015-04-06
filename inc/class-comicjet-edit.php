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
			'es' => array(
				'name' => 'Espanol',
				'iso'  => 'es_ES',
			),
		);

		$this->current_page = $this->set_vars_based_on_url();
		$this->db = comicjet_db();
		$this->save_data();
		$this->set_vars();
		$this->language_selection();

		$this->error_messages = array(
			'file-type-not-supported'  => __( 'Sorry, but that file type is not supported' ),
			'file-too-large'           => __( 'Sorry, that file was too large.' ),
			'invalid-window-dimension' => __( 'Sorry, but we detected an invalid window dimension.' ),
			'invalid-nonce'            => __( 'Sorry, an error occured.' ),
			'user-not-admin'           => __( 'Sorry, but you are need to be admin to do that.' ),
		);

		// Output page
		$this->output_page();

	}

	/**
	 * Redirect when language set.
	 * This is only a fallback for when JavaScript isn't available
	 */
	public function language_selection() {

		// Bail out now if language not being set
		if ( ! isset( $_POST['select-language'] ) ) {
			return;
		}

		// Set the root of the URL
		$url = COMIC_JET_URL;

		// Add new languages to the URL
		if ( array_key_exists( $_POST['language1'], $this->available_languages ) ) {
			$url .= $_POST['language1'] . '/';
		}
		if ( array_key_exists( $_POST['language2'], $this->available_languages ) ) {
			$url .= $_POST['language2'] . '/';
		}

		header( 'Location: ' . $url, 302 );
		die;
	}

	/**
	 * Get value from DB.
	 * 
	 * @param   string  $key  The key in DB
	 * @return  mixed   The value from the DB
	 */
	public function get( $key, $group = '' ) {

		// If no group set, then default to current slug
		if ( '' == $group ) {
			$group = $this->slug;
		}

		return $this->db->get( $key, $group );
	}

	/**
	 * Get current page info from URL.
	 */
	public function set_vars_based_on_url() {

		// Parse which page we are on
		$uri = $_SERVER['REQUEST_URI'];
		$uri = trim( $uri, '/' ); // Strip preceding and trailing slashes
		$uri_bits = explode( '/', $uri ); // Split

		// Set languages
		if ( array_key_exists( $uri_bits[0], $this->available_languages ) ) {
			// A home page, with language set
			$this->language1 = $uri_bits[0];
			if ( isset( $uri_bits[1] ) && array_key_exists( $uri_bits[1], $this->available_languages ) ) {
				$this->language2 = $uri_bits[1];

			}

			// Set current language as constant (needed for accessing within the translation function)
			define( 'COMICJET_CURRENT_LANGUAGE', $this->language1 );
		} else {
			// Defaulting to English
			$this->language1 = 'en';
			define( 'COMICJET_CURRENT_LANGUAGE', 'en' );
		}

		// Work out comic page information
		if ( __( 'comic' ) == $uri_bits[0] ) {

			if ( isset( $uri_bits[1] ) ) {
				$this->slug = filter_var( $uri_bits[1], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );

				// Calculate the current page number
				if ( isset( $uri_bits[2] ) ) {

					// Set different page types and page numbers where apppropriate
					if ( __( 'edit' ) == $uri_bits[2] ) {
						// Editing a comic
						$this->page_type = 'edit_comic';

					} elseif( 3 == count( $uri_bits ) ) {

						// This is a page number without language specification. So default to site language.
						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/
							$this->page_type = 'view_comic';
							$this->page_number = (int) $uri_bits[2]; // Grab current page number
							$this->current_languages[] = 'en'; // Get language #1

						} else {
							// domain.com/comic/slug/lang/
							$this->page_type = '404';

						}

					} elseif( 4 == count( $uri_bits ) ) {

						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/en/
							$this->page_type = 'view_comic';
							$this->page_number = (int) $uri_bits[2]; // Grab current page number
							$this->current_languages[] = $uri_bits[3]; // Get language #1

						} else {
							// domain.com/comic/slug/lang/lang/
							$this->page_type = '404';

						}

					} elseif( 5 == count( $uri_bits ) ) {
						// domain.com/comic/slug/page_number/lang/lang/
						$this->page_type = 'view_comic';
						$this->page_number = (int) $uri_bits[2]; // Grab current page number
						$this->current_languages[] = $uri_bits[3]; // Get language #1
						$this->current_languages[] = $uri_bits[4]; // Get language #2

					} else {
						$this->page_type = '404';

					}
				} else {
					// No languages or page numbers set, so defaulting to site language
					// domain.com/comic/slug/    - no language selected
					$this->page_type = '404';
				}
			} else {
				// No comic slug set, so 404 it
				$this->page_type = '404';

			}
		} elseif( 'registration' == $uri_bits[0] ) {
			$this->page_type = 'registration';
			$this->current_languages[] = 'en';
		} elseif ( array_key_exists( $uri_bits[0], $this->available_languages ) ) {
			// A home page, with language set
			$this->page_type = 'home';
		} elseif( '' == $uri_bits[0] ) {
			// At the root, so set to home page
			$this->page_type = 'home';
			$this->current_languages[] = 'en';
		} else {
			// Not home page or a comic, so 404 it (if we add static pages, then they'll be set here)
			$this->page_type = '404';
			$this->current_languages[] = 'en';

		}

	}

	/**
	 * Save submtited data.
	 */
	public function set_vars() {

		if (
			'edit_comic' == $this->page_type
			||
			'view_comic' == $this->page_type
		) {

			// Get strips
			$strips = $this->get( 'strips' );

			// If a comic is on a page which does not exist, then 404 it
			if (
				'view_comic' == $this->page_type 
				&&
				! isset( $strips[$this->page_number - 1] )
			) {
				$this->page_type = '404';
			}

			// If current page is invalid number,then switch to 404 error page
			if ( isset( $this->page_number ) && count( $strips ) < $this->page_number ) {
				$this->page_type = '404';
			}

/**********************************************************************************
 ** Check current languages /en/de/ and serve 404 error if they don't make sense **
 **********************************************************************************/


		}

	}

	/**
	 * Get the current required images.
	 * 
	 * @param   string  $type  the background or image
	 * @return  string  The image URL
	 */
	public function get_current_images( $type ) {

		// Get data from DB
		$strips = $this->get( 'strips' );
		$used_languages = $this->get( 'languages' );

		// Work out what the first image is, so that it can be displayed as current image
		if ( ! isset( $current_image ) ) {

			foreach( $this->available_languages as $lang => $language ) {

				// Set whether selected or not
				if ( is_array( $used_languages ) ) {
					if ( array_key_exists( $lang, $used_languages ) ) {
						if ( ! empty( $strips[0][$lang] ) ) {
							if ( isset( $strips[0]['current_background'] ) ) {
								$current_background = COMIC_JET_URL . 'strips/' . $strips[0]['current_background'];
							}
							$current_image = COMIC_JET_URL . 'strips/' . $strips[0][$lang];
						}
						break;
					}
				}
			}

			// Provide fallbacks for when image is not set
			if ( empty( $current_image ) ) {
				$current_background = COMIC_ASSETS_URL . 'default-strip.jpg';
				$current_image = COMIC_ASSETS_URL . 'default-strip.jpg';
			}

		}

		// Return chosen image
		if ( 'background' == $type ) {
			return $current_background;
		} else {
			return $current_image;
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
			$this->db->write( 'title', $title, $this->slug );
		}

		// Save language selections
		if ( isset( $_POST['language'] ) ) {
			$this->current_page['languages'] = array();
			foreach( $_POST['language'] as $key => $language ) {
				if ( array_key_exists( $key, $this->available_languages ) ) {
					$this->current_page['languages'][$key] = true;
				}
			}
			$this->db->write( 'languages', $this->current_page['languages'], $this->slug );
		}


		// Set each comic page, including their order
		if ( isset( $_POST['strip_image'] ) ) {

			$count = 0;
			$strips = array();
			foreach( $_POST['strip_image'] as $page => $strip ) {

				// Process pages
				foreach( $strip as $lang => $file_name ) {

					// Process window dimensions
					if ( 'window' == $lang ) {
						foreach( $file_name as $window_id => $window_dimensions ) {
							if ( '' != $window_dimensions ) {
								$new_window_dimensions = sanitize_window_dimensions( $window_dimensions );
								if ( false != $new_window_dimensions ) {
									$strips[$count]['window'][$window_id] = $new_window_dimensions;
								}
							}
						}
					} else {
						$strips[$count][$lang] = sanitize_file_name( $file_name );
					}

				}
				$count++;

			}

		}

		// Add a page
		if ( isset( $_POST['add-new-page'] ) ) {

			if ( ! isset( $strips ) ) {
				$strips = '';
			}
			if ( '' == $strips ) {
				$strips = array();
			}

			$count = count( $strips );
			foreach( $this->current_page['languages'] as $lang => $name ) {
				$strips[$count][$lang] = '';
			}
		}

		// Handle image uploads
		if ( isset( $_FILES ) && isset( $_FILES['file-upload'] ) ) {
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

							$file_name = sanitize_file_name( $file_name ); // Sanitizing file name
							if ( 'thumbnail' == $page ) {
								$thumbnail[$lang] = $file_name; // Thumbnail doesn't go in main strips list							
								$this->db->write( 'thumbnail', $thumbnail, $this->slug );
							} else {
								$strips[$page][$lang] = $file_name;
							}

						}
					}
				}

			}

		}

		// Get current image
		if ( isset( $_POST['view-page'] ) ) {
			foreach( $_POST['view-page'] as $page => $language ) {
				foreach( $language as $lang => $x ) {
					if ( isset( $strips[$page]['current_background'] ) ) {
						$this->current_page['current_background'] = COMIC_JET_URL . 'strips/' . $strips[$page]['current_background'];
					}
					$this->current_page['current_image'] = COMIC_JET_URL . 'strips/' . $strips[$page][$lang];
				}
			}
		}

		// Remove a page
		if ( isset( $_POST['remove-page'] ) ) {

			foreach( $_POST['remove-page'] as $page_to_remove => $x ) {}

			unset( $strips[$page_to_remove] );
			$strips = array_values( $strips );
		}

		// Save this strip to the list of strips
		$this->strip_list = $this->db->get( 'strip_list', 'default' );
		$this->strip_list[$this->slug] = true;
		$this->db->write( 'strip_list', $this->strip_list );

		// Finally, save the data
		// It's important to save everything last, as some items are modified multiple times (no point in saving the same thing multiple times)
		if ( isset( $strips ) ) {
			$this->db->write( 'strips', $strips, $this->slug );
		}

		/*
		echo '<textarea style="position:absolute;left:0;bottom:0;width:600px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
		echo "STRIPS FROM REDIS:\n";
		print_r( $this->db->get( 'strips', $this->slug ) );echo "\n";
//		echo "\nSTRIPS:\n";
//		print_r( $this->strips );
		echo "\nPOST:\n";
		print_r( $_POST );
		echo '</textarea>';
		*/

	}

	/**
	 * Stripping whitespace from the HTML.
	 * 
	 * @param   string  $html  The uncompressed HTML
	 * @return  string         The compressed HTML
	 */
	private function compressing_html( $html ) {

		$search = array(
			'/\>[^\S ]+/s',  // strip whitespaces after tags, except space
			'/[^\S ]+\</s',  // strip whitespaces before tags, except space
			'/(\s)+/s'       // shorten multiple whitespace sequences
		);

		$replace = array(
			'>',
			'<',
			'\\1'
		);

		$html = preg_replace( $search, $replace, $html );

		return $html;
	}

	/**
	 * Output the page.
	 */
	public function output_page() {

		// Load login system
		$comicjet_login = new ComicJet_Login();

		require( 'views/header.php' );

		switch( $this->page_type ) {
			case 'registration':
				require( 'views/registration.php' );
				break;
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

		require( 'views/footer.php' );

		// Compress the HTML output
		$html = $this->compressing_html( $html );

		// Load views
		echo $html;
		exit;

	}

}
new ComicJet_Setup();
