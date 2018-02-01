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


header("charset: UTF-8");

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adi_init.php');
$base_path = dirname(__FILE__);

global $adiinviter;
include_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');


$do = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);
$do = empty($do) ? 'login_page' : $do;

$load_default_template = true;

$adi_current_model = 'inpage';
$contents = '';
$adi_global_redirect_url = '';

if($adiinviter->adiinviter_installed !== true)
{
	$contents = '<h3 style="font-family:Verdana,Tahoma;color:#282828;">AdiInviter Pro is not installed yet.</h3>';
}
else
{
	$adiinviter->requireSettingsList(array('global', 'db_info', 'oauth', 'invitation'));
	$adiinviter->init_user();
	$adiinviter->loadPhrases();
	
	// Get Cache ID from $_POST
	$list_cache = array();$adi_conts_model = 1;
	$added_friends_ids   = array();
	$invitation_sent_ids = array();
	$list_userid = 0;
	$adi_conts_list_id = AdiInviterPro::POST('adi_list_id', ADI_STRING_VARS);
	if(!empty($adi_conts_list_id))
	{
		$list_cache = adimt_get_cache_data($adi_conts_list_id);

		$list_userid = isset($list_cache['userid']) ? $list_cache['userid']+0 : 0;

		if(is_array($list_cache) && count($list_cache) > 0 && isset($list_cache['data']) && 
			isset($list_cache['userid']) && $list_cache['userid']+0 === $adiinviter->userid)
		{
			$contacts            = $list_cache['data']['conts'];
			$registered_contacts = $list_cache['data']['reg_conts'];
			$info                = $list_cache['data']['reg_info'];
			$config              = $list_cache['data']['config'];
			$adi_conts_model     = $list_cache['data']['conts_model'];
			$added_friends_ids   = $list_cache['data']['fr_adds'];
			$invitation_sent_ids = $list_cache['data']['sent_ids'];
		}
	}

	// Hook Location : init_complete
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;

	// Hook Location : inpage_start
	($adi_hook_code = adi_exec_hook_location('inpage_start')) ? eval($adi_hook_code) : false;

	switch($do)
	{
		case 'reset_listcache':
			$adi_reset_list_id = AdiInviterPro::POST('adi_reset_list_id', ADI_STRING_VARS);
			if(!empty($adi_reset_list_id))
			{
				adimt_clear_list_cache($adi_reset_list_id);
			}
		break;

		case 'get_contacts':
			$service       = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS);
			$user_email    = AdiInviterPro::POST('adi_user_email'      , ADI_STRING_VARS);
			$user_password = AdiInviterPro::POST('adi_user_password'   , ADI_STRING_VARS);
			$importer_type = AdiInviterPro::POST('importer_type'       , ADI_STRING_VARS);
			$campaign_id   = AdiInviterPro::POST('campaign_id'         , ADI_PLAIN_TEXT_VARS);
			$content_id    = AdiInviterPro::POST('content_id'          , ADI_INT_VARS);

			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use)
			{
				$campaign_id = ''; $content_id = 0;
			}
			// Contact File Importer
			if($importer_type == 'contact_file' && isset($_FILES['adi_contact_file']))
			{
				$file_name = $_FILES['adi_contact_file']['tmp_name'];
				if(file_exists($file_name))
				{
					$real_name = $_FILES['adi_contact_file']['name'];
					$csv_file_format = substr(strrchr($real_name, "."), 1);
					$csv_file_contents = file_get_contents($file_name);
				}
				else
				{
					$adiinviter->error->report_error($adiinviter->phrases['contact_file_not_found']);
				}
			}
			// Manual Inviter
			$contacts_list = AdiInviterPro::POST('adi_contacts_list', ADI_CONTACTLIST_VARS);

			include(ADI_LIB_PATH . 'get_contacts.php');
			if($adiinviter->error->get_error_count() == 0)
			{
				include(ADI_LIB_PATH . 'get_contacts_phrases.php');
			}
		break;

		case 'submit_friend_adder':
			$show_invite_sender = false;
			
			$contacts     = AdiInviterPro::POST('adi_conts',  ADI_ARRAY_VARS);
			$contacts_txt = AdiInviterPro::POST('adi_conts_txt', ADI_STRING_VARS, 'a-z0-9\/\+\=');
			$service_key  = AdiInviterPro::POST('adi_service_key_val', ADI_STRING_VARS, 'a-z0-9_');

			if(!empty($contacts_txt)) {
				$contacts = adi_decode_conts_text($contacts_txt);
			}

			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			$config = $adiinviter_services[$service_key]['info'];
			$config['service_key'] = $service_key;

			$campaign_id  = AdiInviterPro::POST('campaign_id' , ADI_STRING_VARS , 'a-z0-9_');
			$content_id  = AdiInviterPro::POST('content_id' , ADI_INT_VARS);
			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use) {
				$campaign_id = ''; $content_id = 0;
			}
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;

			if(!empty($contacts) && !empty($config))
			{
				$show_invite_sender = true;
			}

			$friends_count = 0;
			if(AdiInviterPro::isPOST('adi_add_friend_button') && AdiInviterPro::isPOST('adi_reg_ids'))
			{
				$reg_ids = AdiInviterPro::POST('adi_reg_ids', ADI_STRING_VARS);
				$reg_ids = explode(':-:', $reg_ids);
				$reg_ids = array_filter($reg_ids, 'is_numeric');
				if(count($reg_ids) > 0) {
					$reg_ids = array_map('intval', $reg_ids);
				}
				if(count($reg_ids) > 0)
				{
					$result = $adiinviter->send_friend_request($adiinviter->userid, $reg_ids);
					$friends_count = count($result);
				}
			}
			$add_as_friend_response_txt = adi_replace_vars($adiinviter->phrases['adi_ip_add_friends_success_msg'], array(
				'friends_count' => $friends_count,
			));

			if($show_invite_sender === true && count($contacts) > 0)
			{
				$show_invites_sender = true;
				$show_friend_adder   = false;

				$file_path = ADI_LIB_PATH.'get_contacts_phrases.php';
				include($file_path);
			}
			else
			{
				if(AdiInviterPro::isPOST('adi_skip_button'))
				{
					$block_header_text  = $adiinviter->phrases['adi_invite_block_header'];
					$block_message_text = $adiinviter->phrases['adi_ip_block_default_message'];
				}
				else
				{
					$block_header_text  = $adiinviter->phrases['adi_add_friends_response_block_header'];
					$block_message_text = $add_as_friend_response_txt;
				}

				$contents .= eval(adi_get_template('inpage_final_message'));
				$load_default_template = false;
			}
		break;

		case 'adi_redirect':
			if(AdiInviterPro::isPOST('adi_invite_history')) {
				$adi_global_redirect_url = $adiinviter->invite_history_url;
			}
			else if(AdiInviterPro::isPOST('adi_website_register')) {
				$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_register_url'];
			}
		break;

		case 'submit_invite_sender':
			$contacts     = AdiInviterPro::POST('adi_conts',  ADI_ARRAY_VARS);
			$conts_json   = AdiInviterPro::POST('adi_conts_json',  ADI_STRING_VARS);
			$contacts_txt = AdiInviterPro::POST('adi_conts_txt', ADI_STRING_VARS, 'a-z0-9\/\+\=');
			$service_key  = AdiInviterPro::POST('adi_service_key_val', ADI_STRING_VARS, 'a-z0-9_');
			$attach_note  = AdiInviterPro::POST('adi_attach_note_txt_input', ADI_PLAIN_TEXT_VARS);

			if(!empty($conts_json))
			{
				$parts = explode(':-:,:-:', $conts_json);
				foreach($parts as $pt)
				{
					$tp = explode(':-:', $pt);
					if(!empty($tp[0])){ $contacts[$tp[0]] = $tp[1]; }
				}
			}

			// if "Ask Not-Logged In User Details" is ON
			if(!empty($contacts_txt)) {
				$contacts = adi_decode_conts_text($contacts_txt);
			}

			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			$config = $adiinviter_services[$service_key]['info'];
			$config['service_key'] = $service_key;
			$service_key = $config['service_key'];

			$campaign_id  = AdiInviterPro::POST('campaign_id', ADI_STRING_VARS, 'a-z0-9_');
			$content_id  = AdiInviterPro::POST('content_id', ADI_INT_VARS);

			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use) {
				$campaign_id = ''; $content_id = 0;
			}
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;


			if(AdiInviterPro::isPOST('adi_ip_sinfo_cancel') || AdiInviterPro::isPOST('adi_invite_more'))
			{
				$load_default_template = true;
			}
			else if(AdiInviterPro::isPOST('adi_invite_history'))
			{
				$adi_global_redirect_url = $adiinviter->invite_history_url;
			}
			else if(AdiInviterPro::isPOST('adi_website_register'))
			{
				$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_register_url'];
			}
			else
			{
				if(is_array($contacts) && count($contacts) > 0 && is_array($config) && count($config) > 0)
				{
					$loading_guest_form = false;
					$adi_sender_name    = AdiInviterPro::POST('adi_sender_name'  , ADI_STRING_VARS);
					$adi_sender_email   = AdiInviterPro::POST('adi_sender_email' , ADI_STRING_VARS);

					if($adiinviter->settings['adiinviter_store_guest_user_info'] == 1 && !AdiInviterPro::isPOST('adi_sender_email') && !AdiInviterPro::isPOST('adi_sender_name') && $adiinviter->userid == 0)
					{
						$contents .= eval(adi_get_template('sender_information_html'));
						$load_default_template = false;
						$loading_guest_form = true;
					}
					else if($adiinviter->settings['adiinviter_store_guest_user_info'] == 1 && 
						(empty($adi_sender_email) || empty($adi_sender_name)) && $adiinviter->userid == 0)
					{
						if(AdiInviterPro::isPOST('adi_sender_email') && AdiInviterPro::isPOST('adi_sender_name'))
						{
							$adiinviter->error->report_error($adiinviter->phrases['all_fields_are_not_filled']);
						}
						$contents .= eval(adi_get_template('sender_information_html'));
						$load_default_template = false;
						$loading_guest_form = true;
					}

					if($loading_guest_form == false)
					{
						$cs_types = adi_getSetting('campaigns', 'campaigns_list');
						$handler_file = ADI_LIB_PATH.'invitation_handler.php';
						require_once($handler_file);
						if(isset($cs_types[$config['campaign_id']]) && !empty($config['campaign_id']))
						{
							$inv_handler = adi_allocate('Adi_Invitations');
						}
						else
						{
							$inv_handler = adi_allocate('Adi_Campaign');
						}

						$inv_handler->set_invitation_type($config['campaign_id'], $config['content_id']);

						// Assign attach note
						$inv_handler->set_attached_note($attach_note);

						// Set Sender
						if($adiinviter->userid == 0 && !empty($adi_sender_email) && !empty($adi_sender_name))
						{
							$inv_handler->set_sender($adi_sender_email, $adi_sender_name);
						}

						// Initialize
						$inv_handler->init($config, $contacts);
						$success_count = $inv_handler->send_invitations();

						if($inv_handler->get_errors_count() > 0)
						{
							$adiinviter->error->report_error($inv_handler->all_errors[0]);
						}
						else
						{
							adi_call_event('after_all_invitations_sent', array(
								'invitations_count' => count($contacts),
								'service_info'      => $config,
								'campaign_id'       => $inv_handler->campaign_id,
								'content_id'        => $inv_handler->content_id,
							));
							
							$block_header_text = $adiinviter->phrases['adi_ip_invitation_sent_block_header'];
							$invitation_sent_response = $adiinviter->phrases['adi_ip_invitation_sent_message_txt'];
							$block_message_text = adi_replace_vars($invitation_sent_response, array(
								'invitation_count' => $success_count,
							));

							$contents .= eval(adi_get_template('inpage_final_message'));
							$load_default_template = false;
						}

						// Clear the cache
						if(!empty($adi_conts_list_id))
						{
							adimt_clear_list_cache($adi_conts_list_id);
						}
					}
				}
				else
				{
					$block_header_text  = $adiinviter->phrases['adi_ip_invitation_sent_block_header'];
					$block_message_text = $adiinviter->phrases['adi_msg_no_contacts_selected'];
					
					$contents .= eval(adi_get_template('inpage_final_message'));
					$load_default_template = false;
				}
			}
		break;

		// default: $load_default_template = true; break;
	}


	if(isset($adi_global_redirect_url) && !empty($adi_global_redirect_url))
	{
		if( !adi_page_redirect($adi_global_redirect_url) )
		{
			$contents .= eval(adi_get_template('inpage_redirect_page'));
		}
		$load_default_template = false;
	}


	// Load Template
	if($load_default_template === true)
	{
		if($adiinviter->settings['adiinviter_onoff'] == 0)
		{
			$adi_display_message = 'AdiInviter Pro is turned Off by your administrator.';
			$contents .= eval(adi_get_template('inpage_no_permissions'));
			$load_default_template = false;
		}
		else if($adiinviter->can_use_adiinviter == false)
		{
			$adi_display_message = $adiinviter->phrases['adi_ip_no_permissions_message'];
			$contents .= eval(adi_get_template('inpage_no_permissions'));
			$load_default_template = false;
		}
		else if($adiinviter->show_recaptcha == true)
		{
			$show_captcha = true;
			$adi_show_error_message = false;

			$g_recaptcha_response = AdiInviterPro::POST('g-recaptcha-response', ADI_STRING_VARS);
			$adi_no_captcha = AdiInviterPro::POST('adi_no_captcha_display'    , ADI_INT_VARS    );
			if($adi_no_captcha !== 1)
			{
				if(!empty($g_recaptcha_response))
				{
					$is_valid = $adiinviter->verify_recaptcha_response($g_recaptcha_response);
					if($is_valid) {
						$show_captcha = false;
						$load_default_template = true;
					}
				}
			}
			else 
			{
				$show_captcha = false;
				$load_default_template = true;
			}

			if($show_captcha == true)
			{
				$load_default_template = false;
				$contents .= eval(adi_get_template('inpage_show_captcha'));
			}
		}

		$importer_type = AdiInviterPro::POST('importer_type', ADI_STRING_VARS);
		$importer_type = (!empty($importer_type) ? $importer_type : 'addressbook');

		$adi_service_key_val = AdiInviterPro::POST('adi_service_key_val', ADI_STRING_VARS);

		if($load_default_template == true)
		{
			$campaign_id = AdiInviterPro::POST('adi_campaign_id', ADI_PLAIN_TEXT_VARS);
			$content_id = AdiInviterPro::POST('adi_content_id', ADI_INT_VARS);
			$campaign_id = (AdiInviterPro::isGET('adi_campaign_id')) ? AdiInviterPro::GET('adi_campaign_id', ADI_PLAIN_TEXT_VARS) : $campaign_id;
			$content_id = (AdiInviterPro::isGET('adi_content_id')) ? AdiInviterPro::GET('adi_content_id', ADI_INT_VARS) : $content_id;
			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use) {
				$campaign_id = ''; $content_id = 0;
			}
			
			$contents .= eval(adi_get_template('inpage_login_page'));
		}
	} // ($load_default_template === true)
} // adiinviter_installed check
?>