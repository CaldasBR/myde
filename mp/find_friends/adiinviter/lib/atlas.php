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

class Adi_Service_atlas extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Atlas';
	public $media_key    = 'atlas';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function fetchContacts()
	{
		$this->setcookie('USE_COOKIES=1;');

		$form_action = 'https://auser.centrum.cz/';
		$payload = array(
			'ego_domain' => $this->domain,
			'url'        => 'http://amail.centrum.cz/',
			'ego_user'   => $this->user,
			'ego_secret' => $this->password,
		);
		$this->post($form_action, $payload, true);

		if(strpos($this->res, 'loadingprogress') === false) {
			return false;
		}

		$this->get('http://amail.centrum.cz/index.php?m=myabook&op=contact_list&limit=2000&u='.urlencode($this->user));

		$result = @json_decode($this->res, true);
		if(is_array($result) && count($result) > 0)
		{
			foreach($result as $cont_details)
			{
				$email = isset($cont_details['email']) ? $cont_details['email'] : '';
				$firstname = trim(isset($cont_details['firstName']) ? $cont_details['firstName'] : '');
				$surname = trim(isset($cont_details['surname']) ? $cont_details['surname'] : '');
				$name = trim($firstname.' '.$surname);
				if(list($key,$value) = adi_parse_contact($name, $email, 1))
				{
					$this->contacts[$key] = $value;
				}
			}
		}
		return $this->contacts;
	}
	
	function endSession()
	{
		$this->get('https://auser.centrum.cz/logout.php?url=http%3A%2F%2Famail.centrum.cz%2Ffree%2Flogout.php%3Flogout%3D5%26name%3D'.urlencode($this->user), true);
	}
}
?>