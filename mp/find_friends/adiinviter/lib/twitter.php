<?php
/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
| AdiInviter Pro (http://www.adiinviter.com)                                                |
+-------------------------------------------------------------------------------------------+
| @license    For full copyright and license information, please see the LICENSE.txt        |
+ @copyright  Copyright (c) 2015 AdiInviter Inc. All rights reserved.                       +
| @link       http://www.adiinviter.com                                                     |
+ @author     AdiInviter Dev Team                                                           +
| @docs       http://www.adiinviter.com/docs                                                |
+ @support    Email us at support@adiinviter.com                                            +
| @contact    http://www.adiinviter.com/support                                             |
+-------------------------------------------------------------------------------------------+
| Do not edit or add to this file if you wish to upgrade AdiInviter Pro to newer versions.  |
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/


require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'OAuth.php');

class Adi_Service_twitter_OAuth extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'twitter.com';
	public $media_key    = 'twitter';
	public $use_ssl      = false;
	public $use_pm       = true;
	public $email_or_id  = 0;
	public $enable_curl_session = false;

	public $consumer_key    = '';
	public $consumer_secret = '';
	public $authorized_verifier = '';

	public $token       = '';
	public $sha1_method = null;
	public $consumer    = null;

	public $accessTokenURL    = 'https://api.twitter.com/oauth/access_token';
	public $requestTokenURL   = 'https://api.twitter.com/oauth/request_token';
	public $authorizeTokenURL = 'https://api.twitter.com/oauth/authorize';

	public function init_params()
	{
		$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
	}

	public function getRequestToken()
	{
		$this->init_params();

		global $adiinviter;
		$callback_url = $adiinviter->getCallbackURL('twitter');

		$res = $this->oAuthRequest($this->requestTokenURL, array( 'oauth_callback' => $callback_url));
		$tok = adi_oAuthParseResponse($res);
		if(isset($tok['oauth_token']) && !empty($tok['oauth_token']))
		{
			$adiinviter->session->set('adi_twitter_request_token',  $tok['oauth_token']);
			$adiinviter->session->set('adi_twitter_request_secret', $tok['oauth_token_secret']);
			$screen_name = isset($_GET['screen_name']) ? $_GET['screen_name'] : '';
			$url = $this->authorizeTokenURL.'?oauth_token='.$tok['oauth_token'].'&oauth_callback='.urlencode($callback_url).'&force_login=true&screen_name'.$screen_name;
			header("Location: ".$url);
			exit;
		}
		else {
			echo 'Failed to get Twitter Request Token';
			exit;
		}
		return false;
	}

	public function getAccessToken()
	{
		$this->init_params();

		global $adiinviter;
		$access_token = $xoauth_yahoo_guid = '';
		if(!AdiInviterPro::isGET('oauth_verifier')) {
			adi_throwLibError(11); return false;
		}
		else
		{
			$callback_url = $adiinviter->getCallbackURL('twitter');
			$oauth_verifier = AdiInviterPro::GET('oauth_verifier', ADI_STRING_VARS);

			$oAuthToken = $adiinviter->session->get('adi_twitter_request_token');
			$oAuthTokenSecret = $adiinviter->session->get('adi_twitter_request_secret');

			$this->authorized_verifier = $oauth_verifier;

			$this->token = new OAuthConsumer($oAuthToken, $oAuthTokenSecret);
			$method = 'GET';
			$r = $this->oAuthRequest($this->accessTokenURL, array('oauth_verifier'=> $oauth_verifier) );
			$token = adi_oAuthParseResponse($r);
			$tok = new OAuthConsumer($token['oauth_token'],$token['oauth_token_secret']);

			$access_token = $tok->key;		
			$access_secret = $tok->secret;
			$this->token = new OAuthConsumer($access_token, $access_secret);

			$url = 'https://api.twitter.com/1.1/application/rate_limit_status.json';
			$params = array('resources' => 'direct_messages');
			$ttt = $this->call_JSON($url, $params, 'GET');
			$resources = isset($ttt['resources']) ? $ttt['resources'] : array();
			$direct_messages_lmt = isset($resources['direct_messages']) ? $resources['direct_messages'] : array();
			$new_msgs_limit = isset($direct_messages_lmt['/direct_messages']) ? $direct_messages_lmt['/direct_messages']['limit']+0 : 0;
			$adiinviter->session->set('adi_twitter_send_limit',  $new_msgs_limit);
		}
		if(!empty($access_token))
		{
			$adiinviter->session->set('adi_twitter_access_token',  $access_token);
			$adiinviter->session->set('adi_twitter_access_secret', $access_secret);
			return true;
		}
		else if(empty($access_token))
		{
			adi_throwLibError(12); return false;
		}
		else if(AdiInviterPro::isGET('error'))
		{
			return 'OAuth Error : '.AdiInviterPro::GET('error', ADI_STRING_VARS);
		}
		return false;
	}

	public function fetchContacts()
	{
		$this->init_params();
		$this->set_as_loggedin();
		return $this->contacts;
	}

	function call_JSON($url, $params=array(), $request_method=NULL) 
	{
		$res = $this->oAuthRequest($url, $params, $request_method);
		return adi_oauth_wrapper::parseJSON($res, true);
	}

	function oAuthRequest($url, $args=array(), $method=NULL)
	{
		$method = 'GET';
		$req = OAuthRequest::from_consumer_and_token(
			$this->consumer,
			$this->token,
			$method,
			$url,
			$args);
		$req->sign_request($this->sha1_method, $this->consumer, $this->token);
		return $this->get($req->to_url());
	}

	public $access_token = '';
	public $access_secret = '';
	public $messages_quota = '';
	function sendInvitations($subject, $body, $receivers_data)
	{
		$this->init_params();
		global $adiinviter;
		$access_token  = $adiinviter->session->get('adi_twitter_access_token');
		$access_secret = $adiinviter->session->get('adi_twitter_access_secret');
		$new_msgs_limit = $adiinviter->session->get('adi_twitter_send_limit')+0;

		if(empty($access_token) || !$adiinviter->session->is_set('adi_twitter_access_token'))
		{
			$access_token   = $this->access_token;
			$access_secret  = $this->access_secret;
			$new_msgs_limit = $this->messages_quota;
		}

		$this->token = new OAuthConsumer($access_token, $access_secret);

		foreach($receivers_data as $id => $vars)
		{
			$temp_body = adi_replace_vars($body, $vars);
			$temp_subject = adi_replace_vars($subject, $vars);

			$url = 'https://api.twitter.com/1.1/direct_messages/new.json';
			$args = array(
				'user_id'    => $id ,
				'text'       => $temp_body,
				'wrap_links' => 'true',
			);
			$method = 'POST';
			$req = OAuthRequest::from_consumer_and_token(
				$this->consumer,
				$this->token,
				$method,
				$url,
				$args
			);
			$req->sign_request($this->sha1_method, $this->consumer, $this->token);
			$params = $req->get_parameters();
			$res = $this->post($url,$params,false,true);
		}
		return true;
	}

	function get_invites_quota()
	{
		global $adiinviter;
		$new_msgs_limit = $adiinviter->session->get('adi_twitter_send_limit')+0;
		return $new_msgs_limit;
	}

	function get_sendmail_details()
	{
		global $adiinviter;
		$access_token  = $adiinviter->session->get('adi_twitter_access_token');
		$access_secret = $adiinviter->session->get('adi_twitter_access_secret');
		return array(
			'acc_tok' => $access_token,
			'acc_sec' => $access_secret,
		);
	}

	function endSession()
	{
		global $adiinviter;
		$adiinviter->session->remove('adi_twitter_request_token');
		$adiinviter->session->remove('adi_twitter_request_secret');
		$adiinviter->session->remove('adi_twitter_access_token');
		$adiinviter->session->remove('adi_twitter_access_secret');
	}
}


?>