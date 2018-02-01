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


$adiinviter->requireSettingsList('invitation');

$service_key = $config['service_key'];
$adi_output_contacts_list = '';

$adi_current_model = isset($adi_current_model) ? $adi_current_model : 'popup';
$adi_process_contacts = isset($adi_process_contacts) ? $adi_process_contacts : true;

$service_name = $config['service'];
if($config['service_key'] == 'csv_inviter') {
	$service_name = $adiinviter->phrases['contact_file_display_name'];
}
else if($config['service_key'] == 'manual_inviter') {
	$service_name = $adiinviter->phrases['manual_inviter_display_name'];
}

$adi_phrase_vars = array(
	'website_url'         => $adiinviter->settings['adiinviter_root_url'],
	'adiinviter_root_url' => $adiinviter->settings['adiinviter_website_root_url'],
	'login_url'           => $adiinviter->settings['adiinviter_website_login_url'],
	'register_url'        => $adiinviter->settings['adiinviter_website_register_url'],

	'service_key'         => $config['service_key'],
	'service_name'        => $config['service'],

	'invitation_id'       => '',
	'registered_count'    => isset($registered_contacts) ? count($registered_contacts) : 0,
	'conts_count'         => isset($contacts) ? count($contacts) : 0,
);


if($show_friend_adder)
{
	$fa_top_msg_in_guest_user_friend = adi_replace_vars($adiinviter->phrases['fa_top_msg_in_guest_user_friend'], $adi_phrase_vars);
	$fa_top_msg_in_user_friend = adi_replace_vars($adiinviter->phrases['fa_top_msg_in_user_friend'], $adi_phrase_vars);
	$fa_top_msg_in_guest_user  = adi_replace_vars($adiinviter->phrases['fa_top_msg_in_guest_user'], $adi_phrase_vars);
	$fa_default_top_msg        = adi_replace_vars($adiinviter->phrases['fa_default_top_msg'], $adi_phrase_vars);

	if($adiinviter->userid != 0)
	{
		if($adiinviter->friends_system)
		{
			$fa_top_msg = $fa_top_msg_in_user_friend;
		}
		else 
		{
			$fa_top_msg = $fa_default_top_msg;
		}
	}
	else
	{
		if($adiinviter->friends_system)
		{
			$fa_top_msg = $fa_top_msg_in_guest_user_friend;
		}
		else {
			if($adiinviter->user_registration_system == true) {
				$fa_top_msg = $fa_top_msg_in_guest_user;
			}
			else {
				$fa_top_msg = $fa_default_top_msg;
			}
		}
	}

	if($adiinviter->friends_system == true)
	{
		$fa_top_header = $adiinviter->phrases['fa_top_header_with_friends_system'];
	}
	else
	{
		$fa_top_header = $adiinviter->phrases['fa_top_header_without_friends_system'];
	}
}

if($show_invites_sender)
{
	$invites_info = array('num_invites' => $adiinviter->num_invites);
	$adiinviter_number_of_invitations_txt = adi_replace_vars($adiinviter->phrases['adiinviter_number_of_invitations_txt'],$invites_info);

	$adiinviter_invitation_statuses = array(
		'blocked'         => $adiinviter->phrases['adi_invitation_status_blocked'],
		'invitation_sent' => $adiinviter->phrases['adi_invitation_status_invited'],
		'never_invited'   => $adiinviter->phrases['adiinviter_invitation_never_invited'],
		'waiting'         => $adiinviter->phrases['adiinviter_invitation_waiting'],
	);
}

$open_login_form_link = $adiinviter->phrases['open_login_form_link'];

$contacts_html_file = $adiinviter->theme_path.ADI_DS.'contacts_html.php';
if(file_exists($contacts_html_file)) {
	include($contacts_html_file);
}

// Number of invites
$num_invtes_js_val = $adiinviter->num_invites;
if(strtolower($adiinviter->num_invites) == 'unlimited') {
	$num_invtes_js_val = count($contacts);
}
else if(!is_numeric($adiinviter->num_invites)) {
	$num_invtes_js_val = 0;
}

// Config JSON
$config_json = json_encode($config);


if(!$adi_process_contacts && $config['email'] == 1)
{
	$sep = '';
	foreach($contacts as $email => $details)
	{
		$adi_output_contacts_list .= $sep . $details['name'] .' <' . $email . '>';
		$sep = ', ';
	}
	$adi_output_contacts_list = str_replace("'", '&#39;', $adi_output_contacts_list);
}

($adi_hook_code = adi_exec_hook_location('before_contacts_dispatch')) ? eval($adi_hook_code) : false;

if($show_friend_adder || $show_invites_sender)
{
	$contents .= eval(adi_get_template('show_contacts'));
}

?>