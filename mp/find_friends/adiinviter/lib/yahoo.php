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

class Adi_Service_yahoo_OAuth extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Yahoo.com';
	public $media_key    = 'yahoo';
	public $use_ssl      = false;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $enable_curl_session = false;

	public $consumer_key    = '';
	public $consumer_secret = '';

	public $authorizeTokenURL = 'https://api.login.yahoo.com/oauth2/request_auth';
	public $accessTokenURL    = 'https://api.login.yahoo.com/oauth2/get_token';

	public function getRequestToken()
	{
		global $adiinviter;
		$callback_url = $adiinviter->getCallbackURL('yahoo');
		header("Location: ".$this->authorizeTokenURL.'?client_id='. $this->consumer_key.'&response_type=code&redirect_uri='.urlencode($callback_url)
		);
		exit;
	}

	public function getAccessToken()
	{
		global $adiinviter;
		$access_token = $xoauth_yahoo_guid = '';
		if(!AdiInviterPro::isGET('code')) {
			adi_throwLibError(11); return false;
		}
		else
		{
			$callback_url = $adiinviter->getCallbackURL('yahoo');
			$code = AdiInviterPro::GET('code', ADI_STRING_VARS);
			$post_elements = array(
				'code'          => $code,
				'client_id'     => $this->consumer_key,
				'client_secret' => $this->consumer_secret,
				'redirect_uri'  => $callback_url,
				'grant_type'    => 'authorization_code',
			);
			$headers = array(
				'Authorization' => 'Basic '.base64_encode($post_elements['client_id'].':'.$post_elements['client_secret']),
			);
			$result   = $this->post($this->accessTokenURL, $post_elements, $headers);
			$response = json_decode($result, true);
			if(is_array($response) && isset($response['access_token']))
			{
				$access_token = $response['access_token'];
				$xoauth_yahoo_guid = $response['xoauth_yahoo_guid'];
			}
		}
		if(!empty($access_token))
		{
			$adiinviter->session->set('adi_yahoo_access_token', $access_token);
			$adiinviter->session->set('adi_yahoo_xoauth_yahoo_guid', $xoauth_yahoo_guid);
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
		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		global $adiinviter;
		$adiinviter->session->remove('adi_yahoo_access_token');
		$adiinviter->session->remove('adi_yahoo_xoauth_yahoo_guid');
	}
}


?>