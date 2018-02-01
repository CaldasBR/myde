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

class Adi_Service_o2 extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'O2';
	public $media_key       = 'o2';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts() 
	{
		$form_action = "https://poczta.o2.pl/login.html";
		$post_elements = array(
			'username' => $this->user,
			'password' => $this->password,
		);
		$this->post($form_action, $post_elements, false, true);
		if (strstr($this->res, 'login') !== false) 
		{
			return false;
		}
		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('http://poczta.o2.pl/wylogowano');
	}
}
?>