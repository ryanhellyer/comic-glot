document.onkeydown = comicjet_keypress;

function comicjet_keypress(e) {

	e = e || window.event;


	if (typeof comicjet_prev_url != "undefined") {
		if (e.keyCode == "37") {
			window.location.assign(comicjet_prev_url);
		}
	}

	if (typeof comicjet_next_url != "undefined") {
		if (e.keyCode == "39") {
			window.location.assign(comicjet_next_url);
		}
	}

}