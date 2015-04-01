<?php

echo '<h3>This is comic "' . $this->get( 'title' ) . '" on page number ' . $this->current_page['page_number'] . '</h3>';

$strips = $this->get( 'strips' );

$page = $strips[$this->current_page['page_number'] - 1]; // Grab this page only

echo '

<div class="image-display">';

if ( '' != $this->get_current_images( 'current-background' ) ) {
	echo '
	<img src="' . esc_attr( COMIC_STRIPS_URL . $strips[$this->current_page['page_number'] - 1]['current_background'] ) . '" />';
}

// Loop through currently selected languages
foreach( $this->current_page['current_languages'] as $current_lang ) {

	// Loop through languages used in this strip
	foreach( $page as $lang => $file_name ) {

		// If langs match, output image
		if ( $current_lang == $lang ) {
			$bubble_image[] = COMIC_STRIPS_URL . $file_name;
		}
	}
}

echo '
	<img id="bubble" onmouseover="this.style.cursor=\'pointer\'" onclick="toggle_image()" src="' . esc_attr( $bubble_image[0] ) . '" />';

echo '
</div>

';



// Add previous and current pages
if ( isset( $this->current_page['current_languages'][0] ) ) {
	$languages = $this->current_page['current_languages'][0] . '/';
} else {
	$languages = '';
}

if ( isset( $this->current_page['current_languages'][1] ) ) {
	$languages .= $this->current_page['current_languages'][1] . '/';
}

if ( isset( $strips[$this->current_page['page_number']] ) ) {
	$next_page_number = $this->current_page['page_number'] + 1;

	$url = COMIC_JET_URL . __( 'comic' ) . '/' . $this->slug . '/' . $next_page_number . '/' . $languages;
	echo '<a href="' . esc_attr( $url ) . '">' . __( 'Next' ) . '</a>';
}

echo ' | ';

if ( isset( $strips[$this->current_page['page_number'] - 2] ) ) {
	$previous_page_number = $this->current_page['page_number'] - 1;
	$url = COMIC_JET_URL . __( 'comic' ) . '/' . $this->slug . '/' . $previous_page_number . '/' . $languages;

	echo '<a href="' . esc_attr( $url ) . '">' . __( 'Previous' ) . '</a>';
}




echo '

<hr />';



if ( isset( $bubble_image[1] ) ) {
	echo'

<script>
   function toggle_image() {
        var img = document.getElementById("bubble").src;
        if (img.indexOf(\'' . esc_attr( $bubble_image[0] ) . '\')!=-1) {
            document.getElementById("bubble").src  = "' . esc_attr( $bubble_image[1] ) . '";
        }
         else {
           document.getElementById("bubble").src = "' . esc_attr( $bubble_image[0] ) . '";
       }

    }
</script>';
}


echo '<h1>' . ( microtime( true ) - COMIC_JET_START_TIME ) . '</h1>';
/*
0,082 seconds via 15 queries
0,071 seconds via 3 queries

generated in 0.098 seconds
	served from batcache in 0.002 seconds
*/
