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


class Adi_Service_tonline extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 't-online.de';
	public $media_key       = 'tonline';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$url = 'https://email.t-online.de/em';
		$this->get($url, true);

		$form_action = 'https://accounts.login.idm.telekom.com/sso';
		$payload = $this->getFormFields();
		$payload['pw_usr']    = $this->user;
		$payload['pw_pwd']    = $this->password;
		$payload['pw_submit'] = 'Login';

		$this->post($form_action, $payload, true);

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$url_logout = "https://email.t-online.de/ab/srv/session/logout";
		$this->get($url_logout, false);
	}
}

?>