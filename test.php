<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<title>Comic Glot</title>

	<link rel="stylesheet" href="views/assets/style.css" type="text/css" media="all" />
	<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.1/themes/base/jquery-ui.css"/>

	<script type="text/javascript" src="views/assets/jquery.js"></script>
	<script type="text/javascript" src="views/assets/jquery-ui.js"></script>
	<script type="text/javascript" src="views/assets/sortable.min.js"></script>

</head>
<body>

<form method="post" action="" enctype="multipart/form-data">

	<?php

	if ( ! empty( $_POST ) ) {
		echo '<textarea style="width:900px;height:200px;border:1px solid #eee;background:#fafafa;padding:20px;margin:20px 0;">';
		print_r( $_POST );
		echo '</textarea>';
	}

	?>

	<img src="strips/en.jpg" />

	<div class="controls">

		<ul class="sortable">
			<li>
				<h3>Strip 2</h3>

				<h4>English</h4>
				<p>
					<input type="file" name="" value="bla" />
				</p>

				<h4>Deutsch</h4>
				<p>
					<input type="file" name="" />
				</p>
			</li>
			<li>
				<h3>Strip 1</h3>

				<h4>English</h4>
				<p>
					<input type="file" name="" value="bla" />
				</p>

				<h4>Deutsch</h4>
				<p>
					<input type="file" name="" />
				</p>
			</li>
		</ul>

		<p>
			<input type="submit" name="add-new-strip" id="add-new-strip" class="button" value="Add new strip" />
		</p>

		<h3>Select languages to use</h3>
		<p>
			<label>English</label>
			<input type="checkbox" name="language[en]" value="1" />
		</p>
		<p>
			<label>Deutsch</label>
			<input type="checkbox" name="language[de]" value="1" />
		</p>

		<p class="submit">
			<input type="submit" name="save" class="button" value="Save Changes" />
		</p>

	</div>
</form>

<script>
jQuery(function($){ 

	// Allow for resorting rows
	$('.sortable').sortable({
		axis: "y", // Limit to only moving on the Y-axis
	});

});


</script>


</body>
</html>