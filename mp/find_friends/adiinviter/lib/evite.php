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

class Adi_Service_evite extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Evite';
	public $media_key    = 'evite';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	
	function fetchContacts() 
	{
		$this->get('https://www.evite.com/login/?next=/profile/contacts');

		$form_action = 'https://www.evite.com/login/?next=/profile/contacts';
		$payload = $this->getFormFields();
		$payload['email']    = $this->user;
		$payload['password'] = $this->password;
		$payload['submit']   = 'login';
		$this->post($form_action, $payload, false);

		$this->get($this->last_info['redirect_url'], false);

		$this->get($this->last_info['redirect_url'], false);

		if(strpos($this->res, 'signout') === false)
		{
			return false;
		}

		$headers = array('Referer' => 'http://www.evite.com/profile/contacts');
		$this->get('http://www.evite.com/ajax/profile/contacts/', $headers);

		$result = (array)@json_decode($this->res, true);

		foreach($result as $details)
		{
			$name = isset($details['name']) ?  $details['name'] : '';
			$email = isset($details['email']) ?  $details['email'] : '';
			if(list($key, $value) = adi_parse_contact($name, $email))
			{
				$this->contacts[$key] = $value;
			}
		}
		return $this->contacts;
	}
	
	function endSession() 
	{
		$logout_url = "http://www.evite.com/ajax_logout";
		$this->get($logout_url, false);
	}
}
?>