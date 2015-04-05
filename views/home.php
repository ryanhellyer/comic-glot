<?php

$current_language = $this->language1;

echo '
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

	echo '
			<option' . $selected . ' value="' . esc_attr( $lang ) . '">' . $language['name'] . '</option>';
}

echo '
			</select>

			<span></span>

			<label>' . __( 'I want to learn' ) . '</label>
			<select id="language2" name="language2">';

$available_languages_two = $this->available_languages;
unset( $available_languages_two['en'] );
$available_languages_two['en'] = $this->available_languages['en'];
foreach( $available_languages_two as $lang => $language ) {

	if ( $this->language2 == $lang ) {
		$selected = ' selected="selected"';
	} else {
		$selected = '';
	}

	echo '
			<option' . $selected . ' value="' . esc_attr( $lang ) . '">' . $language['name'] . '</option>';
}

echo '
			</select>

			<span></span>

			<input type="submit" id="select-language" name="select-language" value="' . __( 'Start learning' ) . '&nbsp; &gt;" />
		</form>

		<div id="comic-selection">';


// Haven't sorted out language control on pages like this yet ...
$lang = 'en';


$count = 0;
$strip_list = $this->db->get( 'strip_list', 'default' );
foreach( $strip_list as $strip_slug => $x ) {
	$title = $this->db->get( 'title', $strip_slug );
	$edit_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/edit/';
	$comic_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/1/' . $this->language1 . '/';
	if ( isset( $this->language2 ) ) {
		$comic_url .= $this->language2 . '/';
	}

	$strips = $this->db->get( 'strips', $strip_slug );

	$thumbnail = $this->db->get( 'thumbnail', $strip_slug );

	if ( isset( $thumbnail[$lang] ) ) {
		$thumbnail_file = $thumbnail[$lang];
		$count++;

		echo '
		<div class="block" id="' . esc_attr( 'comic-' . $count ) . '">
			<a href="' . esc_attr( $comic_url ) . '" class="block-inner">
				<img src="' . esc_attr( COMIC_STRIPS_URL . $thumbnail_file ) . '" />
				<p>' . $title . '</p>
			</a>';

		// Show edit link for admins
		if ( $comicjet_login->current_user_is_admin() ) {
			echo '
			<a class="edit-link" href="' . esc_attr( $edit_url ) . '">' . __( 'Edit' ) . '</a>';
		}

		echo '
		</div>';
	}

}


echo '
		</div>
	</div>
</div>
';
