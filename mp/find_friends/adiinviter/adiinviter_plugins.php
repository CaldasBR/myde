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


class Adi_Scheduled_Plugin
{
	public $log = '';
	public $plugin_id = '';
	public $default_settings = array(
		'plugin_id'            => '',
		'plugin_on_off'        => 0,
		'plugin_title'         => '',
		'plugin_description'   => "",
		'plugin_duration_type' => 0, // 0:Relative time, 1: Absolute time
		'plugin_num_days'      => '0',
		'plugin_num_hours'     => '1',
		'plugin_num_minutes'   => '0',
		'plugin_date'          => '1',
		'plugin_hour'          => '0',
		'plugin_next_time'     => 0,
		'plugin_last_run'      => 0,
	);
	public $custom_settings = array();
	public $internal_errors = '';

	final function log_text($text = '')
	{
		$this->log .= $text."\n\n";
	}

	final function parse_plugin_id($plugin_id)
	{
		if(empty($plugin_id)) {
			return $plugin_id;
		}
		return preg_replace('/[^a-z0-9_]/i', '', $plugin_id);
	}
	final function install($cur_settings)
	{
		$response_val = false;
		$this->settings = $cur_settings;
		
		foreach($this->default_settings as $name => $value)
		{
			$this->settings[$name] = $value;
		}

		$this->adi->loadCache('language');

		if(isset($this->custom_settings) && count($this->custom_settings) > 0)
		{
			foreach($this->custom_settings as $varname => $option)
			{
				$value = $option['value'];
				if(is_array($value))
				{
					if(count($value) > 0)
					{
						foreach($value as $lang_id => $translated_text)
						{
							$this->settings[$varname.'_'.$lang_id] = $translated_text;
						}
					}
				}
				else
				{
					$this->settings[$varname] = $option['value'];
				}
			}
		}

		// Check if Plugin already exists.
		$already_exists = false;
		$plugin_id = $this->plugin_id;
		if(empty($plugin_id))
		{
			$plugin_id = get_class($this);
			if($plugin_id == 'Adi_Scheduled_Plugin')
			{
				$this->adi->trace('fn.install : Plugin ID is not set in plugin resource "'.get_class($this).'".');
				return false;
			}
		}

		$new_plugin_id = $this->parse_plugin_id($plugin_id);
		if($plugin_id != $new_plugin_id)
		{
			$this->trace('fn.install : Invalid Plugin ID "'.$plugin_id.'".');
			return false;
		}

		$this->adi->requireSettingsList('plugins');
		$all_plugins_list = $this->adi->get_installed_plugins_list();

		if(in_array($plugin_id, $all_plugins_list))
		{
			$already_exists = true;
		}

		if(!$already_exists)
		{
			$pass = true;
			if(method_exists($this, 'custom_install_procedure'))
			{
				$pass = ($this->custom_install_procedure() === true);
			}
			if($pass)
			{
				$response_val = true;

				$plugin_duration_type = $this->settings['plugin_duration_type'];
				if($plugin_duration_type == 0)
				{
					$day     = $this->settings['plugin_num_days'];
					$hour    = $this->settings['plugin_num_hours'];
					$minutes = $this->settings['plugin_num_minutes'];
					$cur_ts  = $this->adi->adi_get_utc_timestamp();
					$this->settings['plugin_next_time'] = adi_get_plugin_next_time($cur_ts,$plugin_duration_type,$day,$hour,$minutes);
				}
				else
				{
					$day    = $this->settings['plugin_date'];
					$hour   = $this->settings['plugin_hour'];
					$cur_ts = $this->adi->adi_get_utc_timestamp();
					$this->settings['plugin_next_time'] = adi_get_plugin_next_time($cur_ts,$plugin_duration_type,$day,$hour);
				}

				foreach($this->settings as $name => $value)
				{
					$response_val = adi_addSetting($plugin_id, $name, $value) && $response_val;
				}
				if(!in_array($plugin_id, $this->adi->plugins_list))
				{
					$this->adi->plugins_list[] = $plugin_id;
				}
				$this->adi->trace('fn.install : New plugin installed successfully : '.$plugin_id);
			}
		}
		else
		{
			$this->adi->trace('fn.install : Plugin already exists : '.$plugin_id);
			return true;
		}
		if(!$response_val)
		{
			$this->adi->trace('fn.install : Something went wrong while installing plugin : '.$plugin_id);
		}
		return $response_val;
	}
	function custom_install_procedure()
	{
		// Customized actions before installing this plugin.
		return true;
	}

