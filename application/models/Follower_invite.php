<?php
	defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for Disable table
 */
class Follower_invite extends PS_Model {

	/**
	 * Constructs the required data
	 */
	function __construct() 
	{
		parent::__construct( 'bs_follower_invite', 'id', 'event' );
	}
}