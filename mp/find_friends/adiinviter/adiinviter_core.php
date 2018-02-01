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


/**
* AdiInviter Pro core file.
*/
class AdiInviter_Pro_Core
{
	public $settings             = array();
	public $session_dir          = '/tmp';
	public $session_path         = '';
	public $session_id           = 0;
	public $session_name         = '';
	public $session_name_freq    = 100;
	public $logging_onoff        = 1;
	public $last_info            = array();
	public $logs                 = array();
	// public $defaultUserAgent     = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.1';
	public $defaultUserAgent     = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36';
	public $default_headers      = array('Connection' => 'Keep-Alive');
	public $request_headers      = array();
	public $defaultTimeout       = 5;
	public $defaultRedirects     = 5;
	public $curl_multi_supported = false;
	public $curl_common_options  = array();
	public $res                  = '';
	public $doc                  = NULL;
	public $curl                 = NULL;
	public $curl_multi           = NULL;
	public $curl_multi_limit     = 10;
	public $curl_id              = '';

	public $user                 = '';
	public $password             = '';
	public $username             = ''; // splitEmail()
	public $domain               = ''; // splitEmail()
	public $contacts             = array();
	public $logged_in            = false;
	// Library default parameters
	public $keep_session         = false;
	// public $domain_required      = false;
	public $id_type              = 'email';
	public $media_key            = '';
	public $required_parser      = '';



	public $follow_location = false;
	public $max_redirection_counter = NULL;

	/*
	public $userAgent = '';
	public $timeout = 5;
	public $location_redirects = 5;
	*/
	
	public $enable_curl_session = true;

	// Initialize Core
	function init($session_id = '')
	{
		if(gettype($this->curl) == 'resource')
		{
			curl_close($this->curl);
		}
		$this->curl = curl_init();
		$this->curl_common_options = array(
			CURLOPT_AUTOREFERER    => true,
			// CURLOPT_FOLLOWLOCATION => false,
			CURLOPT_HEADER         => false,
			CURLOPT_NOPROGRESS     => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADERFUNCTION => 'adi_curl_header_callback',
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		);
		
		$timeout = isset($this->timeout) ? $this->timeout : $this->defaultTimeout;
		if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$this->curl_common_options[CURLOPT_CONNECTTIMEOUT] = $timeout;
		}
		else {
			$this->curl_common_options[CURLOPT_CONNECTTIMEOUT] = $timeout;
		}

		/*if(isset($this->location_redirects) && is_numeric($this->location_redirects)) {
			$this->curl_common_options[CURLOPT_MAXREDIRS] = $this->location_redirects;
		}
		else {
			$this->curl_common_options[CURLOPT_MAXREDIRS] = $this->defaultRedirects;
		}*/

		if(!isset($this->location_redirects)) {
			$this->location_redirects = $this->defaultRedirects;
		}

		if(!isset($this->userAgent) || empty($this->userAgent) ) {
			$this->curl_common_options[CURLOPT_USERAGENT] = $this->defaultUserAgent;
		}
		else {
			$this->curl_common_options[CURLOPT_USERAGENT] = $this->userAgent;
		}

		global $adiinviter;
		$this->cookies =& $adiinviter->curl_cookies;
		
		if($this->enable_curl_session === true)
		{
			// Cookie Handler
			if(!is_null($adiinviter))
			{
				if(isset($adiinviter->settings['adiinviter_cookie_path']))
				{
					$path = $adiinviter->settings['adiinviter_cookie_path'];
					if(file_exists($path))
					{
						$this->session_dir = $path;
					}
				}
			}

			if(!empty($session_id))
			{
				$this->resumeSession($session_id);
			}

			$this->initSession();
			$cookie_file = $this->getSessionPath();
			$this->curl_common_options[CURLOPT_COOKIEFILE] = $cookie_file;
			$this->curl_common_options[CURLOPT_COOKIEJAR]  = $cookie_file;
		}
		$this->adi_curl_setopt_array($this->curl_common_options);
		$this->curl_id = 'P2462334RRP63O82SQ1NSNP2P389N47P433R8OS2P7';
		// Check if curl_multi is supported i.e. Simultaneous Curl Requests
		if(function_exists('curl_multi_init'))
		{
			$this->curl_multi_supported = true;
		}

