<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Read More
 *
 * @param      string  $string   string
 * @param      integer  $limit   character limit
 *
 * @return     string   ( description_of_the_return_value )
 */
if ( !function_exists( 'read_more' )) 
{
	function read_more( $string, $limit )
	{
		$string = strip_tags($string);
		
		if (strlen($string) > $limit) {
		
		    // truncate string
		    $stringCut = substr($string, 0, $limit);
		
		    // make sure it ends in a word so assassinate doesn't become ass...
		    $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
		}
		return $string;
	}
}

if(! function_exists('get_user_lang')){
	function get_user_lang($user_id){
		$CI =& get_instance();
		$result = $CI->db->query("SELECT bs_language.name, core_users.user_id, core_users.language from core_users inner join bs_language on bs_language.id=core_users.language where core_users.user_id='$user_id'");
		
		if($result){
			foreach($result->result() as $lang){
				return $lang->name;
			}
		}
	}
}

/**
 * transform 'added date' display
 *
 * @param      integer  $time   The time
 *
 * @return     string   ( description_of_the_return_value )
 */
if ( ! function_exists( 'ago' ))
{
	function ago( $time )
	{
		// get ci instance
		$CI =& get_instance();
		//for language
		$conds['status'] = 1;
		$language = $CI->Language->get_one_by($conds);
		$language_id = $language->id;
		//for today language string
		$conds_today['key'] = "today_label";
		$conds_today['language_id'] = $language_id;
		$today_string = $CI->Language_string->get_one_by( $conds_today );
		$today_now = $today_string->value;
		if ( empty( $time )) return '"'.$today_now.'"';

		// get ci instance
		$CI =& get_instance();
		
		$time = mysql_to_unix( $time );
		$now = $CI->db->query('SELECT NOW( ) as now')->row()->now;
		$now = mysql_to_unix( $now );

		if ( $time > $now ){

			$now = date('Y-m-d H:i:s');
			$now = mysql_to_unix( $now );
		}

		$periods = array("second_ago", "minute_ago", "hour_ago", "day_ago", "week_ago", "month_ago", "year_ago", "decade_ago");
		$lengths = array("60","60","24","7","4.35","12","10");

		$difference = $now - $time;

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		// if ($difference != 1) {
			// load the language
			$conds_str['key'] = $periods[$j];
			$conds_str['language_id'] = $language_id;
			$lang_string = $CI->Language_string->get_one_by( $conds_str );
			$message = $lang_string->value;
		// }
		//for just now language string
		$conds_now['key'] = "just_now_label";
		$conds_now['language_id'] = $language_id;
		$just_string = $CI->Language_string->get_one_by( $conds_now );
		$just_now = $just_string->value;
		//for ago language string
		$conds_ago['key'] = "ago_label";
		$conds_ago['language_id'] = $language_id;
		$ago_string = $CI->Language_string->get_one_by( $conds_ago );
		$ago = $ago_string->value;
		if ($difference==0) {
			return '"'.$just_now.'"';
		} else {
			return "$difference $message $ago";
		}
	}
}

/**
 * return the message
 *
 * @param      <type>  $key    The key
 */
if ( ! function_exists( 'get_msg' ))
{
	function get_msg( $key )
	{
		// get ci instance
		$CI =& get_instance();

		$conds['status'] = 1;
		$language = $CI->Language->get_one_by($conds);
		$language_id = $language->id;
		// load the language
		$conds_str['key'] = $key;
		$conds_str['language_id'] = $language_id;
		$lang_string = $CI->Language_string->get_one_by( $conds_str );
		$message = $lang_string->value;

		if ( empty( $message )) {
		// if message is empty, return the key
			return $key;
		}

		// return the message
		return $message;
	}
}

/**
 * return the message
 *
 * @param      <type>  $key    The key
 */
