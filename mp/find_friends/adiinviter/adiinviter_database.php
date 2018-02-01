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
 * For using mysql connection
 */
class Adi_mysql_Database extends Adi_Database_Queries
{
	public $db_allowed = false;

	public $db_hostname = '';
	public $db_username = '';
	public $db_password = '';
	public $db_name     = '';

	function reconnect()
	{
		return $this->adi_connect_to_db($this->db_hostname, $this->db_username, $this->db_password, $this->db_name);
	}
	function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
	{
		$dbname = null;
		if(is_bool($dbname_or_error_report)) {
			$error_report = $dbname_or_error_report;
		}
		else if(is_string($dbname_or_error_report)) {
			$dbname = $dbname_or_error_report;
		}

		$this->db_hostname = $hostname;
		$this->db_username = $username;
		$this->db_password = $password;
		$this->db_name     = $dbname;
		try
		{
			$dbhandle = @mysql_pconnect($hostname, $username, $password);
			if($dbhandle !== false)
			{
				if(is_null($dbname)) {
					$this->report_trace('Database : Database name is empty');
				}
				else {
					$selected = @mysql_select_db($dbname, $dbhandle);
					if($selected !== false)
					{
						mysql_set_charset('utf8', $dbhandle);
						$this->db_allowed = true;
						return true;
					}
					else {
						if($error_report) {
							$error_no  = mysql_errno($dbhandle);
							$error_txt = '';
							if($error_no !== 0) {
								$error_txt = 'DB ERROR '.$error_no.' : '.mysql_error($dbhandle);
							}
							$this->report_trace('Database : Failed to connect to database. '."\n".$error_txt);
						}
					}
				}
			}
			else {
				if($error_report)
				{
					$this->report_trace('Could not connect to database.');
				}
			}
		}
		catch(Exception $e) {}
		return false;
	}
	function adi_escape_string($value)
	{
		if($this->db_allowed === true && function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($value);
		}
		return $value;
	}
	function adi_ping_db()
	{
		return mysql_ping();
	}
	function adi_get_error()
	{
		return mysql_error();
	}
	function adi_query_read($query = '', $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_query_read : MySql connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		$result = mysql_query($query);
		return $result;
	}
	function adi_fetch_array($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_array : MySql connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return mysql_fetch_array($pointer);
	}
	function adi_fetch_assoc($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_assoc : MySql connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return mysql_fetch_assoc($pointer);
	}
	function adi_query_write($query = '', $error_report = true)
	{
		if(! $this->adi_ping_db())
		{
			$this->report_trace('fn.adi_query_write : MySql connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return mysql_query($query);
	}
	function adi_free_result($pointer = '', $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_free_result : MySql connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return mysql_free_result($pointer);
	}
}






/*
* For using MySQLi connections
*/
class Adi_mysqli_Database extends Adi_Database_Queries
{
	public $conn =  null;

	public $db_allowed = false;

	public $db_hostname = '';
	public $db_username = '';
	public $db_password = '';
	public $db_name     = '';

	function reconnect()
	{
		return $this->adi_connect_to_db($this->db_hostname, $this->db_username, $this->db_password, $this->db_name);
	}

	function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
	{
		$dbname = null;
		if(is_bool($dbname_or_error_report)) {
			$error_report = $dbname_or_error_report;
		}
		else if(is_string($dbname_or_error_report)) {
			$dbname = $dbname_or_error_report;
		}

		$this->db_hostname = $hostname;
		$this->db_username = $username;
		$this->db_password = $password;
		$this->db_name     = $dbname;
		try
		{
			$this->conn = @new mysqli($hostname, $username, $password, $dbname);
			if(@$this->conn->connect_errno !== 0)
			{
				if($error_report)
				{
					global $adiinviter;
					$error_txt = 'DB ERROR '.$this->conn->connect_errno.' : '.$this->conn->connect_error;
					$adiinviter->trace('Database : Failed to connect to database. '."\n".$error_txt);
				}
			}
			else
			{
				$this->conn->set_charset("utf8");
				$this->db_allowed = true;
				return true;
			}
		}
		catch(Exception $e) {}
		return false;
	}

	function adi_escape_string($value)
	{
		if($this->db_allowed === true)
		{
			return mysqli_real_escape_string($this->conn, $value);
		}
		return $value;
	}

	function adi_ping_db()
	{
		return ($this->conn && $this->db_allowed) ? mysqli_ping($this->conn) : false;
	}
	function adi_get_error()
	{
		if(is_null($this->conn))
		{
			return ' Error : MySqli object is NULL.';
		}
		else {
			return ($this->db_allowed) ? mysqli_error($this->conn) : 'Connection Not established';
		}
	}

	function adi_query_read($query = '', $error_report = true)
	{
		if(! $this->adi_ping_db())
		{
			$this->report_trace('fn.adi_query_read : MySqli connection went down.. Reconnecting now..', true);
			$this->reconnect();
		}
		return ($this->db_allowed) ? mysqli_query($this->conn, $query) : false;
	}

	function adi_fetch_array($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_array : MySqli connection went down.. Reconnecting now..', true);
			$this->reconnect();
			return false;
		}
		return ($this->db_allowed) ? mysqli_fetch_array($pointer) : false;
	}

	function adi_fetch_assoc($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_assoc : MySqli connection went down.. Reconnecting now..', true);
			$this->reconnect();
		}
		return ($this->db_allowed) ? mysqli_fetch_assoc($pointer) : false;
	}

