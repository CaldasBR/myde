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

class Adi_Service_mail_ru extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Mail.ru';
	public $media_key    = 'mail_ru';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	
	function fetchContacts() 
	{
		$this->get("https://m.mail.ru/login", true);
		
		preg_match('/action="([^"]+)"/i', $this->res, $matches);
		$form_action = isset($matches[1]) ? $matches[1] : '';
		$post_elements = $this->getFormFields();
		$post_elements['Login'] = $this->username;
		$post_elements['Domain'] = $this->domain;
		$post_elements['Password'] = $this->password;
		
		$this->post($form_action, $post_elements, true);
		if (strpos($this->res, 'messages/inbox') === false) 
		{
			return false;
		}

		$this->get('https://m.mail.ru/messages/inbox/?back=1', true);

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get("http://win.mail.ru/cgi-bin/logout", true);
	}
}
?>