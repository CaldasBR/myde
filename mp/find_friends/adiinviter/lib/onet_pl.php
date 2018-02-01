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

class Adi_Service_onet_pl extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Onet';
	public $media_key       = 'onet_pl';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$url = 'https://konto.onet.pl/auth.html?app_id=kontakty.onet.pl.front.onetapi.pl';
		$this->get($url);

		$url = 'http://kropka.onet.pl/_s/kropka/1?DV=POCZTA%2FLOGOWANIE%2FKONTAKTY_ONET_PL';
		$this->get($url, true);

		$form_action = 'https://konto.onet.pl/login.html?app_id=kontakty.onet.pl.front.onetapi.pl';
		$payload = array(
			'noscript'     => '1',
			'login'        => $this->user,
			'password'     => $this->password,
			'perm'         => '0',
			'provider'     => '',
			'access_token' => '',
		);
		$this->post($form_action, $payload, false);

		preg_match('/onet_ubi=([^;]+)/i', $this->last_info['request_header'], $matches);
		$onet_ubi = isset($matches[1]) ? $matches[1] : '';

		if(empty($onet_ubi)) {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession()
	{
		$this->get("https://m.poczta.onet.pl/wyloguj.html", false);
	}
}
?>