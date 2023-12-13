<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/admin
 * @author     Firdaus Zahari <firdaus@fsylum.net>
 */
class Outdated_Notice_Admin
{
	private $table_name ;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'outdated_notice';

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
		global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table_name  = $wpdb->prefix . 'csv_folha';
		// Create the CSV table 
		$this->create_csv_table();
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
		 * defined in Outdated_Notice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Outdated_Notice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/outdated-notice-admin.css', array(), $this->version, 'all');
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
		 * defined in Outdated_Notice_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Outdated_Notice_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/outdated-notice-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page()
	{

		$this->plugin_screen_hook_suffix = add_options_page(
			__('Outdated Notice Settings', 'outdated-notice'),
			__('Outdated Notice', 'outdated-notice'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_options_page')
		);
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page()
	{
		include_once 'partials/outdated-notice-admin-display.php';
	}

	/**
	 * Register all related settings of this plugin
	 *
	 * @since  1.0.0
	 */
	public function register_setting()
	{

		add_settings_section(
			$this->option_name . '_general',
			__('General', 'outdated-notice'),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_position',
			__('Text position', 'outdated-notice'),
			array($this, $this->option_name . '_position_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_position')
		);

		add_settings_field(
			$this->option_name . '_month',
			__('Informe o mês do arquivo:', 'outdated-notice'),
			array($this, $this->option_name . '_month_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_month')
		);

		register_setting($this->plugin_name, $this->option_name . '_position', array($this, $this->option_name . '_sanitize_position'));
		register_setting($this->plugin_name, $this->option_name . '_month', 'intval');

		add_settings_section(
			$this->option_name . '_upload',
			__('CSV upload', 'outdated-notice'),
			array($this, 'csv_upload_field_cb'),
			$this->plugin_name
		);
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function outdated_notice_general_cb()
	{
		global $wpdb;
		
		echo '<p>' . __('CSV Folha', 'outdated-notice') . '</p>';
		
	}

	/**
	 * Render the radio input field for position option
	 *
	 * @since  1.0.0
	 */
	public function outdated_notice_position_cb()
	{
		global $wpdb;
		$position = get_option($this->option_name . '_position');
?>
		<fieldset>
			<label>
				<input type="radio" name="<?php echo $this->option_name . '_position' ?>" id="<?php echo $this->option_name . '_position' ?>" value="before" <?php checked($position, 'before'); ?>>
				<?php _e($wpdb->get_var("SELECT COUNT(id) FROM '$this->table_name'") . " linhas.", 'outdated-notice'); ?>
			</label>
			<br>
			<label>
				<input type="radio" name="<?php echo $this->option_name . '_position' ?>" value="after" <?php checked($position, 'after'); ?>>
				<?php _e('After the content', 'outdated-notice'); ?>
			</label>
		</fieldset>
	<?php
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function outdated_notice_month_cb()
	{
		$month = get_option($this->option_name . '_month');
		echo '<input type="text" name="' . $this->option_name . '_month' . '" id="' . $this->option_name . '_month' . '" value="' . $month . '"> ' . __('', 'outdated-notice');
	}

	/**
	 * Sanitize the text position value before being saved to database
	 *
	 * @param  string $position $_POST value
	 * @since  1.0.0
	 * @return string           Sanitized value
	 */
	public function outdated_notice_sanitize_position($position)
	{
		if (in_array($position, array('before', 'after'), true)) {
			return $position;
		}
	}
	###################
	/**
	 * Render the CSV upload field and process the uploaded file
	 *
	 * @since  1.0.0
	 */
	public function csv_upload_field_cb()
	{
		// Check if the "Escolha o mês" option has been selected
		$selected_month = get_option($this->option_name . '_month');
		#$selected_month = isset($_POST[$this->option_name . '_month']) ? absint($_POST[$this->option_name . '_month']) : 0;

	?>
		<!-- HTML markup for the "Escolha o mês" option field -->
		<fieldset>
			<label for="<?php echo $this->option_name . '_month'; ?>"><?php _e('Escolha o mês:', 'outdated-notice'); ?></label>
			<select name="<?php echo $this->option_name . '_month'; ?>" id="<?php echo $this->option_name . '_month'; ?>" required>
				<option value="0" <?php selected($selected_month, 0); ?>><?php _e('Escolha o mês!', 'outdated-notice'); ?></option>
				<?php
				// Generate options for months
				for ($i = 1; $i <= 12; $i++) {
					echo '<option value="' . $i . '" ' . selected($selected_month, $i) . '>' . date("F", mktime(0, 0, 0, $i, 1)) . '</option>';
				}
				?>
			</select>
		</fieldset>
		<!-- HTML markup for the CSV upload field -->
		<fieldset>
			<label for="<?php echo $this->option_name . '_csv_file'; ?>"><?php _e('Upload CSV File', 'outdated-notice'); ?></label>
			<input type="file" name="<?php echo $this->option_name . '_csv_file'; ?>" id="<?php echo $this->option_name . '_csv_file'; ?>" accept=".csv, .txt" required>
		</fieldset>

		<?php
		// Process CSV file if form is submitted
	    if (isset($_POST['upload_csv']) && check_admin_referer('csv_upload_nonce', 'csv_upload_nonce') && $selected_month !== 0) {    
			$csv_file = $_FILES[$this->option_name . '_csv_file'];

			// Validate file type
			$file_type = wp_check_filetype($csv_file['name'], array('csv' => 'text/csv'));
			if ($file_type['ext'] !== 'csv' || $file_type['ext'] !== 'txt' ) {
				echo '<p style="color: red;">Invalid file type. Please upload a CSV file.</p>';
				return;
			}

			// Read CSV data
			$csv_data = array_map('str_getcsv', file($csv_file['tmp_name'], FILE_SKIP_EMPTY_LINES));

			// Get column headers
			$headers = array_shift($csv_data);

			// Insert data into the CSV table
			if ($this->insert_csv_data($headers, $csv_data, $selected_month)) {
				echo '<p style="color: green;">CSV file uploaded and data inserted into the table.</p>';
			} else {
				echo '<p style="color: red;">CSV upload error.</p>';
			}
		} else {
			echo '<p style="color: yellow;">Informe o mês e o respectivo arquivo CSV.</p>';

		}
		?>
		<?php
	}

	/**
	 * Create the CSV table if it doesn't exist
	 */
	private function create_csv_table()
	{
		global $wpdb;

		if (!$wpdb->get_var("select 1 from information_schema.tables where table_name='$this->table_name '")) {
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE $this->table_name (id mediumint(9) NOT NULL AUTO_INCREMENT, "
			 . $this->generate_field_columns() 
			 . ", PRIMARY KEY  (id)) $charset_collate;";

			try{
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			} catch (Exception $e) {
				// Handle the exception here (e.g., log the error or display a message)
				error_log('Database error: ' . $e->getMessage());
				// You can also display an error message if needed
				//wp_die('Error creating table: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Generate column definitions for the CSV fields
	 */
	private function generate_field_columns()
	{
		$columns = array();

		for ($i = 1; $i <= 58; $i++) {
			$columns[] = "field_$i TEXT NOT NULL";
		}
		$columns[] = "month INT NOT NULL DEFAULT 0";

		return implode(", ", $columns);
	}

	/**
	 * Insert CSV data into the CSV table
	 */
	private function insert_csv_data($headers, $data, $month)
	{
		global $wpdb;

		foreach ($data as $row) {
			$insert_data = array();
			foreach ($row as $key => $value) {
				$insert_data["field_" . ($key + 1)] = sanitize_text_field($value);
			}
			$insert_data["month"] = $month;

			try{
				$wpdb->insert($this->table_name , $insert_data);
			} catch (Exception $e) {
				// Handle the exception here (e.g., log the error or display a message)
				error_log('Database error: ' . $e->getMessage());
				// You can also display an error message if needed
				//wp_die('Error creating table: ' . $e->getMessage());
			}
		}
		return $wpdb->get_var("select 1 from $this->table_name where month == $month ");
	}
	/**
	 * Delete rows based on "month" value in table
	 *
	 * @param  string $month
	 * @return bool
	 */
	function csv_delete_by_month($month)
	{
		global $wpdb;

		// Prepare SQL statement
		$sql = "DELETE FROM $this->table_name  WHERE month = %s";

		// Prepare and execute query
		$result = $wpdb->query($wpdb->prepare($sql, $month));

		// Return true if successful, false otherwise
		return $result !== false;
	}
}