if ( ! function_exists( 'smtp_config' ))
{
	function smtp_config( )
	{
		// get ci instance
		$CI =& get_instance();
		$smtp_host = $CI->Backend_config->get_one('be1')->smtp_host;
		$smtp_port = $CI->Backend_config->get_one('be1')->smtp_port;
		$smtp_user = $CI->Backend_config->get_one('be1')->smtp_user;
		$smtp_pass = $CI->Backend_config->get_one('be1')->smtp_pass;

		$config = Array(
		    'protocol' => 'smtp',
		    'smtp_host' => $smtp_host,
		    'smtp_port' => $smtp_port,
		    'smtp_user' => $smtp_user, //sender@blog.panacea-soft.com //azxcvbnm
		    'smtp_pass' => $smtp_pass,
		    'mailtype'  => 'text', 
		    'charset'   => 'iso-8859-1'
		);
		
		return $config;
	}
}

/**
 * Show the flash message
 */
if ( ! function_exists( 'flash_msg')) 
{
	function flash_msg()
	{
		// get ci instance
		$CI =& get_instance();

		$CI->load->view( 'common/flash_msg' );
	}
}

/**
 * Shows the analytic.
 */
if ( ! function_exists( 'show_analytic' ))
{
	function show_analytic()
	{
		// get ci instance
		$CI =& get_instance();

		$CI->load->view( 'ps/analytic' );
	}
}

/**
 * Shows the ads.
 */
if ( ! function_exists( 'show_ads' ))
{
	function show_ads()
	{
		// get ci instance
		$CI =& get_instance();

		$CI->load->view( 'ps/ads' );
	}
}

/**
 * Shows the breadcrumb.
 *
 * @param      <type>  $urls   The urls
 */
if ( ! function_exists( 'show_breadcrumb' )) 
{
	function show_breadcrumb( $urls = array() )
	{
		// get ci instance
		$CI =& get_instance();

		$template_path = $CI->config->item( 'be_view_path' );

		// load breadcrumb
		$CI->load->view( $template_path .'/partials/breadcrumb', array( 'urls' => $urls )); 
	}
}

/**
 * Shows the breadcrumb.
 *
 * @param      <type>  $urls   The urls
 */
if ( ! function_exists( 'show_breadcrumb_language' )) 
{
	function show_breadcrumb_language( $urls = array() )
	{
		// get ci instance
		$CI =& get_instance();

		$template_path = $CI->config->item( 'be_view_path' );

		// load breadcrumb
		$CI->load->view( $template_path .'/partials/breadcrumb_language', array( 'urls' => $urls )); 
	}
}

/**
 * Shows the data.
 *
 * @param      <type>  $string  The string
 */
if ( ! function_exists( 'show_data' )) 
{
	function show_data( $string )
	{
		// get ci instance
		$CI =& get_instance();
		$CI->load->library( 'PS_Security' );

		return $CI->ps_security->clean_output( $string );
	}
}

/**
 * Determines if view exists.
 *
 * @param      <type>   $path   The path
 *
 * @return     boolean  True if view exists, False otherwise.
 */
if ( ! function_exists( 'is_view_exists' )) 
{
	function is_view_exists( $path )
	{
		return file_exists( APPPATH .'views/'. $path .'.php' );
	}
}

/**
 * Gets the dummy photo.
 *
 * @return     <type>  The dummy photo.
 */
if ( ! function_exists( 'get_dummy_photo' )) 
{
	function get_dummy_photo()
	{
		return "default_news.jpeg";
	}
}

/**
 * Gets the configuration.
 *
 * @param      <type>  $key    The key
 *
 * @return     <type>  The configuration.
 */
if ( ! function_exists( 'get_app_config' )) 
{
	function get_app_config( $key )
	{
		// get ci instance
		$CI =& get_instance();

		$CI->load->model( 'About' );
		$abt = $CI->About->get_one( 'abt1' );

		if ( isset( $abt->{$key} )) {
			return $abt->{$key};
		}

		return false;
	}
}

/**
 * Image URL Path
 *
 * @param      <type>  $path   The path
 *
 * @return     <type>  ( description_of_the_return_value )
 */
if ( ! function_exists( 'img_url' ))
{
	function img_url( $path = false )
	{
		return base_url( '/uploads/'. $path );
	}
}

/**
 * Gets the default photo.
 *
 * @param      <type>  $id     The identifier
 * @param      <type>  $type   The type
 */
