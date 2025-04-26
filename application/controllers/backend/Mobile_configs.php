<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile settings Controller
 */


class Mobile_configs extends BE_Controller {
	
	/**
	 * Construt required variables
	 */
	protected $languages = array(
		array('language_code'=> 'en', 'country_code' => 'US', 'name' => 'English'),
		array('language_code'=> 'ar', 'country_code' => 'DZ', 'name' => 'Arabic'),
		array('language_code'=> 'hi', 'country_code' => 'IN', 'name' => 'Hindi'),
		array('language_code'=> 'de', 'country_code' => 'DE', 'name' => 'German'),
		array('language_code'=> 'es', 'country_code' => 'ES', 'name' => 'Spainish'),
		array('language_code'=> 'fr', 'country_code' => 'FR', 'name' => 'French'),
		array('language_code'=> 'id', 'country_code' => 'ID', 'name' => 'Indonesian'),
		array('language_code'=> 'it', 'country_code' => 'IT', 'name' => 'Italian'),
		array('language_code'=> 'ja', 'country_code' => 'JP', 'name' => 'Japanese'),
		array('language_code'=> 'ko', 'country_code' => 'KR', 'name' => 'Korean'),
		array('language_code'=> 'ms', 'country_code' => 'MY', 'name' => 'Malay'),
		array('language_code'=> 'pt', 'country_code' => 'PT', 'name' => 'Portuguese'),
		array('language_code'=> 'ru', 'country_code' => 'RU', 'name' => 'Russian'),
		array('language_code'=> 'th', 'country_code' => 'TH', 'name' => 'Thai'),
		array('language_code'=> 'tr', 'country_code' => 'TR', 'name' => 'Turkish'),
		array('language_code'=> 'zh', 'country_code' => 'CN', 'name' => 'Chinese'),
	);

