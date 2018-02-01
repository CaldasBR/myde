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

class Adi_Service_icloud extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'iCloud';
	public $media_key    = 'icloud';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;
	
	function fetchContacts() 
	{
		$this->get('https://www.icloud.com');
		
		$form_action = "https://setup.icloud.com/setup/ws/1/login";
		$post_elements = '{"apple_id":"' . $this->user . '","password":"' . $this->password . '","extended_login":false}';
		$headers = array(
			'Origin'  => 'https://www.icloud.com',
			'Host'    => 'setup.icloud.com',
			'Referer' => 'https://www.icloud.com/',
		);
		$this->post($form_action, $post_elements, $headers);
		$result = @json_decode($this->res, true);
		if(isset($result['error']) && $result['error'] == '1') {
			return false;
		}

		$this->set_as_loggedin();
		return $this->contacts;
	}
	
	function endSession() 
	{

	}
}
?>