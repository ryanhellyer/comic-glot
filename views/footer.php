<?php

// Prevent compounding query vars in request URI
$request_uri = explode( '?', $_SERVER['REQUEST_URI'] );
$request_uri = $request_uri[0];

$footer = '
</div>

<footer>
	<p>
		Copyright &copy; 2015 Comic Jet.
		Generated in ' . round( 1000 * ( microtime( true ) - COMIC_JET_START_TIME ), 1 ) . ' ms.
	</p>
	<p class="align-right">
		<a href="' . esc_attr( COMIC_JET_URL . __( 'contact' ) . '/?report=' . $request_uri ) . '">Report bug</a>
	</p>
</footer>
';



/**
 * Left and right arrow key support
 */
$scripts[] = COMIC_ASSETS_URL . 'arrow-key-support.js';

$script_vars['comicjet_root_url'] = COMIC_JET_URL;
if ( isset( $this->slug ) ) {
	$script_vars['comicjet_slug'] = $this->slug;
}
if ( isset( $prev_url ) ) {
	$script_vars['comicjet_prev_url'] = filter_var( $prev_url, FILTER_SANITIZE_URL );
}
if ( isset( $next_url ) ) {
	$script_vars['comicjet_next_url'] = filter_var( $next_url, FILTER_SANITIZE_URL );
}


/**_
 * Add script vars.
 */
if ( isset( $script_vars ) && is_array( $script_vars ) ) {
	$footer .= "<script>\n";
	foreach( $script_vars as $var => $value ) {
		$footer .= '	var ' . $var . ' = "' . $value . "\";\n";
	}
	$footer .= '</script>';
}

/**
 * Add scripts.
 */
if ( isset( $scripts ) && is_array( $scripts ) ) {
	foreach( $scripts as $var => $value ) {
		$footer .= "\n";
		$footer .= '<script src="' . $value . '"></script>';
	}
}


$footer .= '

</body>
</html>';
