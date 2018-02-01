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
$admin_path = dirname(__FILE__);

$sett_id = AdiInviterPro::POST('sett_id', ADI_STRING_VARS);
$sett_group_name = AdiInviterPro::POST('sett_group_name', ADI_STRING_VARS);
$setting_name = AdiInviterPro::POST('setting_name', ADI_STRING_VARS);
$lang_id = AdiInviterPro::POST('lang_id', ADI_STRING_VARS);

if(empty($sett_group_name) || empty($setting_name) || empty($lang_id)) {
	exit;
}

$settings = adi_getSetting($sett_group_name);
if(!is_array($settings) || count($settings) == 0) {
	exit;
}


$available_lang_ids = $adiinviter->get_installed_lang_ids();
if(!in_array($lang_id, $available_lang_ids))
{
	$lang_id = $adiinviter->current_language;
}


$handler_file = ADI_LIB_PATH.'invitation_handler.php';
require_once($handler_file);


$inv_handler = adi_allocate('Adi_Invitations');



$common_markups = array(
"website_name"          => "Your Website Name",
"website_url"           => "Your Website URL  ",
"website_logo"          => "Your Website Logo",
"register_link"         => "Your Website Sign Up Page URL",
"invitation_id"         => "Unique Invitation Id",

"invitation_assets_url" => "Invitation Assets Directory URL",
"receiver_name"         => "Imported Contact's Name",
"receiver_email"        => "Imported Contact's Email Address",
"service"               => "Importer Service Name",
"service_id"            => "Importer Service Id",

"sender_name"       => "Invite Sender's Full Name",
"sender_email"          => "Email Address Of Invite Sender",
"sender_avatar_url"     => "Invite Sender's Avatar Image URL",
"sender_profile_url"    => "Invite Sender's Profile Page URL",
"sender_userid"         => "User Id Of Invite Sender",
"sender_username"       => "Username Of Invite Sender",

"adiinviter_root_url"   => "AdiInviter Pro Root Directory URL",
"verify_invitation_url" => "Verify Invitation Page URL",

"content_id"            => "Dynamic Content Id",
"content_title"         => "Dynamic Content Title",
"content_body"          => "Dynamic Content Body",
"category_id"           => "Category Id Of Dynamic Content",
"content_url"           => "Dynamic Content URL",
"alias_url_value"       => "URL Alias Value Of Dynamic Content",

"guest_mode"            => "Begin Not-Logged In User Contents",
"/guest_mode"           => "End Not-Logged In User Contents",
"user_mode"             => "Begin Logged In User Contents",
"/user_mode"            => "End Logged In User Contents",
"attach_note_block"     => "Begin Attach Note Contents Block",
"/attach_note_block"    => "End Attach Note Contents Block",
"attached_note"         => "Attached Note Text",

"invitations_count"             => "Total Invitations Sent",
"issued_date"                   => "Invitation Issue Date",
"elapsed_days"                  => "Invitation Elapsed Days",

);




$top_header_text = $top_header_desc_text = '';
$markups = array();
$markups_header = $markups_desc = '';
$textarea_css = 'textarea_height2';

$signup_block = $user_mode_block = $guest_mode_block = $attach_note_block = true;

$make_right_desc_shorter = false;

$show_preview_btn = true;
$task_settings = array();

