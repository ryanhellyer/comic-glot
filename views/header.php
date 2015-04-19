<?php

$scripts[] = COMIC_ASSETS_URL . 'cookie-functions.js';

// Grab home URL
$home_url = COMIC_JET_URL;
if ( isset( $this->language2 ) ) {
	$home_url .= $this->language1 . '/' . $this->language2 . '/';
} elseif ( 'en' != $this->language1 ) {
	$home_url .= $this->language1 . '/';
}


$header = '<!DOCTYPE html>

<html lang="' . esc_attr( $this->available_languages[$this->language1]['iso'] ) . '">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Jet</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />
</head>
<body class="' . esc_attr( $this->page_type ) . '">

<header id="site-header">
	<h1>
		<a href="' . esc_attr( $home_url ) . '">COMIC JET! 
					<small style="
						display: block;
						text-shadow: none;
						-webkit-text-stroke-width: 0.3px;
						-webkit-text-stroke-color: #aa5500;
						text-stroke-width: 0;
						border:none;
						position: relative;
						font-weight: bold;
						letter-spacing:0;
						font-size:24px;
						color: #ffcc00;
						font-family:sans-serif;
					">
							<span>private</span> alpha
					</small>
		</a>
	</h1>

	<nav id="primary">
		<ul>

			<li><a href="' . COMIC_JET_URL . __ ('signup' ) . '/">' . __( 'Sign up' ) . '</a></li>
			<li><a href="#" onclick="alert(\'Coming once this reaches beta!\');">' . __( 'Login' ) . '</a></li>
		</ul>
	</nav>';

$header .= '
</header>

<div id="wrap">';
