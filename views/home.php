<?php

echo '
<div class="inner">
	<div class="content">

		<h1 id="site-title">' . __( 'Learn from comics' ) . '</h1>

		<form id="comic-type">
			<select onchange="javascript:location.href = this.value;">
				<option value="' . COMIC_JET_URL . '">' . __( 'Read comics in English' ) . '</option>
				<option value="' . COMIC_JET_URL . 'de/en/">' . sprintf( __( 'Learn %s via %s' ), 'Deutsch', 'English' ) . '</option>
				<option value="' . COMIC_JET_URL . 'en/de/">' . sprintf( __( 'Learn %s via %s' ), 'English', 'Deutsch' ) . '</option>
			</select>
		</form>

		<div id="comic-selection">';


// Haven't sorted out language control on pages like this yet ...
$lang = 'en';


$count = 0;
$strip_list = $this->db->get( 'strip_list', 'default' );
foreach( $strip_list as $strip_slug => $x ) {
	$title = $this->db->get( 'title', $strip_slug );
	$edit_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/edit/';
	$comic_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/1/en/de/';

	$strips = $this->db->get( 'strips', $strip_slug );

	$thumbnail = $this->db->get( 'thumbnail', $strip_slug );

	if ( isset( $thumbnail[$lang] ) ) {
		$thumbnail_file = $thumbnail[$lang];
		$count++;

		echo '
		<div class="block" id="' . esc_attr( 'comic-' . $count ) . '">
			<div class="block-inner">
				<img src="' . esc_attr( COMIC_STRIPS_URL . $thumbnail_file ) . '" />
				<p>' . $title . '</p>
			</div>';

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
