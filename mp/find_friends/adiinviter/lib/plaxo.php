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

class Adi_Service_plaxo extends AdiInviter_Pro_Core
{
	public $version         = 2001;
	public $service_name    = 'Plaxo';
	public $media_key       = 'plaxo';
	public $use_ssl         = true;
	public $use_pm          = false;
	public $email_or_id     = 1;
	public $required_parser = 'csv';

	function fetchContacts() 
	{
		$url = 'https://www.plaxo.com/auth?secure=1';
		$this->get($url, false);

		$form_action = 'https://www.plaxo.com/auth?isnew=1&secure=1';
		$payload = array(
			'done'     => '',
			'identity' => $this->user,
			'password' => $this->password,
		);
		$headers = array(
			'Referer' => 'https://www.plaxo.com/auth?secure=1',
		);
		$this->post($form_action, $payload, $headers, false);

		$url = $this->last_info['redirect_url'];
		if(strpos($url, 'http://www.plaxo.com/?src=') === false) {
			return false;
		}
		$this->get($url, true);

		if(strpos($this->res, 'auth/signout') === false) {
			return false;
		}

		$url = "http://www.plaxo.com/export?t=ab_contacts_export_all&memberImport=1";
		$this->get($url, true);

		$form_action = "http://www.plaxo.com/export/plaxo_ab_outlook.csv";
		$post_elements = array("paths.0.folder_id" => $this->getElementString($this->res, 'name="paths.0.folder_id" value="', '"'), "paths.0.checked" => "on", "NumPaths" => 1, "type" => "O", "do_submit" => 1, "x" => 51, "y" => 19);
		$this->post($form_action, $post_elements);

		global $adiinviter;
		$this->contacts = $adiinviter->cf_parser->get_contacts_from_file($this->res, 'csv');
		return $this->contacts;
	}

	function endSession()
	{
		$this->get('http://www.plaxo.com/auth/signout?src=header&lang=en', true);
	}
}
?>