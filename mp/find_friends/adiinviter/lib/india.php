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

class Adi_Service_india extends AdiInviter_Pro_Core
{
	public $version         = 1001;
	public $service_name    = 'India';
	public $media_key       = 'india';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';
	
	function fetchContacts() 
	{
		$this->get("http://mail.india.com/login");

		$form_action = 'http://mail.india.com/authenticate';
		$post_elements = array(
			'forgotten-title'    => 'Retrieve forgotten Password',
			'utf8'               => '✓',
			'authenticity_token' => $this->getElementString($this->res,'authenticity_token" type="hidden" value="','"'),
			'user[email]'        => $this->user,
			'user[password]'     => $this->password,
			'Submit'             => '',
		);
		$this->post($form_action, $post_elements, true);
		if (strstr($this->res, 'logout') === false) 
		{
			return false;
		}

		$url = 'http://mail.india.com/address_book/groups/all/contacts.csv';
		$this->get($url);
		if (strstr($this->res, '@') === false) 
		{
			adi_throwLibError(1);
			return false;
		}
		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}
	
	function endSession() 
	{
		$url = 'http://mail.india.com/logout';
		$this->get($url);
	}
}
?>