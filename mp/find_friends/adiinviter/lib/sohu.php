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

class Adi_Service_sohu extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Sohu.com';
	public $media_key       = 'sohu';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts() 
	{
		$url = 'http://mail.sohu.com/';
		$this->get($url);

		$form_action = 'http://passport.sohu.com/apiv2/login';
		$payload = array(
			'domain'           => $this->domain,
			'callback'         => 'passport2000'.str_shuffle('8528854299802333').'_cb.'.time().rand(200,600),
			'appid'            => '1113',
			'userid'           => $this->user,
			'password'         => md5($this->password),
			'persistentcookie' => '0',
		);
		$this->post($form_action, $payload, true);

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('http://passport.sohu.com/sso/logout.jsp?s='.time().rand(200,600).'&appid=9999', true);
	}
}
?>