$setting_name_id = $sett_group_name.':'.$setting_name;
if($sett_id == 'campaign') {
	$setting_name_id = 'campaign:'.$setting_name;
}
else if($sett_id == 'task') {
	$setting_name_id = 'task:'.$setting_name;
	$task_id = $sett_group_name;
	$task_settings = adi_getSetting($task_id);
	$found = false;
	if(is_array($task_settings) && count($task_settings) > 0)
	{
		$task_file = ADI_PLUGINS_PATH . $task_id.'.php';
		if(file_exists($task_file)) 
		{
			include_once(ADI_LIB_PATH.'adiinviter_plugins.php');
			include_once($task_file);
			$found = true;
		}
	}
	$class_name = $task_id;
	if($found && class_exists($class_name))
	{
		$task_handler = new $class_name();
		if(isset($task_handler->custom_settings[$setting_name]))
		{
			$option = $task_handler->custom_settings[$setting_name];
			$subject_editor  = isset($option['subject_editor']) ? $option['subject_editor'] : false;
			$template_editor = isset($option['template_editor']) ? $option['template_editor'] : false;
			if($subject_editor) {
				$setting_name_id = 'task:subject_editor';
			}
			else {
				$setting_name_id = 'task:template_editor';
			}
		}
	}
}





switch($setting_name_id)
{
	// Regular Invitations
	case 'invitation:invitation_subject':
		$top_header_text = 'Edit Invitation Subject';
		$top_header_desc_text = 'Modify or customize invitation email subject.';

		$source_code_label = 'Enter Invitation Subject';
		$textarea_css = 'textarea_height1';

		$markups_header = 'Invitation Subject Markups';
		$markups_desc = 'You can use following markups under invitation subject.';

		$signup_block = $attach_note_block = false;

		$show_preview_btn = false;

		$markups = array(
			"website_name", "receiver_name", "service", "service_id", "sender_name", "sender_username", "guest_mode", "/guest_mode", "user_mode", "/user_mode",
		);
	break;


	case 'invitation:invitation_body':
		$top_header_text = 'Edit Invitation Email Body';
		$top_header_desc_text = 'Modify or customize invitation email message body.';

		$source_code_label = 'Invitation Email Source Code (HTML Supported)';
		$textarea_css = 'textarea_height2';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url", "guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;


	case 'invitation:invitation_social_body':
		$top_header_text = 'Edit Social Network Invitation Body';
		$top_header_desc_text = 'Invitation message body for social network invitations.';

		$source_code_label = 'Social Network Invitation Body (No HTML)';
		$textarea_css = 'textarea_height3';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url", "guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;


	case 'invitation:invitation_social_body_twitter':
		$top_header_text = 'Edit Invitation Message Body For Twitter';
		$top_header_desc_text = 'Invitation message body for Twitter invitations.';

		$source_code_label = 'Invitation Message Body (No HTML)';
		$textarea_css = 'textarea_height3';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url", "guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;





// Campaign invitations
	case 'campaign:invitation_subject' :
		$top_header_text = 'Edit Invitation Subject';
		$top_header_desc_text = 'Modify or customize invitation email subject.';

		$source_code_label = 'Enter Invitation Subject';
		$textarea_css = 'textarea_height1';

		$markups_header = 'Invitation Subject Markups';
		$markups_desc = 'You can use following markups under invitation subject.';

		$signup_block = $attach_note_block = false;

		$show_preview_btn = false;

		$markups = array(
			"website_name", "receiver_name", "service", "service_id", "sender_name", "sender_username", 
			"content_id", "content_title", "content_body", "category_id", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode",
		);
	break;


	case 'campaign:campaign_email_body' :
		$top_header_text = 'Edit Invitation Email Body';
		$top_header_desc_text = 'Modify or customize invitation email message body.';

		$source_code_label = 'Invitation Email Source Code (HTML Supported)';
		$textarea_css = 'textarea_height2';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url",
			"content_id", "content_title", "content_body", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;


	case 'campaign:campaign_social_body' :
		$top_header_text = 'Edit Social Network Invitation Body';
		$top_header_desc_text = 'Invitation message body for social network invitations.';

		$source_code_label = 'Social Network Invitation Body (No HTML)';
		$textarea_css = 'textarea_height3';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url",
			"content_id", "content_title", "content_body", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;


	case 'campaign:campaign_twitter_body' :
		$top_header_text = 'Edit Invitation Message Body For Twitter';
		$top_header_desc_text = 'Invitation message body for Twitter invitations.';

		$source_code_label = 'Invitation Message Body (No HTML)';
		$textarea_css = 'textarea_height3';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id",  "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email",  "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url",
			"content_id", "content_title", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;










// Sendmail Invitations
	case 'campaign:invitation_subject' :
		$top_header_text = 'Edit Invitation Subject';
		$top_header_desc_text = 'Modify or customize invitation email subject.';

		$source_code_label = 'Enter Invitation Subject';
		$textarea_css = 'textarea_height1';

		$markups_header = 'Invitation Subject Markups';
		$markups_desc = 'You can use following markups under invitation subject.';

		$signup_block = $attach_note_block = false;

		$show_preview_btn = false;

		$markups = array(
			"website_name", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_userid", "sender_username", 
			"content_id", "content_title", "content_body", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode",
		);
	break;


	case 'campaign:campaign_email_body' :
		$top_header_text = 'Edit Invitation Email Body';
		$top_header_desc_text = 'Modify or customize invitation email message body.';

		$source_code_label = 'Invitation Email Source Code (HTML Supported)';
		$textarea_css = 'textarea_height2';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url",
			"content_id", "content_title", "content_body", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;


	case 'campaign:social_body' :
		$top_header_text = 'Edit Social Network Invitation Body';
		$top_header_desc_text = 'Invitation message body for social network invitations.';

		$source_code_label = 'Social Network Invitation Body (No HTML)';
		$textarea_css = 'textarea_height3';

		$markups_header = 'Invitation Markups';
		$markups_desc = 'You can use following markups under invitation message body.';

		$markups = array(
			"website_name", "website_url", "website_logo", "register_link", "invitation_id", "invitation_assets_url", "receiver_name", "receiver_email", "service", "service_id", "sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", "adiinviter_root_url", "verify_invitation_url",
			"content_id", "content_title", "content_body", "category_id", "content_url", "alias_url_value", 
			"guest_mode", "/guest_mode", "user_mode", "/user_mode", "attach_note_block", "/attach_note_block", "attached_note",
		);
	break;




// Task Fields
	case 'task:subject_editor' :
		$field_options = $task_handler->custom_settings[$setting_name];

		$top_header_text = isset($field_options['name']) ? $field_options['name'] : '';
		$top_header_desc_text = isset($field_options['description']) ? $field_options['description'] : '';

		$source_code_label = isset($field_options['source_code_label']) ? $field_options['source_code_label'] : '';
		$textarea_css = 'textarea_height1';

		$markups_header = isset($field_options['markups_list_header']) ? $field_options['markups_list_header'] : '';
		$markups_desc = isset($field_options['markups_list_description']) ? $field_options['markups_list_description'] : '';

		$signup_block = $attach_note_block = false;

		$show_preview_btn = false;
		$make_right_desc_shorter = true;

		$markups = array(
			"website_name", "receiver_name", "receiver_email", "service", "service_id", 
			"sender_name", "sender_email", "sender_userid", "sender_username", 
			'invitations_count',  'issued_date', 'elapsed_days',
			"guest_mode", "/guest_mode", "user_mode", "/user_mode",
		);
	break;


	case 'task:template_editor' :
		$field_options = $task_handler->custom_settings[$setting_name];

		$top_header_text = isset($field_options['name']) ? $field_options['name'] : '';
		$top_header_desc_text = isset($field_options['description']) ? $field_options['description'] : '';

		$source_code_label = isset($field_options['source_code_label']) ? $field_options['source_code_label'] : '';
		$textarea_css = 'textarea_height2';

		$markups_header = isset($field_options['markups_list_header']) ? $field_options['markups_list_header'] : '';
		$markups_desc = isset($field_options['markups_list_description']) ? $field_options['markups_list_description'] : '';

		$attach_note_block = false;
		$make_right_desc_shorter = true;

		$markups = array(
			"website_name", "website_url", "website_logo", "invitation_id", 
			"invitation_assets_url", "adiinviter_root_url", "verify_invitation_url", "register_link", 
			"receiver_name", "receiver_email", "service", "service_id", 
			"sender_name", "sender_email", "sender_avatar_url", "sender_profile_url", "sender_userid", "sender_username", 
			'invitations_count', 'issued_date', 'elapsed_days',
			"guest_mode", "/guest_mode", "user_mode", "/user_mode",
		);
	break;


	case '':
	break;
}