	final function uninstall()
	{
		// Must initialise $this->adi variable with $adiinviter
		// global $adiinviter;
		// $this->adi =& $adiinviter;

		$plugin_id = $this->plugin_id;
		if(empty($plugin_id))
		{
			$plugin_id = get_class($this);
		}

		$response_val = false;
		if(!empty($this->plugin_id) && $this->adi->db_allowed === true)
		{
			$pass = true;
			if(method_exists($this, 'custom_uninstall_procedure'))
			{
				$pass = ($this->custom_uninstall_procedure() === true);
			}
			if($pass == true)
			{
				adi_build_query_write('remove_setting_group', array(
					'setting_group_name' => $plugin_id,
				));

				if( ($ind = array_search($plugin_id, $this->adi->plugins_list)) !== false )
				{
					unset($this->adi->plugins_list[$ind]);
				}
				$response_val = true;
			}
		}
		return $response_val;
	}
	function custom_uninstall_procedure()
	{
		// Customized actions before uninstalling this plugin.
		// Must return true;
		return true;
	}

	final function save_settings($settings = null)
	{
		// Must initialise $this->adi variable with $adiinviter
		// global $adiinviter;
		// $this->adi =& $adiinviter;

		if(is_null($settings) || !is_array($settings))
		{
			$settings = $this->settings;
		}
		$this->settings = $settings;

		if($this->settings_filter() === false)
		{
			return 'Plugin settings are not valid.';
		}

		if($settings['plugin_duration_type'] == 0 && $settings['plugin_num_days'] == 0 && $settings['plugin_num_hours'] == 0 && $settings['plugin_num_minutes'] == 0)
		{
			return 'Time duration setting is not valid.';
		}

		$plugin_id = $this->plugin_id;
		if(empty($plugin_id)) {
			$plugin_id = get_class($this);
		}

		if(count($settings) > 0)
		{
			// Not editable
			unset($settings['plugin_title']);
			unset($settings['plugin_description']);

			$settings['plugin_num_days']  = (int)$settings['plugin_num_days'];
			$settings['plugin_num_hours'] = (int)$settings['plugin_num_hours'];

			$settings['plugin_date'] = (int)$settings['plugin_date'];
			$settings['plugin_hour'] = (int)$settings['plugin_hour'];

			$current_settings = adi_getSetting($plugin_id);

			if($settings['plugin_duration_type'] != $current_settings['plugin_duration_type'])
			{
				$last_run  = $current_settings['plugin_last_run'];
				// $last_run  = $this->adi->adi_get_utc_timestamp();

				if($last_run == 0) { $last_run = $this->adi->adi_get_utc_timestamp(); }
				$plugin_duration_type = $settings['plugin_duration_type'];
				if($plugin_duration_type == 0)
				{
					$day  = $settings['plugin_num_days'];
					$hour = $settings['plugin_num_hours'];
					$minutes = $settings['plugin_num_minutes'];
					$settings['plugin_next_time'] = adi_get_plugin_next_time($last_run,$plugin_duration_type,$day,$hour,$minutes);
				}
				else
				{
					$day  = $settings['plugin_date'];
					$hour = $settings['plugin_hour'];
					$settings['plugin_next_time'] = adi_get_plugin_next_time($last_run,$plugin_duration_type,$day,$hour);
				}
				$settings['plugin_last_run'] = $last_run;
			}

			foreach ($settings as $name => $value)
			{
				$this->settings[$name] = $value;
				adi_saveSetting($plugin_id, $name, $value);
			}
		}
		return true;
	}

