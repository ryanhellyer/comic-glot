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
	<script>
	var comicjet_home_url = "' . COMIC_JET_URL . '";
';

if ( isset( $this->slug ) ) {
	$html .= '
	var comicjet_slug = "' . $this->slug . '";';
}

$html .= '
	</script>



<script>

function setCookie(cname,cvalue) {
	var d = new Date();
	d.setTime(d.getTime() + (10*365*24*60*60*1000));
	var expires = "expires="+d.toUTCString();

	document.cookie = cname + "=" + cvalue + "; " + expires + ";path=/;domain=' . COMIC_JET_DOMAIN . '";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(";");
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==" ") c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

</script>
';

$html .= '

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

$html .= '
</header>

<div id="wrap">';
