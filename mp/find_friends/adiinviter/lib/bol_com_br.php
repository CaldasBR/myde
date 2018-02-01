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

class Adi_Service_bol_com_br extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Bol.com.br';
	public $media_key    = 'bol_com_br';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function fetchContacts() 
	{
		$this->get('https://visitante.acesso.uol.com.br/login.html');

		$form_action = 'https://visitante.acesso.uol.com.br/login.html';
		$payload = $this->getFormFields();
		$payload['user'] = $this->user;
		$payload['pass'] = $this->password;
		$this->post($form_action, $payload, true);

		$url = adi_get_text_around($this->res, 'login/doorway', '"', '"', true);
		$this->get($url, false);

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get('http://bmail.uol.com.br/login/signout');
	}
}
?>