	function settings_filter()
	{
		return true;
	}


	// Mail sending function for plugins
	public $email_body = '';
	public $email_subject = '';

	public $replace_var_keys = array();
	public $replace_vars = array();

	public $sender_name = '';
	public $sender_email = '';

	public $email_mode = 'user'; // "user", "guest"

	public $sendmail_error = '';

	public $sender_channel_initialized = false;

	public $invite_details = array(); // adiinviter table row for particular invitation.

	public $parsed_email_subject = '';
	public $parsed_email_body = '';

	public $sender_cache = array();

	function init_sendmail()
	{
		$this->adi->requireSettingsList(array('global', 'db_info', 'plugins'));
		include_once(ADI_LIB_PATH.'invitation_handler.php');

		$this->sender_name  = $this->adi->settings['adiinviter_sender_name'];
		$this->sender_email = $this->adi->settings['adiinviter_email_address'];

		$this->email_subject = $this->adi->settings['adiinviter_website_name'];

		// initialise sender channel
		$this->init_sender_channel();
	}
	function set_sender_details($sender_name = null, $sender_email = null)
	{
		if(!empty($sender_name) && is_string($sender_name))
		{
			$this->sender_name = $sender_name;
		}
		if(!empty($sender_email) && is_string($sender_email))
		{
			$this->sender_email = $sender_email;
		}
	}
	function set_email_details($subject = null, $body)
	{
		if(!empty($subject) && is_string($subject))
		{
			$this->email_subject = $subject;
		}

		if(!empty($body) && is_string($body))
		{
			$this->email_body = $body;
		}

		// Parse email body
		$this->get_replace_vars_list();
	}
	function get_replace_vars_list()
	{
		// Fetch list of vars in email body
		if($this->email_body == '')
		{
			$this->sendmail_error = 'Email body is empty.';
			return false;
		}
		preg_match_all('/\[([a-z_]*)\]/i', $this->email_body, $matches);
		if(count($matches[1]) > 0)
		{
			$this->replace_var_keys = array_unique($matches[1]);
		}

		// Fetch list of vars in email subject.
		$subject_vars = array();
		if($this->email_subject == '')
		{
			$this->email_subject = $this->adi->settings['adiinviter_website_name'];
		}
		preg_match_all('/\[([a-z_]*)\]/i', $this->email_subject, $matches);
		if(count($matches[1]) > 0)
		{
			$subject_vars = array_unique($matches[1]);
			$this->replace_var_keys = array_unique(array_merge($this->replace_var_keys, $subject_vars));
		}
	}
	function prepare_replace_vars($var_keys = null)
	{
		if(!is_array($var_keys) )
		{
			$var_keys = $this->replace_var_keys;
		}
		if(count($var_keys) > 0)
		{
			// Check if sender info is required
			$sender_info_keys = array('sender_name','sender_username','sender_email','sender_avatar_url','sender_profile_url');
			$common_keys = array_intersect($var_keys, $sender_info_keys);
			if(count($common_keys) > 0)
			{
				$this->load_sender_info();
			}

			// get Website details
			$this->replace_vars['website_name'] = $this->adi->settings['adiinviter_website_name'];
			$this->replace_vars['website_logo'] = $this->adi->settings['adiinviter_website_logo'];

			// Useful URLs
			$this->replace_vars['adiinviter_root_url']   = $this->adi->adi_root_url;
			$this->replace_vars['verify_invitation_url'] = $this->adi->verify_invitation_url;
			$this->replace_vars['register_link']         = $this->adi->settings['adiinviter_website_register_url'];
			$this->replace_vars['website_url']           = $this->adi->website_url;
			$this->replace_vars['invitation_assets_url'] = $this->adi->adi_root_url.'/adi_invitations';

			// Check if receiver details are required.
			$receiver_info_keys = array('receiver_name','receiver_email');
			$common_keys = array_intersect($var_keys, $receiver_info_keys);
			if(count($common_keys) > 0)
			{
				$this->load_receiver_info();
			}

			// Fetch if Inviter count is required
			if(in_array('invitations_count', $var_keys))
			{
				$invitations_count = 0;
				$receiver_email = $this->invite_details['receiver_email'];
				$cond = '';
				if($this->adi->db_allowed == true)
				{
					$result = false;
					if(empty($receiver_email))
					{
						$result = adi_build_query_read('count_inviters_to_socialids', array(
							'social_id' => $this->invite_details['receiver_social_id'],
							'service_id' => $this->invite_details['service_used'],
						));
					}
					else
					{
						$result = adi_build_query_read('count_inviters_to_emails', array(
							'receiver_email' => $receiver_email,
						));
					}

					if($result)
					{
						if($row = adi_fetch_array($result))
						{
							$invitations_count += $row['cnt'];
						}
					}

					$this->replace_vars['invitations_count'] = $invitations_count;
				}
			}

			// Fetch if invitation_id is required
			if(in_array('invitation_id', $var_keys))
			{
				if(!isset($this->invite_details['invitation_id']) || empty($this->invite_details['invitation_id']))
				{
					$this->adi->trace('fn.prepare_replace_vars : Failed to obtain inviation_id.');
				}
				else {
					$this->replace_vars['invitation_id'] = $this->invite_details['invitation_id'];
				}
			}

			// Fetch if service details are required
			if(in_array('service_id', $var_keys))
			{
				$service_id='';
				if(!isset($this->invite_details['service_used']) || empty($this->invite_details['service_used']))
				{
					$this->adi->trace('fn.prepare_replace_vars : Failed to obtain service_id.');
				}
				else {
					$service_id = $this->invite_details['service_used'];
				}
				$this->replace_vars['service_id'] = $service_id;
			}
			if(in_array('service', $var_keys))
			{
				$service_name = '';
				if(!isset($this->invite_details['service_used']) || empty($this->invite_details['service_used']))
				{
					$this->adi->trace('fn.prepare_replace_vars : Failed to obtain service name.');
				}
				else 
				{
					$service_id = $this->invite_details['service_used'];
					$adi_services = adi_allocate_pack('Adi_Services');
					$service_info = $adi_services->get_service_details($service_id, 'info');
					if(isset($service_info[$service_id]['info']))
					{
						$service_name = $service_info[$service_id]['info']['service'];
					}
				}
				$this->replace_vars['service'] = $service_name;
			}
			
			// Fetch if issued date is required.
			if(in_array('issued_date', $var_keys))
			{
				$issued_date = '';
				if(!isset($this->invite_details['issued_date']) || empty($this->invite_details['issued_date']))
				{
					$this->adi->trace('fn.prepare_replace_vars : Failed to obtain service_id.');
				}
				else 
				{
					$issued_date = $this->adi->adi_format_timstamp($this->invite_details['issued_date']);
				}
				$this->replace_vars['issued_date'] = $issued_date;
			}

			// Get elapsed days if required.
			if(in_array('elapsed_days', $var_keys))
			{
				$elapsed_days = 0;
				if(!isset($this->invite_details['issued_date']) || empty($this->invite_details['issued_date']))
				{
					$this->adi->trace('fn.prepare_replace_vars : Failed to obtain elapsed days.');
				}
				else 
				{
					$idate = $this->invite_details['issued_date'];
					$cur_time = $this->adi->adi_get_utc_timestamp();
					$elapsed_days = floor(($cur_time - $idate) / 86400);
				}
				$this->replace_vars['elapsed_days'] = $elapsed_days;
			}

		}
	}
	function set_reminders_count($reminders_cnt = 1)
	{
		$this->vars['reminders_count'] = $reminders_cnt;
	}

