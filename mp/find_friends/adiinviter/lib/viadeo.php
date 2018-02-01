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


class Adi_Service_viadeo extends AdiInviter_Pro_Core
{
	public $version         = 1002;
	public $service_name    = 'Viadeo';
	public $media_key       = 'viadeo';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;

	function fetchContacts()
	{
		$this->get('https://secure.viadeo.com/en/signin', false);

		$payload = array(
			'_csrf'    => '',
			'email'    => $this->user,
			'password' => $this->password,
		);
		$headers = array(
			'Referer' => 'https://secure.viadeo.com/en/signin',
		);
		$submit_url = 'https://secure.viadeo.com/en/signin';
		$this->post($submit_url, $payload, $headers, false);

		// $this->get($this->last_info['redirect_url'], false);

		$url = 'http://www.viadeo.com/';
		$this->get($url, true);

		if(strpos($this->res, 'deconnexion') === false) {
			return false;
		}

		$url = 'http://www.viadeo.com/r/addressbook/search/?'.time().'289&maxResults=2000&pageNumber=1&type=all';
		$this->get($url, $headers);

		$result = (array) @json_decode($this->res, true);

		if(count($result) > 0)
		foreach($result['contacts'] as $contact)
		{
			$name = trim($contact['firstname'].' '.$contact['lastname']);
			$email = $contact['email'];
			if (list($key, $value) = adi_parse_contact($name, $email)) 
			{
				$this->contacts[$key] = $value;
			}
		}

		return $this->contacts;
	}

	function endSession()
	{
		
	}
}

?>