if ( ! function_exists( 'get_default_photo' ))
{
	function get_default_photo( $id, $type )
	{
		$default_photo = "";

		// get ci instance
		$CI =& get_instance();

		// get all images
		$img = $CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type, 'ordering' => '1'))->result();

		$img1 = $CI->Image->get_all_by( array( 'img_parent_id' => $id, 'img_type' => $type))->result();

		if ( count( $img ) > 0 ) {
		// if there are images for news,
			
			$default_photo = $img[0];
		} elseif ( count( $img1 ) > 0) {
			$default_photo = $img1[0];
		} else {
		// if no image, return empty object

			$default_photo = $CI->Image->get_empty_object();
		}

		return $default_photo;
	}
}


/**
 * Gets the generate_random_string
 *
 * @param      <type>  $id     The identifier
 * @param      <type>  $type   The type
 */
if ( ! function_exists( 'generate_random_string' ))
{
	function generate_random_string($length = 5) {
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
}

/**
	Global User Ban or Delete Checking
 */
if ( ! function_exists( 'global_user_check' )) 
{
	function global_user_check( $user_id )
	{
		// get ci instance
		$CI =& get_instance();

		$CI->load->model( 'User' );
		$conds['user_id'] = $user_id;
		$user_data = $CI->User->get_one_by($conds);
		$is_ban = $user_data->is_banned;

		if ($user_data == "") {
			$CI->error_response( get_msg( 'err_user_not_exist' ), 400);
		} elseif ($is_ban == '1') {
			$CI->error_response( get_msg( 'user_banned' ), 500);
		}

		return true;
	}
}

/**
	lat lng checking
 */
if ( ! function_exists( 'location_check' )) 
{
	function location_check( $lat, $lng )
	{

		// get ci instance
		$CI =& get_instance();

		if ($lat == "0.0" || $lng == "0.0") {
			$CI->error_response( get_msg( 'err_lat_lng' ), 400);
		}elseif ($lat < -90 || $lat > 90) {
			$CI->error_response( get_msg( 'lat_invlaid' ), 400);
		}elseif ($lng < -180 || $lng > 180){
			$CI->error_response( get_msg( 'lng_invlaid' ), 400);
		}	

		return true;
	}
}

/**
* Deep linking output short url
*/
if ( ! function_exists( 'deep_linking_shorten_url' ))
{
	function deep_linking_shorten_url ($description,$title,$img,$id) {
		// get ci instance
		$CI =& get_instance();
		
		// check description length
		if(strlen($description)>6605){
			$description = substr($description, 0, 6605);
		}

		$longUrl = $CI->Backend_config->get_one('be1')->dyn_link_deep_url.$id;
	  
		//Web API Key From Firebase   
		$key = $CI->Backend_config->get_one('be1')->dyn_link_key;

		//Firebase Rest API URL 
		$url = $CI->Backend_config->get_one('be1')->dyn_link_url . $key;

		//To link with Android App, so need to provide with android package name
		$androidInfo = array(
		    "androidPackageName" => $CI->Backend_config->get_one('be1')->dyn_link_package_name
		);
		
		//For iOS

		$iOSInfo = array(
		   "iosBundleId" => $CI->Backend_config->get_one('be1')->ios_boundle_id ,
		   "iosAppStoreId" => $CI->Backend_config->get_one('be1')->ios_appstore_id
		);

		//For meta data when share the URL 
		$socialMetaTagInfo = array(
		    "socialDescription" => $description,
		    "socialImageLink"   => $img,
		    "socialTitle"       => $title
		);
		
		//For only 4 character at url 
		$suffix = array(
		    "option" => "SHORT"
		);

		$data = array(
		     "dynamicLinkInfo" => array(
		        "dynamicLinkDomain" => $CI->Backend_config->get_one('be1')->dyn_link_domain,
		        "link" => $longUrl,
		        "androidInfo" => $androidInfo,
		        "iosInfo" => $iOSInfo,
		        "socialMetaTagInfo" => $socialMetaTagInfo
		     ),
		     "suffix" => $suffix
		);

		$headers = array('Content-Type: application/json');

		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POST, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );

		$data = curl_exec ( $ch );
		curl_close ( $ch );

		$short_url = json_decode($data);
		  
		if(isset($short_url->error)){
		    //return $short_url->error->message;
		    return $short_url->error->message;
		} else {
		    //return $short_url->shortLink;
		    return $short_url->shortLink;
		}



	}
}


	/**
* Sending Message From FCM For Android
*/

