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


class Adi_Importer
{
	public $service = null;
	public $required_settings = array(
		'adiinviter_root_url',
		'adiinviter_website_root_url',
	);
	public $cookie_path = '/tmp';
	public $local_error = '';

	function checkRequirements()
	{
		global $adiinviter;
		$adiinviter->requireSettingsList(array('global'));
		foreach ($this->required_settings as $name) 
		{
			if(empty($adiinviter->settings[$name])) 
			{
				$this->local_error = 'Empty setting : '.$name;
				return false;
			}
		}
		$this->cookie_path = $adiinviter->settings['adiinviter_cookie_path'];
		return true;
	}
	function initService($service_name, $session_id = '') 
	{
		global $adiinviter;
		include_once(ADI_LIB_PATH.'adiinviter_core.php');
		if($result = $this->checkRequirements())
		{
			$service_path = ADI_IMPORTER_PATH . $service_name.'.php';
			if(file_exists($service_path))
			{
				include($service_path);
				$class_name = 'Adi_Service_'.$service_name;
				if(class_exists($class_name))
				{
					$this->adi =& $adiinviter;
					$this->adi->importer = new $class_name();
					$this->adi->importer->init($session_id);
					return true;
				}
				else
				{
					$this->local_error = 'Service Class not found';
					adi_throwLibError(6);
					return false;
				}
			}
			else {
				$this->local_error = 'Service file not found';
				adi_throwLibError(5);
				return false;
			}
		}
		else {
			return false;
		}
	}
	function checkAccessDetails($email, $password)
	{
		if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}$/i', $email) !== 1)
		{
			adi_throwLibError(8);
			return false;
		}
		if(empty($password))
		{
			adi_throwLibError(9);
			return false;
		}
		return true;
	}
	function getSessionID() {
		return $this->adi->importer->getSessionID();
	}
	function setSessionID($sid) {
		return $this->adi->importer->setSessionID($sid);
	}
	function get_captcha(&$adi_captcha_info, $captcha_id = '') {
		return $this->adi->importer->get_captcha($adi_captcha_info, $captcha_id);
	}
	function fetchContacts($user_email, $user_password) 
	{
		if($this->local_error == '')
		{
			$id_check = true;
			if($this->adi->importer->id_type == 1) 
			{
				$id_check = (bool)$this->checkAccessDetails($user_email, $user_password);
			}
			if($id_check)
			{
				$this->adi->importer->setAccessDetails($user_email, $user_password);
				$contacts = $this->adi->importer->fetchContacts();
				if(!empty($this->adi->importer->required_parser))
				{
					if(isset($this->adi->cf_parser))
					{
						$contacts = $this->adi->cf_parser->contacts;
					}
				}
				if($this->adi->importer->use_pm != true) 
				{
					$this->endSession();
				}
				if(is_array($contacts)) {
					return $contacts;
				}
				else {
					return array();
				}
			}
		}
	}
	function sendInvitations($subject, $body, $receivers_data)
	{
		$result = $this->adi->importer->sendInvitations($subject, $body, $receivers_data);
		$this->endSession();
		return $result;
	}
	function endSession()
	{
		$this->adi->importer->endSession();     // Close service related session
		$this->adi->importer->destroySession(); // Destroy curl session
		$this->adi->importer->close_channel();  // Close Curl
	}
}


class adi_oauth_wrapper
{
	public static function oAuthParseResponse($responseString)
	{
	    $r = array();
	    foreach(explode('&', $responseString) as $param) 
	    {
	      $pair = explode('=', $param, 2);
	      if (count($pair) != 2) continue;
	      $r[urldecode($pair[0])] = urldecode($pair[1]);
	    }
	    return $r;
	}
	public static function getAuthorizeURL($token, $authorizeURL,$params = '') 
	{
		if (is_array($token)) $token = $token['oauth_token'];

		return ($authorizeURL . '?oauth_token='
			. $token
			. $params . '&oauth_callback=http://'
			. $_SERVER['HTTP_HOST']
			// ($_SERVER['PORT'] == '80' ? '' : (':' . $_SERVER['PORT'])) .
			. $_SERVER['SCRIPT_NAME'] 
			// . '?f=callback'.($this->model == 'fallback'? '&m=f':'')
		);
	}
	public static function parseJSON($json, $method)
	{
	  	if(gettype($json)=="object"){
	  		return $json;
	  	}
	  	if($method == true)
	  		$r = json_decode($json, true);
	  	else
	  		$r = json_decode($json);
	    return $r;
	}
}


