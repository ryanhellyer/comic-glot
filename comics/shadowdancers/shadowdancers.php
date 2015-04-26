<?php

$bubbles = array(
	'title' => array(
		'language_strings' => array(
			'en' => 'Shadow Dancers',
			'de' => 'Translate Shadow Dancers',
		),
	),
	1 => array(
		0 => array(
			'top'  => 0,
			'left' => 0,
			'width' => 40,
			'height' => 5,
			'language_strings' => array(
				'en' => 'Hospital of Aricona',
				'de' => 'Krankenhaus von Aracona',
			),
		),
		1 => array(
			'top'  => 20,
			'left' => 30,
			'width' => 20,
			'height' => 30,
			'language_strings' => array(
				'en' => 'XXXHospital of Aricona',
				'de' => 'AAA Krankenhaus von Aracona',
			),
		),
	),
);
echo json_encode( $bubbles );
