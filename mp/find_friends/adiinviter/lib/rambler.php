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

class Adi_Service_rambler extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Rambler';
	public $media_key    = 'rambler';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.101 Safari/537.36';

	function fetchContacts()
	{
		$this->setCookie('r_id_split=3;');

		$url = 'https://mail.rambler.ru/';
		$this->get($url);

		$form_action = 'https://mail.rambler.ru/jsonrpcid';
		$payload = '{"rpc":"2.0","method":"Rambler::Id::create_web_session","params":[{"expire":0,"password":"'.$this->password.'","login":"'.$this->username.'"}]}';
		$headers = array('Content-Type' => 'application/json');
		$this->post($form_action, $payload, $headers);

		$result = (array)@json_decode($this->res, true);
		$rsid	 = isset($result['result']) ? ( isset($result['result']['rsid']) ? $result['result']['rsid'] : '' ) : '';
		if(empty($rsid)) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{
		$url_logout = "https://id.rambler.ru/logout";
		$this->get($url_logout, true);
	}
}
?>