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


class Adi_Session_Base
{
	public $session_name = 'PHPSESSID';
	public $active = false;
	function __construct() {}
	function init()
	{
		if($this->verify())
		{
			$this->active = true;
			adiinviter_trace('Session : Already initialized.');	
		}
		else 
		{
			if(headers_sent())
			{
				adi_throwErrorDesc('AdiInviter Error : Cannot initialize session. Headers already sent.');
				return false;
			}
			else
			{
				$sesssion_path = ini_get('session.save_path');
				/*if(!file_exists($sesssion_path))
				{
					echo 'session.save_path does not contain a valid path on your server.';
					exit;
				}*/
				$host = $_SERVER['HTTP_HOST'];
				$host = (strpos($host, 'www.') !== false ? str_replace('www.', '.', $host) : '.'.ltrim($host));
				session_set_cookie_params(0, '/', $host, false, false);
				if(!empty($this->session_name))
            {
               session_name($this->session_name);
            }
				session_start();
				$this->active = true;
				adiinviter_trace('Session : initialized.');
			}
		}
	}
	function verify()
	{
		return isset($_SESSION);
	}
	function get($key) 
	{
		if(!$this->active) {
			$this->init();
		}
		return (isset($_SESSION[$key]) ? $_SESSION[$key] : '');
	}
	function set($key, $value) 
	{
		if(!$this->active) {
			$this->init();
		}
		$_SESSION[$key] = $value;
		return true;
	}
	function remove($key)
	{
		if(!$this->active) {
			$this->init();
		}
		if(isset($_SESSION[$key]) ) {
			unset($_SESSION[$key]);
			return true;
		}
		else {
			return false;
		}
	}
	function is_set($key)
	{
		if(!$this->active) {
			$this->init();
		}
		return isset($_SESSION[$key]);
	}
}


class Adi_Permissions_Base
{
	// Permissions
	public $can_use_adiinviter = true;
	public $can_delete_invites = false;
	public $can_download_csv   = false;
	public $show_recaptcha     = false;
	public $last_num_invites   = 'Unlimited';

	function reset_usergroup_perms()
	{
		global $adiinviter;

		// Remove all previous set permissions
		$ug_perms = $this->getPermsForAllUsergroups();
		foreach($ug_perms as $gid => $perms)
		{
			$this->removeUsergroupPermissions($gid);
		}

		$usergroups = $adiinviter->getAllUsergroupsInfo();
		$ug_perms = array();
		$non_logged_in_groupid = $adiinviter->getGuestUsergroupId();
		foreach($usergroups as $gid => $name)
		{
			if($gid == $non_logged_in_groupid)
			{
				$ug_perms[$gid] = array(1,1,1, 0,0,0, "Unlimited");
			}
			else
			{
				$ug_perms[$gid] = array(1,1,1, 0,0,0, "Unlimited");
			}
		}
		$adiinviter->permissions->removeUsergroupPermissions(0);
		$adiinviter->permissions->storeUsergroupPermissions($ug_perms);
	}

	function getPermsForUsergroup($usergroup_id = 0)
	{
		global $adiinviter;
		$all_perms = isset($adiinviter->settings['usergroup_permisssions']) ? $adiinviter->settings['usergroup_permisssions'] : array();
		if(count($all_perms) > 0 && isset($all_perms[$usergroup_id]))
		{
			return $all_perms[$usergroup_id];
		}
		return false;
	}

	// Get all usergroup permissions
	function getPermsForAllUsergroups($size = 10, $offset = 0)
	{

		$perms_arr = array();
		global $adiinviter;
		$all_perms = isset($adiinviter->settings['usergroup_permisssions']) ? $adiinviter->settings['usergroup_permisssions'] : array();
		if(count($all_perms) > 0)
		{
			if($size > 0) {
				$perms_arr = array_slice($all_perms, $offset, $size, true);
			}
			else {
				$perms_arr = $all_perms;
			}
		}
		return $perms_arr;
	}

	function getUsergroupPermsCount()
	{
		global $adiinviter;
		$all_perms = isset($adiinviter->settings['usergroup_permisssions']) ? $adiinviter->settings['usergroup_permisssions'] : array();
		return count($all_perms);
	}


	function storeUsergroupPermissions($new_perms)
	{
		if(count($new_perms) > 0)
		{
			global $adiinviter;
			$all_perms = isset($adiinviter->settings['usergroup_permisssions']) ? $adiinviter->settings['usergroup_permisssions'] : array();
			$update_setting = false;
			foreach($new_perms as $usergroup_id => $perms)
			{
				if(!empty($usergroup_id) || $usergroup_id == 0)
				{
					// Update user num_invites
					$adiinviter->updateUsergroupNumInvites($usergroup_id, $perms[$adiinviter->last_num_invites_ind]);

					$all_perms[$usergroup_id] = $perms;

					$update_setting = true;
				}
			}

			if($update_setting)
			{
				adi_saveSetting('db_info', 'usergroup_permisssions', $all_perms);
			}
		}
		return true;
	}

	function removeUsergroupPermissions($usergroupid)
	{
		if(!empty($usergroupid) || $usergroupid == 0)
		{
			global $adiinviter;
			$all_perms = isset($adiinviter->settings['usergroup_permisssions']) ? $adiinviter->settings['usergroup_permisssions'] : array();
			if(isset($all_perms[$usergroupid]))
			{
				unset($all_perms[$usergroupid]);
				adi_saveSetting('db_info', 'usergroup_permisssions', $all_perms);
			}
			return true;
		}
		else
		{
			adiinviter_trace('Invalid usergroupid.');
		}
	}
}


class Adi_Error_Base
{
	public $show_error = false;
	public $errors = array();
	public $last_error = '';
	function report_error($err_msg, $source_id = '')
	{
		if(!empty($err_msg) && !in_array($err_msg, $this->errors))
		{
			$this->errors[] = $this->last_error = $err_msg;
			global $adiinviter;
			$adiinviter->trace('Internal Error Reported '.(empty($source_id)?'':'('.$source_id.') ').': '.$err_msg);
			$this->show_error = true;
		}
	}
	function get_error_message()
	{
		return $this->last_error;
	}
	function get_error_count()
	{
		return count($this->errors);
	}
	function generate_error_for_js()
	{
		$ret_str = '';
		if(count($this->errors) > 0)
		{
			foreach($this->errors as $err_msg)
			{
				$ret_str .= "adi.show_pp_err('".$err_msg."');\n";
			}
		}
		return $ret_str;
	}
	function generate_error_for_inpage()
	{
		$hide_on_start = ''; $err_msg = '';
		if(count($this->errors) > 0)
		{
			$hide_on_start = ' style="visibility:visible;"';
			$err_msg = $this->errors[0];
		}
		$adi_pp_error_icon  = $this->images_url . 'error_ico.png';
		$ret_str = '<div class="adi_inpage_error_out"'.$hide_on_start.'>
		<center><table class="adi_clear_table adi_inpage_error_table">'.
		"<tr class='adi_clear_tr'><td valign='top' class='adi_clear_td'><img class='adi_inpage_error_icon' src='" . $adi_pp_error_icon . "'></td><td valign='center' class='adi_clear_td adi_inpage_error_msg'>".$err_msg."</td></tr>".
		'</table></center></div>';
		return $ret_str;
	}
}


class Adi_Internal_Errors
{
	public $all_errors = array();
	public $error_count = 0;
	public $error_msg = '';
	final function report_error($error_msg, $report_error = false)
	{
		$this->all_errors[] = $error_msg;
		$this->error_count++;
		if( $report_error === true && function_exists('adi_throwErrorDesc'))
		{
			adi_throwErrorDesc($error_msg);
		}
	}

	final function report_trace($trace_msg, $report_error = false)
	{
		if( $report_error === true && function_exists('adiinviter_trace'))
		{
			adiinviter_trace($trace_msg);
		}
	}

	function get_errors()
	{
		return $this->all_errors;
	}
	function get_errors_count()
	{
		return count($this->all_errors);
	}
	function get_errors_message()
	{
		return implode("\n\n", $this->all_errors);
	}
}

