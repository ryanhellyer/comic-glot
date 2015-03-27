<?php

/**
 * Controls the login process.
 */
class ComicJet_Login {

	public $username      = 'ryan';
	public $password      = '1231';
	public $nonce         = 'xxxx';
	public $password_hash = '$2y$10$cGiRDLK11XsfTlV6uMGdnu9hNe2.OUeD8Qzi3vxvoqcSNi2FWGs0a';
	public $cookie_name   = 'test-cookie';
	public $cookie_value  = 'xxxx';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		if ( isset( $_POST['submit'] ) ) {
			$this->login();
		}

		if ( isset( $_GET['logout'] ) ) {
			$this->logout();
		}
	}

	/**
	 * Log the user out.
	 */
	public function logout() {
		$logout_value = md5( $this->nonce );
		if ( $logout_value == $_GET['logout'] ) {
			unset( $_COOKIE[$this->cookie_name] );

			setcookie(
				$this->cookie_name,  // The test cookie name
				null,                // Set cookie value to null
				-1,                  // Expiry time
				'/',                 // Path
				COMIC_JET_DOMAIN,    // Cookie domain
				false,               // https only
				true                 // http only
			);

			header( 'Location: ' . COMIC_JET_URL, true, 302 );
		}
	}

	/**
	 * Log the user in.
	 */
	public function login() {
		if (
			isset( $_POST['username'] ) 
			&&
			isset( $_POST['password'] )
			&&
			isset( $_POST['nonce'] )
		) {

			// Nonce check
			if ( $this->nonce != $_POST['nonce'] ) {
				$this->error[] = 'failed-nonce';
			}

			// Username check
			if ( $this->username != $_POST['username'] ) {
				$this->error[] = 'wrong-username';
			}

			// Password check
			if ( $this->password != $_POST['password'] ) {
				$this->error[] = 'failed-password';
			}

			// Bail out now if errors found
			if ( ! empty( $this->error ) ) {
				echo '<h1>You fail</h1>';
				print_r( $this->error );
				die;
			}

			// If password matches, then log them in by setting the cookie
			$password_hash = password_hash( $_POST['password'], PASSWORD_DEFAULT );
			if ( password_verify( $_POST['password'], $this->password_hash ) ) {

				// Set cookie
				setcookie(
					$this->cookie_name,  // The test cookie name
					$this->cookie_value, // Cookie value
					-1,                  // Expiry time
					'/',                 // Path
					COMIC_JET_DOMAIN,    // Cookie domain
					false,               // https only
					true                 // http only
				);
				$_COOKIE[$this->cookie_name] = $this->cookie_value; // Set cookie for use immediately

			}

		}

	}

	/**
	 * Display login form.
	 */
	public function logout_url() {

		if ( isset( $_COOKIE[$this->cookie_name] ) && $this->cookie_value == $_COOKIE[$this->cookie_name] ) {
			echo '<a href="' . esc_attr( COMIC_JET_URL . '?logout=' . md5( $this->nonce ) ) . '">Log out</a>';
		} else {
			echo 'Not logged in.';
		}

		echo '
		<form action="" method="post">
			<input type="text" name="username" />
			<input type="password" name="password" />
			<input type="hidden" name="nonce" value="' . esc_attr( $this->nonce ) . '" />
			<input type="submit" name="submit" value="' . __( 'Submit' ) . '" />
		</form>';
	}

	/**
	 * Display login form.
	 */
	public function login_form() {

		if ( isset( $_COOKIE[$this->cookie_name] ) && $this->cookie_value == $_COOKIE[$this->cookie_name] ) {
			echo '<a href="' . esc_attr( COMIC_JET_URL . '?logout=' . md5( $this->nonce ) ) . '">Log out</a>';
		} else {
			echo 'Not logged in.';
		}

		echo '
		<form action="" method="post">
			<input type="text" name="username" />
			<input type="password" name="password" />
			<input type="hidden" name="nonce" value="' . esc_attr( $this->nonce ) . '" />
			<input type="submit" name="submit" value="' . __( 'Submit' ) . '" />
		</form>';
	}

}
