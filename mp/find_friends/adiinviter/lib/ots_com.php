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

class Adi_Service_ots_com extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = '126.com';
	public $media_key    = 'ots_com';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $required_parser = 'csv';
	public $ots_session_id = '';
	public $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';

	function fetchContacts() 
	{
		$url = 'http://mail.126.com/';
		$this->get($url);

		$payload = $this->getFormFields();
		$form_action = 'https://mail.126.com/entry/cgi/ntesdoor?df=mail126_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail126&verifycookie=-1&net=failed&style=-1&race=-2_-2_-2_db&uid='.$this->user.'&hid=10010102';
		$payload['username'] = $this->user;
		$payload['password'] = $this->password;
		$this->post($form_action, $payload, true);

		if(strpos($this->res, 'main.jsp') === false) {
			return false;
		}

		$url = adi_get_text_around($this->res, 'main.jsp', ' ', "\n", true);
		$this->get($url);

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('http://hwwebmail.mail.126.com/js6/logout.jsp?sid='.$this->ots_session_id.'&uid='.$this->user.'&username='.$this->username.'&date='.time().'906');
	}
}
?>