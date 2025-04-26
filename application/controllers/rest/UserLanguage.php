<?php
require_once( APPPATH .'libraries/REST_Controller.php' );

/**
 * REST API for Language
 */
class UserLanguage extends API_Controller
{

	function __construct()
	{
        parent::__construct( 'UserLanguage' );
		$this->load->model('languageModel');
		
	}

    public function updateUserLanguage_post(){
        $user_id = $this->security->xss_clean($this->post('user_id'));
        $lang_id = $this->security->xss_clean($this->post('lang_id'));
        if($user_id!='' && $lang_id!=''){
            $where = array('user_id'=>$user_id);
            $data = array('language'=>$lang_id);
            $result = $this->languageModel->update($table_name='core_users', $where, $data);
            if($result==1){
              $this->response(array('status'=>'success','message'=>'updated successfully'));
            }else{
                echo json_encode("Please try again");
            }
        }else{
            echo json_encode("Please try again");
        }
    }

    public function getLanguage_get(){
        $client_api_key = $this->get( 'api_key' );
        if ( $client_api_key == NULL ) {
            echo json_encode("invalid api key");
        }
        $conds['key'] = $client_api_key;

        $api_key = $this->Api_key->get_all_by( $conds)->result();
        $server_api_key = $api_key[0]->key;

        if ( $client_api_key != $server_api_key ) {
            echo json_encode("invalid api key");
        }else{
            $table_name = 'bs_language';
            $where = array('app_language'=>1);
            $result = $this->languageModel->getdata($table_name, $where, $order_by='order_by');
            $data = [];
            if($result){
                foreach($result->result() as $lng){
                    $data['lang'][]=$lng;
                }
            }

            echo json_encode($data);
        }

        
    }

}