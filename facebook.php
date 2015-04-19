<?php

/**
 * A simple Facebook PHP example.
 * https://gist.github.com/daaku/818006
 *
 * - This is not a "Facebook SDK".
 * - This example uses Curl, Hash, JSON, Session extensions.
 * - This does not use the JavaScript SDK, nor the cookie set by it.
 * - This works with Canvas, Page Tabs with IFrames, the Registration Plugin
 *   and with any other flow which uses the signed_request.
 *
 * Based on work by Naitik Shah <n@daaku.org>
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */

// Set configuration
define( 'FACEBOOK_APP_ID',     '374453866080639' );
define( 'FACEBOOK_APP_SECRET', 'c7994a22ca40230fe579503b8bb0619a' );
define( 'FACEBOOK_REDIRECT_URL', COMIC_JET_URL . __( 'signup' ) . '/' );




function idx($array, $key, $default=null) {
	return array_key_exists($key, $array) ? $array[$key] : $default;
}

class FacebookApiException extends Exception {
	public function __construct($response, $curlErrorNo) {
		$this->response = $response;
		$this->curlErrorNo = $curlErrorNo;
	}
}

class Facebook {
	public function __construct($opts) {
		$this->appId = $opts['appId'];
		$this->secret = $opts['secret'];
		$this->accessToken = idx($opts, 'accessToken');
		$this->userId = idx($opts, 'userId');
		$this->signedRequest = idx($opts, 'signedRequest', array());
		$this->maxSignedRequestAge = idx($opts, 'maxSignedRequestAge', 86400);
	}

	public function loadSignedRequest($signedRequest) {
		list($signature, $payload) = explode('.', $signedRequest, 2);
		$data = json_decode(self::base64UrlDecode($payload), true);
		if (isset($data['issued_at']) &&
				$data['issued_at'] > time() - $this->maxSignedRequestAge &&
				self::base64UrlDecode($signature) ==
					hash_hmac('sha256', $payload, $this->secret, $raw=true)) {
			$this->signedRequest = $data;
			$this->userId = idx($data, 'user_id');
			$this->accessToken = idx($data, 'oauth_token');
		}
	}

	public function api($path, $params=null, $method='GET', $domain='graph') {
		if (!$params) $params = array();
		$params['method'] = $method;
		if (!array_key_exists('access_token', $params) && $this->accessToken)
			$params['access_token'] = $this->accessToken;
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_HTTPHEADER     => array('Expect:'),
			CURLOPT_POSTFIELDS     => http_build_query($params, null, '&'),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_URL            => "https://$domain.facebook.com$path",
			CURLOPT_USERAGENT      => 'nshah-0.1',
		));
		$result = curl_exec($ch);
		$decoded = json_decode($result, true);
		$curlErrorNo = curl_errno($ch);
		curl_close($ch);

		if ($curlErrorNo !== 0 || (is_array($decoded) && isset($decoded['error'])))
			throw new FacebookApiException($decoded, $curlErrorNo);
		return $decoded;
	}

	public static function base64UrlDecode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
}

function FB() {
	$fb = new Facebook(array(
		'appId'  => FACEBOOK_APP_ID,
		'secret' => FACEBOOK_APP_SECRET,
	));
	header('P3P: CP=HONK'); // cookies for iframes in IE
	session_start();
	if (isset($_POST['signed_request'])) {
		$fb->loadSignedRequest($_POST['signed_request']);
echo 'posted';
		$_SESSION['facebook_user_id'] = $fb->userId;
		$_SESSION['facebook_access_token'] = $fb->accessToken;
	} else {
echo 'not posted';
		$fb->userId = idx($_SESSION, 'facebook_user_id');
		$fb->accessToken = idx($_SESSION, 'facebook_access_token');
	}
	return $fb;
}