class Adi_OAuth_Importer
{
	public $adi = null;
	public $importer = null;
	public $service_key = '';
	public $external_mode = false;

	public function init($service_key = '')
	{
		global $adiinviter;
		$this->adi =& $adiinviter;
		$this->service_key = $service_key;

		include_once(ADI_LIB_PATH.'adiinviter_core.php');
		$service_file = ADI_IMPORTER_PATH.$this->service_key.'.php';
		if(file_exists($service_file))
		{
			include_once($service_file);
			$consumer_key = $consumer_secret = ''; $error_ocurred = false;
			$this->adi->requireSettingsList(array('invitation','oauth'));
			$this->adi->service_key = $this->service_key;
			if($this->service_key == 'gmail' || $this->service_key == 'orkut')
			{
				$service_key = 'google';
			}
			if(isset($this->adi->settings[$service_key.'_consumer_key']))
			{
				$consumer_key = $this->adi->settings[$service_key.'_consumer_key'];
			}
			if(isset($this->adi->settings[$service_key.'_consumer_secret']))
			{
				$consumer_secret = $this->adi->settings[$service_key.'_consumer_secret'];
			}
			$supported_services = array('aol', 'xing');
			if( (empty($consumer_key) || empty($consumer_secret)))
			{
				if(in_array($this->service_key, $supported_services)) {
					$this->external_mode = true;
				}
				else {
					$error_ocurred = true;
					$this->adi->error->report_error('Consumer Key or Secret key for '.$this->service_key.' is Empty.');
					return false;
				}
			}

			$class_name = 'Adi_Service_' . $this->service_key . '_OAuth';
			if(class_exists($class_name))
			{
				$this->adi->importer = new $class_name();
				$this->adi->importer->settings =& $this->adi->settings;
				$this->adi->importer->consumer_key    = $consumer_key;
				$this->adi->importer->consumer_secret = $consumer_secret;

				$this->adi->importer->init();
				return true;
			}
			else
			{
				$this->adi->trace('Adi_OAuth_Importer.init : Service class not found.');
				adi_throwLibError(6);
			}
		}
		else
		{
			$this->adi->trace('Adi_OAuth_Importer.init : Service file not found "'.$service_key.'.php".');
			adi_throwLibError(5);
		}
		return false;
	}

	function get_request_token()
	{
		$sess_key = 'adi_'.$this->service_key.'_before_redirect';
		$this->adi->session->set($sess_key, 1);
		if($this->external_mode === true)
		{
			$this->adi->importer->media_key = 'oauth_login';
		}
		else
		{
			return $this->adi->importer->getRequestToken();
		}
		$this->adi->importer->reset_channel();
	}