class Adi_Database_Queries extends Adi_Internal_Errors
{
	public $repeat_statement_separator = ',';
	public $queries = array();
	function init_queries()
	{
		$tb_adiinviter              = ADI_TABLE_PREFIX . "adiinviter";
		$tb_adiinviter_conts        = ADI_TABLE_PREFIX . "adiinviter_conts";
		$tb_adiinviter_guest        = ADI_TABLE_PREFIX . "adiinviter_guest";
		$tb_adiinviter_lang         = ADI_TABLE_PREFIX . "adiinviter_lang";
		$tb_adiinviter_queue        = ADI_TABLE_PREFIX . "adiinviter_queue";
		$tb_adiinviter_services     = ADI_TABLE_PREFIX . "adiinviter_services";
		$tb_adiinviter_settings     = ADI_TABLE_PREFIX . "adiinviter_settings";

		$this->queries = array(
// General
'check_for_table' => "SHOW TABLES LIKE '%[query_text]%'",
'get_all_tables'  => "SHOW TABLES",
'check_table_structure' => "DESCRIBE [table_name]",
'rename_table' => "RENAME TABLE [current_name] TO [new_name]",

'create_settings_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_settings` (
	`group_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`value` text COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`group_name`,`name`)
)",
'create_services_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_services` (
	`id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
	`info` text COLLATE utf8_unicode_ci NOT NULL,
	`params` text COLLATE utf8_unicode_ci NOT NULL,
	`domains` text COLLATE utf8_unicode_ci NOT NULL,
	`logos` text COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`id`)
)",
'create_language_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_lang` (
	`lang_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`theme_id` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
	`name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
	`value` text COLLATE utf8_unicode_ci NOT NULL,
	UNIQUE KEY `lang_id` (`lang_id`,`theme_id`,`name`)
)",
'create_inviations_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter` (
	`invitation_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`inviter_id` int(11) NOT NULL,
	`guest_id` int(11) NOT NULL DEFAULT '0',
	`receiver_userid` int(11) NOT NULL DEFAULT '0',
	`receiver_username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`receiver_name` text COLLATE utf8_unicode_ci NOT NULL,
	`receiver_social_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`receiver_email` text COLLATE utf8_unicode_ci NOT NULL,
	`invitation_status` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`service_used` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`issued_date` bigint(20) NOT NULL,
	`type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
	`campaign_id` varchar(60) NOT NULL DEFAULT '',
	`content_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
	`topic_redirect` int(11) NOT NULL DEFAULT '0',
	`visited` int(1) NOT NULL DEFAULT '0'
)",
'create_guest_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_guest` (
	`guest_id` int(11) NOT NULL DEFAULT '0',
	`email` text COLLATE utf8_unicode_ci NOT NULL,
	`name` text COLLATE utf8_unicode_ci NOT NULL
)",
'create_queue_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_queue` (
	`mqueueid` int(80) NOT NULL AUTO_INCREMENT,
	`invitation_id` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`toemail` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`subject` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	`sender_info` varchar(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`mqueueid`)
)",
'create_conts_table' => "CREATE TABLE IF NOT EXISTS `$tb_adiinviter_conts` (
	`list_id` varchar(32) NOT NULL,
	`userid` int(11) NOT NULL,
	`data` mediumtext NOT NULL,
	`create_date` int(11) NOT NULL,
	PRIMARY KEY (`list_id`)
)",


// Settings
'fetch_setting' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([setting_group_ids]) AND name = '[setting_name]'",
'fetch_setting_groups' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([setting_group_ids])",
'fetch_setting_groups_like' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name LIKE 'Adi_Plugin_%'",
'add_setting' => "REPLACE INTO $tb_adiinviter_settings VALUES('[setting_group_name]', '[setting_name]', '[setting_value]')",
'update_setting' => "UPDATE $tb_adiinviter_settings SET value = '[setting_value]' WHERE name='[setting_name]' AND group_name = '[setting_group_name]'",
'remove_setting_group' => "DELETE FROM $tb_adiinviter_settings WHERE group_name = '[setting_group_name]'",
'remove_setting' => "DELETE FROM $tb_adiinviter_settings WHERE group_name = '[setting_group_name]' AND name = '[setting_name]'",
'search_for_message_templates' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([plugins_ids_list]) AND name LIKE '%_en'",
'get_sorted_plugins_ids' => "SELECT group_name FROM $tb_adiinviter_settings WHERE name = 'plugin_next_time' ORDER BY value ASC ",
'check_plugins_list' => "SELECT * FROM $tb_adiinviter_settings WHERE name = 'plugin_on_off'",

// Language
'get_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE name IN ([phrase_varnames]) AND lang_id = '[lang_id]'",
'get_all_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE lang_id = '[lang_id]'",
'get_theme_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE theme_id = '[theme_id]'",
'get_all_lang_ids' => "SELECT DISTINCT lang_id FROM $tb_adiinviter_lang",
'insert_phrase' => "INSERT INTO $tb_adiinviter_lang VALUES ('[lang_id]', '[theme_id]', '[var_name]', '[phrase_text]')",
'insert_phrases' => "REPLACE INTO $tb_adiinviter_lang VALUES [REPEAT_STATEMENT]('[lang_id]', '[theme_id]', '[var_name]', '[phrase_text]')[/REPEAT_STATEMENT]",
'update_phrase' => "UPDATE $tb_adiinviter_lang SET value='[phrase_text]' WHERE name='[var_name]' AND lang_id = '[lang_id]'",
'remove_phrase_from_all' => "DELETE FROM $tb_adiinviter_lang WHERE name = '[var_name]'",
'remove_language' => "DELETE FROM $tb_adiinviter_lang WHERE lang_id = '[lang_id]'",
'search_in_phrases' => "SELECT * FROM `$tb_adiinviter_lang` WHERE [in_language] lang_id = '[lang_id]' AND [/in_language] ([search_in_vars]value LIKE '%[serach_query]%'[/search_in_vars][search_in_both] OR [/search_in_both][search_in_text]name LIKE '%[serach_query]%'[/search_in_text]) ORDER BY name ASC",

// Scheduled Plugins
'get_executing_plugins' => "SELECT * FROM $tb_adiinviter_settings WHERE name = 'plugin_next_time' AND value < [current_time]",


// Invitations
'get_invs_count' => "SELECT COUNT(1) AS cnt FROM $tb_adiinviter WHERE inviter_id = [userid] [status_check] AND invitation_status = '[invitation_status]'[/status_check] [query_check] AND (receiver_name LIKE '[query]%' OR receiver_name LIKE '% [query]%' OR receiver_email LIKE '[query]%')[/query_check]",

'count_inviters_to_emails' => "SELECT count(1) as cnt from $tb_adiinviter WHERE campaign_id = '' AND receiver_email = '[receiver_email]'",
'count_inviters_to_socialids' => "SELECT count(1) as cnt from $tb_adiinviter WHERE campaign_id = '' AND receiver_social_id = '[social_id]' AND service_used = '[service_id]'",

'get_invs' => "SELECT * FROM $tb_adiinviter WHERE inviter_id = [userid]
		[status_check] AND invitation_status = '[invitation_status]' [/status_check]
		[query_check] AND (receiver_name LIKE '[query]%' OR receiver_name LIKE '% [query]%' OR receiver_email LIKE '[query]%')[/query_check]
		ORDER BY issued_date DESC LIMIT [offset], [size]",

'delete_own_invites' => "DELETE FROM $tb_adiinviter WHERE invitation_id = '[invitation_id]' AND inviter_id = [inviter_id]",
'delete_invites' => "DELETE FROM $tb_adiinviter WHERE invitation_id = '[invitation_id]'",
'get_email_invites_count' => "SELECT count(1) as cnt from $tb_adiinviter WHERE inviter_id = [userid] AND receiver_email !=''",
'get_csv_contacts' => "SELECT receiver_email, receiver_name FROM $tb_adiinviter WHERE inviter_id = [userid] AND receiver_email != ''",
'get_socialid_details' => "SELECT * FROM $tb_adiinviter WHERE receiver_social_id IN ([socialids_list])",
'get_invitation_details' => "SELECT * FROM $tb_adiinviter WHERE invitation_id IN ([invitation_ids])",
'get_invs_to_emails' => "SELECT * FROM $tb_adiinviter WHERE receiver_email IN ([emails_list])",
'get_invs_to_socialids' => "SELECT * FROM $tb_adiinviter WHERE receiver_social_id IN ([socialids_list]) AND service_used = '[service_id]'",
'update_invite_status' => "UPDATE $tb_adiinviter SET invitation_status = '[status]' WHERE invitation_id = '[invitation_id]'",
'mark_invite_as_registered' => "UPDATE $tb_adiinviter SET invitation_status = 'accepted', receiver_username = '[username]', receiver_userid = [userid], receiver_email = '[email]' WHERE invitation_id = '[invitation_id]'",
'mark_email_as_registered' => "UPDATE $tb_adiinviter SET invitation_status = 'accepted', receiver_username = '[username]', receiver_userid = [userid], receiver_email = '[email]' WHERE invitation_id = '[invitation_id]' OR receiver_email = '[receiver_email]'",
'mark_socialid_as_registered' => "UPDATE $tb_adiinviter SET invitation_status = 'accepted', receiver_username = '[username]', receiver_userid = [userid], receiver_email = '[email]' WHERE invitation_id = '[invitation_id]' OR (receiver_social_id = '[socialid]' AND service_used = '[service_id]')",
'check_user_redirection' => "SELECT * FROM $tb_adiinviter WHERE [field_name] = '[field_value]' AND campaign_id != '' AND topic_redirect = 1",
'delete_invitation' => "DELETE FROM $tb_adiinviter WHERE inviter_id = [inviter_id]
[campaign_id_check] AND campaign_id = '[campaign_id]' AND content_id = '[content_id]'[/campaign_id_check]
[social_invitation] AND service_used = '[service_id]' AND receiver_social_id IN ('[social_id]') [/social_invitation]
[email_invitation] AND receiver_email IN ([receiver_email])[/email_invitation]",
'add_invitation' => "INSERT INTO $tb_adiinviter VALUES [REPEAT_STATEMENT]('[invitation_id]', [userid], [guest_id], 0, '', '[receiver_name]', '[social_id]', '[receiver_email]', '[status]', '[service_id]', [issued_date], '[service_type]', '[campaign_id]', '[content_id]', [topic_redirect], [visited])[/REPEAT_STATEMENT]",
'check_guest_id' => "SELECT * FROM $tb_adiinviter_guest WHERE [field_name] = '[field_value]'",
'get_max_guest_id' => "SELECT MAX(guest_id) as mx FROM $tb_adiinviter_guest",
'add_guest_details' => "INSERT INTO $tb_adiinviter_guest VALUES ([guest_id], '[sender_email]', '[sender_name]')",
'update_guest_details' => "UPDATE $tb_adiinviter_guest SET userid = [userid] WHERE [field_name] = '[field_value]'",
'delete_guest_details' => "DELETE FROM $tb_adiinviter_guest WHERE [field_name] = '[field_value]'",

'update_invitations' => "UPDATE $tb_adiinviter SET `[update_field]` = [update_value] WHERE [check_field] = '[check_value]'",
'get_invites_statistics' => "SELECT inviter_id, COUNT( 1 ) AS total,
	SUM(CASE WHEN invitation_status = 'invitation_sent'  THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'blocked'  THEN 1 ELSE 0 END ) AS blocked,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN invitation_status = 'waiting'  THEN 1 ELSE 0 END ) AS waiting
	FROM $tb_adiinviter WHERE inviter_id = [userid]",
'get_invite_dates' => "SELECT MIN(issued_date) as fs, MAX(issued_date) as ls FROM $tb_adiinviter",
'get_invites_for_duration' => "SELECT issued_date,
	SUM(CASE WHEN invitation_status = 'invitation_sent' THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM $tb_adiinviter as adiinviter
	WHERE issued_date > [start_date] AND issued_date <= [last_date]",
'get_short_invites_for_duration' => "SELECT count(inviter_id) as cnt,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM $tb_adiinviter as adiinviter WHERE issued_date > [start_date] AND issued_date < [last_date]",
'get_invites_summary' => "SELECT issued_date,
	SUM(CASE WHEN invitation_status = 'invitation_sent' THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM $tb_adiinviter",
'get_top_inviters' => "SELECT inviter_id, count(inviter_id) as cnt,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked,
	SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited
	FROM $tb_adiinviter as adiinviter WHERE inviter_id != 0 
	GROUP BY inviter_id ORDER BY cnt DESC LIMIT [offset],[size]",

// Users
'get_user_details' => "SELECT * FROM `[user_table]` WHERE `[userid_field]` = [userid]",
'get_userids_details' => "SELECT * FROM `[user_table]` WHERE `[userid_field]` IN ([userids])",
'get_username_details' => "SELECT * FROM `[user_table]` WHERE `[username_field]` = '[username_value]'",
'get_email_details' => "SELECT * FROM `[user_table]` WHERE `[email_field]` IN ([emails_list])",
'update_invites_limit_all' => "UPDATE `[user_table]` SET adi_num_invites = '[num_invites]'",
'update_invites_limit' => "UPDATE `[user_table]` SET adi_num_invites = '[num_invites]' WHERE `[field_name]` = [field_value]",
'update_invites_limit_mapping' => "UPDATE `[user_table]` AS u INNER JOIN `[usergroup_mapping_table]` AS umt ON (u.[user_userid_field] = umt.[mapping_userid_field]) SET u.adi_num_invites = '[num_invites]' WHERE umt.[mapping_usergroupid_field] = '[usergroupid]'",
'reduce_invites_limit' => "UPDATE `[user_table]` SET adi_num_invites = adi_num_invites - '[num_invites]' WHERE `[field_name]` = [field_value]",
'add_invite_limit_column' => "ALTER TABLE `[table_name]` add column adi_num_invites VARCHAR(40) DEFAULT 'Unlimited'",
'remove_invite_limit_column' => "ALTER TABLE `[table_name]` DROP COLUMN `adi_num_invites`",


// Usergroups
'get_usergroups_details' => "SELECT * FROM `[usergroup_table]`",
'get_usergroup_mapping' => "SELECT * FROM `[usergroup_mapping_table]` WHERE `[userid_field]` = [userid]",

// Avatar
'get_avatar_details' => "SELECT * FROM `[avatar_table]` WHERE `[userid_field]` = [userid]",

// Friends
'check_friend_request' => "SELECT * FROM `[friend_table]` WHERE (`[userid_field]` = [userid] AND `[friendid_field]` = [friendid])",
'friend_request_with_status' => "INSERT INTO `[friend_table]` (`[userid_field]`,`[friendid_field]`,`[status_field]`) VALUES([userid], [friendid], '[status_value]')",
'friend_request' => "INSERT INTO `[friend_table]` (`[userid_field]`,`[friendid_field]`) VALUES([userid], [friendid])",
'get_user_friends' => "SELECT * FROM `[friends_table]` WHERE (`[userid_field]` = [userid]) OR (`[friendid_field]` = [userid])",
'get_mutual_friends' => "SELECT * FROM [friends_table] WHERE `[userid_field]` IN ([userids]) AND `[friendid_field]` IN ([friendids])",
'get_mutual_friends_status' => "SELECT * FROM [friends_table] WHERE `[userid_field]` IN ([userids]) AND `[friendid_field]` IN ([friendids]) AND `[status_field]` = '[status_value]'",

// Mail Queue
'mail_queue_count' => "SELECT count(mqueueid) as cnt FROM $tb_adiinviter_queue",
'get_mails_from_queue' => "SELECT * FROM $tb_adiinviter_queue WHERE toemail LIKE '%@%' ORDER BY mqueueid ASC LIMIT 0, [mails_count]",
'get_mails_from_twitter_queue' => "SELECT * FROM $tb_adiinviter_queue WHERE sender_info LIKE '%twitter%' ORDER BY mqueueid ASC LIMIT 0, [mails_count]",
'add_to_mail_queue' => "INSERT INTO $tb_adiinviter_queue VALUES(0,'[invitation_id]', '[receiver_email]', '[mail_subject]', '[mail_body]', '[sender_info]')",
'remove_from_mail_queue' => "DELETE FROM $tb_adiinviter_queue WHERE mqueueid = [mqueueid]",

// Campaign
'get_content_details' => "SELECT * FROM `[content_table]` WHERE `[contentid_field]` = [content_id]",

// Contacts Cache
'insert_list_cache' => "INSERT INTO $tb_adiinviter_conts VALUES ('[list_id]', '[importer_userid]', '[cache_data]', '[create_date]')",
'get_list_cache' => "SELECT * FROM $tb_adiinviter_conts WHERE list_id = '[list_id]'",
'update_list_cache' => "UPDATE $tb_adiinviter_conts SET data = '[cache_data]' WHERE list_id = '[list_id]'",
'clear_list_cache' => "DELETE FROM $tb_adiinviter_conts WHERE list_id = '[list_id]'",
'auto_clear_listcache' => "DELETE FROM $tb_adiinviter_conts WHERE create_date < [create_date]",
'remove_previous_listache' => "DELETE FROM $tb_adiinviter_conts WHERE userid = [userid]",

		);
	}

