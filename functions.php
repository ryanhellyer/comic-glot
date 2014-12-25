<?php

/**
 * Primary class used to load the theme
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Comic Glot
 * @since Comic Glot 1.0
 */
class Comic_Glot_Setup {

	public $script_urls;
	public $css_urls;
	protected $langs = array(
		'en_GB' => array(
			'name' => 'English',
			'slug' => 'en',
		),
		'nb_NO' => array(
			'name' => 'Norsk Bokmål',
			'slug' => 'nb',
		),
		'de_DE' => array(
			'name' => 'Deutsch',
			'slug' => 'de',
		),
	);

	protected function get_lang_from_slug( $slug ) {
		foreach( $this->langs as $lang => $lang_info ) {
			if ( $slug == $lang_info['slug'] ) {
				return $lang;
			}
		}

		return false;
	}

	protected function get_slug_from_lang( $language ) {
		foreach( $this->langs as $lang => $lang_info ) {
			if ( $language == $lang ) {
				return $lang_info['slug'];				
			}
		}

		return false;
	}

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {

		// Add script URL's
		$this->script_urls = array(
			includes_url( '/js/jquery/jquery.js' ),
			get_stylesheet_directory_uri() . '/scripts/swipe.js',
			get_stylesheet_directory_uri() . '/scripts/swipe-init.js',
		);

		// Add CSS URL's
		$this->css_urls = array(
			get_stylesheet_directory_uri() . '/style.css',
		);

		if ( isset( $_GET['manifest'] ) ) {
			$this->manifest();
		}

		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_head',            array( $this, 'stylesheet' ) );
		add_action( 'wp_footer',          array( $this, 'scripts' ) );
		add_action( 'the_content',        array( $this, 'override_content' ) );
		add_action( 'init',               array( $this, 'register_post_type' ) );
		add_filter( 'cmb2_meta_boxes',    array( $this, 'meta_boxes' ) );
		add_action( 'init',               array( $this, 'rewrites' ) );
//		add_filter( 'generate_rewrite_rules', array( $this, 'modify_existing_rewrites' ) );

//		add_action( 'template_redirect',  array( $this, 'bla' ) );
		add_action('query_vars',          array( $this, 'rewrite_query_vars' ) );
	}

	public function rewrite_query_vars( $query_vars ) {

		$query_vars['bla'] = 'bla';
		$query_vars['lang'] = 'lang';
		return $query_vars;
	//print_r( $wp_query );die;
	}

	public function bla() {

		if ( '' != get_query_var( 'lang' ) ) {
			echo ' ... ' . get_query_var( 'lang' );
			die;
		}

	}

	/**
	 * Register the post-type
	 */
	public function register_post_type() {
		$args = array(
			'public'             => true,
			'label'              => __( 'Comic', 'comic-glot' ),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'comic' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
		);
		register_post_type( 'comic', $args );
	}

	/*
	 * Add rewrite rules
	 */
	public function rewrites() {
		add_rewrite_rule( 'comic/([^/]+)/([^/]+)/?$', 'index.php?comic=$matches[1]&lang=$matches[2]', 'top' );
	}

	/**
	 * Override the content
	 * 
	 * @param  string   $content   The post content
	 * @return string   The modified post content
	 */
	public function override_content( $content ) {
		$content = '';
		$frames = get_post_meta( get_the_ID(), '_frames', true );

		// Get languages
		$slugs = get_query_var( 'lang' );
		$slugs = explode( '-', $slugs );
		if ( is_array( $slugs ) && '' == $slugs[0] ) {
			foreach( $this->langs as $lang => $lang_info ) {
				$slugs[] = $this->get_slug_from_lang( $lang );
			}
		} elseif ( is_array( $slugs ) ) {
			// 
		} else {
			$slugs = array(
				0 => $slugs,
			);
		}

		// Loop through each frame
		foreach( $frames as $frame ) {
			$content .= '<div>';

			// Load the frame for each language
			foreach( $slugs as $key => $slug ) {
				$lang = $this->get_lang_from_slug( $slug );
				$attachment_id = absint( $frame[$lang . '_id'] );
				if ( is_int( $attachment_id ) ) {
					$url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
					$content .= '<span lang="' . esc_attr( $lang )  . '">';
					$content .= '<img src="' . esc_url( $url ) . '" /><br />';
					$content .= '</span>';
				}
			}

			$content .= '</div>';
		}

		return $content;
	}

