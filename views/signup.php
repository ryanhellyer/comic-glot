<?php

$script_vars['facebook_app_id'] = FACEBOOK_APP_ID;
$script_vars['lang_iso_code'] = 'en_US';



//$scripts[] = COMIC_ASSETS_URL . 'facebook.js';
$script_vars['lang_iso_code'] = 'en_US';
$scripts[]  = 'https://connect.facebook.net/' . 'en_US' . '/all.js';
$scripts[] = COMIC_ASSETS_URL . 'facebook.js';


require( COMIC_JET_ROOT_DIR . 'inc/facebook-php-sdk/src/facebook.php' );


// new facebook object to interact with facebook
$facebook = new Facebook(
	array(
		'appId' => FACEBOOK_APP_ID,
		'secret' => FACEBOOK_APP_SECRET,
	)
);

//
// if user is logged in on facebook and already gave permissions
// to your app, get his data:
$user_id = $facebook->getUser();


$html = '
	<div class="inner">
		<div id="contact" class="content">
			<h1 id="site-title">' . __( 'Sign up' ) . '</h1>';

if ( $user_id ) {
	//
	// already logged? show some data
	$user_info = $facebook->api( '/' + $user_id );

	// Log user details to file
	$log_array = array(
		'time'        => time(),
		'email'       => $user_info['email'],
		'first_name'  => $user_info['first_name'],
		'facebook_id' => $user_info['id'],
		'birthday'    => $$user_info['birthday'],
		'gender'      => $user_info['gender'],
		'last_name'   => $user_info['last_name'],
		'locale'      => $user_info['locale'],
		'timezone'    => $user_info['timezone'],
		'verified'    => $user_info['verified'],
	);

	// Loop through all items and sanitize them ready for logging
	foreach( $log_array as $key => $value ) {
		$sanitized_log_array[$key] = esc_html( $value );
	}
	$log_json = json_encode( $sanitized_log_array ) . "\n";
	file_put_contents( COMIC_JET_ROOT_DIR . 'users.log', $log_json, FILE_APPEND | LOCK_EX );




print_r( $_COOKIES );
/*
foreach ( $_COOKIES as $cookie_id => $cookie_value ) {
	echo $cookie_id;
	setcookie( $cookie_id, NULL, 1, "/", ".domain.name" );
}
*/


	$html .= '<h3>Storing in Redis</h3>';
	$html .= 'Email: ' . esc_html( $user_info['email'] ) . '<br />';
	$html .= 'First_name: ' . esc_html( $user_info['first_name'] ) . '<br />';

	$html .= '<h3>Storing in flat file</h3>';
	$html .= 'ID: ' . esc_html( $user_info['id'] ) . '<br />';
	$html .= 'Birthday: ' . esc_html( $user_info['birthday'] ) . '<br />';
	$html .= 'Gender: ' . esc_html( $user_info['gender'] ) . '<br />';
	$html .= 'Last_name: ' . esc_html( $user_info['last_name'] ) . '<br />';
	$html .= 'Locale: ' . esc_html( $user_info['locale'] ) . '<br />';
	$html .= 'Timezone: ' . esc_html( $user_info['timezone'] ) . '<br />';
	$html .= 'Verified: ' . esc_html( $user_info['verified'] ) . '<br />';
} else {
 //
 // use javaascript api to open dialogue and perform
 // the facebook connect process by inserting the fb:login-button

	$html .= '
<a href="#" onclick="fb_login();"><img src="' . COMIC_ASSETS_URL . 'facebook-signin.png" border="0" alt=""></a>
<script>
</script>




	<div id="fb-root"></div>
<!--
	<fb:login-button scope="email,user_birthday"></fb:login-button>
-->
';
}