	function buildQuery( $query_id, $params = array() )
	{
		if( !isset($this->queries[$query_id]) )
		{
			adiinviter_trace('Query not found for query_id : "'.$query_id.'"');
			return false;
		}
		if( empty($this->queries[$query_id]) )
		{
			adiinviter_trace('Query is empty for query_id : "'.$query_id.'"');
			return false;
		}

		$query = $this->queries[$query_id];
		if( count($params) > 0 )
		{
			if(isset($params['adi_query_conditions']))
			{
				foreach($params['adi_query_conditions'] as $key => $cond)
				{
					if($cond == true)
					{
						$query = str_replace(array('['.$key.']','[/'.$key.']'), array('',''), $query);
					}
					else
					{
						$query = preg_replace('/\['.$key.'\].*\[\\/'.$key.'\]/i', '', $query);
					}
				}
			}
			if(isset($params['repeat_for']) && strpos($query, '[REPEAT_STATEMENT]') !== false && strpos($query, '[/REPEAT_STATEMENT]') !== false)
			{
				$t = explode('[REPEAT_STATEMENT]', ' '.$query.' ', 2);
				$first_part  = $t[0];
				$t = explode('[/REPEAT_STATEMENT]', ' '.$t[1].' ', 2);
				$repeat_part = $t[0];
				$second_part = $t[1];
				$m_str = '';$ext = '';
				foreach($params['repeat_for'] as $vars)
				{
					$cur_pass = $repeat_part;
					foreach( $vars as $key => $value )
					{
						if( is_array($value) )
						{
							$value = "'".implode("','", $value)."'";
						}
						$cur_pass = str_replace('['.$key.']', trim($value), $cur_pass);
					}
					$m_str .= $ext . $cur_pass;
					$ext = $this->repeat_statement_separator;
				}
				$query = $first_part . $m_str . $second_part;
			}
			else
			{
				foreach( $params as $key => $value )
				{
					if( is_array($value) )
					{
						$value = "'".implode("','", $value)."'";
					}
					$query = str_replace('['.$key.']', trim($value), $query);
				}
			}
		}
		return $query;
	}