	function adi_query_write($query = '', $error_report = true)
	{
		if(! $this->adi_ping_db())
		{
			$this->report_trace('fn.adi_query_write : MySqli connection went down.. Reconnecting now..', true);
			$this->reconnect();
		}
		return ($this->db_allowed) ? mysqli_query($this->conn, $query) : false;
	}

	function adi_free_result($pointer = '', $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_free_result : MySqli connection went down.. Reconnecting now..', true);
			$this->reconnect();
		}
		return ($this->db_allowed) ? mysqli_free_result($pointer) : false;
	}
}





/**
 * For using SQLSRV connection to SQL Server
 */
class Adi_sqlsrv_Database extends Adi_Database_Queries
{
	public $conn =  null;
	public $db_allowed = false;

	public $db_hostname = '';
	public $db_username = '';
	public $db_password = '';
	public $db_name     = '';

	public $repeat_statement_separator = '';

	function init_queries()
	{
		$tb_adiinviter             = ADI_TABLE_PREFIX . "adiinviter";
		$tb_adiinviter_guest       = ADI_TABLE_PREFIX . "adiinviter_guest";
		$tb_adiinviter_lang        = ADI_TABLE_PREFIX . "adiinviter_lang";
		$tb_adiinviter_conts       = ADI_TABLE_PREFIX . "adiinviter_conts";
		$tb_adiinviter_queue       = ADI_TABLE_PREFIX . "adiinviter_queue";
		$tb_adiinviter_services    = ADI_TABLE_PREFIX . "adiinviter_services";
		$tb_adiinviter_settings    = ADI_TABLE_PREFIX . "adiinviter_settings";

		$this->queries = array(
// General
'check_for_table' => "SELECT name FROM sys.tables WHERE name like '%[query_text]%'",
'get_all_tables'  => "SELECT name FROM sys.tables",
'check_table_structure' => "SELECT column_name as Field FROM INFORMATION_SCHEMA.Columns WHERE table_name = '[table_name]'",
'rename_table' => "EXEC sp_rename [current_name], [new_name]",

'create_settings_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_settings')
BEGIN
	CREATE TABLE $tb_adiinviter_settings (
		group_name varchar(100) NOT NULL,
		name varchar(50) NOT NULL,
		value text NOT NULL,
		PRIMARY KEY (group_name, name)
	)
END",
'create_services_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_services')
BEGIN
	CREATE TABLE $tb_adiinviter_services (
		id varchar(30) NOT NULL,
		info text NOT NULL,
		params text NOT NULL,
		domains text NOT NULL,
		logos text NOT NULL,
		PRIMARY KEY (id)
	)
END",
'create_language_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_lang')
BEGIN
	CREATE TABLE $tb_adiinviter_lang (
		lang_id varchar(50) NOT NULL,
		theme_id varchar(80) NOT NULL,
		name varchar(60) NOT NULL,
		value text NOT NULL,
		UNIQUE (lang_id, theme_id, name)
	)
END",
'create_inviations_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter')
BEGIN
	CREATE TABLE $tb_adiinviter (
		invitation_id varchar(50) NOT NULL,
		inviter_id int NOT NULL,
		guest_id int NOT NULL DEFAULT '0',
		receiver_userid int NOT NULL DEFAULT '0',
		receiver_username varchar(100) NOT NULL,
		receiver_name nvarchar(300) NOT NULL,
		receiver_social_id varchar(50) NOT NULL,
		receiver_email nvarchar(100) NOT NULL,
		invitation_status varchar(50) NOT NULL,
		service_used varchar(50) NOT NULL,
		issued_date bigint NOT NULL,
		type varchar(50) NOT NULL,
		campaign_id varchar(60) NOT NULL DEFAULT '',
		content_id varchar(100) NOT NULL,
		topic_redirect tinyint NOT NULL DEFAULT '0',
		visited tinyint NOT NULL DEFAULT '0'
	)
