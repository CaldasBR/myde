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

class Adi_Service_yeah extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Yeah';
	public $media_key       = 'yeah';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	
	function fetchContacts() 
	{
		$url = 'http://mail.yeah.net/';
		$this->get($url);
		$this->res = @gzdecode($this->res);

		$payload = $this->getFormFields();
		$form_action = 'https://mail.yeah.net/entry/cgi/ntesdoor?df=webmailyeah&from=web&funcid=loginone&iframe=1&language=-1&passtype=1&verifycookie=1&product=mailyeah&style=-1&uid='.$this->user;
		$payload['user'] = $this->username;
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
		$this->get('http://reg.163.com/Logout.jsp?username='.$this->user.'&url=http://www.yeah.net/logout.htm%23yeah|'.$this->username.'|'.time().'720|false');
	}
}
?>