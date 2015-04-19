
/*
 * Loop through all comics read and set CSS of their box.
 */
comics_read_json = getCookie("comics_read");
if ("" != comics_read_json) {
	var comics_read = JSON.parse(comics_read_json);

	for(var comic_slug in comics_read) {
		var page_number = comics_read[comic_slug];

		if ( "end" == page_number ) {

			// Style already read comics
			var comic_block = document.getElementById("comic-"+comic_slug).innerHTML;
			document.getElementById("comic-"+comic_slug).innerHTML = comic_block + "<div class=\'read\'>" + text_already_read + "</div>";
			document.getElementById("comic-"+comic_slug).style.opacity = "0.8";

		} else {

			// Style comics which are bein read
			var comic_block = document.getElementById("comic-"+comic_slug).innerHTML;
			document.getElementById("comic-"+comic_slug).innerHTML = comic_block + "<div class=\'read\'>" + text_reading + "</div>";

			// Change URL
			var parent = document.getElementById("comic-"+comic_slug);
			var child = parent.childNodes[1];

			var new_url = (
				comicjet_root_url +
				text_comic_slug +
				"/" +
				comic_slug +
				"/" +
				page_number +
				"/" +
				comicjet_language1 +
				"/" +
				comicjet_language2 +
				"/"
			);
			child.setAttribute("href", new_url);
		}

	}

}
