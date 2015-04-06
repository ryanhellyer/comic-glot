<?php

$html = '<!DOCTYPE html>

<html lang="' . esc_attr( $this->available_languages[$this->language1]['iso'] ) . '">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />

';

$home_url = COMIC_JET_URL;
if ( isset( $this->language2 ) ) {
	$home_url .= $this->language1 . '/' . $this->language2 . '/';
} elseif ( 'en' != $this->language1 ) {
	$home_url .= $this->language1 . '/';
}

$html .= '
	<script>var comicjet_home_url = "' . COMIC_JET_URL . '";</script>
</head>
<body class="' . esc_attr( $this->page_type ) . '">

<header id="site-header">
	<h1><a href="' . esc_attr( $home_url ) . '">COMIC JET!</a></h1>

	<nav id="primary">
		<ul>
			<li><a href="#" onclick="alert(\'Coming once this reaches beta!\');">' . __( 'Sign up' ) . '</a></li>
			<li><a href="#" onclick="alert(\'Coming once this reaches beta!\');">' . __( 'Login' ) . '</a></li>
		</ul>
	</nav>';

$html .= '
</header>

<div id="wrap">';
