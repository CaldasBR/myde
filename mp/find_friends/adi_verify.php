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


include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adi_init.php');
$base_path = dirname(__FILE__);
global $adiinviter;
include_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');
if(!headers_sent())
{
	header("charset: UTF-8");
}
$contents = '';

if($adiinviter->adiinviter_installed !== true)
{
	$contents = '<h3 style="font-family:Verdana,Tahoma;color:#282828;">AdiInviter Pro is not installed yet.</h3>';
}
else
{
	$adiinviter->requireSettingsList(array('global','db_info'));
	$adiinviter->init_user();
	$adiinviter->loadPhrases();
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;


	$adi_action    = AdiInviterPro::GET('adi_do', ADI_STRING_VARS);
	$invitation_id = AdiInviterPro::GET('invitation_id', ADI_STRING_VARS, '0-9a-z');

	($adi_hook_code = adi_exec_hook_location('verify_start')) ? eval($adi_hook_code) : false;

	$inviter_id   = 0;  $invitation_status = '';
	$adi_global_redirect_url = ''; 

	$adi_global_verify_invitation_message = ''; 
	$adi_global_verify_invitation_error = $adiinviter->phrases['adi_vi_defualt_error_msg']; 

	$campaign_id   = ''; $content_id = '';

	$invitation_exists = false;
	$check_params = true;

	$invite_info = array();

	if(!empty($invitation_id) && $adiinviter->db_allowed)
	{
		$invitation_id = preg_replace('/[^0-9a-z]/i', '', $invitation_id);
		if($result = adi_build_query_read('get_invitation_details', array(
			'invitation_ids' => array($invitation_id),
		)))
		{
			if($row = adi_fetch_assoc($result))
			{
				$invitation_exists = true;
				$invite_info = $row;

				$adiinviter->session->set('adi_invitation_id', $invitation_id);
				$inviter_id = $row['inviter_id'];
				$invitation_status = $row['invitation_status'];

				$campaign_id = $row['campaign_id'];
				$content_id = $row['content_id'];
			}
		}
	}


	if($adi_action == 'unsubscribe')
	{
		if($invitation_exists)
		{
			if($invitation_status == 'accepted')
			{
				$adi_global_verify_invitation_error = $adiinviter->phrases['adi_vi_failed_to_block_accepted_invitation'];
			}
			else 
			{
				if($result = adi_build_query_write('update_invite_status', array(
					'status' => 'blocked',
					'invitation_id' => $invitation_id,
				)))
				{
					$adi_global_verify_invitation_message = $adiinviter->phrases['adi_vi_invitation_blocked_messeage'];
					if($invitation_status != 'blocked')
					{
						adi_call_event('invitation_unsubscribed', array(
							'invitation_info' => $invite_info,
						));
					}
				}
			}
		}
		else
		{
			$adiinviter->trace('fl.adi_verify : Invitation with invitation id "'.$invitation_id.'" does not exist.');
		}
	}
	else if($adi_action == 'accept')
	{
		if($invitation_exists)
		{
			if($campaign_id != "")
			{
				if($adiinviter->user_registration_system == true && $adiinviter->userid == 0)
				{
					$register_page_url = trim($adiinviter->settings['adiinviter_website_register_url'], ' ?&');
					$adi_global_redirect_url = $register_page_url;
				}
				else
				{
					$adi_global_redirect_url = get_content_url($campaign_id, $content_id);
				}
			}
			else
			{
				if($invitation_status == 'accepted')
				{
					if($adiinviter->userid == 0 && $adiinviter->user_system)
					{
						$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_login_url'];
					}
					else
					{
						$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_root_url'];
					}
				}
				else
				{
					if($adiinviter->user_registration_system == true)
					{
						$register_page_url = trim($adiinviter->settings['adiinviter_website_register_url'], ' ?&');
						$adi_global_redirect_url = $register_page_url;
					}
					else
					{
						$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_root_url'];
					}
				}
			}
		}
		else
		{
			$adi_global_redirect_url = $adiinviter->settings['adiinviter_website_root_url'];
		}
	}
	else
	{
		$adiinviter->trace('fl.adi_verify : Invalid action id : "'.$adi_action.'"');
	}


	// Perform Action
	if( isset($adi_global_redirect_url) && !empty($adi_global_redirect_url) )
	{
		$vars = array(
			'invitation_id'    => '',
			'inviter_id'       => '',
			'inviter_username' => '',
			'inviter_email'    => '',
		);
		if($invitation_exists)
		{
			$vars['invitation_id'] = $invitation_id;
			$vars['inviter_id'] = $inviter_id;
			if($inviter_id != 0 && (strpos($adi_global_redirect_url, '[inviter_username]') !== false || 
			strpos($adi_global_redirect_url, '[inviter_email]') !== false) )
			{
				$adi_user = $adiinviter->getUserInfo($inviter_id);
				$vars['inviter_username'] = $adi_user->username;
				$vars['inviter_email']    = $adi_user->email;
			}
		}
		$adi_global_redirect_url = adi_replace_vars($adi_global_redirect_url, $vars);
		if(isset($adi_global_redirect_url) && !empty($adi_global_redirect_url))
		{
			if( !adi_page_redirect($adi_global_redirect_url) )
			{
				$contents .= eval(adi_get_template('inpage_redirect_page'));
			}
		}
	}
	else
	{
		$contents .= eval(adi_get_template('inpage_verify_invitation'));
	}
}

?>