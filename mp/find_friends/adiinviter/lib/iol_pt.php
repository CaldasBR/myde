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

class Adi_Service_iol_pt extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'iOL.pt';
	public $media_key       = 'iol_pt';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'vcf';

	function fetchContacts() 
	{
		$adiaction = 'http://webmail.iol.pt';

		$this->get($adiaction);
		$payload = $this->getFormFields();

		$payload['_user']      = $this->user;
		$payload['_pass']      = $this->password;
		$payload['iol_user']   = $this->username;
		$payload['iol_domain'] = $this->domain;
		$payload['iol_pass']   = $this->password;

		$this->post($adiaction, $payload, false);

		if(strpos($this->res, 'name="iol_user"') !== false)
		{
			return false;
		}

		$this->get('http://webmail.iol.pt/?_task=addressbook&_source=0&_action=export');

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'vcf');
		return $this->contacts;
	}

	function endSession() 
	{
		$this->get('http://webmail.iol.pt/?_task=logout', true);
	}
}
?>