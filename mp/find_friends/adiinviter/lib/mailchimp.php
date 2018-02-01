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


class Adi_Service_mailchimp_OAuth extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Mailchimp.com';
	public $media_key    = 'mailchimp';
	public $use_ssl      = false;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $enable_curl_session = false;

	public $consumer_key    = '';
	public $consumer_secret = '';

	public $authorizeTokenURL = 'https://login.mailchimp.com/oauth2/authorize';
	public $accessTokenURL    = 'https://login.mailchimp.com/oauth2/token';

	function getRequestToken()
	{
		global $adiinviter;
		$callback_url = $adiinviter->getCallbackURL('mailchimp');
		header("Location: ".$this->authorizeTokenURL.'?client_id='.$this->consumer_key.'&hl=en-GB&from_login=1&pli=1&response_type=code&redirect_uri='.urlencode($callback_url)
		);
		exit;
	}

	function getAccessToken()
	{
		global $adiinviter;
		$access_token = $api_endpoint = $dc = '';
		if(!AdiInviterPro::isGET('code')) {
			adi_throwLibError(11); return false;
		}
		else
		{
			$callback_url = $adiinviter->getCallbackURL('mailchimp');
			$code = AdiInviterPro::GET('code', ADI_STRING_VARS);
			$post_elements = array(
				'code'          => $code,
				'client_id'     => $this->consumer_key,
				'client_secret' => $this->consumer_secret,
				'redirect_uri'  => $callback_url,
				'grant_type'    => 'authorization_code',
			);
			$result   = $this->post($this->accessTokenURL, $post_elements);
			$response = json_decode($result, true);
			if(is_array($response) && isset($response['access_token']))
			{
				$access_token = $response['access_token'];
				$headers = array(
					'User-Agent'    => 'oauth2-draft-v10',
					'Host'          => 'login.mailchimp.com',
					'Accept'        => 'application/json',
					'Authorization' => 'OAuth '.$access_token,
				);
				$res = $this->get('https://login.mailchimp.com/oauth2/metadata', $headers);
				$result = json_decode($res, true);
				$api_endpoint = $result['api_endpoint'];
				$dc = $result['dc'];
			}
		}

		if(!empty($access_token) && !empty($api_endpoint) && !empty($dc))
		{
			$adiinviter->session->set('adi_mailchimp_access_token', $access_token);
			$adiinviter->session->set('adi_mailchimp_api_endpoint', $api_endpoint);
			$adiinviter->session->set('adi_mailchimp_dc', $dc);
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
		$adiinviter->session->remove('adi_mailchimp_access_token');
		$adiinviter->session->remove('adi_mailchimp_api_endpoint');
		$adiinviter->session->remove('adi_mailchimp_dc');
	}
}


?>