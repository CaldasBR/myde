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

class Adi_Service_interia extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Interia';
	public $media_key       = 'interia';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	
	function fetchContacts()
	{
		$this->get("https://poczta.interia.pl", true);

		$adiaction = str_replace('&amp;', '&', adi_get_text_around($this->res, 'poczta/zaloguj', '"', '"', true));		
		$payload = $this->getFormFields();
		$payload['email'] = $this->user;
		$payload['pass'] = $this->password;
		$this->post($adiaction, $payload, true);

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get('http://webmail.iol.pt/?_task=logout', true);
	}
}

?>