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

//require_once  'dompdf/autoload.inc.php';
require_once('tcpdf/tcpdf.php');

//use Dompdf\Dompdf;
//use Dompdf\Options;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    csv_contracheque
 * @subpackage csv_contracheque/public
 * @author    marcoslkz <mail>
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
	 * The options name to be used in this plugin
	 *
	 * @since  	1.0.0
	 * @access 	private
	 * @var  	string 		$option_name 	Option name of this plugin
	 */
	private $option_name = 'csv_contracheque';

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

	function table_results_distinct($coluna, $cpf)
	{
		$results = 0;
		global $wpdb;
		$sql = "SELECT DISTINCT $coluna FROM  $this->table_name WHERE field_9 = %s";
		try {

			$query = $wpdb->prepare($sql, $cpf);
			$results = $wpdb->get_results($query, ARRAY_A);
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
	function get_unique_options($cpf)
	{
		// Retrieve results
		$results = $this->table_results_distinct('field_57', $cpf);

		// Transform the results into an array suitable for HTML select options
		$options = array();
		foreach ($results as $result) {
			$options[$result['field_57']] = $result['field_57'];
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

		$month = date('m', $timestamp);

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
		$sql = "SELECT * FROM  $this->table_name WHERE field_9 = %s and field_57 = %s";
		try {

			// Fetch data from the database based on the filter
			$query = $wpdb->prepare($sql, $cpf, $ref);
			$results = $wpdb->get_results($query);
		} catch (Exception $e) {
			// Handle the exception
			return  '<p>Error: ' . $e->getMessage() . '</p>';
		}
		if (empty($results)) {
			return "<p>Sem informações encontradas para folha: " . $ref . " CPF: " . $cpf . ".</p>";
		}

		$logo_id = get_theme_mod('custom_logo');
		$logo_url = wp_get_attachment_image_src($logo_id, 'full');

		if ($logo_url) {
			$html .= '<img src="' . esc_url($logo_url[0]) . '" alt="Logo">';
		}
		// Set some content to display (table data)
		$html .= '<br><br><h4>' . esc_html($results[0]->field_8) . "<br>CNPJ: " . esc_html($results[0]->field_10) . '      Ref.' . esc_html($results[0]->field_57) . '</h4>';
		$html .= '<table >';
		$html .= '<tr><th>Código' . 
		'</th><th  colspan="2" rowspan="1">Nome' .
		'</th><th>CPF' . 
		'</th><th colspan="1">Função' . 
		'</th><th colspan="2">Seção' .
		'</th></tr>';
		$html .= '<tr><td>' . esc_html($results[0]->field_3) . 
		'</td><td  colspan="2" rowspan="1">' . esc_html($results[0]->field_4) . 
		'</td><td>' . esc_html($results[0]->field_9) . 
		'</td><td colspan="1">' . esc_html($results[0]->field_12) . 
		'</td><td colspan="2">' . esc_html($results[0]->field_14) . 
		'</td></tr>';
		//$html .= '</table ><table >';
		$html .= '<tr>
		<th>Cód.</th>
		<th colspan="3" rowspan="1">Descrição</th>
		<th>Referência</th>
		<th>Vencimentos</th>
		<th>Descontos</th>
		</tr>';
		$countlines = 0;
		
		foreach ($results as $row) {
			$html .= '<tr>';
			$html .= '<td>' . esc_html($row->field_23) . '</td>';
			$html .= '<td colspan="3" rowspan="1">' . esc_html($row->field_21) . '</td>';
			$html .= '<td>' . $this->number_double($row->field_24) . '</td>';
			$valor = str_replace(',', '.', $row->field_25);
			if ($row->field_26 == 'D') {
				$descontos = bcadd($valor, $descontos, 2);
				$html .= '<td></td><td>' . $this->number_double($row->field_25) . '</td>';
			} elseif ($row->field_26 == 'P') {
				$html .= '<td>' . $this->number_double($row->field_25) . '</td><td></td>';
				$vencimentos = bcadd($valor, $vencimentos, 2);
			} else {
				$html .= '<td></td><td></td>';
			}
			$html .= '</tr>';
			$countlines++;
		}
		
		for($i = 0; $i < (11-$countlines); ++$i) {
			$html .= '<tr><td> - </td><td colspan="3" rowspan="1"></td><td></td><td></td><td></td></tr>';
		}

		$html .= '<tr><th colspan="5" rowspan="1">TOTAIS: </th><th>' . $this->number_double($vencimentos) . '</th><th>' . $this->number_double($descontos) . '</th></tr>';
		//$html .= '</table >';
		//$html .= '<table >';
		$html .= '<tr><td colspan="3">' . esc_html($row->field_39) .
			'</td><th colspan="2">Valor Líquido: </th><th colspan="2">' . $this->number_double(bcsub($vencimentos, $descontos, 2)) . 
			'</th></tr>';
		$html .=  '<tr><td colspan="7" rowspan="1">Agência: ' . esc_html($row->field_28) . '-' . esc_html($row->field_29) . 
			' Conta '. esc_html($row->field_7) . ': ' . esc_html($row->field_5) . '-' . esc_html($row->field_6) . 
			'</td></tr>';

		$html .= '<tr><th colspan="2" rowspan="1">Salário Base' .
			'</th><th>Sal.Contr.INSS' .
			'</th><th>Base Cálc. FGTS' .
			'</th><th>F.G.T.S. do Mês' .
			'</th><th>Base Cálculo IRRF' .
			'</th><th>Faixa IRRF' .
			'</th></tr>';
		$html .= '<tr><td colspan="2" rowspan="1">' . esc_html($row->field_30) . 
			'</td><td>' . esc_html($row->field_31) .
			'</td><td>' . $this->number_double($row->field_34) .
			'</td><td>' . $this->number_double($row->field_35) .
			'</td><td>' . $this->number_double($row->field_32) .
			'</td><td>' . $this->number_double($row->field_36) .
			'</td></tr>';
		$html .= '</table>';
		return $html;
	}
	function full_html($title, $content)
	{
		//	<link rel="stylesheet" href="' .  plugin_dir_url(__FILE__) . '/css/csv-contracheque-public.css">
		$html = '
			<html>
			<head>
				<style>
.csv-contracheque table {
border-collapse: collapse;
width: 100%;
}
.csv-contracheque th {
border: 1px solid black;
padding: 8px;
white-space: nowrap; /* This prevents line breaks */

font-weight: bold;
}
.csv-contracheque td {
border: 1px solid black;
padding: 8px;
white-space: nowrap; /* This prevents line breaks */
font-size: 0.9em; 
}
</style>
					<title>' . $title . '</title>
			</head>
			<body>
			<div class="csv-contracheque" style="overflow-x:auto;">'
			. $content . '
			</div>
			</body>
			</html>
		';

		return $html;
	}



	public function generate_contracheque_pdf($cpf, $ref)
	{
// Your HTML content (replace this with your actual HTML)
$html = $this->full_html("Contracheque", $this->show_contracheque($cpf, $ref));

// Create a new TCPDF instance
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('empresa');
$pdf->SetTitle('Contracheque');
$pdf->SetKeywords('PDF, HTML, PHP, TCPDF');

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Remover cabeçalho e rodapé padrão
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// URL da imagem
//$image_url = wp_get_attachment_url(get_option($this->option_name . '_logoid'));
$image_url = get_attached_file(get_option($this->option_name . '_logoid'));
$image_size = get_attached_file(get_option($this->option_name . '_logosize'));
$image_size = 50;
		
// Exibir a imagem no PDF
if ($image_url) {
    $htmli = '<img src="' . $image_url . '" width="'. $image_size .'"  />';
	$pdf->writeHTML($htmli, true, false, true, false, '');
    //$pdf->Image($image_url, 10, 10, 50, '', '', '', 'L', false, 300, '', false, false, 0, false, false, false);
} else {
    $pdf->Cell(0, 10, 'Logo '. $image_url .' não encontrado.', 0, 1, 'L');
}
		
// Convert HTML to PDF
//$html .= '<style>'.file_get_contents(_BASE_PATH.'public/css/csv-contracheque-public.css').'</style>';
$pdf->writeHTML($html, true, false, true, false, 'J');

// Output the PDF to the browser or save it to a file
$pdf->Output('contracheque.pdf', 'I'); // 'I' for inline display, 'D' for download

// Clean up
$pdf->Close();
	}

	// Shortcode function
	function contracheque_form_pdf_shortcode()
	{
		// Get the current user object
		$current_user = wp_get_current_user();
		$cpf = "";
		if (!$current_user->exists()) return "Acesso negado.";
		else $cpf = esc_html($current_user->user_login);

		// Display a simple form for selecting the month
		$html = '
		<form id="contracheque_pdf_form" method="post">

			<label for="selected_month">Escolha o contracheque: </label>
			<select name="selected_month" id="selected_month">
			';

		$options = $this->get_unique_options($cpf);
		foreach ($options as $value => $label) {
			$html .= '<option value="' . $value . '">' . esc_html($label) . '</option>';
		}

		$html .=  '
			</select>
			<input type="submit" value="Imprimir Contracheque">
			';

		// Generate HTML for the table
		$selected_month = isset($_POST['selected_month']) ? $_POST['selected_month'] : 0;

		if ($selected_month != 0) {
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
				$options = $this->get_unique_options(esc_html($current_user->user_login));
				foreach ($options as $value => $label) {
					echo '<option value="' . $value . '">' . esc_html($label) . '</option>';
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
		$selected_month = isset($_POST['selected_month']) ? $_POST['selected_month'] : 0;
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

