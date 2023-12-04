<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://estudiorochayasoc.com.ar
 * @since      1.0.0
 *
 * @package    Nexa_Erp_Integration
 * @subpackage Nexa_Erp_Integration/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Nexa_Erp_Integration
 * @subpackage Nexa_Erp_Integration/includes
 * @author     Estudio Rocha & Asociados <facundo@estudiorochayasoc.com.ar>
 */
class Nexa_Erp_Integration_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'nexa-erp-integration',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