	function load_sender_info($userid = null)
	{
		if(is_null($userid)) {
			$userid = $this->invite_details['inviter_id'];
		}
		$profile_url = ''; $username=''; $name=''; $email=''; $avatar_url='';
		if(!empty($userid) && $userid != 0)
		{
			if(isset($this->sender_cache[$userid]))
			{
				$name        = $this->sender_cache[$userid]['sender_name'];
				$username    = $this->sender_cache[$userid]['sender_username'];
				$email       = $this->sender_cache[$userid]['sender_email'];
				$avatar_url  = $this->sender_cache[$userid]['sender_avatar_url'];
				$profile_url = $this->sender_cache[$userid]['sender_profile_url'];
			}
			else
			{
				$user = $this->adi->getUserInfo($userid);
				$name = $user->userfullname;
				$username = $user->username;
				$email = $user->email;
				$avatar_url = $user->avatar;
				$opts = array(
					'uesrid'   => $user->userid,
					'username' => $user->username,
					'email'    => $user->email,
				);
				$profile_url = $this->adi->getProfilePageURL($opts);
				$this->sender_cache[$userid] = array(
					'sender_name'        => $name,
					'sender_username'    => $username,
					'sender_email'       => $email,
					'sender_avatar_url'  => $avatar_url,
					'sender_profile_url' => $profile_url,
				);
			}
		}
		$this->replace_vars['sender_name']  = $name;
		$this->replace_vars['sender_username']  = $username;
		$this->replace_vars['sender_email'] = $email;
		$this->replace_vars['sender_avatar_url']  = $avatar_url;
		$this->replace_vars['sender_profile_url'] = $profile_url;
	}
	
