<?php


echo '<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" href=""/>

	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery-ui.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'sortable.min.js"></script>

</head>
<body>

<form method="post" action="" enctype="multipart/form-data">

	<img src="' . esc_attr( $this->current_image ) . '" />

	<div class="controls">';

		// Error messages
		if ( ! empty( $this->error ) ) {
			echo '<p class="error">';
			foreach( $this->error as $error ) {
				echo $this->error_messages[$error];
			}
			echo '</p>';
		}

		echo '
		<ul class="sortable">';

		foreach( $this->strips as $key => $strip ) {

			echo '
			<li>
				<input class="button alignright" type="submit" name="' . esc_attr( 'remove-page[' . $key . ']' ) . '" value="' . __( 'Remove' ) . '" />
				<h3>' . sprintf( __( 'Page %s' ), $key + 1 ) . '</h3>';

			foreach( $this->languages as $lang => $language ) {

				// Bail out if language is not used
				if ( false == $language['used'] ) {
					break;
				}

				echo '

				<h4>' . $this->languages[$lang]['name'] . '</h4>
				<input class="button alignright" type="submit" name="' . esc_attr( 'view-page[' . $key . '][' . $lang . ']' ) . '" value="' . __( 'View' ) . '" />
				<p>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][' . $lang . ']' ) . '" value="" />
				</p>';

				// Set image URL
				if ( isset( $strip[$lang] ) ) {
					$file_name = $strip[$lang];
				} else {
					$file_name = '';
				}

				echo '
				<input type="text" style="font-size:10px;color:#aaa;border:1px solid #ddd" name="' . esc_attr( 'strip_image[' . $key . ']['. $lang . ']' ) . '" value="' . esc_attr( $file_name ) . '" />';

			}

			echo '
			</li>';
		}

		echo '
		</ul>

		<p>
			<input type="submit" name="add-new-page" id="add-new-page" class="button" value="' . __( 'Add new page' ) . '" />
		</p>

		<h3>' . __( 'Select languages to use' ) . '</h3>';

		foreach( $this->languages as $lang => $language ) {

			// Set whether selected ornot
			if ( true == $language['used'] ) {
				$checked = 'checked="checked" ';
			} else {
				$checked = '';
			}

			echo '
		<p>
			<label>' . $language['name'] . '</label>
			<input ' . $checked . 'type="checkbox" name="' . esc_attr( 'language[' . $lang . ']' ) . '" value="1" />
		</p>';
		}

		echo '
		<p class="submit">
			<input type="submit" name="save" class="button" value="' . __( 'Save Changes' ) . '" />
		</p>

	</div>

	' . get_nonce_field() . '
</form>

<script>
jQuery(function($){ 

	// Allow for resorting rows
	$(".sortable").sortable({
		axis: "y", // Limit to only moving on the Y-axis
	});

});


</script>


</body>
</html>';
