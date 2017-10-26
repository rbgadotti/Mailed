<?php

class Mailed {

	private static $initiated = false;

	public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}

		$action = !empty($_GET['action']) ? 'action_'. $_GET['action'] : (!empty($_POST['action']) ? 'action_'. $_POST['action'] : null);

		if(!is_null($action) && method_exists(get_called_class(), $action)){
			self::$action();
		}

		add_action('wp_enqueue_scripts', array( 'Mailed', 'add_scripts'));
		add_action('wp_ajax_mailed_register_form', array( 'Mailed', 'ajax_mailed_form'));
		add_action('wp_ajax_nopriv_mailed_register_form', array( 'Mailed', 'ajax_mailed_form'));
		add_action('admin_menu', array( 'Mailed', 'register_menu_page') );

		/*
			Needs to fire at init: https://developer.wordpress.org/reference/hooks/set-screen-option/#comment-2265
		*/
		add_filter('set-screen-option', array( 'Mailed', 'set_option'), 10, 3);

	}

	/*
		Init hook (once time)
	*/
	private static function init_hooks() {
		self::$initiated = true;
	}

	/*
		Plugin Activation
	*/
	public static function plugin_activation() {

		self::create_table();
	
	}

	/*
		Plugin Deactivation
	*/
	public static function plugin_deactivation() {}

	/*
		Plugin Uninstall
	*/
	public static function plugin_uninstall() {

		self::drop_table(); 

	}

	/*
		Add Scripts
	*/
	public static function add_scripts(){

		wp_enqueue_script('mailed', plugins_url( '/mailed.js', __FILE__ ), array(), '1.0.0');

	  $js_vars = array(
	  	'permalink' => get_the_permalink(),
	  	'ajaxurl' => admin_url('admin-ajax.php')
	  );

	  wp_localize_script('mailed', 'mailed', $js_vars);


	}

	/*
		Add menu page Mailed
	*/
	public static function register_menu_page() {

		$hook = add_menu_page( 'Mailed', 'Mailed', 'activate_plugins', 'list', array( 'Mailed', 'render_view_list'), 'dashicons-email', 25);

		add_action("load-$hook", array( 'Mailed', 'add_options'));

	}

	/*
		Set page options
	*/
	public function set_option($status, $option, $value){

		return $value;

	}

	/*
		Add page options
	*/
	public static function add_options(){

		global $mailedList;

	  $option = 'per_page';
	  $args = array(
			'label' => 'Itens por página',
			'default' => MAILED__LIST_ITEMS_PER_PAGE,
			'option' => 'mailed_per_page'
		);

	  add_screen_option( $option, $args );

	  $mailedList = new Mailed_List;

	}

	/*
		Ajax Mailed register function
	*/
	public static function ajax_mailed_form(){

		$res = array(
			'status' => null,
			'message' => null
		);

		$mailed_email = null;
		$mailed_firstname = null;
		$mailed_lastname = null;

		try {

			if(empty($_POST['mailed_email']) && empty($_POST['email']))
				throw new Exception(MAILED__AJAX_ERROR_400, 400);

			if(empty($_POST['mailed_firstname']) && empty($_POST['firstname']))
				throw new Exception(MAILED__AJAX_ERROR_400, 400);

			$email = !empty($_POST['mailed_email']) ? $_POST['mailed_email'] : $_POST['email'];
			$firstname = !empty($_POST['mailed_firstname']) ? $_POST['mailed_firstname'] : $_POST['firstname'];
			$lastname = (!empty($_POST['mailed_lastname']) ? $_POST['mailed_lastname'] : (!empty($_POST['lastname']) ? $_POST['lastname'] : NULL));	

			/*
				Validation
			*/
			if(!filter_var($email, FILTER_VALIDATE_EMAIL))
				throw new Exception(MAILED__AJAX_ERROR_400, 400);

			/*
				Create
			*/
			global $wpdb;

			$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

			$field_email = MAILED__FIELD_EMAIL;
			$field_firstname = MAILED__FIELD_FIRSTNAME;
			$field_lastname = MAILED__FIELD_LASTNAME;

			$mailed_email = $email;
			$mailed_firstname = $firstname;
			$mailed_lastname = is_null($lastname) ? 'NULL' : "'$lastname'";

			$sql = "INSERT INTO $table_name($field_email, $field_firstname, $field_lastname) VALUES('$mailed_email', '$mailed_firstname', $mailed_lastname);";

			$wpdb->query($sql);

			/*
				Mailchimp Integration
			*/
			if(MailedMailchimp::is_mailchimp_autosubscribe_on()){

				MailedMailchimp::mailchimp_add($email, $firstname, $lastname);

			}

			/*
				Returns ok
			*/
			$res['status'] = 200;
			$res['message'] = MAILED__AJAX_ERROR_200;			

		} catch (Exception $e) {

			$res['status'] = $e->getCode();
			$res['message'] = $e->getMessage();

		} finally {

			header('Content-Type: application/json');
			echo json_encode($res);
			die();

		}

	}

	/*
		Render Mailed option main page 
	*/
	public static function render_view_list(){

		$page_info = get_current_screen();
		$current_page = 'view.'.$page_info->parent_file.'.php';

		if(file_exists(MAILED__PLUGIN_DIR . $current_page))
			require_once MAILED__PLUGIN_DIR . $current_page;

	}

	/*
		Get data to export
	*/
	public static function get_export_data($id = null){

		global $wpdb;

		$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

		$field_id = MAILED__FIELD_ID;
		$field_email = MAILED__FIELD_EMAIL;
		$field_firstname = MAILED__FIELD_FIRSTNAME;
		$field_lastname = MAILED__FIELD_LASTNAME;
		$field_created_at = MAILED__FIELD_CREATED_AT;

		$sql = "SELECT $field_email, $field_firstname, $field_lastname, $field_created_at FROM $table_name";

		if(!is_null($id)){

			if(is_array($id)){
				$sql .= " WHERE $field_id IN (". implode(',', $id) . ")";
			}else{
				$sql .= " WHERE $field_id = $id";
			}

		}

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		foreach($result as &$row){
			$row[$field_created_at] = date('d/m/Y H:i', strtotime($row[$field_created_at]));
		}

		return $result;

	}

	/*
		Action: Export all data to CSV
	*/
	public static function action_export_csv(){

		self::export_to_csv(self::get_export_data());

	}

	/*
		Action: Export all data to Excel
	*/
	public static function action_export_excel(){

		self::export_to_excel(self::get_export_data());

	}

	/*
		Action: Export selected data to CSV
	*/
	public static function action_export_selected_csv(){

		$data = !empty($_POST['mailed']) ? $_POST['mailed'] : null;

		if(!is_null($data)){

			array_map("intval", $data);

			$result = self::get_export_data($data);

			// Add header
			array_unshift($result, array('Email', 'Nome', 'Sobrenome', 'Criado em'));

			self::export_to_csv($result);
			
		}		

	}

	/*
		Action: Export selected data to Excel
	*/
	public static function action_export_selected_excel(){

		$data = !empty($_POST['mailed']) ? $_POST['mailed'] : null;

		if(!is_null($data)){

			array_map("intval", $data);

			$result = self::get_export_data($data);

			// Add header
			array_unshift($result, array('Email', 'Nome', 'Sobrenome', 'Criado em'));

			self::export_to_excel($result);

		}		

	}

	/*
		Action: Sync selected data with MailChimp
	*/
	public static function action_sync_selected_mailchimp(){

		if(MailedMailchimp::is_mailchimp_autosubscribe_on()){

			$data = !empty($_POST['mailed']) ? $_POST['mailed'] : null;

			if(!is_null($data)){

				array_map("intval", $data);

				$result = self::get_export_data($data);

				MailedMailchimp::mailchimp_add_multiple($result);

			}

		}

	}

	/*
		Create Mailed table
	*/
	public static function create_table(){

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

		$field_id = MAILED__TABLE_NAME . '_id';
		$field_email = MAILED__FIELD_EMAIL;
		$field_firstname = MAILED__FIELD_FIRSTNAME;
		$field_lastname = MAILED__FIELD_LASTNAME;
		$field_created_at = MAILED__FIELD_CREATED_AT;

		$sql = "CREATE TABLE $table_name ( 
		  `$field_id` INT(20) NOT NULL AUTO_INCREMENT ,
		  `$field_email` VARCHAR(255) NOT NULL ,
		  `$field_firstname` VARCHAR(255) NOT NULL ,
		  `$field_lastname` VARCHAR(255) NULL ,
		  `$field_created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
		  PRIMARY KEY (`$field_id`)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}

	/*
		Drop Mailed Table
	*/
	public static function drop_table(){

		global $wpdb;
		
		$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

		$sql = "DROP TABLE IF EXISTS $table_name;";

		$wpdb->query($sql);

	}

	/*
		Export data to CSV
	*/
	public static function export_to_csv($data = array(), $delimiter = ';'){

		$out = fopen('php://memory', 'w');

		foreach($data as $row){
			fputcsv($out, $row, $delimiter);
		}

    // reset the file pointer to the start of the file
    fseek($out, 0);
    // tell the browser it's going to be a csv file
    header('Content-Type: text/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="mailed-'. date('YmdHi') .'.csv";');
    // make php send the generated csv lines to the browser
    fpassthru($out);
    die();

	}

	/*
		Export data to Excel
	*/
	public static function export_to_excel($data = array()){

		require_once( MAILED__DEPENDENCY_PHPEXCEL );

		$field_email = MAILED__FIELD_EMAIL;
		$field_firstname = MAILED__FIELD_FIRSTNAME;
		$field_lastname = MAILED__FIELD_LASTNAME;
		$field_created_at = MAILED__FIELD_CREATED_AT;

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Mailed Newsletter")
								 ->setDescription("Exported newsletter list from Wordpress")
								 ->setKeywords("office 2007 openxml wordpress mailed");

		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('A1', $data[0][0])
		            ->setCellValue('B1', $data[0][1])
		            ->setCellValue('C1', $data[0][2])
		            ->setCellValue('D1', $data[0][3]);

		// Current row
		$i = 1;

		// Remove first index from data array
		array_shift($data);

		foreach($data as $row){

			$i++;

			$objPHPExcel->setActiveSheetIndex(0)
			            ->setCellValue('A'. $i, $row[$field_email])
			            ->setCellValue('B'. $i, $row[$field_firstname])
			            ->setCellValue('C'. $i, $row[$field_lastname])
			            ->setCellValue('D'. $i, $row[$field_created_at]);

		}

		$objPHPExcel->getActiveSheet()->setTitle('Mailed');

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="mailed-'. date('YmdHi') .'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;

	}

}