	function buildAndRead( $query_id, $params = array() )
	{
		$query = $this->buildQuery($query_id, $params);
		if(is_string($query)) 
		{
			$log = '';
			adiinviter_trace('Query Executed : '.$query);
			if($result = $this->adi_query_read($query))
			{}
			else
			{
				adi_throwErrorDesc('Query failed to execute ['.$query_id.'] : '.$query.' : '.$this->adi_get_error());
			}
			return $result;
		}
		else {
			return false;
		}
	}
	function buildAndWrite( $query_id, $params = array() )
	{
		$query = $this->buildQuery($query_id, $params);
		if(is_string($query)) 
		{
			$log = '';
			adiinviter_trace('Query Executed : '.$query);
			if($result = $this->adi_query_write($query))
			{}
			else
			{
				adi_throwErrorDesc('Query failed to execute : ' . $query.' : '.$this->adi_get_error());
			}
			return $result;
		}
		else {
			return false;
		}
	}
}




class Adi_Invite_History_Base
{
	public $ih_column_receiver_details = true;
	public $ih_column_service_info = false;
	public $ih_column_status = true;
	public $ih_column_issued_date = true;

	public $summary_defaults = array(
		'total'    => 0,
		'accepted' => 0,
		'invitation_sent' => 0,
		'blocked'  => 0,
		'waiting'  => 0,
	);
	public $show_types = array('accepted', 'blocked', 'invited');

	public $invite_history_pagination_size = 25;

	function get_invitations_count($userid, $show_type = 'all', $search_query = '')
	{
		$conditions = array();
		$summary = $this->summary_defaults;
		if(!in_array($show_type, $this->show_types))
		{
			$show_type = 'all';
		}

		if($show_type == 'invited') {
			$show_type = 'invitation_sent';
		}
		$result = adi_build_query_read('get_invs_count', array(
			'userid' => $userid,
			'invitation_status' => $show_type,
			'query' => $search_query,
			'adi_query_conditions' => array(
				'status_check' => (!empty($show_type) && $show_type != 'all'),
				'query_check'  => (!empty($search_query)),
			),
		));

		$cnt = 0;
		if($row = adi_fetch_array($result))
		{
			$cnt = $row['cnt']+0;
		}
		return $cnt;
	}

	function get_invitations_resource($userid,  $page_no = 1, $page_size = 20, $show_type = 'all', $search_query = '')
	{
		$offset = ($page_size * ($page_no - 1));
		
		if(!in_array($show_type, $this->show_types))
		{
			$show_type = 'all';
		}
		if($show_type == 'invited') {
			$show_type = 'invitation_sent';
		}
		$result = adi_build_query_read('get_invs', array(
			'userid' => $userid,
			'invitation_status' => $show_type,
			'query' => $search_query,
			'offset' => $offset,
			'size'   => $page_size,
			'adi_query_conditions' => array(
				'status_check' => (!empty($show_type) && $show_type != 'all'),
				'query_check'  => (!empty($search_query)),
			),
		));

		
		return $result;
	}

	function get_invite_history_recods($userid, $page_no = 1, $page_size = 20, $show_type = 'all', $search_query = '')
	{
		$ih_records = array();
		$sr_no = ($page_size * ($page_no - 1)) + 1;

		$adi_services = adi_allocate_pack('Adi_Services');
		$adiinviter_services = $adi_services->get_service_details('all', 'info');

		$invitation_status_phrases = array(
			'accepted' => $this->adi->phrases['adi_invitation_status_accepted'],
			'blocked'  => $this->adi->phrases['adi_invitation_status_blocked'],
			'waiting'  => $this->adi->phrases['adiinviter_invitation_waiting'],
			'invitation_sent' => $this->adi->phrases['adi_invitation_status_invited'],
		);
		$odd = 1;
		if($resource = $this->get_invitations_resource($userid, $page_no, $page_size, $show_type, $search_query))
		{
			while($row = adi_fetch_array($resource))
			{
				$cur_row = $row;
				$cur_row['srno'] = $sr_no;

				$odd = ($odd == 1) ? 0 : 1;
				$cur_row['row_odd'] = $odd;

				$cur_row['service_email'] = 0;
				if($adiinviter_services[$cur_row['service_used']]['info']['email'] == 1)
				{
					$cur_row['service_email'] = 1;
				}

				if($cur_row['invitation_status'] == 'accepted')
				{
					$receiver_userid = $cur_row['receiver_userid'];
					if($cur_row['receiver_name'] == 'Unknown Name') 
					{
						$cur_row['receiver_name'] = $cur_row['receiver_username'];
					}
					if(!empty($this->adi->settings['adiinviter_profile_page_url']))
					{
						$user = $this->adi->getUserInfo($receiver_userid, false, false);
						if($user != false)
						{
							$opts = array(
								'userid'   => $user->userid,
								'username' => $user->username,
								'email'    => $user->email,
							);
							$profile_page_url = $this->adi->getProfilePageURL($opts);
							$cur_row['profile_page_url'] = $profile_page_url;
							$cur_row['userfullname'] = $user->userfullname;
						}
					}
				}

				if($this->ih_column_service_info)
				{
					$service_name = $adiinviter_services[$cur_row['service_used']]['info']['service'];
					if(!empty($cur_row['domain']))
					{
						$service_name = $cur_row['domain'];
					}
					$cur_row['service_name'] = $service_name;
				}
				$cur_row['status_text'] = $invitation_status_phrases[$row['invitation_status']];

				$cur_row['issued_date'] = $this->adi->adi_format_timeAgo($cur_row['issued_date']);

				$ih_records[] = $cur_row;
				$sr_no++;
			}
		}
		return $ih_records;
	}

	function delete_invites($invitation_ids, $owner_check = true)
	{
		if(!is_array($invitation_ids))
		{
			$invitation_ids = array($invitation_ids);
		}

		if($owner_check == true && $this->adi->userid != 0)
		{
			foreach($invitation_ids as $invite_id)
			{
				if(!empty($invite_id))
				{
					adi_build_query_write('delete_own_invites', array(
						'inviter_id'    => $this->adi->userid,
						'invitation_id' => $invite_id,
					));
				}
			}
		}
		else
		{
			foreach($invitation_ids as $invite_id)
			{
				if(!empty($invite_id))
				{
					adi_build_query_write('delete_invites', array(
						'invitation_id' => $invite_id,
					));
				}
			}
		}
		return true;
	}
	
