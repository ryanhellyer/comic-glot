<?php

$current_language = $this->language1;


// Queue'ing langauge selector script
$scripts[] = COMIC_ASSETS_URL . 'language-selector.js';

// Set read/reading information for each comic
$scripts[] = COMIC_ASSETS_URL . 'read-reading.js';

$script_vars['text_comic_slug']     = __( 'comic' );
$script_vars['text_already_read']   = __( 'Already read' );
$script_vars['text_reading']        = __( 'Reading' );

$script_vars['comicjet_language1']  = $this->language1;
$script_vars['comicjet_language2']  = $this->language2;

$html = '
<div class="inner">
	<div class="content">

		<h1 id="site-title">' . __( 'Learn languages from comics' ) . '</h1>

		<form id="comic-type" name="comic-type" method="post">

			<label>' . __( 'I speak' ) . '</label>
			<select id="language1" name="language1">';

foreach( $this->available_languages as $lang => $language ) {

	if ( $this->language1 == $lang ) {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}

	$html .= '
			<option' . $selected . ' value="' . esc_attr( $lang ) . '">' . $language['name'] . '</option>';
}

$html .= '
			</select>

			<span></span>

			<label>' . __( 'I want to learn' ) . '</label>
			<select id="language2" name="language2">';

$available_languages_two = $this->available_languages;
unset( $available_languages_two['en'] );
$available_languages_two['en'] = $this->available_languages['en'];
foreach( $available_languages_two as $lang => $language ) {

	if ( isset( $this->language2 ) && $this->language2 == $lang ) {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}

	$html .= '
			<option' . $selected . ' value="' . esc_attr( $lang ) . '">' . $language['name'] . '</option>';
}

$html .= '
			</select>

			<span></span>

			<input type="submit" id="select-language" name="select-language" value="' . __( 'Start learning' ) . '&nbsp; &gt;" />
		</form>

		<div id="comic-selection">';


$comic_dir = COMIC_JET_ROOT_DIR . '/comics/';
$folders = scandir( $comic_dir );
$count = 0;
foreach ( $folders as $name ) {

	// Ignore system paths
	if ( $name === '.' || $name === '..' ) {
		continue;
	}

	// Bail out if it isn't a folder
    if ( ! is_dir( $comic_dir . '/' . $name ) ) {
    	continue;
    }

    // Grab thumbnail URL
	$url_bit = COMIC_JET_URL . 'comics/' . $name . '/thumb-';
	$path_bit = $comic_dir . '/' . $name . '/thumb-';
	if ( file_exists( $path_bit . $this->language1 . '.png' ) ) {
		$thumbnail = $url_bit . $this->language1 . '.png';
	} elseif ( file_exists( $path_bit . $this->language1 . '.jpg' ) ) {
		$thumbnail = $url_bit . $this->language1 . '.jpg';
	} else {
		continue; // Bail out if no thumbnail present - we use this to determine if the comic is meant to be available or not
	}

	// If second language set, then bail out if no thumbnail present for that language
	if (
		isset( $this->language2 )
		&&
		! file_exists( $path_bit . $this->language2 . '.png' )
		&&
		! file_exists( $path_bit . $this->language2 . '.jpg' )
	) {
		continue;
	}

	// Get the title from the meta file data
	$meta = $this->get_meta( $name );
	if ( false != $meta ) {
		$lang1 = $this->language1;
		$title = $meta->title->language_strings->$lang1;
	} else {
		$title = 'UNTITLED';
	}

	$url_bit = COMIC_JET_URL . __( 'comic' ) . '/' . $name . '/';
	$comic_url = $url_bit . '0/' . $this->language1 . '/';
	if ( isset( $this->language2 ) ) {
		$comic_url .= $this->language2 . '/';
	}
	$edit_url = $url_bit . 'edit/';

	$html .= '
		<div class="block" id="' . esc_attr( 'comic-' . $name ) . '">
			<a href="' . esc_attr( $comic_url ) . '" class="block-inner">
				<img src="' . esc_attr( $thumbnail ) . '" />
				<p>' . $title . '</p>
			</a>';

	// Show edit link for admins
	if ( $comicjet_login->current_user_is_admin() ) {
		$html .= '
			<a class="edit-link" href="' . esc_attr( $edit_url ) . '">' . __( 'Edit' ) . '</a>';
	}

	$html .= '
		</div>';

	$count++;
}


if ( 0 == $count ) {
	$html .= '
		<p class="error">
			' . __( 'Sorry, but no comics match your selection.' ) . '
		</p>';
}


$html .= '
		</div>
	</div>
</div>
';

