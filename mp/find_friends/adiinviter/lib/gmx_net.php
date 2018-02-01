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


class Adi_Service_gmx_net extends AdiInviter_Pro_Core
{
	public $version            = 1001;
	public $service_name       = 'Gmx.net';
	public $media_key          = 'gmx_net';
	public $use_ssl            = true;
	public $use_pm             = false;
	public $email_or_id        = 1;
	public $required_parser    = 'csv';
	public $gmx_de_sess_token  = '';
	public $gmx_com_sess_token = '';

	function fetchContacts()
	{
		$this->gmx_net=false;
		if(!in_array($this->domain, array('gmx.com','gmx.us'))) {
			$this->gmx_net = true;
		}

		if($this->gmx_net) 
		{
			$this->get('http://www.gmx.net/');

			$form_action = 'https://service.gmx.net/de/cgi/login?hal=true';
			$payload = $this->getFormFields();
			$payload['loginFailedURL'] = 'http://www.gmx.net/logoutlounge/free_ssl/?status=login-failed&site=gmx&agof=97_L&pg=null&pa=-1&pp=___NULL&region=de';
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

			preg_match('/session=([^&]+)/i', $this->res, $matches);
			$this->gmx_de_sess_token = isset($matches[1]) ? $matches[1] : '';
			if(empty($this->gmx_de_sess_token)) {
				return false;
			}
		}
		else 
		{
			$this->get("http://www.gmx.com/", true);
			$payload = $this->getFormFields();

			$form_action = 'https://login.gmx.com/login';
			$payload['btnLogin'] = 'Log in';
			$payload['username'] = $this->user;
			$payload['password'] = $this->password;
			$this->post($form_action, $payload, true);

			preg_match('/session=([^&]+)/i', $this->res, $matches);
			$this->gmx_com_sess_token = isset($matches[1]) ? $matches[1] : '';
			if(empty($this->gmx_com_sess_token)) {
				return false;
			}
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		if($this->gmx_net) {
			$this->get('https://navigator.gmx.net/logout?sid='.$this->gmx_de_sess_token);
		}
		else {
			$this->get('https://navigator-lxa.gmx.com/logout?sid='.$this->gmx_com_sess_token);
		}
	}
	
}

?>