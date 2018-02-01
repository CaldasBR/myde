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

class Adi_Service_email_it extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Email.it';
	public $media_key       = 'email_it';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	
	function fetchContacts() 
	{
		$username = preg_replace('/@.*/', '', $user);
		$this->setCookie('rbapcpmp=1; ');
		$this->get('http://www.email.it/mail.php');

		$form_action = 'http://www.email.it/mail.php';
		$headers = array(
			'Host' => 'www.email.it',
			'Origin' => 'http://www.email.it',
			'Referer' => 'http://www.email.it/mail.php',
			'Content-Type'=>'application/x-www-form-urlencoded',
		);
		$tst = number_format(microtime(true) * 1000, 0, '.', '');
		$post_elements = array( 
			'xjxfun'=>'checkUser',
			'xjxr'=>$tst,
			'xjxargs[]'=>'S'.$this->user,
			'xjxargs[]'=>'S'.$this->password,
		);
		$this->post($form_action, $post_elements, $headers, true);

		$form_action = adi_get_text_around($this->res, 'legal.email.it', '"', '"', true);
		$post_elements = array(
			'f_user' => $this->username,
			'f_pass' => $this->password,
			'home'   => '',
			'it.infocamere.webmail.logoutUrl' => 'https://legal.email.it/',
			'it.infocamere.webmail.errorUrl'  => 'https://legal.email.it/legal_wm.php',
			'user'      => $this->username,
			'pswd'      => $this->password,
			'username'  => $this->username,
			'password'  => $this->password,
			'LOGIN'     => $this->username,
			'PASSWD'    => $this->password,
			'tempomemo' => 'duesett',
			'language'  => 'it_IT.utf-8',
		);
		$headers['Host'] = 'wm.email.it';
		$this->post($form_action, $post_elements, $headers);
		$subdomain = '';
		preg_match('/sid=([^&]+)/i', $this->res, $matches);
		$sid = isset($matches[1]) ? $matches[1] : '';
		if (strpos($this->res, "menu.php?") === false) {
			return false;
		}

		$this->headers = array('Referer' => 'http://' . $subdomain . '.email.it/webmail/wm_5/addressbook?startp=1&us=' . $us . '&sid=' . $sid . '&folde=&prem=undefined', 'Host' => $subdomain . '.email.it',);
		$this->login_ok = 'http://' . $subdomain . '.email.it/webmail/wm_5/addressbook_export.php?us=' . $us . '&sid=' . $sid . '&tid=1&lid=0&prem=0';
		
		$url = $this->login_ok;
		$this->get($url, $this->headers);
		if (strstr($this->res, '@') === false) {
			adi_throwLibError(1);
			return false;
		}

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}
	
	function endSession() 
	{

	}
}
?>