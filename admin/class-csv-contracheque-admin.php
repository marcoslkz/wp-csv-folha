<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/admin
 * @author    marcoslkz <mail>
 */
class csv_contracheque_Admin
{
	private $table_name;

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
	private $option_name = 'csv_contracheque';

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
		 * defined in csv_contracheque_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The csv_contracheque_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/csv-contracheque-admin.css', array(), $this->version, 'all');
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
		 * defined in csv_contracheque_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The csv_contracheque_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/csv-contracheque-admin.js', array('jquery'), $this->version, false);
	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_options_page()
	{

		$this->plugin_screen_hook_suffix = add_options_page(
			__('CSV Contracheque Envio', 'csv-contracheque'),
			__('CSV Contracheque', 'csv-contracheque'),
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
		include_once 'partials/csv-contracheque-admin-display.php';
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
			__('General', 'csv-contracheque'),
			array($this, $this->option_name . '_general_cb'),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_month',
			__('Informe o período do arquivo:', 'csv-contracheque'),
			array($this, $this->option_name . '_month_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_month')
		);

		add_settings_field(
			$this->option_name . '_upload',
			__('Base de dados: ', 'csv-contracheque'),
			array($this, $this->option_name . '_csv_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_upload')
		);
		
		add_settings_field(
			$this->option_name . '_logoid',
			__('Logo da Empresa: ', 'csv-contracheque'),
			array($this, $this->option_name . '_logoid_cb'),
			$this->plugin_name,
			$this->option_name . '_general',
			array('label_for' => $this->option_name . '_logoid')
		);


		register_setting($this->plugin_name, $this->option_name . '_logoid', 'intval');
		register_setting($this->plugin_name, $this->option_name . '_month', 'intval');
		register_setting($this->plugin_name, $this->option_name . '_year', 'intval');
		register_setting($this->plugin_name, $this->option_name . '_decimo', 'intval');
		//register_setting($this->plugin_name, $this->option_name . '_upload');

	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function csv_contracheque_general_cb()
	{
		global $wpdb;

		echo '<p>' . __('Primeiro, escolha o mês e ano, então salve. Após definir o mês/ano envie o arquivo.', 'csv-contracheque') . '</p>';
	}

	/**
	 * Render the treshold month input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function csv_contracheque_month_cb()
	{
		$selected_month = get_option($this->option_name . '_month');
		$selected_year = get_option($this->option_name . '_year');

?>
		<!-- HTML markup for the "Escolha o mês" option field -->
		<fieldset>
			<label for="<?php echo $this->option_name . '_month'; ?>"><?php _e('', 'csv-contracheque'); ?></label>
			<select name="<?php echo $this->option_name . '_month'; ?>" id="<?php echo $this->option_name . '_month'; ?>">
				<option value="0" <?php selected($selected_month, 0); ?>><?php _e('Escolha o período!', 'csv-contracheque'); ?></option>
				<?php
				// Generate options for months
				for ($i = 1; $i <= 12; $i++) {
					echo '<option value="' . $i . '" ' . selected($selected_month, $i) . '>' . date("F", mktime(0, 0, 0, $i, 1)) . '</option>';
				}
				?>
			</select>
		</fieldset>
		<!-- HTML markup for the "Escolha o mês" option field -->
		<fieldset>
			<select name="<?php echo $this->option_name . '_year'; ?>" id="<?php echo $this->option_name . '_year'; ?>">
				<option value="0" <?php selected($selected_year, 0); ?>></option>
				<?php
				// Generate options 
				for ($i = 2000; $i <= 2035; $i++) {
					echo '<option value="' . $i . '" ' . selected($selected_year, $i) . '>' . $i . '</option>';
				}
				?>
			</select>
			<select name="<?php echo $this->option_name . '_decimo'; ?>" id="<?php echo $this->option_name . '_decimo'; ?>">
				<option value="0" >Folha</option><option value="1" >Décimo terceiro</option>
			</select>
		</fieldset>

	<?php
		#echo '<input type="text" name="' . $this->option_name . '_month' . '" id="' . $this->option_name . '_month' . '" value="' . $month . '"> ' . __('', 'csv-contracheque');
	}

	/**
	 * Render the CSV upload field and process the uploaded file
	 *
	 * @since  1.0.0
	 */
	public function csv_contracheque_logoid_cb()
	{
		$selected_logo = get_option($this->option_name . '_logoid');

?>
		<!-- HTML markup for the "Logo" option field -->
		<fieldset>
			<label for="<?php echo $this->option_name . '_logoid'; ?>"><?php _e('', 'csv-contracheque'); ?></label>
			<select name="<?php echo $this->option_name . '_logoid'; ?>" id="<?php echo $this->option_name . '_logoid'; ?>">
			<option value="0" <?php selected($selected_logo, 0); ?>><?php _e('Selecione uma logo da Galeria de Mídia', 'csv-contracheque'); ?></option>
				<?php
				$imagens = get_posts(array(
					'post_type' => 'attachment',
					'posts_per_page' => -1,
					'post_status' => 'inherit',
					'post_mime_type' => 'image'
				));

				// Generate options for images
				foreach ($imagens as $imagem) {
					//$imagem_url = wp_get_attachment_url($imagem->ID);
					echo '<option value="' . $i . '" ' . selected($selected_year, $i) . '>' . $i . '</option>';
					echo '<option value="' . $imagem->ID . '" ' . selected($selected_logo, $imagem->ID) . '>' . $imagem->post_title . '</option>';
				}
				?>
			</select>
		</fieldset>
	<?php
	}
	/**
	 * Render the CSV upload field and process the uploaded file
	 *
	 * @since  1.0.0
	 */
	public function csv_contracheque_csv_cb()
	{
	?>
<?php
		$selected_month = get_option($this->option_name . '_logoid');
		$selected_month = get_option($this->option_name . '_month');
		$selected_year = get_option($this->option_name . '_year');
		$selected_decimo = get_option($this->option_name . '_decimo');

		echo '<p style="color: black;">Período ' . $selected_month . '/' . $selected_year . ': ' . $this->table_get_by_month_and_year($selected_month, $selected_year) . ' linhas.</p>';

		// Process CSV file if form is send
		//if (isset($_POST['upload_csv']) && check_admin_referer('csv_upload_nonce', 'csv_upload_nonce') && $selected_month !== 0) {
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $selected_month > 0) {
			$csv_file = $_FILES[$this->option_name . '_upload'];
			// Check for errors in file upload
			if ($csv_file['error'] !== UPLOAD_ERR_OK) {
				echo '<p>File upload failed. Please try again.</p>';
				return;
			}

			// Read CSV data
			try {
				$file_path = $csv_file['tmp_name'];
				$csv_data = array_map(function ($v) {
					return str_getcsv($v, "\t");
				}, file($file_path, FILE_SKIP_EMPTY_LINES));
				$csv_data = mb_convert_encoding($csv_data, 'UTF-8', 'ISO-8859-1');
			} catch (Exception $e) {
				// Handle the exception here (e.g., log the error or display a message)
				echo '<p>Database error, contact admin: ' . $e->getMessage() . '</p>';
				wp_die('Error creating table: ' . $e->getMessage());
			}

			if (!$selected_decimo ) $result = $this->table_delete_by_month_and_year($selected_month, $selected_year);
			$result = $this->insert_csv_data($csv_data, $selected_month); // not year
			if ($result) {
				echo '<p style="color: green;">CSV OK: ' .
					$this->table_get_by_month_and_year($selected_month, $selected_year) . ' linhas adicionadas no banco.</p>';
			} else {
				echo '<p style="color: red;">CSV error: verifique o arquivo.</p>';
			}
		}
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

			try {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			} catch (Exception $e) {
				// Handle the exception here (e.g., log the error or display a message)
				error_log('Database error: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Generate column definitions for the CSV fields
	 */
	private function generate_field_columns()
	{
		$columns = array();

		for ($i = 1; $i <= 57; $i++) {
			$columns[] = "field_$i VARCHAR(255)";
		}
		$columns[] = "month INT";

		return implode(", ", $columns);
	}

	/**
	 * Insert CSV data into the CSV table
	 */
	private function insert_csv_data($data, $month)
	{
		global $wpdb;

		foreach ($data as $row) {
			$insert_data = array();
			foreach ($row as $key => $value) {
				$insert_data["field_" . ($key + 1)] = sanitize_text_field($value);
			}
			$insert_data["month"] = $month;

			try {
				$return = $wpdb->insert($this->table_name, $insert_data);
			} catch (Exception $e) {
				// Handle the exception here (e.g., log the error or display a message)
				error_log('Database error: ' . $e->getMessage());
			}
		}
		return $return;
	}

	/**
	 * Count lines rows based on "month" value in table
	 *
	 * @param  string $month
	 * @return bool
	 */
	private function table_get_by_month_and_year($month, $year)
	{
		global $wpdb;

		// Prepare SQL statement
		$sql = "SELECT COUNT(*) FROM $this->table_name  WHERE  month = %s AND field_57 like  %s";

		// Prepare and execute query
		$result = $wpdb->get_var($wpdb->prepare($sql, $month, '%' . $wpdb->esc_like($year)));

		return $result ? (int) $result : 0;
	}

	/**
	 * Delete rows based on "month" value in table
	 *
	 * @param  string $month
	 * @return bool
	 */
	function table_delete_by_month_and_year($month, $year)
	{
		global $wpdb;

		// Prepare SQL statement
		$sql = "DELETE FROM $this->table_name  WHERE month = %s AND field_11 = %s";

		// Prepare and execute query
		$result = $wpdb->query($wpdb->prepare($sql, $month, $year));

		// Return true if successful, false otherwise
		return $result !== false;
	}

}

