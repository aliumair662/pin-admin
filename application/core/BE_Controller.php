<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Backend Controller which extends PS main Controller
 * 1) Loading Template
 */
class BE_Controller extends PS_Controller {

	/**
	 * constructs required variables
	 * 1) template path
	 * 2) base url
	 * 3) site url
	 *
	 * @param      <type>  $auth_level   The auth level
	 * @param      <type>  $module_name  The module name
	 */
	function __construct( $auth_level, $module_name )
	{
		parent::__construct( $auth_level, $module_name );

		// template path
		$this->template_path = $this->config->item( 'be_view_path' );

		// base url & site url
		$be_url = $this->config->item( 'be_url' );

		if ( !empty( $be_url )) {
		// if fe controller path is not empty,
			
			$this->module_url = $be_url .'/'. $this->module_url;
		}

		// load meta data
		$this->load_metadata();

		// get paignation config
		$this->pag = $this->config->item('pagination');
	}

	/**
	 * Loads a template.
	 *
	 * @param      <type>  $view   The view
	 */
	function load_template( $view = false, $data = false ) 
	{
		// load header
		$this->load_view( 'partials/header' );

		// load view
		if ( !empty( $view )){
			$this->load_view( 'partials/structure', array( 'view' => $view, 'data' => $data ));
		}

		// load footer
		$this->load_view( 'partials/footer' );
	}

	/**
	 * Index
	 */
	function index()
	{
		$this->list_view( $this->module_site_url( 'index' ));
	}


	/**
	 * Index
	 */
	function string_index($language_id=false)
	{
		$this->language_list_view( $this->module_site_url( 'index' ),$language_id);
	}

	/**
	 * Search
	 */
	function string_search($language_id)
	{
		$this->language_list_view( $this->module_site_url( 'search' ),$language_id);	
	}

	/**
	 * Search
	 */
	function search()
	{
		$this->list_view( $this->module_site_url( 'search' ));	
	}

	/**
	 * List View
	 *
	 * @param      <type>  $base_url  The base url
	 */
	function list_view( $base_url )
	{
		// pagination
		$rows_count = $this->data['rows_count'];
		$this->load_pag( $base_url, $rows_count );

		// load add list
		$this->load_list( $this->data );
	}

	/**
	 * List View
	 *
	 * @param      <type>  $base_url  The base url
	 */
	function language_list_view( $base_url , $language_id=false )
	{
		// pagination
		$rows_count = $this->data['rows_count'];
		$this->load_pag( $base_url, $rows_count );
		
		$this->data['language_id'] = $language_id;

		// load add list
		$this->load_list_language( $this->data );
	}

