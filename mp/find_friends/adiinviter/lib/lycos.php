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

class Adi_Service_lycos extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Lycos';
	public $media_key    = 'lycos';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	public $required_parser = 'vcf';

	public $userAgent = 'Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.122 Mobile Safari/537.36';

	function fetchContacts() 
	{
		$url = 'http://www.mail.lycos.com/';
		$this->get($url);

		$form_action = 'http://www.mail.lycos.com/service/login';
		$payload = $this->getFormFields();
		$payload['m_U'] = $this->user;
		$payload['m_P'] = $this->password;
		$payload['login'] = 'Login';

		$headers = array(
			'Host'    => 'www.mail.lycos.com',
			'Origin'  => 'http://www.mail.lycos.com',
			'Referer' => 'http://www.mail.lycos.com/',
		);
		curl_setopt($this->curl, CURLOPT_MAXREDIRS, 7);
		$this->post($form_action, $payload, $headers, true);

		if(strpos($this->res, '_task=logout') === false) {
			return false;
		}

		$url = 'https://webmail.lycos.com/?_task=addressbook&_action=export';
		$this->get($url);

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get("https://webmail.lycos.com/?_task=logout", true);
	}
}
?>