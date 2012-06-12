<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	// Default map-center
	'default_lat' => 54.838664,
	'default_lng' => -4.086914,

	// Default zoom-level
	'default_zoom' => 5,

	// Default "sensor" setting - Used for mobile devices
	'default_sensor' => FALSE,

	// Default map-type
	'default_maptype' => 'road',

	// The instance will be set in the render method to a random string (if this value is empty)
	'instance' => '',

	// Default view-options
	'default_view' => 'gmap',
	'default_gmap_size_x' => '100%',
	'default_gmap_size_y' => '100%',

	// Default Google Maps controls
	'default_gmap_controls' => array(
		'maptype' => array(
			'display' => TRUE,
			'style' => 'default',
			'position' => NULL,
		),
		'navigation' => array(
			'display' => TRUE,
			'style' => 'default',
			'position' => NULL,
		),
		'scale' => array(
			'display' => TRUE,
			'position' => NULL,
		),
	),
	
	// Default options for polylines
	'default_polyline_options' => array(
		'strokeColor' => '#000',
		'strokeOpacity' => 1,
		'strokeWeight' => 3,
	),
	
	// Default options for polygons
	'default_polygon_options' => array(
		'strokeColor' => '#000',
		'strokeOpacity' => 1,
		'strokeWeight' => 3,
		'fillColor' => '#000',
		'fillOpacity' >= .5,
	),
);