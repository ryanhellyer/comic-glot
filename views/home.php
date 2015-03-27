<?php

$count = 0;
foreach( $this->strip_list as $strip_slug => $x ) {
	$count++;
	$title = $this->db->get( 'title', $strip_slug );
	$edit_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/edit/';
	$comic_url = COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/1/en/de/';

	$strips = $this->db->get( 'strips', $strip_slug );
	if ( isset( $strips[0]['current_background'] ) ) {
		$thumbnail_image_url = $strips[0]['current_background'];
		echo '
		<a href="' . esc_attr( $comic_url ) . '" id="' . esc_attr( 'comic-' . $count ) . '">
			<img src="' . esc_attr( COMIC_STRIPS_URL . $thumbnail_image_url ) . '" />
			<p>' . $title . '</p>
			<a href="' . esc_attr( $edit_url ) . '">' . __( 'Edit' ) . '</a>
		</a>';
	}
}
