<?php

// Obtain meta data
$meta_file_path = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . $this->slug . '.txt';
$meta_json = file_get_contents( $meta_file_path );
$meta = json_decode( $meta_json );
$lang1 = $this->language1;
$title = $meta->title->language_strings->$lang1;


/**
 * Create pagination links
 */
$pagination = '';
// Create previous button HTML
$prev_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . ( $this->page_number - 1 );
$prev_url_bit = COMIC_JET_URL . 'comic/' . $this->slug . '/' . ( $this->page_number - 1 );

$pagination .= '<div class="pagination previous-link">';
if (
	file_exists( $prev_path_bit . '-' . $this->language1 . '.png' )
	||
	file_exists( $prev_path_bit . '-' . $this->language1 . '.jpg' )
) {
	$prev_url = $prev_url_bit . '/' . $this->language1 . '/';

	// If second language too, then add that
	if ( isset( $this->language2 ) ) {
		$prev_url .= $this->language2 . '/';
	}

	$pagination .= '<a href="' . esc_attr( $prev_url ) . '">' . __( 'Previous' ) . '</a>';

}
$pagination .= '</div>';


$current_language1 = "<div onclick='toggle_image()'>" . sprintf( __( 'Switch to %s' ), '<span>' . $this->get_language_name( $this->language1 ) . '</span>' ) . '</div>';
$current_language2 = "<div onclick='toggle_image()'>" . sprintf( __( 'Switch to %s' ), '<span>' . $this->get_language_name( $this->language2 ) . '</span>' ) . '</div>';


$pagination .= '
<div class="pagination current-language">
	' . $current_language1 . '
</div>';


// Create next button HTML
$next_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . ( $this->page_number + 1 );
$next_url_bit = COMIC_JET_URL . 'comic/' . $this->slug . '/' . ( $this->page_number + 1 );

$pagination .= '<div class="pagination next-link">';
if (
	file_exists( $next_path_bit . '-' . $this->language1 . '.png' )
	||
	file_exists( $next_path_bit . '-' . $this->language1 . '.jpg' )
) {
	$next_url = $next_url_bit . '/' . $this->language1 . '/';

	// If second language too, then add that
	if ( isset( $this->language2 ) ) {
		$next_url .= $this->language2 . '/';
	}

	$pagination .= '<a href="' . esc_attr( $next_url ) . '">' . __( 'Next' ) . '</a>';
}
$pagination .= '</div>';
$pagination .= '</div>';


// Set page info. text
if ( 0 == $this->page_number ) {
	$page_info = __( 'cover page' );
} else {
	$page_info = sprintf( __( 'page %s' ), $this->page_number );
}

$html = '
<div class="inner">
	<div class="content">

		<h1 id="site-title">' . $title . '</h1>
		<h2 id="site-description">' . $page_info . '</h2>';

// Next and previous pages
$html .= '<div id="pagination-top">';
$html .= $pagination;
$html .= '</div>';


$html .= '
		<div id="comic-display">';

// Hunt out current image URL
$file_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . $this->page_number;
$file_url_bit = COMIC_JET_URL . 'comics/' . $this->slug . '/' . $this->page_number;

if ( file_exists( $file_path_bit . '.png' ) ) {
	$url = $file_url_bit . '.png';
} elseif ( file_exists( $file_path_bit . '.jpg' ) ) {
	$url = $file_url_bit . '.jpg';
} else {
	$url = '';
	trigger_error( 'ComicJet: No file found', E_USER_ERROR );
}


// Work out bubble image URLs
if ( isset( $this->language1 ) ) {
	$bubble_image[] = $file_url_bit . '-' . $this->language1 . '.png';
}
if ( isset( $this->language2 ) ) {
	$bubble_image[] = $file_url_bit . '-' . $this->language2 . '.png';
}

// Add image(s)
$html .= '

			<div class="image-display">
				<img src="' . esc_attr( $url ) . '" />
				<img id="bubble" onmouseover="this.style.cursor=\'pointer\'" onclick="toggle_image()" src="' . esc_attr( $bubble_image[1] ) . '" />

';

// Adding speech bubbles
$page_number = $this->page_number;
$lang2 = $this->language2;
if ( isset( $meta->$page_number ) ) {
	$scripts[] = COMIC_ASSETS_URL . 'bubbles.js';

	foreach( $meta->$page_number as $key => $value ) {
		$html .= '
				<div style="' . esc_attr( 'top:' . $value->top . '%;left:' . $value->left . '%;width:' . $value->width . '%;height:' . $value->height . '%' ) . '" class="bubble">' . esc_html( $value->language_strings->$lang2 ) . '</div>';	
	}
}

$html .= '

			</div>';


// Next and previous pages
$html .= "\n\n" . '<div id="pagination-bottom">';
$html .= $pagination;
$html .= '</div>';


$script_vars['bubble_image_0'] = esc_attr( $bubble_image[0] );
if ( isset( $bubble_image[1] ) ) {
	$script_vars['bubble_image_1'] = esc_attr( $bubble_image[1] );
}
$script_vars['current_language1'] = $current_language1;
$script_vars['current_language2'] = $current_language2;
$script_vars['page_slug'] = $this->slug;

$next_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . ( $this->page_number + 1 );
if ( file_exists( $next_path_bit . '-' . $this->language1 . '.png' ) ) {
	$script_vars['next_comic_read'] = $this->page_number;
} else {
	$script_vars['next_comic_read'] = 'end';
}



// If second language set, then dynamically change speech bubble onclick
if ( isset( $bubble_image[0] ) ) {
	$scripts[] = COMIC_ASSETS_URL . 'toggle-image.js';
}



$html .= '
		</div>
	</div>
</div>
';
