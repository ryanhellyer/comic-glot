<?php

define( 'COMIC_OPTION', 'example-option' );
define( 'COMIC_GROUP', 'example-group' );


/**
 * Get a single speech bubble field.
 * 
 * @param  string  $value  Option value
 * @return string  The table row HTML
 */
function comic_get_bubble_field( $value = '' ) {

	// Set values if they don't exist
	$options = array(
		'title',
		'width',
		'height',
		'x-position',
		'y-position',
		'font-family',
		'font-size',
		'text-colour',
	);
	foreach( $options as $option ) {
		if ( ! isset( $value[$option] ) ) {
			$value[$option] = '';
		}

	}

	$bubble_html = '
	<div class="bubble">
		<p>
			<label>' . __( 'Text', 'plugin-slug' ) . '</label>
			<input type="text" name="' . COMIC_OPTION . '[][title]" value="' . esc_attr( $value['title'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Width', 'plugin-slug' ) . '</label>
			<div class="slider"></div>
			<input class="slider-input" type="text" name="' . COMIC_OPTION . '[][width]" value="' . esc_attr( $value['width'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Height', 'plugin-slug' ) . '</label>
			<div class="slider"></div>
			<input class="slider-input" type="text" name="' . COMIC_OPTION . '[][height]" value="' . esc_attr( $value['height'] ) . '" />
		</p>
		<p>
			<label>' . __( 'X position', 'plugin-slug' ) . '</label>
			<div class="slider"></div>
			<input class="slider-input" type="text" name="' . COMIC_OPTION . '[][x-position]" value="' . esc_attr( $value['x-position'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Y position', 'plugin-slug' ) . '</label>
			<div class="slider"></div>
			<input class="slider-input" type="text" name="' . COMIC_OPTION . '[][y-position]" value="' . esc_attr( $value['y-position'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Font family', 'plugin-slug' ) . '</label>
			<select name="' . COMIC_OPTION . '[][font-family]">
				<option value="sans-serif">Sans serif</option>
				<option value="serif">Serif</option>
			</select>
		</p>
		<p>
			<label>' . __( 'Font-size', 'plugin-slug' ) . '</label>
			<div class="slider"></div>
			<input class="slider-input" type="text" name="' . COMIC_OPTION . '[][font-size]" value="' . esc_attr( $value['font-size'] ) . '" />
		</p>
		<p>
			<label>' . __( 'Text color', 'plugin-slug' ) . '</label>
			<input class="text-colour" type="text" name="' . COMIC_OPTION . '[][text-colour]" value="' . esc_attr( $value['text-colour'] ) . '" />
		</p>
	</div>';


	// Strip out white space (need on line line to keep JS happy)
	$bubble_html = str_replace( '	', '', $bubble_html );
	$bubble_html = str_replace( "\n", '', $bubble_html );

	// Return the final HTML
	return $bubble_html;
}

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
					<div class="file-upload-wrapper">
						<div class="file-upload-inner">
							<input class="file-upload" type="file" name="' . COMIC_OPTION . '[][file]" />
						</div>
					</div>

					<div class="box-with-content"></div>

					<h3>English</h3>
					<span class="button add-new-bubble">' . __( 'Add new speech bubble', 'plugin-slug' ) . '</span>

					<h3>Deutsch</h3>
					<span class="button add-new-bubble">' . __( 'Add new speech bubble', 'plugin-slug' ) . '</span>

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
	<link rel='stylesheet' id='open-sans-css'  href='//fonts.googleapis.com/css?family=Open+Sans%3A300italic%2C400italic%2C600italic%2C300%2C400%2C600&#038;subset=latin%2Clatin-ext&#038;ver=4.2-alpha-31205' type='text/css' media='all' />
	<link rel="stylesheet" href="<?php echo COMIC_ASSETS_URL . 'style.css'; ?>" type="text/css" media="all" />

	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery-ui.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'file-upload.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'jquery.ajaxfileupload.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo COMIC_ASSETS_URL . 'sortable.min.js'; ?>"></script>


<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css"/>

</head>
<body>


<!--[if lt IE 10]>
Sorry, but you are need a modern browser to use this website.
<![endif]-->

<div style="display:none;">
	<div class="new">NEW</div>
	<div class="container">
	</div>

	<hr />
	<div class="new">NEW</div>
	<div class="container">
	</div>
</div>

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
.button {
	display: inline-block;
	border-radius: 10px;
	border: 1px solid #999;
	padding: 4px 8px;
	background: #f4f4f4;
}
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
	var row_html = '<?php echo comic_get_row( '' ); ?>';

	// Add the buttons
	add_buttons();

	// Add a fresh row on clicking the add row button
	$( "#add-new-row" ).click(function() {
		$( ".sortable" ).append( row_html ); // Add the new row
		add_buttons(); // Add buttons tot he new row
	});

	// Allow for resorting rows
	$('.sortable').sortable({
		axis: "y", // Limit to only moving on the Y-axis
	});



	// ***** SPEECH BUBBLE FUNCTIONALITY _ ADD NEW ONE HERE ****
	var bubble_html = '<?php echo comic_get_bubble_field( '' ); ?>';

	$( ".add-new-bubble" ).before( bubble_html ); // Add the new row

	// Add a fresh speech bubble on clicking the add speech bubble button
	$( ".add-new-bubble" ).click(function() {
		$(this).before( bubble_html ); // Add the new row
	});

	$( ".slider" ).slider(
		{
			slide: function( event, ui ) {
				$(this).next('.slider-input').val( ui.value );
			}
		}
	);

});

</script>

<style>
.demo {
	width: 150px;
	height: 150px;
	padding: 10px 0 0 0;
	background-color: rgba(0,0,0,0.1);
	position: absolute;
	top: 150px;
	left: 300px;
}
.demo textarea {
	min-width: 5px;
	min-height: 5px;
}
.container {
	background: #fafafa;
	border:1px solid #eee;
	width: 800px;
	height: 300px;
}
</style>

<script>
jQuery(document).ready(function($) {
	$('.new').click(function() {
		$(this).next('.container').append( '<div contenteditable class="demo">'+Math.floor((Math.random() * 10) + 1)+'</div>' );

		$('.demo').draggable({
//			cancel: '',
			containment: '.container',
			scroll: false
		}).resizable();
	});


});
</script>

</body>
</html>