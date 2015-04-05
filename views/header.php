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
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery-ui.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'sortable.min.js"></script>

</head>
<body class="' . esc_attr( $this->page_type ) . '">

<header id="site-header">
	<h1><a href="' . COMIC_JET_URL . '">Comic Jet!</a></h1>
<!--
	<nav id="primary">
		<ul>
			<li><a href="#">' . __( 'Sign up' ) . '</a></li>
			<li><a href="#">' . __( 'Login' ) . '</a></li>
			<li>
				<a href="#">' . __( 'English' ) . '</a>
			</li>
		</ul>
	</nav>
-->
	<nav id="language-selector">
		<ul>
			<li><a href="#">' . __( 'English' ) . '</a></li>
			<li><a href="#">' . __( 'Deutsch' ) . '</a></li>
		</ul>
	</nav>
	' . $comicjet_login->login_form() . '
</header>

<div id="wrap">';
