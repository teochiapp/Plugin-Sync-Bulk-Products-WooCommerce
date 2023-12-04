<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://estudiorochayasoc.com.ar
 * @since             1.0.0
 * @package           Nexa_Erp_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Nexa ERP | Integration
 * Plugin URI:        https://estudiorochayasoc.com.ar
 * Description:       Integración de Nexa ERP con Woocommerce
 * Version:           1.0.0
 * Author:            Estudio Rocha & Asociados
 * Author URI:        https://estudiorochayasoc.com.ar
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nexa-erp-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('NEXA_ERP_INTEGRATION_VERSION', '1.0.0');
define('NEXA_ERP_API', 'http://oficina.nexa.com.ar:4019');
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-nexa-erp-integration-activator.php
 */
function activate_nexa_erp_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-nexa-erp-integration-activator.php';
	Nexa_Erp_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-nexa-erp-integration-deactivator.php
 */
function deactivate_nexa_erp_integration()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-nexa-erp-integration-deactivator.php';
	Nexa_Erp_Integration_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_nexa_erp_integration');
register_deactivation_hook(__FILE__, 'deactivate_nexa_erp_integration');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-nexa-erp-integration.php';

require plugin_dir_path(__FILE__) . 'includes/class-nexa-erp-integration-ajax-handler.php';

 

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_nexa_erp_integration()
{

	$plugin = new Nexa_Erp_Integration();
	$plugin->run();
}
run_nexa_erp_integration();
