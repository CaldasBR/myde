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

class Adi_Service_web_de extends AdiInviter_Pro_Core
{
	public $version           = 2001;
	public $service_name      = 'Web.de';
	public $media_key         = 'web_de';
	public $use_ssl           = true;
	public $use_pm            = false;
	public $email_or_id       = 1;
	public $required_parser   = 'csv';
	public $web_de_sess_token = '';

	protected $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';
	
	function fetchContacts() 
	{
		$this->get('http://web.de/', true);

		$payload = $this->getFormFields();

		$form_action = 'https://login.web.de/intern/login?hal=true';
		$payload['loginFailedURL'] = 'http://web.de/logoutlounge/fm?status=login-failed&site=webde&region=de&agof=97_L&pg=null&pa=-1&pp=___NULL';
		$payload['username'] = $this->user;
		$payload['password'] = $this->password;
		$payload['uinguserid'] = '';

		$this->post($form_action, $payload, false);

		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], false);
		}
		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], false);
		}
		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], false);
		}
		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], false);
		}
		if(!empty($this->last_info['redirect_url'])) {
			$this->get($this->last_info['redirect_url'], false);
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$this->get('https://navigator.web.de/logout?sid='.$this->web_de_sess_token);
	}
}

?>