if ( ! function_exists( 'send_android_fcm' ))
{
	function send_android_fcm( $registatoin_ids, $data, $platform_names) 
    {
    	// print_r($registatoin_ids);die;
    	// get ci instance
		$CI =& get_instance();
    	$message = $data['message'];
		$flag = $data['flag'];
		$id = $data['item_id'];

		$title = $CI->Item->get_one($id)->title;
		$item_name = str_replace(' ' , '%20' , $title);
		$item_approval_name = str_replace(' ' , '-' , $title);

		$conds_img['img_parent_id'] = $id;
		$conds_img['img_type'] = "item";
		$conds_img['ordering'] = '1';

		$images = $CI->Image->get_all_by($conds_img)->result();
		$img_path = $images->img_path;

		if (count($images) == 0) {
			$conds_img1['img_parent_id'] = $id;
			$conds_img1['img_type'] = "item";

			$images1 = $CI->Image->get_all_by($conds_img1)->result();

			if (count($images1) == 1) {
				$img_path = $images1->img_path;
			} else {
				$img_path = $images1[0]->img_path;
			}
		}

		$price = $CI->Item->get_one($id)->price;

		$item_currency_id = $CI->Item->get_one($id)->item_currency_id;
		$currency = $CI->Currency->get_one($item_currency_id)->currency_symbol;
		
		//to get prj name
		$dyn_link_deep_url = $CI->Backend_config->get_one('be1')->dyn_link_deep_url;
		$prj_url = explode('/', $dyn_link_deep_url);
		$i = count($prj_url)-2;
		$prj_name = $prj_url[$i];

		for ($i=0; $i <count($platform_names) ; $i++) { 
    		//print_r($platform_names[$i]);
    		$click_action = "";
    		$currency_tmp =  '&currency=';
    		$currency_tmp = htmlentities($currency_tmp);
    		
    		if ($click_action =="" && $platform_names[$i] == "frontend" && $flag == "event_reminder") {

    			//for chat

    			$click_action = $prj_name. '/' . 'chat?buyer_user_id=' . $data['buyer_user_id'] . '&seller_user_id=' . $data['seller_user_id'] . '&item_name=' .
    			$item_name . '&item_id=' . $data['item_id'] . '&item_image_name=' . $img_path . '&item_price=' . $price . $currency_tmp . 
    			$currency . '&condition=' . $condition . '&chat_flag=' . $data['chat_flag'] ;

    		} elseif ($click_action =="" && $platform_names[$i] == "frontend" && $flag == "review") {
    			$click_action = $prj_name. '/' . 'review-list?user_id=' . $data['review_user_id'];
    		} elseif ($click_action =="" && $platform_names[$i] == "frontend" && $flag == "approval") {
    			$click_action = $prj_name. '/' . 'item/' . $item_approval_name . '?item_id=' . $data['item_id'] . '&item_name=' . $item_approval_name ;
    		} elseif ($click_action =="" && $platform_names[$i] == "frontend" && $flag == "follow") {
    			$click_action = "";
    		} elseif ($click_action =="" && $platform_names[$i] == "frontend" && $flag == "verify_agent") {
    			$click_action = "";
    		} elseif ($platform_names[$i] == "android" || $platform_names[$i] == "IOS") {
    			$click_action = "FLUTTER_NOTIFICATION_CLICK";
    		}


    	}

    	//Google cloud messaging GCM-API url
    	$url = 'https://fcm.googleapis.com/fcm/send';

    	if ($flag == 'approval' || $flag == 'verify_agent' || $flag == 'follow') {
    		// - Testing Start
			$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $message,
	    		'sound' => 'default',
	    		'message' => $message,
	    		'flag' => $flag,
		    	'click_action' => $click_action
	    	);


	    	$fields = array(
	    		'sound' => 'default',
	    		'notification' => $noti_arr,
	    	    'registration_ids' => $registatoin_ids,
	    	    'data' => array(
	    	    	'message' => $message,
	    	    	'flag' => $flag,
	    	    	'click_action' => $click_action
	    	    )

	    	);
    	} elseif ($flag == 'review') {

    		$rating = $data['rating'];

    		$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $message,
	    		'sound' => 'default',
	    		'message' => $message,
	    		'flag' => 'review',
	    		'rating' => $rating,
		    	'click_action' => $click_action
	    	);

	    	$fields = array(
	    		'sound' => 'default',
	    		'notification' => $noti_arr,
	    	    'registration_ids' => $registatoin_ids,
	    	    'data' => array(
	    	    	'message' => $message,
	    	    	'rating' => $rating,
	    	    	'flag' => 'review',
	    	    	'click_action' => $click_action
	    	    )

	    	);
    	} else if ($flag == 'event_reminder') {

    		$message = $data['message'];
			$buyer_id = $data['buyer_user_id'];
			$seller_id = $data['seller_user_id'];
			$sender_name = $data['sender_name'];
			$item_id = $data['item_id'];
			$sender_profle_photo = $data['sender_profle_photo'];



			$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $message,
	    		'sound' => 'default',
	    		'message' => $message,
		    	'flag' => $flag,
		    	'buyer_id' => $buyer_id,
		    	'seller_id' => $seller_id,
		    	'item_id' => $item_id,
		    	'sender_name' => $sender_name,
		    	'sender_profle_photo' => $sender_profle_photo,
		    	'action' => "abc", //hardcoded value
		    	'click_action' => $click_action
	    	);
	    	// - Testing End

			// print_r($registatoin_ids); die;
	    	$fields = array(
	    		'sound' => 'default',
	    		'notification' => $noti_arr,
	    	    'registration_ids' => $registatoin_ids,
	    	    'data' => array(
	    	    	'message' => $message,
		    		'flag' => $flag,
	    	    	'buyer_id' => $buyer_id,
	    	    	'seller_id' => $seller_id,
	    	    	'item_id' => $item_id,
	    	    	'sender_name' => $sender_name,
	    	    	'sender_profle_photo' => $sender_profle_photo,
	    	    	'action' => "abc", //hardcoded value
	    	    	'click_action' => $click_action
	    	    )

	    	);


    	}

    	else if($flag == 'chat')
    	{
    		$message = $data['message'];
			$buyer_id = $data['buyer_user_id'];
			$seller_id = $data['seller_user_id'];
			$sender_name = $data['sender_name'];
			$item_id = $data['item_id'];
			$sender_profle_photo = $data['sender_profle_photo'];



			$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $message,
	    		'sound' => 'default',
	    		'message' => $message,
		    	'flag' => $flag,
		    	'buyer_id' => $buyer_id,
		    	'seller_id' => $seller_id,
		    	'item_id' => $item_id,
		    	'sender_name' => $sender_name,
		    	'sender_profle_photo' => $sender_profle_photo,
		    	'action' => "abc", //hardcoded value
		    	'click_action' => $click_action
	    	);
	    	// - Testing End

			// print_r($registatoin_ids); die;
	    	$fields = array(
	    		'sound' => 'default',
	    		'notification' => $noti_arr,
	    	    'registration_ids' => $registatoin_ids,
	    	    'data' => array(
	    	    	'message' => $message,
		    		'flag' => $flag,
	    	    	'buyer_id' => $buyer_id,
	    	    	'seller_id' => $seller_id,
	    	    	'item_id' => $item_id,
	    	    	'sender_name' => $sender_name,
	    	    	'sender_profle_photo' => $sender_profle_photo,
	    	    	'action' => "abc", //hardcoded value
	    	    	'click_action' => $click_action
	    	    )

	    	);
    	}

    	// print_r($fields);die;

    	// Update your Google Cloud Messaging API Key
    	//define("GOOGLE_API_KEY", "AIzaSyAzKBPuzGuR0nlvY0AxPrXsEMBuRUxO4WE");
    	$fcm_api_key = $CI->Backend_config->get_one('be1')->fcm_api_key;
    	define("GOOGLE_API_KEY", $fcm_api_key);
    	//define("GOOGLE_API_KEY", $this->config->item( 'fcm_api_key' ));  	
    	
    	//print_r(GOOGLE_API_KEY); die;
    	//print_r($fields); die;
    	$headers = array(
    	    'Authorization: key=' . GOOGLE_API_KEY,
    	    'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);	
    	if ($result === FALSE) {
    	    die('Curl failed: ' . curl_error($ch));
    	}
    	curl_close($ch);

    	return $result;
    }
}