	public function stylesheet() {
		foreach( $this->css_urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo '<link rel="stylesheet" href="' . esc_url( $url ) . '" type="text/css" media="all" />';
		}
	}

	public function scripts() {
		foreach( $this->script_urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo '<script src="' . esc_url( $url ) . '"></script>';

		}
	}

	/**
	 * Convert URLs to https where required
	 * @param    string   $url    The URL
	 * @return   string   $url    The URL after conversion to http/https
	 */
	public function convert_url_to_https( $url ) {

		// Convert URLs to SSL if needed
		if ( is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		} else {
			$url = str_replace( 'https://', 'http://', $url );
		}

		return $url;
	}

	/**
	 * Get the posts attached images
	 * 
	 * @param    int  $post_id   The post ID
	 * @return   array  The array of URLs
	 */
	static public function get_attached_images( $post_id ) {

		// Add images
		$images = get_attached_media( 'image', $post_id );
		foreach( $images as $key => $image ) {
			$attachment_id = $image->ID;
			$url = wp_get_attachment_image_src( $attachment_id, 'full' );
			$url = $url[0];
			$url = self::convert_url_to_https( $url );
			$urls[] = $url;
		}

		return $urls;
	}

	/**
	 * Add the manifest file
	 */
	public function manifest() {
		$post_id = absint( $_GET['manifest'] );

		// Add the page header
		header( 'Content-Type: text/cache-manifest' );

		// Declare this is a cache manifest
		echo "CACHE MANIFEST\n";

		$urls = self:: get_attached_images( $post_id );

		// Add script and CSS URLs in
		$urls = array_merge( $this->script_urls, $urls );
		$urls = array_merge( $this->css_urls, $urls );

		// Output each URL
		foreach( $urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo esc_url( $url ) . "\n";
		}

		// Output the current page URL
		echo get_permalink( $post_id ) . "\n";

		// Add string
		$time = absint( time() / 30 );
		echo '#' . $time;
		exit;
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Make theme available for translation
		load_theme_textdomain( 'comic-glot', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'excerpt-thumb', 250, 350 );
	}

	/**
	 * Define the metabox and field configurations.
	 *
	 * @param  array $meta_boxes
	 * @return array
	 */
	public function meta_boxes( array $meta_boxes ) {

		foreach( $this->langs as $lang => $lang_info ) {
			$fields[] = array(
				'name' => $lang_info['name'],
				'id'   => $lang,
				'desc' => __( 'Upload an image', 'comic-glot' ),
				'type' => 'file',
			);
		}

		/**
		 * Repeatable Field Groups
		 */
		$meta_boxes['field_group'] = array(
			'id'           => 'comic-frames',
			'title'        => __( 'Comic frames', 'comic-glot' ),
			'object_types' => array( 'comic', ),
			'fields'       => array(
				array(
					'id'          => '_frames',
					'type'        => 'group',
					'description' => __( 'Add frames to your comic', 'comic-glot' ),
					'options'     => array(
						'group_title'   => __( 'Frame {#}', 'comic-glot' ), // {#} gets replaced by row number
						'add_button'    => __( 'Add another frame', 'comic-glot' ),
						'remove_button' => __( 'Remove frame', 'comic-glot' ),
						'sortable'      => true, // beta
					),
					'fields'      => $fields,
				),
			),
		);
		return $meta_boxes;
	}

}
new Comic_Glot_Setup;


require( 'meta-boxes/init.php' );
