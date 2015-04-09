<?php

$html = '<!DOCTYPE html>

<html lang="' . esc_attr( $this->available_languages[$this->language1]['iso'] ) . '">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Jet</title>

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
';
/*
	<script src="' . COMIC_ASSETS_URL . 'hello.min.js"></script>

	<script>
		function comicjet_facebook_login() {

			hello( "facebook" ).login().then( function(){
				alert("You are signed in to Facebook");
			}, function( e ){
				alert("Signin error: " + e.error.message );
			});

		}

hello.on("auth.login", function(auth){

	// call user information, for the given network
	hello( auth.network ).api( "/me" ).then( function(r){
		console.log(r);
		alert(r.email);
	});

});

	</script>


	<script>
		hello.init({ 
			facebook : "374453866080639"
		},{	scope:"email",redirect_uri:"redirect.html"});
	</script>
*/
$html .= '

</head>
<body class="' . esc_attr( $this->page_type ) . '">

<header id="site-header">
	<h1><a href="' . esc_attr( $home_url ) . '">COMIC JET! <small style="
						text-shadow: none;
						-webkit-text-stroke-width: 0.3px;
						-webkit-text-stroke-color: #aa5500;
						text-stroke-width: 0;
						border:none;
						position: relative;
						top: -11px;
						left: 10px;
						font-weight: bold;
						letter-spacing:0;
						font-size:30px;
						color: #ffcc00;
						font-family:sans-serif;">(semi-private alpha)</small></a></h1>

	<nav id="primary">
		<ul>
<!--
			<li><button onclick="comicjet_facebook_login();">Facebook</button></li>
-->
			<li><a href="#" onclick="alert(\'Coming once this reaches beta!\');">' . __( 'Sign up' ) . '</a></li>
			<li><a href="#" onclick="alert(\'Coming once this reaches beta!\');">' . __( 'Login' ) . '</a></li>
		</ul>
	</nav>';

$html .= '
</header>

<div id="wrap">';