	function load_receiver_info()
	{
		$this->invite_details;
	}

	function init_sender_channel()
	{
		if($this->sender_channel_initialized == false)
		{
			$this->email_sender = adi_allocate('Adi_Send_Mail');
			$this->email_sender->init();
		}
	}
	
	function parse_contents($var_keys = null)
	{
		if(is_null($var_keys)) {
			$var_keys = $this->replace_var_keys;
		}
		$email_subject = $this->parsed_email_subject;
		$email_body = $this->parsed_email_body;
		foreach($this->replace_var_keys as $varname)
		{
			$replacement_txt = '';
			if(isset($this->replace_vars[$varname]))
			{
				$replacement_txt = $this->replace_vars[$varname];
			}
			$email_subject = str_replace('['.$varname.']', $replacement_txt, $email_subject);
			$email_body    = str_replace('['.$varname.']', $replacement_txt, $email_body);
		}
		$this->parsed_email_subject = $email_subject;
		$this->parsed_email_body = $email_body;
	}

	function parse_email_mode($email_mode = null)
	{
		if(is_null($email_mode)){
			$email_mode = $this->email_mode;
		}
		if($email_mode != 'guest') {
			$email_mode = 'user';
		}

		if($email_mode == 'user')
		{
			$this->parsed_email_body = preg_replace('#\[guest_mode\].*\[/guest_mode\]#isU', '', $this->parsed_email_body);
			$this->parsed_email_body = preg_replace('#\[/?user_mode\]#isU', '', $this->parsed_email_body);
		}
		else
		{
			$this->parsed_email_body = preg_replace('#\[user_mode\].*\[/user_mode\]#isU', '', $this->parsed_email_body);
			$this->parsed_email_body = preg_replace('#\[/?guest_mode\]#isU', '', $this->parsed_email_body);
		}
	}

	function send_email($receiver_email)
	{
		$this->email_sender->set_sender($this->sender_name, $this->sender_email);

		$this->parsed_email_subject = $this->email_subject;
		$this->parsed_email_body = $this->email_body;

		$this->parse_email_mode();
		$this->parse_contents();
		$this->email_sender->send($receiver_email, $this->parsed_email_subject, $this->parsed_email_body);
	}
}



