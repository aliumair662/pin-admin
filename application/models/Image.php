<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for category table
 */
class Image extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'core_images', 'img_id', 'img' );
	}

	/**
	 * Implement the where clause
	 *
	 * @param      array  $conds  The conds
	 */
	function custom_conds( $conds = array())
	{
		// img_id condition
		if ( isset( $conds['img_id'] )) {
			$this->db->where( 'img_id', $conds['img_id'] );
		}
	
		// img_type condition
		if ( isset( $conds['img_type'] )) {
			$this->db->where( 'img_type', $conds['img_type'] );
		}

		// img_parent_id condition
		if ( isset( $conds['img_parent_id'] )) {
			$this->db->where( 'img_parent_id', $conds['img_parent_id'] );
		}

		// img_path condition
		if ( isset( $conds['img_path'] )) {
			$this->db->where( 'img_path', $conds['img_path'] );
		}

		// ordering condition
		if ( isset( $conds['ordering'] )) {
			$this->db->where( 'ordering', $conds['ordering'] );
		}
		
		// not_img_type condition
		if ( isset( $conds['not_img_type'] )) {
			$this->db->where( 'img_type !=', $conds['not_img_type'] );
		}

		// not_img_path condition
		if ( isset( $conds['not_img_path'] )) {
			$this->db->where( 'img_path !=', $conds['not_img_path'] );
		}

		// all image delete except about, backend-logo, fav-icon, login-image condition
		if(isset($conds['all_img_del'])){
			if($conds['all_img_del'] == 1){
				foreach($conds['img_types'] as $img_type)
					$this->db->where( 'img_type !=', $img_type );
			}
		}
		
		if ( isset( $conds['order_by'] )) {
			$this->db->order_by( 'ordering', 'asc');
		} else {
			$this->db->order_by('added_date', 'desc' );
		}
	}
}