/**
* Sending Message From FCM For Android & iOS By using topics subscribe
*/

if ( ! function_exists( 'send_android_fcm_topics_subscribe' ))
{
	function send_android_fcm_topics_subscribe( $data ) 
    {	
    	$CI =& get_instance();
    	//Google cloud messaging GCM-API url
    	$url = 'https://fcm.googleapis.com/fcm/send';
    	// $fields = array(
    	//     'registration_ids' => $registatoin_ids,
    	//     'data' => $message,
    	// );

    	if ($data['subscribe'] == '0' && $data['push'] == 1) {
    		// push noti
    		$noti_arr = array(
	    		'title' => $data['message'],
	    		'body' => $data['desc'],
	    		'sound' => 'default',
	    		'flag' => 'broadcast'
	    	);

	    	

	    	$noti_data = array(
	    		'sound' => 'default',
	    		'message' => $data['message'],
	    		'flag' => 'broadcast',
	    		'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
	    	);
	    	
	    	$fields = array(
	    		'sound' => 'default',
	    		'flag' => 'broadcast',
	    		'notification' => $noti_arr,
	    		'data' => $noti_data,
	    	    'to' => '/topics/' . $CI->Backend_config->get_one('be1')->topics

	    	);

    	} else {
    		// subscribe noti

    		$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $data['message'],
	    		'item_id' => $data['item_id'],
	    		'sound' => 'default',
	    		'flag' => 'subscribe'
	    	);

	    	

	    	$noti_data = array(
	    		'sound' => 'default',
	    		'message' => $data['message'],
	    		'item_id' => $data['item_id'],
	    		'flag' => 'subscribe',
	    		'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
	    	);
	    	
	    	$fields = array(
	    		'sound' => 'default',
	    		'flag' => 'subscribe',
	    		'notification' => $noti_arr,
	    		'data' => $noti_data,
	    	    'to' => '/topics/' . $data['property_by_id'] . '_MB'

	    	);

	    	//print_r($fields);die;
    	}

    	

    	define("GOOGLE_API_KEY", $CI->Backend_config->get_one('be1')->fcm_api_key);  	
    		
    	$headers = array(
    	    'Authorization: key=' . GOOGLE_API_KEY,
    	    'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);		
    	if ($result === FALSE) {
    	    die('Curl failed: ' . curl_error($ch));
    	}
    	curl_close($ch);
    	return $result;
    }
}

