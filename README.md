Comic Jet
=========

Top Menu
	Signup

Privacy page


URLs
	301 to pages with trailing slash (pages without trailing slashes still work)


Login
	login cookie
		store email, first name, last name
	Redis
		login cookie
		comics_read cookie

	Redis
		store login cookie
6		store comics_read cookie


Things to store in DB
	Per user
		page number read to in comic (or "complete")
		login info.
		star rating for comic
		Time spent on each page (can calc time on whole comic from this)
		Time spent on own language per page (calc for whole comic from this)
		How many times each comic is read
			Maybe store time per page for each times comic is read
	General info
		Track device types, screen widths
		Track how many times each comic is visited
		Track how many times each comic is completed
		Track star ratings (separate from users own rating)
		



TODO:
	0. en/de/ should show German by default, since that's what you want to learn.
	1. Add tutorial for new users. When cookie set, don't show.
	2. Login system
		* http://adodson.com/hello.js/#hellojs???
		* Log email address, username (if available), name and time of signing up to file. Use new file each month.
	3. Add link between next and prev buttons for switching between languages

Instead of charging, give free comics to those who tweet or FB about the last comic they read. Each comic = 1 tweet/FB message.

When logged in, should store exact last page user was on. Then when they revisit that comic, it should return to that page. Store in Redis with key of "slug-pagenumber", then the value of the page number they were on.

Encourage translators, but giving prominent credit and link back. Could show picture of them with info. before or after reading comic.


Comic GLot

Suitable comic to port into it:
Available in German ... http://mimiandeunice.com/about/
http://www.sandraandwoo.com/gaiade/2014/12/19/alle-schranken-fallen-030/
https://web.law.duke.edu/cspd/comics/digital.php - multiple languages
http://comicbookplus.com/?dlid=57775 - Public domain coz old? Saw some comment as such on the site.
http://www.itchyfeetcomic.com/ - may not allow for translations - ask



Ideas:
1. My assignment for students to translate, then have language school review. School gets free access in return.
2. After each page, users select from five potential plot lines to check if they understood. Rated accordingly.
3. Email signup. Newsletter. Powered via blog.
4. blog.comicjet.com
	Weekly list of new comics and news.
	Email signup.

