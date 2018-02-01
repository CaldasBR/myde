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


header("Content-type: text/javascript;");
header("charset: UTF-8");
header("Cache-Control: must-revalidate");

if(isset($_GET['adi_do']) && isset($_POST['adi_do']))
{
	unset($_POST['adi_do']);
}

include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adi_init.php');
$base_path = dirname(__FILE__);
global $adiinviter;
require_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');
$adi_current_model = 'popup';

$do = AdiInviterPro::GET('adi_do', ADI_STRING_VARS, 'a-z_');
$do = empty($do) ? AdiInviterPro::POST('adi_do', ADI_STRING_VARS, 'a-z_') : $do;
$contents = '';

if($adiinviter->adiinviter_installed !== true)
{
	$contents = '/* AdiInviter Pro is not installed yet. */';
}
else
{
	$adiinviter->requireSettingsList(array('global','db_info', 'oauth'));
	$adiinviter->init_user();
	// Hook Location : init_complete
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;

	// Hook Location : ajax_start
	($adi_hook_code = adi_exec_hook_location('ajax_start')) ? eval($adi_hook_code) : false;

	switch($do)
	{
		case 'contact_file':
			$adiinviter->loadPhrases();
			$contents .= eval(adi_get_template('contact_file'));
		break;

		case 'download_sample':
			include(ADI_LIB_PATH . 'download_samples.php');
		break;

		case 'supported_formats':
			$adiinviter->loadPhrases();
			$contents .= eval(adi_get_template('supported_formats'));
		break;

		case 'attach_note':
			$adiinviter->requireSettingsList('invitation');
			$adiinviter->loadPhrases();

			$contents .= eval(adi_get_template('popup_attach_note'));
		break;

		case 'topic_redirect':
			$adiinviter->loadPhrases();

			$content_url = '';
			$content_title = '';

			$adi_invitation_id = $adiinviter->session->get('adi_show_redirect');
			if(!empty($adi_invitation_id))
			{
				$result = adi_build_query_read('check_user_redirection', array(
					'field_name' => 'invitation_id',
					'field_value' => $adi_invitation_id,
				));
				$adiinviter->session->remove('adi_show_redirect');
			}
			else {
				$result = adi_build_query_read('check_user_redirection', array(
					'field_name' => 'receiver_userid',
					'field_value' => $adiinviter->userid,
				));
			}

			if($result)
			{
				if($rr = adi_fetch_array($result))
				{
					$campaign_id = $rr['campaign_id'];
					$content_id = $rr['content_id'];
					$inviter_info = $adiinviter->phrases['defualt_inviter_username'];
					$content_settings = adi_getSetting('campaign_'.$campaign_id);

					$userid = $rr['inviter_id'];
					if($userid != 0)
					{
						$adi_user = $adiinviter->getUserInfo($userid);
						$inviter_info = '<font style="font-weight:bold;">'.$adi_user->username.'</font>';
						if($adiinviter->profile_page_system == true)
						{
							$opts = array(
								'userid' => $adi_user->userid,
								'username' => $adi_user->username,
								'email' => $adi_user->email,
							);
							$profile_page_url = $adiinviter->getProfilePageURL($opts);
							$inviter_info = '<a class="adi_link" href="'.$profile_page_url.'" target="_blank">'.$inviter_info.'</a>';
						}
					}
					else 
					{
						$result = adi_build_query_read('check_guest_id', array(
							'field_name' => 'invitation_id',
							'field_value' => $rr['invitation_id'],
						));
						if($row = adi_fetch_array($result))
						{
							$inviter_info = $row['name'];
						}
					}

					$content_url = get_content_url($campaign_id, $content_id, $content_settings);
					$content_title = get_content_title($campaign_id, $content_id, $content_settings);

					$cs_redirect_top_message = str_replace('[inviter_info]', $inviter_info, $adiinviter->phrases['cs_redirect_top_message']);
					$contents .= eval(adi_get_template('popup_topic_redirect'));

					if(!empty($adi_invitation_id))
					{
						adi_build_query_write('update_invitations', array(
							'update_field' => 'topic_redirect',
							'update_value' => 0,
							'check_field'  => 'invitation_id',
							'check_value'  => $adi_invitation_id,
						));
					}
					else
					{
						adi_build_query_write('update_invitations', array(
							'update_field' => 'topic_redirect',
							'update_value' => 0,
							'check_field'  => 'receiver_userid',
							'check_value'  => $adiinviter->userid,
						));
					}
				}
			}
		break;

		case 'invite_preview':
			$adiinviter->requireSettingsList('invitation');
			$adiinviter->loadPhrases();
			$adiinviter_services = array();

			$service_key = AdiInviterPro::POST('service'     , ADI_STRING_VARS, ADI_SERVICE_CHARLIST);
			$campaign_id = AdiInviterPro::POST('campaign_id' , ADI_STRING_VARS, ADI_CAMPAIGNID_CHARLIST);
			$content_id  = AdiInviterPro::POST('content_id'  , ADI_STRING_VARS, ADI_CONTENTID_CHARLIST);

			$attach_note = AdiInviterPro::POST('attach_note', ADI_PLAIN_TEXT_VARS);
			$attach_note = empty($attach_note) ? $adiinviter->phrases['default_attach_note_in_preview'] : strip_tags($attach_note);

			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use) {
				$campaign_id = ''; $content_id = 0;
			}

			$adi_services = adi_allocate_pack('Adi_Services');
			$config = array();
			if(!empty($service_key))
			{
				$adiinviter_services = $adi_services->get_service_details($service_key);
				if(isset($adiinviter_services[$service_key]))
				{
					$config = $adiinviter_services[$service_key]['info'];
					$config['service_key'] = $service_key;
				}
			}
			$invitation_body = '';
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;
			
			if(count($config) > 0)
			{
				require_once(ADI_LIB_PATH.'invitation_handler.php');
				if($campaign_id == '')
				{
					$inv_handler = adi_allocate_pack('Adi_Invitations');
				}
				else
				{
					$inv_handler = adi_allocate_pack('Adi_Campaign');
				}

				$inv_handler->set_invitation_type($campaign_id, $content_id);

				// Assign attach note
				$inv_handler->set_attached_note($attach_note);

				$adi_sender_name  = $adiinviter->settings['adiinviter_sender_name'];
				$adi_sender_email = $adiinviter->settings['adiinviter_email_address'];

				// Set Sender
				if($adiinviter->userid == 0 && !empty($adi_sender_email) && !empty($adi_sender_name))
				{
					$inv_handler->set_sender($adi_sender_email, $adi_sender_name);
				}
				
				// Initialize
				$contacts = array(
					'receiver_email@domain.com' => array('name' => 'Your Friend'),
				);
				if(is_numeric($adiinviter->num_invites) && $adiinviter->num_invites == 0)
				{
					$adiinviter->num_invites++;
				}
				$inv_handler->init($config, $contacts);
				$invitation_body = $inv_handler->body;
			}
			else
			{
				$invitation_body = $adiinviter->phrases['invitation_preview_invalid_service_name'];
			}

			$invitation_body = str_replace("'", '&#39;', $invitation_body);
			$invitation_body = str_replace("'", "\\'", adi_parse_to_js_string($invitation_body));
			$invitation_body = str_replace(' href="', ' onclick="return false;" href="', $invitation_body);
			$contents .= eval(adi_get_template('popup_invite_preview'));
		break;

		case 'type_search' :
			$query = trim(AdiInviterPro::POST('query', ADI_STRING_VARS, '0-9a-z_.'));
			$result = array();
			if(strlen($query) > 3 && strpos($query, '.') !== false) 
			{
				$adi_services = adi_allocate_pack('Adi_Services');
				$adiinviter_domains = $adi_services->get_service_details('all', 'domains');

				foreach($adiinviter_domains as $service_id => $params)
				{
					if(count($params['domains']) > 0)
					{
						if($params['domains'][0] != '*') 
						{
							foreach($params['domains'] as $domain)
							{
								if(strpos($domain, $query) === 0)
								{
									$result[$service_id][] = $domain;
								}
							}
						}
					}
				}
			}
			echo json_encode($result);
		break;

		case 'login_form' :
			$adiinviter->loadPhrases();

			$campaign_id = '';
			$content_id = '';

			if($adiinviter->show_recaptcha == '1' && $do != 'security_check')
			{
				$real_scc_key = $adiinviter->get_scc_key();
				$scc_key = AdiInviterPro::GET('adi_scc', ADI_STRING_VARS, 'a-z0-9');
				if($scc_key != $real_scc_key && !AdiInviterPro::isPOST('recaptcha_challenge_field') && !AdiInviterPro::isPOST('recaptcha_response_field'))
				{
					// exit;
				}
			}
			$contents .= eval(adi_get_template('login_form'));
		break;

		case 'submit_friend_adder':
			$adiinviter->loadPhrases();

			$friends_count = 0;
			$reg_ids = AdiInviterPro::POST('adi_reg_ids', ADI_STRING_VARS);
			$reg_ids = explode(':-:', $reg_ids);
			$reg_ids = array_filter($reg_ids, 'is_numeric');
			if(AdiInviterPro::isPOST('adi_reg_ids') && count($reg_ids) > 0 && $adiinviter->userid != 0)
			{
				$reg_ids = array_map('intval', $reg_ids);
				if(count($reg_ids) > 0)
				{
					$result = $adiinviter->send_friend_request($adiinviter->userid, $reg_ids);
					$friends_count = count($result);
				}
			}
			if($friends_count > 0)
			{
				$popup_message_text = str_replace('[count]', $friends_count, $adiinviter->phrases['friends_added_message']).
				'<input type="hidden" class="adi_fr_added_ids" value="'.implode(',', $reg_ids).'">';
				$contents .= eval(adi_get_template('popup_final_message'));
			}
			else
			{
				$adiinviter->error->report_error($adiinviter->phrases['adi_msg_no_contacts_selected']);
				echo $adiinviter->error->generate_error_for_js();
			}
		break;

		case 'oauth_login':
			$service_key = AdiInviterPro::GET('adi_service'  , ADI_STRING_VARS , ADI_SERVICE_CHARLIST );
			$username    = AdiInviterPro::GET('adi_username' , ADI_STRING_VARS );
			$step        = AdiInviterPro::GET('adi_s'        , ADI_STRING_VARS , 'a-z0-9_' );

			$campaign_id  = AdiInviterPro::GET('adi_campaign_id' , ADI_STRING_VARS, ADI_CAMPAIGNID_CHARLIST);
			$content_id  = AdiInviterPro::GET('adi_content_id' , ADI_STRING_VARS, ADI_CONTENTID_CHARLIST);

			$adi_fc = AdiInviterPro::GET('adi_fc', ADI_INT_VARS);
			$adi_fr = AdiInviterPro::GET('adi_fr', ADI_INT_VARS);

			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use) {
				$campaign_id = ''; $content_id = 0;
			}

			$config = array();
			if(!empty($service_key))
			{
				$adi_services = adi_allocate_pack('Adi_Services');
				$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
				$config = $adiinviter_services[$service_key]['info'];
				$config['service_key'] = $service_key;
			}
			include_once(ADI_LIB_PATH . 'importer.php');
			if(count($config) > 0)
			{
				$importer = new Adi_OAuth_Importer();
				if($importer->init($service_key))
				{
					$adi_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    				$adi_domainname = $_SERVER['HTTP_HOST'];

    				$adi_site_domain = $adi_protocol.$adi_domainname;
					if($step == 'start')
					{
						if(!empty($campaign_id))
						{
							$adiinviter->session->set($service_key.'_campaign_id', $campaign_id);
							$adiinviter->session->set($service_key.'_content_id', $content_id);
						}
						$adiinviter->session->set($service_key.'_rdomain', $adi_site_domain);
						$importer->get_request_token();
					}
					else
					{
						$rev_domain='';
						if($adi_fc > 7)
						{
							$adiinviter->error->report_error('Redirect loop detected.');
						}

						if($adiinviter->session->is_set($service_key.'_campaign_id'))
						{
							$campaign_id = $adiinviter->session->get($service_key.'_campaign_id');
							$adiinviter->session->remove($service_key.'_campaign_id');
							$content_id = $adiinviter->session->get($service_key.'_content_id');
							$adiinviter->session->remove($service_key.'_content_id');
						}
						if($adiinviter->session->is_set($service_key.'_rdomain'))
						{
							$rev_domain = $adiinviter->session->get($service_key.'_rdomain');
							// $adiinviter->session->remove($service_key.'_rdomain');
						}
						else
						{
							$adiinviter->error->report_error('Failed to initialize session.');
						}

						$response = 0;
						if($adi_fc === 0 && $adiinviter->error->get_error_count() == 0)
						{
							$response = (string)$importer->get_access_token();
						}
						if(AdiInviterPro::isGET('adi_fr'))
						{
							$response = $adi_fr;
						}
						if(!headers_sent()) {
							header("Content-type: text/html;");
						}

						$adi_post_fields = '';
						foreach($adiinviter->form_hidden_elements as $elem_name => $elem_val)
						{
							$adi_post_fields .= '<input type="hidden" name="'.$elem_name.'" value="'.$elem_val.'">';
						}
						$real_domain_url = '';
						if(!empty($rev_domain))
						{
							$adi_fc++;
							$is_https = ((isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) || 
							(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on"));
							if(preg_match('/https?:\/\//', $rev_domain) === false) {
								$rev_domain = "http" . ($is_https ? "s://" : "://") . $rev_domain;
							}
							$real_domain_url = $url = $rev_domain . $_SERVER['REQUEST_URI'] . '&adi_fc='.$adi_fc.'&adi_fr='.$response;
						}

						$contents = '<!DOCTYPE html>
<html><head><script type="text/javascript">
function load_occurred()
{
	var inpage = (window.opener == undefined || window.opener == null);
	if(inpage) {
		document.getElementById("adi_oauth_redirect").submit();
	}
	else {
		var errmsg = "'.$adiinviter->error->last_error.'";
		if(errmsg != "")
		{
			document.write(errmsg);
		}
		else
		{
			try {
				var op = window.opener.adi_oauth_resp;
				op.respond("'.$response.'"); window.close();
			}
			catch(err){
				'.(!empty($real_domain_url) ? 'window.location.href = "'.$real_domain_url.'";' : '').'
			}
		}
	}
}
</script></head><body>
<form action="'.$adiinviter->inpage_model_url_rel.'" method="post" id="adi_oauth_redirect">
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="adi_oauth" value="show_contacts">
	<input type="hidden" name="importer_type" value="addressbook">
	<input type="hidden" name="adi_service_key_val" value="'.$service_key.'">
	<input type="hidden" name="campaign_id" value="'.$campaign_id.'" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="'.$content_id.'" class="adi_nc_content_id">
	'.$adi_post_fields.'
</form>
<script type="text/javascript">
load_occurred();
</script>
</body></html>';
					}
				}
				else
				{
					if($adiinviter->error->get_error_count() > 0)
					{
						echo $adiinviter->error->errors[0];
					}
				}
			}
		break;

		case 'get_contacts':		
			$adiinviter->loadPhrases();

			$service        = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS, ADI_SERVICE_CHARLIST );
			$user_email     = AdiInviterPro::POST('adi_user_email'      , ADI_STRING_VARS );
			$user_password  = AdiInviterPro::POST('adi_user_password'   , ADI_STRING_VARS );
			$importer_type  = AdiInviterPro::POST('importer_type'       , ADI_STRING_VARS, 'a-z_' );
			$campaign_id    = AdiInviterPro::POST('campaign_id'         , ADI_STRING_VARS, ADI_CAMPAIGNID_CHARLIST );
			$content_id     = AdiInviterPro::POST('content_id'          , ADI_STRING_VARS, ADI_CONTENTID_CHARLIST );
			$adi_session_id = AdiInviterPro::POST('adi_session_id'      , ADI_STRING_VARS, ADI_CONTENTID_CHARLIST );
			$adi_contacts_target = AdiInviterPro::POST('adi_contacts_target', ADI_STRING_VARS );

			if(!empty($adi_contacts_target)) {
				$adi_process_contacts = false;
			}

			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use)
			{
				$campaign_id = ''; $content_id = 0;
			}

			if($importer_type == 'contact_file')
			{
				header("Content-type: text/html;");

				echo '<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head><body><div id="adi_contents"><!--';
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
			$contacts_list = AdiInviterPro::POST('adi_contacts_list', ADI_CONTACTLIST_VARS);

			// Get contacts according to importer_type
			$file_path = ADI_LIB_PATH.'get_contacts.php';
			include($file_path);

			if($adiinviter->error->get_error_count() == 0)
			{
				// Get contacts according to importer_type
				$file_path = ADI_LIB_PATH.'get_contacts_phrases.php';
				include($file_path);
				echo $contents;
				$contents = '';
			}
			else 
			{
				echo $adiinviter->error->generate_error_for_js();
			}

			if($importer_type == 'contact_file')
			{
				echo "\n".'--></div><script language="javascript" type="text/javascript">window.parent.window.adi_parse_cf_resp(document.getElementById("adi_contents").innerHTML.replace(/^<!--|-->/g,"").replace(/^\&lt;!--|--\&gt;/g,""));</script></body></html>';
			}
		break;

		case 'submit_invite_sender':
			$adiinviter->loadPhrases();

			$contacts    = AdiInviterPro::POST('adi_conts',  ADI_ARRAY_VARS);
			$conts_json  = AdiInviterPro::POST('adi_conts_json',  ADI_STRING_VARS);
			$service_key = AdiInviterPro::POST('adi_service_key_val', ADI_STRING_VARS, 'a-z0-9_');
			$attach_note = AdiInviterPro::POST('adi_attach_note_txt_input', ADI_PLAIN_TEXT_VARS);


			if(!empty($conts_json))
			{
				$parts = explode(':-:,:-:', $conts_json);
				foreach($parts as $pt)
				{
					$tp = explode(':-:', $pt);
					if(!empty($tp[0])){ $contacts[$tp[0]] = $tp[0]; }
				}
			}
			

			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			$config = $adiinviter_services[$service_key]['info'];
			$config['service_key'] = $service_key;
			
			$campaign_id  = AdiInviterPro::POST('campaign_id', ADI_STRING_VARS, 'a-z0-9_');
			$content_id  = AdiInviterPro::POST('content_id', ADI_INT_VARS);
			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use)
			{
				$campaign_id = ''; $content_id = 0;
			}
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;


			if(!is_array($config) || count($config) == 0)
			{
				$popup_message_text = $adiinviter->phrases['adi_msg_no_contacts_selected'];
				$contents .= eval(adi_get_template('popup_final_message'));
			}
			else
			{
				$adi_sender_name = ''; $adi_sender_email = '';

				$adi_sender_name  = AdiInviterPro::POST('adi_sender_name', ADI_STRING_VARS);
				$adi_sender_email = AdiInviterPro::POST('adi_sender_email', ADI_STRING_VARS);

				$handler_file = ADI_LIB_PATH.'invitation_handler.php';
				require_once($handler_file);
				if($config['campaign_id'] == '')
				{
					$inv_handler = adi_allocate('Adi_Invitations');
				}
				else
				{
					$inv_handler = adi_allocate('Adi_Campaign');
				}

				$inv_handler->set_invitation_type($campaign_id, $content_id);

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

					$popup_message_text = adi_replace_vars($adiinviter->phrases['adi_ip_invitation_sent_message_txt'], array(
						'invitation_count' => $success_count,
					));
					$contents .= eval(adi_get_template('popup_final_message'));
				}
			}
		break;

		case 'final_message':
			$adiinviter->loadPhrases();
			$popup_message_text = $adiinviter->phrases['adi_ip_block_default_message'];
			$contents .= eval(adi_get_template('popup_final_message'));
		break;

		case 'get_sender_info':
			$adiinviter->loadPhrases();
			$attach_note = AdiInviterPro::POST('adi_attach_note_txt_input', ADI_PLAIN_TEXT_VARS);
			$service_key = AdiInviterPro::POST('adi_service_key_val', ADI_PLAIN_TEXT_VARS);

			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			$config = $adiinviter_services[$service_key]['info'];
			$config['service_key'] = $service_key;

			$campaign_id  = AdiInviterPro::POST('campaign_id', ADI_STRING_VARS, 'a-z0-9_');
			$content_id  = AdiInviterPro::POST('content_id', ADI_INT_VARS);
			$allow_campaign_use = $adiinviter->is_campaign_allowed($campaign_id, $content_id);
			if(!$allow_campaign_use)
			{
				$campaign_id = ''; $content_id = 0;
			}
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;

			$contents .= eval(adi_get_template('sender_information_html'));
		break;

		case 'security_check' :
			if(AdiInviterPro::isPOST('g-recaptcha-response'))
			{
				$contents = "adipps.rc.show_err();";
				$g_recaptcha_response = AdiInviterPro::POST('g-recaptcha-response', ADI_STRING_VARS);
				if(!empty($g_recaptcha_response))
				{
					$is_valid = $adiinviter->verify_recaptcha_response($g_recaptcha_response);
					if($is_valid) {
						$contents = "adipps.rc.set_key('".$adiinviter->get_scc_key()."');";
					}
				}
			}
			else
			{
				$adiinviter->loadPhrases();
				$contents .= eval(adi_get_template('popup_show_captcha'));
			}
		break;


		// Special Campaign check implemented for Wordpress package 3.5.1.
		case 'check_campaign':
			$content_settings = adi_getSetting('campaign_post_share');

			$pids = AdiInviterPro::POST('post_ids', ADI_ARRAY_VARS);
			$response_arr = array();

			if(count($pids) > 0 && $adiinviter->settings['adiinviter_onoff'] == 1 && $content_settings['campaign_on_off'] == 1)
			{
				$restricted_ids = array();
				if(!empty($content_settings['restricted_ids']))
				{
					$restricted_ids = explode(',', $content_settings['restricted_ids']);
				}
				$restricted_category_ids = array();
				if(!empty($content_settings['restricted_category_ids']))
				{
					$restricted_category_ids = explode(',', $content_settings['restricted_category_ids']);
				}

				foreach($pids as $postid)
				{
					$response_arr[$postid] = 1;
					if(is_numeric($postid) && $postid > 0)
					{
						if(in_array($postid, $restricted_ids))
						{
							unset($response_arr[$postid]);
						}

						$categories = get_the_category($postid);
						if(count($categories) > 0 && count($restricted_category_ids) > 0)
						{
							foreach($categories as $categ)
							{
								if(in_array($categ->term_id, $restricted_category_ids))
								{
									unset($response_arr[$postid]);
								}
							}
						}
					}
				}
			}
			$contents = json_encode($response_arr);
		break;

		case 'get_importer_captcha':
			$adiinviter->loadPhrases();

			$service_key   = AdiInviterPro::POST('adi_service_key_val' , ADI_STRING_VARS, ADI_SERVICE_CHARLIST );
			$user_email    = AdiInviterPro::POST('adi_user_email'      , ADI_STRING_VARS );
			$user_password = AdiInviterPro::POST('adi_user_password'   , ADI_STRING_VARS );
			$importer_type = AdiInviterPro::POST('importer_type'       , ADI_STRING_VARS, 'a-z_' );

			$adi_services = adi_allocate_pack('Adi_Services');
			$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
			$config = $adiinviter_services[$service_key]['info'];
			$config['service_key'] = $service_key;	

			$adi_captcha_url = '';
			$adi_captcha_info = array();
			if(is_array($config) && count($config) > 0)
			{
				include_once(ADI_LIB_PATH . 'importer.php');
				$adi_importer = new Adi_Importer();
				if($adi_importer->initService($service_key))
				{
					$adiinviter->importer->setAccessDetails($user_email, $user_password);
					$adi_importer->get_captcha($adi_captcha_info);
				}
			}
			$adi_captcha_url = '';
			if(isset($adi_captcha_info['captcha_url'])) {
				$adi_captcha_url = $adi_captcha_info['captcha_url'];
				unset($adi_captcha_info['captcha_url']);
			}
			$contents .= eval(adi_get_template('importer_captcha_html'));

		break;

		default:
			break;
	}
}

echo $contents;

?>