	function get_access_token()
	{
		$sess_key = 'adi_'.$this->service_key.'_before_redirect';
		if(!$this->adi->session->is_set($sess_key))
		{
			$adi_root_url = $this->adi->getCallbackURL($this->service_key);
			preg_match('/(https?:\/\/)?([^\/]+)(\/?.*)/i', $adi_root_url, $matches);
			if(count($matches) > 0)
			{
				$http   = (isset($matches[1]) && !empty($matches[1])) ? $matches[1] : 'http://';
				$domain = $matches[2];
				if(strpos($domain, 'www.') !== false) {
					$domain = str_replace('www.', '', $domain);
				}
				else {
					$domain = 'www.' . $domain;
				}
				if(!isset($_SERVER['REQUEST_URI'])) 
				{
					$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],0);
					if(isset($_SERVER['QUERY_STRING']) AND $_SERVER['QUERY_STRING'] != "") 
					{
						$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
					}
				}
				$adi_root_url = $http . $domain . $_SERVER['REQUEST_URI'];
				header('Location: '.$adi_root_url);
				exit;
			}
		}
		$this->adi->session->remove($sess_key);
		if($this->external_mode === true)
		{
			$adi_error = '';
			$service_key = '';
			if(AdiInviterPro::isGET('adi_service'))
			{
				$service_key = AdiInviterPro::GET('adi_service', ADI_STRING_VARS);
			}
			if(AdiInviterPro::isGET('lp') || AdiInviterPro::isGET('adierr'))
			{
				$lp = AdiInviterPro::GET('lp');
				$ae = AdiInviterPro::GET('adierr');
				if(!empty($ae))
				{
					$adi_error = $ae;
				}
				if(!empty($service_key) && empty($adi_error) && !empty($lp))
				{
					$key = 'adi_'.$service_key.'_lp';
					$on_services = $this->adi->settings['services_onoff']['on'];
					if(is_array($on_services) && count($on_services) > 0)
					{
						if(in_array($service_key, $on_services) !== false)
						{
							$this->adi->session->set($key, AdiInviterPro::GET('lp', ADI_STRING_VARS));
						}
					}
				}
			}
			return !empty($adi_error) ? $adi_error : '1';
		}
		else
		{
			$result = $this->adi->importer->getAccessToken();
			if($result === true) {
				$result = '1';
			}
			else if($result === false && $this->adi->error->last_error != '') {
				$result = $this->adi->error->last_error;
			}
			return $result;
		}
	}

	function get_contacts()
	{
		if($this->external_mode === true)
		{
			$this->adi->importer->media_key .= '_contacts';
			if($this->adi->session->is_set('adi_'.$this->adi->service_key.'_lp'))
			{
				$this->adi->lp = $this->adi->session->get('adi_'.$this->adi->service_key.'_lp');
				if(!empty($this->adi->lp))
				{
					$this->adi->importer->reset_channel();
					$this->adi->session->set('adi_'.$this->adi->service_key.'_lp', '');
				}
			}
		}
		else
		{
			$this->adi->importer->contacts = $this->adi->importer->fetchContacts();
		}
		if(!empty($this->adi->importer->required_parser))
		{
			if(isset($this->adi->cf_parser))
			{
				$contacts = $this->adi->cf_parser->contacts;
			}
		}
		if($this->adi->importer->use_pm !== true) 
		{
			$this->endSession();
		}
		if( !is_array($this->adi->importer->contacts) )
		{
			$this->adi->importer->contacts = array();
		}
		return $this->adi->importer->contacts;
	}

	function sendInvitations($subject, $body, $receivers_data)
	{
		if( $this->external_mode === true && in_array($this->service_key, array('twitter')) )
		{
			return false;
		}
		$result = $this->adi->importer->sendInvitations($subject, $body, $receivers_data);
		return $result;
	}

	function get_sendmail_details()
	{
		return $this->adi->importer->get_sendmail_details();
	}

	function get_invites_quota()
	{
		return $this->adi->importer->get_invites_quota();
	}

	function endSession()
	{
		$this->adi->importer->endSession();     // Close service related session
		$this->adi->importer->destroySession(); // Destroy curl session
		$this->adi->importer->close_channel();  // Close Curl
	}
}


function adi_oAuthParseResponse($responseString)
{
	$r = array();
	foreach(explode('&', $responseString) as $param)
	{
		$pair = explode('=', $param, 2);
		if (count($pair) != 2) continue;
		$r[urldecode($pair[0])] = urldecode($pair[1]);
	}
	return $r;
}
function adi_getAuthorizeURL($token, $authorizeURL, $params = '')
{
	if (is_array($token))
		$token = $token['oauth_token'];

	return ($authorizeURL.'?oauth_token=' .
		$token .
		$params . '&oauth_callback=http://' .
		$_SERVER['HTTP_HOST'] .
		$_SERVER['SCRIPT_NAME']
	);
}
function adi_parseJSON($json, $method)
{
	if(gettype($json) == "object")
	{
		return $json;
	}
	if($method == true)
		$r = json_decode($json, true);
	else
		$r = json_decode($json);
	return $r;
}


?>