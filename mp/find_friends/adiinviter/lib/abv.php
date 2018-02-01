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

class Adi_Service_abv extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'ABV';
	public $media_key    = 'abv';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	public $userAgent = 'Mozilla/5.0 (Linux; Android 4.2.2; GT-I9505 Build/JDQ39) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.59 Mobile Safari/537.36';

	function fetchContacts() 
	{
		$form_action = 'https://passport.abv.bg/app/profiles/servicelogin';
		$payload = array(
			'service'       => 'mobile20',
			'username'      => $this->user,
			'password'      => $this->password,
			'submit_button' => 'вход',
		);
		$this->post($form_action, $payload, true);

		if(strpos($this->res, 'profiles/servicelogin') !== false)
		{
			return false;
		}

		preg_match('/mmail\/login[^\'"]+/i', $this->res, $matches);
		$url_part = isset($matches[0]) ? $matches[0] : '';

		if(!empty($url_part))
		{
			$this->get('https://m.abv.bg/'.$url_part,false);
		}

		$url = 'https://m.abv.bg/mmail/contacts';
		$payload = '7|0|7|https://m.abv.bg/mail/|9BFC76C195B4F462DFA7159499DB59D9|bg.abv.mobile.sg.client.service.ContactsService|getAllContacts|I|java.lang.String/2004016611||1|2|3|4|2|5|6|10|7|';
		$headers = array(
			'Referer' =>'https://m.abv.bg/MMail.html',
			'X-GWT-Module-Base' =>'https://m.abv.bg/mail/',
			'X-GWT-Permutation' =>'ECA556C31CE62CFAB702081F645CCED9',
			'Content-Type' => 'text/x-gwt-rpc; charset=UTF-8',
		);
		$this->post($url, $payload, $headers);

		$this->res = (strpos($this->res, '//OK') !== false) ? substr($this->res, 4) : $this->res;

		$parts = preg_split('/,|\[|\]/', $this->res);
		$start_store = false;
		$email = $first_name = $last_name = '';
		foreach($parts as $part)
		{
			if(strpos($part, 'bg.abv.mail.sg.shared.model.ContactItem') !== false) {
				$start_store = true;
			}
			if($start_store === true)
			{
				if(strpos($part, '@') !== false)
				{
					if(!empty($email))
					{
						$name = trim( trim($first_name) . ' ' . trim($last_name));
						if(list($key, $value) = adi_parse_contact($name, $email))
						{
							$this->contacts[$key] = $value;
						}
					}
					$email = $part;
					$first_name = $last_name = '';
				}
				else if(empty($first_name)) {
					$first_name = empty($part) ? ' ' : $part;
				}
				else {
					$last_name = empty($part) ? ' ' : $part;
				}
			}
		}
		$name = trim( trim($first_name) . ' ' . trim($last_name));
		if(list($key, $value) = adi_parse_contact($name, $email))
		{
			$this->contacts[$key] = $value;
		}

		return $this->contacts;
	}
	
	function endSession() 
	{
		
	}
}
?>