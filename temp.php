<?php

$response  = 'access_token=CAAFUkFWsKX8BAKVA6FmksZB7RWNxxmVkwd8S3gy1NzjrYgg53mLyutIgxQc3ZCKli2Kn005FnixxqR11a4c2P9tynTcJ3bPiVHAb1HPQWZCBq7tS3ZCBZAg9WrETfqRxwXFMRAIWf1lRdkPe253ZAIUkfv60BaRnhwbXwSkkhUwlxEyLsBwZCAOkh7dAWa0JN9633wgxoUhFy8i4JujizcK&expires=5182290Access ';
//	$response = file_get_contents( $url );
//print_r( $response );
	$response = explode( '=', $bla );

	$access_token = $response[1];
	$access_token = explode( '&', $access_token );
	$access_token = $access_token[0];

	$expires = $response[2];

	echo 'Access token: ' . $access_token . '<br />';
	echo 'Expires: ' . $expires . '<br />';

die;