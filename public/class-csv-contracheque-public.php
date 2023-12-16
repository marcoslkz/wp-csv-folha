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
class csv_contracheque_Public {

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
	public function __construct( $plugin_name, $version ) {

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
	public function enqueue_styles() {

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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/csv-contracheque-public.css', array(), $this->version, 'all' );

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
		 * defined in csv_contracheque_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The csv_contracheque_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/csv-contracheque-public.js', array( 'jquery' ), $this->version, false );

	}

	public function the_content( $post_content ) {

		if ( is_main_query() && is_singular( 'post' )) {
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

			// Add the class
			$notice = '<div class="csv-contracheque-folha">' . $this->show_contracheque('17803224709') . '</div>';
			//$notice = sprintf( $notice, $class );
			$post_content = $post_content . $notice ;
		}

        return $post_content;
	}
	// Function to download table data as PDF with field_4 filter
	function show_contracheque($filter_value) {
		global $wpdb;
		$vencimentos = 0.0;
		$descontos = 0.0;

		// Fetch data from the database based on the filter
		$query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE field_9 = %s", $filter_value);
		$results = $wpdb->get_results($query);
		if (empty($results)) {
			return "";
		}
		
		// Set some content to display (table data)
		$html = '<h4>' . esc_html($results[0]->field_8) ."<br>CNPJ:" . esc_html($results[0]->field_10) . '      Ref.' . esc_html($results[0]->field_57) . '</h4>';
        $html .= '<table border="1" style="text-align: left;">';
		$html .= '<tr><th>Código:<br>' . esc_html($results[0]->field_3) . '</th><th>Nome:<br>' . esc_html($results[0]->field_4) . '</th><th>CPF:<br>' . esc_html($results[0]->field_9) . '</th><th>Função:<br>' . esc_html($results[0]->field_12) . '</th><th>Seção:<br>' . esc_html($results[0]->field_14) . '</th></tr>';
		
		
		$html .= '<tr><th>Cód.</th><th>Descrição</th><th>Referência</th><th>Vencimentos</th><th>Descontos</th></tr>';

		foreach ($results as $row) {
			$html .= '<tr>';
			$html .= '<td>' . esc_html($row->field_23) . '</td>';
			$html .= '<td>' . esc_html($row->field_21) . '</td>';
			$html .= '<td>' . esc_html($row->field_24) . '</td>';
			$valor=str_replace(',', '.', $row->field_25);
			if ($row->field_26 == 'D') {
				$descontos=bcadd($valor, $descontos, 2);
				$html .= '<td> </td><td>' . esc_html($row->field_25) . '</td>';
			}
			elseif ($row->field_26 == 'P'){
                $html .= '<td>' . esc_html($row->field_25) . '</td><td> </td>';
				$vencimentos=bcadd($valor, $vencimentos, 2);
			}else{
                $html .= '<td></td><td></td>';				
			}
			//$html .= '</tr>';
		}
		$html .= '<tr><th>TOTAIS:<th></th><th></th><th>' . number_format($vencimentos, 2, ',', '') . '</th><th>' . number_format($descontos, 2, ',', '')  . '</th></tr>';
		$html .= '<tr><th>Banco: <th>' . esc_html($row->field_39) . '</th><th>Agência:<br> ' . esc_html($row->field_28) . '-' . esc_html($row->field_29) . '</th><th><th>Valor Líquido:<br>' . number_format(bcsub($vencimentos, $descontos, 2), 2, ',', '') . '</th></tr>';
		$html .= '</table>';
		
		$html .= '</table>';		
		$html .= '<table border="1" style="text-align: left;">';
		$html .= '<tr><td>Salário Base:<br>' . esc_html($row->field_30) . '</td><td>Sal.Contr.INSS:<br>' . esc_html($row->field_31) . 
			'</td><td>Base Cálc. FGTS:<br>' . esc_html($row->field_34) . 
			'</td><td>F.G.T.S. do Mês:<br>' . esc_html($row->field_35) . 
			'</td><td>Base Cálculo IRRF:<br>' . esc_html($row->field_32) . 
			'</td><td>Faixa IRRF:<br>' . esc_html($row->field_36) . 
			'</td></tr>';
		$html .= '</table>';
		return $html ;
		// Output HTML content to PDF
		//$pdf->writeHTML($html, true, false, true, false, '');

		// Set the file name for the PDF download
		//$file_name = 'table_data.pdf';

		// Output PDF as a download file
		//$pdf->Output($file_name, 'D');
	}


	public function me_post_pdf(){
		if (isset($_POST['me_post_pdf'])){
			include 'dompdf-master/autoload.inc.php';
			global $wp;
			$current_url = home_url(add_query_arg(array(),$wp->request));
			
			$html = file_get_contents($current_url);
			
			$options = new Options();
			$options->set('A4','potrait');
			$options->set('enable_css_float',true);
			$options->set('isHtml5ParserEnabled', true);
		
			$dompdf = new DOMPDF($options);
			$dompdf->loadHtml($html);
		
			$dompdf->render();
		
			$dompdf->stream('title.pdf');
		}
	}

}
