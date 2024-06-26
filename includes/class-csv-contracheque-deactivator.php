<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    csv_contracheque
 * @subpackage csv_contracheque/includes
 * @author    marcoslkz <mail>
 */
class csv_contracheque_Deactivator
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
		global $wpdb;
		$table_name = $wpdb->prefix . 'csv_folha';

		// Check if the table exists
		if ($wpdb->get_var("select 1 from information_schema.tables where table_name='$table_name'")) {
			// Drop the table
			$wpdb->query("DROP TABLE $table_name");
		}
		//csv_plugin_deactivation()
	}

}
