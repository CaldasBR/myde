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

class Adi_Service_yandex extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Yandex';
	public $media_key    = 'yandex';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $required_parser = 'csv';
	
	function fetchContacts() 
	{
		$this->get('https://passport.yandex.ru/passport?mode=auth', true);
		$post_elements = $this->getFormFields($this->res);
		
		$tst = number_format(microtime(true) * 1000, 0, '.', '');
		$form_action = "https://passport.yandex.ru/passport?mode=auth";
		$post_elements["login"] = $this->user;
		$post_elements["passwd"] = $this->password;
		$post_elements['timestamp'] = $tst;
		$headers=array(
			'Host'    => 'passport.yandex.ru',
			'Origin'  => 'http://mail.yandex.ru',
			'Referer' => 'http://mail.yandex.ru/',
		);
		$this->post($form_action, $post_elements, $headers);
		
		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get(urldecode("http://passport.yandex.ru/passport?mode=logout"));
	}
}
?>