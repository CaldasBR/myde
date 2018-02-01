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

class Adi_Service_mynet extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Mynet';
	public $media_key    = 'mynet';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $logout_url   = '';

	function fetchContacts() 
	{
		$this->get("http://uyeler.mynet.com/login/?loginRequestingURL=http%3A%2F%2Feposta.mynet.com%2Findex%2Fmymail.html&formname=eposta");

		$form_action = "https://uyeler.mynet.com/index/uyegiris.html";
		$payload = $this->getFormFields();
		$payload['rememberstate'] = '0';
		$payload['username'] = $this->username;
		$payload['password'] = $this->password;

		$this->post($form_action, $payload, true);

		$this->get("http://eposta.mynet.com/index/mymail.html", true);
		if (strstr($this->res, '/login/login.asp') !== false) 
		{
			return false;
		}

		$url_parts = parse_url($this->last_info['url']);
		$this->logout_domain = $url_parts['scheme'].'://'.$url_parts['host'];

		$url = 'http://adresdefteri2.mynet.com/ajax/get_list.php';
		$this->get($url);

		$result = (array)@json_decode(trim($this->res, '()'), true);
		if(is_array($result) && count($result) > 0)
		{
			foreach($result['personResults'] as $ind => $details)
			{
				$givenName  = isset($details['givenName']) ? $details['givenName'] : '';
				$surname  = isset($details['surname']) ? $details['surname'] : '';
				$fullname = trim(trim($givenName).' '.trim($surname));
				$NickName  = (isset($details['NickName']) && empty($fullname)) ? $details['NickName'] : $fullname;
				$default_mail  = isset($details['default_mail']) ? $details['default_mail'] : '';
				if(list($key,$value) = adi_parse_contact($fullname, $default_mail, 1))
				{
					$this->contacts[$key] = $value;
				}
				$mail2  = isset($details['mail2']) ? $details['mail2'] : '';
				if(list($key,$value) = adi_parse_contact($fullname, $mail2, 1))
				{
					$this->contacts[$key] = $value;
				}
			}
		}
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->logout_domain = empty($this->logout_domain) ? 'http://5611.email.mynet.com' : $this->logout_domain;
		$this->get($this->logout_domain.'/src/signout.php', array(
			'Connection' => 'Close', 
			'Referer' => $this->logout_domain.'/index.php'), 
		true);
	}
}
?>