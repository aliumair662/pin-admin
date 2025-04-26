<?php
require_once( APPPATH .'libraries/REST_Controller.php' );
require_once( APPPATH .'libraries/braintree_lib/autoload.php' );
require_once( APPPATH .'libraries/stripe_lib/autoload.php' );

/**
 * REST API for News
 */
class Packages extends API_Controller
{

	/**
	 * Constructs Parent Constructor
	 */
	function __construct()
	{
		parent::__construct( 'Package' );
	}

	/**
	 * Default Query for API
	 * @return [type] [description]
	 */
	function default_conds()
	{
		$conds = array();

		if ( $this->is_get ) {
		// if is get record using GET method
        }

		return $conds;
	}

	/**
	 * Convert Object
	 */
	function convert_object( &$obj )
	{

		// call parent convert object
		parent::convert_object( $obj );

		// convert customize item object
		$this->ps_adapter->convert_package( $obj );
	}

	/**
	 * package bought transaction
	 */
	function is_package_bought_post() {
		// validation rules for chat history
		$rules = array(
			array(
	        	'field' => 'user_id',
	        	'rules' => 'required|callback_id_check[User]'
	        ),
	        array(
	        	'field' => 'package_id',
	        	'rules' => 'required|callback_id_check[Package]'
			)
        );
		if ( !$this->is_valid( $rules )) exit;
		$user_id = $this->post('user_id');
		$package_id = $this->post('package_id');

		// print_r($this->post( 'payment_method' ));die;
		if($this->post( 'payment_method' ) == "paypal") {

			//User using Paypal to submit the transaction
			$payment_info = $this->Paid_config->get_one('pconfig1');

			$gateway = new Braintree_Gateway([
			  'environment' => trim($payment_info->paypal_environment),
			  'merchantId' => trim($payment_info->paypal_merchant_id),
			  'publicKey' => trim($payment_info->paypal_public_key),
			  'privateKey' => trim($payment_info->paypal_private_key)
			]);

			$result = $gateway->transaction()->sale([
			  'amount' 			   => $this->post( 'price' ),
			  'paymentMethodNonce' => $this->post( 'payment_method_nonce' ),
			  'options' => [
			    'submitForSettlement' => True
			  ]
			]);

			if($result->success == 1) {
			
				$paypal_result = $result->success;
			
			} else {

				$this->error_response( get_msg( 'paypal_transaction_failed' ), 500);
			
			}

		} else if($this->post( 'payment_method' ) == "stripe") {

			//User using Stripe to submit the transaction
			$paid_config = $this->Paid_config->get_one('pconfig1');

			$payment_method_nonce = explode('_' , $this->post( 'payment_method_nonce' ));

			if ($payment_method_nonce[0] == 'tok') {
			
				try {
				
					# set stripe test key
					\Stripe\Stripe::setApiKey( trim($paid_config->stripe_secret_key) );
					
					$charge = \Stripe\Charge::create(array(
				    	"amount" 	  => (int)$this->post( 'price' ) * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
				    	"currency"    => trim($paid_config->currency_short_form),
				    	"source"      => $this->post( 'payment_method_nonce' ),
				    	"description" => get_msg('order_desc')
				    ));
				    
				    if( $charge->status == "succeeded" )
				    {
				    	$stripe_result = 1;
				    } else {
				    	$this->error_response( get_msg( 'stripe_transaction_failed' ), 500);
				    }
					
				} 

				catch(exception $e) {
				  	
				 	$this->error_response( get_msg( 'stripe_transaction_failed' ), 500);
				    
				}
			} else if($payment_method_nonce[0] == 'pm') {
				try {
					\Stripe\Stripe::setApiKey( trim($paid_config->stripe_secret_key) );

					$paymentIntent = \Stripe\PaymentIntent::create([
						'payment_method' => $this->post( 'payment_method_nonce' ),
						'amount' => $this->post( 'price' ) * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
						'currency' => trim($paid_config->currency_short_form),
						'confirmation_method' => 'manual',
	                	'confirm' => true,
					]);

					if( $paymentIntent->status == "succeeded" )
				    {
				    	$stripe_result = 1;
				    } else {
				    	$this->error_response( get_msg( 'stripe_transaction_failed' ), 500);
				    }
				} 

				catch(exception $e) {
				  	
				 	$this->error_response( get_msg( 'stripe_transaction_failed' ), 500);
				    
				}

			}

		} else if($this->post( 'payment_method' ) == "razor") {
          
			//User Using COD 
			$payment_method = "Razor";


			$razor_result = 1;

		} else if($this->post( 'payment_method' ) == "offline") {

			//User Using COD 
			$payment_method = "Offline";


			$offline_result = 1;

		} else if($this->post( 'payment_method' ) == "paystack") {

			//User Using COD 
			$payment_method = "Paystack";


			$paystack_result = 1;

		} else if($this->post( 'payment_method' ) == "In_App_Purchase") {

			//User Using COD 
			$payment_method = "In_App_Purchase";


			$in_app_purchase_result = 1;

		}
		
		if( $paypal_result == 1 || $stripe_result == 1  || $razor_result == 1 || $paystack_result == 1 || $in_app_purchase_result == 1 ) {
			
			// get post count by package id
			$post_count = $this->Package->get_one($package_id)->post_count;
			$remaining_post = $this->User->get_one($user_id)->remaining_post;
			
			// set post count to package buyer user
			$user_data = array(
				"remaining_post" => (int)$remaining_post + (int)$post_count
			);

			$status = 1;
			$this->save_history($package_id, $status);
			$this->User->save($user_data, $user_id);
			$this->success_response( get_msg( 'success_pkg_bought' ), 201);

		}else if( $offline_result == 1 ) {
			
			$status = 0;
			$id = $this->save_history($package_id, $status);
			$this->success_response( get_msg( 'success_pkg_bought' ), 201);
				
		}else{
			$this->error_response( get_msg( 'err_paid_adpost_history' ), 500);
		}		
	}
	
	function save_history($package_id, $status) {

	  	$paid_data = array(
	  		"package_id" => $package_id,
	  		"user_id" => $this->post('user_id'),
	  		"price" => $this->post('price'),
	  		"payment_method" => $this->post('payment_method'),
	  		"razor_id" => $this->post('razor_id'),
	  		"isPaystack" => $this->post('isPaystack'),
			"status" => $status,
			"added_date" => date("Y-m-d H:i:s"),
	  	);

	  	if(!$this->Package_bought->save($paid_data)){
			$this->error_response( get_msg( 'err_pkg_bought_save' ), 503);
		}
	  	$id = $paid_data['id'];
	  	// print_r($id);die;
	  	return $id;

	}

}