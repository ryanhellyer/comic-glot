jQuery(document).ready(function($) {

//	$( ".clickme" ).click(function() {
	$( ".file-upload" ).click(function() {
		$(this).next('.box-with-content').text('abc');
		alert('pooper');
	});

});