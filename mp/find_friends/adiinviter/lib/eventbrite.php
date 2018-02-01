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


class Adi_Service_eventbrite_OAuth extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Eventbrite';
	public $media_key    = 'eventbrite';
	public $use_ssl      = false;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $enable_curl_session = false;

	public $consumer_key    = '';
	public $consumer_secret = '';

	public $authorizeTokenURL = 'https://www.eventbrite.com/oauth/authorize';
	public $accessTokenURL    = 'https://www.eventbrite.com/oauth/token';

	function getRequestToken()
	{
		global $adiinviter;
		$callback_url = $adiinviter->getCallbackURL('eventbrite');

		header('Location: '.$this->authorizeTokenURL.'?response_type=code&client_id='.$this->consumer_key);
		exit;
	}

	function getAccessToken()
	{
		global $adiinviter;
		$access_token = '';
		if(!AdiInviterPro::isGET('code')) {
			adi_throwLibError(11); return false;
		}
		else
		{
			$callback_url = $adiinviter->getCallbackURL('eventbrite');
			$code = AdiInviterPro::GET('code', ADI_STRING_VARS);
			$post_elements = array(
				'code'          => $code,
				'client_id'     => $this->consumer_key,
				'client_secret' => $this->consumer_secret,
				// 'redirect_uri'  => $callback_url,
				'grant_type'    => 'authorization_code',
			);
			$headers = array(
				'Content-type: application/x-www-form-urlencoded',
			);
			$result   = $this->post($this->accessTokenURL, $post_elements, $headers);
			$response = json_decode($result, true);
			if(is_array($response) && isset($response['access_token']))
			{
				$access_token = $response['access_token'];
			}
		}
		if(!empty($access_token))
		{
			$adiinviter->session->set('adi_eventbrite_access_token', $access_token);
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

	function fetchContacts()
	{
		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		global $adiinviter;
		$adiinviter->session->remove('adi_eventbrite_access_token');
	}
}

?>