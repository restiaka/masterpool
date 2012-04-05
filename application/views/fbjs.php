  <script>
  <?php $CI = &get_instance();?>
    var APP_APPLICATION_ID = '<?php echo $CI->config->item('APP_APPLICATION_ID');?>';
	var APP_EXT_PERMISSIONS = '<?php echo $CI->config->item('APP_EXT_PERMISSIONS');?>';
  window.fbAsyncInit = function() {
    FB.init({
				appId: APP_APPLICATION_ID, status: true, cookie: true, xfbml: true,
				oauth: true, frictionlessRequests: true, useCachedDialogs: true
			});
	fbApiInitialized = true;
	FB.Canvas.setAutoGrow(91);
	FB.Event.monitor('auth.statusChange', function(session) {
	  if (session && session.status != 'not_authorized') {
		//var userID = session.authResponse['userID']
		if (session.authResponse['accessToken']) {
		  userAuth = 'connected';
		}
	  }else if (session === undefined) {
		userAuth = 'not_connected';
	  }else if (session && session.status == 'not_authorized') {
		userAuth = 'not_connected';
	  }
    });
  };

  function fbRequireLogin(redirectURL)
  {
   var redirectURL = redirectURL || null;
   		 	FB.getLoginStatus(function(response) {
					if (response.session) {
						FB.api(
							  {
								method: 'fql.query',
								query: 'SELECT '+ APP_EXT_PERMISSIONS +' FROM permissions WHERE uid = '+ response.session.uid
							  },
							  function(response){
								  for(i in response[0]) {if(response[0][i] == 0) { fbDialogLogin(redirectURL); break; }};
							  }
							 );
					}else{
					 	fbDialogLogin(dialogType,redirectURL);						
					}
			});
  }
  
  /*
   * TO RECIPIENTS : {message:"My Great Request",to:user_ids}
   * TO MULTI RECIPIENTS : {message:"My Great Request"}
   * TO MULTI RECIPIENTS DEFINED : {message:"My Great Request",suggestions: [uid1, uid2, uid3]}
   */
  function fbDialogRequest(jsonData)
  {
  
    fbEnsureInit(function(){

	  var requestJSON = MergeJSON({method:'apprequests'},jsonData); 
       FB.ui(
				requestJSON,
				function(response) {
						if (response && response.request_ids) {
							var requests = response.request_ids.join(',');
							/*TO DO : AJAX POST REQUEST TO KEEP REQUEST ON DATABASE*/
						} else {
							alert('canceled');
						}
				}
			);
    });
  }
  
  
  /* 
   * To Own Wall:
   * var jsonData =>  {name:'',caption:'{*actor*},description:'',picture:'',link:'',actions:[{ name: 'Get Started', link: 'http://apps.facebook.com/mobile-start/' }]}
   *
   * To Friend Wall
   * var jsonData =>  {to:FRIEND ID,name:'',caption:'{*actor*},description:'',picture:'',link:'',user_message_prompt:'',actions:[{ name: 'Get Started', link: 'http://apps.facebook.com/mobile-start/' }]}
   *
   */
  function fbDialogFeed(jsonData){
    fbEnsureInit(function(){
	   var requestJSON = MergeJSON({method:'feed'},jsonData); 
	   FB.ui(
			  requestJSON,
			  function(response) {
				if (response && response.post_id) {
				  /*POST IS PUBLISHED*/
				} else {
				  /*POST NOT PUBLISHED*/
				}
			  }
			);
	});
  };
 
  function fbEventSubscribe(event)
  { 
   /* event :
	* auth.login -- fired when the user logs in
    * auth.logout -- fired when the user logs out
    * auth.sessionChange -- fired when the session changes
    * auth.statusChange -- fired when the status changes
    * xfbml.render -- fired when a call to FB.XFBML.parse() completes
    * edge.create -- fired when the user likes something (fb:like)
    * edge.remove -- fired when the user unlikes something (fb:like)
    * comment.create -- fired when the user adds a comment (fb:comments)
    * comment.remove -- fired when the user removes a comment (fb:comments)
    * fb.log -- fired on log message
	*/
	fbEnsureInit(function(){
		FB.Event.subscribe(event, function(response) {
			switch (event){ 
			  case "edge.create" : 
				 var qs = getUrlVars(response);
			  break;
			  case "edge.remove" : 
				 var qs = getUrlVars(response);
			  break;
			  case "comment.create" :
				  var commentID = response.commentID;
				  var qs = getUrlVars(response.href);
				  var parentCommentID = response.parentCommentID;
			  break;
			  case "comment.remove" :
				  var commentID = response.commentID;
				  var qs = getUrlVars(response.href);
				  var parentCommentID = response.parentCommentID;
			  break;
			  case "auth.login" :
				  window.top.location.reload();
			  break;
			  case "auth.logout" :
				  window.top.location.reload();
			  break;
			}
		});
	});
  } 
  
  
   function addToPage(redirectURI) {
     fbEnsureInit(function(){		
       FB.ui({
          method: 'pagetab',
          redirect_uri: redirectURI,
        });
      });
	}

  
  function fbEnsureInit(callback) {
    if (!window.fbApiInitialized) {
        setTimeout(function() { fbEnsureInit(callback); }, 50);
    } else {
        if (callback) { callback(); }
    }
  }

  function fbDialogLogin(redirectURL)
  {
   var redirectURL = redirectURL || "";
   var callback = function(response) { if (response.authResponse) { window.top.location.href = redirectURL;} }
   if(redirectURL == "") callback = null;

   fbEnsureInit(function(){
		FB.login(callback, {scope: APP_EXT_PERMISSIONS});	
   });
  }


	function uninstallApp() {
	  fbEnsureInit(function(){		
		  FB.api({method: 'auth.revokeAuthorization'},
			function(response) {
			  window.location.reload();
			});
	  });
	}

	function fbDialogLogout() {
	  fbEnsureInit(function(){		
		  FB.logout(function(response) {
			window.location.reload();
		  });
	  });
	}

  function getUrlVars(obj){
	var vars = [];
	var hash = [];
	var hashes = obj.slice(obj.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}	 
	return vars;
  }
	
  function MergeJSON (o, ob) {
      for (var z in ob) { o[z] = ob[z]; }
      return o;
  }	
  
</script>

