Comic Jet
=========

Interested comic authors: Dan Aldrin

Beta version:
	Graphical interface
	Facebook login
	Initial tutorial popover on use
	Toolbar
		Next, Previous and Change Language buttons
		Shrinks when unused

Initial version:
	Android app.
	Manual login

Second version:
	Textual pop-over translations
	Offline support
	Mobile gestures
	Gamification
		can't continue unless answer questions
		pay money just to read

Use WHATWG spec for image sizes:
	http://fusion.net/story/121962/performance-is-a-feature-speeding-up-fusion-net/?utm_source=twitter&utm_medium=social&utm_campaign=socialshare&utm_content=desktop+top

From Isaac
	If they stop scrolling for three seconds, pop a message asking if they need help and should click to switch.
	Add fixed toolbar to switch.
		This shrinks when paused and unused.
		Shows next, previous and change language.
	Perhaps use gestures for forwards and backwards
		Need swipe page away, then fade in new page slowly
	When can't use graphical translation, use tooltip
	Gamification
		Can't continue, unless answer questions
			then get next comic
		Pay money to just read.



Maybe this will work instead of Facebook SDK? (from http://stackoverflow.com/questions/7124256/post-to-facebook-wall-without-using-php-sdk)
	1) Auth app in dialog:
	https://www.facebook.com/dialog/oauth?client_id=xxx&scope=publish_stream,offline_access&redirect_uri=http://site.com
	2) Get "forever" access token:
	https://graph.facebook.com/oauth/access_token?client_id=xxx&client_secret=yyy&code=zzz&redirect_uri=http://site.com
	3) Post to wall (MAYBE NEED TO SEND THIS AS POST REQUEST):
	https://graph.facebook.com/me/feed?access_token=aaa&message=msg

	Can use this to confirm obtained access_token is legit:
	https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/v2.3#checktoken


Top Menu
	Sign in (not seen when logged in)
	nothing else?

Footer menu
	Report bug
	Contact
	About/Impressum
	Privacy page
	Sign in (not seen when logged in)
	Social links
		Twitter
		Facebook

functions.php
	esc_html() ... use in signup.php and contact.php


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
		store comics_read cookie


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

