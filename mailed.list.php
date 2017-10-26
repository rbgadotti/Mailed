<?php

class Mailed_List extends WP_List_Table {

	public $mailed_mailchimp_list_members;

	/*
		Columns list
	*/
	public function get_columns(){

		$columns = array(
			'cb' => '<input type="checkbox" />',
			'mailed_email' => 'Email',
			'mailed_firstname' => 'Nome',
			'mailed_lastname' => 'Sobrenome',
			'mailed_created_at' => 'Criado em',
		);

		if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()){
			$columns['mailed_mailchimp_status'] = 'Mailchimp Status';
		}

		return $columns;

	}

	/*
		Items to show
	*/
	public function get_items($per_page = MAILED__LIST_ITEMS_PER_PAGE, $page_number = 1){

		global $wpdb;

		$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

		$sql = "SELECT * FROM $table_name";

		/*
			Search
		*/
		if ( !empty( $_POST['s'] ) ) {
			$sql .= ' WHERE '. MAILED__FIELD_EMAIL . ' LIKE \'%'. $_POST['s'] . '%\'';
			$sql .= ' OR '. MAILED__FIELD_FIRSTNAME . ' LIKE \'%'. $_POST['s'] . '%\'';
			$sql .= ' OR '. MAILED__FIELD_LASTNAME . ' LIKE \'%'. $_POST['s'] . '%\'';
		}

		if ( ! empty( $_REQUEST['orderby'] ) && $_REQUEST['orderby'] != 'mailed_mailchimp_status') {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		/*
			Mailchimp Lista table integration
		*/
		if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()){

			/*
				Add Mailchimp status column
			*/

			foreach($result as &$row){

				foreach($this->mailed_mailchimp_list_members as $mailchimp_member){

					if($mailchimp_member->email_address == $row['mailed_email']){

						$row['mailchimp_status'] = $mailchimp_member->status;
						break;

					}else{

						$row['mailchimp_status'] = 'unsubscribed';

					}

				}

			}

			/* Order table */

			usort($result, function($a, $b) {
			  return $a['mailchimp_status'] - $b['mailchimp_status'];
			});

			$order = !empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'asc';

			if($order == 'asc'){
				$result = array_reverse($result);
			}

		}

		return $result;

	}

	/*
		Get number of total items
	*/
	public function get_total_items(){

		global $wpdb;

		$table_name = $wpdb->prefix . MAILED__TABLE_NAME;

		$sql = "SELECT COUNT(*) FROM $table_name";

		/*
			Search
		*/
		if ( !empty( $_POST['s'] ) ) {
			$sql .= ' WHERE '. MAILED__FIELD_EMAIL . ' LIKE \'%'. $_POST['s'] . '%\'';
			$sql .= ' OR '. MAILED__FIELD_FIRSTNAME . ' LIKE \'%'. $_POST['s'] . '%\'';
			$sql .= ' OR '. MAILED__FIELD_LASTNAME . ' LIKE \'%'. $_POST['s'] . '%\'';
		}

		$rowCount = $wpdb->get_var($sql);

		return $rowCount;

	}

	/*
		Default column
	*/
	public function column_default( $item, $column_name ) {

		switch( $column_name ){

			case 'mailed_email':
			case 'mailed_firstname':
			case 'mailed_lastname':
			case 'mailed_created_at':
				return $item[ $column_name ];
			default:
				return print_r( $item, true );

		}

	}

	/*
		Main column
	*/
	public function column_mailed_email($item) {

		// $actions = array(
		// 	'edit'      => sprintf('<a href="?page=%s&action=%s&mailed=%s">Edit</a>',$_REQUEST['page'],'edit',$item['mailed_id']),
		// 	'delete'    => sprintf('<a href="?page=%s&action=%s&mailed=%s">Delete</a>',$_REQUEST['page'],'delete',$item['mailed_id']),
		// );

		// return sprintf('%1$s %2$s', $item['mailed_email'], $this->row_actions($actions) );

		return $item['mailed_email'];

	}

	/*
		Cb column
	*/
	public function column_cb($item){

		return sprintf(
			'<input type="checkbox" name="mailed[]" value="%s" />', $item['mailed_id']
		);

	}

	/*
		Created at column
	*/
	public function column_mailed_created_at($item){

		return date('d/m/Y H:i', strtotime($item['mailed_created_at']));

	}


	/*
		Mailchimp List Table integration column: Status
	*/
	public function column_mailed_mailchimp_status($item){

		return !is_null($item['mailchimp_status']) ? '<span class="mailchimp_status__'. strtolower($item['mailchimp_status']) .'">'. $item['mailchimp_status'] .'</span>' : '';

	}

	/*
		Bulk actions
	*/
	public function get_bulk_actions() {

		$actions = array(
			'delete' => 'Delete',
			'export_selected_csv' => 'Exportar selecionados (CSV)',
			'export_selected_excel' => 'Exportar selecionados (Excel)',

		);

		if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()){

			$actions['sync_selected_mailchimp'] = 'Sincronizar com MailChimp';

		}

		return $actions;

	}


	/*
		Sortable column
	*/
	public function get_sortable_columns() {

		$sortable_columns = array(
			'mailed_email'  => array('mailed_email',false),
			'mailed_firstname'  => array('mailed_firstname',false),
			'mailed_lastname'  => array('mailed_lastname',false),
			'mailed_created_at'  => array('mailed_created_at',false)
		);

		if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()){
			$sortable_columns['mailed_mailchimp_status'] = array('mailed_mailchimp_status', false);
		}

		return $sortable_columns;

	}

	/*
		Default item sort
	*/
	public function usort_reorder( $a, $b ) {

		$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'mailed_created_at';
		$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		
		$result = strcmp( $a[$orderby], $b[$orderby] );
		
		return ( $order === 'asc' ) ? $result : -$result;

	}

	/*
		Not found
	*/
	public function no_items() {
	_e( 'Nenhum registro encontrado.' );
	}


	/*
		Prepare items
	*/
	public function prepare_items(){

		// If config to show mailchimp data in table

		if(MailedMailchimp::is_mailchimp_intragrate_list_table_on()){

			$this->mailed_mailchimp_list_members = MailedMailchimp::mailchimp_list_members();

		}

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		// $this->_column_headers = array($columns, $hidden, $sortable);
		$this->_column_headers = $this->get_column_info();

	  $per_page = $this->get_items_per_page('mailed_per_page', MAILED__LIST_ITEMS_PER_PAGE);
	  $current_page = $this->get_pagenum();

		$this->items = $this->get_items($per_page, $current_page);

		$total_items = $this->get_total_items();

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page
		));

	}

}