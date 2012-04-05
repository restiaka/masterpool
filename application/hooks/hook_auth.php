<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
 function appAuth()
 { 
	$CI = &get_instance(); 
	$arg = array('appId'=> $CI->config->item('APP_APPLICATION_ID'),'secret'=> $CI->config->item('APP_SECRET_KEY'));
	
	//SETUP FACEBOOK API !!!IMPORTANT!!!
	 $CI->load->library('facebook', $arg);	
	
	//GETTING AUTHORIZED FACEBOOK USER 									
	/* TODO : user re-Auth condition 
	   if(!$CI->session->userdata('user')){
		 $CI->session->set_userdata('user',getAuthorizedUser(true));	
	   } 
	 */
	 
	//HANDLING FACEBOOK REQUEST_IDS
	if($request_ids = fetchRequests()){
		$CI->session->set_userdata('user_request_ids',$request_ids);		
		deleteRequests();
	}
	
	//GET FACEBOOK SIGNED REQUEST
	 $signed_request = $CI->facebook->getSignedRequest();

	//SETUP SIGNED REQUEST COOKIE FOR NEXT REQUEST 
	 if(isset($_REQUEST['signed_request'])){
		@setcookie("fbsr_{$rows['APP_APPLICATION_ID']}",$_REQUEST['signed_request']);
	 }
	 
	//EXTRACT APP_DATA QUERY STRING FOR FACEBOOK PAGE URL REDIRECTION
	 if(isset($signed_request['app_data']) && $signed_request['app_data']){   
	   list($mode,$value) = explode("|",$signed_request['app_data']);
	   switch($mode){
		case 'redirect' : redirect($value);
						  break;
		case 'redirect_media' : redirect(menu_url('media').'?m='.$value);
						  break;				  
	   }
	 }

 }