	function __construct() {

		parent::__construct( MODULE_CONTROL, 'MOBILE_CONFIGS' );

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
	 * Load About Entry Form
	 */

	function index( $id = "mb1" ) {

		if ( $this->is_POST()) {
		// if the method is post

			// server side validation
			if ( $this->is_valid_input()) {

				// save user info
				$this->save( $id );
			}
		}
		
		//Get About Object
		$this->data['app'] = $this->Mobile_config->get_one( $id );

		$this->data['languages'] = $this->languages;

		$this->load_form($this->data);

	}

	/**
	 * Update the existing one
	 */
	function edit( $id = "mb1") {


		// load user
		$this->data['app'] = $this->Mobile_config->get_one( $id );

		// call the parent edit logic
		parent::edit( $id );
	}

	/**
	 * Saving Logic
	 * 1) save about data
	 * 2) check transaction status
	 *
	 * @param      boolean  $id  The about identifier
	 */
	function save( $id = false ) {

		// start the transaction
		$this->db->trans_start();
		
		// prepare data for save
		$data = array();

		// google_playstore_url
		if ( $this->has_data( 'google_playstore_url' )) {
			$data['google_playstore_url'] = $this->get_data( 'google_playstore_url' );
		}

		// apple_appstore_url
		if ( $this->has_data( 'apple_appstore_url' )) {
			$data['apple_appstore_url'] = $this->get_data( 'apple_appstore_url' );
		}

		// default_language
		if ( $this->has_data( 'default_language' )) {
			$data['default_language'] = $this->get_data( 'default_language' );
		}

		// exclude language
		$exclude_language = "";
		foreach($this->languages as $language){
			if ( $this->has_data( $language['language_code'] )) {
				continue;
			}elseif($this->get_data( 'default_language' ) == $language['language_code']){
				continue;
			}else{
				$exclude_language .= $language['language_code'] . ',';
			}
		}
		$data['exclude_language'] = substr($exclude_language, 0, -1);

		// print_r(substr($exclude_language, 0, -1	)); die;
		
		// price_format
		if ( $this->has_data( 'price_format' )) {
			$data['price_format'] = $this->get_data( 'price_format' );
		}

		// date_format
		if ( $this->has_data( 'date_format' )) {
			$data['date_format'] = $this->get_data( 'date_format' );
		}

		// ios_appstore_id
		if ( $this->has_data( 'ios_appstore_id' )) {
			$data['ios_appstore_id'] = $this->get_data( 'ios_appstore_id' );
		}

		// fb_key
		if ( $this->has_data( 'fb_key' )) {
			$data['fb_key'] = $this->get_data( 'fb_key' );
		}

		// default_loading_limit
		if ( $this->has_data( 'default_loading_limit' )) {
			$data['default_loading_limit'] = $this->get_data( 'default_loading_limit' );
		}

		// category_loading_limit
		if ( $this->has_data( 'category_loading_limit' )) {
			$data['category_loading_limit'] = $this->get_data( 'category_loading_limit' );
		}

		// posted_by_loading_limit
		if ( $this->has_data( 'posted_by_loading_limit' )) {
			$data['posted_by_loading_limit'] = $this->get_data( 'posted_by_loading_limit' );
		}

		// agent_loading_limit
		if ( $this->has_data( 'agent_loading_limit' )) {
			$data['agent_loading_limit'] = $this->get_data( 'agent_loading_limit' );
		}
		
		// amenities_loading_limit
		if ( $this->has_data( 'amenities_loading_limit' )) {
			$data['amenities_loading_limit'] = $this->get_data( 'amenities_loading_limit' );
		}

		// recent_item_loading_limit
		if ( $this->has_data( 'recent_item_loading_limit' )) {
			$data['recent_item_loading_limit'] = $this->get_data( 'recent_item_loading_limit' );
		}

		// popular_item_loading_limit
		if ( $this->has_data( 'popular_item_loading_limit' )) {
			$data['popular_item_loading_limit'] = $this->get_data( 'popular_item_loading_limit' );
		}

		// discount_item_loading_limit
		if ( $this->has_data( 'discount_item_loading_limit' )) {
			$data['discount_item_loading_limit'] = $this->get_data( 'discount_item_loading_limit' );
		}

		// feature_item_loading_limit
		if ( $this->has_data( 'feature_item_loading_limit' )) {
			$data['feature_item_loading_limit'] = $this->get_data( 'feature_item_loading_limit' );
		}

		// block_slider_loading_limit
		if ( $this->has_data( 'block_slider_loading_limit' )) {
			$data['block_slider_loading_limit'] = $this->get_data( 'block_slider_loading_limit' );
		}

		// follower_item_loading_limit
		if ( $this->has_data( 'follower_item_loading_limit' )) {
			$data['follower_item_loading_limit'] = $this->get_data( 'follower_item_loading_limit' );
		}

		// block_item_loading_limit
		if ( $this->has_data( 'block_item_loading_limit' )) {
			$data['block_item_loading_limit'] = $this->get_data( 'block_item_loading_limit' );
		}

		// default_razor_currency
		if ( $this->has_data( 'default_razor_currency' )) {
			$data['default_razor_currency'] = $this->get_data( 'default_razor_currency' );
		}

		// if use_thumbnail_as_placeholder is checked
		if ( $this->has_data( 'is_use_thumbnail_as_placeholder' )) {
			$data['is_use_thumbnail_as_placeholder'] = 1;
		} else {
			$data['is_use_thumbnail_as_placeholder'] = 0;
		}
	
		// if is_show_token_id is checked
		if ( $this->has_data( 'is_show_token_id' )) {
			$data['is_show_token_id'] = 1;
		} else {
			$data['is_show_token_id'] = 0;
		}

		// if is_show_admob is checked
		if ( $this->has_data( 'is_show_admob' )) {
			$data['is_show_admob'] = 1;
		} else {
			$data['is_show_admob'] = 0;
		}

		// if show_facebook_login is checked
		if ( $this->has_data( 'show_facebook_login' )) {
			$data['show_facebook_login'] = 1;
		} else {
			$data['show_facebook_login'] = 0;
		}

		// if show_google_login is checked
		if ( $this->has_data( 'show_google_login' )) {
			$data['show_google_login'] = 1;
		} else {
			$data['show_google_login'] = 0;
		}

		// if show_phone_login is checked
		if ( $this->has_data( 'show_phone_login' )) {
			$data['show_phone_login'] = 1;
		} else {
			$data['show_phone_login'] = 0;
		}

		// if is_razor_support_multi_currency is checked
		if ( $this->has_data( 'is_razor_support_multi_currency' )) {
			$data['is_razor_support_multi_currency'] = 1;
		} else {
			$data['is_razor_support_multi_currency'] = 0;
		}

		// if is_use_googlemap is checked
		if ( $this->has_data( 'is_use_googlemap' )) {
			$data['is_use_googlemap'] = 1;
		} else {
			$data['is_use_googlemap'] = 0;
		}

		// item_detail_view_count_for_ads
		if ( $this->has_data( 'item_detail_view_count_for_ads' )) {
			$data['item_detail_view_count_for_ads'] = $this->get_data( 'item_detail_view_count_for_ads' );
		}

		// if is_show_admob_inside_list is checked
		if ( $this->has_data( 'is_show_admob_inside_list' )) {
			$data['is_show_admob_inside_list'] = 1;
		} else {
			$data['is_show_admob_inside_list'] = 0;
		}

		// if is_show_ads_in_item_detail is checked
		if ( $this->has_data( 'is_show_ads_in_item_detail' )) {
			$data['is_show_ads_in_item_detail'] = 1;
		} else {
			$data['is_show_ads_in_item_detail'] = 0;
		}

		// if is_force_login is checked
		if ( $this->has_data( 'is_force_login' )) {
			$data['is_force_login'] = 1;
		} else {
			$data['is_force_login'] = 0;
		}

		// if is_language_config is checked
		if ( $this->has_data( 'is_language_config' )) {
			$data['is_language_config'] = 1;
		} else {
			$data['is_language_config'] = 0;
		}

		// blue_mark_size
		if ( $this->has_data( 'blue_mark_size' )) {
			$data['blue_mark_size'] = $this->get_data( 'blue_mark_size' );
		}

		// mile
		if ( $this->has_data( 'mile' )) {
			$data['mile'] = $this->get_data( 'mile' );
		}

		// video_duration
		if ( $this->has_data( 'video_duration' )) {
			$data['video_duration'] = $this->get_data( 'video_duration' );
		}

		// profile_image_size
		if ( $this->has_data( 'profile_image_size' )) {
			$data['profile_image_size'] = $this->get_data( 'profile_image_size' );
		}

		// upload_image_size
		if ( $this->has_data( 'upload_image_size' )) {
			$data['upload_image_size'] = $this->get_data( 'upload_image_size' );
		}

		// chat_image_size
		if ( $this->has_data( 'chat_image_size' )) {
			$data['chat_image_size'] = $this->get_data( 'chat_image_size' );
		}

		// promote_first_choice_day
		if ( $this->has_data( 'promote_first_choice_day' )) {
			$data['promote_first_choice_day'] = $this->get_data( 'promote_first_choice_day' );
		}

		// promote_second_choice_day
		if ( $this->has_data( 'promote_second_choice_day' )) {
			$data['promote_second_choice_day'] = $this->get_data( 'promote_second_choice_day' );
		}

		// promote_third_choice_day
		if ( $this->has_data( 'promote_third_choice_day' )) {
			$data['promote_third_choice_day'] = $this->get_data( 'promote_third_choice_day' );
		}

		// promote_fourth_choice_day
		if ( $this->has_data( 'promote_fourth_choice_day' )) {
			$data['promote_fourth_choice_day'] = $this->get_data( 'promote_fourth_choice_day' );
		}
		
		// if no_filter_with_location_on_map is checked
		if ( $this->has_data( 'no_filter_with_location_on_map' )) {
			$data['no_filter_with_location_on_map'] = 1;
		} else {
			$data['no_filter_with_location_on_map'] = 0;
		}

		// if is_show_owner_info is checked
		if ( $this->has_data( 'is_show_owner_info' )) {
			$data['is_show_owner_info'] = 1;
		} else {
			$data['is_show_owner_info'] = 0;
		}
		

		// save mobile config
		if ( ! $this->Mobile_config->save( $data, $id )) {
		// if there is an error in inserting user data,	

			// rollback the transaction
			$this->db->trans_rollback();

			// set error message
			$this->data['error'] = get_msg( 'err_model' );
			
			return;
		}

		// commit the transaction
		if ( ! $this->check_trans()) {
        	
			// set flash error message
			$this->set_flash_msg( 'error', get_msg( 'err_model' ));
		} else {

			if ( $id ) {
			// if user id is not false, show success_add message
				
				$this->set_flash_msg( 'success', get_msg( 'success_mobile_edit' ));
			} else {
			// if user id is false, show success_edit message

				$this->set_flash_msg( 'success', get_msg( 'success_mobile_add' ));
			}
		}
		
		redirect( site_url('/admin/mobile_configs') );

	}

	 /**
	 * Determines if valid input.
	 *
	 * @return     boolean  True if valid input, False otherwise.
	 */
	function is_valid_input( $id = 0 ) {
 		return true;
	}

}