<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Downloads Controller
 */
class Ratings extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'RATINGS' );
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
		
		// no publish filter
		$conds['no_publish_filter'] = 1;
		$conds['order_by'] = 1;
		$conds['order_by_field'] = "overall_rating";
		$conds['order_by_type'] = "desc";

		$this->data['ratings'] = $this->User->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		$this->data['rows_count'] = $this->User->count_all_by( $conds );

		// load index logic
		parent::index();
	}

	/**
 	* Show detail user
	*/
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'rating_view' );

		// load user
		$this->data['rating'] = $this->User->get_one( $id );
		
		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'rating_search' );

		// condition with search term
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {	
				$this->session->set_userdata(array("searchterm" => NULL));
			}

		} else {

			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

		}

		$conds['status'] = "1";
		
		// pagination
		$this->data['rows_count'] = $this->User->count_all_by( $conds );

		// search data
		$this->data['ratings'] = $this->User->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}
	
	/**
	 * Export csv file
	 */
	function export_csv(){

		// no publish filter
		$conds['no_publish_filter'] = 1;
		$conds['order_by'] = 1;
		$conds['order_by_field'] = "overall_rating";
		$conds['order_by_type'] = "desc";

		// filename
		$filename = 'most_rating_users_'. date('Y_m_d') . '.csv';
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=$filename");
		header("Content-Type: application/csv;");

		// get user data 
		$data = $this->User->get_all_by( $conds )->result_array();

		// file creation 
		$file = fopen('php://output', 'w');

		$header = array(get_msg('user_name'), get_msg('overall_rating'), get_msg('added_date'), get_msg('verify_agent'));
		fputcsv($file, $header);

		foreach($data as $key=>$value){
			$line = array($value['user_name'],  $value['overall_rating'], $value['added_date'], $value['apply_to']);
			fputcsv($file, $line);
		}

		fclose($file);exit;
		redirect(site_url('admin/ratings/'));
	}
}