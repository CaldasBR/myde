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


class Adi_Service_citromail_hu extends AdiInviter_Pro_Core
{
	public $version          = 1001;
	public $service_name     = 'CitroMail';
	public $media_key        = 'citromail_hu';
	public $use_ssl          = true;
	public $use_pm           = false;
	public $email_or_id      = 1;
	public $vip_token        = '';
	public $citro_url_domain = '';

	function fetchContacts()
	{
		$form_action = 'http://auth.citromail.hu/index.vip';		
		$payload = array( 
			'user'   => $this->user,
			'ipv'    => '2',
			'passwd' => $this->password,
		);
		$headers = array(
			'Referer' => 'http://citromail.hu/index.vip',
		);
		$this->post($form_action, $payload, $headers, false);
		$url = $this->last_info['redirect_url'];
		if(!empty($url))
		{
			$url_parts = parse_url($url);
			$this->citro_url_domain = $url_parts['scheme'].'://'.$url_parts['host'];
			$this->get($url, false);
		}

		if(strpos($this->res, 'Location: index.php') === false)
		{
			return false;
		}

		preg_match('/vip=([^;]*)/i', $url, $matches);
		$this->vip_token = isset($matches[1]) ? $matches[1] : '';

		$this->set_as_loggedin();
		return $this->contacts;
	}

	function endSession()
	{
		$url = $this->citro_url_domain.'/logouts.php?vip='.$this->vip_token;
		$this->get($url, false);
	}
}

?>