?>



<div style="padding: 15px;">
	<form method="post" action="" class="settings_list tedit_save_form">
	<div class="ate_ho">
		<input type="button" value="Back" class="btn_org cancel_teditor" style="float:right;margin-top:15px;">
		<label class="opts_head"><?php echo $top_header_text; ?></label><br>
		<label class="opts_note"><?php echo $top_header_desc_text; ?></label>
	</div>

	<table style="width:100%;">
	<tr><td colspan="2" style="height: 15px;"></td></tr>
	<tr>
		<td style="vertical-align:bottom;"><div class="opts_head" style=""><?php echo $source_code_label; ?> :</div></td>
		<td style="vertical-align:middle;width:300px;">
		<div style="float:right;margin-bottom:5px;">
<?php
$options = array(); $default_option = $lang_id;
$default_text = '';
foreach($available_lang_ids as $lang_id => $lang_name)
{
	if(empty($default_option))
	{
		$default_option = $lang_id;
		$default_text   = $lang_name.' ('.$lang_id.')';
	}
	$options[] = array($lang_id => $lang_name.' ('.$lang_id.')');
}
$details = array(
	'input_name'     => 'template_lang',
	'input_class'    => 'teditor_source_lang',
	'default_option' => $default_option,
	'options'        => $options,
	'default_text'   => $default_text,
);
echo adi_get_select_plugin($details, $type = "down", array('type' => 2));
?>
	</div>
	<div class="aclr"></div>
	</td>
	<tr><td colspan="2" style="height: 5px;"></td></tr>
	<tr>
		<td colspan="2">
	<div style="">

		<?php
		foreach($available_lang_ids as $lang_id => $lang_name)
		{
			// $translated_text = $inv_handler->get_translated_context($settings, $setting_name, $lang_id);
			$translated_text = isset($settings[$setting_name.'_'.$lang_id]) ? $settings[$setting_name.'_'.$lang_id] : '';
			echo '<textarea name="adi_templates['.$sett_group_name.']['.$setting_name.']['.$lang_id.']" class="bx ate_txar tedit_source_code '.$textarea_css.' tedit_source_'.$lang_id.'" style="'.($lang_id == 'en' ? '' : 'display:none;').'">'.$translated_text.'</textarea>';
		}
		?>

	</div>
		</td>
	</tr>
	<tr><td colspan="2" style="height: 15px;"></td></tr>
	<tr>
		<td>
