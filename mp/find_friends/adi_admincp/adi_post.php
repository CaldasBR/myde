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


$admin_path = dirname(__FILE__);
include_once($admin_path.DIRECTORY_SEPARATOR.'adi_init.php');
try 
{
	$subsettings = AdiInviterPro::POST('subsettings', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('subsettings') && count($subsettings) > 0)
	{
		$arr_keys = array_unique(array_keys($subsettings));
		if(!(count($arr_keys) == 1 && in_array('adminconfig', $arr_keys)))
		{
			$adiinviter->requireSettingsList(array('global','db_info'));
		}
		foreach($subsettings as $settings_name => $sub_subsettings)
		{
			if($settings_name == 'adminconfig')
			{
				if(is_array($sub_subsettings) && count($sub_subsettings) > 0)
				{
					// Save Admin Settings
					$adminconfig_file = ADI_ADMIN_PATH . 'adi_admin_config.php';
					include($adminconfig_file);
					$change_cnt = 0;
					foreach($sub_subsettings as $name => $val)
					{
						if(isset($adiinviter_settings[$name]) && $adiinviter_settings[$name] != $val) 
						{
							$change_cnt++;
							$adiinviter_settings[$name] = $val;
						}
					}
					if($change_cnt > 0)
					{
						if(!$adiinviter->safe_mode) 
						{
							file_put_contents($adminconfig_file, '<?php
$adiinviter_settings = '.var_export($adiinviter_settings, true).';
?>');
						}
					}
				}
			}
			else
			{
				switch($settings_name)
				{
					case 'global': 
						if(isset($subsettings['adiinviter_website_root_url']))
						{
							$subsettings['adiinviter_website_root_url'] = trim($subsettings['adiinviter_website_root_url'], ' /');
						}

						if(isset($subsettings['adiinviter_root_url']))
						{
							$subsettings['adiinviter_root_url'] = trim($subsettings['adiinviter_root_url'], ' /');
						}

						if(isset($sub_subsettings['adiinviter_onoff']))
						{
							if($sub_subsettings['adiinviter_onoff'] != 1)
							{
								$sub_subsettings['adiinviter_onoff'] = 0;
							}
						}
						
						$vars = AdiInviterPro::POST('vars', ADI_ARRAY_VARS);
						if(count($vars) > 0 && isset($sub_subsettings['services_onoff']))
						{
							$sub_subsettings['services_onoff'] = array(
								'on' => explode(',', $vars['on_services_order']),
								'off' => array_filter(explode(',', $vars['off_services_order'])),
							);

							if(count($sub_subsettings['services_onoff']['on']) < 1)
							{
								continue;
							}
						}
					break;

					case 'db_info': 
					break;

					case 'invitation':
						if(isset($sub_subsettings['attach_note_length_limit']))
						{
							$limit = $sub_subsettings['attach_note_length_limit'];
							$sub_subsettings['attach_note_length_limit'] = is_numeric($limit) ? max(10, $limit) : 10;
						}
					break;

					case 'oauth':
						foreach($sub_subsettings as $sett_name => $sett_val)
						{
							if(in_array($sett_val, array(
								'Use adiinviter.com OAuth application Client ID',
								'Use adiinviter.com OAuth application Client Secret',
								'Use adiinviter.com OAuth application API Key',
								'Use adiinviter.com OAuth application Consumer Key',
								'Use adiinviter.com OAuth application Consumer Secret Key',
							)))
							{
								$sub_subsettings[$sett_name] = '';
							}
						}
					break;

					default: break;
				}
				
				// Word limit field for all campaigns
				if(isset($sub_subsettings['word_limit']))
				{
					$limit = (int)$sub_subsettings['word_limit'];
					$sub_subsettings['word_limit'] = is_numeric($limit) ? max(10, $limit) : 10;
				}

				$old_settings = $adiinviter->settings;


				if(is_array($sub_subsettings) && count($sub_subsettings) > 0)
				{
					foreach($sub_subsettings as $name => $val)
					{
						if(is_array($val) && $name != 'services_onoff')
						{
							$new_val = adi_getSetting($settings_name, $name);
							foreach($val as $nme => $nv)
							{
								$new_val[$nme] = $nv;
							}
							$val = $new_val;
						}
						adi_saveSetting($settings_name, $name, $val);
					}
				}

				// Notifiers
				switch($settings_name)
				{
					case 'global': 
						if(isset($sub_subsettings['adiinviter_onoff']) && $sub_subsettings['adiinviter_onoff'] != $old_settings['adiinviter_onoff'])
						{
							if($sub_subsettings['adiinviter_onoff'] == 1) {
								adi_call_event('system_turned_on');
							}
							else 
							{
								adi_call_event('system_turned_off');
								$sub_subsettings['adiinviter_onoff'] = 0;
							}
						}
					break;

					case 'db_info':
						if(isset($sub_subsettings['user_table']['table_name']))
						{
							$old_user_table = trim($old_settings['user_table']['table_name']);
							$new_user_table = trim($sub_subsettings['user_table']['table_name']);
							$userid_field   = trim($sub_subsettings['user_table']['userid']);
							$username_field = trim($sub_subsettings['user_table']['username']);
							$email_field    = trim($sub_subsettings['user_table']['email']);
							if($old_user_table != $new_user_table)
							{
								$reset_ugperms = false;
								if(!empty($old_user_table))
								{
									$user_fields = array();
									if($result = adi_build_query_read('check_table_structure', array(
											'table_name' => $old_user_table,
										)))
									{
										while($row = adi_fetch_assoc($result))
										{
											$user_fields[] = $row['Field'];
										}
									}
									if(in_array('adi_num_invites', $user_fields))
									{
										adi_build_query_write('remove_invite_limit_column', array(
											'table_name' => $old_user_table,
										));
									}
									$reset_ugperms = true;
								}
								if(!empty($new_user_table) && !empty($userid_field) && !empty($username_field) && !empty($email_field) )
								{
									$user_fields = array();
									if($result = adi_build_query_read('check_table_structure', array(
											'table_name' => $new_user_table,
										)))
									{
										while($row = adi_fetch_assoc($result))
										{
											$user_fields[] = $row['Field'];
										}
									}
									if(!in_array('adi_num_invites', $user_fields))
									{
										adi_build_query_write('add_invite_limit_column', array(
											'table_name' => $new_user_table,
										));
									}
									$reset_ugperms = true;
								}
								if($reset_ugperms)
								{
									$adiinviter->settings_group['db_info'] = false;
									$adiinviter->requireSettingsList('db_info');
									$adiinviter->permissions->reset_usergroup_perms();
								}
							}

							if(isset($sub_subsettings['usergroup_table']['table_name']))
							{
								$usergroup_table       = $sub_subsettings['usergroup_table']['table_name'];	
								$usergroup_usergroupid = $sub_subsettings['usergroup_table']['usergroupid'];	
								$usergroup_name        = $sub_subsettings['usergroup_table']['name'];	
								if( ($old_settings['usergroup_table']['table_name'] != $usergroup_table) ||
									($old_settings['usergroup_table']['usergroupid'] != $usergroup_usergroupid) ||
									($old_settings['usergroup_table']['name'] != $usergroup_name) )
								{
									$adiinviter->settings_group['db_info'] = false;
									$adiinviter->requireSettingsList('db_info');
									$adiinviter->permissions->reset_usergroup_perms();
								}
							}
						}

					break;
				}
			}
		}
	}


	$adi_templates = AdiInviterPro::POST('adi_templates', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('adi_templates') && count($adi_templates) > 0)
	{
		foreach($adi_templates as $group_name => $sub_subsettings)
		{
			if(is_array($sub_subsettings) && count($sub_subsettings) > 0)
			{
				foreach($sub_subsettings as $name => $translations) 
				{
					if(count($translations) > 0)
					{
						foreach($translations as $lang_id => $val)
						{
							$tr_name = $name.'_'.$lang_id;
							$result = adi_getSetting($group_name, $name.'_'.$lang_id);
							if($result !== false)
							{
								adi_saveSetting($group_name, $tr_name, $val);
							}
							else
							{
								adi_addSetting($group_name, $tr_name, $val);
							}
						}
					}
				}
			}
		}
	}


	// Add new Language
	$add_new_lang_form = AdiInviterPro::POST('add_new_lang_form', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('add_new_lang_form') && !empty($add_new_lang_form['lang_id']))
	{
		$lang_id = $add_new_lang_form['lang_id'];
		$lang_id = preg_replace('/[^a-z_0-9]/i', '', $lang_id);
		$adiinviter->loadCache('langauge');
		$available_lang_ids = $adiinviter->get_installed_lang_ids();
		if(isset($adiinviter->cache['language'][$lang_id]) && !isset($available_lang_ids[$lang_id]) && $lang_id != '')
		{
			$adiinviter->trace('fl.adi_post : Adding new language with lang_id "'.$lang_id.'"');

			$adi_installer = adi_allocate_pack('Adi_Installer');
			if($adi_installer->install_language($lang_id)) {}
			else
			{
				echo '$(".new_lang_form_response").html("<font color=\'red\'><i>Failed to add new langauge.</i></font>");';
			}
		}
		else
		{
			echo '$(".new_lang_form_response").html("<font color=\'red\'><i>Failed to add new langauge.</i></font>");';
		}
	}

	// Remove Language
	$adi_delete_language = AdiInviterPro::POST('adi_delete_language', ADI_STRING_VARS);
	if(AdiInviterPro::isPOST('adi_delete_language') && $adi_delete_language != '')
	{
		$lang_id = $adi_delete_language;
		$adi_installer = adi_allocate_pack('Adi_Installer');
		$adi_installer->uninstall_language($lang_id);
	}


	// Add new phrase form submit
	$new_phrase_form = AdiInviterPro::POST('new_phrase_form', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('new_phrase_form') && is_array($new_phrase_form))
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$phrase_varname  = $new_phrase_form['phrase_varname'];
		$phrase_text     = $new_phrase_form['phrase_text'];
		$theme_id        = $new_phrase_form['theme_id'];

		$adiinviter->loadPhrases();

		if(!$adiinviter->addPhrase($phrase_varname, $phrase_text, $theme_id))
		{
			echo '$(".new_phrase_form_response").html("<font color=\'red\'><i>Failed to add new Phrase.</i></font>");';
		}
		else
		{
			echo '$(".new_phrase_form_response").html("<font color=\'green\'><i>New phrase added successfully.</i></font>");';
			echo 'adi.hideNewPhraseForm();';
		}
	}

	// Edit phrase form submit
	$edit_phrase_form = AdiInviterPro::POST('edit_phrase_form', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('edit_phrase_form') && count($edit_phrase_form) > 0)
	{
		$adiinviter->requireSettingsList(array('global','db_info'));

		$phrase_varname = $edit_phrase_form['phrase_varname'];
		$all_phrases    = $edit_phrase_form['phrase_text'];

		$adiinviter->loadCache('language');
		$done = false;
		
		$available_lang_ids = $adiinviter->get_installed_lang_ids();

		if( count($all_phrases) > 0)
		{
			foreach($all_phrases as $lang_id => $phrase_text)
			{
				if(isset($adiinviter->cache['language'][$lang_id]))
				{
					$adiinviter->savePhrases(array($phrase_varname => $phrase_text), $lang_id);
					$done = true;
				}
			}
		}

		if($done)
		{
			echo 'adi.hideEditPhraseForm();';
		}
		else 
		{
			echo '$("#ediPhrase_response").html("<font color=\'red\'><i>Something went wrong.</i></font>");';
		}
	}

	$remove_phrase = AdiInviterPro::POST('remove_phrase', ADI_STRING_VARS);
	if(AdiInviterPro::isPOST('remove_phrase') && !empty($remove_phrase))
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$phrase_varname = $remove_phrase;
		$adiinviter->loadPhrases();
		if(!empty($phrase_varname))//&& isset($adiinviter->phrases[$phrase_varname]))
		{
			$adiinviter->removePhrase($phrase_varname);
		}
	}

	// Update Usergroup permissions
	$usergroupPerms = AdiInviterPro::POST('usergroupPerms', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('usergroupPerms') && count($usergroupPerms) > 0)
	{
		if(!headers_sent())
		{
			header('Content-type: text/javascript');
		}
		$adiinviter->requireSettingsList(array('global','db_info'));
		$usergroup_perms = $usergroupPerms;
		$sucess = false;
		if($adiinviter->permissions->storeUsergroupPermissions($usergroup_perms))
		{
			echo 'adi_notif.show_success("Usergroup permissions updated successfully.")';
			$sucess = true;
		}
		if(!$sucess)
		{
			echo 'adi_notif.show_failure("Failed to update usergroup permissions.")';
		}
	}

	// Add Campaign
	$new_campaign_form = AdiInviterPro::POST('new_campaign_form', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('new_campaign_form') && is_array($new_campaign_form))
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$campaign_id = trim($new_campaign_form['id'], " \t\r\n");
		$name = trim($new_campaign_form['name'], " \t\r\n");

		$tcid = preg_replace('/[^0-9a-z_]/i', '', $campaign_id);
		if(empty($name) || empty($tcid))
		{
			echo "$('#adi_new_cs_response').html('<font color=\"red\"><i>Please fill up all the fields.</i></font>');";
		}
		else if($tcid !== $campaign_id)
		{
			echo "$('#adi_new_cs_response').html('<font color=\"red\"><i>Only alphabets, numbers and underscore(_) is allowed in Campaign ID.</i></font>');";
		}
		else
		{
			$val = adi_getSetting('campaigns','campaigns_list');
			$sucess = false;
			$found = false;

			if(isset($val[$campaign_id])) {
				$found = true;
			}
			else
			{
				if(in_array($name, array_values($val)))
				{
					$found = true;
				}
			}
			if($found == false)
			{
				$adi_installer = adi_allocate_pack('Adi_Installer');
				$campaigns_list = array(
					$campaign_id => array(
						'title'        => $name,
					),
				);
				$adi_installer->installCampaign($campaigns_list);
				$sucess = true;
			}
			
			if($sucess) {
				echo "$('#adi_new_cs_response_2').html('<font color=\"green\"><i>Campaign created successfully.</i></font>'); adi_campaign.hideNewContShareForm(); adi_campaign.updateContShareList();";
			}
			else if($found) {
				echo "$('#adi_new_cs_response').html('<font color=\"red\"><i>Campaign with the name \"".$name."\" already exists.</i></font>');";
			}
			else {
				// echo "$('#adi_new_cs_response').html('<font color=\"red\"><i>Something went wrong.</i></font>');";
			}
		}
	}
	
	// Edit campaign name
	$edit_campaign = AdiInviterPro::POST('edit_campaign', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('edit_campaign') && is_array($edit_campaign))
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$campaign_id = (int)$edit_campaign['campaign_id'];
		$campaign_name = $edit_campaign['campaign_name'];

		if(!empty($campaign_name))
		{
			$val = adi_getSetting('campaigns','campaigns_list');
			$sucess = false; $found = false;
			if(isset($val[$campaign_id])) 
			{
				$val[$campaign_id] = $campaign_name;
				$found = true;
			}
			if($found) 
			{
				adi_saveSetting('campaigns', 'campaigns_list', $val);
			}
		}
	}

	// Delete campaign
	$remove_campaign = AdiInviterPro::POST('remove_campaign', ADI_STRING_VARS);
	if(AdiInviterPro::isPOST('remove_campaign') && !empty($remove_campaign))
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		$campaign_id = $remove_campaign;
		if(!empty($campaign_id))
		{
			$val = adi_getSetting('campaigns','campaigns_list');
			$sucess = false; $found = false;
			if(isset($val[$campaign_id])) 
			{
				unset($val[$campaign_id]);
				$found = true;
			}
			if($found) 
			{
				adi_saveSetting('campaigns', 'campaigns_list', $val);
				adi_deleteSettings('campaign_'.$campaign_id);
				echo "$('#adi_new_cs_response_2').html('<font color=\"green\"><i>Campaign removed successfully.</i></font>');";
				echo 'adi_campaign.updateContShareList();';
			}
		}
	}

	$reset_password = AdiInviterPro::POST('reset_password', ADI_ARRAY_VARS);
	if(AdiInviterPro::isPOST('reset_password') && count($reset_password) > 0)
	{
		$adiinviter->requireSettingsList(array('global','db_info'));
		
		$curr_passord = isset($reset_password['current_password']) ? $reset_password['current_password'] : '';
		$new_password = isset($reset_password['new_password']) ? $reset_password['new_password'] : '';
		$confirm_passord = isset($reset_password['confirm_new_password']) ? $reset_password['confirm_new_password'] : '';

		$curr_passord_md = md5($curr_passord);
		$changed = false;

		if(!empty($new_password) && $new_password == $confirm_passord)
		{
			$config_file_path = ADI_ADMIN_PATH . 'adi_admin_config.php';
			include($config_file_path);

			if($adiinviter_settings['controlpanel_password'] == $curr_passord_md)
			{
				$adiinviter_settings['controlpanel_password'] = md5($new_password);
				$code = '<?php
$adiinviter_settings = '.var_export($adiinviter_settings, true).';
?>';
				file_put_contents($config_file_path, $code);
				$changed = true;

				echo "adi_reset.hide(); adi_notif.show_success('Admin panel password has been reset successfully.');";
			}
			else
			{
				echo "$('.reset_password_processing').html('<font color=\"red\">Current password is wrong.</font>');";
			}
		}
	}

}
catch (Exception $e)
{
	echo 'alert("' . $e->getMessage() . '");';
}
?>