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


if(!headers_sent())
{
	header("charset: UTF-8");
	header("Cache-Control: must-revalidate");
}

$admin_path = dirname(__FILE__);
include_once($admin_path.DIRECTORY_SEPARATOR.'adi_init.php');

$response = array();
$do = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);
$adminconfig = $_POST['subsettings']['adminconfig'];
$db_class_name = '';
$db = null;

if(isset($adminconfig['adiinviter_db_type']))
{
	$db_type = $adminconfig['adiinviter_db_type'];
	$db_prefix = $adminconfig['adiinviter_table_prefix'];
	if(isset($adiinviter->admin_settings['adiinviter_available_db_types'][$db_type]))
	{
		$db_class_name = 'Adi_'.$db_type.'_Database';
		if(class_exists($db_class_name))
		{
			if(! defined('ADI_TABLE_PREFIX')) {
				define('ADI_TABLE_PREFIX', $db_prefix);
			}
			$db = new $db_class_name();
		}
		else
		{
			$response[] = array(
				'error_code' => 1,
				'error_msg' => "<font color='red'><i>Database connection type!!</i></font>",
			);
		}
	}
}

if($do != 'checkConnDetails')
{
	$db =& $adiinviter->db;
}


if($do != '' && !is_null($db))
{
	$db = new $db_class_name();
	$db->init_queries();
	if(!isset($hostname))
		$hostname = $adminconfig['adiinviter_hostname'];
	if(!isset($username))
		$username = $adminconfig['adiinviter_username'];
	if(!isset($password))
		$password = $adminconfig['adiinviter_password'];
	if(!isset($dbname))
		$dbname   = $adminconfig['adiinviter_dbname'];

	if($do == 'checkConnDetails')
	{
		$current_response = array();
		if(!empty($dbname) && ($result = $db->adi_connect_to_db($hostname, $username, $password, $dbname)))
		{
			$current_response['error_code'] = 0;
			$current_response['error_msg'] = "<font color='green'><i>Database connection details are correct!!</i></font>";
		}
		else
		{
			$current_response['error_code'] = 1;
			$current_response['error_msg'] = "<font color='red'><i>Incorrect database connection details!!</i></font>";
		}
		$current_response['resp_label'] = 'checkConn_resp';

		$response[] = $current_response;
	}
	else if($do == 'checkUserDetails')
	{
		/**
		 * Check User Details
		 */

		$error_msg = '';
		if($db->adi_connect_to_db($hostname, $username, $password, $dbname) && isset($_POST['subsettings']['db_info']['user_table']))
		{
			$user_table = $_POST['subsettings']['db_info']['user_table'];
			if(!isset($user_table_name))
				$user_table_name = $user_table['table_name'];
			if(!isset($user_userid))
				$user_userid = $user_table['userid'];
			if(!isset($user_username))
				$user_username = $user_table['username'];
			$user_email   = $user_table['email'];
			/*if(!isset($user_usergroupid))
				$user_usergroupid   = $user_table['usergroupid'];*/
			if(!isset($user_avatar))
				$user_avatar   = $user_table['avatar'];

			if(!empty($user_table_name))
			{
				$user_fields = array();
				if($result = $db->buildAndRead('check_table_structure', array(
					'table_name' =>$user_table_name
				)))
				{
					while($row = $db->adi_fetch_assoc($result))
					{
						$user_fields[] = $row['Field'];
					}
				}

				if(!in_array($user_userid, $user_fields)) {
					$error_msg = '"<b>'.$user_userid.'</b>" column for userid does not exist in the table : '.$user_table_name;
				}
				else if(!in_array($user_username, $user_fields)) {
					$error_msg = '"<b>'.$user_username.'</b>" column for username does not exist in the table : '.$user_table_name;
				}
				else if(!in_array($user_email, $user_fields)) {
					$error_msg = '"<b>'.$user_email.'</b>" column for email does not exist in the table : '.$user_table_name;
				}
				else if(!in_array($user_avatar, $user_fields) && $user_avatar != '') {
					$error_msg = '"<b>'.$user_avatar.'</b>" column for avatar does not exist in the table : '.$user_table_name;
				}

				if($error_msg == '' && $user_avatar == '')
				{
					// checking for Avatar details
					$avatar_table = $_POST['subsettings']['db_info']['avatar_table'];
					if(!isset($avatar_table_table_name))
						$avatar_table_table_name = $avatar_table['table_name'];
					if(!isset($avatar_table_userid))
						$avatar_table_userid = $avatar_table['userid'];
					if(!isset($avatar_table_avatar))
						$avatar_table_avatar = $avatar_table['avatar'];

					$adiinviter_avatar_url = $_POST['subsettings']['db_info']['adiinviter_avatar_url'];

					if($avatar_table_table_name != '' && $avatar_table_userid != '' && $avatar_table_avatar != '')
					{
						// user-usergroupid mapping is used.
						$avatar_fields = array();
						if($result = $db->buildAndRead('check_table_structure', array(
							'table_name' => $avatar_table_table_name,
						)))
						{
							while($row = $db->adi_fetch_assoc($result))
							{
								$avatar_fields[] = $row['Field'];
							}
						}
						if(!in_array($avatar_table_userid, $avatar_fields)) {
							$error_msg = '"<b>'.$avatar_table_userid.'</b>" column for userid does not exist in the table : '.$avatar_table_table_name;
						}
						else if(!in_array($avatar_table_avatar, $avatar_fields)) {
							$error_msg = '"<b>'.$avatar_table_avatar.'</b>" column for avatar does not exist in the table : '.$avatar_table_table_name;
						}
					}
					else
					{
						 if(strpos(strtolower($adiinviter_avatar_url), '[avatar_value]') !== false)
						{
							$error_msg = 'You have to specify user to avatar mapping if you want to use [avatar_value] in Avatar URL.';
						}
					}
				}
			}
		}
		else
		{
			$response[] = array(
				'error_code' => 1,
				'error_msg'  => "<font color='red'><i>Database connection details are incorrect!!</i></font>",
				'resp_label' => 'checkConn_resp',
			);
			$error_msg = 'Database connection details are incorrect!!';
		}

		if($error_msg != '')
		{
			$current_response['error_code'] = 1;
			$current_response['error_msg']  = "<font color='red'><i>" . $error_msg . "</i></font>";
		}
		else
		{
			if(empty($user_table_name))
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg']  = "<font color='green'><i>User integration has been disabled.</i></font>";
			}
			else
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg']  = "<font color='green'><i>User related details are correct!!</i></font>";
			}
		}
		$current_response['resp_label'] = 'checkUser_resp';
		$response[] = $current_response;
	}
	else if($do == 'checkUsergroupDetails')
	{
		/**
		 * Check Usergroup Details
		 */
		$error_msg = '';
		if($db->adi_connect_to_db($hostname, $username, $password, $dbname))
		{
			$usergroup_table = $_POST['subsettings']['db_info']['usergroup_table'];
			if(!isset($usergroup_table_name))
				$usergroup_table_name = $usergroup_table['table_name'];
			if(!isset($usergroup_usergroupid))
				$usergroup_usergroupid = $usergroup_table['usergroupid'];
			if(!isset($usergroup_name))
				$usergroup_name = $usergroup_table['name'];

			$user_table = $_POST['subsettings']['db_info']['user_table'];
			if(!isset($user_table_name))
				$user_table_name = $user_table['table_name'];
			if(!isset($user_usergroupid))
				$user_usergroupid   = $user_table['usergroupid'];

			if(!empty($usergroup_table_name))
			{
				$usergroup_fields = array();
				if($result = $db->buildAndRead('check_table_structure', array(
					'table_name' => $usergroup_table_name,
				)))
				{
					while($row = $db->adi_fetch_assoc($result))
					{
						$usergroup_fields[] = $row['Field'];
					}
				}
				if(!in_array($usergroup_usergroupid, $usergroup_fields)) {
					$error_msg = '"<b>'.$usergroup_usergroupid.'</b>" column for usergroupid does not exist in the table : '.$usergroup_table_name;
				}
				else if(!in_array($usergroup_name, $usergroup_fields)) {
					$error_msg = '"<b>'.$usergroup_name.'</b>" column for usergroup name does not exist in the table : '.$usergroup_table_name;
				}
			}

			if(!empty($user_table_name))
			{
				$user_fields = array();
				if($result = $db->buildAndRead('check_table_structure', array(
					'table_name' => $user_table_name,
				)))
				{
					while($row = $db->adi_fetch_assoc($result))
					{
						$user_fields[] = $row['Field'];
					}
				}
				if(!in_array($user_usergroupid, $user_fields) && $user_usergroupid != '') {
					$error_msg = '"<b>'.$user_usergroupid.'</b>" column for usergroupid does not exist in the table : '.$user_table_name;
				}

				if($error_msg == '' && $user_usergroupid == '')
				{
					// Checking for Usergroup mapping details
					$usergroup_mapping = $_POST['subsettings']['db_info']['usergroup_mapping'];
					if(!isset($usergroup_mapping_table_name))
						$usergroup_mapping_table_name = $usergroup_mapping['table_name'];
					if(!isset($usergroup_mapping_userid))
						$usergroup_mapping_userid = $usergroup_mapping['userid'];
					if(!isset($usergroup_mapping_usergroupid))
						$usergroup_mapping_usergroupid = $usergroup_mapping['usergroupid'];
					if($usergroup_mapping_table_name != '' && $usergroup_mapping_userid != '' && $usergroup_mapping_usergroupid != '')
					{
						// user-usergroupid mapping is used.
						$usergroup_mapping_fields = array();
						if($result = $db->buildAndRead('check_table_structure', array(
							'table_name' => $usergroup_mapping_table_name,
						)))
						{
							while($row = $db->adi_fetch_assoc($result))
							{
								$usergroup_mapping_fields[] = $row['Field'];
							}
						}

						if(!in_array($usergroup_mapping_userid, $usergroup_mapping_fields)) {
							$error_msg = '"<b>'.$usergroup_mapping_userid.'</b>" column for usergroupid does not exist in the table : '.$usergroup_mapping_table_name;
						}
						else if(!in_array($usergroup_mapping_userid, $usergroup_mapping_fields)) {
							$error_msg = '"<b>'.$usergroup_mapping_usergroupid.'</b>" column for usergroupid does not exist in the table : '.$usergroup_mapping_table_name;
						}
					}
				}
			}
		}
		else
		{
			$response[] = array(
				'error_code' => 1,
				'error_msg'  => "<font color='red'><i>Database connection details are incorrect!!</i></font>",
				'resp_label' => 'checkConn_resp',
			);
			$error_msg = 'Database connection details are incorrect!!';
		}

		if($error_msg != '')
		{
			$current_response['error_code'] = 1;
			$current_response['error_msg']  = "<font color='red'><i>" . $error_msg . "</i></font>";
		}
		else
		{
			if(empty($usergroup_table_name))
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg'] = "<font color='green'><i>Usergroups integration has been disabled.</i></font>";
			}
			else
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg'] = "<font color='green'><i>Usergroup related details are correct!!</i></font>";
			}
		}
		$current_response['resp_label'] = 'checkUsergroup_resp';
		$response[] = $current_response;
	}
	else if($do == 'checkFriendsDetails')
	{
		$error_msg = '';
		if($db->adi_connect_to_db($hostname, $username, $password, $dbname))
		{
			$friends_table = $_POST['subsettings']['db_info']['friends_table'];
			if(!isset($friends_table_name))
				$friends_table_name = $friends_table['table_name'];
			if(!isset($friends_userid))
				$friends_userid = $friends_table['userid'];
			if(!isset($friends_friend_id))
				$friends_friend_id = $friends_table['friend_id'];
			if(!isset($friends_status))
				$friends_status = $friends_table['status'];
			if(!isset($friends_yes_value))
				$friends_yes_value = $friends_table['yes_value'];
			if(!isset($friends_pending_value))
				$friends_pending_value = $friends_table['pending_value'];

			if(!empty($friends_table_name))
			{
				$friend_fields = array();
				if($result = $db->buildAndRead('check_table_structure', array(
					'table_name' => $friends_table_name,
				)))
				{
					while($row = $db->adi_fetch_assoc($result))
					{
						$friend_fields[] = $row['Field'];
					}
				}
				if(!in_array($friends_userid, $friend_fields)) {
					$error_msg = '"<b>'.$friends_userid.'</b>" column for userid does not exist in the table : '.$friends_table_name;
				}
				else if(!in_array($friends_friend_id, $friend_fields)) {
					$error_msg = '"<b>'.$friends_friend_id.'</b>" column for friend id does not exist in the table : '.$friends_table_name;
				}
				else if(!in_array($friends_status, $friend_fields) && $friends_status != '') {
					$error_msg = '"<b>'.$friends_status.'</b>" column for friends relationship status does not exist in the table : '.$friends_table_name;
				}
				if($friends_status != '')
				{
					$query = "SELECT * FROM ".$friends_table_name." WHERE ".$friends_status." = '".$friends_yes_value."' OR ".$friends_status." = '".$friends_pending_value."' LIMIT 0,1";
					if($result = $db->adi_query_read($query, false)) { }
					else
					{
						$error_msg = 'Test Query : <b>'.$query."</b><br>".mysql_error();
					}
				}
			}
		}
		else
		{
			$response[] = array(
				'error_code' => 1,
				'error_msg'  => "<font color='red'><i>Database connection details are incorrect!!</i></font>",
				'resp_label' => 'checkConn_resp',
			);
			$error_msg = 'Database connection details are incorrect!!';
		}

		if($error_msg != '')
		{
			$current_response['error_code'] = 1;
			$current_response['error_msg']  = "<font color='red'><i>" . $error_msg . "</i></font>";
		}
		else
		{
			if(empty($friends_table_name))
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg']  = "<font color='green'><i>Friends relationship integration has been disabled.</i></font>";
			}
			else
			{
				$current_response['error_code'] = 0;
				$current_response['error_msg']  = "<font color='green'><i>Friends relationship related details are correct!!</i></font>";
			}
		}
		$current_response['resp_label'] = 'checkFriends_resp';
		$response[] = $current_response;
	}
}

if(!isset($do_not_echo))
{
	echo json_encode($response);
}

?>