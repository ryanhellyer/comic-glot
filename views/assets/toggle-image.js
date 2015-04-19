function toggle_image() {
	var img = document.getElementById("bubble").src;
	if (img.indexOf(bubble_image_1)!=-1) {
		document.getElementById("bubble").src  = bubble_image_0;
		document.getElementsByClassName("current-language")[0].innerHTML = current_language2;
		document.getElementsByClassName("current-language")[1].innerHTML = current_language2;
	} else {
		document.getElementById("bubble").src = bubble_image_1;
		document.getElementsByClassName("current-language")[0].innerHTML = current_language1;
		document.getElementsByClassName("current-language")[1].innerHTML = current_language1;
	}

}


/* Setting cookies */
var comics_read_json = getCookie("comics_read");

if ("" == comics_read_json) {
	var comics_read = {};
} else {
	var comics_read = JSON.parse(comics_read_json);
}


if ( "end" != comics_read[page_slug] ) {
	comics_read[comicjet_slug] = next_comic_read;
	var comics_read_newjson = JSON.stringify(comics_read);
	setCookie("comics_read", comics_read_newjson, 1);
}