	/**
	 * Add a new record
	 */
	function add()
	{
		// check access
		$this->check_access( ADD );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input()) {

				// save user info
				$this->save();
			}
		}

		// load entry form
		$this->load_form( $this->data );
	}
	
	/**
	 * Add a new record
	 */
	function add_language($language_id = 0)
	{
		// check access
		$this->check_access( ADD );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input()) {

				// save user info
				$this->save(false,$language_id);
			}
		}

		// load entry form
		$this->load_language_form( $this->data );
	}

	/**
	 * Edit the exiting record
	 */
	function edit( $id )
	{
		// check access
		$this->check_access( EDIT );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input( $id )) {

				// save user info
				$this->save( $id );
			}
		}

		// load entry form
		$this->load_form( $this->data );
	}

	/**
	 * Edit the exiting record
	 */
	function string_edit( $id )
	{
		// check access
		$this->check_access( EDIT );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input( $id )) {

				// save user info
				$this->save( $id, $language_id );
			}
		}

		// load entry form
		$this->load_language_form( $this->data );
	}

	/**
	 * Add a new record
	 */
	function paid_add($item_id = 0)
	{
		// check access
		$this->check_access( ADD );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input()) {

				// save user info
				$this->save(false,$item_id);
			}
		}

		// pagination
		$base_url = $this->module_site_url( 'add/'.$item_id );
		$rows_count = $this->data['rows_count'];
		$this->load_pag( $base_url, $rows_count );
		// load entry form
		$this->load_template( 'paid_items/promote',$this->data );
	}

	/**
	 * Edit the exiting record
	 */
	function paid_edit( $id )
	{
		// check access
		$this->check_access( EDIT );

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input( $id )) {

				// save user info
				$this->save_edit( $id );
			}
		}
		// pagination
		$base_url = $this->module_site_url( 'edit' );
		$rows_count = $this->data['rows_count'];
		$this->load_pag( $base_url, $rows_count );
		// load entry form
		$this->load_form( $this->data );
	}

	/**
	 * Delete Cover Photo
	 *
	 * @param      <type>  $img_id  The image identifier
	 */
	function delete_cover_photo( $img_id, $id )
	{
		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		// delete image
		if ( !$this->delete_images_by( array( 'img_id' => $img_id ))) {

			// rollback
			$this->trans_rollback();

			//redirect
			redirect( $this->module_site_url( 'edit/'. $id ));
		}


		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_img_delete' ));
		}

		redirect( $this->module_site_url( 'edit/'. $id ));
	}

	/**
	 * Upload image
	 *
	 * @param      integer  $id  The category identifier
	 */
	function replace_profile_photo( $id )
	{
		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		/**
		 * Delete Images
		 */

		// prepare condition
		$user = $this->User->get_one( $id );
		
		$this->ps_image->delete_images( $user->user_profile_photo );
		
		/**
		 * Insert New Image
		 */
		if ( ! $this->insert_profile_images( $_FILES, $id )) {
		// if error in saving image

			// commit the transaction
			$this->db->trans_rollback();
			
			redirect( $this->module_site_url( ));
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_upload' ));
		}

		redirect( $this->module_site_url( ));
	}

	/**
	 * Upload image
	 *
	 * @param      integer  $id  The category identifier
	 */
	function replace_cover_photo( $img_type, $id )
	{
		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		/**
		 * Delete Images
		 */

		// prepare condition
		$conds = array( 'img_type' => $img_type, 'img_parent_id' => $id );

		if ( !$this->delete_images_by( $conds )) {
		// if error in deleting image, redirect

			// rollback
			$this->db->trans_rollback();

			redirect( $this->module_site_url( 'edit/'. $id ));
		}
		
		/**
		 * Insert New Image
		 */
		if ( ! $this->insert_images( $_FILES, $img_type, $id )) {
		// if error in saving image

			// commit the transaction
			$this->db->trans_rollback();
			
			redirect( $this->module_site_url( 'edit/'. $id ));
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_upload' ));
		}

		redirect( $this->module_site_url( 'edit/'. $id ));
	}

	function insert_profile_images( $files, $user_id )
	{

		// return false if the parent id is empty
		if ( empty( $user_id )) return false;

		// upload images
		//print_r($files); die;
		$upload_data = $this->ps_image->upload( $files );
			
		if ( isset( $upload_data['error'] )) {
		// if there is an error in uploading

			// set error message
			$this->data['error'] = $upload_data['error'];
			
			return;
		}

		// save user
		foreach ( $upload_data as $upload ) {
			$image = array(
				'user_profile_photo'=> $upload['file_name']
			);

			if ( ! $this->User->save( $image,$user_id )) {
			// if error in saving image
				
				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return false;
			}
		}

		return true;
	}

	/**
	 * Insert the image records to image table
	 *
	 * @param      <type>   $upload_data    The upload data
	 * @param      <type>   $img_type       The image type
	 * @param      <type>   $img_parent_id  The image parent identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function insert_images( $files, $img_type, $img_parent_id )
	{
		// return false if the image type is empty
		if ( empty( $img_type )) return false;

		// return false if the parent id is empty
		if ( empty( $img_parent_id )) return false;

		// upload images
		//print_r($files); die;
		$upload_data = $this->ps_image->upload( $files );
			
		if ( isset( $upload_data['error'] )) {
		// if there is an error in uploading

			// set error message
			$this->data['error'] = $upload_data['error'];
			
			return;
		}

		// save image 
		foreach ( $upload_data as $upload ) {
			if($img_type == 'item'){

				$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_of_item;
				
				$img_conds['img_parent_id'] = $img_parent_id;
				$img_conds['img_type'] = 'item';
				$img_count = $this->Image->count_all_by($img_conds);

				if($max_img_upload_count > $img_count){

					if ($upload['image_width'] == "" && $upload['file_ext'] == ".ico") {
						// prepare image data
						$image = array(
							'img_parent_id'=> $img_parent_id,
							'img_type' => $img_type,
							'img_desc' => "",
							'img_path' => $upload['file_name'],
							'img_width'=> "",
							'img_height'=> "",
							'ordering' => 1
						);
					} else {
						// prepare image data
						$image = array(
							'img_parent_id'=> $img_parent_id,
							'img_type' => $img_type,
							'img_desc' => "",
							'img_path' => $upload['file_name'],
							'img_width'=> $upload['image_width'],
							'img_height'=> $upload['image_height'],
							'ordering' => 1
						);
					}

					if ( ! $this->Image->save( $image )) {
					// if error in saving image
						
						// set error message
						$this->data['error'] = get_msg( 'err_model' );
						
						return false;
					}
				}else{
					return false;
				}
			}else{
				if ($upload['image_width'] == "" && $upload['file_ext'] == ".ico") {
					// prepare image data
					$image = array(
						'img_parent_id'=> $img_parent_id,
						'img_type' => $img_type,
						'img_desc' => "",
						'img_path' => $upload['file_name'],
						'img_width'=> "",
						'img_height'=> "",
						'ordering' => 1
					);
				} else {
					// prepare image data
					$image = array(
						'img_parent_id'=> $img_parent_id,
						'img_type' => $img_type,
						'img_desc' => "",
						'img_path' => $upload['file_name'],
						'img_width'=> $upload['image_width'],
						'img_height'=> $upload['image_height'],
						'ordering' => 1
					);
				}

				if ( ! $this->Image->save( $image )) {
				// if error in saving image
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}
			}
		}

		return true;
	}

	function insert_images_icon_and_cover( $files, $img_type, $img_parent_id, $type )
	{
		
		// return false if the image type is empty
		if ( empty( $img_type )) return false;

		// return false if the parent id is empty
		if ( empty( $img_parent_id )) return false;

		
		if($type == "cover") {
			if($img_type == 'item'){
				$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_of_item;
			
				$conds['img_parent_id'] = $img_parent_id;
				$conds['img_type'] = 'item';
				$img_count = $this->Image->count_all_by($conds);

				if($max_img_upload_count > $img_count){
					// upload images
					$upload_data = $this->ps_image->upload_cover( $files );
						
					if ( isset( $upload_data['error'] )) {
					// if there is an error in uploading

						// set error message
						$this->data['error'] = $upload_data['error'];
						
						return;
					}
					$image = array(
						'img_parent_id'=> $img_parent_id,
						'img_type' => $img_type,
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> $upload_data[0]['image_width'],
						'img_height'=> $upload_data[0]['image_height']
					);
					if ( ! $this->Image->save( $image )) {
					// if error in saving image
						
						// set error message
						$this->data['error'] = get_msg( 'err_model' );
						
						return false;
					}
				}else{
					return false;
				}
			}else{
				// upload images
				$upload_data = $this->ps_image->upload_cover( $files );
						
				if ( isset( $upload_data['error'] )) {
				// if there is an error in uploading

					// set error message
					$this->data['error'] = $upload_data['error'];
					
					return;
				}
				$image = array(
					'img_parent_id'=> $img_parent_id,
					'img_type' => $img_type,
					'img_desc' => "",
					'img_path' => $upload_data[0]['file_name'],
					'img_width'=> $upload_data[0]['image_width'],
					'img_height'=> $upload_data[0]['image_height']
				);
				if ( ! $this->Image->save( $image )) {
				// if error in saving image
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}
			}
		} else if($type == "icon") {

			// upload images
			$upload_data = $this->ps_image->upload_icon( $files );
				
			if ( isset( $upload_data['error'] )) {
			// if there is an error in uploading

				// set error message
				$this->data['error'] = $upload_data['error'];
				
				return;
			}
			$image = array(
				'img_parent_id'=> $img_parent_id,
				'img_type' => $img_type,
				'img_desc' => "",
				'img_path' => $upload_data[0]['file_name'],
				'img_width'=> $upload_data[0]['image_width'],
				'img_height'=> $upload_data[0]['image_height']
			);

			
			if ( ! $this->Image->save( $image )) {
			// if error in saving image
				
				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return false;
			}

		}
		
		return true;
	}

	function insert_agent_image( $files, $img_type, $img_parent_id, $type )
	{

		// return false if the image type is empty
		if ( empty( $img_type )) return false;

		// return false if the parent id is empty
		if ( empty( $img_parent_id )) return false;

		
		if($type == "agent") {

			// upload images
			$upload_data = $this->ps_image->upload_agent( $files );
				
			if ( isset( $upload_data['error'] )) {
			// if there is an error in uploading

				// set error message
				$this->data['error'] = $upload_data['error'];
				
				return;
			}
			$image = array(
				'img_parent_id'=> $img_parent_id,
				'img_type' => $img_type,
				'img_desc' => "",
				'img_path' => $upload_data[0]['file_name'],
				'img_width'=> $upload_data[0]['image_width'],
				'img_height'=> $upload_data[0]['image_height']
			);
			if ( ! $this->Image->save( $image )) {
			// if error in saving image
				
				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return false;
			}
		}
		
		return true;
	}

	/**
	 * Delete Image by id and type
	 *
	 * @param      <type>  $conds  The conds
	 */
	function delete_images_by( $conds )
	{
		/**
		 * Delete Images from folder
		 *
		 */
		$images = $this->Image->get_all_by( $conds );

		if ( !empty( $images )) {

			foreach ( $images->result() as $img ) {

				if ( ! $this->ps_image->delete_images( $img->img_path ) ) {
				// if there is an error in deleting images

					$this->set_flash_msg( 'error', get_msg( 'err_del_image' ));
					return false;
				}
			}
		}

		/**
		 * Delete images from database table
		 */
		if ( ! $this->Image->delete_by( $conds )) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
			return false;
		}

		return true;
	}

	function insert_icon_images( $files, $img_type, $img_parent_id, $type )
	{
		
		// return false if the image type is empty
		if ( empty( $img_type )) return false;

		// return false if the parent id is empty
		if ( empty( $img_parent_id )) return false;

		
		if($type == "cover") {
			if($img_type == 'item'){
				$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_of_item;
			
				$conds['img_parent_id'] = $img_parent_id;
				$conds['img_type'] = 'item';
				$img_count = $this->Image->count_all_by($conds);

				if($max_img_upload_count > $img_count){
					// upload images
					$upload_data = $this->ps_image->upload_cover( $files );
						
					if ( isset( $upload_data['error'] )) {
					// if there is an error in uploading

						// set error message
						$this->data['error'] = $upload_data['error'];
						
						return;
					}
					$image = array(
						'img_parent_id'=> $img_parent_id,
						'img_type' => $img_type,
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> $upload_data[0]['image_width'],
						'img_height'=> $upload_data[0]['image_height']
					);
					if ( ! $this->Image->save( $image )) {
					// if error in saving image
						
						// set error message
						$this->data['error'] = get_msg( 'err_model' );
						
						return false;
					}
				}else{
					return false;
				}
			}else{
				// upload images
				$upload_data = $this->ps_image->upload_cover( $files );
						
				if ( isset( $upload_data['error'] )) {
				// if there is an error in uploading

					// set error message
					$this->data['error'] = $upload_data['error'];
					
					return;
				}
				$image = array(
					'img_parent_id'=> $img_parent_id,
					'img_type' => $img_type,
					'img_desc' => "",
					'img_path' => $upload_data[0]['file_name'],
					'img_width'=> $upload_data[0]['image_width'],
					'img_height'=> $upload_data[0]['image_height']
				);
				if ( ! $this->Image->save( $image )) {
				// if error in saving image
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}
			}
		} else if($type == "icon") {

			// upload images
			$upload_data = $this->ps_image->upload_icon( $files );
				
			if ( isset( $upload_data['error'] )) {
			// if there is an error in uploading

				// set error message
				$this->data['error'] = $upload_data['error'];
				
				return;
			}
			$image = array(
				'img_parent_id'=> $img_parent_id,
				'img_type' => $img_type,
				'img_desc' => "",
				'img_path' => $upload_data[0]['file_name'],
				'img_width'=> $upload_data[0]['image_width'],
				'img_height'=> $upload_data[0]['image_height']
			);

			
			if ( ! $this->Image->save( $image )) {
			// if error in saving image
				
				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return false;
			}

		}
		
		return true;
	}

    /**
	 * Delete Cover Photo
	 *
	 * @param      <type>  $img_id  The image identifier
	 */
	function delete_profile_photo( $id )
	{
		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		// delete image
		$user = $this->User->get_one( $id );
		
		$this->ps_image->delete_images( $user->user_profile_photo );

		$image = array(
			'user_profile_photo'=> ""
		);

		$this->User->save( $image,$id );

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_img_delete' ));
		}

		redirect( $this->module_site_url( ));
	}
	 /**
	 * Insert the image records to image table
	 *
	 * @param      <type>   $upload_data    The upload data
	 * @param      <type>   $img_type       The image type
	 * @param      <type>   $img_parent_id  The image parent identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function insert_video_and_img( $files, $img_type, $img_parent_id, $type )
	{

		//print_r($img_type);die;
		//print_r($img_parent_id);

		// return false if the image type is empty
		if ( empty( $img_type )) return false;

		// return false if the parent id is empty
		if ( empty( $img_parent_id )) return false;

		// upload images

		if ($img_type == "video") {
			// File upload configuration
	        $config_video['upload_path'] = $this->config->item('upload_path');
	        $config_video['allowed_types'] = $this->config->item('video_type');
			$config_video['max_size'] = $this->config->item('max_size');
			$config_video['overwrite'] = FALSE;
			$config_video['remove_spaces'] = TRUE;
			//$this->load->library('upload', $config_video);
			//$this->upload->initialize($config_video);

			$upload_data = $this->ps_image->upload_video( $files );

			if ( isset( $upload_data['error'] )) {
			// if there is an error in uploading

				// set error message
				$this->data['error'] = $upload_data['error'];
				
				return;
			}

			// save video 
			foreach ( $upload_data as $upload ) {
				
				// prepare video data
				$video = array(
					'img_parent_id'=> $img_parent_id,
					'img_type' => $img_type,
					'img_desc' => "",
					'img_path' => $upload['file_name'],
					'img_width'=> "",
					'img_height'=> ""
				);
				
				if ( ! $this->Image->save( $video )) {
				// if error in saving video
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}

			}


		} else if ($img_type == "video-icon") {
			
			// File upload configuration
	        $config['upload_path'] = $this->config->item('upload_path');
	        $config['allowed_types'] = $this->config->item('image_type');

	        //$this->load->library('upload', $config);
			//$this->upload->initialize($config);

			$upload_data = $this->ps_image->upload_icon( $files );
		
			if ( isset( $upload_data['error'] )) {
			// if there is an error in uploading

				// set error message
				$this->data['error'] = $upload_data['error'];
				
				return;
			}

			// save video 
			foreach ( $upload_data as $upload ) {
				
				// prepare video data
				$video_icon = array(
					'img_parent_id'=> $img_parent_id,
					'img_type' => $img_type,
					'img_desc' => "",
					'img_path' => $upload['file_name'],
					'img_width'=> $upload['image_width'],
					'img_height'=> $upload['image_height']
				);

				if ( ! $this->Image->save( $video_icon )) {
				// if error in saving video
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}

			}

		} else if($type == "cover") {
			if($img_type == 'item'){
				$max_img_upload_count = $this->App_setting->get_one('app1')->max_img_upload_of_item;
			
				$conds['img_parent_id'] = $img_parent_id;
				$conds['img_type'] = 'item';
				$img_count = $this->Image->count_all_by($conds);
	
				if($max_img_upload_count > $img_count){
					// upload images
					$upload_data = $this->ps_image->upload_cover( $files );
						
					if ( isset( $upload_data['error'] )) {
					// if there is an error in uploading

						// set error message
						$this->data['error'] = $upload_data['error'];
						
						return;
					}
					$image = array(
						'img_parent_id'=> $img_parent_id,
						'img_type' => $img_type,
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> $upload_data[0]['image_width'],
						'img_height'=> $upload_data[0]['image_height']
					);
					if ( ! $this->Image->save( $image )) {
					// if error in saving image
						
						// set error message
						$this->data['error'] = get_msg( 'err_model' );
						
						return false;
					}
				}else{
					return false;
				}
			}else{
				// upload images
				$upload_data = $this->ps_image->upload_cover( $files );
						
				if ( isset( $upload_data['error'] )) {
				// if there is an error in uploading

					// set error message
					$this->data['error'] = $upload_data['error'];
					
					return;
				}
				$image = array(
					'img_parent_id'=> $img_parent_id,
					'img_type' => $img_type,
					'img_desc' => "",
					'img_path' => $upload_data[0]['file_name'],
					'img_width'=> $upload_data[0]['image_width'],
					'img_height'=> $upload_data[0]['image_height']
				);
				if ( ! $this->Image->save( $image )) {
				// if error in saving image
					
					// set error message
					$this->data['error'] = get_msg( 'err_model' );
					
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * replace video
	 *
	 * @param      integer  $id  The category identifier
	 */
	function replace_video_upload( $img_type, $id )
	{

		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		/**
		 * Delete Images
		 */

		// prepare condition
		$conds = array( 'img_type' => $img_type, 'img_parent_id' => $id );

		if ( !$this->delete_videos_by( $conds )) {
		// if error in deleting image, redirect

			// rollback
			$this->db->trans_rollback();

			redirect( $this->module_site_url( 'edit/'. $id ));
		}
		
		/**
		 * Insert New Image
		 */
		if ( ! $this->insert_video_and_img( $_FILES, $img_type, $id, "video" )) {

		// if error in saving image

			// commit the transaction
			$this->db->trans_rollback();
			
			redirect( $this->module_site_url( 'edit/'. $id ));
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_video_upload' ));
		}

		redirect( $this->module_site_url( 'edit/'. $id ));
	}

	/**
	 * Delete Video by id and type
	 *
	 * @param      <type>  $conds  The conds
	 */
	function delete_videos_by( $conds )
	{
		/**
		 * Delete Video from folder
		 *
		 */
	
		$videos = $this->Image->get_all_by( $conds );
	
		if ( !empty( $videos )) {

			foreach ( $videos->result() as $vid ) {
				
				if ( ! $this->ps_image->delete_images( $vid->img_path ) ) {
				// if there is an error in deleting images

					$this->set_flash_msg( 'error', get_msg( 'err_del_image' ));
					return false;
				}
			}
		}

		/**
		 * Delete images from database table
		 */
		if ( ! $this->Image->delete_by( $conds )) {

			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
			return false;
		}

		return true;
	}

	/**
	 * Delete Video
	 *
	 * @param      <type>  $img_id  The image identifier
	 */
	function delete_video( $img_id, $id )
	{
		// check edit access
		$this->check_access( EDIT );

		// start the db transaction
		$this->db->trans_start();

		// delete image
		if ( !$this->delete_videos_by( array( 'img_id' => $img_id ))) {

			// rollback
			$this->trans_rollback();

			//redirect
			redirect( $this->module_site_url( 'edit/'. $id ));
		}


		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			$this->set_flash_msg( 'success', get_msg( 'success_video_delete' ));
		}

		redirect( $this->module_site_url( 'edit/'. $id ));
	}
}