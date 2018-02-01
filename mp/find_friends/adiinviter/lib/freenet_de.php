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


class Adi_Service_freenet_de extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Freenet';
	public $media_key       = 'freenet_de';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$this->get('https://www.freenet.de/loginFrame/index.html');

		$payload = $this->getFormFields();
		$form_action = 'https://auth.freenet.de/portal/login.php';
		$payload['username'] = $this->user;
		$payload['password'] = $this->password;
		$this->post($form_action, $payload, true);

		if(strpos($this->res, '403 Forbidden') !== false) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$this->get('https://webmail.freenet.de/login/logoff.html?code=1011');
	}
}

?>