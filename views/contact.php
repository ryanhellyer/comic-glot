<?php
/**
 * The contact page template.
 */

$html = '
	<div class="inner">
		<div id="contact" class="content">';

/**
 * If contact form submitted, then process data.
 */
if ( isset( $_POST['message'] ) || isset( $_POST['email'] ) ) {

	$message = $_POST['email'];
	$message = filter_var( $message, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW );
	$message = wordwrap( $message, 70, "\r\n" ); // In case any of our lines are larger than 70 characters, we should use wordwrap()

	// Subject
	if ( isset( $_GET['report'] ) ) {
		$url = filter_var( COMIC_JET_URL . ltrim( $_GET['report'], '/' ), FILTER_SANITIZE_URL);
		$subject = 'Comic Jet bug report:' . $url;
	} else {
		$subject = 'Comic Jet contact form';
	}

	// From address
	$from = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );

	$headers = 'To: Ryan Hellyer <' . COMIC_JET_EMAIL. '>' . "\r\n";
	$headers .= 'From: Birthday Reminder <' . $from . '>' . "\r\n";

	/**
	 * Send the mail.
	 */
	mail( COMIC_JET_EMAIL, $subject, $message, $headers );

	$html .= '
			<h1 id="site-title">' . __( 'Message sent' ) . '</h1>';

	if ( isset( $url ) ) {
		$html .= '
			<p id="back"><a id="submit" href="' . esc_attr( $url ) . '">' . __( 'Back' ) . '</a></p>';
	}

} else {

	/**
	 * Grab the page title.
	 */
	if ( isset( $_GET['report'] ) ) {
		$title = __( 'Report bug' );
	} else {
		$title = __( 'Contact us' );
	}

	/**
	 * Output the page.
	 */
	$html .= '

			<h1 id="site-title">' . $title . '</h1>

			<form name="contact" method="post">
				<p>
					<label for="name">' . __( 'Name' ) . '</label>
					<input id="name" name="name" type="text" placeholder="" value="" />
				</p>
				<p>
					<label for="email">' . __( 'Email' ) . '</label>
					<input id="email" name="email" type="email" placeholder="" value="" />
				</p>
				<p>
					<label>' . __( 'Message' ) . '</label>
					<textarea id="message" name="message"></textarea>
				</p>
				<p>
					<input id="submit" type="submit" name="submit" value="' . __( 'Send' ) . '" />
				</p>
			</form>';
}

$html .= '

		</div>
	</div>';
