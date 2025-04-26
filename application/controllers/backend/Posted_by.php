<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Postedbys Controller
 */
class Posted_by extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'POSTED BY' );
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
		// get rows count
		$this->data['rows_count'] = $this->Postedby->count_all_by( $conds );
		
		// get postedby
		$this->data['posts'] = $this->Postedby->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );
		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {
		

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'post_search' );
		
		// condition with search term
		$conds = array( 'searchterm' => $this->searchterm_handler( $this->input->post( 'searchterm' )) );
		// no publish filter
		$conds['no_publish_filter'] = 1;

		// pagination
		$this->data['rows_count'] = $this->Postedby->count_all_by( $conds );

		// search data
		$this->data['posts'] = $this->Postedby->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );
		
		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'post_add' );

		// call the core add logic
		parent::add();
	}

	/**
	 * Update the existing one
	 */
	function edit( $id ) {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'post_edit' );

		// load user
		$this->data['postedby'] = $this->Postedby->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save postby
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */


	function save( $id = false ) {

		   if((!isset($id ))|| (isset($id))) {
			// color count
			if($id) {
				$color_counter_total = $this->get_data( 'color_total' );
				if ($color_counter_total == "" || $color_counter_total== 0) {
					$color_counter_total = $this->get_data( 'color_total_existing' );
				}
				$edit_prd_id = $id;
			} else {
				$color_counter_total = $this->get_data( 'color_total' );
			}
		
			// start the transaction
			$this->db->trans_start();
			$logged_in_user = $this->ps_auth->get_user_info();
			
			/** 
			 * Insert Product Records 
			 */
			$data = array();

			// post id
		   if ( $this->has_data( 'id' )) {
				$data['id'] = $this->get_data( 'id' );

			}

			// post name
			if ( $this->has_data( 'name' )) {
				$data['name'] = $this->get_data( 'name' );
			}
			
			$data['status'] = 1;
			// set timezone
			$data['post_by_user_id'] = $logged_in_user->user_id;

			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
			} else {
				//edit
				unset($data['added_date']);
				$data['updated_date'] = date("Y-m-d H:i:s");
				$data['updated_user_id'] = $logged_in_user->user_id;
			}

			//print_r($data);die;

			//save category
			if ( ! $this->Postedby->save( $data, $id )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return;
			}
			//get inserted post id
			$id = ( !$id )? $data['id']: $id ;

			if($color_counter_total == false) { 
				// edit color
				$color_counter_total = 1;
				$color_code = $this->get_data( 'colorvalue1' );
				if($color_code != "") {
					$color_data['post_id'] = $id;
					$color_data['color_code'] = $color_code;
					$color_data['added_date'] = date("Y-m-d H:i:s");
					$color_data['added_user_id'] = $logged_in_user->user_id;
					
					$this->Color->save($color_data);
				}

			} else { 
				// save color
				$this->ps_delete->delete_color( $id );
				$color_counter_total = $color_counter_total;
				for($i=1; $i<=$color_counter_total; $i++) {

					 $color_code = $this->get_data( 'colorvalue' . $i );
					
					if($color_code != "") {

						$color_data['post_id'] = $id;
						$color_data['color_code'] = $color_code;
						$color_data['added_date'] = date("Y-m-d H:i:s");
						$color_data['added_user_id'] = $logged_in_user->user_id;
						$this->Color->save($color_data);
					}

				}
			}

			
			/** 
			 * Check Transactions 
			 */

			// commit the transaction
			if ( ! $this->check_trans()) {
	        	
				// set flash error message
				$this->set_flash_msg( 'error', get_msg( 'err_model' ));
			} else {

				if ( $id ) {
				// if user id is not false, show success_add message
					
					$this->set_flash_msg( 'success', get_msg( 'success_post_edit' ));
				} else {
				// if user id is false, show success_edit message

					$this->set_flash_msg( 'success', get_msg( 'success_post_add' ));
				}
			}
		}
		
		// redirect to list view
			redirect( $this->module_site_url() );
	}
	

	/**
	 * Delete the record
	 * 1) delete condition
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) {

		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );
		
		// delete categories and images
		if ( !$this->ps_delete->delete_postedby( $id )) {

			// set error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));

			// rollback
			$this->trans_rollback();

			// redirect to list view
			redirect( $this->module_site_url());
		}
			
		/**
		 * Check Transcation Status
		 */
		if ( !$this->check_trans()) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
		} else {
        	
			$this->set_flash_msg( 'success', get_msg( 'success_post_delete' ));
		}
		
		redirect( $this->module_site_url());
	}

	
	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {
		
		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'name', get_msg( 'name' ), $rule);

		if ( $this->form_validation->run() == FALSE ) {
		// if there is an error in validating,

			return false;
		}

		return true;
	}

	/**
	 * Determines if valid name.
	 *
	 * @param      <type>   $name  The  name
	 * @param      integer  $id     The  identifier
	 *
	 * @return     boolean  True if valid name, False otherwise.
	 */
	function is_valid_name( $name, $id = 0 )
	{		

		 $conds['name'] = $name;

			
		 	if( $id != "") {
				if ( strtolower( $this->Postedby->get_one( $id )->name ) == strtolower( $name )) {
				// if the name is existing name for that user id,
					return true;
				} else if( $this->Postedby->exists( ($conds ))) {
				// if the name is existed in the system,
					$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
					return false;
				}
			} else {
				if ( $this->Postedby->exists( ($conds ))) {
				// if the name is existed in the system,
					$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
					return false;
				}
			}
			return true;
	}

	/**
	 * Publish the record
	 *
	 * @param      integer  $post_id  The category identifier
	 */
	function ajx_publish( $id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$post_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Postedby->save( $post_data, $id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  $post_id  The condition identifier
	 */
	function ajx_unpublish( $id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$post_data = array( 'status'=> 0 );
			
		// save data
		if ( $this->Postedby->save( $post_data, $id )) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
}