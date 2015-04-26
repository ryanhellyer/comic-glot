<?php

/**
 * Returns true if current user is an administrator.
 * 
 * @return   bool   True if user is an admin
 */
function current_user_is_admin() {
	return true;
}

/**
 * Translate strings.
 * 
 * @param   string  $string  The string to be translated
 * @return  string  The translated string
 */
function __( $string ) {

	$strings = array(
		'Learn languages from comics' => array(
			'de' => 'Sprachen lernen aus Comics',
		),
		'I speak' => array(
			'de' => 'Ich spreche',
		),
		'I want to learn' => array(
			'de' => 'Ich will lernen',
		),
		'Start learning' => array(
			'de' => 'Lernen Sie',
		),
		'Some random notice!' => array(
			'de' => 'Das ist Deutsch ;)',
		),
		'signup' => array(
			'de' => 'anmeldung',
		),
	);

	if (
		'en' == COMICJET_CURRENT_LANGUAGE
		||
		! isset( $strings[$string][COMICJET_CURRENT_LANGUAGE] )
	) {
		return $string;
	}

	$translated_string = $strings[$string][COMICJET_CURRENT_LANGUAGE];

	return $translated_string;
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
 * Escape HTML.
 *
 * @param   string  $html   The HTML to be escaped
 * @return  string          The escaped HTML
 */
function esc_html( $html ) {
	$html = filter_var( $html, FILTER_SANITIZE_STRIPPED );
	return $html;
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

/**
 * Sanitizes a filename, replacing whitespace with dashes.
 * Adapted from WordPress core.
 * 
 * Removes special characters that are illegal in filenames on certain
 * operating systems and special characters requiring special escaping
 * to manipulate at the command line. Replaces spaces and consecutive
 * dashes with a single dash. Trims period, dash and underscore from beginning
 * and end of filename.
 *
 * @param string $filename The filename to be sanitized
 * @return string The sanitized filename
 */
function sanitize_file_name( $filename ) {
	$special_chars = array( '?', '[', ']', '/', "\\", '=', '<', '>', ':', ';', ',', "'", '\"', '&', '$', '#', '*', '(', ')', '|', '~', '`', '!', '{', '}', chr( 0 ) );

	/**
	 * Filter the list of characters to remove from a filename.
	 */
	$filename = preg_replace( "#\x{00a0}#siu", ' ', $filename );
	$filename = str_replace( $special_chars, '', $filename );
	$filename = str_replace( array( '%20', '+' ), '-', $filename );
	$filename = preg_replace( '/[\r\n\t -]+/', '-', $filename );
	$filename = trim( $filename, '.-_' );

	// Split the filename into a base and extension[s]
	$parts = explode( '.', $filename );

	// Return if only one extension
	if ( count( $parts ) <= 2 ) {
		return $filename;
	}

	// Process multiple extensions
	$filename = array_shift( $parts );
	$extension = array_pop( $parts );
	$mimes = get_allowed_mime_types();

	/*
	 * Loop over any intermediate extensions. Postfix them with a trailing underscore
	 * if they are a 2 - 5 character long alpha string not in the extension whitelist.
	 */
	foreach ( (array) $parts as $part ) {
		$filename .= '.' . $part;

		if ( preg_match( "/^[a-zA-Z]{2,5}\d?$/", $part ) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( ! $allowed ) {
				$filename .= '_';
			}
		}
	}
	$filename .= '.' . $extension;

	return $filename;
}

/**
 * Sanitize the window dimensions.
 * Must be in the format of numberxnumber.
 * 
 * @param   string  $dimensions   The window dimensions
 * @return  string  The sanitized window dimensions
 */
function sanitize_window_dimensions( $dimensions ) {
	$dimensions_array = explode( '-', $dimensions );

	// Bail out if more than two dashes
	if ( 2 != count( $dimensions_array) ) {
		return false;
	}

	// Loop through each location
	foreach( $dimensions_array as $key => $coordinates ) {
		$coordinates_array = explode( 'x', $coordinates );

		// Bail out if more than two coordinates
		if ( 2 != count( $coordinates_array ) ) {
			return false;
		}

		// Loop through each coordinate
		foreach( $coordinates_array as $id => $coordinate ) {

			// Bail out if coordinate is not numeric or between 0 and 100
			if ( ! is_numeric( $coordinate ) || $coordinate > 100 || $coordinate < 0) {
				return false;
			}

		}

	}

	return $dimensions;
}

/**
 * Sanitize page slug.
 * Code adapted from http://code.google.com/p/php-slugs/
 * GNU GPL v3
 * 
 * @param    string   $string       Page slug
 * @return   string   The sanitized page slug
 */
function sanitize_slug( $string ) {
	$string = remove_accents( $string );
	$string = symbols_to_words( $string );
	$string = strtolower( $string ); // Force lowercase
	$space_chars = array(
		" ", // space
		"…", // ellipsis
		"–", // en dash
		"—", // em dash
		"/", // slash
		"\\", // backslash
		":", // colon
		";", // semi-colon
		".", // period
		"+", // plus sign
		"#", // pound sign
		"~", // tilde
		"_", // underscore
		"|", // pipe
	);
	foreach( $space_chars as $char ) {
		$string = str_replace( $char, '-', $string ); // Change spaces to dashes
	}
	// Only allow letters, numbers, and dashes
	$string = preg_replace( '/([^a-zA-Z0-9\-]+)/', '', $string );
	$string = preg_replace( '/-+/', '-', $string ); // Clean up extra dashes
	if ( substr( $string, -1 ) === '-' ) { // Remove - from end
		$string = substr( $string, 0, -1 );
	}

	if ( substr( $string, 0, 1 ) === '-' ) { // Remove - from start
		$string = substr($string, 1);
	}

	// Limit number of characters in slug
	$string = substr( $string, 0 , 50 );

	return $string;
}

/**
 * Borrowed from WordPress
 * Converts all accent characters to ASCII characters.
 *
 * If there are no accent characters, then the string given is just returned.
 *
 * @param   string  $string   Text that might have accent characters
 * @return  string  Filtered string with replaced "nice" characters.
 */
function remove_accents( $string ) {

	if ( ! preg_match( '/[\x80-\xff]/', $string ) ) {
		return $string;
	}

	if ( $this->seems_utf8( $string ) ) {
		$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => ''
		);
		$string = strtr( $string, $chars );
	} else {
		// Assume ISO-8859-1 if not UTF-8
		$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
		.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
		.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
		.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
		.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
		.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
		.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
		.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
		.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
		.chr(252).chr(253).chr(255);
		$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";
		$string = strtr($string, $chars['in'], $chars['out']);
		$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
		$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
		$string = str_replace($double_chars['in'], $double_chars['out'], $string);
	}
	return $string;
}

/**
 * Convert symbols to words.
 *
 * @param   string  $string   The string
 * @return  string  The modified string with no symbols
 */
function symbols_to_words( $string ) {
	$string = str_replace( '@', ' at ', $string );
	$string = str_replace( '%', ' percent ', $string );
	$string = str_replace( '&', ' and ', $string );
	return $string;
}
