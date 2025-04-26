<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PanaceaSoft Image Uploader
 */
class PS_Image
{
	// codeigniter instance
	protected $CI;
	
	// photo path to upload
	protected $upload_path;
	
	// photo path for thumbnail
	protected $upload_thumbnail_path;
	protected $upload_thumbnail2x_path;
	protected $upload_thumbnail3x_path;

	// photo url to upload
	public $upload_url;
	
	// photo url for thumbnail
	public $upload_thumbnail_url;

	//uploaded file name to use as the global
	protected $upload_file_name;
	
	// accepted media types
	protected $accept_image_type;

	/**
	 * Initialize Upload Config and load the Upload Library
	 */
	function __construct()
	{
		// load configurations
		$this->load_config();

		// load libraries
		$this->load_libraries();
	}

	/**
	 * Load Configurations
	 */
	function load_config()
	{
		// get CI instance
		$this->CI =& get_instance();

		// get upload path config
		$tmp_upload_path = $this->CI->config->item( 'upload_path' );
		$tmp_thumbnail_path = $this->CI->config->item( 'upload_thumbnail_path' );
		$tmp_thumbnail2x_path = $this->CI->config->item( 'upload_thumbnail_2x_path' );
		$tmp_thumbnail3x_path = $this->CI->config->item( 'upload_thumbnail_3x_path' );
		
		// get upload path config
		$this->upload_path = FCPATH . $tmp_upload_path;
		
		// get upload thumbnail config
		$this->upload_thumbnail_path = FCPATH . $tmp_thumbnail_path;
		$this->upload_thumbnail2x_path = FCPATH . $tmp_thumbnail2x_path;
		$this->upload_thumbnail3x_path = FCPATH . $tmp_thumbnail3x_path;

		// get upload path config
		$this->upload_url = base_url( $tmp_upload_path );
		
		// get upload thumbnail config
		$this->upload_thumbnail_url = base_url( $tmp_thumbnail_path);
		
		// get accepted media types
		$this->accept_image_type = $this->CI->config->item( 'image_type' );
	}

	/**
	 * Loads libraries.
	 */
	function load_libraries()
	{		
		// load file helper
		$this->CI->load->helper('file');

		// load image library
		$this->CI->load->library('image_lib');

		// load uploader library
		$this->CI->load->library('upload');

		// setup uploader configuration
		$config['upload_path'] = $this->upload_path;
		$config['allowed_types'] = $this->accept_image_type;
		$config['overwrite'] = FALSE;
		$this->CI->upload->initialize($config);
	}
	
