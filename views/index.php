<?php

define( 'COMIC_OPTION', 'example-option' );
define( 'COMIC_GROUP', 'example-group' );

/**
 * Get a single table row.
 * 
 * @param  string  $value  Option value
 * @return string  The table row HTML
 */
function comic_get_row( $value = '' ) {

	if ( ! is_array( $value ) ) {
		$value = array();
	}

	if ( ! isset( $value['title'] ) ) {
		$value['title'] = '';
	}

	if ( ! isset( $value['file'] ) ) {
		$value['file'] = '';
	}

	// Create the required HTML
	$row_html = '

				<li>

						<label>' . __( 'Enter your input string.', 'plugin-slug' ) . '</label>

						<input type="text" name="' . COMIC_OPTION . '[][title]" value="' . esc_attr( $value['title'] ) . '" />
						<span class="read-more-text"><br />some text goes here</span>

						<input class="file-upload" type="file" name="' . COMIC_OPTION . '[][file]" />
						<div class="box-with-content">...</div>
				</li>';

	// Strip out white space (need on line line to keep JS happy)
	$row_html = str_replace( '	', '', $row_html );
	$row_html = str_replace( "\n", '', $row_html );

	// Return the final HTML
	return $row_html;
}


?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>
	<link rel="stylesheet" href="<?php echo COMIC_ASSETS_URL . 'style.css'; ?>" type="text/css" media="all" />

	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery-ui.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'file-upload.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery.ajaxfileupload.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'sortable.min.js'; ?>"></script>

</head>
<body>

<!--[if lt IE 10]>
Sorry, but you are need a modern browser to use this website.
<![endif]-->

<form method="post" action="" enctype="multipart/form-data">

	<ul class="sortable"><?php

			// Grab options array and output a new row for each setting
			$options = get_option( COMIC_OPTION );
			if ( is_array( $options ) ) {
				foreach( $options as $key => $value ) {
					echo comic_get_row( $value );
				}
			}

			// Add a new row by default
			echo comic_get_row();
		?>

	</ul>

	<input type="button" id="add-new-row" value="<?php _e( 'Add new row', 'plugin-slug' ); ?>" />

	<?php //settings_fields( COMIC_GROUP ); ?>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'plugin-slug' ); ?>" />
	</p>
</form>

<style>
.read-more-text {
	display: none;
}
.sortable li .toggle {
	display: inline !important;
}
</style>

<script type='text/javascript'>
/* <![CDATA[ */
var test_url_submit = "<?php echo str_replace( '/', "\/", home_url( '/' ) ); ?>?ajax_file_upload=true";
/* ]]> */
</script>

<script>

jQuery(function($){ 

	// Adding some buttons
	function add_buttons() {

		// Loop through each row
		$( ".sortable li" ).each(function() {

			// If no input field found with class .remove-setting, then add buttons to the row
			if(!$(this).find('input').hasClass('remove-setting')) {

				// Add a remove button
				$(this).append('<td><input type="button" class="remove-setting" value="X" /></td>');

				// Add read more button
				$(this).append('<td><input type="button" class="read-more" value="More" /></td>');

				// Remove button functionality
				$('.remove-setting').click(function () {
					$(this).parent().parent().remove();
				});

				// Read more button functionality
				$('.read-more-text').css('display','none');
				$(this).find(".read-more").click(function(){
					$(this).parent().parent().find('.read-more-text').toggleClass('toggle');
				});

			}

		});

	}

	// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
	var html = '<?php echo comic_get_row( '' ); ?>';

	// Add the buttons
	add_buttons();

	// Add a fresh row on clicking the add row button
	$( "#add-new-row" ).click(function() {
		$( ".sortable" ).append( html ); // Add the new row
		add_buttons(); // Add buttons tot he new row
	});

	// Allow for resorting rows
	$('.sortable').sortable({
		axis: "y", // Limit to only moving on the Y-axis
	});

});

</script>


</body>
</html>