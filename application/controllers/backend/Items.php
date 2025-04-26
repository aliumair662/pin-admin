<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Items Controller
 */
class Items extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {

		parent::__construct( MODULE_CONTROL, 'ITEMS' );
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
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );

		// get categories
		$this->data['items'] = $this->Item->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );


		// load index logic
		parent::index();
	}

	/**
	 * Searches for the first match.
	 */
	function search() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_search' );

		// condition with search term
		if($this->input->post('submit') != NULL ){

			if($this->input->post('searchterm') != "") {
				$conds['searchterm'] = $this->input->post('searchterm');
				$this->data['searchterm'] = $this->input->post('searchterm');
				$this->session->set_userdata(array("searchterm" => $this->input->post('searchterm')));
			} else {
				
				$this->session->set_userdata(array("searchterm" => NULL));
			}
			

			if($this->input->post('property_by_id') != ""  || $this->input->post('property_by_id') != '0') {
				$conds['property_by_id'] = $this->input->post('property_by_id');
				$this->data['property_by_id'] = $this->input->post('property_by_id');
				
				$this->session->set_userdata(array("property_by_id" => $this->input->post('property_by_id')));
				
			} else {
				$this->session->set_userdata(array("property_by_id" => NULL ));
			}

			if($this->input->post('posted_by_id') != ""  || $this->input->post('posted_by_id') != '0') {
				$conds['posted_by_id'] = $this->input->post('posted_by_id');
				$this->data['posted_by_id'] = $this->input->post('posted_by_id');
				
				$this->session->set_userdata(array("posted_by_id" => $this->input->post('posted_by_id')));
				
			} else {
				$this->session->set_userdata(array("posted_by_id" => NULL ));
			}
			

			if($this->input->post('item_currency_id') != ""  || $this->input->post('item_currency_id') != '0') {
				$conds['item_currency_id'] = $this->input->post('item_currency_id');
				$this->data['item_currency_id'] = $this->input->post('item_currency_id');
				
				$this->session->set_userdata(array("item_currency_id" => $this->input->post('item_currency_id')));
				
			} else {
				$this->session->set_userdata(array("item_currency_id" => NULL ));
			}

			if($this->input->post('item_location_city_id') != ""  || $this->input->post('item_location_city_id') != '0') {
				$conds['item_location_city_id'] = $this->input->post('item_location_city_id');
				$this->data['item_location_city_id'] = $this->input->post('item_location_city_id');
				$this->data['selected_location_city_id'] = $this->input->post('item_location_city_id');
				$this->session->set_userdata(array("item_location_city_id" => $this->input->post('item_location_city_id')));
				$this->session->set_userdata(array("selected_location_city_id" => $this->input->post('item_location_city_id')));
			} else {
				$this->session->set_userdata(array("item_location_city_id" => NULL ));
			}

			if($this->input->post('item_location_township_id') != ""  || $this->input->post('item_location_township_id') != '0') {
				$conds['item_location_township_id'] = $this->input->post('item_location_township_id');
				$this->data['item_location_township_id'] = $this->input->post('item_location_township_id');
				$this->session->set_userdata(array("item_location_township_id" => $this->input->post('item_location_township_id')));
			} else {
				$this->session->set_userdata(array("item_location_township_id" => NULL ));
			}


		} else {
			//read from session value
			if($this->session->userdata('searchterm') != NULL){
				$conds['searchterm'] = $this->session->userdata('searchterm');
				$this->data['searchterm'] = $this->session->userdata('searchterm');
			}

			if($this->session->userdata('item_location_city_id') != NULL){
				$conds['item_location_city_id'] = $this->session->userdata('item_location_city_id');
				$this->data['item_location_city_id'] = $this->session->userdata('item_location_city_id');
				$this->data['selected_location_city_id'] = $this->session->userdata('item_location_city_id');
			}

			if($this->session->userdata('item_location_township_id') != NULL){
				$conds['item_location_township_id'] = $this->session->userdata('item_location_township_id');
				$this->data['item_location_township_id'] = $this->session->userdata('item_location_township_id');
				$this->data['selected_location_city_id'] = $this->session->userdata('item_location_city_id');
			}


			if($this->session->userdata('property_by_id') != NULL){
				$conds['property_by_id'] = $this->session->userdata('property_by_id');
				$this->data['property_by_id'] = $this->session->userdata('property_by_id');
			}

			if($this->session->userdata('item_currency_id') != NULL){
				$conds['item_currency_id'] = $this->session->userdata('item_currency_id');
				$this->data['item_currency_id'] = $this->session->userdata('item_currency_id');
			}

			if($this->session->userdata('posted_by_id') != NULL){
				$conds['posted_by_id'] = $this->session->userdata('posted_by_id');
				$this->data['posted_by_id'] = $this->session->userdata('posted_by_id');
			}
			

		}
		
		$conds['status'] = 1;

		// pagination
		$this->data['rows_count'] = $this->Item->count_all_by( $conds );


		// search data
		$this->data['items'] = $this->Item->get_all_by( $conds, $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load add list
		parent::search();
	}

	/**
	 * Create new one
	 */
	function add() {

		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_add' );

		// call the core add logic
		parent::add();
	}

	/**
	 * Saving Logic
	 * 1) upload image
	 * 2) save category
	 * 3) save image
	 * 4) check transaction status
	 *
	 * @param      boolean  $id  The user identifier
	 */
	function save( $id = false ) {
		
			$logged_in_user = $this->ps_auth->get_user_info();

			// Item id
		   	if ( $this->has_data( 'id' )) {
				$data['id'] = $this->get_data( 'id' );

			}

			// Currency id
		   	if ( $this->has_data( 'item_currency_id' )) {
				$data['item_currency_id'] = $this->get_data( 'item_currency_id' );
			}

			// location city id
		   	if ( $this->has_data( 'item_location_city_id' )) {
				$data['item_location_city_id'] = $this->get_data( 'item_location_city_id' );
			}

			// location id
		   	if ( $this->has_data( 'item_location_township_id' )) {
				$data['item_location_township_id'] = $this->get_data( 'item_location_township_id' );
			}

			// discount rate
			if ( $this->has_data( 'discount_rate_by_percentage' )) {
				$data['discount_rate_by_percentage'] = $this->get_data( 'discount_rate_by_percentage' );
			}
			
			// property_by_id
		   	if ( $this->has_data( 'property_by_id' )) {
				$data['property_by_id'] = $this->get_data( 'property_by_id' );
			}

			// item_price_type_id
		   	if ( $this->has_data( 'item_price_type_id' )) {
				$data['item_price_type_id'] = $this->get_data( 'item_price_type_id' );
			}

			// posted_by_id
		   	if ( $this->has_data( 'posted_by_id' )) {
				$data['posted_by_id'] = $this->get_data( 'posted_by_id' );
			}

			// configuration
		   	if ( $this->has_data( 'configuration' )) {
				$data['configuration'] = $this->get_data( 'configuration' );
			}

			// floor_no
		   	if ( $this->has_data( 'floor_no' )) {
				$data['floor_no'] = $this->get_data( 'floor_no' );
			}

			// price_unit
		   	if ( $this->has_data( 'price_unit' )) {
				$data['price_unit'] = $this->get_data( 'price_unit' );
			}

			// price_note
		   	if ( $this->has_data( 'price_note' )) {
				$data['price_note'] = $this->get_data( 'price_note' );
			}

			// price
		   	if ( $this->has_data( 'price' )) {
				$data['price'] = $this->get_data( 'price' );
			}

			//title
		   	if ( $this->has_data( 'title' )) {
				$data['title'] = $this->get_data( 'title' );
			}

			// description
		   	if ( $this->has_data( 'description' )) {
				$data['description'] = $this->get_data( 'description' );
			}

			// highlight_info
		   	if ( $this->has_data( 'highlight_info' )) {
				$data['highlight_info'] = $this->get_data( 'highlight_info' );
			}
			
			// area
		   	if ( $this->has_data( 'area' )) {
				$data['area'] = $this->get_data( 'area' );
			}

			// address
		   	if ( $this->has_data( 'address' )) {
				$data['address'] = $this->get_data( 'address' );
			}

			// prepare Item lat
			if ( $this->has_data( 'lat' )) {
				$data['lat'] = $this->get_data( 'lat' );
			}

			// prepare Item lng
			if ( $this->has_data( 'lng' )) {
				$data['lng'] = $this->get_data( 'lng' );
			}
			
			// if 'is_negotiable' is checked,
			if ( $this->has_data( 'is_negotiable' )) {
				$data['is_negotiable'] = 1;
			} else {
				$data['is_negotiable'] = 0;
			}

			// if 'is_sold_out' is checked,
			if ( $this->has_data( 'is_sold_out' )) {
				$data['is_sold_out'] = 1;
			} else {
				$data['is_sold_out'] = 0;
			}

			// if 'status' is checked,
			if ( $this->has_data( 'status' )) {
				$data['status'] = 1;
			} else {
				$data['status'] = 0;
			}

			//g_review_place_id
			if ( $this->has_data( 'g_review_place_id' )) {
				$data['g_review_place_id'] = $this->get_data( 'g_review_place_id' );
			}

			//g_review_score
			if ( $this->has_data( 'g_review_score' )) {
				$data['g_review_score'] = $this->get_data( 'g_review_score' );
			}

			//g_review_quantity
			if ( $this->has_data( 'g_review_quantity' )) {
				$data['g_review_quantity'] = $this->get_data( 'g_review_quantity' );
			}

			//g_review_link
			if ( $this->has_data( 'g_review_link' )) {
				$data['g_review_link'] = $this->get_data( 'g_review_link' );
			}

			// if 'repeat_on' is checked,
			if ( $this->has_data( 'repeat_on' )) {
				$data['repeat_on'] = $this->get_data( 'repeat_on' );
			}

			// if 'event_type' is checked,
			if ( $this->has_data( 'event_type' )) {
				$data['event_type'] = $this->get_data( 'event_type' );
			}

			//start_date && stop_date
			if ( $this->has_data( 'event_type' ) && $this->has_data( 'start_date' ) && $this->has_data( 'stop_date' )) {
				
				if ( $this->get_data( 'event_type' ) == 'event' ) {
					$start_date = $this->get_data( 'start_date' );
					$data['start_date'] = date('Y-m-d',strtotime($start_date));
					$stop_date = $this->get_data( 'stop_date' );
					$data['stop_date'] = date('Y-m-d',strtotime($stop_date));
				}

			}

			// set timezone

			if($id == "") {
				//save
				$data['added_date'] = date("Y-m-d H:i:s");
				$data['added_user_id'] = $logged_in_user->user_id;

			} else {
				//edit
				unset($data['added_date']);
				$data['updated_date'] = date("Y-m-d H:i:s");
				$data['updated_user_id'] = $logged_in_user->user_id;
			}
			//save item
			if ( ! $this->Item->save( $data, $id )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->data['error'] = get_msg( 'err_model' );
				
				return;
			}

			 /** 
			* Upload Image Records 
			*/
		
			if ( !$id ) {
				//echo "aaaaaaaa";die;
			// if id is false, this is adding new record

				if ( ! $this->insert_video_and_img( $_FILES, 'item', $data['id'], "cover" )) {
					// if error in saving image
					// commit the transaction
					$this->db->trans_rollback();
					
					return;

				}

				if ( ! $this->insert_video_and_img( $_FILES, 'video', $data['id'],"video")) {
				// if error in saving image
					// commit the transaction
					//$this->db->trans_rollback();
					
					//return;
				}

				if ( ! $this->insert_video_and_img( $_FILES, 'video-icon', $data['id'],"video-icon")) {
				// if error in saving image
					// commit the transaction
					//$this->db->trans_rollback();
					
					//return;
				}

				
			}

			//amenities

			$data['amenities'] = ( $this->get_data( 'amenities' ) != false )? $this->get_data( 'amenities' ): array();

			$id = ( !$id )? $data['id']: $id ;

			if(!$this->ps_delete->delete_item_amenity( $id )) {
				if (count($data['amenities']) > 0) {
					for ($i=0; $i <count($data['amenities']) ; $i++) { 
						
						$select_data['amenity_id'] = $data['amenities'][$i];
						$select_data['item_id'] = $id;

						$this->Item_amenity->save($select_data);
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
					
					$this->set_flash_msg( 'success', get_msg( 'success_prd_edit' ));
				} else {
				// if user id is false, show success_edit message

					$this->set_flash_msg( 'success', get_msg( 'success_prd_add' ));
				}
			}

		//get inserted item id	
		$id = ( !$id )? $data['id']: $id ;

		///start deep link update item tb by MN
		$description = $data['description'];
		$title = $data['title'];
		$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $id );
        $images = $this->Image->get_all_by( $conds_img )->result();
		$img = $this->ps_image->upload_url . $images[0]->img_path;
		$deep_link = deep_linking_shorten_url($description,$title,$img,$id);
		$itm_data = array(
			'dynamic_link' => $deep_link
		);
        
        $this->Item->save($itm_data,$id);
        $this->upload_item_gallery($id);
		///End
		
		// Item Id Checking 
		if ( $this->has_data( 'gallery' )) {
		// if there is gallery, redirecti to gallery
			redirect( $this->module_site_url( 'gallery/' .$id ));
		} else if ( $this->has_data( 'promote' )) {
			redirect( site_url( ) . '/admin/paid_items/add/'.$id);
		}
		else {
		// redirect to list view
			redirect( $this->module_site_url() );
		}
	}

    //it uploads the item gallery images
	protected function upload_item_gallery($img_parent_id) {
	    $this->load->library('upload'); // Load the Upload library
	    $CI =& get_instance();
	    $CI->load->model('image');
	    $CI->load->model('App_setting');

	    $files = $_FILES['item_gallery']; // Retrieve files from input field
	    
	    // Directories for uploads and thumbnails
	    $upload_path = './uploads/';
	    $thumbnail_path = './uploads/thumbnail/';

	    // Ensure the thumbnail directory exists
	    if (!is_dir($thumbnail_path)) {
	        mkdir($thumbnail_path, 0777, true);
	    }

	    // File upload configurations
	    $config['upload_path'] = $upload_path;
	    $config['allowed_types'] = 'jpg|jpeg|png';
	    $config['max_size'] = 2048; // 2MB
	    $config['encrypt_name'] = true; // Generate unique file names

	    $uploaded_files = []; // To track successful uploads

	    for ($i = 0; $i < count($files['name']); $i++) {
	        $_FILES['file']['name'] = $files['name'][$i];
	        $_FILES['file']['type'] = $files['type'][$i];
	        $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
	        $_FILES['file']['error'] = $files['error'][$i];
	        $_FILES['file']['size'] = $files['size'][$i];

	        $this->upload->initialize($config); // Initialize configurations

	        if ($this->upload->do_upload('file')) {
	            $file_data = $this->upload->data(); // File info

	            // Log the encrypted file name (optional)
	            //log_message('info', 'Uploaded File: ' . $file_data['file_name']);

	            // Create a thumbnail
	            $source_image = $upload_path . $file_data['file_name'];
	            $thumbnail_image = $thumbnail_path . $file_data['file_name'];
	            $this->create_thumbnail($source_image, $thumbnail_image, 150, 150);

	            // Prepare file data for the database
	            $image = [
	                'added_user_id' => NULL,
	                'img_parent_id' => $img_parent_id,
	                'img_type' => 'item',
	                'img_desc' => NULL, // Add descriptions if needed
	                'img_path' => $file_data['file_name'], // Encrypted file name
	                'img_width' => $file_data['image_width'],
	                'img_height' => $file_data['image_height'],
	            ];

	            // Insert into the database
	            $CI->Image->save($image);
	            $uploaded_files[] = $image;
	        } else {
	            // Handle upload errors (log or display)
	            log_message('error', 'Upload Error: ' . $this->upload->display_errors());
	        }
	    }

	    
	    return $uploaded_files;
	}

	protected function create_thumbnail($source_image, $destination_image, $thumb_width, $thumb_height) {
	    // Get image size and type
	    $image_info = getimagesize($source_image);
	    $width = $image_info[0];
	    $height = $image_info[1];
	    $mime = $image_info['mime'];

	    // Create a new image resource from the source file
	    switch ($mime) {
	        case 'image/jpeg':
	            $src_image = imagecreatefromjpeg($source_image);
	            break;
	        case 'image/png':
	            $src_image = imagecreatefrompng($source_image);
	            break;
	        default:
	            log_message('error', 'Unsupported image type: ' . $mime);
	            return false; // Unsupported file type
	    }

	    // Create a new blank image with the desired dimensions
	    $thumb_image = imagecreatetruecolor($thumb_width, $thumb_height);

	    // Resize and copy the source image into the thumbnail image
	    imagecopyresampled(
	        $thumb_image,
	        $src_image,
	        0, 0, 0, 0,
	        $thumb_width, $thumb_height,
	        $width, $height
	    );

	    // Save the thumbnail to the destination
	    switch ($mime) {
	        case 'image/jpeg':
	            imagejpeg($thumb_image, $destination_image, 90); // 90 is quality for JPEG
	            break;
	        case 'image/png':
	            imagepng($thumb_image, $destination_image);
	            break;
	    }

	    // Free memory
	    imagedestroy($src_image);
	    imagedestroy($thumb_image);

	    return true;
	}

    /**
	 * Show Gallery
	 *
	 * @param      <type>  $id     The identifier
	 */
	function gallery( $id ) {
		// breadcrumb urls
		$edit_item = get_msg('prd_edit');

		$this->data['action_title'] = array( 
			array( 'url' => 'edit/'. $id, 'label' => $edit_item ), 
			array( 'label' => get_msg( 'item_gallery' ))
		);
		
		$_SESSION['parent_id'] = $id;
		$_SESSION['type'] = 'item';
    	    	
    	$this->load_gallery();
    }


	/**
 	* Update the existing one
	*/
	function edit( $id ) 
	{
		
		// breadcrumb urls
		$this->data['action_title'] = get_msg( 'prd_edit' );

		// load user
		$this->data['item'] = $this->Item->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );

	}

	/**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) 
	{
		
		$rule = 'required|callback_is_valid_name['. $id  .']';

		$this->form_validation->set_rules( 'title', get_msg( 'name' ), $rule);
		
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
		 $conds['title'] = $name;
		
		if ( strtolower( $this->Item->get_one( $id )->title ) == strtolower( $name )) {
		// if the name is existing name for that user id,
			return true;
		} else if ( $this->Item->exists( ($conds ))) {
		// if the name is existed in the system,
			$this->form_validation->set_message('is_valid_name', get_msg( 'err_dup_name' ));
			return false;
		}
		return true;
	}


	/**
	 * Delete the record
	 * 1) delete Item
	 * 2) delete image from folder and table
	 * 3) check transactions
	 */
	function delete( $id ) 
	{
		// start the transaction
		$this->db->trans_start();

		// check access
		$this->check_access( DEL );

		// delete categories and images
		$enable_trigger = true; 
		
		// delete categories and images
		//if ( !$this->ps_delete->delete_product( $id, $enable_trigger )) {
		$type = "item";

		if ( !$this->ps_delete->delete_history( $id, $type, $enable_trigger )) {

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
        	
			$this->set_flash_msg( 'success', get_msg( 'success_prd_delete' ));
		}
		
		redirect( $this->module_site_url());
	}


	/**
	 * Check Item name via ajax
	 *
	 * @param      boolean  $Item_id  The cat identifier
	 */
	function ajx_exists( $id = false )
	{
		
		// get Item name
		$name = $_REQUEST['title'];
		
		if ( $this->is_valid_name( $name, $id )) {
		// if the Item name is valid,
			
			echo "true";
		} else {
		// if invalid Item name,
			
			echo "false";
		}
	}

	/**
	 * Publish the record
	 *
	 * @param      integer  $prd_id  The Item identifier
	 */
	function ajx_publish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 1 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {
			//Need to delete at history table because that wallpaper need to show again on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->delete_by($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	/**
	 * Unpublish the records
	 *
	 * @param      integer  $prd_id  The category identifier
	 */
	function ajx_unpublish( $item_id = 0 )
	{
		// check access
		$this->check_access( PUBLISH );
		
		// prepare data
		$prd_data = array( 'status'=> 0 );
			
		// save data
		if ( $this->Item->save( $prd_data, $item_id )) {

			//Need to save at history table because that wallpaper no need to show on app
			$data_delete['item_id'] = $item_id;
			$this->Item_delete->save($data_delete);
			echo 'true';
		} else {
			echo 'false';
		}
	}

	function duplicate_item_save( $id ) {
		$conds['id'] = $id;

	    $approval_enable = $this->App_setting->get_one('app1')->is_approval_enabled;
		if ($approval_enable == 1) {
			$status = 0;
		} else {
			$status = 1;
		}

		$items = $this->Item->get_one_by($conds);
		$added_date = date("Y-m-d H:i:s");
		$itm_data = array(
			'item_location_city_id' => $items->item_location_city_id,
			'property_by_id' => $items->property_by_id,
			'item_currency_id' => $items->item_currency_id,
			'item_location_township_id' => $items->item_location_township_id,
			'title' => 'Copy of '.$items->title,
            'posted_by_id' => $items->posted_by_id,
			'description' => $items->description,
			'highlight_info' => $items->highlight_info,
			'price' => $items->price,
			'price_unit' => $items->price_unit,
			'is_negotiable' => $items->is_negotiable,
			'is_sold_out' => $items->is_sold_out,
			'price_note' => $items->price_note,
			'address' => $items->address,
			'area' => $items->area,
			'lat' => $items->lat,
			'lng' => $items->lng,
			'status' => $status,
			'added_date' => $added_date,
			'added_user_id' => $items->added_user_id,
		);
		// print_r($itm_data);die;
		//save item
		if ( ! $this->Item->save( $itm_data )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->itm_data['error'] = get_msg( 'err_model' );
			
			return;
		}
		$conds_img['img_parent_id'] = $id;
		$images = $this->Image->get_all_by($conds_img)->result();
		
		foreach ($images as $img) {
			$img_data = array(
				'img_parent_id'=> $itm_data['id'],
				'img_type' => $img->img_type,
				'img_desc' => $img->img_desc,
				'img_path' => $itm_data['id'] . $img->img_path,
				'img_width'=> $img->img_width,
				'img_height'=> $img->img_height
			);

			$upload_path = FCPATH . $this->config->item('upload_path');
			$upload_thumbnail_path = FCPATH . $this->config->item('upload_thumbnail_path');
			$upload_thumbnail_2x_path = FCPATH . $this->config->item('upload_thumbnail_2x_path');
			$upload_thumbnail_3x_path = FCPATH . $this->config->item('upload_thumbnail_3x_path');

			$upload_copy_file = $upload_path . $itm_data['id'] . $img->img_path;
			$thumb_copy_file = $upload_thumbnail_path . $itm_data['id'] . $img->img_path;
			$thumb2x_copy_file = $upload_thumbnail_2x_path . $itm_data['id'] . $img->img_path;
			$thumb3x_copy_file = $upload_thumbnail_3x_path . $itm_data['id'] . $img->img_path;

			$org_upload_file = $upload_path . $img->img_path;
			$org_thumb_file = $upload_thumbnail_path . $img->img_path;
			$org_thumb2x_file = $upload_thumbnail_2x_path . $img->img_path;
			$org_thumb3x_file = $upload_thumbnail_3x_path . $img->img_path;

			copy($org_upload_file, $upload_copy_file);
			copy($org_thumb_file, $thumb_copy_file);
			//check before create thumb2x,3x
			$is_thumb2x_3x_generate = $this->App_setting->get_one('app1')->is_thumb2x_3x_generate;
			if ($is_thumb2x_3x_generate == '1') {

				if (!file_exists($upload_thumbnail_2x_path)) {
					//check the thumbnail2x folder exist or not
					$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
				} elseif (!file_exists($upload_thumbnail_3x_path)) {
					//check the thumbnail2x folder exist or not
					$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
				} else {
					copy($org_thumb2x_file, $thumb2x_copy_file);
					copy($org_thumb3x_file, $thumb3x_copy_file);
				}
			}

			//save image
			if ( ! $this->Image->save( $img_data )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->img_data['error'] = get_msg( 'err_model' );
				
				return;
			}
		}

		$conds_amenities['item_id'] = $id;
		$amenities = $this->Item_amenity->get_all_by($conds_amenities)->result();
		
		foreach ($amenities as $amenity) {
			$amenity_data = array(
				'item_id'=> $itm_data['id'],
				'amenity_id' => $amenity->amenity_id
			);
			//save image
			if ( ! $this->Item_amenity->save( $amenity_data )) {
			// if there is an error in inserting user data,	

				// rollback the transaction
				$this->db->trans_rollback();

				// set error message
				$this->amenity_data['error'] = get_msg( 'err_model' );
				
				return;
			}
		}

		//generate new deeplink for copy item
		$itm_id = $itm_data['id'];
		$description = $itm_data['description'];
		$title = 'Copy of '.$itm_data['title'];
		$conds_img = array( 'img_type' => 'item', 'img_parent_id' => $itm_id );
        $images = $this->Image->get_all_by( $conds_img )->result();
		$img = $this->ps_image->upload_url . $images[0]->img_path;
		$deep_link = deep_linking_shorten_url($description,$title,$img,$itm_id);
		$itm_data_update = array(
			'dynamic_link' => $deep_link
		);
		
		$this->Item->save($itm_data_update,$itm_id);

		$this->set_flash_msg( 'success', get_msg( 'success_prd_duplicate_add' ));

		redirect( $this->module_site_url());
	}

	//get all location townships when select category

	function get_all_location_townships( $city_id )
    {
    	$conds['city_id'] = $city_id;
    	
    	$townships = $this->Item_location_township->get_all_by($conds);
		echo json_encode($townships->result());
    }


 }