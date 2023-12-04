<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://estudiorochayasoc.com.ar
 * @since      1.0.0
 *
 * @package    Nexa_Erp_Integration
 * @subpackage Nexa_Erp_Integration/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Nexa_Erp_Integration
 * @subpackage Nexa_Erp_Integration/admin
 * @author     Estudio Rocha & Asociados <facundo@estudiorochayasoc.com.ar>
 */
class Nexa_Erp_Integration_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nexa_Erp_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nexa_Erp_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// Check screen base and page
		if ($_GET['page'] === 'nexa-erp-integration') {
			wp_enqueue_style($this->plugin_name, 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css', array(), $this->version, 'all');
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/nexa-erp-integration-admin.css', array(), $this->version, 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nexa_Erp_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nexa_Erp_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ($_GET['page'] === 'nexa-erp-integration') {
			wp_enqueue_script($this->plugin_name, 'https://code.jquery.com/jquery-3.7.1.min.js', [], $this->version, 'all');
			wp_enqueue_script($this->plugin_name, 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js', array('jquery'), $this->version, 'all');
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/nexa-erp-integration-admin.js', array(), $this->version, false);
		}
	}


	public function add_menu()
	{
		// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
		add_menu_page(
			"Nexa ERP | Integration", // Título de la página
			"Nexa ERP | Integration", // Literal de la opción
			"manage_options", // Dejadlo tal cual
			'nexa-erp-integration', // Slug
			array($this, 'nexa_index'), // Función que llama al pulsar
			'dashicons-database', // Icono del menú
			57
		);
	}

	public function nexa_index()
	{
		//include plugin_dir_url(__FILE__) . 'partials/nexa-erp-integration-admin-display.php';
		include 'partials/nexa-erp-integration-admin-display.php';
	}
}
