<?php


echo '<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="' . COMIC_ASSETS_URL . 'style.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css"/>

	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'jquery-ui.js"></script>
	<script type="text/javascript" src="' . COMIC_ASSETS_URL . 'sortable.min.js"></script>

</head>
<body>

<form method="post" action="" enctype="multipart/form-data">

	<img src="' . htmlspecialchars( $this->current_image, ENT_QUOTES ) . '" />

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
				<input class="button alignright" type="submit" name="' . htmlspecialchars( 'remove-page[' . $key . ']', ENT_QUOTES ) . '" value="' . __( 'Remove', 'comic-glot' ) . '" />
				<h3>' . sprintf( __( 'Page %s' ), $key + 1 ) . '</h3>';

			foreach( $this->languages as $lang => $language ) {

				// Bail out if language is not used
				if ( false == $language['used'] ) {
					break;
				}

				echo '

				<h4>' . $this->languages[$lang]['name'] . '</h4>
				<input class="button alignright" type="submit" name="' . htmlspecialchars( 'view-page[' . $key . '][' . $lang . ']', ENT_QUOTES ) . '" value="' . __( 'View', 'comic-glot' ) . '" />
				<p>
					<input type="file" name="' . htmlspecialchars( 'file-upload[' . $key . '][' . $lang . ']', ENT_QUOTES ) . '" value="" />
				</p>';

				// Set image URL
				if ( isset( $strip[$lang] ) ) {
					$url = COMIC_ASSETS_URL . 'strips/' . $strip[$lang];
				} else {
					$url = '';
				}

				echo '
				<input type="hidden" name="' . htmlspecialchars( 'strip_image[' . $key . ']['. $lang . ']', ENT_QUOTES ) . '" value="' . htmlspecialchars( $url, ENT_QUOTES ) . '" />';

			}

			echo '
			</li>';
		}

		echo '
		</ul>

		<p>
			<input type="submit" name="add-new-page" id="add-new-page" class="button" value="' . __( 'Add new page', 'comic-glot' ) . '" />
		</p>

		<h3>' . __( 'Select languages to use', 'comic-glot' ) . '</h3>';

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
			<input ' . $checked . 'type="checkbox" name="' . htmlspecialchars( 'language[' . $lang . ']', ENT_QUOTES ) . '" value="1" />
		</p>';
		}

		echo '
		<p class="submit">
			<input type="submit" name="save" class="button" value="' . __( 'Save Changes', 'comic-glot' ) . '" />
		</p>

	</div>

	' . wp_nonce_field( COMIC_NONCE, COMIC_NONCE ) . '
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
