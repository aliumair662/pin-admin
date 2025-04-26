<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for Favourites
 */
class Property_by_subscribes extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		// call the parent
		parent::__construct( 'Property_by_subscribe' );
	}

    /**
     * Subcategory subscribe
     */
    function subcategory_subscribe_post(){
        
        // validation rules for chat history
		$rules = array(
			array(
	        	'field' => 'user_id',
	        	'rules' => 'required|callback_id_check[User]'
            )
        );
        
        $property_by_ids = $this->post('property_by_ids');

        // exit if there is an error in validation,
        if ( !$this->is_valid( $rules )) exit;

        foreach($property_by_ids as $property_by_id){
            
            $data = array(
                'user_id' => $this->post('user_id'),
                'property_by_id' => $property_by_id
            );
            
            if(!$this->Property_by_subscribe->exists($data)){
                // property by subscribe
                if(!$this->Property_by_subscribe->save($data)){
                    $this->error_response( get_msg( 'err_subcat_subscribe_save' ), 500);
                }
            }else{
                // property by unsubscribe
                if(!$this->Property_by_subscribe->delete_by($data)){
                    $this->error_response( get_msg( 'err_subcat_subscribe_delete' ), 500);
                }
            }            
            
        }
        $this->success_response( get_msg( 'success_subcat_subscribe' ), 200);
    }

}