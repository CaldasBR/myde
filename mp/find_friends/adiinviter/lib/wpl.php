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

class Adi_Service_wpl extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'WPL';
	public $media_key       = 'wpl';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$this->get('http://profil.wp.pl/mlogin_poczta.html', true);

		$form_action = 'https://profil.wp.pl/mlogin_poczta.html?idu=128';
		$payload = $this->getFormFields();
		$payload['login_username'] = $this->username;
		$payload['login_password'] = $this->password;
		$payload['_action'] = 'login';
		$this->post($form_action, $payload, true);

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('http://kontakty.wp.pl/logoutgwt.html');
	}
}
?>