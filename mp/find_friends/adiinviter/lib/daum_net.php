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

class Adi_Service_daum_net extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Daum';
	public $media_key    = 'daum_net';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function fetchContacts() 
	{
		$headers = array(
			'Origin'  => 'http://login.daum.net',
			'Host'    => 'logins.daum.net',
			'Referer' => 'http://login.daum.net/accounts/loginform.do?daumauth=1&service=hanmail&url=http%3A%2F%2Fmail2.daum.net%2Fhanmailex%2Fmobile%2Fbasic%2FTop.daum'
		);
		$form_action = 'https://logins.daum.net/accounts/login.do';
		$payload = array(
			'url'        => 'http://mail2.daum.net/hanmailex/mobile/basic/Top.daum',
			'finaldest'  => '',
			'reloginSeq' => '0',
			'relative'   => '',
			'id'         => $this->user,
			'pw'         => $this->password,
		);
		$this->post($form_action, $payload, $headers, true, false);

		if (strstr($this->res, 'document.location.replace') === false) 
		{
			return false;
		}

		$url = 'http://addrbook.daum.net/aplus/web/top.do';
		$this->get($url);

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$logout_url = "https://logins.daum.net/accounts/logout.do?url=http%3A%2F%2Fwww.daum.net%2F%3Fnil_profile%3Dlogout";
		$this->get($logout_url, true);
	}
}
?>