<?php

$current_language = $this->language1;

$html .= '
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
	$meta_file_path = $comic_dir . '/' . $name . '/' . $name . '.txt';
	if ( file_exists( $meta_file_path ) ) {
		$meta = file_get_contents( $meta_file_path );
		$meta = explode( "\n", $meta );

		foreach( $meta as $key => $meta_bit ) {
			$title_info = $meta_bit;
			$title_exploded = explode( ':', $title_info );

			if ( $this->language1 == $title_exploded[0] ) {
				$title = $title_exploded[1];
			}
		}

	}

	$url_bit = COMIC_JET_URL . __( 'comic' ) . '/' . $name . '/';
	$comic_url = $url_bit . '0/' . $this->language1 . '/';
	if ( isset( $this->language2 ) ) {
		$comic_url .= $this->language2 . '/';
	}
	$edit_url = $url_bit . 'edit/';

	$html .= '
		<div class="block" id="' . esc_attr( 'comic-' . $count ) . '">
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
