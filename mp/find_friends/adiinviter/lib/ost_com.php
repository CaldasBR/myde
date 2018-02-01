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

class Adi_Service_ost_com extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = '163.com';
	public $media_key       = 'ost_com';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	public $ost_session_id  = '';
	
	function fetchContacts()
	{
		$url = 'http://mail.163.com/';
		$this->get($url);
		$this->res = @gzdecode($this->res);

		$payload = $this->getFormFields();
		$form_action = 'https://mail.163.com/entry/cgi/ntesdoor?df=mail163_letter&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&product=mail163&net=n&style=-1&race=1822_481_485_gz&uid='.$this->user;
		$payload['username'] = $this->username;
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
		$this->get('http://hwwebmail.mail.163.com/js6/logout.jsp?sid='.$this->ost_session_id.'&uid='.$this->user.'&username='.$this->username.'&date='.time().'906');
	}
}
?>