	function get_email_invitations_count($userid = null)
	{
		$userid = (is_numeric($userid) && !is_null($userid)) ? $userid : $this->adi->userid;
		$result = adi_build_query_read('get_email_invites_count', array(
			'userid' => $userid
		));
		if($row = adi_fetch_assoc($result))
		{ 
			return $row['cnt'];
		}
		return 0;
	}

	function get_headers_for_csv()
	{
		$headers = "First Name,Middle Name,Last Name,Title,Suffix,Initials,Web Page,Gender,Birthday,Anniversary,Location,Language,Internet Free Busy,Notes,E-mail Address,E-mail 2 Address,E-mail 3 Address,Primary Phone,Home Phone,Home Phone 2,Mobile Phone,Pager,Home Fax,Home Address,Home Street,Home Street 2,Home Street 3,Home Address PO Box,Home City,Home State,Home Postal Code,Home Country,Spouse,Children,Manager's Name,Assistant's Name,Referred By,Company Main Phone,Business Phone,Business Phone 2,Business Fax,Assistant's Phone,Company,Job Title,Department,Office Location,Organizational ID Number,Profession,Account,Business Address,Business Street,Business Street 2,Business Street 3,Business Address PO Box,Business City,Business State,Business Postal Code,Business Country,Other Phone,Other Fax,Other Address,Other Street,Other Street 2,Other Street 3,Other Address PO Box,Other City,Other State,Other Postal Code,Other Country,Callback,Car Phone,ISDN,Radio Phone,TTY/TDD Phone,Telex,User 1,User 2,User 3,User 4,Keywords,Mileage,Hobby,Billing Information,Directory Server,Sensitivity,Priority,Private,Categories";
		return $headers;
	}

	function get_contacts_for_csv()
	{
		$userid = $this->adi->userid;
		if($userid != 0)
		{
			$csv_contents = '';
			$result = adi_build_query_read('get_csv_contacts', array(
				'userid' => $userid
			));
			while($row = adi_fetch_assoc($result))
			{
				$csv_contents .= $row['receiver_name'].",,,,,,,,,,,,,,".$row['receiver_email'].",,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,Normal,,,\r\n";
			}
			return $csv_contents;
		}
	}
}




class Adi_Services_Base
{
	public $adi;
	public $services;
	public $captcha_services;

	public $all_details_loaded  = false;
	public $all_services_loaded = false;

	public $social_importer_service_count = NULL;

	function get_service_details($service_id = 'all', $details = 'all')
	{
		$service_id = empty($service_id) ? 'all' : $service_id;
		$details = empty($details) ? 'all' : $details;
		if($this->adi->adiinviter_installed == true)
		{
			return $this->get_service_details_from_db($service_id, $details);
		}
	}

	function get_service_details_from_db($service_id, $details)
	{
		$this->adi->requireSettingsList('oauth');

		include(ADI_LIB_PATH.'services.php');
		if(isset($adiinviter_services))
		{
			$this->services =& $adiinviter_services;
			$this->captcha_services =& $adi_captcha_services;

			if(is_null($this->social_importer_service_count))
			{
				$this->social_importer_service_count = 0;
				foreach($this->services as $serv_id => $details)
				{
					if($details['info']['invitation'] == 'social')
					{
						$this->social_importer_service_count++; 
					}
				}
			}

			$this->services['aol']['params'][2] = ($this->adi->use_aol_oauth) ? 1 : 0;
		}
		$ret_arr = array();

		if(!empty($service_id) && isset($this->services[$service_id])) {
			return array($service_id => $this->services[$service_id]);
		}
		else {
			return $this->services;
		}
	}
}


class HtmlWordManipulator
{
	var $stack = array();
	function truncate($text, $num=50)
	{
		if(!function_exists('_truncateProtect'))
		{
			function _truncateProtect($match) {
			  return preg_replace('/\s/', "\x01", $match[0]);
			}
		}
	  if (preg_match_all('/\s+/', $text, $junk) <= $num) return $text;
	  $text = preg_replace_callback('/(<\/?[^>]+\s+[^>]*>)/', '_truncateProtect', $text);
	  $words = 0;
	  $out = array();
	  $text = str_replace('<',' <',str_replace('>','> ',$text));
	  $toks = preg_split('/\s+/', $text);
	  foreach ($toks as $tok) {
		if (preg_match_all('/<(\/?[^\x01>]+)([^>]*)>/',$tok,$matches,PREG_SET_ORDER))
		  foreach ($matches as $tag) $this->_recordTag($tag[1], $tag[2]);$out[] = trim($tok);
		if (! preg_match('/^(<[^>]+>)+$/', $tok))
		{
		  if (!strpos($tok,'=') && !strpos($tok,'<') && strlen(trim(strip_tags($tok))) > 0)
		  {
			++$words;
		  }
		  else {}
		}
		if ($words > $num) break;
	  }
	  $truncate = $this->_truncateRestore(implode(' ', $out));
	  return $truncate;
	}
	function restoreTags($text) {
		$text .= "...";
	  foreach ($this->stack as $tag) $text .= "</$tag>";
	  return $text;
	}
	private function _truncateProtect($match) {
	  return preg_replace('/\s/', "\x01", $match[0]);
	}
	private function _truncateRestore($strings) {
	  return preg_replace('/\x01/', ' ', $strings);
	}
	private function _recordTag($tag, $args) {
	  // XHTML
	  if (strlen($args) and $args[strlen($args) - 1] == '/') return;
	  else if ($tag[0] == '/') {
		$tag = substr($tag, 1);
		for ($i=count($this->stack) -1; $i >= 0; $i--) {
		 if ($this->stack[$i] == $tag) {
		   array_splice($this->stack, $i, 1);
		   return;
		 }
		}
		return;
	  }
	  else if (in_array($tag, array('p', 'li', 'ul', 'ol', 'div', 'span', 'a')))
		$this->stack[] = $tag;
	  else return;
	}
}


class Adi_Themes_Base
{
	public $adi;
	function load_parsed_css($theme_id, $orientation = 'ltr')
	{
		$css_code = '';
		$theme_file_name = 'theme.css';
		$theme_path = ADI_LIB_PATH.'themes'.ADI_DS.$theme_id;
		if(file_exists($theme_path.ADI_DS.$theme_file_name))
		{
			$code = file_get_contents($theme_path.ADI_DS.$theme_file_name);
			if($code != '')
			{
				$code = preg_replace('/\/\*.*\*\//i','',$code);
				$code = preg_replace('/[\s\t\n\r]+/i', ' ', $code);
				$code = preg_replace('/;\s+\}/i',';}',$code);
				$css_code .= "/* AdiInviter Theme CSS : ".$theme_id." */\n".$code;
			}
		}
		// Contains CSS for services list
		$adi_services = adi_allocate_pack('Adi_Services');
		$adiinviter_services = $adi_services->get_service_details('all', 'logos');
		$css_code .= "\n\n/* AdiInviter Service providers CSS */\n";
		foreach($adiinviter_services as $service_key => $logos)
		{
			$css_code .= '.'.$service_key.'_si {background-image:url(adi_services/'.$service_key.'.png);} ';
		}

		if($orientation == 'rtl')
		{
			$adivars = array(
				'left' => 'right',
				'right' => 'left',
				'orientation' => 'rtl',
			);
		}
		else
		{
			$adivars = array(
				'left' => 'left',
				'right' => 'right',
				'orientation' => 'ltr',
			);
		}

		$adivars['theme_url'] = $this->adi->theme_url;

		$regex = '/\{\s*adi_var\s*:\s*([^\s]*)\s*\}/isU';
		preg_match_all($regex, $css_code, $matches);
		foreach($matches[1] as $ind => $var_path)
		{
			$parts = explode('.', $var_path);
			$changed = false;
			$val = $adivars;
			while($k = array_shift($parts))
			{
				if(isset($val[$k]))
				{
					$val = $val[$k];
				}
				else
				{
					$val = ''; // To remove not found adivars.
					break;
				}
			}
			$css_code = str_replace($matches[0][$ind], $val, $css_code);
		}
		// echo $css_code;

		$css_code = $this->parse_css($css_code);

		return $css_code;
	}
	function parse_css($css_code = '')
	{
		return $css_code;
	}

