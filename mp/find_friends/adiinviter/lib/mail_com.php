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


class Adi_Service_mail_com extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Mail.com';
	public $media_key    = 'mail_com';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function fetchContacts()
	{
		$this->get("http://www.mail.com", true);
		$post_elements = $this->getFormFields($this->res);

		$form_action = 'https://login.mail.com/login';
		$post_elements['btnLogin'] = 'Log in';
		$post_elements['username'] = $this->user;
		$post_elements['password'] = $this->password;
		$this->post($form_action, $post_elements, true);

		if(strstr($this->res, 'sid=') === false) 
		{
			return false;
		}

		preg_match('/sid=([^&"\']+)/i', $this->res, $sid);
		$this->sid = '';
		if(count($sid) > 0)
		{
			$this->sid = $sid[1];
		}
		$url = 'https://mm.mail.com/contacts';
		$cont_cnt = 0;
		$sid = isset($this->sid) ? $this->sid : '';
		if(empty($sid))
		{
			adi_throwLibError(1);
			return false;
		}
		$page_nr = 1;
		do {
			$hasNext = false;
			$url = 'https://home.navigator-lxa.mail.com/servicecontact/contactlist?sid='.$this->sid.'&page_nr='.$page_nr;
			$this->get($url, false);
			$result = @json_decode($this->res, true);

			if(isset($result['info']) && isset($result['info']['hasNext']))
			{
				$hasNext = $result['info']['hasNext'];
			}
			if(is_array($result) && count($result) > 0 && isset($result['data']))
			{
				foreach($result['data'] as $cont)
				{
					$name = isset($cont['name']) ? $cont['name'] : '';
					$email = isset($cont['mail']) ? $cont['mail'] : '';
					if(list($key, $value) = adi_parse_contact($name, $email))
					{
						$this->contacts[$key] = $value;
						$cont_cnt++;
						if($cont_cnt >= 2000){break;}
					}
				}
			}
			if($cont_cnt >= 2000){break;}
			$page_nr++;
			if($page_nr >= 10){break;}
		}while($hasNext);
		return $this->contacts;
	}
	
	function endSession()
	{
		$sid = isset($this->sid) ? $this->sid : '';
		if(!empty($sid))
		{
			$this->get('https://navigator-lxa.mail.com/logout?sid='.$sid);
		}
		return true;
	}
}

?>