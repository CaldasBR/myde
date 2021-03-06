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

class Adi_Service_laposte extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Laposte';
	public $media_key       = 'laposte';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts() 
	{
		$url = 'http://www.laposte.net/accueil';
		$this->get($url);

		$form_action = 'https://compte.laposte.net/login.do';
		$payload = array(
			'login'    => $this->user,
			'password' => $this->password,
		);
		$this->post($form_action, $payload, false);

		$url = $this->last_info['redirect_url'];
		if(strpos($url, 'public/preauth.jsp') === false) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get('https://compte.laposte.net/logout.do', true);
	}
}
?>