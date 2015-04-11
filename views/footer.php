<?php

$html .= '
</div>

<footer>
	<p>
		Copyright &copy; 2015 Comic Jet.
		Generated in ' . round( 1000 * ( microtime( true ) - COMIC_JET_START_TIME ), 1 ) . ' ms.
	</p>
	<p class="align-right">
		<a href="' . esc_attr( COMIC_JET_URL . __( 'contact' ) . '/?report=' . $_SERVER['REQUEST_URI'] ) . '">Report bug</a>
	</p>
</footer>
';

if ( 'home' == $this->page_type ) {
	$html .= '
<script>

// Switching off form submission
document.getElementById("select-language").type = "button";

// Redirecting after language selector clicked (required for offline use, when POST will not work)
document.getElementById("select-language").onclick = function(){
	var language1 = document.getElementById("language1").value;
	var language2 = document.getElementById("language2").value;
	var new_url = comicjet_home_url+language1+"/"+language2+"/";
	window.location.assign(new_url);
};

</script>';
}

$html .= '

</body>
</html>';
