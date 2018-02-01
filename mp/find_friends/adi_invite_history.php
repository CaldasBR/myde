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
require_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');

$do = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);
$do = AdiInviterPro::isGET('adi_do') ? AdiInviterPro::GET('adi_do', ADI_STRING_VARS) : $do;
$do = empty($do) ? 'invite_history' : $do;

$adi_current_model = 'invites';
$contents = '';
$adi_global_invite_history_message = '';

if($adiinviter->adiinviter_installed !== true)
{
	$contents = '<h3 style="font-family:Verdana,Tahoma;color:#282828;">AdiInviter Pro is not installed yet.</h3>';
}
else
{
	$adiinviter->requireSettingsList(array('global', 'db_info'));
	$adiinviter->init_user();
	$adiinviter->loadPhrases();
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;

	if($adiinviter->settings['adiinviter_onoff'] == 0)
	{
		$adi_display_message = 'AdiInviter Pro is turned Off by your administrator.';
		$contents .= eval(adi_get_template('inpage_no_permissions'));
	}
	else if($adiinviter->can_use_adiinviter == false)
	{
		$adi_display_message = $adiinviter->phrases['adi_ip_no_permissions_message'];
		$contents .= eval(adi_get_template('inpage_no_permissions'));
	}
	else if($adiinviter->db_allowed == false)
	{
		$adi_global_invite_history_message = $adiinviter->phrases['adi_ih_no_db_integration_msg'];
		$contents .= eval(adi_get_template('invites_show_message'));
	}
	else if($adiinviter->user_system == false)
	{
		$adi_global_invite_history_message = $adiinviter->phrases['adi_ih_no_user_system_integration'];
		$contents .= eval(adi_get_template('invites_show_message'));
	}
	else if($adiinviter->userid == 0)
	{
		$adi_global_invite_history_message = $adiinviter->phrases['adi_ih_not_loggedin_msg'];
		$contents .= eval(adi_get_template('invites_show_message'));
	}
	else
	{
		$adi_invite_history = adi_allocate_pack('Adi_Invite_History');
		$adi_invites_page_no = AdiInviterPro::POST('page_no', ADI_INT_VARS);

		$adi_invites_show_type = AdiInviterPro::POST('show_type', ADI_STRING_VARS);
		$adi_invites_show_type = in_array($adi_invites_show_type, $adi_invite_history->show_types) ? $adi_invites_show_type : 'all';

		$adi_invites_query = trim(AdiInviterPro::POST('adi_invites_query', ADI_STRING_VARS));
		$adi_invites_page_size = max($adi_invite_history->invite_history_pagination_size, 10);
		// $adi_invites_page_size = 2;

		($adi_hook_code = adi_exec_hook_location('invite_history_start')) ? eval($adi_hook_code) : false;

		if(in_array($do, array('invite_history', 'paginate')))
		{
			$invitations_count = $adi_invite_history->get_invitations_count($adiinviter->userid, $adi_invites_show_type, $adi_invites_query);
			$adi_invites_page_no = max($adi_invites_page_no, 1);
			if($invitations_count > 0 || !empty($adi_invites_query))
			{
				$total_pages = ceil($invitations_count / $adi_invites_page_size); 
				$pages_list = get_pages_list($total_pages, $adi_invites_page_no, 3);
				$adi_invites_page_no = min($adi_invites_page_no, $total_pages);
				$ih_records = $adi_invite_history->get_invite_history_recods($adiinviter->userid, $adi_invites_page_no, $adi_invites_page_size, $adi_invites_show_type, $adi_invites_query);

				if($do == 'invite_history')
				{
					$adi_ih_show_message = false;
					$adi_global_invite_history_message = $adiinviter->phrases['adi_ih_no_invites_err_msg'];
					$contents .= eval(adi_get_template('invites_show_message'));

					$adi_show_download_button = false;
					if($adi_invite_history->get_email_invitations_count($adiinviter->userid) > 0 && (bool)$adiinviter->can_download_csv == true)
					{
						$adi_show_download_button = true;
					}
					$contents .= eval(adi_get_template('invites_regular_section'));
				}
				else
				{
					$contents .= eval(adi_get_template('invites_table_contents'));
					echo $contents;
				}
			}
			else
			{
				$adi_global_invite_history_message = $adiinviter->phrases['adi_ih_no_invites_err_msg'];
				$contents .= eval(adi_get_template('invites_show_message'));
			}
		}
		else
		{
			switch($do)
			{
				case 'delete_invites':
					$inv_ids = AdiInviterPro::POST('adi_ih_ids_list', ADI_ARRAY_VARS);
					if(AdiInviterPro::isPOST('adi_ih_ids_list') && is_array($inv_ids) && count($inv_ids) > 0 && $adiinviter->can_delete_invites)
					{
						$invite_history = adi_allocate_pack('Adi_Invite_History');
						$invite_history->delete_invites($inv_ids);
					}
				break;

				case 'download_csv':
					$invite_history = adi_allocate_pack('Adi_Invite_History');
					$contents = '';
					$headers  = $invite_history->get_headers_for_csv();
					$contents = $invite_history->get_contacts_for_csv();
					if(!empty($contents))
					{
						header("Cache-Control: public");
						header("Content-Description: File Transfer");
						header("Content-Disposition: attachment; filename=Contacts.csv");
						header("Content-Type: application/zip");
						header("Content-Transfer-Encoding: binary");

						echo $headers."\r\n".$contents;
						exit;
					}
				break;
			}
			echo $contents;
		}
	}
} // adiinviter_installed check

?>