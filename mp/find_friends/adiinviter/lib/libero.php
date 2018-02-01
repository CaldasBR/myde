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

class Adi_Service_libero extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'Libero';
	public $media_key       = 'libero';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts()
	{
		$this->get("http://m.libero.it/mail", true);
		$form_action = "https://login.libero.it/logincheck.php";
		$post_elements=array(
			"SERVICE_ID"  => "m_mail",
			"RET_URL"     => "http://m.mailbeta.libero.it/m/wmm/auth/check",
			"LAYOUT"      => "m",
			"LOGINID"     => $user,
			"PASSWORD"    => $pass,
			"REMEMBERME"  => "S",
			"CAPTCHA_ID"  => "",
			"CAPTCHA_INP" => "",
			"login"       => "+Accedi+",
		);
		
		$this->post($form_action, $post_elements, true);
		if (strpos($this->res, 'logout') === false) {
			return false;
		}
		$url = "http://m.mailbeta.libero.it/m/wmm/contacts?task=export";
		$this->get($url);

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get("http://m.mailbeta.libero.it/doLogout", true);
	}
}
?>