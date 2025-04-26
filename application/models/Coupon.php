<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for category table
 */
class Coupon extends PS_Model {
    function __construct() 
	{
		parent::__construct( 'bs_coupons', 'coupon_id', 'coupon' );
	}

}