<?php if($show_preview_btn) { ?>
<div class="tedit_preview_modes" style="float:left;margin-bottom:5px;">
<?php
$options = array(); $default_option = '';
$default_text = '';
$options = array(
	array('user_mode' => "User Mode"),
	array('guest_mode' => "Guest Mode"),
);
$details = array(
	'input_name'     => 'template_lang',
	'input_class'    => 'teditor_preview_mode',
	'default_option' => 'user_mode',
	'options'        => $options,
	'default_text'   => $default_text,
);
echo adi_get_select_plugin($details, $type = "down", array('type' => 2, 'search_option'  => false));
?>
</div>
			<input type="button" value="Preview Invitation" class="btn_org show_tedit_preview" style="float:left;margin-left: 15px;">
<?php } ?>

		</td>
		<td style="text-align:right;">
			<input type="submit" value="Save Settings" class="btn_grn" id="submit_teditor">
			<input type="button" value="Back" class="btn_org cancel_teditor" style="margin-left: 15px;">
		</td>
	</tr>
	<tr><td colspan="2" style="height: 40px;"></td></tr>
	</table>

	<table style="width:100%;">
	<tr><td colspan="2">
		<div class="ate_ho" style="margin-bottom:40px;margin-right:20px;">
			<label class="opts_head"><?php echo $markups_header; ?></label><br>
			<label class="opts_note"><?php echo $markups_desc; ?></label>
		</div>
	</td></tr>
	<tr>
	<td style="<?php echo ($make_right_desc_shorter) ? '' : 'width:465px;'; ?>vertical-align:top;">
		<table class="markup_table">
		<tr><th>Markup</th><th>Meaning</th></tr>
		<?php
		foreach ($markups as $markup_id)
		{
			$markup_desc = isset($common_markups[$markup_id]) ? $common_markups[$markup_id] : '';
			echo '<tr>
			<td>['.$markup_id.']</td>
			<td>'.$markup_desc.'</td>
			</tr>';
		}
		?>
		</table>
	</td>

	<td style="vertical-align:top;padding-right: 20px;" class="<?php echo ($make_right_desc_shorter) ? 'tedit_desc_shrt' : ''; ?>">


