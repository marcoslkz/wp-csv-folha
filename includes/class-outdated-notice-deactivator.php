<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/includes
 * @author     Firdaus Zahari <firdaus@fsylum.net>
 */
class Outdated_Notice_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		csv_plugin_deactivation()
	}

	/**
	 * Deactivate plugin and remove table "csv_teste"
	 */
	function csv_plugin_deactivation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'csv_folha';

		// Check if the table exists
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
			// Drop the table
			$wpdb->query("DROP TABLE $table_name");
		}
	}
}
