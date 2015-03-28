<?php

echo '

<form method="post" action="" enctype="multipart/form-data">

	<!-- Hidden submit button - ensures that page is saved when enter hit -->
	<input type="submit" style="display:none" name="save" class="button" value="' . __( 'Save Changes' ) . '" />

	<div class="image-display">';

	if ( isset( $this->current_page['current_background'] ) ) {
		echo '
		<img src="' . esc_attr( $this->current_page['current_background'] ) . '" />';
	}

		echo '
		<img src="' . esc_attr( $this->current_page['current_image'] ) . '" />
	</div>

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
		<p>
			<label>' . __( 'Title' ) . '</label>
			<input type="text" name="title" value="' . esc_attr( $this->current_page['title'] ) . '" />
		</p>';


		// Loop through langs
		foreach( $this->available_languages as $lang => $language ) {

			// Bail out if language is not used
			if ( array_key_exists( $lang, $this->current_page['used_languages'] ) ) {
				// Thumbnail input
				echo '
				<p>
					<label>Thumbnail image - ' . $lang . '</label>
					<input type="file" name="' . esc_attr( 'file-upload[thumbnail][' . $lang . ']' ) . '" value="" />
				</p>';

				// Add existing thumbnail
				$thumbnail = $this->db->get( 'thumbnail', $this->current_page['slug'] );
				if ( isset( $thumbnail[$lang] ) ) {
					$file_name = $thumbnail[$lang];
					echo '
				<input type="text" style="font-size:10px;color:#aaa;border:1px solid #ddd" name="' . esc_attr( 'thumbnail[' . $lang . ']' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}

			}
		}


		if ( isset( $this->current_page['strips'] ) && is_array( $this->current_page['strips'] ) ) {
			echo '
		<ul class="sortable">';

			foreach( $this->current_page['strips'] as $key => $strip ) {

				echo '
			<li>
				<input class="button alignright" type="submit" name="' . esc_attr( 'remove-page[' . $key . ']' ) . '" value="' . __( 'Remove' ) . '" />
				<h3>' . sprintf( __( 'Page %s' ), $key + 1 ) . '</h3>';

				// Add current_background image
				echo '
				<p>
					<label>Background image</label>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][current_background]' ) . '" value="" />
				</p>';
				if ( isset( $this->current_page['strips'][$key]['current_background'] ) ) {
					$file_name = $this->current_page['strips'][$key]['current_background'];
					echo '
				<input type="text" style="font-size:10px;color:#aaa;border:1px solid #ddd" name="' . esc_attr( 'strip_image[' . $key . '][current_background]' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}

				// Low resolution background image
				echo '
				<p>
					<label>Low resolution background image (used for offline mode)</label>
					<input type="file" name="' . esc_attr( 'file-upload[' . $key . '][current_background_lowres]' ) . '" value="" />
				</p>';
				if ( isset( $this->current_page['strips'][$key]['current_background_lowres'] ) ) {
					$file_name = $this->current_page['strips'][$key]['current_background_lowres'];
					echo '
				<input type="text" style="font-size:10px;color:#aaa;border:1px solid #ddd" name="' . esc_attr( 'strip_image[' . $key . '][current_background_lowres]' ) . '" value="' . esc_attr( $file_name ) . '" />';
				}


				echo '
				<p>' . __( 'Add window coordinates' ) . '</p>';

				$strip['window'][] = ''; // Add new window
				foreach( $strip['window'] as $window_id => $window_value ) {
					echo '
				<p>
					<input type="text" name="' . esc_attr( 'strip_image[' . $key . '][window][]' ) . '" value="' . esc_attr( $window_value ) . '" />
				</p>';
				}


				foreach( $this->available_languages as $lang => $language ) {

					// Bail out if language is not used
					if ( array_key_exists( $lang, $this->current_page['used_languages'] ) ) {

						echo '

				<h4>' . $language['name'] . '</h4>
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

				}

				echo '
			</li>';
			}

			echo '
		</ul>';
		}

		echo '

		<p>
			<input type="submit" name="add-new-page" id="add-new-page" class="button" value="' . __( 'Add new page' ) . '" />
		</p>

		<h3>' . __( 'Select languages to use' ) . '</h3>';

		foreach( $this->available_languages as $lang => $language ) {

			// Set whether selected or not
			if ( is_array( $this->current_page['used_languages'] ) && array_key_exists( $lang ,$this->current_page['used_languages'] ) ) {
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
