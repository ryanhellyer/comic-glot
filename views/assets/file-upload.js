jQuery(document).ready(function($) {

	$(".file-upload").ajaxfileupload({
		'action': test_url_submit,
		'onComplete': function(response) {
			var json_obj = $.parseJSON(response);
			console.log(json_obj);

			var url = json_obj['url'];

			$(this).parent().parent().next('.box-with-content').html('<img src="'+url+'" />');
			$(this).parent().parent().css('display','none'); // Hide the uploader once image in place
		},

	});

});
