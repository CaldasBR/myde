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


class Adi_Service_sapo extends AdiInviter_Pro_Core
{
	public $version        = 1001;
	public $service_name   = 'Sapo';
	public $media_key      = 'sapo';
	public $use_ssl        = true;
	public $use_pm         = false;
	public $email_or_id    = 1;
	public $defaultTimeout = 8;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		curl_setopt($this->curl, CURLOPT_SSLVERSION, 3); 

		$this->get('https://login.sapo.pt/Login.do',true);

		$form_action = 'https://login.sapo.pt/Login.do';
		$payload = $this->getFormFields();
		$payload['SAPO_LOGIN_USERNAME'] = $this->user;
		$payload['SAPO_LOGIN_PASSWORD'] = $this->password;
		if(isset($payload['persistent'])) {
			unset($payload['persistent']);
		}
		$headers = array(
			'Host'    => 'login.sapo.pt',
			'Origin'  => 'https://login.sapo.pt',
			'Referer' => 'https://login.sapo.pt/Login.do',
		);
		$this->post($form_action, $payload, $headers, false);

		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], $headers);
		}

		if(strpos($this->res, 'Logout.do') === false) {
			return false;
		}

		$this->get('https://mail.sapo.pt/mail/login.php?ssoAuth&site=mail.sapo.pt', true);

		$form_action = 'https://mail.sapo.pt/mail/services/download/?app=turba&fn=%2Fcontactos.csv';
		$payload = array(
			'actionID' => 'export',
			'exportID' => '100',
			'source'   => 'localsql',
		);
		$headers = array(
			'Host'    => 'mail.sapo.pt',
			'Origin'  => 'https://mail.sapo.pt',
			'Referer' => 'https://mail.sapo.pt/mail/turba/data.php',
		);
		$this->post($form_action, $payload, $headers, false);

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}

	function endSession() 
	{
		
	}
}


?>