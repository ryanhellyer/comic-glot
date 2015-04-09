<?php

// Obtain meta data
$meta_file_path = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . $this->slug . '.txt';
$meta = file_get_contents( $meta_file_path );
$meta = explode( "\n", $meta );
foreach( $meta as $key => $meta_bit ) {
	$title_info = $meta_bit;
	$title_exploded = explode( ':', $title_info );

	if ( $this->language1 == $title_exploded[0] ) {
		$title = $title_exploded[1];
	}
}

/**
 * Create pagination links
 */
$pagination = '';
// Create previous button HTML
$prev_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . ( $this->page_number - 1 );
$prev_url_bit = COMIC_JET_URL . 'comic/' . $this->slug . '/' . ( $this->page_number - 1 );

$pagination .= '<div class="pagination" id="previous-link">';
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


$current_language1 = '<div onclick="toggle_image()">' . sprintf( __( 'Switch to %s' ), '<span>' . $this->get_language_name( $this->language1 ) . '</span>' ) . '</div>';
$current_language2 = '<div onclick="toggle_image()">' . sprintf( __( 'Switch to %s' ), '<span>' . $this->get_language_name( $this->language2 ) . '</span>' ) . '</div>';


$pagination .= '
<div class="pagination" id="current-language">
	' . $current_language1 . '
</div>';


// Create next button HTML
$next_path_bit = COMIC_JET_ROOT_DIR . 'comics/' . $this->slug . '/' . ( $this->page_number + 1 );
$next_url_bit = COMIC_JET_URL . 'comic/' . $this->slug . '/' . ( $this->page_number + 1 );

$pagination .= '<div class="pagination" id="next-link">';
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

$html .= '
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
			</div>';

// Next and previous pages
$html .= '<div id="pagination-bottom">';
$html .= $pagination;
$html .= '</div>';

// If second language set, then dynamically change speech bubble onclick
if ( isset( $bubble_image[0] ) ) {
	$html .= '

<script>
function toggle_image() {
	var img = document.getElementById("bubble").src;
	if (img.indexOf(\'' . esc_attr( $bubble_image[1] ) . '\')!=-1) {
		document.getElementById("bubble").src  = "' . esc_attr( $bubble_image[0] ) . '";
		document.getElementById("current-language").innerHTML = \'' . $current_language2 . '\';

	} else {
		document.getElementById("bubble").src = "' . esc_attr( $bubble_image[1] ) . '";
		document.getElementById("current-language").innerHTML = \'' . $current_language1 . '\';
	}

}
</script>';
}



$html .= '
		</div>
	</div>
</div>
';
