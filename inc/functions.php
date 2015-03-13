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
