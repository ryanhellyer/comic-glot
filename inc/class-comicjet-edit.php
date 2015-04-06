<?php

/**
 * Abstracted edit logic.
 * This is instantiated in a separate class to keep the core code-base to a minimum
 */
class ComicJet_Edit {

	/**
	 * Class constructor.
	 */
	public function __construct( $that ) {
		$this->that = $that; // Grab this from other class
		$this->db = comicjet_db(); // Load database class
		$this->save_data(); // Save required data
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
			$this->that->error = 'user-not-admin';
			return;
		}

		// Bail out if nonce not set
		if (
			! isset( $_POST['nonce'] ) 
			|| ! verify_nonce( $_POST['nonce'] ) 
		) {
			$this->that->error['invalid-nonce'];
			return;
		}


		// Save the comic title
		if ( isset( $_POST['title'] ) ) {
			$title = filter_var( $_POST['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );
			$this->db->write( 'title', $title, $this->that->slug );
		}

		// Save language selections
		if ( isset( $_POST['language'] ) ) {
			$this->that->current_page['languages'] = array();
			foreach( $_POST['language'] as $key => $language ) {
				if ( array_key_exists( $key, $this->that->available_languages ) ) {
					$this->that->current_page['languages'][$key] = true;
				}
			}
			$this->db->write( 'languages', $this->that->current_page['languages'], $this->that->slug );
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
			foreach( $this->that->current_page['languages'] as $lang => $name ) {
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
							$this->that->error[] = 'file-type-not-supported'; // Invalid extension, so serve error
						}

						// Give error if file is too large
						if ( $this->that->file_size < $files['size'][$page][$lang] ) {
							$this->that->error[] = 'file-too-large'; // File too big, so serve error
						}

						// Only allow if extension set
						if ( empty( $this->that->error ) ) {

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
								$this->db->write( 'thumbnail', $thumbnail, $this->that->slug );
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
						$this->that->current_page['current_background'] = COMIC_JET_URL . 'strips/' . $strips[$page]['current_background'];
					}
					$this->that->current_page['current_image'] = COMIC_JET_URL . 'strips/' . $strips[$page][$lang];
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
		$this->that->strip_list = $this->db->get( 'strip_list', 'default' );
		$this->that->strip_list[$this->that->slug] = true;
		$this->db->write( 'strip_list', $this->that->strip_list );

		// Finally, save the data
		// It's important to save everything last, as some items are modified multiple times (no point in saving the same thing multiple times)
		if ( isset( $strips ) ) {
			$this->db->write( 'strips', $strips, $this->that->slug );
		}

		/*
		echo '<textarea style="position:absolute;left:0;bottom:0;width:600px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
		echo "STRIPS FROM REDIS:\n";
		print_r( $this->that->db->get( 'strips', $this->that->slug ) );echo "\n";
//		echo "\nSTRIPS:\n";
//		print_r( $this->that->strips );
		echo "\nPOST:\n";
		print_r( $_POST );
		echo '</textarea>';
		*/

	}

}
