<?php

class ComicJet_Setup {

	public $error; // Current error
	public $error_messages; // Array of possible error messages
	public $file_size = 10014158; // Max file size for uploads
	public $comic; // The comic slug
	public $page_type; // The current page type (view, home or 404
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

		$this->db = comicjet_db();
		$this->current_page = $this->set_vars_based_on_url();
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
		$total_bits = count( $uri_bits );

		// Process static pages first coz they goof up due to not having language slugs
		if( 'registration' == $uri_bits[0] ) {
			$this->page_type = 'registration';
			$this->language1 = 'en';
			define( 'COMICJET_CURRENT_LANGUAGE', 'en' );
		} elseif( 'anmeldung' == $uri_bits[0] ) {
			$this->page_type = 'registration';
			$this->language1 = 'de';
			define( 'COMICJET_CURRENT_LANGUAGE', 'de' );
		} elseif ( array_key_exists( $uri_bits[$total_bits - 1], $this->available_languages ) ) {

			// If two languages set
			if ( isset( $uri_bits[$total_bits - 2] ) && array_key_exists( $uri_bits[$total_bits - 2], $this->available_languages ) ) {
				$this->language1 = $uri_bits[$total_bits - 2];
				$this->language2 = $uri_bits[$total_bits - 1];
			} else {
				$this->language1 = $uri_bits[$total_bits - 1];
			}

			// Set current language as constant (needed for accessing within the translation function)
			define( 'COMICJET_CURRENT_LANGUAGE', $this->language1 );
		} else {
			// Defaulting to English
			$this->language1 = 'en';
			define( 'COMICJET_CURRENT_LANGUAGE', 'en' );
		}

		// Work out comic page information
		if ( isset( $this->page_type ) ) {
			// Must be a static page which has already been set, so just continue ... 
		} elseif ( __( 'comic' ) == $uri_bits[0] ) {

			if ( isset( $uri_bits[1] ) ) {
				$this->slug = filter_var( $uri_bits[1], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );

				// Calculate the current page number
				if ( isset( $uri_bits[2] ) ) {

					if ( 'flush' == $uri_bits[2] && current_user_is_admin() ) {
						echo 'Not currently used since not storing any comic stuff in Redis<br />';
						//$this->db->delete( 'title', $uri_bits[1] );
						echo '"' . $uri_bits[1] . '" flushed :)';
						die;
					} elseif( 3 == count( $uri_bits ) ) {
						// Set different page types and page numbers where apppropriate

						// This is a page number without language specification. So default to site language.
						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/
							$this->page_type = 'view_comic';
							$this->page_number = (int) $uri_bits[2]; // Grab current page number
							$this->language1 = 'en'; // Get language #1

						} else {
							// domain.com/comic/slug/lang/
							$this->page_type = '404';

						}

					} elseif( 4 == count( $uri_bits ) ) {

						if ( is_numeric( $uri_bits[2] ) ) {
							// domain.com/comic/slug/2/en/
							$this->page_type = 'view_comic';
							$this->page_number = (int) $uri_bits[2]; // Grab current page number
							$this->language1 = $uri_bits[3]; // Get language #1

						} else {
							// domain.com/comic/slug/lang/lang/
							$this->page_type = '404';

						}

					} elseif( 5 == count( $uri_bits ) ) {
						// domain.com/comic/slug/page_number/lang/lang/
						$this->page_type = 'view_comic';
						$this->page_number = (int) $uri_bits[2]; // Grab current page number
						$this->language1 = $uri_bits[3]; // Get language #1
						$this->language2 = $uri_bits[4]; // Get language #2

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
		} elseif ( array_key_exists( $uri_bits[0], $this->available_languages ) ) {
			// A home page, with language set
			$this->page_type = 'home';
		} elseif( '' == $uri_bits[0] ) {
			// At the root, so set to home page
			$this->page_type = 'home';
			$this->language1 = 'en';
		} else {
			// Not home page or a comic, so 404 it (if we add static pages, then they'll be set here)
			$this->page_type = '404';
			$this->language1 = 'en';

		}

		// Set current language as constant (needed for accessing within the translation function)
		if ( ! defined( 'COMICJET_CURRENT_LANGUAGE' ) ) {
			define( 'COMICJET_CURRENT_LANGUAGE', $this->language1 );
		}
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
		if ( defined( 'COMIC_JET_COMPRESS' ) ) {
			$html = $this->compressing_html( $html );
		}

		// Load views
		echo $html;
		exit;

	}

}
new ComicJet_Setup();
