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

class Adi_Service_terra extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Terra';
	public $media_key       = 'terra';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	
	function fetchContacts() 
	{
		$url = 'http://correo.terra.com/ws/?r=site/csrf&format=json';
		$this->get($url);

		$result = (array) @json_decode($this->res, true);
		$csrf_token = isset($result['CSRF']) ? $result['CSRF'] : '';

		$form_action = 'http://correo.terra.com/ws/?r=site/login&login_capa=1&format=json';
		$payload = array(
			'YII_CSRF_TOKEN' => $csrf_token,
			'LoginForm[username]' => $this->user,
			'LoginForm[password]' => $this->password.'s',
		);
		$this->post($form_action, $payload, true);

		if(empty($csrf_token) || strpos($this->res, 'LoginForm_authorization') !== false) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get("http://correo.terra.com/ws/?r=site/logout", true);
	}
}
?>