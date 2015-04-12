<?php



$html .= '
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
			setCookie("comicjet_logged",r.email);
		});
	});

	</script>


	<script>
		hello.init({ 
			facebook : "374453866080639"
		},{	scope:"email",redirect_uri:"redirect.html"});
	</script>
';




$html .= '
<div class="inner">
<!--
	<h1>' . __( 'Register' ) . '</h1>
-->

	<div class="notice">
		<p>' . COMICJET_CURRENT_LANGUAGE . '
			' . __( 'Some random notice!' ) . '
		</p>
	</div>

	<button onclick="comicjet_facebook_login();">Facebook</button>

</div>';
