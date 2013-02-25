<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD . 'locateee/config.php';

/**
 * Locatee Fieldtype Class for EE2
 *
 * @package LocatEE
 * @author Chris Lock
 * @copyright Copyright (c) 2012 Paramore
 */
class Locateee_ft extends EE_Fieldtype {

	/**
	 * Info that is given to ExpressionEngine
	 * @var array
	 */
	public $info = array(
		'name' => LOCATEEE_LIST_NAME,
		'version' => LOCATEEE_LIST_VER
	);

	/**
	 * Tells EE that our replace_tag handles tag pairs
	 * @var boolean
	 */
	public $has_array_data = true;

	/**
	 * Reference to the EE superglobal
	 * @var object
	 */
	public $EE;

	/**
	 * The current field's ID
	 * @var int
	 */
	public $field_id;

	/**
	 * The current field's short_name
	 * @var string
	 */
	public $field_name;

	/**
	 * An array of field settings from EE
	 * @var array
	 */
	public $settings = array();

	/**
	 * The URL for the current Google Maps API
	 */
	const GOOGLE_MAPS_API_SRC = 'http://maps.google.com/maps/api/js?sensor=false';

	function Locateee_ft()
	{
		parent::EE_Fieldtype();

		$this->EE->lang->loadfile('locateee');

		// Prepare Cache
		if (! isset($this->EE->session->cache['locateee']))
			$this->EE->session->cache['locateee'] = array('includes' => array());
		
		$this->cache =& $this->EE->session->cache['locateee'];
	}
	
	/**
	 * Publish page input field
	 * @param string $data Data returned by EE
	 * @return string Markup for the input field
	 */
	function display_field($data)
	{
		$this->include_theme_css('styles/locateee.css');
		$this->include_external_js(self::GOOGLE_MAPS_API_SRC);
		$this->include_theme_js('scripts/locateee.js');

		return $this->build_field_table(
			$this->get_field_columns(
				$this->process_data($data)
			)
		);
	}

	/**
	 * Add a theme css file and include it in the cache
	 * @author Brandon Kelly, brandon@pixelandtonic.com
	 * @param string $file Name of css file with no path 
	 * @return void
	 */
	private function include_theme_css($file)
	{
		if (in_array($file, $this->cache['includes']))
			return;

		$this->cache['includes'][] = $file;
		$this->EE->cp->add_to_head(
			'<link rel="stylesheet" type="text/css" href="' . $this->theme_url() . $file . '" />'
		);
	}

	/**
	 * Returns the theme url and adds in to the cache
	 * @author Brandon Kelly, brandon@pixelandtonic.com
	 * @return string Path to the themes folder
	 */
	private function theme_url()
	{
		if (! isset($this->cache['theme_url'])) {
			$theme_folder_url = $this->EE->config->item('theme_folder_url');

			if (substr($theme_folder_url, -1) != '/')
				$theme_folder_url .= '/';

			$this->cache['theme_url'] = $theme_folder_url . 'third_party/locateee/';
		}

		return $this->cache['theme_url'];
	}

	/**
	 * Add a theme js file and include it in the cache
	 * @author Brandon Kelly, brandon@pixelandtonic.com
	 * @param string $file Name of js file with no path 
	 * @return void
	 */
	private function include_theme_js($file)
	{
		if (in_array($file, $this->cache['includes']))
			return;
		
		$this->cache['includes'][] = $file;
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="' . $this->theme_url() . $file . '"></script>');
	}

	/**
	 * Add an external js file 
	 * @param string $file Name of js file with path 
	 * @return void
	 */
	private function include_external_js($file)
	{
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="' . $file . '"></script>');
	}

	/**
	 * Add an external js file 
	 * @param string $data Data returned by EE
	 * @return string Markup for inputs in the input field
	 */
	private function get_field_columns($data)
	{
		$columns = array();
		$columns['street'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('street'),
				'street',
				true
			),
			'heading' => lang('street'),
			'is_required' => true,
			'width' => '35%'
		);
		$columns['city'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('city'),
				'city',
				true
			),
			'heading' => lang('city'),
			'is_required' => true,
			'width' => '16%'
		);
		$columns['state'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('state'),
				'state',
				true
			),
			'heading' => lang('state'),
			'is_required' => true,
			'width' => '7%'
		);
		$columns['zip'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('zip'),
				'zip',
				true
			),
			'heading' => lang('zip'),
			'is_required' => true,
			'width' => '10%'
		);
		$columns['location'] = array(
			'field' => $this->build_location_button(),
			'heading' => lang('location'),
			'is_button' => true,
			'width' => '10%'
		);
		$columns['lat'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('lat'),
				'lat'
			),
			'heading' => lang('lat'),
			'width' => '11%'
		);
		$columns['lng'] = array(
			'field' => $this->build_field_input(
				$data,
				lang('lng'),
				'lng'
			),
			'heading' => lang('lng'),
			'width' => '11%'
		);

		return $columns;
	}

	/**
	 * Builds input for field
	 * @param string $data Data returned by EE
	 * @param string $data Label for column
	 * @param string $name Name used for input
	 * @param string $is_required If the field is required
	 * @return string Markup of input
	 */
	private function build_field_input($data, $label, $name, $is_required = false)
	{
		$value = (isset($data[$name]))
			? $data[$name] 
			: null;

		return $this->EE->load->view(
			'form_fields/default',
			array(
				'field_name' => $this->field_name,
				'is_required' => $is_required,
				'label' => $label,
				'name' => $name,
				'value' => $value
			),
			true
		);
	}

	/**
	 * Builds location button
	 * @return string Markup for location button
	 */
	private function build_location_button()
	{
		return $this->EE->load->view(
			'form_fields/location_button',
			array(
				'button_text' => lang('location_button')
			),
			true
		);
	}

	/**
	 * Replaces html quotes and unserializes the string saved in EE
	 * @param string $data String returned from EE
	 * @return mixed empty If no data saved | array Address fields
	 */
	private function process_data($data)
	{
		if (empty($data))
			return $data;

		$data = str_replace('&quot;', '"', $data);
		$data = @unserialize($data);
		
		return $data;
	}

	/**
	 * Builds table for input field
	 * @return string Markup of entire table
	 */
	private function build_field_table($columns)
	{
		return $this->EE->load->view(
			'publish_table',
			array(
				'columns' => $columns,
				'error_data_later' => lang('error_message_later'),
				'error_data_address' => lang('error_message_address'),
				'field_id' => $this->field_id
			),
			true
		);
	}

	/**
	 * Called by EE to save the field value
	 * @param  array $data The raw form data
	 * @return string What to store in the EE table
	 */
	function save($data)
	{
		return serialize($data);
	}

	/**
	 * Called by EE to replace the contents of a template tag with the price
	 * @param  string $data String returned from EE
	 * @param  array $params
	 *         string var_prefix Prefix used before template tag
	 * @param  boolean $tagdata
	 * @return string Tag pair if not empty
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		$data = $this->process_data($data);
		
		if (! is_array($data) OR empty($data))
			return false;
		
		$var_prefix = isset($params['var_prefix'])
			? rtrim($params['var_prefix'], ':') . ':' 
			: null;

		$return_data = array();
		$has_results = false;

		foreach($data as $key => $value) {
			$return_data[$var_prefix . $key] = $value;

			if (! empty($value))
				$has_results = true;
		}

		return ($has_results)
			? $this->EE->TMPL->parse_variables($tagdata, array($return_data))
			: false;
	}
}

/* End of file ft.locateee.php */
/* Location: ./system/expressionengine/third_party/locateee/ft.locateee.php */