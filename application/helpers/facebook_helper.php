<?php
function requireLogin($redirect_uri=''){
   $CI = &get_instance();
   $CI->load->library('facebook');

//Get Login Url for redirection if user not yet authorized your apps
$loginUrl = $CI->facebook->getLoginUrl(array(
'scope' => $CI->config->item('APP_EXT_PERMISSIONS'),
'redirect_uri' => $redirect_uri
));
  //Check for facebook session , redirect to Login Url for unauthorized user
if (!$user = getAuthorizedUser()){
echo "<script>window.top.location.href = '$loginUrl';</script>";
echo "<a href='$loginUrl' style='font-weight:bold;font-size:15px;'>Click here if you're not redirected</a>";
exit;
}
}
 
 
 function fetchRequests(){
   $CI = &get_instance();
   $CI->load->library('facebook');
   if(!$CI->input->get_post('request_ids')) return null;
   $request_ids = explode(',', $CI->input->get_post('request_ids'));
   $data = array();
   foreach ($request_ids as $request_id)
   {
      $full_request_id = $request_id . '_' . $user_id;
      try { $data[] = $CI->facebook->api("/$full_request_id");}
      catch (Exception $e) {}
   }
   return $data;
 }

 function deleteRequests(){
   $CI = &get_instance();
   $CI->load->library('facebook');
   if(!$CI->input->get_post('request_ids')) return;
   $request_ids = explode(',', $CI->input->get_post('request_ids'));
   $user_id = $CI->facebook->getUser();
   foreach ($request_ids as $request_id)
   {
      $full_request_id = $request_id . '_' . $user_id;
      try {$facebook->api("/$full_request_id",'DELETE');}
      catch (Exception $e) {}
   }
   return;
 }
 
 /*
*
* Feed array
* Array('message'=>'','link'=>'',picture'=>'','name'=>'','caption'=>'','description'=>'','actions'=>'')
* message and link are required.
*/
 function feedCreate(Array $feed){
   if(!isset($feed['message']) || !isset($feed['link'])) return false;
   $CI = &get_instance();
   $CI->load->library('facebook');
   try {
   $CI->facebook->api("me/feed","post",$feed);
   return true;
   }catch (Exception $e) { return false;}
 }
 
 function getAppAccessToken($app_config = array()){
    $parameter = array('client_id' => $app_config['app_id'],
						'client_secret' => $app_config['app_secret'],
						'grant_type' => 'client_credentials');
	try{
		$request = graph_request('/oauth/access_token', 'GET',$parameter,true,false);
		parse_str($request);
	} catch (Exception $e){ return NULL; }
	
	$request = $request ? $access_token : NULL;
	return $request;
 }
 

 
 function isFan(){
   $CI = &get_instance();
   $CI->load->library('facebook');
   $sr = $CI->facebook->getSignedRequest();
   if(!$sr['page']['liked'])return false;
   return true;
 }
 
 function user_isFan($pageID = null){
   $CI = &get_instance();
   $CI->load->library('facebook');
   $isFan = false;
   if(!$pageID){
if($pages = getFacebookPage()){
$pageID = $pages['id'];
}else{
return false;
}
   }
    try{
$isFan = $CI->facebook->api(array(
"method" => "pages.isFan",
"page_id" => $pageID,
"uid" => $CI->facebook->getUser()
));
} catch (Exception $e){ return false; }

return $isFan === TRUE ? true : false;
 }
 
 function getFacebookPage(){
  $CI = &get_instance();
  $CI->load->library('facebook');
  $regx = $CI->config->item('facebook_page_url_format');
  $url = $CI->config->item('APP_FANPAGE');
  
	if($url = parse_url($url)){
	$new_url = $url['scheme']."://".$url['host'].$url['path'];
		foreach($regx as $key => $pattern){
			if(preg_match($pattern, $new_url, $matches)){
				switch($key){
					case 'standard': $content = $CI->facebook->api('/'.$matches[2]);
					return $content;
					break;
					case 'custom' : $content = $CI->facebook->api('/'.$matches[1]);
					return $content;
					break;
				}
				break;
			}
		}
	}
  return null;
 }
 
 /*
*
* http://developers.facebook.com/docs/reference/api/application/
*
*
*/
 function getAppDetail($appid,$app_accesstoken,$fields = array()){
 
  if(!$fields){
$fields = array('id','name','link','canvas_name','namespace','logo_url','restrictions',
'app_domains','canvas_url','contact_email','creator_uid','page_tab_default_name',
'page_tab_url','privacy_policy_url','secure_canvas_url','secure_page_tab_url',
'website_url');
  }
 
   $parameter = array( 'fields' => implode(',',$fields),
'access_token' => $app_accesstoken );
  try{
$request = graph_request('/'.$appid, 'GET',$parameter,true,true);
}catch(Exception $e){
return null;
}
return $request;
}

function getAppByIDS($appid,$appsecret){
   if($access_token = getAppAccessToken(array('app_id' => $appid,'app_secret'=> $appsecret))){
     return getAppDetail($appid,$access_token);
   }else{
return null;
   }
}
/*
location = Restriction based on location, such as 'DE' for apps restricted to Germany
age = Minimum age restriction
age_distribution = Restriction based on an age range
id = App ID read-only field.
type = Always application for apps; read-only field.
*/
function setAppRestriction($appid,$app_accesstoken,$fields = array()){
  if(!$fields){
$fields = array("age_distribution"=>"21+");
  }
    $parameter = array( 'restrictions' => json_encode($fields),
'access_token' => $app_accesstoken );
  try{
$request = graph_request('/'.$appid, 'POST',$parameter,true,false);
}catch(Exception $e){
return null;
}
//$request = file_get_contents("https://graph.facebook.com/$appid?".http_build_query($parameter, null, '&'));
return $request;
}

