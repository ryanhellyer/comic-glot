<?php

$script_vars['facebook_app_id'] = FACEBOOK_APP_ID;
$scripts[] = COMIC_ASSETS_URL . 'facebook.js';


require( COMIC_JET_ROOT_DIR . 'inc/facebook-php-sdk/src/facebook.php' );


// new facebook object to interact with facebook
$facebook = new Facebook(array(
 'appId' => FACEBOOK_APP_ID,
 'secret' => FACEBOOK_APP_SECRET,
));

//
// if user is logged in on facebook and already gave permissions
// to your app, get his data:
$user_id = $facebook->getUser();


$html = '
	<div class="inner">
		<h1>' . __( 'Register' ) . '</h1>
';

$html .= '
		<div class="notice">
			<p>' . COMICJET_CURRENT_LANGUAGE . '
				' . __( 'Some random notice!' ) . '
			</p>
		</div>
';



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
	$log_json = json_encode( $log_array ) . "\n";
	file_put_contents( COMIC_JET_ROOT_DIR . 'users.log', $log_json, FILE_APPEND | LOCK_EX );



	$html .= '<h3>Storing in Redis</h3>';
	$html .= 'Email: ' . $user_info['email'] . '<br />';
	$html .= 'First_name: ' . $user_info['first_name'] . '<br />';

	$html .= '<h3>Storing in flat file</h3>';
	$html .= 'ID: ' . $user_info['id'] . '<br />';
	$html .= 'Birthday: ' . $user_info['birthday'] . '<br />';
	$html .= 'Gender: ' . $user_info['gender'] . '<br />';
	$html .= 'Last_name: ' . $user_info['last_name'] . '<br />';
	$html .= 'Locale: ' . $user_info['locale'] . '<br />';
	$html .= 'Timezone: ' . $user_info['timezone'] . '<br />';
	$html .= 'Verified: ' . $user_info['verified'] . '<br />';
} else {
 //
 // use javaascript api to open dialogue and perform
 // the facebook connect process by inserting the fb:login-button

	$html .= '
	<div id="fb-root"></div>
	<fb:login-button scope="email,user_birthday"></fb:login-button>';
}
