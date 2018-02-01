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


class Adi_Service_naver_com extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Naver';
	public $media_key    = 'naver_com';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	public $required_parser = 'csv';

	public $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';

	function fetchContacts()
	{
		$url = 'https://nid.naver.com/nidlogin.login?url=https://contact.naver.com/main.nhn';
		$this->get($url);

		$form_action = 'https://nid.naver.com/nidlogin.login';
		$payload = $this->getFormFields();
		$payload['locale'] = 'en_US';
		$payload['smart_LEVEL'] = '-1';
		$payload['id'] = $this->username;
		$payload['pw'] = $this->password;
		$this->post($form_action, $payload, false);

		$this->get('https://contact.naver.com/main.nhn', true);

		if(strpos($this->res, 'var logoutUrl') === false) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$this->get('https://nid.naver.com/nidlogin.logout?returl=https://contact.naver.com/section.nhn');
	}
}

?>