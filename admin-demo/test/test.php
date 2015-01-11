<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//if ( ! empty( $_POST ) || ! empty( $_FILES ) ) {
function bla() {
	ob_start();
	echo "POST:\n\n";
	print_r( $_POST );
	echo "\n\n\n\nGET:\n\n";
	print_r( $_GET );
	echo "\n\n\n\nFiles:\n\n";
	print_r( $_FILES );
	$post = ob_get_contents();
	ob_end_clean();
	file_put_contents( 'bla.txt', $post );
	//	exit;
}
bla();

?>
<!DOCTYPE html>
<html>
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
	<script src="https://ryan.hellyer.kiwi/wp-includes/js/jquery/jquery.js"></script>
	<script src="jquery.ajaxfileupload.js"></script>
</head>
<body>

<form action="http://local.wordpress-trunk.dev/wp-content/mu-plugins/test/test.php" method="POST" enctype="multipart/form-data">
	<div style="background:red;width:400px;height:400px;" />
		<input name="ryan1" class="ryan" style="width:400px;height:400px;opacity:0;" type="file" multiple="multiple" id="upload_field" />
	</div>
	<input type="text" name="ryan2" class="ryan" value="" />
	<input type="submit" value="submit" />
</form>

<script>
jQuery(document).ready(function($) {	

	$(".ryan").change(function () {

		$(this).ajaxfileupload({
			'action': 'http://local.wordpress-trunk.dev/wp-content/mu-plugins/test/test.php'
		});

	});

});
</script>

</body>
</html>