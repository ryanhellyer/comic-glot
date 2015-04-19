<?php

require( COMIC_JET_ROOT_DIR . 'inc/facebook-php-sdk/src/facebook.php' );
//require( 'facebook-php-sdk/src/facebook.php' );


 
// new facebook object to interact with facebook
$facebook = new Facebook(array(
 'appId' => FACEBOOK_APP_ID,
 'secret' => FACEBOOK_APP_SECRET,
));

//print_r( $facebook );

//
// if user is logged in on facebook and already gave permissions
// to your app, get his data:
$userId = $facebook->getUser();




$html .= '
	<div class="notice">
		<p>' . COMICJET_CURRENT_LANGUAGE . '
			' . __( 'Some random notice!' ) . '
		</p>
	</div>

</div>';


if ($userId) {
	//
	// already logged? show some data
	$userInfo = $facebook->api('/' + $userId);

	$html .= '<h3>Storing in Redis</h3>';
	$html .= 'Email: ' . $userInfo['email'] . '<br />';
	$html .= 'First_name: ' . $userInfo['first_name'] . '<br />';

	$html .= '<h3>Storing in flat file</h3>';
	$html .= 'ID: ' . $userInfo['id'] . '<br />';
	$html .= 'Birthday: ' . $userInfo['birthday'] . '<br />';
	$html .= 'Gender: ' . $userInfo['gender'] . '<br />';
	$html .= 'Last_name: ' . $userInfo['last_name'] . '<br />';
	$html .= 'Locale: ' . $userInfo['locale'] . '<br />';
	$html .= 'Timezone: ' . $userInfo['timezone'] . '<br />';
	$html .= 'Verified: ' . $userInfo['verified'] . '<br />';
} else {
 //
 // use javaascript api to open dialogue and perform
 // the facebook connect process by inserting the fb:login-button

	$html .= '
	<div id="fb-root"></div>
	<fb:login-button scope="email,user_birthday"></fb:login-button>';
}

$html .= '
	<script>
	var facebook_app_id = ' . FACEBOOK_APP_ID . ';
	</script>
	<script src="' . COMIC_ASSETS_URL . 'facebook.js"></script>';


$html .= '
<div class="inner">
<!--
	<h1>' . __( 'Register' ) . '</h1>
-->
';
