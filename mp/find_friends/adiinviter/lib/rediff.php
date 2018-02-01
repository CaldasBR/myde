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


class Adi_Service_rediff extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Rediff';
	public $media_key    = 'rediff';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	public $logout_url   = '';

	function fetchContacts()
	{
		$post_elements = array(
			"login"    => $this->user,
			"passwd"   => $this->password,
			"FormName" => "existing",
			"proceed"  => "GO"
		);
		html_entity_decode($this->post("https://mail.rediff.com/cgi-bin/login.cgi",$post_elements,true));
		if(strstr($this->res, 'login failed') !== false)
		{
			return false;
		}
		$url = $this->getElementString($this->res, 'window.location.replace("', '");');
		$this->get($url, true);
		
		$this->logout_url = "http://login.rediff.com/bn/logout.cgi?formname=general&login={$this->username}&session_id=&function_name=logout";

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$this->get($this->logout_url);
	}
}

?>