	/**
	 * Upload image and resize the required dimension
	 *
	 * @param      <type>          $files   The files
	 * @param      integer|string  $userId  The user identifier
	 * @param      string          $type    The type
	 *
	 * @return     array           ( description_of_the_return_value )
	 */
	function upload( $files )
	{
		// empty array to return processed data
		$data = array();

		if ( empty( $files )) {
		// if there is no file, show error
			
			$data['error'] = "Choose file to upload";
			return $data;
		}
		
		// loop if the files array
		foreach ( $files as $field => $file ) {

			// assign the file name
			$_FILES[$field]['name'] = $_FILES[$field]['name'];
			


			if ( $file['error'] == 0 ) {    
			// if there is no error in file,		
					
				if ( $this->CI->upload->do_upload( $field )) {
				// if file uploading is success
					
					// get uploaded data
					$uploaded_data = $this->CI->upload->data();

					//get file name to use as the global
					$this->upload_file_name = $uploaded_data['file_name'];

					//Start Modify
					$org_img_width = $uploaded_data['image_width'];
					$ord_img_height = $uploaded_data['image_height'];
					
					/*
					P > H : 1000 
					L > W : 1000
					S > H & W : 1000
					*/

					$org_img_type = "";

					$org_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_width; //setting
					$org_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_height; //setting
					$org_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_height; //setting

					
					$thumb_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_thumb_width; //setting
					$thumb_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_thumb_height; //setting
					$thumb_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_thumb_height; //setting

					$thumb2x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb2x_width; //setting
					$thumb2x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb2x_height; //setting
					$thumb2x_img_square_width   = $this->CI->Backend_config->get_one("be1")->square_thumb2x_height; //setting

					$thumb3x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb3x_width; //setting
					$thumb3x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb3x_height; //setting
					$thumb3x_img_square_width  = $this->CI->Backend_config->get_one("be1")->square_thumb3x_height; //setting


					$need_resize = 0; //Flag
					$org_img_ratio = 0; 
					$thumb_img_ratio = 0;

					if($org_img_width > $ord_img_height) {
						$org_img_type = "L";
					} else if ($org_img_width < $ord_img_height) {
						$org_img_type = "P";
					} else {
						$org_img_type = "S";
					}


					if( $org_img_type == "L" ) {
						//checking width because of Landscape Image
						if( $org_img_landscape_width_config < $uploaded_data['image_width'] ) {

							$need_resize = 1;
							$org_img_ratio = round($org_img_landscape_width_config / $uploaded_data['image_width'],3);
							$thumb_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
							$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
							$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);


						} else {
							$thumb1x_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
							$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
							$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);

							$thumb1x_width = $thumb_img_landscape_width_config;
							$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

							$thumb2x_width = $thumb2x_img_landscape_width;
							$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

							$thumb3x_width = $thumb3x_img_landscape_width;
							$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
						}

					}

					if( $org_img_type == "P" ) {
						//checking width because of portrait Image
						if( $org_img_portrait_height_config < $uploaded_data['image_height'] ) {

							$need_resize = 1;
							$org_img_ratio = round($org_img_portrait_height_config / $uploaded_data['image_height'],3);
							$thumb_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
							$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
							$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

						} else {
							$thumb1x_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
							$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
							$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

							$thumb1x_width = round($uploaded_data['image_width'] * $thumb1x_img_ratio, 0);
							$thumb1x_height = $thumb_img_portrait_height_config;

							$thumb2x_width = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
							$thumb2x_height = $thumb2x_img_portrait_height;

							$thumb3x_width = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
							$thumb3x_height = $thumb3x_img_portrait_height;
						}
						
					}

					if( $org_img_type == "S" ) {
						//checking width (or) hight because of square Image
						if( $org_img_square_width_config < $uploaded_data['image_width'] ) {

							$need_resize = 1;
							$org_img_ratio = round($org_img_square_width_config / $uploaded_data['image_width'],3);
							$thumb_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
							$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
							$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

						} else {
							$thumb1x_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
							$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
							$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

							$thumb1x_width = $thumb_img_square_width_config;
							$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

							$thumb2x_width = $thumb2x_img_square_width;
							$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

							$thumb3x_width = $thumb3x_img_square_width;
							$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
						}
						
					}

					//need to check resize flag whether need to do resize or not?
					if( $need_resize == 1 ) {
						//original image need to resize according to config width and height
						
						
						$image_path = $uploaded_data['full_path'];

						$file_name = $uploaded_data['file_name'];
						

						// resize for original image
						$org_img_width  = round($uploaded_data['image_width'] * $org_img_ratio, 0);
						$org_img_height = round($uploaded_data['image_height'] * $org_img_ratio, 0);

						$this->create_thumbnail( $image_path, $org_img_width, $org_img_height, $this->upload_path );
						
						//resize for 1x,2x,3x thumbnail modified by MN
						$thumb_img_width  = round($uploaded_data['image_width'] * $thumb_img_ratio, 0);
						$thumb_img_height = round($uploaded_data['image_height'] * $thumb_img_ratio, 0);

						$thumb2x_img_width  = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
						$thumb2x_img_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

						$thumb3x_img_width  = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
						$thumb3x_img_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);

						$this->create_thumbnail( $image_path, $thumb_img_width, $thumb_img_height );
						
						//check before create thumb2x,3x
						$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
						if ($is_thumb2x_3x_generate == '1') {

							$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
							$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

							if (!file_exists($thumbnail_2x)) {
								//check the thumbnail2x folder exist or not
								$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
							} elseif (!file_exists($thumbnail_3x)) {
								//check the thumbnail2x folder exist or not
								$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
							} else {
								$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
								$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
							}
						}

						$uploaded_data_return['file_name']   = $file_name;
						$uploaded_data_return['image_width']  = $org_img_width;
						$uploaded_data_return['image_height'] = $org_img_height;


						$data[] = $uploaded_data_return;

						//print_r($data);die;

						//End Modify

					} else {
						// keep uploaded data in return object
						$data[] = $uploaded_data;

						// create thumbnail
						$image_path = $uploaded_data['full_path'];

						
						$org_width  =   round($uploaded_data['image_width'], 0);
						$org_height =   round($uploaded_data['image_height'], 0);

						$org_width = $org_width - 1;
						$org_height = $org_height - 1;

						

						// $this->create_thumbnail( $image_path, $org_width, $org_height, $this->upload_thumbnail_path );


						// old code
						// $thumb_width  =   round($uploaded_data['image_width'] * 0.25, 0);
						// $thumb_height =   round($uploaded_data['image_height'] * 0.25, 0);


						// $this->create_thumbnail( $image_path, $thumb_width, $thumb_height );

						//resize for 1x,2x,3x thumbnail modified by MN
						$this->create_thumbnail( $image_path, $thumb1x_width, $thumb1x_height );
						
						//check before create thumb2x,3x
						$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
						if ($is_thumb2x_3x_generate == '1') {

							$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
							$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

							if (!file_exists($thumbnail_2x)) {
								//check the thumbnail2x folder exist or not
								$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
							} elseif (!file_exists($thumbnail_3x)) {
								//check the thumbnail2x folder exist or not
								$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
							} else {
								$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
								$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
							}
						}					
					}

				} else {
				// if file uploading is fail,	
					
					// return error
					$data['error'] = $this->CI->upload->display_errors();

				}
			}
		}


		if ( empty( $data )) {
			$data['error'] = "No file is uploaded";
		}
			
		return $data;
	}

	function upload_cover( $files )
	{
		// empty array to return processed data
		$data = array();

		if ( empty( $files )) {
		// if there is no file, show error
			
			$data['error'] = "Choose file to upload";
			return $data;
		}
		
		// loop if the files array
		foreach ( $files as $field => $file ) {

			if($field == "cover") {

				// assign the file name
				$_FILES[$field]['name'] = $_FILES[$field]['name'];
					
				if ( $file['error'] == 0 ) {    
				// if there is no error in file,		
						
					if ( $this->CI->upload->do_upload( $field )) {
					// if file uploading is success

						// get uploaded data
						$uploaded_data = $this->CI->upload->data();
						
						//get file name to use as the global
						$this->upload_file_name = $uploaded_data['file_name'];
						
						//Start Modify
						$org_img_width = $uploaded_data['image_width'];
						$ord_img_height = $uploaded_data['image_height'];
						
						/*
						P > H : 1000 
						L > W : 1000
						S > H & W : 1000
						*/

						$org_img_type = "";

						$org_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_width; //setting
						$org_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_height; //setting
						$org_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_height; //setting

						
						$thumb_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_thumb_width; //setting
						$thumb_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_thumb_height; //setting
						$thumb_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_thumb_height; //setting

						$thumb2x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb2x_width; //setting
						$thumb2x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb2x_height; //setting
						$thumb2x_img_square_width   = $this->CI->Backend_config->get_one("be1")->square_thumb2x_height; //setting

						$thumb3x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb3x_width; //setting
						$thumb3x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb3x_height; //setting
						$thumb3x_img_square_width  = $this->CI->Backend_config->get_one("be1")->square_thumb3x_height; //setting

						$need_resize = 0; //Flag
						$org_img_ratio = 0; 
						$thumb_img_ratio = 0;

						if($org_img_width > $ord_img_height) {
							$org_img_type = "L";
						} else if ($org_img_width < $ord_img_height) {
							$org_img_type = "P";
						} else {
							$org_img_type = "S";
						}


						if( $org_img_type == "L" ) {
							//checking width because of Landscape Image
							if( $org_img_landscape_width_config < $uploaded_data['image_width'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_landscape_width_config / $uploaded_data['image_width'],3);
								
								$thumb_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);

								$thumb1x_width = $thumb_img_landscape_width_config;
								$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

								$thumb2x_width = $thumb2x_img_landscape_width;
								$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

								$thumb3x_width = $thumb3x_img_landscape_width;
								$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
							}

						}

						if( $org_img_type == "P" ) {
							//checking width because of portrait Image
							if( $org_img_portrait_height_config < $uploaded_data['image_height'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_portrait_height_config / $uploaded_data['image_height'],3);
								
								$thumb_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
								$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
								$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
								$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
								$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

								$thumb1x_width = round($uploaded_data['image_width'] * $thumb1x_img_ratio, 0);
								$thumb1x_height = $thumb_img_portrait_height_config;

								$thumb2x_width = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
								$thumb2x_height = $thumb2x_img_portrait_height;

								$thumb3x_width = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
								$thumb3x_height = $thumb3x_img_portrait_height;
							}

						}

						if( $org_img_type == "S" ) {
							//checking width (or) hight because of square Image
							if( $org_img_square_width_config < $uploaded_data['image_width'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_square_width_config / $uploaded_data['image_width'],3);
								
								$thumb_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

								$thumb1x_width = $thumb_img_square_width_config;
								$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

								$thumb2x_width = $thumb2x_img_square_width;
								$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

								$thumb3x_width = $thumb3x_img_square_width;
								$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
							}
							
						}

						//need to check resize flag whether need to do resize or not?
						if( $need_resize == 1 ) {
							//original image need to resize according to config width and height
							$image_path = $uploaded_data['full_path'];

							$file_name = $uploaded_data['file_name'];
							

							// resize for original image
							$org_img_width  = round($uploaded_data['image_width'] * $org_img_ratio, 0);
							$org_img_height = round($uploaded_data['image_height'] * $org_img_ratio, 0);
							
							$this->create_thumbnail( $image_path, $org_img_width, $org_img_height, $this->upload_path );
							
							// resize for thumbnail image
							$thumb_img_width  = round($uploaded_data['image_width'] * $thumb_img_ratio, 0);
							$thumb_img_height = round($uploaded_data['image_height'] * $thumb_img_ratio, 0);
								
							$thumb2x_img_width  = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
							$thumb2x_img_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

							$thumb3x_img_width  = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
							$thumb3x_img_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);

							
							$this->create_thumbnail( $image_path, $thumb_img_width, $thumb_img_height );
							
							//check before create thumb2x,3x
							$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
							if ($is_thumb2x_3x_generate == '1') {

								$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
								$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

								if (!file_exists($thumbnail_2x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
								} elseif (!file_exists($thumbnail_3x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
								} else {
									$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
									$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
								}
							}

							$uploaded_data_return['file_name']   = $file_name;
							$uploaded_data_return['image_width']  = $org_img_width;
							$uploaded_data_return['image_height'] = $org_img_height;


							$data[] = $uploaded_data_return;

							//print_r($data);die;

							//End Modify

						} else {
							// keep uploaded data in return object
							$data[] = $uploaded_data;

							// create thumbnail
							$image_path = $uploaded_data['full_path'];

							$thumb_width  =   round($uploaded_data['image_width'] * 0.25, 0);
							$thumb_height =   round($uploaded_data['image_height'] * 0.25, 0);

							$org_width = $org_width - 1;
							$org_height = $org_height - 1;

							$this->create_thumbnail( $image_path, $thumb_width, $thumb_height );
							
							//check before create thumb2x,3x
							$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
							if ($is_thumb2x_3x_generate == '1') {

								$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
								$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

								if (!file_exists($thumbnail_2x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
								} elseif (!file_exists($thumbnail_3x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
								} else {
									$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
									$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
								}
							}
						}

					} else {
					// if file uploading is fail,	
						
						// return error
						$data['error'] = $this->CI->upload->display_errors();
					}
				}

			}
		}

		if ( empty( $data )) {
			$data['error'] = "No file is uploaded";
		}
			
		return $data;
	}

	function upload_icon( $files )
	{
		// empty array to return processed data
		$data = array();

		if ( empty( $files )) {
		// if there is no file, show error
			
			$data['error'] = "Choose file to upload";
			return $data;
		}
		
		// loop if the files array
		foreach ( $files as $field => $file ) {

			if($field == "icon") {

				// assign the file name
				$_FILES[$field]['name'] = $_FILES[$field]['name'];
					
				if ( $file['error'] == 0 ) {    
				// if there is no error in file,		
						
					if ( $this->CI->upload->do_upload( $field )) {
					// if file uploading is success

						// get uploaded data
						$uploaded_data = $this->CI->upload->data();
						
						//get file name to use as the global
						$this->upload_file_name = $uploaded_data['file_name'];

						//Start Modify
						$org_img_width = $uploaded_data['image_width'];
						$ord_img_height = $uploaded_data['image_height'];
						
						/*
						P > H : 1000 
						L > W : 1000
						S > H & W : 1000
						*/

						$org_img_type = "";

						$org_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_width; //setting
						$org_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_height; //setting
						$org_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_height; //setting

						
						$thumb_img_landscape_width_config = $this->CI->Backend_config->get_one("be1")->landscape_thumb_width; //setting
						$thumb_img_portrait_height_config = $this->CI->Backend_config->get_one("be1")->potrait_thumb_height; //setting
						$thumb_img_square_width_config   = $this->CI->Backend_config->get_one("be1")->square_thumb_height; //setting

						$thumb2x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb2x_width; //setting
						$thumb2x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb2x_height; //setting
						$thumb2x_img_square_width   = $this->CI->Backend_config->get_one("be1")->square_thumb2x_height; //setting

						$thumb3x_img_landscape_width = $this->CI->Backend_config->get_one("be1")->landscape_thumb3x_width; //setting
						$thumb3x_img_portrait_height = $this->CI->Backend_config->get_one("be1")->potrait_thumb3x_height; //setting
						$thumb3x_img_square_width  = $this->CI->Backend_config->get_one("be1")->square_thumb3x_height; //setting


						$need_resize = 0; //Flag
						$org_img_ratio = 0; 
						$thumb_img_ratio = 0;

						if($org_img_width > $ord_img_height) {
							$org_img_type = "L";
						} else if ($org_img_width < $ord_img_height) {
							$org_img_type = "P";
						} else {
							$org_img_type = "S";
						}


						if( $org_img_type == "L" ) {
							//checking width because of Landscape Image
							if( $org_img_landscape_width_config < $uploaded_data['image_width'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_landscape_width_config / $uploaded_data['image_width'],3);
								
								$thumb_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_landscape_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $uploaded_data['image_width'],3);

								$thumb1x_width = $thumb_img_landscape_width_config;
								$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

								$thumb2x_width = $thumb2x_img_landscape_width;
								$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

								$thumb3x_width = $thumb3x_img_landscape_width;
								$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
							}

						}

						if( $org_img_type == "P" ) {
							//checking width because of portrait Image
							if( $org_img_portrait_height_config < $uploaded_data['image_height'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_portrait_height_config / $uploaded_data['image_height'],3);
								
								$thumb_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
								$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
								$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_portrait_height_config / $uploaded_data['image_height'],3);
								$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $uploaded_data['image_height'],3);
								$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $uploaded_data['image_height'],3);

								$thumb1x_width = round($uploaded_data['image_width'] * $thumb1x_img_ratio, 0);
								$thumb1x_height = $thumb_img_portrait_height_config;

								$thumb2x_width = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
								$thumb2x_height = $thumb2x_img_portrait_height;

								$thumb3x_width = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
								$thumb3x_height = $thumb3x_img_portrait_height;
							}
							
						}

						if( $org_img_type == "S" ) {
							//checking width (or) hight because of square Image
							if( $org_img_square_width_config < $uploaded_data['image_width'] ) {

								$need_resize = 1;
								$org_img_ratio = round($org_img_square_width_config / $uploaded_data['image_width'],3);
								
								$thumb_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

							} else {
								$thumb1x_img_ratio = round($thumb_img_square_width_config / $uploaded_data['image_width'],3);
								$thumb2x_img_ratio = round($thumb2x_img_square_width / $uploaded_data['image_width'],3);
								$thumb3x_img_ratio = round($thumb3x_img_square_width / $uploaded_data['image_width'],3);

								$thumb1x_width = $thumb_img_square_width_config;
								$thumb1x_height = round($uploaded_data['image_height'] * $thumb1x_img_ratio, 0);

								$thumb2x_width = $thumb2x_img_square_width;
								$thumb2x_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

								$thumb3x_width = $thumb3x_img_square_width;
								$thumb3x_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
							}
							
						}

						//need to check resize flag whether need to do resize or not?
						if( $need_resize == 1 ) {
							//original image need to resize according to config width and height
							$image_path = $uploaded_data['full_path'];

							$file_name = $uploaded_data['file_name'];
							

							// resize for original image
							$org_img_width  = round($uploaded_data['image_width'] * $org_img_ratio, 0);
							$org_img_height = round($uploaded_data['image_height'] * $org_img_ratio, 0);
							
							$this->create_thumbnail( $image_path, $org_img_width, $org_img_height, $this->upload_path );
							
							// resize for thumbnail image
							$thumb_img_width  = round($uploaded_data['image_width'] * $thumb_img_ratio, 0);
							$thumb_img_height = round($uploaded_data['image_height'] * $thumb_img_ratio, 0);
							
							$thumb2x_img_width  = round($uploaded_data['image_width'] * $thumb2x_img_ratio, 0);
							$thumb2x_img_height = round($uploaded_data['image_height'] * $thumb2x_img_ratio, 0);

							$thumb3x_img_width  = round($uploaded_data['image_width'] * $thumb3x_img_ratio, 0);
							$thumb3x_img_height = round($uploaded_data['image_height'] * $thumb3x_img_ratio, 0);
							
							$this->create_thumbnail( $image_path, $thumb_img_width, $thumb_img_height );
							
							//check before create thumb2x,3x
							$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
							if ($is_thumb2x_3x_generate == '1') {

								$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
								$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

								if (!file_exists($thumbnail_2x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
								} elseif (!file_exists($thumbnail_3x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
								} else {
									$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
									$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
								}
							}
							
							$uploaded_data_return['file_name']   = $file_name;
							$uploaded_data_return['image_width']  = $org_img_width;
							$uploaded_data_return['image_height'] = $org_img_height;


							$data[] = $uploaded_data_return;

							//print_r($data);die;

							//End Modify

						} else {
							// keep uploaded data in return object
							$data[] = $uploaded_data;

							// create thumbnail
							$image_path = $uploaded_data['full_path'];

							$thumb_width  =   round($uploaded_data['image_width'] * 0.25, 0);
							$thumb_height =   round($uploaded_data['image_height'] * 0.25, 0);

							$org_width = $org_width - 1;
							$org_height = $org_height - 1;

							$this->create_thumbnail( $image_path, $thumb_width, $thumb_height );
							
							//check before create thumb2x,3x
							$is_thumb2x_3x_generate = $this->CI->App_setting->get_one('app1')->is_thumb2x_3x_generate;
							if ($is_thumb2x_3x_generate == '1') {

								$thumbnail_2x = FCPATH . $this->CI->config->item( 'upload_thumbnail_2x_path' );
								$thumbnail_3x = FCPATH . $this->CI->config->item( 'upload_thumbnail_3x_path' );

								if (!file_exists($thumbnail_2x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
								} elseif (!file_exists($thumbnail_3x)) {
									//check the thumbnail2x folder exist or not
									$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
								} else {
									$this->create_thumbnail_2x( $image_path, $thumb2x_img_width, $thumb2x_img_height );
									$this->create_thumbnail_3x( $image_path, $thumb3x_img_width, $thumb3x_img_height );
								}
							}						
						}

					} else {
					// if file uploading is fail,	
						
						// return error
						$data['error'] = $this->CI->upload->display_errors();
					}
				}

			}
		}

		if ( empty( $data )) {
			$data['error'] = "No file is uploaded";
		}
			
		return $data;
	}

	
	/**
	 * Creats a thumbnail by passed width and height
	 *
	 * @param      <type>   $image_data  The image data
	 * @param      integer  $width       The width
	 * @param      integer  $height      The height
	 */
	function create_thumbnail( $image_path, $width = 150, $height = 100, $file_location_path = "" )
	{
		
		//echo $this->upload_thumbnail_path; die;
		// create thumbnail
		$this->CI->image_lib->clear();

		if( $file_location_path == "" ) {
			
			$new_image_path = $this->upload_thumbnail_path;
		} else {
			$new_image_path = $file_location_path;
		}

		
		$config = array(
			'image_library' => 'gd2',
			'source_image' => $image_path, //$image_data['full_path'],
			'new_image' => $new_image_path,
			'maintain_ration' => true,
			'width' => $width,
			'height' => $height,
			'quality' => '50%',
		);

		$this->CI->image_lib->initialize($config);
		$this->CI->image_lib->resize();

		$thumb_file = $new_image_path . $this->upload_file_name; 
		$thumb_file_size = filesize($thumb_file); // bytes
		$thumb_file_size = round($thumb_file_size / 1024, 2); // kilobytes with two digits

		//Get orgiginal file size
		$org_file = $image_path; 
		$org_file_size = filesize($org_file); // bytes
		$org_file_size = round($org_file_size / 1024, 2); // kilobytes with two digits
		//echo $thumb_file_size  . "  <>  " . $org_file_size; die; 
		//Checking
		
		if( $thumb_file_size > $org_file_size ) {
			//thumb image is bigger so copy original image
			copy($org_file, $thumb_file);

		}
	}

	function create_thumbnail_2x( $image_path, $width = 200, $height = 200, $file_location_path = "" )
	{

		//echo $this->upload_thumbnail_path; die;
		// create thumbnail
		$this->CI->image_lib->clear();

		if( $file_location_path == "" ) {
			
			$new_image_path = $this->upload_thumbnail2x_path;
		} else {
			$new_image_path = $file_location_path;
		}
		
		$config = array(
			'image_library' => 'gd2',
			'source_image' => $image_path, //$image_data['full_path'],
			'new_image' => $new_image_path,
			'maintain_ration' => true,
			'width' => $width,
			'height' => $height,
			'quality' => '50%',
		);

		$this->CI->image_lib->initialize($config);
		$this->CI->image_lib->resize();

		$thumb_file = $new_image_path . $this->upload_file_name; 
		$thumb_file_size = filesize($thumb_file); // bytes
		$thumb_file_size = round($thumb_file_size / 1024, 2); // kilobytes with two digits

		//Get orgiginal file size
		$org_file = $image_path; 
		$org_file_size = filesize($org_file); // bytes
		$org_file_size = round($org_file_size / 1024, 2); // kilobytes with two digits
		// echo $thumb_file_size  . "  <>  " . $org_file_size; die; 
		//Checking
		if( $thumb_file_size > $org_file_size ) {
			//thumb image is bigger so copy original image
			copy($org_file, $thumb_file);

		}
	}

	function create_thumbnail_3x( $image_path, $width = 350, $height = 350, $file_location_path = "" )
	{
		
		//echo $this->upload_thumbnail_path; die;
		// create thumbnail
		$this->CI->image_lib->clear();

		if( $file_location_path == "" ) {
			
			$new_image_path = $this->upload_thumbnail3x_path;
		} else {
			$new_image_path = $file_location_path;
		}
		
		$config = array(
			'image_library' => 'gd2',
			'source_image' => $image_path, //$image_data['full_path'],
			'new_image' => $new_image_path,
			'maintain_ration' => true,
			'width' => $width,
			'height' => $height,
			'quality' => '50%',
		);


		$this->CI->image_lib->initialize($config);
		$this->CI->image_lib->resize();

		if($this->upload_file_name){
			$thumb_file = $new_image_path . $this->upload_file_name; 
		}else{
			$thumb_file = $new_image_path . substr($image_path, 8); 
		}
		
		$thumb_file_size = filesize($thumb_file); // bytes
		$thumb_file_size = round($thumb_file_size / 1024, 2); // kilobytes with two digits

		//Get orgiginal file size
		$org_file = $image_path;
		$org_file_size = filesize($org_file); // bytes
		$org_file_size = round($org_file_size / 1024, 2); // kilobytes with two digits
		// echo $thumb_file_size  . "  <>  " . $org_file_size; die; 
		//Checking
		if( $thumb_file_size > $org_file_size ) {
			//thumb image is bigger so copy original image
			copy($org_file, $thumb_file);
		}
	}

	/**
	 * deletes original image and thunmbnail image if exists
	 *
	 * @param      <type>   $img_filename  The image filename
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	function delete_images ( $img_filename )
	{
		if ( empty( $img_filename )) {
		// if file name is empty, return true
			
			return true;
		}

		// delete original photo
		$img_path = $this->upload_path . $img_filename;
		if ( file_exists( $img_path )) {

			unlink( $img_path );
		}

		// delete thumbnail photo
		$thumb_path = $this->upload_thumbnail_path . $img_filename;
		if ( file_exists( $thumb_path )) {
			
			unlink( $thumb_path );
		}

		// delete thumbnail photo
		$thumb_path = $this->upload_thumbnail2x_path . $img_filename;
		if ( file_exists( $thumb_path )) {
			
			unlink( $thumb_path );
		}

		// delete thumbnail photo
		$thumb_path = $this->upload_thumbnail3x_path . $img_filename;
		if ( file_exists( $thumb_path )) {
			
			unlink( $thumb_path );
		}

		return true;
	}
	// video upload

	function upload_video( $files )
	{
		// empty array to return processed data
		$data = array();

		if ( empty( $files )) {
		// if there is no file, show error
			
			$data['error'] = "Choose file to upload";
			return $data;
		}
		
		// loop if the files array
		foreach ( $files as $field => $file ) {

			if($field == "video") {

				// assign the file name
				$_FILES[$field]['name'] = $_FILES[$field]['name'];
					
				if ( $file['error'] == 0 ) {    
				// if there is no error in file,		
						
					if ( $this->CI->upload->do_upload( $field )) {
					// if file uploading is success

						// get uploaded data
						$uploaded_data = $this->CI->upload->data();
							
						$data[] = $uploaded_data;
						//End Modify


					} else {
					// if file uploading is fail,	
						
						// return error
						$data['error'] = $this->CI->upload->display_errors();
					}
				}

			}
		}

		if ( empty( $data )) {
			$data['error'] = "No file is uploaded";
		}
			
		return $data;
	}
}
?>