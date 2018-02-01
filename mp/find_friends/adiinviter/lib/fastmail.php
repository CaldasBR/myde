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

class Adi_Service_fastmail extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Fastmail';
	public $media_key       = 'fastmail';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$this->get("https://www.fastmail.com/");
		
		$form_action = "https://www.fastmail.com/";
		$payload = $this->getFormFields($this->res);
		$payload['username']     = $this->user;
		$payload['password']     = $this->password;
		$payload['screenSize']   = 'mobile';
		$payload['hasPushState'] = '1';
		$payload['interface']    = 'ajax';
		$headers = array(
			'referer' => 'https://www.fastmail.com/',
		);
		$this->post($form_action, $payload, $headers, true);

		preg_match('/&u=([^"]+)/i', $this->res, $matches);
		$rand = isset($matches[1]) ? $matches[1] : '';

		if (strpos($this->res, 'https://www.fastmail.com/?interface') === false) 
		{
			return false;
		}
		preg_match('/u=([a-z0-9]+)/i', $this->last_url, $matches);
		$uid = isset($matches[1]) ? $matches[1] : '';
		$this->login_ok1 = 'https://www.fastmail.fm/authenticate/?u=' . $uid;
		$this->login_ok2 = 'https://www.fastmail.fm/export/contacts/?format=OL&download=1&u=' . $uid;

		$form_action = $this->login_ok1;
		$this->post($form_action, '{"action":"getSession"}', array('X-TrustedClient' => 'Yes',), false);
		$response = @json_decode($this->res, true);
		
		$url = $this->login_ok2;
		$this->get($url, false);
		
		if (strstr($this->res, '@') === false) 
		{
			adi_throwLibError(1);
			return false;
		}

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('https://www.fastmail.com/go/logout');
	}
}
?>