	function install_theme($theme_id)
	{
		if(!$this->adi->isLoaded('global')) {
			$this->adi->requireSettingsList('global');
		}
		$this->adi->loadPhrases();

		$installed_themes_list = $this->adi->settings['adiinviter_themes_list'];
		$all_themes_list = $this->adi->get_themes_list();
		$theme_path = ADI_LIB_PATH . "themes" . ADI_DS . $theme_id . ADI_DS;
		if(!empty($theme_id) && isset($all_themes_list[$theme_id]) && 
			!isset($installed_themes_list[$theme_id]) && is_dir($theme_path))
		{
			$phrases = array(); $theme_config = array();

			// Add to installed themes list
			$installed_themes_list[$theme_id] = $all_themes_list[$theme_id];
			adi_saveSetting('global', 'adiinviter_themes_list', $installed_themes_list);
			$this->adi->settings['adiinviter_themes_list'] = $installed_themes_list;

			// Check for custom phrases in theme folder.
			$phrase_vars = array();
			$phrases_file = $theme_path . 'phrases.php';
			if(file_exists($phrases_file)) {
				include($phrases_file);
			}
			if(count($phrases) > 0)
			{
				// Get custom phrase_vars from all languages
				$lids = $this->adi->get_installed_lang_ids();
				foreach($phrases as $lang_id => $lng_phrases)
				{
					if(isset($lids[$lang_id]))
					{
						$phrase_vars = array_merge($phrase_vars, array_keys($lng_phrases));
					}
					else
					{
						unset($phrases[$lang_id]);
					}
				}
			}
			$phrase_vars = array_unique($phrase_vars);
			
			// Remove Similar Phrases
			$similar_vars = array_intersect($phrase_vars, array_keys($this->adi->phrases));
			if(count($similar_vars) > 0)
			{
				foreach($similar_vars as $varname)
				{
					$ind = array_search($varname, $phrase_vars);
					if(isset($phrase_vars[$ind]))
					{
						unset($phrase_vars[$ind]);
					}
				}
			}
			// exit;
			$new_global_phrases = array();

			// Add Phrases
			if(count($phrase_vars) > 0)
			{
				foreach($phrases as $lang_id => $lng_phrases)
				{
					foreach($phrase_vars as $varname)
					{
						$phrase_val = (string)(isset($lng_phrases[$varname]) ? $lng_phrases[$varname] : '');
						adi_build_query_write('insert_phrase', array(
							'lang_id'     => $lang_id,
							'theme_id'    => $theme_id,
							'var_name'    => $varname,
							'phrase_text' => adi_escape_string($phrase_val),
						));
						if($lang_id == 'en')
						{
							$new_global_phrases[$varname] = $phrase_val;
						}
						else if(!isset($new_global_phrases[$varname]))
						{
							$new_global_phrases[$varname] = '';
						}
					}
				}
				if(count($new_global_phrases) > 0)
				{
					$this->adi->saveGlobalPhrases($new_global_phrases);
				}
			}

			// Call Theme Installer
			$install_path = $theme_path.'install.php';
			if(file_exists($install_path))
			{
				include_once($install_path);
			}
			return true;
		}
		return false;
	}

	function uninstall_theme($theme_id)
	{
		if(!$this->adi->isLoaded('global')) {
			$this->adi->requireSettingsList('global');
		}
		$this->adi->loadPhrases();

		$installed_themes_list = $this->adi->settings['adiinviter_themes_list'];
		$all_themes_list = $this->adi->get_themes_list();
		$theme_path = ADI_LIB_PATH . "themes" . ADI_DS . $theme_id . ADI_DS;
		if(!empty($theme_id) && isset($all_themes_list[$theme_id]) && 
			isset($installed_themes_list[$theme_id]) && is_dir($theme_path))
		{
			// Add to installed themes list
			unset($installed_themes_list[$theme_id]);
			adi_saveSetting('global', 'adiinviter_themes_list', $installed_themes_list);

			$theme_phrasevars = array();
			if($result = adi_build_query_read('get_theme_phrases', array(
				'theme_id' => $theme_id,
			)))
			{
				while($row = adi_fetch_array($result))
				{
					$theme_phrasevars[] = $row['name'];
				}
			}
			$theme_phrasevars = array_unique($theme_phrasevars);
			if(count($theme_phrasevars) > 0)
			{
				foreach($theme_phrasevars as $phrase_varname)
				{
					$this->adi->removePhrase($phrase_varname);
				}
			}

			if($this->adi->settings['adiinviter_theme'] == $theme_id)
			{
				adi_saveSetting('global', 'adiinviter_theme', $this->adi->default_themeid);
			}

			// Call Theme Uninstaller
			$uninstall_path = $theme_path.'uninstall.php';
			if(file_exists($uninstall_path))
			{
				include_once($uninstall_path);
			}
			return true;
		}
		return false;
	}
}


/*
* For AdiInviter Pro Installer
*/
class Adi_Installer_Base
{
	public $adi;
	public $default_settings    = array();
	public $campaigns_list = array();
	public $adiinviter_root     = '';
	public $website_root        = '';

	function set_install_url($url)
	{
		$this->install_url = $url;
		$adiinviter_root = str_replace('/'.$this->adi->adi_admincp_folder.'/adi_install.php', '', $url);
		if($url != $adiinviter_root)
		{
			$this->adiinviter_root = $adiinviter_root.'/find_friends';
		}
		$website_root = str_replace('/find_friends', '', $adiinviter_root);
		if($adiinviter_root != $website_root)
		{
			$this->website_root = $website_root;
		}
	}

	function get_default_settings()
	{
		if(isset($_SERVER['HTTP_REFERER']))
		{
			$val = $_SERVER['HTTP_REFERER'];
		}
		else {
			$val = $_SERVER['HTTP_HOST'].'/'.$_SERVER['SCRIPT_NAME'];
		}
		$t = explode('/'.$this->adi->adi_admincp_folder.'/adi_install.php', $val);
		$root_url = $t[0];

		$sender_email = str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$sender_email = 'invitation@' . preg_replace('/https?:\/\//i', '', $sender_email);

		$this->default_settings = array(
			'global' => array(
				'adiinviter_theme'                => 'desktop',
				'text_orientation'                => 'ltr',
				'adiinviter_root_url'             => $root_url.'/find_friends',
				'adiinviter_website_root_url'     => $root_url,
				'adiinviter_website_register_url' => $root_url,
				'adiinviter_website_login_url'    => $root_url,
				'adiinviter_sender_name'          => 'Default Sender Name',
				'adiinviter_email_address'        => $sender_email,
				'adiinviter_website_logo'         => '',
			),
		);
		return $this->default_settings;
	}
	function get_campaigns_list()
	{
		return $this->campaigns_list;
	}
	function get_website_name()
	{
		return '';
	}
	function get_adi_root_url()
	{
		if(empty($this->adiinviter_root))
		{
			$val = $this->get_website_root_url().'/find_friends';
			return $val;
		}
		else {
			return $this->adiinviter_root;
		}
	}
	function get_website_root_url()
	{
		if(empty($this->website_root))
		{
			if(isset($_SERVER['HTTP_REFERER']))
			{
				$val = $_SERVER['HTTP_REFERER'];
			}
			else {
				$val = 'http://'.$_SERVER['HTTP_HOST'].'/'.trim($_SERVER['SCRIPT_NAME'],' /');
			}
			$val = explode('/'.$this->adi->adi_admincp_folder.'/adi_install.php', $val);
			return $val[0];
		}
		else {
			return $this->website_root;
		}
	}
	function get_register_page_url() {
		return $this->get_website_root_url();
	}
	function get_login_page_url() {
		return $this->get_website_root_url();
	}
	function get_invite_history_url()
	{
		$val = $this->get_website_root_url().'/invite_history.php';
		return $val;
	}
	function get_sender_name()
	{
		$val = 'Default Sender Name';
		return $val;
	}
	function get_sender_email()
	{
		$val = str_replace('www.', '', $_SERVER['SERVER_NAME']);
		$val = 'invitation@' . preg_replace('/https?:\/\//i', '', $val);
		return $val;
	}
	function get_website_logo() {
		return '';
	}
	function get_db_details()
	{
		return array(
			'db_hostname'  => '',
			'db_username'  => '',
			'db_password'  => '',
			'db_name'      => '',
			'table_prefix' => '',
		);
	}

