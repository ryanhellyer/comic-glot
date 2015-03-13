<?php

/**
 * Database setup
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Comic_Glot_DB {

	var $dbname = 'wordpress_trunk';
	var $username = 'root';
	var $password = 'root';
	var $hostname = 'localhost'; 

	/**
	 * Class constructor.
	 */
	public function __construct(){

		// Connection to the database
		try {
			$db = new PDO( 'mysql:host=' . $this->hostname . ';dbname=' . $this->dbname, $this->username, $this->password );
		} catch(PDOException $e) {
			$this->error = 'failed-to-connect-to-database';
		}

/*
 * TABLE DATA
 *
 * = Page =
 * ID
 * title
 * 
 *
 */



	// new data
	$title = 'PHP Security';
	$author = 'Jack Hijack';

	// query
	$sql = "INSERT INTO a1 (Name,StreetA) VALUES (:Name,:StreetA)";
	$q = $db->prepare($sql);
	$q->execute(array(':StreetA'=>$author,
	                  ':Name'=>$title));





		// Add database
		try {
			$table = 'a1';
			$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );//Error Handling
			$sql ="CREATE table $table(
			ID INT( 11 ) AUTO_INCREMENT PRIMARY KEY,
			Prename VARCHAR( 50 ) NOT NULL, 
			Name VARCHAR( 250 ) NOT NULL,
			StreetA VARCHAR( 150 ) NOT NULL, 
			StreetB VARCHAR( 150 ) NOT NULL, 
			StreetC VARCHAR( 150 ) NOT NULL, 
			County VARCHAR( 100 ) NOT NULL,
			Postcode VARCHAR( 50 ) NOT NULL,
			Country VARCHAR( 50 ) NOT NULL);" ;
			$db->exec($sql);
			print("Created $table Table.\n");
		} catch(PDOException $e) {
			$this->error = 'table-already-created';
		}




echo 'xxx';
die;
	}

}
new Comic_Glot_DB;