function appToPage_dialog_url($appid,$redirecturl){
  return "http://www.facebook.com/dialog/pagetab?app_id=$appid&next=$redirecturl";
}
 
 function graph_request($path,$method = "POST",$args = array(),$ssl = true,$json_decode = true,$debug=false){
   $ch = curl_init();
   $domain = "graph.facebook.com";
   $method = strtoupper($method);
   $url = $ssl ? "https://".$domain.$path : "http://".$domain.$path;
   
    if($method == 'POST'){
curl_setopt($ch, CURLOPT_POST, true);
}elseif($method == 'GET'){
curl_setopt($ch, CURLOPT_HTTPGET, true);
}elseif($method == 'DELETE'){
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPGET, true);
}

    if($args && $method == 'POST')
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args, null, '&'));
elseif($args && $method == 'GET')
     $url .= '?'.http_build_query($args, null, '&');

curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   
curl_setopt($ch, CURLOPT_URL, $url);

$result = curl_exec($ch);
if ($result === false) {
      
curl_close($ch);

curl_error($ch);
return false;
    }

curl_close($ch);



return $json_decode ? json_decode($result,true) : $result;
   }
 
 
 function isAppUser($uid)
 {
     $CI = &get_instance();
   $CI->load->library('facebook');
  
  $facebook = $CI->facebook;
      try{
return $facebook->api(array('method'=>'users.isAppUser','uid'=>$uid));
} catch (Exception $e){
return false;//Got an exception of invalid OAUTH 2.0 token
}
 }
 
 
 /*
*
* Commonly used for mobile authentication
*
*/
 function graphRequireLogin($redirect_uri,$display = 'popup')
 {
     $CI = &get_instance();
   $CI->load->library('facebook');
   $CI->load->model('setting_m');
  
  $facebook = $CI->facebook;
  
    $code = $_REQUEST["code"];
$dialog_options = array (
'client_id' => $CI->setting_m->get('APP_APPLICATION_ID'),
'redirect_uri' => $redirect_uri,
'display' => $display,
'scope' => $CI->setting_m->get('APP_EXT_PERMISSIONS')
);
$token_options = array (
'client_id' => $CI->setting_m->get('APP_APPLICATION_ID'),
'redirect_uri' => $redirect_uri,
'client_secret' => $CI->setting_m->get('APP_SECRET_KEY'),
'code' => $code
);

$dialog_url = "http://www.facebook.com/dialog/oauth?".http_build_query($dialog_options);
$token_url = "https://graph.facebook.com/oauth/access_token?".http_build_query($token_options);
   
   if(!$user = getAuthorizedUser()){
    if(empty($code)){
redirect($dialog_url);
}elseif(isset($code) && !empty($code)){
$access_token = file_get_contents_curl($token_url);
parse_str($access_token);
}
   }

 }
 
 
 function getFacebookUser($uid){
$content = file_get_contents('http://graph.facebook.com/'.$uid);
return json_decode($content);
 }
 
function getAuthorizedUser($permissions = true){
	$CI = &get_instance();
	$CI->load->library('facebook');
	$profile = null;
    try {
		$profile = $CI->facebook->api('/me?fields=id,name,email,birthday,link,first_name,last_name,username,gender');
		if(isset($profile['birthday'])){
			$birthday_date = DateTime::createFromFormat('m/d/Y', $profile['birthday']);
			$now_date = new DateTime(date('Y-m-d'));
			$profile['age'] = (int) $birthday_date->diff($now_date)->format('%y');
		}
		if($permissions){
			$APP_EXT_PERMISSIONS = explode(',',$CI->config->item('APP_EXT_PERMISSIONS'));
			if($data = $CI->facebook->api('/me/permissions')){
				$scopes = $data['data'][0];
				foreach($APP_EXT_PERMISSIONS as $PERMS){
					if(isset($scopes[$PERMS]) && $scopes[$PERMS] == 1) {
						continue;
					}else{
						$profile = null;
						break;
					}
				}
			}
			if($profile) $profile['scope'] = $scopes;
		}
	} catch (Exception $e){}
	return $profile;
 }
 


 function authorizeButton($text = 'Click here to Authorize',$redirectURL = null){
$redirectURL = $redirectURL ? "'".$redirectURL."'" : null;
    return "<a onclick=\"fbDialogLogin(".$redirectURL."); return false;\" class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\">".$text."</span></a>";
 }
 
 function authorizeBanner($image_url = null,$login = true,$redirectURL = null){
	$onclick = $login ? "onclick=\"fbDialogLogin('$redirectURL'); return false;\"" : "";
    $href = $login ? "#" : $redirectURL;
	if($image_url){
		return "<a href=\"$href\" $onclick data-ajax=\"false\"><img src=\"$image_url\" /></a>";
	}else{
	   return "<a href=\"$href\" $onclick data-ajax=\"false\" class=\"fb_button fb_button_medium\"><span class=\"fb_button_text\" >Join The Contest</a>";
	}
 }
 
  function fblike($href,$attr = "show_faces='false' width='430' font=''")
  {
     return "<fb:like href='$href' $attr ></fb:like>";
  }
  
  function fbcomment($href,$attr = "colorscheme='light' width='460' num_posts='5'")
  {
   return "<fb:comments href='$href' $attr ></fb:comments>";
  }