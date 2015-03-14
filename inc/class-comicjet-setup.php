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

		$this->current_page_info();

		$this->db = comicjet_db();

		$this->error_messages = array(
			'file-type-not-supported' => __( 'Sorry, but that file type is not supported' ),
			'file-too-large'          => __( 'Sorry, that file was too large.' ),
		);

		$this->set_vars();
		$this->save_data();

		// Output page
		$this->output_page();

	}

	/**
	 * Current page info.
	 */
	public function current_page_info() {

		// Parse which page we are on
		$uri = $_SERVER['REQUEST_URI'];
		$uri = trim( $uri, '/' ); // Strip preceding and trailing slashes
		$uri_bits = explode( '/', $uri ); // Split

		// Work out comic page information
		if ( __( 'comic' ) == $uri_bits[0] ) {

			if ( isset( $uri_bits[1] ) ) {
				$this->comic = $uri_bits[1];
				$this->comic = filter_var( $this->comic, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );

				// Calculate the current page number
				if ( isset( $uri_bits[2] ) && 3 == count( $uri_bits ) ) {

					// Set edit mode
					if ( __( 'edit' ) == $uri_bits[2] ) {
						$this->page_type = 'edit';
					} else {
						$this->page_type = 'view_comic';
						$this->page_number = (int) $uri_bits[2]; // Grab current page number
					}
				} else {
					$this->page_type = 'view_comic';
					$this->page_number = 1; // No page number set, so we must be on page 1
				}
			} else {
				$this->page_type = '404';
			}
		} elseif( '' == $uri_bits[0] ) {
			$this->page_type = 'home';
		} else {
			$this->page_type = '404';
		}

	}

	/**
	 * Save submtited data.
	 */
	public function set_vars() {

		// Get strips
		$this->strips = $this->db->get( 'languages' );
		if ( empty( $this->languages ) ) {
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
			$this->db->add( 'languages', $this->strips );
		}

		// Get languages
		$this->strips = $this->db->get( 'strips' );
		if ( empty( $this->strips ) ) {
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
			$this->db->add( 'strips', $this->strips );
		}

	}

	/**
	 * Save submtited data.
	 */
	public function save_data() {

		// Set images from form input
		if ( isset( $_POST['strip_image'] ) ) {
			foreach( $_POST['strip_image'] as $page => $strip ) {
				foreach( $strip as $lang => $file_name ) {
					$this->strips[$page][$lang] = $file_name;
				}
			}

		}

		// Get default image to show (from first page)
		foreach( $this->languages as $lang => $name ) {

			// Get file name of first language
			$file_name = $this->strips[0][$lang];
			$this->current_image = COMIC_JET_URL . 'strips/' . $file_name;
			break; // Break free from foreach now, as only wanted first item

		}

		if ( ! empty( $_POST ) ) {

			// Bail out if nonce not set
			if (
				! isset( $_POST['nonce'] ) 
				|| ! verify_nonce( $_POST['nonce'] ) 
			) {
				return;
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
								$new_path = COMIC_JET_STRIPS_DIR . $file_name;
								$count = 1;
								while ( file_exists( $new_path ) ) {
									$file_name = $name . '-' . $count . $extension;
									$new_path = COMIC_JET_STRIPS_DIR . $file_name;
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

			// Get current image
			if ( isset( $_POST['view-page'] ) ) {
				foreach( $_POST['view-page'] as $page => $language ) {
					foreach( $language as $lang => $x ) {
						$this->current_image = COMIC_GLOT_URL . 'strips/' . $this->strips[$page][$lang];
					}
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


			// Finally, save the data
			$this->db->replace( 'languages', $this->strips );
//			$this->db->delete( 'strips', $this->strips );
			$this->db->replace( 'strips', $this->strips );

			echo '<textarea style="position:absolute;left:0;bottom:0;width:600px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
echo "STRIPS FROM REDIS:\n";
print_r( $this->db->get( 'strips' ) );echo "\n";

			echo "Errors:\n";
			print_r( $this->error );
			echo "\nPOST:\n";
			print_r( $_POST );
			echo "\nSTRIPS:\n";
			print_r( $this->strips );
			echo '</textarea>';
		}

	}

	/**
	 * Output the page.
	 */
	public function output_page() {

		require( 'views/header.php' );

		switch( $this->page_type ) {
			case 'edit':
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
