comic-glot
==========


Maybe use imagemap for comics.


Comic GLot

Suitable comic to port into it:
http://www.sandraandwoo.com/gaiade/2014/12/19/alle-schranken-fallen-030/
https://web.law.duke.edu/cspd/comics/digital.php - multiple languages
http://comicbookplus.com/?dlid=57775 - Public domain coz old? Saw some comment as such on the site.


Potential names:
polyhumorous

Ideas:
1. My assignment for students to translate, then have language school review. School gets free access in return.
2. After each page, users select from five potential plot lines to check if they understood. Rated accordingly.
3. Email signup. Newsletter.

Logo ideas:
http://content.sportslogos.net/logos/17/300/full/1606.gif
http://image.shutterstock.com/display_pic_with_logo/88446/88446,1200255590,10/stock-vector-jet-fighter-vector-illustration-8515000.jpg
https://dribbble.com/shots/1695680-Jumbo-Jet-Sketch



SQL
	comics - table
		1	Asterix			The Gaul		Descripton		Content blob	Goscinny and Uderzo
		2	Asterix			And Cleopatra	Descripton		Content blob	Goscinny and Uderzo
		3	Donald Duck		Does Dallas		Descripton		Content blob	Goscinny and Uderzo

Disk
	users - crude temporary hack
		ryan:hash
		bob:hash
		ronald:hash

Form
	HTML only
	Add JS later

PHP
	nonce function - time based
	login - crude check from disk database
	File uploads - check extension and upload
	Caching functions - can be empty initially

Frontend
	One big image (set as background)
	Only view one section at a time
	Buttons used to move image to next location

Backend
	One big image for each language
	Set viewing points

Future features
	Star ratings for comics
		In memory store, but backed up to SQL via Cron