/**
* Sending Message From FCM For Frontend By using topics subscribe
*/
if ( ! function_exists( 'send_android_fcm_topics_subscribe_fe' ))
{
	function send_android_fcm_topics_subscribe_fe( $data, $prj_name ) 
    {
    	
    	$CI =& get_instance();
    	//Google cloud messaging GCM-API url

    	$url = 'https://fcm.googleapis.com/fcm/send';
    	// $fields = array(
    	//     'registration_ids' => $registatoin_ids,
    	//     'data' => $message,
    	// );

    	if ($data['subscribe'] == '0' && $data['push'] == 1) {
    		// push noti

	    	$noti_arr = array(
	    		'title' => $data['message'],
	    		'body' => $data['desc'],
	    		'sound' => 'default',
	    		'flag' => 'fe_broadcast'
	    	);

	    	

	    	$noti_data = array(
	    		'sound' => 'default',
	    		'message' => $data['message'],
	    		'flag' => 'fe_broadcast',
	    		'click_action' => $prj_name. '/' . 'notification'
	    	);
	    	
	    	$fields = array(
	    		'sound' => 'default',
	    		'flag' => 'fe_broadcast',
	    		'notification' => $noti_arr,
	    		'data' => $noti_data,
	    	    'to' => '/topics/' . $CI->Backend_config->get_one('be1')->topics_fe
	    	);


	    } else {
	    	// subscribe noti


    		// to get item name for FE click action

    		$id = $data['item_id'];

			$title = $CI->Item->get_one($id)->title;
			$item_name = str_replace(' ' , '%20' , $title);
			$itm_name = str_replace(' ' , '-' , $title);

    		$click_action = $prj_name. '/' . 'item/' . $itm_name . '?item_id=' . $data['item_id'] . '&item_name=' . $itm_name ;


    		$noti_arr = array(
	    		'title' => get_msg('site_name'),
	    		'body' => $data['message'],
	    		'sound' => 'default',
	    		'flag' => 'subscribe'
	    	);

	    	

	    	$noti_data = array(
	    		'sound' => 'default',
	    		'message' => $data['message'],
	    		'flag' => 'subscribe',
	    		'click_action' => $click_action
	    	);
	    	
	    	$fields = array(
	    		'sound' => 'default',
	    		'flag' => 'subscribe',
	    		'notification' => $noti_arr,
	    		'data' => $noti_data,
	    	    'to' => '/topics/' . $data['property_by_id'] . '_FE'
	    	);

	    	//print_r($fields);die;

	    }

    	define("GOOGLE_API_KEY", $CI->Backend_config->get_one('be1')->fcm_api_key);  	
    		
    	$headers = array(
    	    'Authorization: key=' . GOOGLE_API_KEY,
    	    'Content-Type: application/json'
    	);
    	$ch = curl_init();
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_POST, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    	$result = curl_exec($ch);	
    	if ($result === FALSE) {
    	    die('Curl failed: ' . curl_error($ch));
    	}
    	curl_close($ch);
    	return $result;
    }
}

