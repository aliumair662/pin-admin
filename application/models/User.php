<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Model for core_USERS table
 */
class User extends PS_Model {

	// table name for module
	protected $module_table_name;

	// table name for permission
	protected $permission_table_name;

	// table name for role access
	protected $role_access_table_name;

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'core_users', 'user_id', 'usr' );

		// initialize table names
		$this->module_table_name = "core_modules";
		$this->permission_table_name = "core_permissions";
		$this->role_access_table_name = "core_role_access";
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
    


	function custom_conds( $conds = array())
	{
		// default where clause
		
		// check block user
		if( isset($conds['from_block_user_id'])){
			$block_conds['from_block_user_id']   = $conds['from_block_user_id'];
			$blocked_users = $this->Block->get_all_by($block_conds);
			
			foreach($blocked_users->result() as $blocked_user){
				$this->db->where('user_id !=', $blocked_user->to_block_user_id);
			}
			
			$this->db->where('user_id !=', $conds['from_block_user_id']);
		}

		// order by
		if ( isset( $conds['order_by'] )) {
			$order_by_field = $conds['order_by_field'];
			$order_by_type = $conds['order_by_type'];
			
			$this->db->order_by( 'core_users.'.$order_by_field, $order_by_type);
		}

		// user_id condition
		if ( isset( $conds['user_id'] )) {
			$this->db->where( $this->primary_key, $conds['user_id'] );
		}


		// system_role_id condition
		if ( isset( $conds['system_role_id'] )) {
			$this->db->where( 'role_id !=', $conds['system_role_id'] );
		}

		// normal_role_id condition
		if ( isset( $conds['register_role_id'] )) {
			$this->db->where( 'role_id', $conds['register_role_id'] );
		}

		// user show_email condition
		if ( isset($conds['is_show_email'] )) {
			$this->db->where( 'is_show_email' , $conds['is_show_email'] );
		}

		// user show_phone condition
		if ( isset($conds['is_show_phone'] )) {
			$this->db->where( 'is_show_phone' , $conds['is_show_phone'] );
		}

		// facebook_id condition
		if ( isset( $conds['facebook_id'] )) {
			$this->db->where( 'facebook_id', $conds['facebook_id'] );
		}

		// google_id condition
		if ( isset( $conds['google_id'] )) {
			$this->db->where( 'google_id', $conds['google_id'] );
		}

		// phone_id condition
		if ( isset( $conds['phone_id'] )) {
			$this->db->where( 'phone_id', $conds['phone_id'] );
		}

		// apple_id condition
		if ( isset( $conds['apple_id'] )) {
			$this->db->where( 'apple_id', $conds['apple_id'] );
		}

		// user_email condition
		if ( isset( $conds['user_email'] )) {
			$this->db->where( 'user_email', $conds['user_email'] );
		}

		// user_phone condition
		if ( isset( $conds['user_phone'] )) {
			$this->db->where( 'user_phone', $conds['user_phone'] );
		}

		// user_name condition
		if ( isset( $conds['user_name'] )) {
			$this->db->where( 'user_name', $conds['user_name'] );
		}

		// keyword condition
		if ( isset( $conds['keyword'] )) {
			$this->db->like( 'user_name', $conds['keyword'] );
		}

		// user_pass condition
		if ( isset( $conds['user_password'] )) {
			$this->db->where( 'user_password', md5( $conds['user_password'] ));
		}

		// overall_rating condition
		if ( isset( $conds['overall_rating'] )) {
			$this->db->where( 'overall_rating',$conds['overall_rating'] );
		}

        // searchterm
        if ( isset( $conds['searchterm'] )) {
            $this->db->group_start();
            $this->db->like( 'user_name', $conds['searchterm'] );
            $this->db->or_like( 'user_email', $conds['searchterm'] );
            $this->db->group_end();
        }
		
		// user_is_sys_admin condition
		if ( isset( $conds['user_is_sys_admin'] )) {
			$this->db->where( 'user_is_sys_admin', $conds['user_is_sys_admin'] );
		}

		// is_banned condition
		if ( isset( $conds['is_banned'] )) {
			$this->db->where( 'is_banned', $conds['is_banned'] );
		}

		// role_id condition
		if ( isset( $conds['role_id'] )) {
			$this->db->where( 'role_id', $conds['role_id'] );
		}

		// code condition
		if ( isset( $conds['code'] )) {
			$this->db->where( 'code', $conds['code'] );
		}

		// email_verify condition
		if ( isset( $conds['email_verify'] )) {
			$this->db->where( 'email_verify', $conds['email_verify'] );
		}

		// facebook_verify condition
		if ( isset( $conds['facebook_verify'] )) {
			$this->db->where( 'facebook_verify', $conds['facebook_verify'] );
		}

		// google_verify condition
		if ( isset( $conds['google_verify'] )) {
			$this->db->where( 'google_verify', $conds['google_verify'] );
		}

		// phone_verify condition
		if ( isset( $conds['phone_verify'] )) {
			$this->db->where( 'phone_verify', $conds['phone_verify'] );
		}

		// apple_verify condition
		if ( isset( $conds['apple_verify'] )) {
			$this->db->where( 'apple_verify', $conds['apple_verify'] );
		}

		// user_profile_photo condition
		if ( isset( $conds['user_profile_photo'] )) {
			$this->db->where( 'user_profile_photo', $conds['user_profile_photo'] );
		}

		// status condition
		if ( isset( $conds['status'] )) {
			$this->db->where( 'status', $conds['status'] );
		} else {
			$this->db->where( 'status', 1 );
		}

        // verify blue mark id condition
        if ( isset( $conds['is_verify_blue_mark'] )) {

            if ($conds['is_verify_blue_mark'] != "") {
                if($conds['is_verify_blue_mark'] != '0'){
                    $this->db->where( 'is_verify_blue_mark', $conds['is_verify_blue_mark'] );
                }

            }
        }

		// user remaining_post condition
		if ( isset($conds['remaining_post'] )) {
			$this->db->where( 'remaining_post' , $conds['remaining_post'] );
		}

		// added_date_timestamp condition
		if ( isset( $conds['added_date_timestamp'] )) {
			$this->db->where( 'added_date_timestamp', $conds['added_date_timestamp'] );
		}

		$this->db->order_by('added_date', 'desc');
	}

	/**
	 * Save function creates/updates the user data to users table.
	 * If the user_id is already exist in the users table,update user data
	 * else, the function will create new data row
	 * 
	 * @param ref array $user_data
	 * @param int $user_id
	 * @return bool
	 */
	function save_user( &$user_data, $permission_data, $user_id = false )
	{
		// start the transaction
		$this->db->trans_start();

		if ( !$user_id && ! $this->exists( array( 'user_email' => $user_data['user_email'] ))) {
		//if there is no data with this id, insert new			
			
			// generate new user id
			$user_id = $this->generate_key( 'USR' );
			$user_data['user_id'] = $user_id;

			if ( ! $this->db->insert( $this->table_name, $user_data )) {
			// if error in inserting new, rollback

				$this->db->trans_rollback();
        		return false;
			}

		} else {
		//else update the data
		
			$this->db->where( 'user_id', $user_id );
			
			if ( ! $this->db->update( $this->table_name, $user_data )) {
			// if error in updating, rollback
				
				$this->db->trans_rollback();
        		return false;
			}
		}
		
		//Clear existing permission & Insert new permission
		if ( ! $this->db->delete( $this->permission_table_name, array( $this->primary_key => $user_id )) ) {
		// if error in cleaing existing permission, rollback	

			$this->db->trans_rollback();
    		return false;
		}
			
		// loop and insert the passed permissions
		foreach ( $permission_data as $module ) {

			// prep module data
			$data = array( 'module_id' => $module, $this->primary_key => $user_id );

			if ( ! $this->db->insert(  $this->permission_table_name, $data )) {
			// if error in inserting permission data, rollback
				
				$this->db->trans_rollback();
        		return false;
			}
		}

		// commit the transaction
		return $this->db->trans_commit();
	}

	function multi_user_profiles($user_id=''){

		$user_multi_profiles = new stdClass();

		if(!empty($user_id)){
			$user_multi_profiles=$this->db->order_by('id','asc')->get_where('core_users_profiles_images',array('user_id'=>$user_id))->result();
		}

		return $user_multi_profiles;

	}

	// /**
	//  * Delete function update 1 to deleted fields from users table
	//  * according to the user id
	//  * 
	//  * @param int $user_id
	//  * @return bool
	//  */
	// function delete( $user_id )
	// {
	// 	if ( $user_id == $this->auth->get_user_info()->user_id ) {
	// 	// Don't let user deleted their self
			
	// 		return false;
	// 	}

	// 	// start the transaction
	// 	$this->db->trans_start();
		
	// 	if ( ! $this->db->delete( $this->permission_table_name, array( $this->primary_key => $user_id ))) {
	// 	// if error in deleteing permission, rollback
		
	// 		$this->db->trans_rollback();
	// 		return false;
	// 	}

	// 	$this->db->where( $this->primary_key, $user_id );

	// 	if ( ! $this->db->update( $this->table_name, array( 'status' => 0 ))) {
	// 	// if error in updating user status,

	// 		$this->db->trans_rollback();
	// 		return false;
	// 	}

	// 	// commit the transaction
	// 	return $this->db->trans_commit();
	// }

	/**
	 * Has-permission determine whether the user has access for module
	 * 
	 * @return bool
	 */
	function has_permission( $module_id, $user_id )
	{
		// if no module id is null,allow access
		if ( $module_id == null ) {
			return true;
		}

		// Join Permission Table, Module Table & User Table
		$this->db->from( $this->permission_table_name );
		$this->db->where( $this->primary_key, $user_id );
		$this->db->join( $this->module_table_name, $this->module_table_name .".module_id = ". $this->permission_table_name .".module_id" );
		$this->db->where( $this->module_table_name .'.module_id', $module_id );
		$query = $this->db->get();
		
		return $query->num_rows() == 1;
	}

	/**
	 * has_access determine whether the user has access for action
	 */
	function has_access( $action_id, $role_id)
	{
		//if action is null, allow access
		if ( $action_id == null ) {
			return true;
		}
		
		$this->db->from( $this->role_access_table_name );
		$this->db->where( 'role_id', $role_id );
		$this->db->where( 'action_id', $action_id );
		$query = $this->db->get();
		
		return $query->num_rows() == 1;
	}
}