END",
'create_guest_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_guest')
BEGIN
	CREATE TABLE $tb_adiinviter_guest (
		guest_id int NOT NULL DEFAULT '0',
		email nvarchar(100) NOT NULL,
		name nvarchar(300) NOT NULL
	)
END",
'create_queue_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_queue')
BEGIN
	CREATE TABLE $tb_adiinviter_queue (
		mqueueid int NOT NULL IDENTITY,
		invitation_id varchar(50) NOT NULL,
		toemail varchar(80) NOT NULL,
		subject varchar(80) NOT NULL,
		message text NOT NULL,
		sender_info varchar(400) NOT NULL,
		PRIMARY KEY (mqueueid)
	)
END",
'create_conts_table' => "IF NOT EXISTS (SELECT name FROM sys.tables WHERE name = '$tb_adiinviter_conts')
BEGIN
	CREATE TABLE $tb_adiinviter_conts (
		list_id varchar(32) NOT NULL,
		userid int NOT NULL,
		data text NOT NULL,
		create_date int NOT NULL,
		PRIMARY KEY (list_id)
	)
END",


// Settings
'fetch_setting' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([setting_group_ids]) AND name = '[setting_name]'",
'fetch_setting_groups' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([setting_group_ids])",
'fetch_setting_groups_like' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name LIKE 'Adi_Plugin_%'",
'add_setting' => "IF NOT EXISTS
	(SELECT name FROM $tb_adiinviter_settings
	WHERE group_name = '[setting_group_name]' AND name = '[setting_name]')
	BEGIN
		INSERT INTO $tb_adiinviter_settings VALUES('[setting_group_name]', '[setting_name]', '[setting_value]')
	END
ELSE
	BEGIN
		UPDATE $tb_adiinviter_settings
		SET value = '[setting_value]'
		WHERE group_name = '[setting_group_name]' AND name = '[setting_name]'
	END",
'update_setting' => "UPDATE $tb_adiinviter_settings SET value = '[setting_value]' WHERE name='[setting_name]' AND group_name = '[setting_group_name]'",
'remove_setting_group' => "DELETE FROM $tb_adiinviter_settings WHERE group_name = '[setting_group_name]'",
'remove_setting' => "DELETE FROM $tb_adiinviter_settings WHERE group_name = '[setting_group_name]' AND name = '[setting_name]'",
'search_for_message_templates' => "SELECT * FROM $tb_adiinviter_settings WHERE group_name IN ([plugins_ids_list]) AND name LIKE '%_en'",
'get_sorted_plugins_ids' => "SELECT group_name FROM $tb_adiinviter_settings WHERE name = 'plugin_next_time' ORDER BY CAST(value AS VARCHAR(5000))",
'check_plugins_list' => "SELECT * FROM $tb_adiinviter_settings WHERE name = 'plugin_on_off'",

// Language
'get_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE name IN ([phrase_varnames]) AND lang_id = '[lang_id]'",
'get_all_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE lang_id = '[lang_id]'",
'get_theme_phrases' => "SELECT * FROM $tb_adiinviter_lang WHERE theme_id = '[theme_id]'",
'get_all_lang_ids' => "SELECT DISTINCT lang_id FROM $tb_adiinviter_lang",
'insert_phrase' => "INSERT INTO $tb_adiinviter_lang VALUES ('[lang_id]', '[theme_id]', '[var_name]', '[phrase_text]')",
'insert_phrases' => "[REPEAT_STATEMENT]
IF NOT EXISTS
	(SELECT name FROM $tb_adiinviter_lang
	WHERE lang_id = '[lang_id]' AND theme_id = '[theme_id]' AND name = '[var_name]')
	BEGIN
		INSERT INTO $tb_adiinviter_lang VALUES('[lang_id]', '[theme_id]', '[var_name]', '[phrase_text]')
	END
ELSE
	BEGIN
		UPDATE $tb_adiinviter_lang
		SET value = '[phrase_text]'
		WHERE lang_id = '[lang_id]' AND theme_id = '[theme_id]' AND name = '[var_name]'
	END