<?php if($signup_block) { ?>
	<div class="tedit_desc_bl">
		<div class="hh1">Sign Up URL</div>
		<div class="pp1 mb5">Make sure that the <span class="pp1 hg2">[invitation_id]</span> markup is present in your sign up url. Without this markup, invitations won't be marked as accepted, visited or unsubscribed. Following are the examples of sign up urls using <span class="pp1 hg2">[invitation_id]</span> markup :</div>
		<ul class="bullets mb4">
			<li><span class="pp2 hg5">http://www.yourdomain.com/register.php?</span><span class="pp2 hg0">invitation_id=</span><span class="pp2 hg2">[invitation_id]</span></li>
			<li><span class="pp2 hg3">[verify_invitation_url]</span><span class="pp2 hg5">adi_do=accept&</span><span class="pp2 hg0">invitation_id=</span><span class="pp2 hg2">[invitation_id]</span></li>
		</ul>
		<div class="pp1 mb5">When using <span class="pp1 hg3">[verify_invitation_url]</span> markup, do not use question mark (<span class="pp1 hg2">?</span>) for specifying query string parameters.</div>
	</div>
<?php } ?>
<?php if($guest_mode_block) { ?>
	<div class="tedit_desc_bl">
		<div class="hh1">Guest Mode</div>
		<div class="pp1">Contents enclosed within <span class="pp1 hg2">[guest_mode][/guest_mode]</span> will be inserted into outgoing invitations only when invite sender is a not-logged in user in your website.</div>
	</div>
<?php } ?>
<?php if($user_mode_block) { ?>
	<div class="tedit_desc_bl">
		<div class="hh1">User Mode</div>
		<div class="pp1">Contents enclosed within <span class="pp1 hg2">[user_mode][/user_mode]</span> will be inserted into outgoing invitations only when invite sender is a logged in user in your website.</div>
	</div>
<?php } ?>
<?php if($attach_note_block) { ?>
	<div class="tedit_desc_bl">
		<div class="hh1">Attach Note Block</div>
		<div class="pp1">Contents enclosed within <span class="pp1 hg2">[attach_note_block][/attach_note_block]</span> will be inserted into outgoing invitations only when attach note is present. Use <span class="pp1 hg0">[attached_note]</span> markup to insert the attached note text entered by invite sender.</div>
	</div>
 <?php } ?>
	</td>
	</tr></table>


	</form>
</div>

<script type="text/javascript">
	adtmpl_editor.list = <?php echo json_encode($available_lang_ids); ?>;
	adtmpl_editor.show_tabs_onexit = <?php echo ($sett_id == 'campaign') ? 'true' : 'false'; ?>;
</script>