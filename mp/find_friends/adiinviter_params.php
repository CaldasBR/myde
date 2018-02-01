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
include_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');

$contents = '';

if( !headers_sent() )
{
	header("Cache-Control: public");
	header("Content-type: text/javascript");
	header("charset: UTF-8");
}

if($adiinviter->adiinviter_installed !== true)
{
	$contents .= '/* AdiInviter Pro is not installed yet. */';
}
else
{
	$adiinviter->requireSettingsList(array('global','db_info','invitation', 'oauth'));
	$adiinviter->init_user();
	$adiinviter->loadPhrases();
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;

	$extended_options = array(
		'phrases'  => array(),
		'services' => array(),
		'aurl'     => rtrim(adi_common_url($adiinviter->settings['adiinviter_root_url']), '/').'/',
	);

	// Phrases required in Javascript
	$requested_phrases = array(
		'manualinv_textarea_default_txt','invalid_server_response','adi_ab_service_field_default_txt','adi_msg_no_contacts_selected','adi_msg_invalid_contact_file_format','adi_msg_no_service_selected','adi_msg_invalid_service','adi_msg_empty_email_address','adi_msg_empty_password','adi_msg_contact_file_not_selected','adi_msg_contact_file_size_limit_exceeded','adi_error_contact_list_length_limit_exceeded','adi_msg_empty_contacts_list','adi_default_message_for_all_popups','adi_acknowledgement_message_before_delete_inv','adi_ab_submit_form_btn_text','adi_oauth_service_submit_btn_label','adi_pp_close_text','adi_ip_sinfo_top_message','adi_search_contacts_default_text',
	);
	foreach($requested_phrases as $varname)
	{
		$extended_options['phrases'][$varname] = $adiinviter->phrases[$varname];
	}

	// Load service details
	$on_services = $adiinviter->settings['services_onoff']['on'];
	$adi_services = adi_allocate_pack('Adi_Services');
	$services_params = $adi_services->get_service_details('all', 'all');
	$adi_captcha_services = $adi_services->captcha_services;

	$supported_services_list = array();
	foreach($on_services as $service_key)
	{
		if(isset($services_params[$service_key]))
		{
			if($services_params[$service_key]['domains'][0] != '*')
			{
				$services_params[$service_key]['domains'] = array('-');
			}
			$require_captcha = in_array($service_key, $adi_captcha_services) ? 1 : 0;
			$services_params[$service_key]['params'][3] = $require_captcha;
			$extended_options['services'][$service_key] = array($services_params[$service_key]['params'], $services_params[$service_key]['domains']);
		}
	}

	$enable_topic_redirect_popup = $adiinviter->check_for_topic_redirect();
	if($enable_topic_redirect_popup)
	{
		$extended_options['rurl'] = 1;
	}

	$extended_options['anl'] = $adiinviter->settings['attach_note_length_limit'];
	$extended_options['zs']  = $adiinviter->lowest_zindex;

	$extended_options['no_avatar_img'] = $adiinviter->default_no_avatar;
	$extended_options['scc'] = ($adiinviter->show_recaptcha ? 0 : 1);

	$register_url = $adiinviter->settings['adiinviter_website_register_url'];
	$opts = array(
		'invitation_id' => '',
		'inviter_id' => '',
	);
	$register_url = adi_replace_vars($register_url, $opts);
	$register_url = adi_common_url($register_url);

	$extended_options['regurl'] = $register_url;
	$extended_options['ihurl']  = $adiinviter->invite_history_url_rel;

	$extended_options['cflt'] = $adiinviter->contact_file_size_limit * 1024;
	$extended_options['cllt'] = $adiinviter->contacts_list_length_limit;
	$extended_options['orie'] = $adiinviter->current_orientation;

	$extended_options['fel'] = $adiinviter->form_hidden_elements;

	// Redirect Url
	$contents .= 'adjq.extend(adi,'.json_encode($extended_options).');'."\n\n";

	if($adiinviter->db_allowed == true && $adiinviter->userid != 0)
	{
		if($enable_topic_redirect_popup)
		{
			$contents .= "\n".'adjq(document).ready(function(){
			setTimeout(function(){
				adipps.tr.show();
			},2000);
		});';
		}
	}

	// Get Popups HTML Code
	$template_file_path = $adiinviter->template_path . ADI_DS . 'popup_html.php';
	include($template_file_path);
	$contents .= "
adi.pphtml = '" . adi_parse_to_js_string($popup_with_back_panel) . "';
adi.wbphtml = '" . adi_parse_to_js_string($popup_without_back_panel) . "';
";

	// Get Contacts HTML Codes
	$template_file_path = $adiinviter->template_path . ADI_DS . 'contacts_html.php';
	include($template_file_path);
	$contents .= "
adi.member_html =  '".      adi_parse_to_js_string($member_without_mutual_friends)."';
adi.member_with_mf_html='". adi_parse_to_js_string($member_with_mutual_friends)."';
adi.mf_html = '".           adi_parse_to_js_string($mutual_friends_list_without_profile_page)."';
adi.mf_with_pp_html = '".   adi_parse_to_js_string($mutual_friends_list_with_profile_page)."';
adi.social_html = '".       adi_parse_to_js_string($social_contact_without_avatar)."';
adi.social_avatar_html = '".adi_parse_to_js_string($social_contact_with_avatar   )."';
adi.email_html = '".        adi_parse_to_js_string($email_contact_without_avatar )."';
adi.email_avatar_html = '". adi_parse_to_js_string($email_contact_with_avatar    )."';
	";

	($adi_hook_code = adi_exec_hook_location('before_javascript_dispatch')) ? eval($adi_hook_code) : false;

	// Get Theme Javascript code.
	if(file_exists($adiinviter->theme_path.ADI_DS.'theme.js'))
	{
		$code = file_get_contents($adiinviter->theme_path.ADI_DS.'theme.js');
		if($code != '')
		{
			$contents .= "\n\n/* AdiInviter theme JS */\n".$code."\n\n";
		}
	}

	// Release any Platform oriented Javascript modifications here
	$contents .= $adiinviter->platform_js();
}

if(!headers_sent())
{
	$ETag = md5($contents);
	$req_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : '';
	if(!error_get_last())
	{
		if(!empty($req_etag) && $ETag === $req_etag)
		{
			header('HTTP/1.1 304 Not Modified');
			exit;
		}
		header('ETag: '.$ETag);
	}
}

echo $contents;
exit;

?>
