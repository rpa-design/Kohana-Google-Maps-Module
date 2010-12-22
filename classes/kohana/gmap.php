<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Gmap
{
	protected $_config = NULL;
	protected $_options = array(
		'lat' => NULL,
		'lng' => NULL,
		'zoom' => NULL,
		'sensor' => FALSE,
		'maptype' => NULL,
		'view' => NULL,
		'gmap_size_x' => NULL,
		'gmap_size_y' => NULL,
		'gmap_controls' => array(),
	);
	protected $marker = array();
	protected $template = NULL;
	protected static $maptypes = array(
		'road'      => 'google.maps.MapTypeId.ROADMAP',
		'satellite' => 'google.maps.MapTypeId.SATELLITE',
		'hybrid'    => 'google.maps.MapTypeId.HYBRID',
		'terrain'   => 'google.maps.MapTypeId.TERRAIN',
	);
	
	/**
	 * Constructor for the Google-Map class.
	 * 
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{
		$this->_config = Kohana::config('gmap');
		$this->_options = Arr::extract(Arr::merge($this->_options, $options), array_keys($this->_options));
		
		$this->set_pos($this->_options['lat'], $this->_options['lng']);
		$this->set_sensor($this->_options['sensor']);
		$this->set_gmap_size($this->_options['gmap_size_x'], $this->_options['gmap_size_y']);
	} // function
	
	/**
	 * Renders the google-map template.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	} // function
	
	/**
	 * Set a marker somewhere on the map.
	 * 
	 * @param string $id
	 * @param float  $lat
	 * @param float  $lng
	 * @param array  $options
	 * @return Gmap
	 */
	public function add_marker($id, $lat, $lng, array $options = array())
	{
		Gmap::validate_latitude($lat);
		Gmap::validate_longitude($lng);
		
		$available_options = array(
			'title',
			'content',
			'icon',
		);
		
		if (! isset($options['title']) OR empty($options['title']))
		{
			$options['title'] = $id;
		} // if
		
		$this->marker[$id] = array(
			'id' => URL::title($id, '_'),
			'lat' => $lat,
			'lng' => $lng,
			'options' => Arr::extract($options, $available_options),
		);
		
		return $this;
	} // function
	
	/**
	 * Renders the google-map template.
	 * 
	 * @return string
	 */
	public function render($view = '')
	{
		// Set a default map-type.
		if (! isset($this->_options['maptype']))
		{
			$this->_options['maptype'] = Gmap::$maptypes[$this->_config->default_maptype];
		} // if
		
		if (! isset($this->_options['zoom']))
		{
			$this->_options['zoom'] = $this->_config->default_zoom;
		} // if
		
		if (! isset($this->_options['sensor']))
		{
			$this->_options['sensor'] = $this->_config->default_sensor;
		} // if
		
		if (! isset($this->_options['lat']))
		{
			$this->_options['lat'] = $this->_config->default_lat;
		} // if
		
		if (! isset($this->_options['lng']))
		{
			$this->_options['lng'] = $this->_config->default_lng;
		} // if
		
		if (! isset($this->_options['gmap_size_x']))
		{
			$this->_options['gmap_size_x'] = $this->_config->default_gmap_size_x;
		} // if
		
		if (! isset($this->_options['gmap_size_y']))
		{
			$this->_options['gmap_size_y'] = $this->_config->default_gmap_size_y;
		} // if
		
		if (! empty($view))
		{
			$this->_options['view'] = $view;
		}		
		elseif (! isset($this->_options['view']))
		{
			$this->_options['view'] = $this->_config->default_view;
		} // if
		
		$this->template = View::factory($this->_options['view']);
		
		$this->template
			->bind('options', $this->_options)
			->bind('marker', $this->marker);
		
		return $this->template->render();
	} // function
	
	/**
	 * Set another map-type. Possible types are 'road', 'satellite', 'hybrid' and 'terrain'.
	 * 
	 * @param string $maptype
	 */
	public function set_maptype($maptype)
	{
		Gmap::validate_maptype($maptype);
		$this->_options['maptype'] = Gmap::$maptypes[$maptype];
		
		return $this;
	} // function
	
	/**
	 * Set a new position to show, when starting up the map.
	 * 
	 * @param float $lat
	 * @param float $lng
	 * @return Gmap
	 */
	public function set_pos($lat = NULL, $lng = NULL)
	{
		if ($lat != NULL)
		{
			Gmap::validate_latitude($lat);
			$this->_options['lat'] = $lat;
		} // if
		
		if ($lng != NULL)
		{
			Gmap::validate_longitude($lng);
			$this->_options['lng'] = $lng;
		} // if
		
		return $this;
	} // function
	
	/**
	 * Set the sensor-parameter for the google-api.
	 * 
	 * @param boolean $sensor
	 * @return Gmap
	 */
	public function set_sensor($sensor)
	{
		if (! is_bool($sensor))
		{
			throw new Kohana_Exception('The parameter must be boolean. ":sensor" given',
				array(':sensor' => $sensor));
		} // if
		
		$this->_options['sensor'] = $sensor;
		
		return $this;
	} // function

	/**
	 * Set a size for the rendered Google-Map.
	 * You may set a CSS attribute like for example "500px", "50%" or "10em".
	 * If you just set an integer, "px" will be used.
	 * 
	 * @param mixed $x May be a CSS attribute ("500px", "50%", "10em") or an int
	 * @param mixed $y May be a CSS attribute ("500px", "50%", "10em") or an int
	 * @return Gmap
	 */
	public function set_gmap_size($x = NULL, $y = NULL)
	{
		if (is_numeric($x))
		{
			$x = $x . 'px';
		} // if
		
		if (is_numeric($y))
		{
			$y = $y . 'px';
		} // if
		
		if ($x != NULL)
		{
			$this->_options['gmap_size_x'] = $x;
		} // if
		
		if ($y != NULL)
		{
			$this->_options['gmap_size_y'] = $y;
		} // if
		
		return $this;
	} // function
	
	/**
	 * Set the view for displaying the Google-map.
	 * 
	 * @param string $view
	 * @return Gmap
	 */
	public function set_view($view)
	{
		$this->_options['view'] = $view;
		
		return $this;
	} // function
	
	/**
	 * Validate, if the latitude is in bounds.
	 * 
	 * @param float $lat
	 * @return boolean
	 */
	protected static function validate_latitude($lat)
	{
		if ($lat > 180 OR $lat < -180)
		{
			throw new Kohana_Exception('Latitude has to lie between -180.0 and 180.0! Set to :lat',
				array(':lat' => $lat));
		} // if
		
		return TRUE;
	} // function
	
	/**
	 * Validate, if the longitude is in bounds.
	 * 
	 * @param float $lng
	 * @return boolean
	 */
	protected static function validate_longitude($lng)
	{
		if ($lng > 90 OR $lng < -90)
		{
			throw new Kohana_Exception('Longitude has to lie between -90.0 and 90.0! Set to :lng',
				array(':lng' => $lng));
		} // if
		
		return TRUE;
	} // function
	
	/**
	 * Validate, if the given map-type is supported.
	 * 
	 * @param string $maptype
	 */
	protected static function validate_maptype($maptype)
	{
		if (! array_key_exists($maptype, Gmap::$maptypes))
		{
			throw new Kohana_Exception('":maptype" is no supported map-type.',
				array(':maptype' => $maptype));
		} // if
		
		return TRUE;
	} // function
} // class