[/REPEAT_STATEMENT]",
'update_phrase' => "UPDATE $tb_adiinviter_lang SET value='[phrase_text]' WHERE name='[var_name]' AND lang_id = '[lang_id]'",
'remove_phrase_from_all' => "DELETE FROM $tb_adiinviter_lang WHERE name = '[var_name]'",
'remove_language' => "DELETE FROM $tb_adiinviter_lang WHERE lang_id = '[lang_id]'",
'search_in_phrases' => "SELECT * FROM $tb_adiinviter_lang
WHERE
 [in_language] lang_id = '[lang_id]' AND [/in_language]
([search_in_vars]value LIKE '%[serach_query]%'[/search_in_vars]
 [search_in_both] OR [/search_in_both]
 [search_in_text]name LIKE '%[serach_query]%'[/search_in_text])
ORDER BY CAST(name AS VARCHAR(5000))",

// Scheduled Plugins
'get_executing_plugins' => "SELECT * FROM $tb_adiinviter_settings WHERE name = 'plugin_next_time' AND value < [current_time]",


// Invitations
'get_invs_count' => "SELECT COUNT(1) AS cnt FROM $tb_adiinviter WHERE inviter_id = [userid]",
'get_invs_count_for_status' => "SELECT COUNT(1) AS cnt FROM $tb_adiinviter WHERE inviter_id = [userid] AND invitation_status = '[invitation_status]'",
'count_inviters_to_emails' => "SELECT count(1) as cnt from $tb_adiinviter WHERE campaign_id = '' AND receiver_email = '[receiver_email]'",
'count_inviters_to_socialids' => "SELECT count(1) as cnt from $tb_adiinviter WHERE campaign_id = '' AND receiver_social_id = '[social_id]' AND service_used = '[service_id]'",
'get_invs' => "SELECT * FROM nw_adiinviter
WHERE inviter_id = [userid]
ORDER BY issued_date DESC
OFFSET [offset] ROWS
FETCH NEXT [size] ROWS ONLY",

'get_invs_for_status' => "SELECT * FROM nw_adiinviter
WHERE inviter_id = [userid] AND invitation_status = '[invitation_status]'
ORDER BY issued_date DESC
OFFSET [offset] ROWS
FETCH NEXT [size] ROWS ONLY",
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
[social_invitation] AND service_used = '[service_id]' AND receiver_social_id = '[social_id]'[/social_invitation]
[email_invitation] AND receiver_email = ('[receiver_email]')[/email_invitation]",
'add_invitation' => "INSERT INTO $tb_adiinviter VALUES [REPEAT_STATEMENT]('[invitation_id]', [userid], [guest_id], 0, '', '[receiver_name]', '[social_id]', '[receiver_email]', '[status]', '[service_id]', [issued_date], '[service_type]', '[campaign_id]', '[content_id]', [topic_redirect], [visited])[REPEAT_STATEMENT]",
'check_guest_id' => "SELECT * FROM $tb_adiinviter_guest WHERE [field_name] = '[field_value]'",
'get_max_guest_id' => "SELECT MAX(guest_id) as mx FROM $tb_adiinviter_guest",
'add_guest_details' => "INSERT INTO $tb_adiinviter_guest VALUES ([guest_id], '[sender_email]', '[sender_name]')",
'update_guest_details' => "UPDATE $tb_adiinviter_guest SET userid = [userid] WHERE [field_name] = '[field_value]'",
'delete_guest_details' => "DELETE FROM $tb_adiinviter_guest WHERE [field_name] = '[field_value]'",

'update_invitations' => "UPDATE $tb_adiinviter SET [update_field] = [update_value] WHERE [check_field] = '[check_value]'",
'get_invites_statistics' => "SELECT inviter_id, COUNT( 1 ) AS total,
	SUM(CASE WHEN invitation_status = 'invitation_sent'  THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'blocked'  THEN 1 ELSE 0 END ) AS blocked,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN invitation_status = 'waiting'  THEN 1 ELSE 0 END ) AS waiting
	FROM $tb_adiinviter WHERE inviter_id = [userid]
	GROUP BY inviter_id",
