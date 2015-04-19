 window.fbAsyncInit = function() {
 FB.init({
 appId : facebook_app_id,
 status : true,
 cookie : true,
 xfbml : true,
 oauth : true,
 });
 
FB.Event.subscribe('auth.login', function(response) {
 // ------------------------------------------------------
 // This is the callback if everything is ok
 window.location.reload();
 });
 };
 
(function(d){
 var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
 js = d.createElement('script'); js.id = id; js.async = true;
 js.src = "//connect.facebook.net/en_US/all.js";
 d.getElementsByTagName('head')[0].appendChild(js);
 }(document));
