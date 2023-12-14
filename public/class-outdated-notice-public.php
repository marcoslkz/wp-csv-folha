<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://fsylum.net
 * @since      1.0.0
 *
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Outdated_Notice
 * @subpackage Outdated_Notice/public
 * @author     Firdaus Zahari <firdaus@fsylum.net>
 */
class Outdated_Notice_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/outdated-notice-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/outdated-notice-public.js', array( 'jquery' ), $this->version, false );

	}

	public function the_content( $post_content ) {

		if ( is_main_query()) {
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
			$position  = get_option( 'outdated_notice_position', 'before' );
			$days      = (int) get_option( 'outdated_notice_month', 0 );
			$date_now  = new DateTime( current_time('mysql') );

			// Add the class
			$notice = '<div class="outdated-notice %s">' . $notice . '</div>';
		}

        return $post_content;
	}
	// Function to download table data as PDF with field_4 filter
function download_table_data_as_pdf($filter_value) {
    global $wpdb;

    // Fetch data from the database based on the filter
    $table_name = $wpdb->prefix . 'your_table_name'; // Replace with your table name
    $query = $wpdb->prepare("SELECT * FROM $table_name WHERE field_9 = %s", $filter_value);
    $results = $wpdb->get_results($query);

    // Include the TCPDF library
	//require_once get_template_directory() . ‘/dompdf/autoload.inc.php’;
	//use Dompdf\Dompdf;
    require_once('tcpdf/tcpdf.php');

    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('marcoslkz');
    $pdf->SetAuthor('marcoslkz');
    $pdf->SetTitle('Contracheque');
    $pdf->SetSubject('Contracheque');

    // Add a page
    $pdf->AddPage();

    // Set some content to display (table data)
    $html = '<h1>' . $results[1]['field_8'] . '</h1>';
    $html .= '<table border="1">';
    $html .= '<tr><th>ID</th><th>Field 1</th><th>Field 2</th><th>Field 3</th><th>Field 4</th></tr>';
    
    foreach ($results as $row) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['field_1'] . '</td>';
        $html .= '<td>' . $row['field_2'] . '</td>';
        $html .= '<td>' . $row['field_3'] . '</td>';
        $html .= '<td>' . $row['field_4'] . '</td>';
        $html .= '</tr>';
    }
    
    $html .= '</table>';

    // Output HTML content to PDF
    $pdf->writeHTML($html, true, false, true, false, '');

    // Set the file name for the PDF download
    $file_name = 'table_data.pdf';

    // Output PDF as a download file
    $pdf->Output($file_name, 'D');
}


}