	function installCampaign($campaigns_list = array())
	{
		if(!is_array($campaigns_list)) {
			return false;
		}
		else if(count($campaigns_list) < 1) {
			return false;
		}
		
		$this->adi->loadCache('language');
		$val = adi_getSetting('campaigns','campaigns_list');
		foreach($campaigns_list as $campaign_id => $details)
		{
			if(!isset($val[$campaign_id]))
			{
				$val[$campaign_id] = $details['title'];
				adi_saveSetting('campaigns', 'campaigns_list', $val);
				include(ADI_ADMIN_PATH.'adi_campaign_default.php');
				$default_settings = $campain_default_settings;

				$sett_names = array('invitation_subject', 'campaign_email_body', 'campaign_social_body');
				$modified_settings = array_intersect($sett_names, array_keys($details));
				foreach($modified_settings as $name)
				{
					if(isset($details[$name]) && is_array($details[$name]) && count($details[$name]) > 0)
					{
						foreach($details[$name] as $lang_id => $lang_translation)
						{
							if(isset($this->adi->cache['language'][$lang_id]))
							{
								$details[$name.'_'.$lang_id] = $lang_translation;
							}
						}
					}
				}

				foreach($default_settings as $name => $vals)
				{
					$insert_val = $vals;
					if(isset($details[$name]))
					{
						if($name == 'content_table')
						{
							$insert_val = adi_null_text(json_encode($details[$name]));
						}
						else {
							$insert_val = $details[$name];
						}
					}
					adi_addSetting('campaign_'.$campaign_id, $name, $insert_val);
				}
			}
		}
		return true;
	}

	function before_installation()
	{
		return true;
	}

	function finish_installation()
	{
		return true;
	}

	function update_admin_settings($settings)
	{
		if(is_array($settings) && count($settings) > 0)
		{
			$config_file = ADI_ADMIN_PATH.DIRECTORY_SEPARATOR.'adi_admin_config.php';
			include($config_file);

			$update_config_file = false;
			foreach($settings as $name => $val)
			{
				if(isset($adiinviter_settings[$name]))
				{
					$adiinviter_settings[$name] = $val;
					$update_config_file = true;
				}
			}
			if($update_config_file === true)
			{
				file_put_contents($config_file, '<?php
$adiinviter_settings = '.var_export($adiinviter_settings, true).';
?>');
			}
		}
	}

	function check_to_install($lang_tag)
	{
		if(!empty($lang_tag))
		{
			$this->adi->loadCache('language');
			if(count($this->adi->global_phrases) == 0)
			{
				$this->adi->loadGlobalPhrases();
			}
			if(isset($this->adi->cache['language'][$lang_tag])) 
			{
				return true;
			}
		}
		return false;
	}

	function import_language($xml_contents, $allow_overwrite = false)
	{
		$xml = new XMLReader(); 
		$xml->xml($xml_contents); 
		$xml_assoc = adi_parse_xml($xml, "root");
		if(count($xml_assoc) > 0 && isset($xml_assoc[0]['tag']))
		{
			$languages = (isset($xml_assoc[0]['tag']) && $xml_assoc[0]['tag'] == 'languages') ? $xml_assoc[0]['childs'] : array();
		}
		
		$lang_phrases = array();
		$lang_id = '';
		if(count($languages) > 0)
		{
			foreach($languages as $lang)
			{
				$lang_id = isset($lang['attr']['id']) ? $lang['attr']['id'] : '';
				if(!empty($lang_id))
				{
					if($this->check_to_install($lang_id))
					{
						$phrases = $lang['childs'];
						if(count($phrases) > 0)
						{
							foreach($phrases as $phrase)
							{
								$varname = isset($phrase['attr']['name']) ? $phrase['attr']['name'] : '';
								$val = isset($phrase['childs'][0]['text']) ? $phrase['childs'][0]['text'] : '';
								if(!empty($varname))
								{
									$lang_phrases[$varname] = $val;
								}
							}
						}
					}
					else
					{
						$lang_id = '';
					}
				}
			}
		}
		$xml->close();
		if(!empty($lang_id) && count($lang_phrases) > 0)
		{
			return $this->add_new_language($lang_id, $lang_phrases, $allow_overwrite);
		}
		return false;
	}

	function export_language($lang_id)
	{
		$lang_ids = $this->adi->get_installed_lang_ids();
		if(empty($lang_id) || !isset($lang_ids[$lang_id])) 
		{
			return false;
		}

		$phrases = $this->adi->loadPhrases(array(), $lang_id);
		$xml_code = '<?xml version="1.0" encoding="UTF-8"?>
<languages>
	<language id="'.$lang_id.'">'; 
		if(count($phrases) > 0)
		{
			foreach($phrases as $varname => $val)
			{
				$xml_code .= '<phrase name="'.$varname.'">'.$val.'</phrase>'."\r\n\t\t";
			}
		}
		$xml_code .= '</language>
</languages>';
		return $xml_code;
	}

	function install_language($lang_tag)
	{
		if($this->check_to_install($lang_tag))
		{
			return $this->add_new_language($lang_tag);
		}
		return 'Unknown error occurred.';
	}

	function add_new_language($lang_tag, $phrases = array(), $allow_overwrite = false)
	{
		if(empty($lang_tag))
		{
			return false;
		}

		// Get default phrases.
		$global_phrases = $this->adi->global_phrases;
		if(is_array($phrases) && count($phrases) > 0)
		{
			$phrases = array_merge($global_phrases, $phrases);
		}
		else
		{
			$phrases = $global_phrases;
		}

		if(!$allow_overwrite)
		{
			$existing_lang_ids = $this->adi->get_installed_lang_ids();
			if(isset($existing_lang_ids[$lang_tag]))
			{
				$existing_phrases = $this->adi->loadPhrases(array(), $lang_tag);
				foreach($phrases as $varname => $value)
				{
					if(isset($existing_phrases[$varname]))
					{
						unset($phrases[$varname]);
					}
				}
			}
		}

		$theme_phrases = array();
		if($result = adi_build_query_read('get_all_phrases', array(
			'lang_id' => 'en'
		)))
		{
			while($row = adi_fetch_array($result))
			{
				$theme_phrases[$row['name']] = $row['theme_id'];
			}
		}

		if(count($phrases) > 0)
		{
			// Insert phrases
			$phrases_chunks = array_chunk($phrases, 50, true);
			foreach($phrases_chunks as $sphrases)
			{
				$repeat_for = array();
				foreach($sphrases as $name => $value)
				{
					$theme_id = isset($theme_phrases[$name]) ? $theme_phrases[$name] : $this->adi->default_themeid;
					$repeat_for[] = array(
						'lang_id'     => $lang_tag,
						'theme_id'    => $theme_id,
						'var_name'    => $name,
						'phrase_text' => adi_escape_string($value),
					);
					unset($phrases[$name]);
				}
				if(count($repeat_for) > 0)
				{
					adi_build_query_write('insert_phrases', array(
						'repeat_for' => $repeat_for,
					));
				}
			}
		}
		return true;
	}
	function uninstall_language($lang_id)
	{
		if(empty($lang_id))
		{
			return 'Language tag is empty';
		}
		$existing_lang_ids = $this->adi->get_installed_lang_ids();
		if(!isset($existing_lang_ids[$lang_id])) 
		{
			return '"'.$lang_id.'" language not found.';
		}

		if($this->adi->adiinviter_installed)
		{
			adi_build_query_write('remove_language', array(
				'lang_id' => $lang_id,
			));
		}
		
		// remove invitation body for this language
		adi_deleteSettings('invitation', 'invitation_body_'.$lang_id);
		adi_deleteSettings('invitation', 'invitation_social_body'.$lang_id);

		// remove Campaign body for this language
		$campaign_ids = adi_getSetting('campaigns', 'campaigns_list');
		if(count($campaign_ids) > 0)
		{
			foreach($campaign_ids as $content_id)
			{
				adi_deleteSettings($content_id, 'campaign_body_'.$lang_id);
				adi_deleteSettings($content_id, 'social_body_'.$lang_id);
			}
		}

		// remove email template settings for this language from installed plugins
		$all_plugins_list = $this->adi->get_all_plugins_list();
		if(count($all_plugins_list) > 0)
		{
			$result = adi_build_query_read('search_for_message_templates', array(
				'plugins_ids_list' => $all_plugins_list
			));
			while($row = adi_fetch_array($result))
			{
				$group_name   = $row['group_name'];
				$name         = $row['name'];
				$default_name = preg_replace('/_en$/i', '', $name);
				if($name != $default_name)
				{
					adi_deleteSettings($group_name, $default_name.'_'.$lang_id);
				}
			}
		}
		return true;
	}
}


/**
* Prototype of the classes which defines Campaign details fetching methods.
* For e.g. Adi_Campaign_topic_share : where "topic_share" is a campaign_id
*/
class Adi_Campaigns_Prototype
{
	public $adi;
	public $title='';
	public $body='';
	public $category_id='';
	public $url='';