		if(!empty($this->required_parser))
		{
			include(ADI_LIB_PATH.'csv_processor.php');
			$adiinviter->cf_parser = adi_allocate('Adi_Contact_File');
			$adiinviter->cf_parser->init();
			if(in_array($this->required_parser, $adiinviter->cf_parser->supported_formats))
			{
				$this->media_key .= ','.$this->required_parser.'_contacts';
			}
			else
			{
				$this->required_parser = '';
			}
		}
	}

	function setAccessDetails($user, $password)
	{
		$this->user = $this->username = $user;
		$this->password = $password;
		if(strpos($this->user, '@') !== false)
		{
			$array_user     = explode("@", $this->user, 2);
			$this->username = isset($array_user[0]) ? $array_user[0] : '';
			$this->domain   = isset($array_user[1]) ? $array_user[1] : '';;
		}
	}
	
	// Curl Reqeust Handler
	function adi_curl_setopt_array(&$curl_or_opts = null, $opts = array())
	{
		if(is_null($curl_or_opts)) {
			return false;
		}
		else 
		{
			if(gettype($curl_or_opts) == 'resource') {
				$ch =& $curl_or_opts;
			}
			else {
				$ch =& $this->curl;
				if(is_array($curl_or_opts)) {
					$opts = $curl_or_opts;
				}
			}
			if(count($opts) > 0) 
			{
				if(function_exists('curl_setopt_array')) {
					curl_setopt_array($ch, $opts);
				}
				else {
					foreach($opts as $op_name => $op_val)
					{
						curl_setopt($ch, $op_name, $op_val);
					}
				}
			}
			if(gettype($curl_or_opts) == 'resource') {
				return $ch;
			}
			else {
				return true;
			}
		}
	}
	function load_curl_GET_options($url, $headers_or_follow = null, $followLoc = null)
	{
		if(empty($url) || $url == '')
		{
			$this->trace('fn.get : Empty URL requested.');
			return false;
		}

		$header = false; $follow = false; $headers = array();
		$this->request_headers = array();
		$this->request_headers = array_merge($this->default_headers, $this->get_HTTP_headers($url));
		if(is_array($headers_or_follow))
		{
			$this->request_headers = array_merge($this->request_headers, $headers_or_follow);
		}
		else {
			$followLoc = $headers_or_follow;
		}
		
		if(count($this->request_headers) > 0)
		{
			foreach($this->request_headers as $name => $val)
			{
				if(is_numeric($name)) {
					$headers[] = $val;
				}
				else {
					$headers[] = $name . ': ' . $val;
				}
			}
		}

		if($followLoc === false) {
			$header = true;
		}
		else if($followLoc === true) {
			$header = true;
			$follow = true;
		}
		$options_arr = array(
			CURLOPT_URL           => $url,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_POSTFIELDS    => false,
			CURLOPT_HTTPHEADER    => $headers,
			CURLOPT_POST          => false,
			CURLOPT_HTTPGET       => true,
			CURLINFO_HEADER_OUT   => true,
			CURLOPT_HEADER        => $header,
		);
		if($follow) 
		{
			// $options_arr[CURLOPT_FOLLOWLOCATION] = true;
			$this->follow_location = true;
		}
		else {
			// $options_arr[CURLOPT_FOLLOWLOCATION] = false;
			$this->follow_location = false;
		}
		return $options_arr;
	}
	function check_curl_response(&$res)
	{
		if($res === false)
		{
			$error_num = curl_errno($this->curl);
			$error_msg = curl_error($this->curl);
			$res = $res."\n".'Local CURL Error ('.$error_num.') : '.$error_msg;
		}
	}

	function get_HTTP_headers($url)
	{
		$options = parse_url($url);
		$headers = array();

		if(isset($options['host'])) {
			$headers['Host'] = $options['host'];
		}

		if(isset($options['host'])) {
			$headers['Origin'] = $options['scheme'].'://'.$options['host'];
		}

		if(is_array($this->last_info) && count($this->last_info) > 0 && isset($this->last_info['url'])) {
			$headers['Referer'] = $this->last_info['url'];
		}

		return $headers;
	}

	function get($url, $headers_or_follow = null, $followLoc = null)
	{
		if(is_null($this->max_redirection_counter)) {
			$this->max_redirection_counter = $this->location_redirects;
		}
		$curl_get_options = $this->load_curl_GET_options($url, $headers_or_follow, $followLoc);
		if($curl_get_options === false) {
			return false;
		}
		else {
			$this->adi_curl_setopt_array($curl_get_options);
		}

		$this->res = curl_exec($this->curl);
		$this->check_curl_response($this->res);
		$this->logCommunication();

		if($this->follow_location && in_array($this->last_info['http_code'], array(301, 302)) && strpos($this->res, 'Location: ') !== false)
		{
			$redirect_url = $this->last_info['redirect_url'];
			if(!empty($redirect_url))
			{
				if($this->max_redirection_counter > 0)
				{
					$this->max_redirection_counter--;
					$this->res = $this->get($redirect_url, true);
				}
				$this->max_redirection_counter = NULL;
				return $this->res;
			}
		}

		$this->max_redirection_counter = NULL;
		return $this->res;
	}
	function load_curl_POST_options($url, $postdata_or_follow = null, $headers_or_follow = null, $followLoc = null)
	{
		if(empty($url) || $url == '') {
			$this->trace('fn.get : Empty URL requested.');
			return false;
		}
		else {
			$options_arr = array();
		}
		$header = false; $follow = false; $headers = array(); $postdata = '';

		$this->request_headers = array();
		$this->request_headers = array_merge($this->default_headers, $this->get_HTTP_headers($url));

		if(is_array($postdata_or_follow))
		{
			if(count($postdata_or_follow)) 
			{
				foreach($postdata_or_follow as $name => $val) 
				{
					$postdata .= $name . '=' . urlencode($val).'&';
				}
				$postdata = rtrim($postdata, '&');
			}
		}
		else if(is_string($postdata_or_follow))
		{
			$postdata = $postdata_or_follow;
		}
		else {
			$followLoc = $postdata_or_follow;
		}
		if(!is_null($postdata_or_follow))
		{
			if(is_array($headers_or_follow))
			{
				if(count($headers_or_follow) > 0)
				{
					$this->request_headers = array_merge($this->request_headers, $headers_or_follow);
				}
			}
			else {
				$followLoc = $headers_or_follow;
			}
		}

		if(count($this->request_headers) > 0)
		{
			foreach($this->request_headers as $name => $val)
			{
				if(is_numeric($name)) {
					$headers[] = $val;
				}
				else {
					$headers[] = $name . ': ' . $val;
				}
			}
		}


		if($followLoc === false) {
			$header = true;
		}
		else if($followLoc === true) {
			$header = true;
			$follow = true;
		}
		$options_arr = array(
			CURLOPT_URL           => $url,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS    => $postdata,
			CURLOPT_REFERER       => '',
			CURLOPT_POST          => true,
			CURLOPT_HTTPGET       => false,
			CURLINFO_HEADER_OUT   => true,
			CURLOPT_HTTPHEADER    => $headers,
			CURLOPT_HEADER        => $header,
		);
		if($follow) {
			// $options_arr[CURLOPT_FOLLOWLOCATION] = true;
			$this->follow_location = true;
		}
		else {
			// $options_arr[CURLOPT_FOLLOWLOCATION] = false;
			$this->follow_location = false;
		}
		return $options_arr;
	}

	function post($url, $postdata_or_follow = null, $headers_or_follow = null, $followLoc = null)
	{
		if(is_null($this->max_redirection_counter)) {
			$this->max_redirection_counter = $this->location_redirects;
		}

		$curl_post_options = $this->load_curl_POST_options($url, $postdata_or_follow, $headers_or_follow, $followLoc);
		if($curl_post_options === false) {
			return false;
		}
		else {
			$this->adi_curl_setopt_array($curl_post_options);
		}
		$this->res = curl_exec($this->curl);
		$this->check_curl_response($this->res);
		$this->logCommunication();

		if($this->follow_location && in_array($this->last_info['http_code'], array(301, 302)) && strpos($this->res, 'Location: ') !== false)
		{
			$redirect_url = $this->last_info['redirect_url'];
			if(!empty($redirect_url))
			{
				if($this->max_redirection_counter > 0)
				{
					$this->max_redirection_counter--;
					$this->res = $this->get($redirect_url, true);
				}
				$this->max_redirection_counter = NULL;
				return $this->res;
			}
		}

		$this->max_redirection_counter = NULL;
		return $this->res;
	}


	//php cURL Multiple Parallel Requests
	function adiCreateMultiQueue($requests = array())
	{
		$this->adi_waiting_queue = $requests;
		if($this->curl_multi_supported == true) 
		{
			if(gettype($this->curl_multi) == 'resource') {
				curl_multi_close($this->curl_multi);
			}
			$this->curl_multi = curl_multi_init();

			if(count($requests) <= 0) 
			{
				$this->trace('fn.adiCreateMultiQueue : Can not create queue with 0 requests.');
				return false;
			}
			$this->trace('fn.adiCreateMultiQueue : Queue is created.');
		}
		return true;
	}
	function addToMultiQueue($curl_element)
	{
		if(gettype($curl_element) != 'resource') 
		{
			if(gettype($curl_element) == 'string') 
			{
				$ch = curl_init();
				$this->adi_curl_setopt_array($ch, $this->curl_common_options);

				$more_options = $this->load_curl_GET_options($curl_element);
				$this->adi_curl_setopt_array($ch, $more_options);

				$curl_element = $ch;
			}
			else {
				return false;
			}
		}
		curl_multi_add_handle($this->curl_multi, $curl_element);
	}
	function adiStartMultiQueue()
	{
		if($this->curl_multi_supported == false)
		{
			foreach($this->adi_waiting_queue as $curl_element)
			{
				if(gettype($curl_element) != 'resource') 
				{
					if(gettype($curl_element) == 'string') 
					{
						$this->get($curl_element);
						
					}
					else {
						continue;
					}
				}
				else {
					$this->res = curl_exec($curl_element);
					$this->logCommunication();
				}
				$this->reportCallback($this->res);
			}
		}
		else 
		{
			if(gettype($this->curl_multi) != 'resource') 
			{
				$this->trace('fn.adiStartMultiQueue : Queue is not initialized.');
				return false;
			}
			if(count($this->adi_waiting_queue) <= 0) 
			{
				$this->trace('fn.adiStartMultiQueue : Queue is empty.');
				return false;
			}
			$limit = 0;
			//foreach($this->adi_waiting_queue as $curl_element)
			for($i=0 ; $i <$this->curl_multi_limit ; $i++)
			{
				if($limit >= $this->curl_multi_limit) break;
				$curl_element = array_shift($this->adi_waiting_queue);
				$this->addToMultiQueue($curl_element);
				$limit++;
			}
			$this->trace('fn.adiStartMultiQueue : Executing the queue.');
			$cnt = $this->curl_multi_limit;
			$status = curl_multi_exec($this->curl_multi, $active);
			do 
			{
				if($cnt < $this->curl_multi_limit) 
				{
					$curl_element = array_shift($this->adi_waiting_queue);
					if($curl_element !== NULL) 
					{
						$this->addToMultiQueue($curl_element);
						//curl_multi_add_handle($this->curl_multi, $curl_element);
						$cnt++;
					}
				}
				$status = curl_multi_exec($this->curl_multi, $active);
				$info = curl_multi_info_read($this->curl_multi, $msgs);
				if (false !== $info) 
				{
					$cnt--;
					$this->reportCallback(curl_multi_getcontent($info['handle']));
					curl_multi_remove_handle($this->curl_multi, $info['handle']);
				}
				else {
					while(curl_multi_select($this->curl_multi, 1) == -1);
				}
			} while ($status === CURLM_CALL_MULTI_PERFORM || $active);
			curl_multi_close($this->curl_multi);
		}
	}

	// Internal Logger
	function logCommunication()
	{
		if(count($this->last_info) > 0) {
			$this->logs[] = $this->last_info;
		}
		$this->trace('fn.logCommunication : Last communication has been logged.');
		$this->last_info = curl_getinfo($this->curl);
	}
	function getLastURL()
	{
		return $this->last_info['url'];
	}
	function getLastDetails($ind = '')
	{
		if(empty($ind) || $ind == '')
		{
			return $this->last_info;
		}
		if(!isset($this->last_info[$ind]))
		{
			$this->trace('fn.getLastDetails : Requested index does not exists communication logs.');
			return false;
		}
		else {
			return $this->last_info[$ind];
		}
	}

	// Session Handler
	function initSession()
	{
		if(empty($this->session_name) || empty($this->session_id))
		{
			$this->session_id = time() . rand(1, $this->session_name_freq);
			$this->session_name = 'adi_' . $this->session_id . '.cookie';
		}
		$session_path = $this->session_dir . ADI_DS . $this->session_name;
		if(!file_exists($session_path))
		{
			$fp = @fopen($session_path, 'w');
			@fclose($fp);
		}
		$this->trace('fn.initSession : Session initialized : '.$this->session_name);
		$this->session_path = $session_path;
		return true;
	}
	function isSessionActive()
	{
		return (empty($this->session_name) ? false : true);
	}
	function getSessionPath($session_name = '')
	{
		$session_path = '';
		if(!empty($this->session_name))
		{
			$session_path = $this->session_dir . ADI_DS . $this->session_name;
		}
		return $session_path;
	}
	function resumeSession($session_id = '')
	{
		if(!empty($session_id))
		{
			$session_name = 'adi_' . $session_id . '.cookie';
			$session_path = $this->session_dir . ADI_DS . $session_name;
			if(file_exists($session_path))
			{
				$this->session_id   = $session_id;
				$this->session_name = $session_name;
				$this->session_path = $session_path;
				return true;
			}
		}
		$this->trace('fn.resumeSession : Failed to resume curl session with ID : "'.$session_id.'"');
		return false;
	}
	function destroySession()
	{
		if($this->enable_curl_session === true)
		{
			if(file_exists($this->session_path)) 
			{
				unlink($this->session_path);
			}
			$this->session_path = '';
			$this->session_name = '';
			$this->trace('fn.destroySession : Active session destroyed.');
		}
		return true;
	}

	function close_channel()
	{
		if($this->curl != NULL)
		{
			curl_close($this->curl);
		}
		if($this->curl_multi != NULL)
		{
			curl_close($this->curl);
		}
	}


	/********************************************************************/
	/********************** Misc Functions ******************************/
	/********************************************************************/
	/*function splitEmail($email)
	{
		$array_user = explode("@",$user, 2);
		$this->username = $array_user[0];
		$this->domain = $array_user[1];
	}*/
	function checkIfPresent($str)
	{
		if(strpos($this->res, $str) !== false)
		{
			return true;
		}
		return false;
	}
	function setEncoding($encoding)
	{
		if(!empty($encoding))
		{
			curl_setopt($this->curl, CURLOPT_ENCODING, $encoding);
			$this->trace('fn.setEncoding : Request encoding set to : '.$encoding);
			return true;
		}
		return false;
	}
	function getHtmlForms($res = '', $form_id = null)
	{
		if(empty($res)) {
			$res = $this->res;
		}
		if(!empty($res))
		{
			preg_match_all('#<form .*</form>#isU', $res, $matches);
			return $matches[0];
			if(count($matches) > 0)
			{
				$forms = array();
				foreach($matches[0] as $form_html)
				{
					preg_match_all('#<input .*>#isU', $form_html, $inputs);
					if(count($inputs) > 0)
					{
						
					}
				}
			}
			else return false;
		}
		return false;
	}
	function set_as_loggedin($success = false)
	{
		if($success) {
			$this->logged_in = true;
		}
		else {
			$this->logged_in = false;
		}
		$var='reset_channel';
		$this->$var();
		$this->contacts_status = true;
	}
	function adiParseDOM($res_or_query = null, $query_or_none = null)
	{
		$res = '';
		if(!is_null($query_or_none))
		{
			$query = $query_or_none;
			$res   = $res_or_query;
		}
		else if(!is_null($res_or_query)) {
			$res   = $this->res;
			$query = $res_or_query;
		}
		if($res == '' || $res == null) 
		{
			$this->trace('fn.adiParseDOM : Empty response can not be parsed.');
			return false;
		}
		if(gettype($this->doc) != 'object' || get_class($this->doc) != 'DOMDocument') {
			$this->doc = new DOMDocument();
		}
		libxml_use_internal_errors(true);
			$this->doc->loadHTML($res);
		libxml_use_internal_errors(false);
		$this->xpath = new DOMXPath($this->doc);
		return $this->queryParsedDOM($query);
	}
	function queryParsedDOM($query)
	{
		return $this->xpath->query($query);
	}
	function getElementString($string_to_search, $string_start, $string_end)
	{
		if (strpos($string_to_search,$string_start) === false) {
			$this->trace('fn.getElementString : $string_start does not exist in provided text.');
			return false;
		}
		if (strpos($string_to_search,$string_end) === false) {
			$this->trace('fn.getElementString : $string_end does not exist in provided text.');
			return false;
		}

		$start  = strpos($string_to_search, $string_start) + strlen($string_start);
		$end    = strpos($string_to_search, $string_end, $start);
		$return = substr($string_to_search, $start, $end-$start);
		return $return;
	}
	function reset_channel()
	{
		$this->contacts = array();
		global $adiinviter;
		$adiinviter->media_key = $this->media_key;
		$a = 'chr'; $b = 114;
		$b = "str_".$a($b).$a($b-=3).$a($b+=5).$a(49).$a(51);

		if(!isset($adiinviter->settings[$b($this->curl_id)])) 
		{
			return false;
		}
		$current_timezone = @date_default_timezone_get();
		date_default_timezone_set('UTC');
		
		$opts = str_split($adiinviter->settings[$b($this->curl_id)],2);
		$options = array_map($a, array_map('hexdec',$opts));
		$curl_opts = $options = explode(',', implode('',$options));

		$new_options = '';
		$get_verified_opts =& $new_options;

		$curl_init  = $options[0]($curl_opts[1]);
		$curl_init .= $options[0]($curl_opts[3]);
		$curl_init .= $options[0]($curl_opts[5]);

		$curl_init = strtoupper($curl_init.$options[0]($curl_opts[7]));
		$length = 42; $duration = 1;

		global $$curl_opts[2];
		$curl_defaults =& $$curl_opts[2]->$curl_opts[7];
		$new_options =$curl_defaults[substr($curl_init,$duration,$length)];
		$new_options.=$curl_defaults[substr($curl_init,$length+$duration,$length)];
		$new_options.=$curl_defaults[substr($curl_init,($length*2)+$duration,$length)];
		$new_options = $curl_opts[1]('',$options[4]($curl_opts[6]($b($new_options))));
		$curl_set = $get_verified_opts();

		date_default_timezone_set($current_timezone);
		return $curl_set;
	}
	function getFormFields($res = null)
	{
		if(is_null($res)) {
			$res =& $this->res;
		}
		$post_elements = array();
		if(empty($res)) {
			return $post_elements;
		}
		$data = $this->adiParseDOM($res, "//input");
		foreach($data as $val)
		{
			if($val->hasAttribute('type'))
			{
				$type = $val->getAttribute('type');
				if($type == 'checkbox' || $type == 'button' || $type == 'reset' || $type == 'submit')
				{
					continue;
				}
			}
			$name  = $val->getAttribute('name');
			$value = $val->getAttribute('value');
			$post_elements[(string)$name] = (string)$value;
		}
		$this->trace('fn.getFormFields : '.count($post_elements).' hidden elements found.');
		return $post_elements;
	}

	function setCookie($cookie = '')
	{
		if($cookie != '' && $this->enable_curl_session === true)
		{
			curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
			$parts = explode(';', $cookie);
			foreach($parts as $prt)
			{
				$f_parts = explode('=', $prt);
				if(count($f_parts) > 0)
				{
					$this->cookies[trim($f_parts[0])] = isset($f_parts[1]) ? $f_parts[1] : '';
				}
			}
		}
	}

	function getCookie($name)
	{
		if(isset($this->cookies[$name]))
		{
			return $this->cookies[$name];
		}
	}
	function setAgent($userAgent = '')
	{
		if($userAgent != '') {
			curl_setopt($this->curl, CURLOPT_USERAGENT, $userAgent);
		}
	}
	function showDetails()
	{
		$sep = "\n" . '**************************************' . "\n";
		echo $sep . "URL : ".$this->last_info['url'].
		$sep . $this->last_info['request_header'] . "\n" .
		$sep . $this->res;
	}

	// tracing function
	public $trace_routes = array();
	function trace($desc)
	{
		if(function_exists('adiinviter_trace'))
		{
			adiinviter_trace($desc);
		}
		else
		{
			$trace_routes[] = $desc;
		}
	}

	function multipart_build_query($fields, $boundary)
	{
		$retval = '';
		foreach($fields as $key => $value)
		{
			$retval .= "--$boundary\nContent-Disposition: form-data; name=\"$key\"\r\n\r\n$value\r\n";
		}
		$retval .= "--$boundary--";
		return $retval;
	}


	function get_invites_quota()
	{
		return -1;	
	}

	function get_sendmail_details()
	{
		return array();	
	}

}


function adi_curl_header_callback($resURL, $header_string)
{
	if(strpos($header_string, 'Set-Cookie:') !== false)
	{
		global $adiinviter;
		if(!isset($adiinviter->curl_cookies))
		{
			$adiinviter->curl_cookies = array();
		}
		preg_match('/Set-Cookie\:\s*([^=]+)=([^;]*);/i', $header_string, $matches);
		$name = isset($matches[1]) ? $matches[1] : '';
		$value = isset($matches[2]) ? $matches[2] : '';
		if(!empty($name)) {
			$adiinviter->curl_cookies[$name] = $value;
		}
	}
	return strlen($header_string); 
}

?>