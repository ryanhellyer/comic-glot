<?php

$html .= '
<div class="inner">
	<div class="content">

		<h1 id="site-title">' . sprintf( __( 'Editing "%s"' ), $this->get( 'title' ) ) . '</h1>

		<form method="post" action="" enctype="multipart/form-data">

	<!-- Hidden submit button - ensures that page is saved when enter hit -->
	<input type="submit" style="display:none" name="save" class="button" value="' . __( 'Save Changes' ) . '" />

	<div class="controls">';

		// Error messages
		if ( ! empty( $this->error ) ) {
			$html .= '<p class="error">';
			foreach( $this->error as $error ) {
				echo $this->error_messages[$error];
			}
			$html .= '</p>';
		}

		$html .= '
		<p>
			<label>' . __( 'Title' ) . '</label>
			<input type="text" name="title" value="' . esc_attr( $this->get( 'title' ) ) . '" />
		</p>';


		// Loop through langs
		foreach( $this->available_languages as $lang => $language ) {

			// Bail out if language is not used
			$used_languages = $this->get( 'languages' );
			if ( '' != $used_languages && array_key_exists( $lang, $used_languages ) ) {
				// Thumbnail input
				$html .= '
				<p>
					<label>Thumbnail - ' . $lang . '</label>
					<input type="file" name="' . esc_attr( 'file-upload[thumbnail][' . $lang . ']' ) . '" value="" />';

				// Add existing thumbnail
				$thumbnail = $this->get( 'thumbnail' );
				if ( isset( $thumbnail[$lang] ) ) {
					$file_name = $thumbnail[$lang];
					$html .= '
					<input type="text" name="' . esc_attr( 'thumbnail[' . $lang . ']' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}

				$html .= '
				</p>';

			}
		}

		$strips = $this->get( 'strips' );
		if ( is_array( $strips ) ) {
			$html .= '
		<ul class="sortable">';

			foreach( $strips as $key => $strip ) {

				$html .= '
			<li>
				<input class="button alignright" type="submit" name="' . esc_attr( 'remove-page[' . $key . ']' ) . '" value="' . __( 'Remove' ) . '" />
				<h3>' . sprintf( __( 'Page %s' ), $key + 1 ) . '</h3>';

				// Add current_background image
				$html .= '
				<p>
					<label>Background image</label>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][current_background]' ) . '" value="" />';

				if ( isset( $strips[$key]['current_background'] ) ) {
					$file_name = $strips[$key]['current_background'];
					$html .= '
					<input type="text" name="' . esc_attr( 'strip_image[' . $key . '][current_background]' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}

				$html .= '
				</p>';

				// Low resolution background image
				$html .= '
				<p>
					<label>Low res offline background image</label>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][current_background_lowres]' ) . '" value="" />
				</p>';
				if ( isset( $strips[$key]['current_background_lowres'] ) ) {
					$file_name = $strips[$key]['current_background_lowres'];
					$html .= '
				<input type="text" name="' . esc_attr( 'strip_image[' . $key . '][current_background_lowres]' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}


				$html .= '
				<p>' . __( 'Add window coordinates' ) . '</p>';

				$strip['window'][] = ''; // Add new window
				foreach( $strip['window'] as $window_id => $window_value ) {
					$html .= '
				<p>
					<input type="text" name="' . esc_attr( 'strip_image[' . $key . '][window][]' ) . '" value="' . esc_attr( $window_value ) . '" />
				</p>';
				}


				foreach( $this->available_languages as $lang => $language ) {

					// Bail out if language is not used
					if ( array_key_exists( $lang, $used_languages ) ) {

						$html .= '

				<h4>' . $language['name'] . '</h4>
				<input class="button alignright" type="submit" name="' . esc_attr( 'view-page[' . $key . '][' . $lang . ']' ) . '" value="' . __( 'View' ) . '" />
				<p>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][' . $lang . ']' ) . '" value="" />';


						// Set image URL
						if ( isset( $strip[$lang] ) ) {
							$file_name = $strip[$lang];
						} else {
							$file_name = '';
						}

						$html .= '
				<input type="text" name="' . esc_attr( 'strip_image[' . $key . ']['. $lang . ']' ) . '" value="' . esc_attr( $file_name ) . '" />';
					}

					$html .= '
				</p>';

				}

				$html .= '
			</li>';
			}

			$html .= '
		</ul>';
		}

		$html .= '

		<p>
			<input type="submit" name="add-new-page" id="add-new-page" class="button" value="' . __( 'Add new page' ) . '" />
		</p>

		<h3>' . __( 'Select languages to use' ) . '</h3>';

		foreach( $this->available_languages as $lang => $language ) {

			// Set whether selected or not
			if ( is_array( $used_languages ) && array_key_exists( $lang, $used_languages ) ) {
				$checked = 'checked="checked" ';
			} else {
				$checked = '';
			}

			$html .= '
		<p>
			<label>' . $language['name'] . '</label>
			<input ' . $checked . 'type="checkbox" name="' . esc_attr( 'language[' . $lang . ']' ) . '" value="1" />
		</p>';
		}

		$html .= '
		<p class="submit">
			<input type="submit" name="save" class="button" value="' . __( 'Save Changes' ) . '" />
		</p>

	</div>

	' . get_nonce_field() . '
		</form>
	</div>
</div>

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
