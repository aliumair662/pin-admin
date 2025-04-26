<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for News
 */
class Agents extends API_Controller
{
	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct( 'Agent' );
		$this->load->library( 'PS_Image' );
	}

	/**
	 * Default Query for API
	 * @return [type] [description]
	 */
	function default_conds()
	{
		$conds = array();

		if ( $this->is_get ) {
		// if is get record using GET method

			$conds['user_type'] = 1;

		}

		return $conds;
	}


	function add_post() {

		
		// validation rules for user register
		$rules = array(
			array(
	        	'field' => 'user_id',
	        	'rules' => 'required|callback_id_check[User]'
	        )

        );

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        $application_status = $this->User->get_one($this->post('user_id'))->application_status;
        //print_r($application_status);die;

        if ($application_status == '1') {
        	$this->error_response( get_msg('already_agent'), 503);
        } elseif ($application_status == '2') {
        	$this->error_response( get_msg('agent_pending'), 503);
        } else {

        	$agent_data = array(
	        	
	        	"user_id" => $this->post('user_id'),
	        	"application_status" => 2,
	        	"apply_to" => 1,
	        	"user_type" => 0
	        );
			

			$user_id = $this->post('user_id');

			$role_id =  $this->Agent->get_one($user_id)->role_id;	
			$status =  $this->Agent->get_one($user_id)->status;
			$is_banned = $this->Agent->get_one($user_id)->is_banned;
			$user_type = $this->User->get_one($user_id)->user_type;

			if ($role_id == 4 && $status == 1 && $is_banned == 0) {
				//only approved register user can apply to agent
				$application_status = $this->Agent->get_one($user_id)->application_status;

				if ($application_status == 0 || $application_status == 3) {
					//start to apply agent
					$this->Agent->save($agent_data,$user_id);
				}else{
					//already applied agent
					$agent_data['application_status'] = $this->Agent->get_one($user_id)->application_status;
					$agent_data['apply_to'] = $this->Agent->get_one($user_id)->apply_to;
					$agent_data['user_type'] = $this->Agent->get_one($user_id)->user_type;

					$this->Agent->save($agent_data,$user_id);
				}
			}else{
				$this->error_response( get_msg('cannot_apply_agent'), 503);
			}

        }
        
		  	


		$files = $this->post('file');
		$img_id = $this->post('img_id');


		if ($img_id == "") {

			// upload images
			$upload_data = $this->ps_image->upload( $_FILES );
			//print_r($upload_data[0]['file_ext']);die;

			if ($upload_data[0]['file_ext'] == ".pdf") {
				foreach ( $upload_data as $upload ) {
				   	$user_img_data = array( 
					   	'img_parent_id'=> $user_id,
						'img_type' => "agent",
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> "",
						'img_height'=> ""
				   	);
				}
			}else{
				foreach ( $upload_data as $upload ) {
				   	$user_img_data = array( 
					   	'img_parent_id'=> $user_id,
						'img_path' => $upload['file_name'],
						'img_type' => "agent",
						'img_width'=> $upload['image_width'],
						'img_height'=> $upload['image_height']
				   	);
				}
			}
			
			$this->Image->save( $user_img_data );
		   
			
			   
		} else {
			
			// upload images
			$upload_data = $this->ps_image->upload( $_FILES );

			if ($upload_data[0]['file_ext'] == ".pdf") {
				foreach ( $upload_data as $upload ) {
				   	$user_img_data = array( 
				   		'img_id' => $img_id,
					   	'img_parent_id'=> $user_id,
						'img_type' => "agent",
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> "",
						'img_height'=> ""
				   	);
				}
			}else{
				foreach ( $upload_data as $upload ) {
					$user_img_data = array( 
				   		'img_id' => $img_id,
					   	'img_parent_id'=> $user_id,
						'img_desc' => "",
						'img_path' => $upload_data[0]['file_name'],
						'img_width'=> $upload['image_width'],
						'img_height'=> $upload['image_height']
				   	);
				}
			}

		  	
		  	$this->Image->save( $user_img_data, $img_id );
		
		}

		

       // $conds['id'] = $id; 

		$obj = $this->Agent->get_one( $user_id );
		$this->ps_adapter->convert_agent( $obj );
		$this->custom_response( $obj );
	}

	//image resize calculation

	function image_resize_calculation( $path )
	{


		// Start 

		$uploaded_file_path = $path;

		list($width, $height) = getimagesize($uploaded_file_path);
		$uploaded_img_width = $width;
		$uploaded_img_height = $height;

		$org_img_type = "";

		$org_img_landscape_width_config = $this->Backend_config->get_one("be1")->landscape_width; //setting
		$org_img_portrait_height_config = $this->Backend_config->get_one("be1")->potrait_height; //setting
		$org_img_square_width_config   = $this->Backend_config->get_one("be1")->square_height; //setting

		
		$thumb_img_landscape_width_config = $this->Backend_config->get_one("be1")->landscape_thumb_width; //setting
		$thumb_img_portrait_height_config = $this->Backend_config->get_one("be1")->potrait_thumb_height; //setting
		$thumb_img_square_width_config   = $this->Backend_config->get_one("be1")->square_thumb_height; //setting


		// $org_img_landscape_width_config = 1000; //setting
		// $org_img_portrait_height_config = 1000; //setting
		// $org_img_square_width_config   = 1000; //setting

		
		// $thumb_img_landscape_width_config = 200; //setting
		// $thumb_img_portrait_height_config = 200; //setting
		// $thumb_img_square_width_config   = 200; //setting


		$need_resize = 0; //Flag
			
		$org_img_ratio = 0; 
		$thumb_img_ratio = 0;

		if($uploaded_img_width > $uploaded_img_height) {
			$org_img_type = "L";
		} else if ($uploaded_img_width < $uploaded_img_height) {
			$org_img_type = "P";
		} else {
			$org_img_type = "S";
		}


		if( $org_img_type == "L" ) {
			//checking width because of Landscape Image
			if( $org_img_landscape_width_config < $uploaded_img_width ) {

				$need_resize = 1;
				$org_img_ratio = round($org_img_landscape_width_config / $uploaded_img_width,3);
				$thumb_img_ratio = round($thumb_img_landscape_width_config / $uploaded_img_width,3);


			}

		}

		if( $org_img_type == "P" ) {
			//checking width because of portrait Image
			if( $org_img_portrait_height_config < $uploaded_img_height ) {

				$need_resize = 1;
				$org_img_ratio = round($org_img_portrait_height_config / $uploaded_img_height,3);
				$thumb_img_ratio = round($thumb_img_portrait_height_config / $uploaded_img_height,3);
			}
			
		}

		if( $org_img_type == "S" ) {
			//checking width (or) hight because of square Image
			if( $org_img_square_width_config < $uploaded_img_width ) {

				$need_resize = 1;
				$org_img_ratio = round($org_img_square_width_config / $uploaded_img_width,3);
				$thumb_img_ratio = round($thumb_img_square_width_config / $uploaded_img_width,3);

			}
			
		}


		// if( $need_resize == 1 ) {
			//original image need to resize according to config width and height
			
			// resize for original image
			$new_image_path = FCPATH . "uploads/";
		    
		    if( $need_resize == 1 ) {
				$org_img_width  = round($uploaded_img_width * $org_img_ratio, 0);
				$org_img_height = round($uploaded_img_height * $org_img_ratio, 0);

			}else {

				$org_img_width = $org_img_width - 2;
				$org_img_height = $org_img_height - 2;
			}
			
			$this->ps_image->create_thumbnail( $uploaded_file_path, $org_img_width, $org_img_height, $new_image_path );
			
			// resize for thumbnail image
			$new_image__thumb_path = FCPATH . "uploads/thumbnail/";
			$thumb_img_width  = round($uploaded_img_width * $thumb_img_ratio, 0);
			$thumb_img_height = round($uploaded_img_height * $thumb_img_ratio, 0);
			
			
			$this->ps_image->create_thumbnail( $uploaded_file_path, $thumb_img_width, $thumb_img_height, $new_image__thumb_path );

			

			//End Modify

		// }


		// End


	}


	/**
	 * Convert Object
	 */
	function convert_object( &$obj )
	{

		// call parent convert object
		parent::convert_object( $obj );

		// convert customize item object
		$this->ps_adapter->convert_agent( $obj );
	}

}