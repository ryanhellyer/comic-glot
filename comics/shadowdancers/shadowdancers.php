<?php

$bubbles = array(
	'title' => array(
		'language_strings' => array(
			'en' => 'Shadow Dancers',
			'de' => 'Translate Shadow Dancers',
		),
	),
	0 => array(
		0 => array(
			'top'  => 40,
			'left' => 0,
			'width' => 90,
			'height' => 30,
			'language_strings' => array(
				'en' => 'Shadow Dancers',
				'de' => 'Schatten tänzer',
			),
		),
	),
	1 => array(
		0 => array(
			'top'  => 0,
			'left' => 0,
			'width' => 50,
			'height' => 15,
			'language_strings' => array(
				'en' => 'Hospital of Aricona',
				'de' => 'Krankenhaus von Aracona',
			),
		),
		1 => array(
			'top'  => 33,
			'left' => 0,
			'width' => 25,
			'height' => 20,
			'language_strings' => array(
				'en' => 'Ow, my head.',
				'de' => 'Au, mein Kopf.',
			),
		),
		2 => array(
			'top'  => 33,
			'left' => 26,
			'width' => 33,
			'height' => 20,
			'language_strings' => array(
				'en' => 'Ilias is awake! Ilias is awake!',
				'de' => 'Ilias ist aufgewacht! Ilias ist aufgewacht!',
			),
		),
		3 => array(
			'top'  => 69,
			'left' => 0,
			'width' => 33,
			'height' => 13,
			'note' => __( 'This is name and does not need translated!' ),
		),
		4 => array(
			'top'  => 83,
			'left' => 15,
			'width' => 40,
			'height' => 14,
			'language_strings' => array(
				'en' => 'Mama.',
				'de' => 'Mom.',
			),
		),
		5 => array(
			'top'  => 69,
			'left' => 60,
			'width' => 40,
			'height' => 17,
			'language_strings' => array(
				'en' => 'I heard Lilith\'s voice. Is she here too?',
				'de' => 'Ich habe Lilith\'s stimme gehört. Ist sie auch hier?',
			),
		),
	),
);
$json = json_encode( $bubbles );
file_put_contents( $txt_file, $json );
