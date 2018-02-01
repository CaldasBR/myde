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

class Adi_Service_xing_OAuth extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Xing.com';
	public $media_key    = 'xing';
	public $use_ssl      = false;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $enable_curl_session = false;

	public $consumer_key    = '';
	public $consumer_secret = '';

	public $token       = '';
	public $sha1_method = null;
	public $consumer    = null;

	public $requestTokenURL   = 'https://api.xing.com/v1/request_token';
	public $authorizeTokenURL = 'https://api.xing.com/v1/authorize';
	public $accessTokenURL    = 'https://api.xing.com/v1/access_token';

	public function init_params()
	{
		$this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret, NULL);
	}

	public function getRequestToken()
	{
		$this->init_params();
		global $adiinviter;
		$callback_url = $adiinviter->getCallbackURL('xing');
		$res = $this->oAuthRequest($this->requestTokenURL, array(
			'oauth_callback'         => $callback_url,
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_version'          => '1.0',
		));
		$tok = adi_oauth_wrapper::oAuthParseResponse($res);

		$adiinviter->session->set('adi_xing_request_token',  $tok['oauth_token']);
		$adiinviter->session->set('adi_xing_request_secret', $tok['oauth_token_secret']);

		$url = $this->authorizeTokenURL.'?oauth_token='.$tok['oauth_token'].'&oauth_callback='.urlencode($callback_url);
		header("Location: ".$url);
		exit;
	}

	public function getAccessToken()
	{
		$this->init_params();
		global $adiinviter;
		$oauth_verifier = AdiInviterPro::isGET('oauth_verifier')?AdiInviterPro::GET('oauth_verifier',ADI_STRING_VARS):'';
		$request_token  = $adiinviter->session->get('adi_xing_request_token');
		$request_secret = $adiinviter->session->get('adi_xing_request_secret');

		if(AdiInviterPro::isGET('oauth_verifier') && !empty($request_secret) && !empty($oauth_verifier))
		{
			$this->token = new OAuthConsumer($request_token, $request_secret);

			$res   = $this->oAuthRequest($this->accessTokenURL, array('oauth_verifier' => $oauth_verifier));
			$token = adi_oauth_wrapper::oAuthParseResponse($res);
			$tok   = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);

			$adiinviter->session->remove('adi_xing_request_token');
			$adiinviter->session->remove('adi_xing_request_secret');

			$adiinviter->session->set('adi_xing_access_token', $tok->key);
			$adiinviter->session->set('adi_xing_access_secret', $tok->secret);
			$adiinviter->session->set('adi_xing_user_id', $token['user_id']);
			return true;
		}
		return false;
	}

	public function fetchContacts()
	{
		$this->set_as_loggedin();
		return $this->contacts;
	}

	protected function call_JSON($url, $params=array(), $request_method=NULL) 
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

	function endSession()
	{
		global $adiinviter;
		$adiinviter->session->remove('adi_xing_access_token');
		$adiinviter->session->remove('adi_xing_access_secret');
		$adiinviter->session->remove('adi_xing_user_id');
	}
}


?>