/**
* Sending Notifications From OneSignal For subscribe
* @param $include_subscription_ids | array
*/
if ( ! function_exists( 'send_one_signal_notification' ))
{
    function send_one_signal_notification($include_subscription_ids)
    {
        $url = 'https://onesignal.com/api/v1/notifications';

        //$apiKey = 'os_v2_app_igfmsmufu5a7pntry2g2eflhp3mnf3aquryedqvicotynr2v3wgnfpoffojnclqtzo3uagdoupoq5jefqf4r67ekjkyt52ax7bbyh3i';
        $apiKey = 'os_v2_app_bmslovaex5hrfcffjfrqohtjqiegrzsqtauu3subt63cabtg7bcif3vgdwev5zluwfs7gz7mmtjmy2m5l6xlfeaxmldsezklr2j7sba';
      
       // $appId = '418ac932-85a7-41f7-b671-c68da215677e';
        $appId = '0b24b754-04bf-4f12-88a5-4963071e6982';

        $headers = [
            'Authorization: Basic ' . $apiKey,
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        $payload = [
            'app_id' => $appId,
            'target_channel' => 'push',
            'headings' => ['en' => 'Pin App Registatoin'],
            'contents' => ['en' => 'We got a new user!'],
            'included_segments' => $include_subscription_ids,
        ];

        // Initialize cURL
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            return 'Error:' . curl_error($ch);
        } else {
            return $response;
        }

        // Close cURL session
        curl_close($ch);
    }
}

if (! function_exists('create_one_signal_player')) {

	function create_one_signal_player($user_id)
	{

		$payload = json_encode([
            "properties" => [
                "language" => "en",
                "timezone_id" => "UTC",
                "lat" => 0,
                "long" => 0,
                "country" => "US",
                "first_active" => time(),
                "last_active" => time()
            ],
            "identity" => [
                "external_id" => $user_id,
            ]
        ]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://api.onesignal.com/apps/0b24b754-04bf-4f12-88a5-4963071e6982/users");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json; charset=utf-8',
			'Authorization: Basic os_v2_app_bmslovaex5hrfcffjfrqohtjqibdcb7kowee2c4f7haslnzzj7sqx3ax2ynhkm6zxxs7y2zifresqimxofkkmdmcl2eiykpfvpalmxi'
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		curl_close($ch);

		if ($response) {
            $data = json_decode($response, true); // Convert JSON to PHP array
            if (isset($data['identity']['onesignal_id'])) {
                $onesignal_id = $data['identity']['onesignal_id'];

                // Store onesignal_id in users table (using CodeIgniter Query Builder)
                $ci = &get_instance();
                $ci->load->database();
                $ci->db->where('user_id', $user_id);
                $ci->db->update('core_users', ['onesignal_id' => $onesignal_id]);

                return true;
            }
        }

		return true;
	}
}