/*
* To execute plugins handling functionalities.
*/
class Adi_Plugin_Handler extends Adi_Internal_Errors
{
	public $adi;
	function init($adiinviter)
	{
		$this->adi =& $adiinviter;
		$this->adi->requireSettingsList(array('global', 'db_info', 'plugins'));
	}

	function get_all_plugins_list()
	{
		$all_resources = array();
		if($dh = opendir(ADI_PLUGINS_PATH))
		{
			while (($filename = readdir($dh)) !== false) 
			{
				$file_path = ADI_PLUGINS_PATH . $filename;
				if($filename != '.' && $filename != '..' && is_file($file_path) && strpos($filename, '.php') !== false)
				{
					$all_resources[] = str_replace('.php', '', $filename);
				}
			}
			$this->adi->trace('fn.get_all_plugins_list : '.count($all_resources).' plugins found.');
		}
		else
		{
			$this->report_error('fn.get_all_plugins_list : Failed to read plugins directory : '.ADI_PLUGINS_PATH, true);
		}
		return $all_resources;
	}

	function scan_for_plugins()
	{
		$all_resources = $this->get_all_plugins_list();
		$response_val = false;
		if(count($all_resources) > 0)
		{
			foreach($all_resources as $plugin_id)
			{
				$response_val = $this->install_plugin($plugin_id);
			}
		}
		return $response_val;
	}

	function install_plugin($plugin_id)
	{
		$response_val = false;
		$common_settings = array(
			'plugin_on_off'        => 1,
			'plugin_title'         => '',
			'plugin_description'   => '',
			'plugin_next_time'     => 0,
			'plugin_last_run'      => 0,

			'plugin_duration_type' => 0,
			'plugin_num_days'      => '1',
			'plugin_num_hours'     => '0',
			'plugin_num_minutes'   => '0',
			'plugin_date'          => '0',
			'plugin_hour'          => '1',
		);

		$file_path = ADI_PLUGINS_PATH . $plugin_id.'.php';

		if(file_exists($file_path))
		{
			include_once($file_path);
			if(class_exists($plugin_id))
			{
				$plugin = new $plugin_id();
				$plugin->adi =& $this->adi;

				// Install Plugin
				$this->adi->trace('fn.scan_for_plugins : Installing plugin : '.$plugin_id);
				if($plugin->install($common_settings, $plugin_id))
				{
					$response_val = $plugin_id;
				}
				else
				{
					$this->report_error('fn.scan_for_plugins : Failed to install plugin : '.$plugin_id);
				}
			}
			else
			{
				$this->report_error('fn.scan_for_plugins : plugin file "'.$plugin_id.'.php" does not contain the resource : '.$plugin_id);
			}
		}
		return $response_val;
	}

	function uninstall_plugin($plugin_id)
	{
		$found = false;
		$this->plugin_id = $plugin_id;

		if(empty($plugin_id))
		{
			$this->adi->trace('fn.uninstall_plugin : plugin does not exists.');
		}
		else
		{
			$plugin_file_path = ADI_PLUGINS_PATH . $plugin_id.'.php';
			$plugin = null;
			if(file_exists($plugin_file_path))
			{
				include_once($plugin_file_path);
				if(class_exists($plugin_id))
				{
					$plugin = new $plugin_id();
				}
				else
				{
					$this->adi->trace('fle.adiinviter_plugins : Plugin file "'.$plugin_id.'.php" does not contain the resource : '.$plugin_id, true);
				}
			}
			else
			{
				$this->adi->trace('fn.uninstall_plugin : Plugin file does not found : '.$plugin_file_path, true);
			}

			if(is_null($plugin))
			{
				$plugin = new Adi_Scheduled_Plugin();
			}
			$plugin->adi =& $this->adi;
			$plugin->plugin_id = $plugin_id;

			if(method_exists($plugin, 'uninstall'))
			{
				if($plugin->uninstall())
				{
					return true;
				}
				else 
				{
					$this->report_error('fn.uninstall_plugin : Failed to uninstall the plugin "'.$plugin_id.'" located at : '.$plugin_file_path, true);
				}
			}
		}
		return false;
	}

