<?php


echo '<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />

	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery-ui.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'sortable.min.js"></script>

</head>
<body>

<a href="' . COMIC_JET_URL . '">Home</a>
';

foreach( $this->strip_list as $strip_slug => $x ) {
	$title = $this->db->get( 'title', $strip_slug );
	echo "\n|";
	echo '<a href="' . COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/edit/">' . __( 'Edit' ) . ' ' . $title . '</a>';
	echo "\n|";
	echo '<a href="' . COMIC_JET_URL . __( 'comic' ) . '/' . $strip_slug . '/1/en/">' . $title . '</a>';
}

echo '
|
<a href="' . COMIC_JET_URL . 'asfasf">404</a>
<hr />
';
