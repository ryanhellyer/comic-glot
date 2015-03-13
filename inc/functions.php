<?php

/**
 * Translate strings.
 * 
 * @param   string  $string  The string to be translated
 * @return  string  The translated string
 */
function __( $string ) {
	return $string;
}

/**
 * Escape attributes.
 *
 * @param   string  $attribute   The attribute to be escaped
 * @return  string  The escaped attribute
 */
function esc_attr( $attribute ) {
	return htmlspecialchars( $attribute, ENT_QUOTES );
}

/**
 * Get a nonce field.
 *
 * @return string  The nonce field
 */
function get_nonce_field() {
	return '<input type="hidden" name="nonce" value="' . md5( COMIC_NONCE ) . '" />';
}

/**
 * Verify a nonce.
 *
 * @param   string  $nonce   The nonce value
 * @return  bool  True if nonce is valid
 */
function verify_nonce( $nonce ) {
	if ( $nonce == md5( COMIC_NONCE ) ) {
		return true;
	} else {
		false;
	}
}
