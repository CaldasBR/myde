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


class Adi_Service_virgilio extends AdiInviter_Pro_Core
{
	public $version        = 1001;
	public $service_name   = 'Virgilio';
	public $media_key      = 'virgilio';
	public $use_ssl        = true;
	public $use_pm         = false;
	public $email_or_id    = 1;
	public $virgilio_token = '';
	public $userAgent      = 'Mozilla/5.0 (MeeGo; NokiaN9) AppleWebKit/534.13 (KHTML, like Gecko) NokiaBrowser/8.5.0 Mobile Safari/534.13';
	public $required_parser = 'csv';

	function fetchContacts() 
	{
		$this->get('https://m.mail.virgilio.it/m/wmm');
		$payload = $this->getFormFields();

		$adiaction = 'https://m.mail.virgilio.it'.adi_get_text_around($this->res, '/m/wmm?', '"', '"', true); 
		$payload['user']   = $this->username;
		$payload['pass']   = $this->password;
		$payload['j_id28'] = 'ENTRA';
		$boundary = '--W'.str_shuffle('ebKitFormBoundaryQHCEXTHKYXQNr0Ec');
		$payload = $this->multipart_build_query($payload, $boundary);

		$headers = array(
			'Content-Type' => 'multipart/form-data; boundary='.$boundary,
			'Content-Length' => strlen($payload),
			'Referer' => 'https://m.mail.virgilio.it/m/wmm',
		);
		$this->post($adiaction, $payload, $headers, true);

		if(strpos($this->res, 'logout') === false) {
			return false;
		}

		$this->get('http://m.mail.virgilio.it/m/wmm/contacts?task=export');

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get('http://m.mail.virgilio.it/m/wmm/auth/logout', true);
	}
}
?>