<?php


echo '<!DOCTYPE html>

<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />
<link href="//fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600" rel="stylesheet" type="text/css" />

';
if ( 'edit_comic' == $this->page_type ) {
	echo '
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery-ui.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'sortable.min.js"></script>';
}


$home_url = COMIC_JET_URL;
if ( isset( $this->language2 ) ) {
	$home_url .= $this->language1 . '/' . $this->language2 . '/';
} elseif ( 'en' != $this->language1 ) {
	$home_url .= $this->language1 . '/';
}

echo '
	<script>var comicjet_home_url = "' . esc_attr( $home_url ) . '";</script>
</head>
<body class="' . esc_attr( $this->page_type ) . '">

<header id="site-header">
	<h1><a href="' . esc_attr( $home_url ) . '">Comic Jet!</a></h1>

	<nav id="primary">
		<ul>
			<li><a href="#">' . __( 'Sign up' ) . '</a></li>
			<li><a href="#">' . __( 'Login' ) . '</a></li>
		</ul>
	</nav>';
/*
echo '
	<nav id="language-selector">
		<ul>';

foreach( $this->language_options() as $url => $language ) {
	echo '
			<li><a href="' . esc_attr( $url ) . '">' . $language . '</a></li>';
}

echo '
		</ul>
	</nav>
	' . $comicjet_login->login_form() . '
 */	
echo '
</header>

<div id="wrap">';