	function update_plugins_list()
	{
		if($this->adi->db_allowed == true)
		{
			$all_plugins_list = $this->adi->get_all_plugins_list(true);
			return true;
		}
		return false;
	}
}



function parse_plugin_time_val($val, $lower_limit = 1, $upper_limit = 31)
{
	$days = array();
	$val  = trim($val, " \n\t\r,");
	$val  = preg_replace('/[^0-9\/\*\-\,]/i', '', $val);

	if($val == '*')
	{
		$days = range($lower_limit, $upper_limit);
	}
	else if(is_numeric($val) && $val <= $upper_limit && $val >= $lower_limit)
	{
		$days[] = ($val < 10) ? '0'.$val : $val;
	}
	else
	{
		$all_terms = explode(',', $val);
		foreach($all_terms as $term)
		{
			if(strpos($term, '-') !== false)
			{
				$tmp = explode('-', $term);
				if((int)$tmp[0] < (int)$tmp[1])
				{
					$days = array_merge($days, range($tmp[0], $tmp[1]));
				}
			}
			else if(strpos($term, '/') !== false)
			{
				$tmp = explode('/', $term);
				if($tmp[0] == '*' && is_numeric($tmp[1]))
				{
					$days = array($term);
					break;
				}
			}
			else if(is_numeric($term) && $term <= $upper_limit && $term >= $lower_limit)
			{
				$days[] = ($term < 10) ? '0'.$term : $term;
			}
		}
	}
	sort($days);
	$days = array_unique($days);
	return $days;
}



function adi_get_plugin_next_time($cur_ts, $duration_type, $num_days, $num_hours, $num_minutes = 0)
{
	$mar = ($cur_ts % 60);
		$cur_ts -= $mar;
	/*if($mar < 15) {
	}
	else {
		$cur_ts += (60-$mar);
	}*/

	$num_days  = (int)$num_days;
	$num_hours = (int)$num_hours;

	$next_ts = 0;
	if($duration_type == 0)
	{
		$num_minutes = (int)$num_minutes;

		// Relative Time
		$num_days    = max(0, min(31, $num_days));
		$num_hours   = max(0, min(23, $num_hours));
		$num_minutes = max(0, min(59, $num_minutes));
		
		$diff = ($num_days * 86400) + ($num_hours * 3600) + ($num_minutes * 60);
		$next_ts = $cur_ts + $diff;
	}
	else
	{
		// Absolute Time
		$num_days = max(1, min(31, $num_days));
		$num_hours = max(0, min(23, $num_hours));

		$month = date('n', $cur_ts);
		$year  = date('Y', $cur_ts);

		while(!checkdate($month, $num_days, $year)) {
			$num_days--;
		}

		$tmp = adi_mktime($num_hours, 0, 0, $month, $num_days, $year);

		if($tmp < $cur_ts)
		{
			$month++;
			if($month == 13)
			{
				$month = 1;
				$year++;
			}
			$tmp = adi_mktime($num_hours, 0, 0, $month, $num_days, $year);
			$next_ts = $tmp;
		}
		else
		{
			$next_ts = $tmp;
		}
	}
	// $next_ts = mktime(date("H", $next_ts), 0, 0, date("n", $next_ts), date("j", $next_ts), date("Y", $next_ts));
	return $next_ts;
}




/*
* Wrapper class for sending mails through AdiInviter plugins.
*/
class Adi_plugin_Send_Mail extends Adi_Internal_Errors
{
	public $subject;
	public $mail_body;
	function init() 
	{}
}


?>