	public $campaign_id = '';
	public $campaign_name = '';
	public $content_settings = array();

	function get_content_title($content_id = 0)
	{
		return $this->title;
	}
	function get_content_body($content_id = 0)
	{
		return $this->body;
	}
	function get_category_id($content_id = 0)
	{
		return $this->category_id;
	}
	function get_content_url($content_id = 0)
	{
		return $this->url;
	}

	final function install($default_settings)
	{
		global $adiinviter;
		$installed_campaign = adi_getSetting('campaigns','campaigns_list');
		if(!empty($this->campaign_name) && !empty($this->campaign_id) && !isset($installed_campaign[$this->campaign_id]))
		{
			if(count($this->content_settings) > 0)
			{
				$settings = array_merge($default_settings, $this->content_settings);
			}
			$settings['title'] = $this->campaign_name;
			$campaign_id  = $this->campaign_id;

			foreach($settings as $name => $val)
			{
				if($name == 'content_table')
				{
					$val = adi_null_text(json_encode($val));
				}
				adi_addSetting('campaign_'.$campaign_id, $name, $val);
			}

		}
	}
}




class Adi_Events_Base
{
	public $history = array();
	public $adi = null;

	public function call_event($id, $data = null)
	{
		if(is_string($id) && !empty($id))
		{
			$func_name = 'event_'.$id;
			$data = is_null($data) ? array() : $data;
			if(method_exists($this, $func_name))
			{
				$this->history[] = $id;
				$this->adi->trace('Event Listener function called for event_id : "'.$id.'"');
				return $this->$func_name($data);
			}
			else
			{
				$this->adi->trace('Event Listener not found for event_id : "'.$id.'"');
			}
		}
		else
		{
			$this->adi->trace('Event Invalid event_id : "'.$id.'"');
		}
		return true;
	}
}


if(!class_exists('AdiInviter_Pro_Core'))
{
	$path = dirname(__FILE__);
	include_once($path.DIRECTORY_SEPARATOR.'adiinviter_core.php');
}

class Adi_Updates extends AdiInviter_Pro_Core
{
	public $adi = null;
	function check_for_updates()
	{
		$this->adi->requireSettingsList('updates');
		$this->init();

		$last_build_id = $this->adi->settings['adi_package_build_id'];
		if(!is_array($this->adi->settings['adi_updates_list']))
		{
			$this->adi->settings['adi_updates_list'] = array();
		}
		else
		{
			if(count($this->adi->settings['adi_updates_list']) > 0)
			{
				$build_ids = array_keys($this->adi->settings['adi_updates_list']);
				$max_build_id = max($build_ids);
				if($last_build_id < $max_build_id)
				{
					$last_build_id = $max_build_id;
				}
			}
		}

		adi_saveSetting('global', 'check_for_updates_last_time', $this->adi->adi_get_utc_timestamp());
		$url = $this->adi->settings['check_for_updates_link'];
		$result = $this->post($url, array('bid'=>$last_build_id, 'from'=>$this->adi->settings['adiinviter_website_root_url']));
		$json = @json_decode($result, true);
		if(is_array($json) && count($json) > 0)
		{
			$json_keys = array_keys($json);
			sort($json_keys, SORT_NUMERIC);

			$list = $this->adi->settings['adi_updates_list'];
			foreach($json_keys as $build_id)
			{
				if(isset($json[$build_id]) && $build_id > $last_build_id)
				{
					$list[$build_id] = $json[$build_id];
				}
			}
			$this->adi->settings['adi_updates_list'] = $list;

			adi_saveSetting('updates', 'adi_updates_list', $this->adi->settings['adi_updates_list']);
			
			$updates_count = count($this->adi->settings['adi_updates_list']);
			$this->adi->settings['adiinviter_email_notification'] = adi_parse_email($this->adi->settings['adiinviter_email_notification']);
			if(!empty($this->adi->settings['adiinviter_email_notification']) && $updates_count > 0)
			{
				$this->send_email_notification($updates_count);
			}
		}
	}

	function send_email_notification($updates_count = 1)
	{
		$email = adi_parse_email($this->adi->settings['adiinviter_email_notification']);

		include_once(ADI_LIB_PATH.'invitation_handler.php');
		$mailer = adi_allocate_pack('Adi_Send_Mail');
		$mailer->init();

		// Sender Information
		$sender_name = $this->adi->settings['adiinviter_sender_name'];
		$sender_email = $this->adi->settings['adiinviter_email_address'];
		$mailer->set_sender($sender_name, $sender_email);

		// Send email notification
		$subject = $this->adi->settings['adi_email_notification_subject'];
		$body = $this->adi->settings['adi_email_notification_body'];

		$replace_vars = array(
			'updates_count' => $updates_count,
		);

		$subject = adi_replace_vars($subject, $replace_vars);
		$body = adi_replace_vars($body, $replace_vars);

		if(!empty($email))
		{
			$mailer->send($email, $subject, $body);
		}
	}

	function set_current_build_id($build_id)
	{
		if(!is_numeric($build_id))
		{
			return false;
		}
		$this->adi->requireSettingsList('updates');
		if($this->adi->settings['adi_package_build_id'] < $build_id)
		{
			adi_saveSetting('updates', 'adi_package_build_id', $build_id);

			if(count($this->adi->settings['adi_updates_list']) > 0)
			{
				$bids = array_keys($this->adi->settings['adi_updates_list']);
				$cnt = 0;
				foreach($bids as $bid)
				{
					if($bid <= $build_id)
					{
						unset($this->adi->settings['adi_updates_list'][$bid]);
						$cnt++;
					}
				}
				if($cnt > 0)
				{
					adi_saveSetting('updates', 'adi_updates_list', $this->adi->settings['adi_updates_list']);
				}
			}
		}
	}
}



// Cache Contacts List
function adimt_cache_contacts($cache_data, $list_id = '')
{
	global $adiinviter;
	if(!is_array($cache_data) || count($cache_data) == 0)
	{
		return false;
	}

	// Clear any list created before 2 hours from now.
	$min_timestamp = $adiinviter->adi_get_utc_timestamp();
	adi_build_query_write('auto_clear_listcache', array(
		'create_date' => ($min_timestamp - $adiinviter->contacts_cache_timeout),
	));

	$list_id = '';
	do {
		$if_exists = false;
		$list_id = $adiinviter->get_unique_id(6);
		$list_id = md5(microtime().':'.$list_id.rand(100,99999));
		if($adiinviter->db_allowed == true)
		{
			$result = adi_build_query_read('get_list_cache', array(
				'list_id' => $list_id,
			));
			if($row = adi_fetch_array($result))
			{
				if($list_id == $row['list_id'])
				{
					$if_exists = true;
				}
			}
		}
	}while($if_exists);

	if(!empty($list_id))
	{
		$userid = $adiinviter->userid;
		$timestamp = $adiinviter->adi_get_utc_timestamp();
		$cache_data_str = adi_json_encode($cache_data);

		// Remove any previously created list cache.
		if($adiinviter->userid != 0)
		{
			adi_build_query_write('remove_previous_listache', array(
				'userid' => $adiinviter->userid,
			));
		}

		adi_build_query_write('insert_list_cache', array(
			'list_id' => $list_id,
			'importer_userid' => $userid,
			'cache_data' => adi_escape_string($cache_data_str),
			'create_date' => $timestamp,
		));
		return $list_id;
	}
	return false;
}

function adimt_update_list_cache($list_id, $cache_data)
{
	if(!is_array($cache_data) || count($cache_data) == 0) {
		return false;
	}
	$cache_data_str = adi_json_encode($cache_data);
	adi_build_query_write('update_list_cache', array(
		'list_id'    => $list_id,
		'cache_data' => adi_escape_string($cache_data_str),
	));
	return true;
}

function adimt_clear_list_cache($list_id)
{
	if(empty($list_id)) {
		return false;
	}
	adi_build_query_write('clear_list_cache', array(
		'list_id' => $list_id,
	));
	return true;
}

function adimt_get_cache_data($adi_listid = '')
{
	if(!empty($adi_listid))
	{
		$result = adi_build_query_read('get_list_cache', array(
			'list_id' => $adi_listid,
		));
		if($row = adi_fetch_array($result))
		{
			$cache_data = adi_json_decode($row['data']);
			return array(
				'list_id' => $row['list_id'],
				'userid' => $row['userid'],
				'data' => $cache_data,
				'create_date' => $row['create_date'],
			);
		}
	}
	return array();
}



?>