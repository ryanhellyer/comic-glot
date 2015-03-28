<?php

// Haven't sorted out language control on pages like this yet ...
$lang = 'en';



$count = 0;
foreach( $this->strip_list as $strip_slug => $x ) {
	$count++;
	$title = $this->db->get( 'title', $strip_slug );
	$edit_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/edit/';
	$comic_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/1/en/de/';

	$strips = $this->db->get( 'strips', $strip_slug );

	$thumbnail = $this->db->get( 'thumbnail', $strip_slug );

	if ( isset( $thumbnail[$lang] ) ) {
		$thumbnail_file = $thumbnail[$lang];

		echo '
		<div class="block" id="' . esc_attr( 'comic-' . $count ) . '">
			<a class="comic-link" href="' . esc_attr( $comic_url ) . '">
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