'get_invite_dates' => "SELECT MIN(issued_date) as fs, MAX(issued_date) as ls FROM $tb_adiinviter",
'get_invites_for_duration' => "SELECT issued_date / [duration] as issued_date,
	SUM(CASE WHEN invitation_status = 'invitation_sent' THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM nw_adiinviter as adiinviter
	WHERE issued_date > [start_date] AND issued_date <= [last_date]
	GROUP BY issued_date / [duration]",
'get_short_invites_for_duration' => "SELECT count(inviter_id) as cnt,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM $tb_adiinviter as adiinviter
	WHERE issued_date > [start_date] AND issued_date < [last_date]",
'get_invites_summary' => "SELECT inviter_id * 0 as inviter_id,
	SUM(CASE WHEN invitation_status = 'invitation_sent' THEN 1 ELSE 0 END ) AS invitation_sent,
	SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
	SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited,
	SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked
	FROM $tb_adiinviter
	GROUP BY inviter_id * 0",
'get_top_inviters' => "SELECT inviter_id, count(inviter_id) as cnt,
SUM(CASE WHEN invitation_status = 'accepted' THEN 1 ELSE 0 END ) AS accepted,
SUM(CASE WHEN invitation_status = 'blocked' THEN 1 ELSE 0 END ) AS blocked,
SUM(CASE WHEN visited = '1' THEN 1 ELSE 0 END ) AS visited
FROM $tb_adiinviter as adiinviter
WHERE inviter_id != 0
GROUP BY inviter_id
ORDER BY cnt DESC
OFFSET [offset] ROWS
FETCH NEXT [size] ROWS ONLY",


// Users
'get_user_details' => "SELECT * FROM [user_table] WHERE [userid_field] = [userid]",
'get_userids_details' => "SELECT * FROM [user_table] WHERE [userid_field] IN ([userids])",
'get_username_details' => "SELECT * FROM [user_table] WHERE [username_field] = '[username_value]'",
'get_email_details' => "SELECT * FROM [user_table] WHERE [email_field] IN ([emails_list])",
'update_invites_limit_all' => "UPDATE [user_table] SET adi_num_invites = '[num_invites]'",
'update_invites_limit' => "UPDATE [user_table] SET adi_num_invites = '[num_invites]' WHERE [field_name] = [field_value]",
'update_invites_limit_mapping' => "UPDATE [user_table] AS u INNER JOIN [usergroup_mapping_table] AS umt ON (u.[user_userid_field] = umt.[mapping_userid_field]) SET u.adi_num_invites = '[num_invites]' WHERE umt.[mapping_usergroupid_field] = '[usergroupid]'",
'reduce_invites_limit' => "UPDATE [user_table] SET adi_num_invites = adi_num_invites - '[num_invites]' WHERE [field_name] = [field_value]",
'add_invite_limit_column' => "ALTER TABLE [table_name] ADD adi_num_invites VARCHAR(40) NOT NULL
	CONSTRAINT adi_num_invites_default_value DEFAULT 'Unlimited'",
'remove_invite_limit_column' => "ALTER TABLE [table_name] DROP CONSTRAINT adi_num_invites_default_value;
	ALTER TABLE [table_name] DROP COLUMN adi_num_invites",


// Usergroups
'get_usergroups_details' => "SELECT * FROM [usergroup_table]",
'get_usergroup_mapping' => "SELECT * FROM [usergroup_mapping_table] WHERE [userid_field] = [userid]",

// Avatar
'get_avatar_details' => "SELECT * FROM [avatar_table] WHERE [userid_field] = [userid]",

// Friends
'check_friend_request' => "SELECT * FROM [friend_table] WHERE ([userid_field] = [userid] AND [friendid_field] = [friendid])",
'friend_request_with_status' => "INSERT INTO [friend_table] ([userid_field],[friendid_field],[status_field]) VALUES([userid], [friendid], '[status_value]')",
'friend_request' => "INSERT INTO [friend_table] ([userid_field],[friendid_field]) VALUES([userid], [friendid])",
'get_user_friends' => "SELECT * FROM [friends_table] WHERE ([userid_field] = [userid]) OR ([friendid_field] = [userid])",
'get_mutual_friends' => "SELECT * FROM [friends_table] WHERE [userid_field] IN ([userids]) AND [friendid_field] IN ([friendids])",
'get_mutual_friends_status' => "SELECT * FROM [friends_table] WHERE [userid_field] IN ([userids]) AND [friendid_field] IN ([friendids]) AND [status_field] = '[status_value]'",

// Mail Queue
'mail_queue_count' => "SELECT count(mqueueid) as cnt FROM $tb_adiinviter_queue",
'get_mails_from_queue' => "SELECT * FROM $tb_adiinviter_queue ORDER BY mqueueid ASC LIMIT 0, [mails_count]",
'add_to_mail_queue' => "INSERT INTO $tb_adiinviter_queue VALUES(0,'[invitation_id]', '[receiver_email]', '[mail_subject]', '[mail_body]', '[sender_info]')",
'remove_from_mail_queue' => "DELETE FROM $tb_adiinviter_queue WHERE mqueueid = [mqueueid]",

// Campaign
'get_content_details' => "SELECT * FROM [content_table] WHERE [contentid_field] = [content_id]",


// Contacts Cache
'insert_list_cache' => "INSERT INTO $tb_adiinviter_conts VALUES ('[list_id]', '[importer_userid]', '[cache_data]', '[create_date]')",
'get_list_cache' => "SELECT * FROM $tb_adiinviter_conts WHERE list_id = '[list_id]'",
'update_list_cache' => "UPDATE $tb_adiinviter_conts SET data = '[cache_data]' WHERE list_id = '[list_id]'",
'clear_list_cache' => "DELETE FROM $tb_adiinviter_conts WHERE list_id = '[list_id]'",
'auto_clear_listcache' => "DELETE FROM $tb_adiinviter_conts WHERE create_date < [create_date]",
'remove_previous_listache' => "DELETE FROM $tb_adiinviter_conts WHERE userid = [userid]",

		);
	}

	function reconnect()
	{
		return $this->adi_connect_to_db($this->db_hostname, $this->db_username, $this->db_password, $this->db_name);
	}
	function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
	{
		$dbname = null;
		if(is_bool($dbname_or_error_report)) {
			$error_report = $dbname_or_error_report;
		}
		else if(is_string($dbname_or_error_report)) {
			$dbname = $dbname_or_error_report;
		}

		$this->db_hostname = $hostname;
		$this->db_username = $username;
		$this->db_password = $password;
		$this->db_name     = $dbname;

		$connectionInfo = array(
			'Database' => $this->db_name,
			'UID' => $this->db_username,
			'PWD' => $this->db_password,
		);

		try
		{
			$conn = @sqlsrv_connect($hostname, $connectionInfo);
			if($conn)
			{
				$this->conn = $conn;
				$this->db_allowed = true;
				return true;
			}
			else
			{
				$this->report_trace('Could not connect to database.');
			}
		}
		catch(Exception $e) {}
		return false;
	}
	function adi_escape_string($value)
	{
		if($this->db_allowed === true)
		{
			if(empty($value)) return $value;
			if(is_numeric($value)) return $value;
			/*$unpacked = unpack('H*hex', $value);
			return '0x' . $unpacked['hex'];*/
			$value = str_replace("'", "''", $value);
			// $value = str_replace('"', '\\"', $value);
			// return mysql_real_escape_string($value);
		}
		return $value;
	}
	function adi_ping_db()
	{
		return true;
	}
	function adi_get_error()
	{
		$errors = sqlsrv_errors();
		return (is_array($errors) && count($errors) > 0) ? var_export($errors, true) : '';
	}
	function adi_query_read($query = '', $error_report = true)
	{
		if(!$this->adi_ping_db())
		{
			$this->report_trace('fn.adi_query_read : SQLSRV connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		$result = sqlsrv_query($this->conn, $query);
		return $result;
	}
	function adi_fetch_array($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_array : SQLSRV connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return sqlsrv_fetch_array($pointer);
	}
	function adi_fetch_assoc($pointer, $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_fetch_assoc : SQLSRV connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return sqlsrv_fetch_array($pointer, SQLSRV_FETCH_ASSOC);
	}
	function adi_query_write($query = '', $error_report = true)
	{
		if(! $this->adi_ping_db())
		{
			$this->report_trace('fn.adi_query_write : SQLSRV connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return sqlsrv_query($this->conn, $query);
	}
	function adi_free_result($pointer = '', $error_report = true)
	{
		if(! $this->adi_ping_db()) {
			$this->report_trace('fn.adi_free_result : SQLSRV connection went down.. Reconnecting now..');
			$this->reconnect();
		}
		return sqlsrv_free_stmt($pointer);
	}
}


// When connectin type is not selected.
class Adi_none_Database extends Adi_Database_Queries
{
	function adi_connect_to_db($hostname, $username, $password, $dbname_or_error_report = null, $error_report = false)
	{
		return false;
	}

	function adi_escape_string($value)
	{
		return $value;
	}

	function adi_query_read($query = '', $error_report = true)
	{
		return false;
	}

	function adi_fetch_array($pointer, $error_report = true)
	{
		return false;
	}

	function adi_query_write($query = '', $error_report = true)
	{
		return false;
	}

	function adi_free_result($pointer = '', $error_report = true)
	{
		return false;
	}
}

?>