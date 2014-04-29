<?php
/**
 * Yahoo strategy for OpAuth
 * Based on http://developer.yahoo.com/oauth/guide/oauth-auth-flow.html
 * 
 * More information on Opauth: http://opauth.org
 * 
 * @copyright Copyright 2013 Valerii Igumentsev (http://facebook.com/ibooper)
 * @link        http://opauth.org
 * @package     Opauth.YahooStrategy
 * @license     MIT License
 */
class YahooStrategy extends OpauthStrategy {

        /**
         * Comulsory parameters
         */
        public $expects = array('key','secret');


        public $optionals = array('appid');
        /**
         * Optional parameters
         */
        public $defaults = array(
                'appid' =>'',
                'redirect_uri' => '{complete_url_to_strategy}int_callback',
        );

        /**
         * Request
         */

        public function __construct($strategy, $env){
                parent::__construct($strategy, $env);
                if (!session_id()) {
					session_start();
				}                 
                require dirname(__FILE__).'/Vendor/YahooOAuthApplication.class.php';
	        
                $this->yauth = new YahooOAuthApplication($this->strategy['key'],$this->strategy['secret'],$this->strategy['appid']);
                
        }      
                
                
        /**
         * Auth request
         */
        public function request(){
       	
	        $OauthRequestToken = $this->yauth->getRequestToken($this->strategy['redirect_uri']);
	         if(!$OauthRequestToken->key)$this->errorExit($OauthRequestToken);	
	        
	        $_SESSION['yahoo']['yahoo_key'] = $OauthRequestToken->key;
	        $_SESSION['yahoo']['yahoo_secret']  = $OauthRequestToken->secret;
	        $_SESSION['yahoo']['yahoo_expires_in'] = $OauthRequestToken->expires_in;
	    
	        $AuthUrl = $this->yauth->getAuthorizationUrl($OauthRequestToken);
	       
	        $this->clientGet($AuthUrl);	
        }

        /**
         * Receives oauth_verifier, requests for access_token and redirect to callback
         */
        public function int_callback(){
	        $token = new YahooOAuthRequestToken($_SESSION['yahoo']['yahoo_key'], $_SESSION['yahoo']['yahoo_secret'], $_SESSION['yahoo']['yahoo_expires_in']);
	        
	        if(!$token->key)$this->errorExit($token);	        
	        
	        $access_token = $this->yauth->getAccessToken($token,$_GET['oauth_verifier']);

	        if(!$access_token->key)$this->errorExit($access_token);
	    
	        $yid = $this->yauth->getGUID();
	        
	        if(empty($yid)){
	        	$error = array(
                    'code' => 'user ID_error',
                    'raw' => 'ailed when attempting to obtain user ID'
              );
              $this->errorCallback($error);
              return false;  

	        
	        }
		        
		    $userInfo = $this->yauth->getProfile($yid);
	        
	        if(empty($userInfo)){
	        	$error = array(
					'code' => 'userinfo_error',
					'message' => 'Failed when attempting to query for user information',
              );
              $this->errorCallback($error);
              return false;  
	        
	        }
	   
	        $profile =$this->recursiveGetObjectVars($userInfo);

			$this->auth = array(
					'uid' => $profile['profile']['guid'],
					'info' => array(),
					'credentials' => array(
						'token' => $this->yauth->token->key,
						'secret' => $this->yauth->token->secret,
						'expires' => date('c', time() + $this->yauth->token->expires_in)
					),
					'raw' => $profile['profile']
				);

				if (!empty($profile['profile']['emails'][0]['handle'])) $this->auth['info']['email'] = $profile['profile']['emails'][0]['handle'];
				if (!empty($profile['profile']['nickname'])) $this->auth['info']['nickname'] = $profile['profile']['nickname'];
				if (!empty($profile['profile']['givenName'])) $this->auth['info']['first_name'] = $profile['profile']['givenName'];
				if (!empty($profile['profile']['familyName'])) $this->auth['info']['last_name'] = $profile['profile']['familyName'];
				if (!empty($profile['profile']['location'])) $this->auth['info']['location'] = $profile['profile']['location'];
				if (!empty($profile['profile']['profileUrl'])) $this->auth['info']['urls']['yahoo'] = $profile['profile']['profileUrl'];
				if (!empty($profile['profile']['image']['imageUrl'])) $this->auth['info']['picture'] = $profile['profile']['image']['imageUrl'];
	        
				$this->callback();
	       
	    }
	    
	    //reload clientGet
	    
	   	public static function clientGet($url, $data = array(), $exit = true) {
			self::redirect($url, $exit);
		}
 
		private function errorExit($obj){
			 $error = array(
                    'code' => $obj->oauth_problem,
                    'raw' => (array)$obj
             );
             $this->errorCallback($error);
             return false;  
			
		}
	    

}
