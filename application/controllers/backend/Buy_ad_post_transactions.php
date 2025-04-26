<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Transactions Controller
 */
class Buy_ad_post_transactions extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'BUY_AD_POST_TRANSACTIONS' );
		///start allow module check 
		$conds_mod['module_name'] = $this->router->fetch_class();
		$module_id = $this->Module->get_one_by($conds_mod)->module_id;
		
		$logged_in_user = $this->ps_auth->get_user_info();

		$user_id = $logged_in_user->user_id;
		if(empty($this->User->has_permission( $module_id,$user_id )) && $logged_in_user->user_is_sys_admin!=1){
			return redirect( site_url('/admin') );
		}
		///end check

	}

	/**
	 * List down the registered users
	 */
	function index() {
		$conds['status'] = 1;
		// get rows count
		$this->data['rows_count'] = $this->Package_bought->count_all_by( $conds );

		// get transactions
		$this->data['transactions'] = $this->Package_bought->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'adpost_history_search' );

		

		if($this->input->post('submit') != NULL ){
			// condition with search term
			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				$this->session->set_userdata(array("searchterm" => NULL));
			}

			if($this->input->post('date') != "") {
				$conds['date'] = $this->input->post('date');
				$this->data['date'] = $this->input->post('date');
				$this->session->set_userdata(array("date" => $this->input->post('date')));
			} else {
				$this->session->set_userdata(array("date" => NULL ));
			}
		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('date') != NULL){
				$conds['date'] = $this->session->userdata('date');
				$this->data['date'] = $this->session->userdata('date');
			}
		}
        
		// pagination
		$this->data['rows_count'] = $this->Package_bought->count_all_by( $conds );

		// search data
		$this->data['transactions'] = $this->Package_bought->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Update the existing one
	 */
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'adpost_history_view' );

		// load user
		$this->data['trans'] = $this->Package_bought->get_one( $id );
		// load item
		$package_id = $this->Package_bought->get_one( $id )->package_id;
		$user_id = $this->Package_bought->get_one( $id )->user_id;
		$this->data['package'] = $this->Package->get_one( $package_id );
		$this->data['user'] = $this->User->get_one( $user_id );

		// call the parent edit logic
		parent::edit( $id );
	}
	
	/**
	 * Export csv file
	 */
	function export_csv(){

		// filename
		$filename = 'ad_post_transactions_'. date('Y_m_d') . '.csv';
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/csv;");

		// get user data
		$conds['status'] = 1;
		$data = $this->Package_bought->get_all_by($conds)->result_array();
		
		if(count($data) != 0){
			// file creation 
			$file = fopen('php://output', 'w');

			$header = array(get_msg('no'), get_msg('user_name'), get_msg('package_name'), get_msg('amount_label'), get_msg('payment_method_label'), get_msg('date_label'), get_msg('status_label'));
			fputcsv($file, $header);
			$i=1;
			foreach($data as $key=>$value){
				$line = array($i++, $this->User->get_one($value['user_id'])->user_name, $this->Package->get_one($value['package_id'])->title, $value['price'] . $this->Currency->get_one($this->Package->get_one($value['package_id'])->currency_id)->currency_symbol, $value['payment_method'], $value['added_date']);
				fputcsv($file, $line);
			}

			fclose($file);exit;
		}else{
			$this->set_flash_msg( 'error', get_msg( 'no_transaction_yet' ));
		}

		redirect($this->module_site_url());
		
	}
}

?>