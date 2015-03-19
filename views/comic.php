<?php

if ( count( $this->current_page['strips'] ) < $this->current_page['page_number'] ) {
	require( '404.php' );
	exit;
}

echo '<h3>This is comic "' . $this->current_page['title'] . '" on page number ' . $this->current_page['page_number'] . '</h3>';

if ( ! isset( $this->current_page['strips'][$this->current_page['page_number'] - 1] ) ) {
	echo '<strong>This page does not exist (page number too high)</strong>';
	exit;
}

$page = $this->current_page['strips'][$this->current_page['page_number'] - 1]; // Grab this page only

echo '

<div class="image-display">
	<img src="' . esc_attr( $this->current_page['current_background'] ) . '" />';

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
</div>';



// Add previous and current pages
if ( isset( $this->current_page['strips'][$this->current_page['page_number']] ) ) {
	$next_page_number = $this->current_page['page_number'] + 1;
	$url = COMIC_JET_URL . __( 'comic' ) . '/' . $this->current_page['slug'] . '/' . $next_page_number . '/' . $this->current_page['current_languages'][0] . '/' . $this->current_page['current_languages'][1];
	echo '
<a href="' . esc_attr( $url ) . '">' . __( 'Next' ) . '</a>';
}

echo ' | ';

if ( isset( $this->current_page['strips'][$this->current_page['page_number'] - 2] ) ) {
	$previous_page_number = $this->current_page['page_number'] - 1;
	$url = COMIC_JET_URL . __( 'comic' ) . '/' . $this->current_page['slug'] . '/' . $previous_page_number . '/' . $this->current_page['current_languages'][0] . '/' . $this->current_page['current_languages'][1];

	echo '
<a href="' . esc_attr( $url ) . '">' . __( 'Previous' ) . '</a>';
}




echo '

<hr />';




if ( isset( $bubble_image[1] ) ) {
	echo'

<script>
   function toggle_image() {
        var img = document.getElementById("bubble").src;
        if (img.indexOf(\'' . $bubble_image[0] . '\')!=-1) {
            document.getElementById("bubble").src  = "' . $bubble_image[1] . '";
        }
         else {
           document.getElementById("bubble").src = "' . $bubble_image[0] . '";
       }

    }
</script>';
}
