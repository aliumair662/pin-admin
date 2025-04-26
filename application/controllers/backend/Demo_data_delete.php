<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Deeplink_generators Controller
 */

class Demo_data_delete extends BE_Controller {
	
	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'DEMO_DATA_DELETE' );
		///start allow module check 
		$conds_mod['module_name'] = $this->router->fetch_class();
		
		$module_id = $this->Module->get_one_by($conds_mod)->module_id;
		
		$logged_in_user = $this->ps_auth->get_user_info();

		$user_id = $logged_in_user->user_id;
		if(empty($this->User->has_permission( $module_id,$user_id )) && $logged_in_user->user_is_sys_admin!=1){
			return redirect( site_url('/admin/') );
		}

		///end check
	}

	/**
	 * Load About Entry Form
	 */

	function index( ) {

		$conds1['group_id'] = 1;
		$conds2['group_id'] = 2;
		$conds3['group_id'] = 3;
		$this->data['entries'] = $this->Module->get_all_by($conds1);
		$this->data['approvals'] = $this->Module->get_all_by($conds2);
		$this->data['reports'] = $this->Module->get_all_by($conds3);

		$tables = ['core_users', 'core_images', 'bs_accept_offer', 'bs_amenities', 'bs_app_purchase', 'bs_blocks', 'bs_chat_history', 'bs_color', 'bs_contact', 'bs_delete_history', 'bs_favourite', 'bs_feeds', 'bs_follows','bs_items', 'bs_items_currency', 'bs_items_posted_by', 'bs_items_price', 'bs_items_property_by', 'bs_items_report', 'bs_item_amenities', 'bs_item_location_cities', 'bs_item_location_townships', 'bs_offline_payment', 'bs_order_by', 'bs_package_bought_transactions', 'bs_packages', 'bs_paid_items_history', 'bs_push_notification_messages', 'bs_push_notification_tokens', 'bs_push_notification_users', 'bs_ratings', 'bs_reported_item_status', 'bs_status', 'bs_propertyby_subscribes', 'bs_touches', 'bs_user_bought'];
		$available = 0;
		foreach($tables as $value){

			if($value == 'core_images')	{
				unset($conds);
				$conds['all_img_del']= 1;
				$conds['img_types']= ['about', 'backend-logo', 'fav-icon', 'login-image'];

				if($this->Image->count_all_by($conds)> 0){
					$available = 1; break;
				}
			}else if($value == 'core_users' ){
				// delete all user expect admin user and reset admin data
				$conds['user_is_sys_admin'] = 0;
				if($this->User->count_all_record_by($conds, $value)>0) {
					$available = 1; break;
				}
			}else{
				if($this->User->count_all_record($value) > 0){
					$available = 1; break;
				}
			}
		}

		$this->data['available'] = $available;

		if ( $this->is_POST()) {
		// if the method is post

			// prepare password
			if ( $this->has_data( 'password' )) {
				$data['password'] = $this->get_data( 'password' );
			}

			// save user info
			$this->deleteDemoData( );
			
		}

		$this->load_form($this->data);

	}

	function delete_demo_data() {
		$tables = ['core_users', 'core_images', 'bs_accept_offer', 'bs_amenities', 'bs_app_purchase', 'bs_blocks', 'bs_chat_history', 'bs_color', 'bs_contact', 'bs_delete_history', 'bs_favourite', 'bs_feeds', 'bs_follows','bs_items', 'bs_items_currency', 'bs_items_posted_by', 'bs_items_price', 'bs_items_property_by', 'bs_items_report', 'bs_item_amenities', 'bs_item_location_cities', 'bs_item_location_townships', 'bs_offline_payment', 'bs_order_by', 'bs_package_bought_transactions', 'bs_packages', 'bs_paid_items_history', 'bs_push_notification_messages', 'bs_push_notification_tokens', 'bs_push_notification_users', 'bs_ratings', 'bs_reported_item_status', 'bs_status', 'bs_propertyby_subscribes', 'bs_touches', 'bs_user_bought'];
		foreach($tables as $value){

			if($value == 'core_images')	{
				unset($conds);
				$conds['all_img_del']= 1;
				$conds['img_types']= ['about', 'backend-logo', 'fav-icon', 'login-image'];

				$this->delete_images_by($conds);
			}else if($value == 'core_users' ){
				// delete all user expect admin user and reset admin data
				$conds['user_is_sys_admin'] = 0;
				$this->User->delete_table_by($conds, $value);

				// reset rating, blue mark, follower, following, remaining_post
				$conds1['status'] = 1;
				$conds1['is_banned'] = 0;
				$conds1['overall_rating'] = 0;
				$conds1['follower_count'] = 0;
				$conds1['following_count'] = 0;
				$conds1['is_verify_blue_mark'] = 0;
				$conds1['blue_mark_note'] = '';
				$conds1['remaining_post'] = 0;
				
				$conds2['user_is_sys_admin'] = 1;
				$id = $this->User->get_one_by($conds2)->user_id;				
				$this->User->save($conds1, $id);
			}else{
				$this->User->delete_table($value);
			}
		}

		$this->set_flash_msg( 'success', get_msg( 'success_demo_data_delete' ));
		redirect( $this->module_site_url());
	}


	/**
	 * Check admin password validation
	 */
	function ajx_valid()
	{
		// get user password
		$password = $_REQUEST['password'];

		$conds['user_is_sys_admin'] = 1;
		$admin_pwd = $this->User->get_one_by($conds)->user_password;

		if ( md5($password) == $admin_pwd) {
			// if the password is valid,
			echo "true";

		} else {
			// if invalid password,
			echo "false";
		}
	}

}