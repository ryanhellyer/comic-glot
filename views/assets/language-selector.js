// Switching off form submission
document.getElementById("select-language").type = "button";
// Redirecting after language selector clicked (required for offline use, when POST will not work)
document.getElementById("select-language").onclick = function(){
	var language1 = document.getElementById("language1").value;
	var language2 = document.getElementById("language2").value;
	var new_url = comicjet_root_url+language1+"/"+language2+"/";
	window.location.assign(new_url);
};
