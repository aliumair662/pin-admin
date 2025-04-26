<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model class for about table
 */
class Agent extends PS_Model {

    /**
     * Constructs the required data
     */
    function __construct()
    {
        parent::__construct(  'core_users', 'user_id', 'usr' );
    }

    /**
     * Implement the where clause
     *
     * @param      array  $conds  The conds
     */
    function custom_conds( $conds = array())
    {

        // id condition
        if ( isset( $conds['user_id'] )) {
            $this->db->where( 'user_id', $conds['user_id'] );
        }

        // user_name condition
        if ( isset( $conds['user_name'] )) {
            $this->db->where( 'user_name', $conds['user_name'] );
        }

        // user_email condition
        if ( isset( $conds['user_email'] )) {
            $this->db->where( 'user_email', $conds['user_email'] );
        }

        // user_phone condition
        if ( isset( $conds['user_phone'] )) {
            $this->db->where( 'user_phone', $conds['user_phone'] );
        }

        // user_address condition
        if ( isset( $conds['user_address'] )) {
            $this->db->where( 'user_address', $conds['user_address'] );
        }

        // city condition
        if ( isset( $conds['city'] )) {
            $this->db->where( 'city', $conds['city'] );
        }

        // user_about_me condition
        if ( isset( $conds['user_about_me'] )) {
            $this->db->where( 'user_about_me', $conds['user_about_me'] );
        }

        // apply_to condition
        if ( isset( $conds['apply_to'] )) {
            $this->db->where( 'apply_to', $conds['apply_to'] );
        } else {
            $this->db->where( 'apply_to', 1 );
        }

        // application_status condition
        if ( isset( $conds['application_status'] )) {
            $this->db->where( 'application_status', $conds['application_status'] );
        }

        // user_type condition
        if ( isset( $conds['user_type'] )) {
            $this->db->where( 'user_type', $conds['user_type'] );
        }

        // searchterm
        if ( isset( $conds['searchterm'] )) {
            $this->db->like( 'user_name', $conds['searchterm'] );
            $this->db->or_like( 'user_email', $conds['searchterm'] );
        }
    }
}