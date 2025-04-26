<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Image_lists Controller
 */
class Image_lists extends BE_Controller {

	/**
	 * Construt required variables
	 */
	function __construct() {
		
		
		parent::__construct( MODULE_CONTROL, 'IMAGE_LISTS' );
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
		$conds['order_by_field'] = "added_date";
		$conds['order_by_type'] = "desc";
		$conds['not_img_type'] = "video";
		$conds['not_img_path'] = "";
		// get rows count
		$this->data['rows_count'] = $this->Image->count_all_by( $conds );

		// get images
		$this->data['images'] = $this->Image->get_all_by( $conds , $this->pag['per_page'], $this->uri->segment( 4 ) );

		// load index logic
		parent::index();
	}

	function createThumbs( $img_id ) {

		//print($img_id);die;
		$img_url = $this->Image->get_one($img_id)->img_path;
		// get the path to the current directory
		$path = 'uploads/';
		$path_info = pathinfo($_SERVER['SCRIPT_NAME']);

		$url = $path_info['dirname'];
		 
		// create an array to store image names in.
		$images = array();
		$dir = new DirectoryIterator($path);


			$img_path = $this->ps_image->upload_url . $img_url;

			if (@getimagesize($img_path)) {


				$img_size = getimagesize($img_path);
				$org_img_width = $img_size[0];
				$ord_img_height = $img_size[1];


				//setting from backend config
				$thumb_img_landscape_width_config = $this->Backend_config->get_one("be1")->landscape_thumb_width; //setting
				$thumb_img_portrait_height_config = $this->Backend_config->get_one("be1")->potrait_thumb_height; //setting
				$thumb_img_square_width_config   = $this->Backend_config->get_one("be1")->square_thumb_height; //setting

				$thumb2x_img_landscape_width = $this->Backend_config->get_one("be1")->landscape_thumb2x_width; //setting
				$thumb2x_img_portrait_height = $this->Backend_config->get_one("be1")->potrait_thumb2x_height; //setting
				$thumb2x_img_square_width   = $this->Backend_config->get_one("be1")->square_thumb2x_height; //setting

				$thumb3x_img_landscape_width = $this->Backend_config->get_one("be1")->landscape_thumb3x_width; //setting
				$thumb3x_img_portrait_height = $this->Backend_config->get_one("be1")->potrait_thumb3x_height; //setting
				$thumb3x_img_square_width  = $this->Backend_config->get_one("be1")->square_thumb3x_height; //setting

				
				if($org_img_width > $ord_img_height) {
					$org_img_type = "L";
				} else if ($org_img_width < $ord_img_height) {
					$org_img_type = "P";
				} else {
					$org_img_type = "S";
				}
				
				if( $org_img_type == "L" ) {
					$thumb1x_img_ratio = round($thumb_img_landscape_width_config / $org_img_width,3);
					$thumb2x_img_ratio = round($thumb2x_img_landscape_width / $org_img_width,3);
					$thumb3x_img_ratio = round($thumb3x_img_landscape_width / $org_img_width,3);

					$thumb1x_width = $thumb_img_landscape_width_config;
					$thumb1x_height = round($ord_img_height * $thumb1x_img_ratio, 0);

					$thumb2x_width = $thumb2x_img_landscape_width;
					$thumb2x_height = round($ord_img_height * $thumb2x_img_ratio, 0);

					$thumb3x_width = $thumb3x_img_landscape_width;
					$thumb3x_height = round($ord_img_height * $thumb3x_img_ratio, 0);
				}
				
				if( $org_img_type == "P" ) {
					$thumb1x_img_ratio = round($thumb_img_portrait_height_config / $ord_img_height,3);
					$thumb2x_img_ratio = round($thumb2x_img_portrait_height / $ord_img_height,3);
					$thumb3x_img_ratio = round($thumb3x_img_portrait_height / $ord_img_height,3);

					$thumb1x_width = round($org_img_width * $thumb1x_img_ratio, 0);
					$thumb1x_height = $thumb_img_portrait_height_config;

					$thumb2x_width = round($org_img_width * $thumb2x_img_ratio, 0);
					$thumb2x_height = $thumb2x_img_portrait_height;

					$thumb3x_width = round($org_img_width * $thumb3x_img_ratio, 0);
					$thumb3x_height = $thumb3x_img_portrait_height;
				}

				if( $org_img_type == "S" ) {
					$thumb1x_img_ratio = round($thumb_img_square_width_config / $org_img_width,3);
					$thumb2x_img_ratio = round($thumb2x_img_square_width / $org_img_width,3);
					$thumb3x_img_ratio = round($thumb3x_img_square_width / $org_img_width,3);

					$thumb1x_width = $thumb_img_square_width_config;
					$thumb1x_height = round($ord_img_height * $thumb1x_img_ratio, 0);

					$thumb2x_width = $thumb2x_img_square_width;
					$thumb2x_height = round($ord_img_height * $thumb2x_img_ratio, 0);

					$thumb3x_width = $thumb3x_img_square_width;
					$thumb3x_height = round($ord_img_height * $thumb3x_img_ratio, 0);
				}
				
				$img_2x_url = 'uploads/' . $img_url ;
				$img_3x_url = 'uploads/' . $img_url ;

				//$this->ps_image->create_thumbnail( $img_path, $thumb1x_width, $thumb1x_height );
				$is_thumb2x_3x_generate = $this->App_setting->get_one('app1')->is_thumb2x_3x_generate;
				
				//check whether thumb2x,3x setting is ON or OFF
				if ($is_thumb2x_3x_generate == '1') {

					$upload_thumbnail_2x_path = FCPATH . $this->config->item('upload_thumbnail_2x_path') . $img_url;
					$upload_thumbnail_3x_path = FCPATH . $this->config->item('upload_thumbnail_3x_path') . $img_url;

					$thumbnail_2x = FCPATH . $this->config->item('upload_thumbnail_2x_path');
					$thumbnail_3x = FCPATH . $this->config->item('upload_thumbnail_3x_path');

					if (!file_exists($thumbnail_2x)) {
						//check the thumbnail2x folder exist or not
						$this->set_flash_msg( 'error', get_msg( 'create_thumb2x_folder' ));	
					} elseif (!file_exists($thumbnail_3x)) {
						//check the thumbnail2x folder exist or not
						$this->set_flash_msg( 'error', get_msg( 'create_thumb3x_folder' ));	
					} elseif (file_exists($upload_thumbnail_2x_path)) {
						//check the image exist in thumb2x folder or not
						$this->set_flash_msg( 'error', get_msg( 'thumb2x_already_existed' ));	
					} elseif (file_exists($upload_thumbnail_3x_path)) {
						//check the image exist in thumb3x folder or not
						$this->set_flash_msg( 'error', get_msg( 'thumb3x_already_existed' ));	
					} else {
						$this->ps_image->create_thumbnail_2x( $img_2x_url, $thumb2x_width, $thumb2x_height );
						$this->ps_image->create_thumbnail_3x( $img_3x_url, $thumb3x_width, $thumb3x_height );

						if ( !$this->check_trans()) {

						$this->set_flash_msg( 'error', get_msg( 'err_model' ));	
						} else {
				        	
							$this->set_flash_msg( 'success', get_msg( 'success_img_create' ));
						}
					}

				} else {
					$this->set_flash_msg( 'error', get_msg( 'thumb2x_3x_settting_off' ));	
				}
				
			} else {
				$this->set_flash_msg( 'error', get_msg( 'img_not_exist' ));

			}	
			redirect( $this->module_site_url());

	}
	
}