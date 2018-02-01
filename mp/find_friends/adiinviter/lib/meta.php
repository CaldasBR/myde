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

class Adi_Service_meta extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'Meta';
	public $media_key    = 'meta';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function fetchContacts() 
	{
		$form_action = "http://passport.meta.ua/";
		$post_elements = array(
			'login'    => $this->user,
			'password' => $this->password,
			'mode'     => 'login',
			'from'     => 'mail',
			'lifetime' => 'alltime',
			'subm'     => 'Enter',
		);
		$this->post($form_action, $post_elements, true);
		
		if (strpos($this->res, 'logout') === false) 
		{
			return false;
		}
		$url = "http://webmail.meta.ua/adress_table.php";
		$this->get($url, true);
		
		if (strpos($this->res, '@') === FALSE) 
		{
			adi_throwLibError(1);
			return false;
		}

		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		if (!empty($this->res)) $doc->loadHTML($this->res);
		libxml_use_internal_errors(false);
		$xpath = new DOMXPath($doc);
		$query = "//tr[@onmouseout='row_out(this)']";
		$data = $xpath->query($query);
		$name = "";
		foreach ($data as $node) 
		{
			$email = $node->getElementsByTagName('a')->item(1)->nodeValue;
			$name = $node->getElementsByTagName('a')->item(0)->nodeValue;
			if (list($key, $value) = adi_parse_contact($name, $email)) 
			{
				$this->contacts[$key] = $value;
			}
		}
		return $this->contacts;
	}
	
	function endSession() 
	{
		$this->get('http://webmail.meta.ua/logout.php', true);
	}
}
?>