<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for News
 */
class Sociallink extends API_Controller
{
    function __construct()
	{

		parent::__construct('Sociallink');
		
	}

    function add_post(){

        $user_id = $this->post('user_id');
        $type = $this->post('type');
        $username = $this->post('username');
        $link=$this->post('link');
        

        $checkduplicate=$this->db->get_where('bs_social_link',array('user_id'=>$user_id,'type'=>$type));
        if($checkduplicate->num_rows()<1){

            $status=$this->db->insert('bs_social_link',array('user_id'=>$user_id,'type'=>$type,'username'=>$username,'link'=>$link));
            if($status){
                $this->success_response( get_msg( 'success_add_sociallink' ), 200);
            }else{
                $this->error_response( get_msg( 'error_to_add_sociallink' ), 500);
            }
            

        }else{
            $updateid=$checkduplicate->row_array()['id'];

            $this->db->where('id',$updateid);
            $status=$this->db->update('bs_social_link',array('username'=>$username,'link'=>$link));

            if($status){
                $this->success_response( get_msg( 'success_update_sociallink' ), 200);
            }else{
                $this->error_response( get_msg( 'error_to_update_sociallink' ), 500);
            }
        }

    }

}