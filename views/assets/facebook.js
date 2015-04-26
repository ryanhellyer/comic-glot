window.fbAsyncInit = function() {
	FB.init({
		appId   : facebook_app_id,
		oauth   : true,
		status  : true, // check login status
		cookie  : true, // enable cookies to allow the server to access the session
		xfbml   : true // parse XFBML
	});

};

function fb_login(){
	FB.login(function(response) {

		if (response.authResponse) {
			access_token = response.authResponse.accessToken;
			user_id = response.authResponse.userID;

			FB.api("/me", function(response) {
				user_email = response.email;
			});

			// Reload page so that we can display a logged in message
			window.location.reload();

		} else {
			// User cancelled or did not fully authorize
		}
	}, {
		scope: "publish_stream,email"
	});
}