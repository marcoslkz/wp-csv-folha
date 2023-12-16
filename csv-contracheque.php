<?php

/**
 * @link              http://cloudomega.systes.net
 * @since             1.0.3
 * @package           csv_contracheque
 *
 * @wordpress-plugin
 * Plugin Name:       CSV Contracheque
 * Plugin URI:        https://github.com/marcoslkz/wp-csv-folha
 * Description:       Plugin para contracheque.
 * Version:           1.0.3
 * Author:           marcoslkz
 * Author URI:        https://github.com/marcoslkz
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       csv-contracheque
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-csv-contracheque-activator.php
 */
function activate_csv_contracheque() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csv-contracheque-activator.php';
	csv_contracheque_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-csv-contracheque-deactivator.php
 */
function deactivate_csv_contracheque() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-csv-contracheque-deactivator.php';
	csv_contracheque_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_csv_contracheque' );
register_deactivation_hook( __FILE__, 'deactivate_csv_contracheque' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-csv-contracheque.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_csv_contracheque() {

	$plugin = new csv_contracheque();
	$plugin->run();

}
run_csv_contracheque();
