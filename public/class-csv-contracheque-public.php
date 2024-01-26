<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/public
 */

require_once  'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/public
 * @author    marcoslkz <firdaus@fsylum.net>
 */
class csv_contracheque_Public
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
	private $table_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{
		global $wpdb;

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->table_name  = $wpdb->prefix . 'csv_folha';
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/csv-contracheque-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/csv-contracheque-public.js', array('jquery'), $this->version, true);
		// Localize the script with the ajaxurl variable
		wp_localize_script(
			$this->plugin_name,
			'ajax_obj',
			array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => 1234)
		);
	}

	function get_results_distinct($coluna, $cpf)
	{
		$results = 0;
		global $wpdb;
		try {

			$query = $wpdb->prepare("SELECT DISTINCT %s FROM %s WHERE field_9 = '%s'", $coluna, $this->table_name, $cpf);
			$results = $wpdb->get_results($query);
		} catch (Exception $e) {
			// Handle the exception
			$results = 0;
		}
		return $results;
	}

	/**
	 * Retrieve unique results for a specific query.
	 *
	 * @param string $cpf   The CPF value.
	 * @param int    $month The month value.
	 *
	 * @return array Unique results suitable for creating HTML select options.
	 */
	function get_unique_results_for_select($cpf)
	{
		// Retrieve results
		$results = $this->get_results_distinct('field_56', $cpf);

		// Transform the results into an array suitable for HTML select options
		$options = array();
		foreach ($results as $result) {
			$options[$result['field_56']] = $result['field_56'];
		}

		return $options;
	}

	/**
	 * Convert a date string to the month.
	 *
	 * @param string $dateString The date string (format: "mm/dd/yyyy").
	 *
	 * @return int|false The month as an integer (1 to 12), or false on failure.
	 */
	function convertDateStringToMonth($dateString)
	{
		$timestamp = strtotime($dateString);

		if ($timestamp === false) {
			// Invalid date string
			return false;
		}

		$month = (int)date('m', $timestamp);

		return $month;
	}

	function get_month_ptbr($month_number)
	{
		// Ensure the input is a valid month number
		if (!is_numeric($month_number) || $month_number < 1 || $month_number > 12) {
			return 'Inválido';
		}

		$month_names_ptbr = array(
			'Janeiro',
			'Fevereiro',
			'Março',
			'Abril',
			'Maio',
			'Junho',
			'Julho',
			'Agosto',
			'Setembro',
			'Outubro',
			'Novembro',
			'Dezembro'
		);

		// Adjust the index to match array indexing (arrays are zero-based)
		$month_index = $month_number - 1;

		return $month_names_ptbr[$month_index];
	}
	function number_double($val1)
	{
		return number_format(doubleval($val1), 2, ',', '');
	}

	// (0) Página do Contra Cheque
	// (1)  (?)
	// (2)  Código do Colaborador
	// (3)  Nome do Colaborador
	// (4)  Número da Conta Corrente
	// (5)  Dígito da Conta Corrente
	// (6)  (?)
	// (7)  Nome da Empresa
	// (8)  CPF do Colaborador
	// (9)  CNPJ da Empresa
	// (10) (?)
	// (11) Cargo do Colaborador
	// (12) CBO
	// (13) CC
	// (14) (?)
	// (15) (?)
	// (16) Depto
	// (17) (?)
	// (18) (?)
	// (19) Data Admissão
	// (20) DESCRIÇÃO
	// (21) (?)
	// (22) Código da Descrição
	// (23) Valor de Referência
	// (24) Valor do Vencimento ou Desconto
	// (25) Define se é Vencimento (P) ou Desconto (D)
	// (26) (?)
	// (27) Número da Agência
	// (28) Dígito da Agência
	// (29) Salário Base
	// (30) Sal. Contr. INSS
	// (31) Base Cálculo IRRF
	// (32) (?)
	// (33) Base Cálc. FGTS
	// (34) F.G.T.S. do Mês
	// (35) Faixa IRRF
	// (36) (?)
	// (37) (?)
	// (38) Nome do Banco com Código
	// (39) Cargo do Colaborador
	// (40) (?)
	// (41) Data C. Conta
	// (42) Data de Nascimento do Colaborador
	// (43) (?)
	// (44) (?)
	// (45) (?)
	// (46) (?)
	// (47) (?)
	// (48) Horas de Referência
	// (49) (?)
	// (50) (?)
	// (51) (?)
	// (52) (?)
	// (53) (?)
	// (54) (?)
	// (55) (?)
	// (56) Data de Referência
	// Function to download table data as PDF with field_4 filter
	function show_contracheque($cpf, $ref)
	{
		global $wpdb;
		$vencimentos = 0.0;
		$descontos = 0.0;
		$results = "";

		$html = "";
		try {

			// Fetch data from the database based on the filter
			$query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE field_9 = '%s' and field_56 = %s", $cpf, $ref);
			$results = $wpdb->get_results($query);
		} catch (Exception $e) {
			// Handle the exception
			return  '<p>Error: ' . $e->getMessage() . '</p>';
		}
		if (empty($results)) {
			return "<p>Sem informações encontradas para o mês: " . $ref . " CPF: " . $cpf . ".</p>";
		}

		$logo_id = get_theme_mod('custom_logo');
		$logo_url = wp_get_attachment_image_src($logo_id, 'full');

		if ($logo_url) {
			$html .= '<img src="' . esc_url($logo_url[0]) . '" alt="Logo">';
		}
		// Set some content to display (table data)
		$html .= '<h4>' . esc_html($results[0]->field_8) . "<br>CNPJ: " . esc_html($results[0]->field_10) . '      Ref.' . esc_html($results[0]->field_57) . '</h4>';
		$html .= '<table >';
		$html .= '<tr><th>Código: <br>' . esc_html($results[0]->field_3) . '</th><th  colspan="3" rowspan="1">Nome:<br>' . esc_html($results[0]->field_4) . '</th><th>CPF:<br>' . esc_html($results[0]->field_9) . '</th><th>Função:<br>' . esc_html($results[0]->field_12) . '</th><th>Seção:<br>' . esc_html($results[0]->field_14) . '</th></tr>';
		$html .= '<tr><th>Cód.</th><th colspan="3" rowspan="1">Descrição</th><th>Referência</th><th>Vencimentos</th><th>Descontos</th></tr>';

		foreach ($results as $row) {
			$html .= '<tr>';
			$html .= '<td>' . esc_html($row->field_23) . '</td>';
			$html .= '<td colspan="3" rowspan="1">' . esc_html($row->field_21) . '</td>';
			$html .= '<td>' . $this->number_double($row->field_24) . '</td>';
			$valor = str_replace(',', '.', $row->field_25);
			if ($row->field_26 == 'D') {
				$descontos = bcadd($valor, $descontos, 2);
				$html .= '<td> </td><td>' . $this->number_double($row->field_25) . '</td>';
			} elseif ($row->field_26 == 'P') {
				$html .= '<td>' . $this->number_double($row->field_25) . '</td><td> </td>';
				$vencimentos = bcadd($valor, $vencimentos, 2);
			} else {
				$html .= '<td></td><td></td>';
			}
			//$html .= '</tr>';
		}

		foreach ($results as $row) {
			$html .= '<tr><td><br></td><td colspan="3" rowspan="1"></td><td></td><td></td><td></td></tr>';
		}

		$html .= '<tr><th colspan="5" rowspan="1">TOTAIS: </th><th>' . $this->number_double($vencimentos) . '</th><th>' . $this->number_double($descontos) . '</th></tr>';
		$html .= '<td colspan="2" rowspan="1">' . esc_html($row->field_39) . '</td><td>Agência: ' . esc_html($row->field_28) . '-' . esc_html($row->field_29) .
			'</td><td colspan="2" rowspan="1"></td><td  colspan="1" rowspan="2">Valor Líquido: </td><td  colspan="1" rowspan="2">' . bcsub($vencimentos, $descontos, 2) . '</td></tr>';
		$html .= '<td colspan="2" rowspan="1">CPF: ' . esc_html($row->field_9) . '</td><td></td></tr>';

		$html .= '<tr><td colspan="2" rowspan="1">Salário Base:<br>' . esc_html($row->field_30) . '</td><td>Sal.Contr.INSS:<br>' . esc_html($row->field_31) .
			'</td><td>Base Cálc. FGTS:<br>' . $this->number_double($row->field_34) .
			'</td><td>F.G.T.S. do Mês:<br>' . $this->number_double($row->field_35) .
			'</td><td>Base Cálculo IRRF:<br>' . $this->number_double($row->field_32) .
			'</td><td>Faixa IRRF:<br>' . $this->number_double($row->field_36) .
			'</td></tr>';
		$html .= '</table>';
		return $html;
		// Output HTML content to PDF
		//$pdf->writeHTML($html, true, false, true, false, '');

		// Set the file name for the PDF download
		//$file_name = 'table_data.pdf';

		// Output PDF as a download file
		//$pdf->Output($file_name, 'D');
	}
	function full_html($title, $content)
	{
		$html = '
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    @page { margin: 0in; }
    body { background-color: white; padding: 1in; }
    #wrapper { background-color: yellow; width: 95%; height: 95%; padding: 5%; }
  </style>
				<title>' . $title . '</title>
			</head>
			<body>
				' . $content . '
			</body>
			</html>
		';

		return $html;
	}

	public function generate_contracheque_pdf($cpf, $ref)
	{
		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$dompdf->loadHtml($this->full_html("Contracheque", $this->show_contracheque($cpf, $ref)));

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		$dompdf->render();

		$dompdf->stream();
		$dompdf->stream('contracheque_' .  $this->get_month_ptbr($this->convertDateStringToMonth($ref)) . '.pdf', array('Attachment' => 0));
		//$pdf->SetTitle('Contracheque_' . $this->get_month_ptbr($month));
		wp_die(); // Always include this to terminate the script

		//exit(); // Always include this to terminate the script
		// Output the generated PDF as a response
		$pdf_output = $dompdf->output();
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="download.pdf"');
		echo $pdf_output;
		exit();

		//}
	}


	// Shortcode function
	function contracheque_form_pdf_shortcode()
	{
		// Get the current user object
		$current_user = wp_get_current_user();
		$cpf = "";
		if (!$current_user->exists()) return "<p>Acesso negado.<\p>";
		else $cpf = esc_html($current_user->user_login);

		// Display a simple form for selecting the month
		$html = '
		<form id="contracheque_pdf_form" method="post">

			<label for="selected_month">Escolha o contracheque: </label>
			<select name="selected_month" id="selected_month">
			';

		$options = $this->get_unique_results_for_select($cpf);
		foreach ($options as $value => $label) {
			$html .= '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
		}

		$html .=  '
			</select>
			<input type="submit" value="Exibir Contracheque">
			';

		// Generate HTML for the table
		$selected_month = isset($_POST['selected_month']) ? (int)$_POST['selected_month'] : 0;

		if ($selected_month > 0) {
			$this->generate_contracheque_pdf($cpf, $selected_month);
		}
		return $html;
	}
	// Shortcode function
	function contracheque_form_month_shortcode()
	{

		ob_start(); // Start output buffering
		
		$current_user = wp_get_current_user();
?>
		<form id="contracheque_month_form" method="post">

			<label for="selected_month">Escolha o contracheque: </label>
			<select name="selected_month" id="selected_month">
				<!-- Options for months -->
				<?php
				$options = $this->get_unique_results_for_select(esc_html($current_user->user_login));
				foreach ($options as $value => $label) {
					echo '<option value="' . esc_attr($value) . '">' . esc_html($label) . '</option>';
				}
				?>
			</select>
			<input type="submit" value="Exibir Contracheque">
			<div id="contracheque_result_table"></div>
			<script src="<?php echo plugin_dir_url(__FILE__) . 'js/csv-contracheque-public.js'; ?>"></script>
	<?php
		return ob_get_clean(); // Return the buffered content
	}
	// AJAX handler
	function contracheque_get_data_callback()
	{
		// Generate HTML for the table
		$selected_month = isset($_POST['selected_month']) ? (int)$_POST['selected_month'] : 0;
		// Get the current user object
		$current_user = wp_get_current_user();

		// Check if a user is logged in
		if ($current_user->exists())
			$cpf = esc_html($current_user->user_login);

		$html = '<div class="csv-contracheque">' . $this->show_contracheque($cpf, $selected_month);
		$html .= "</div>";
		//$html .= $cpf;
		//$html .= "<p>" . $selected_month . "</p>";
		//$html .= "<p>" . $cpf . "</p>";

		echo $html;

		wp_die(); // Always include this to terminate the script
	}
}
