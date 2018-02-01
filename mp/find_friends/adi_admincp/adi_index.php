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


$init_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'adi_init.php';
include_once($init_file_path);

if( (!$adiinviter->adiinviter_installed) || $adiinviter->admin_settings['first_login'] == 1)
{
	header('location: adi_install.php');
	exit;
}

$available_lang_ids = $adiinviter->get_installed_lang_ids();

$platform_js = (isset($platform_js) ? $platform_js : '');
$ts = time();

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta charset="UTF-8">
	<link href='adi_css/adiinviter.png' rel='shortcut icon'/>
	<title>Admin Panel</title>

<style type="text/css">
#adi_splash_iframe
{
	display: none;
}
</style>

	<link type="text/css" rel="stylesheet" href="adi_css/index.css" />
	<!-- <link type="text/css" rel="stylesheet" href="adi_css/dashboard.css" /> -->
	<link type="text/css" rel="stylesheet" href="adi_css/zebra_datepicker.css" />
	<link type="text/css" rel="stylesheet" href="adi_css/zebra_datepicker_metallic.css" />
	<link type="text/css" rel="stylesheet" href="adi_css.php?" />
	<link type="text/css" rel="stylesheet" href="adi_css/jquery.mCustomScrollbar.css" />

	<script type="text/javascript" src="adi_js/jquery.min.js"></script>
	<script type="text/javascript">

		var adi_scr_mode = 1024;
		var w = $(window).width();
		if(w >= 1263) {
			adi_scr_mode = 1280;
		}
		else if(w >= 1145) {
			adi_scr_mode = 1152;
		}
		
	</script>
	<script type="text/javascript" src="adi_js/drogdrop.js"></script>
	<script type="text/javascript" src="adi_js/adiinviter.js"></script>
	<script type="text/javascript" src="adi_js/plugins.js"></script>
	<script type="text/javascript" src="adi_js/charts.js"></script>
	<script type="text/javascript" src="adi_js/adi_charts.js"></script>
	<script type="text/javascript" src="adi_js/zebra_datepicker.src.js"></script>
	<script type="text/javascript" src="adi_js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script type="text/javascript" src="adi_js/jquery.tinyscrollbar.min.js"></script>
	<script type="text/javascript">
		adi.frm_elements_ob = <?php echo json_encode($adiinviter->form_hidden_elements); ?>;
		adi.frm_elements = '<?php echo http_build_query($adiinviter->form_hidden_elements, '', '&'); ?>';
		<?php echo $platform_js; ?>
	</script>
	<!--[if IE]>
	<script language="javascript" type="text/javascript" src="adi_js/canvas.js"></script>
	<![endif]-->
</head>
<body class="admin_body" style="overflow-y: scroll;">
<div class="adi_drag_carrier"></div>

<iframe src="" frameborder="0" id="adi_splash_iframe" class=""></iframe>



<div class="top_notif">
	<div class="adi_notif adi_notif_success"> 
		<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
			<p class="adi_notif_success_txt">Settings updated succesfully.</p>
		</td>
		<td>
			<div class="adi_notif_close"></div>
		</td></tr></table>
	</div>
	<div class="adi_notif adi_notif_failure">
		<table cellpadding="0" cellspacing="0" width="100%"><tr><td>
			<p class="adi_notif_failure_txt">Something went wrong.</p>
		</td>
		<td>
			<div class="adi_notif_close"></div>
		</td></tr></table>
	</div>
</div>


<iframe src="" frameborder="0" name="back_channel" id="back_channel" style="border:none;width:0px;height:0px;display:none;"></iframe>
<div class="adi_preview_test_size" style="display:none;"></div>


<div class="adi_graph_tt">
	<div class="tt_arrow"></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
		<td rowspan="2"><div class="tt_title"></div></td>
		<td><div class="tt_val tt_total_val">0</div></td>
		<td><div class="tt_val tt_joined_val">0</div></td>
		<td><div class="tt_val tt_unsubscribed_val">0</div></td>
	</tr>
	<tr>
		<td><div class="tt_col tt_total_lbl">Invitations</div></td>
		<td><div class="tt_col tt_joined_lbl">Joined</div></td>
		<td><div class="tt_col tt_unsubscribed_lbl">Unsubscribed</div></td>
	</tr>
	</table>
</div>



<div class="overlay mask" id="modal_mask"></div>

<div class="overlay" id="modal_message">
	<table style="width:100%;height:100%;">
		<tr>
			<td style="text-align:center;vertical-align:middle;">
				<center>
					<div class="container" style="width:300px;">
						<span id="modal_msg_txt">Processing request..</span><br>
						<img src="adi_css/images/loading.gif" style="margin-top: 10px;">
					</div>
				</center>
			</td>
		</tr>
	</table>
</div>


<?php 

$adiinviter->requireSettingsList(array('global','db_info'));
$adiinviter->init_user();

$sender_name = ($adiinviter->userid == 0) ? $adiinviter->settings['adiinviter_sender_name'] : $adiinviter->userfullname;

$bbcodes_json = array(
	// 'sender_name'        => $sender_name,
	'invitation_assets_url' => $adiinviter->adi_root_url.'/adi_invitations',
	'sender_avatar_url'     => $adiinviter->default_no_avatar,
	'sender_profile_url'    => $adiinviter->settings['adiinviter_profile_page_url'],
	'website_logo'          => $adiinviter->settings['adiinviter_website_logo'],
	'my_website_logo'       => $adiinviter->settings['adiinviter_website_logo'],
	'website_name'          => $adiinviter->settings['adiinviter_website_name'],
);

?>
<script type="text/javascript">
adtmpl_editor.bbcodes = <?php echo json_encode($bbcodes_json); ?>;
</script>




<!-- Popup Start : Create/Import New Language -->
<div class="overlay" id="new_language">
	<table style="width:100%;height:100%;">
	<tr>
		<td style="text-align:center;vertical-align:middle;">
			<center>

	<table cellpadding="0" cellspacing="0">
	<tr><td valign="top">
	<div style="position: relative;height: 44px;"><div class="section_header_cont_out"><div class="section_header_cont">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td valign="top" class="sect_head_opt sect_head_opt_checked sect_head_default" data="sect_create_lang">
					<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
						<div class="sect_head_opt_txt">Create Language Pack</div>
					</div>
				</td>
				<td valign="top" class="sect_head_opt sect_head_opt_unchecked" data="sect_import_lang" style="width:50%;">
					<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
						<div class="sect_head_opt_txt">Import Language Pack</div>
					</div>
				</td>
			</tr>
		</table>
	</div></div></div>
	</td></tr>
	<tr><td>

	<div class="adi_preview_out">

		<!-- Import Language Section -->
		<div class="sect_out_div sect_import_lang" style="min-height:;">
			<form method="post" action="adi_lang.php" target="back_channel" enctype="multipart/form-data" class="import_lang_form">
			<div style="margin:10px;width:910px;">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">

					<tr class="first">
						<td class="label_box" colspan="2">
							<span class="opts_head" style="font-size:15px;">Import Language pack</span><br>
						</td>
					</tr>

					<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

					<tr>
						<td class="label_box">
							<span class="opts_head">Lanaguage Pack</span><br>
							<label class="opts_note">Choose language pack .xml file.</label>
						</td>
						<td>
							<input type="file" name="lang_xml" class="file_input adi_lang_xml_inp">
						</td>
					</tr>

					<tr>
						<td class="label_box">
							<span class="opts_head">Allow Overwrite</span><br>
							<label class="opts_note">Choose Yes to overwrite existing phrases.</label>
						</td>
						<td>
							<?php
							$val = $adiinviter->settings['adiinviter_onoff'];
							if($val == '1') {
								$isOn  = 'selected';
								$isOff = '';
							}
							else {
								$isOn  = '';
								$isOff = 'selected';	
							}
							?>
							<p class="switch">
								<label class="adi_switch_on cb-enable" data="1"><span>Yes</span></label>
								<label class="adi_switch_off cb-disable selected" data="0"><span>No</span></label>
								<input type="hidden" name="allow_overwrite" value="0" class="switch_val">
							</p>
						</td>
					</tr>
					<tr>
						<td style="height:41px;"></td>
					</tr>
				</table>
				<hr class="bef_submit">
				<div class="cont_submit">
					<span class="import_lang_form_response"></span>
					<input type="button" value="Cancel" class="btn_org adi_hide_new_lang">
					<input type="submit" value="Import" class="btn_grn adi_btn_space_left" id="adi_save_settings">
				</div>
			</div>
			<input type="hidden" name="adi_do" value="import_language">
			</form>
		</div>

		<!-- Create Language Section -->
		<div class="sect_out_div sect_create_lang" style="min-height:;margin-top:25px;">
			<form method="post" action="" class="settings_list adi_new_lang_form">
			<div style="margin:10px;width:910px;">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">

					<tr class="first">
						<td class="label_box" colspan="2">
							<span class="opts_head" style="font-size:15px;">Create New Language</span>
							<a href="http://www.adiinviter.com/docs/languages#create-new-language" class="adi_docs_link" target="_blank" style="font-weight: bold;">Reference Documentation</a>
						</td>
					</tr>

					<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

					<tr>
						<td class="label_box">
							<span class="opts_head">Lanaguage</span><br>
							<label class="opts_note">Choose language name.</label>
						</td>
						<td>
					<?php
					$adiinviter->loadCache('language');
					$all_languages = $adiinviter->cache['language'];
					$installed_ids = $adiinviter->get_installed_lang_ids();
					if(count($all_languages) > 0)
					{
						$size='';
						if(count($all_languages) > 5) {
							$size = ' size="10"';
						}
						echo '<select name="add_new_lang_form[lang_id]" class="opts adi_new_lang_name" '.$size.' style="width: 200px;">';
						foreach($all_languages as $id => $lang_name)
						{
							if(!isset($installed_ids[$id]))
							{
								echo '<option value="'.$id.'">'.$lang_name.'</option>';
							}
						}
					}
					?>
						</td>
					</tr>
				</table>
				<hr class="bef_submit">
				<div class="cont_submit">
					<span class="new_lang_form_response"></span>
					<input type="button" value="Cancel" class="btn_org adi_hide_new_lang">
					<input type="submit" value="Create" class="btn_grn adi_btn_space_left" id="adi_save_settings">
				</div>
			</div>
			</form>
		</div>

		
	</div>
</td></tr>
</table>
		</center>
<script type="text/javascript">
$(document).ready(function(){
	$('.adi_hide_new_lang').click(function(){
		new_lang.hide();
	});
	$('.import_lang_form').submit(function(){
		if($('.adi_lang_xml_inp').val() == '')
		{
			$('.import_lang_form_response').html('<font color="red"><i>No langauge xml choosen.</i></font>');
			return false;
		}
	});
});

</script>
		</td>
	</tr>
	</table>
</div>
<!-- Popup End : Create/Import New Language -->



<!-- Popup Start : reset_password_popup -->
<div class="overlay" id="reset_password_popup">
<table style="width:100%;height:100%;">
	<tr>
		<td style="text-align:center;vertical-align:middle;">
			<center>
	<div class="container new_phrase_form_outer" style="width: 550px;">
		<form id="reset_password_form">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box" colspan="2">
						<span class="opts_head">Reset Password</span><br>
					</td>
				</tr>

				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Current Password</span>
					</td>
					<td align="left">
						<input type="password" class="txinput reg rp_cur_password" name="reset_password[current_password]" value="" style="width: 230px;">
						</select>
					</td>
				</tr>
				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">New Password</span>
					</td>
					<td>
						<input type="password" class="txinput reg rp_new_password" name="reset_password[new_password]" value="" style="width: 230px;">
					</td>
				</tr>
				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Confirm New Password</span>
					</td>
					<td>
						<input type="password" class="txinput reg rp_confirm_password" name="reset_password[confirm_new_password]" value="" style="width: 230px;">
					</td>
				</tr>

				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

			</table>
			<div style="padding: 10px 10px;">
				<input style="float: right;" type="button" value="Cancel" class="btn_grn" id="reset_password_form_cancel">
				<input style="float: right;margin-right: 15px;" type="submit" value="Save" class="btn_grn">
				<div class="reset_password_processing" style="text-align:left;"></div>
				<div style="clear:both;"></div>
				<div class="clr"></div>
			</div>
		</form>
		</div>
			</center>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#reset_password_form_cancel').click(function(){ adi_reset.hide(); });
	$('#reset_password_form').submit(function(){ 
		adi_reset.submit_form(this); 
		return false;
	});
});
</script>
<!-- Popup End : reset_password_popup -->





<!-- Template Editor : Preview Popup -->
<div class="overlay" id="tedit_show_preview"><table style="width:100%;height:100%;"><tr><td style="text-align:center;vertical-align:middle;"><center><table><tr><td>

<div class="adi_preview_out">
	<table width="100%"><tr>
		<td colspan="3">
			<label class="opts_head">Invitation Preview</label>
		</td>
	</tr>
	<tr><td colspan="3" class="hr_sep_td"><hr class="sep" style="margin: 15px 0px;"></td></tr>
	<tr>
	<td>
		<div class="" style="min-height:480px;">
			<center>
				<table cellpadding="0" cellspacing="0"><tr><td>
				<div class="inv_preview_iframe_out">
					<iframe id="tedit_preview_iframe" class="tmpl_cd_iframe" style="min-width:500px; min-height:150px;"></iframe>
				</div>
				</td></tr></table>
			</center>
		</div>
	</td>
	</tr>
	<tr><td colspan="3" class="hr_sep_td"><hr class="sep" style="margin: 15px 0px;"></td></tr>
	<tr>
		<td colspan="3">
			<input type="button" value="OK" class="btn_org tedit_close_preview" style="float:right;">
			<div class="aclr"></div>
		</td>
	</tr>
	</table>
</div>

</td></tr></table></center></td></tr></table></div>
<script type="text/javascript">
$('.tedit_close_preview').click(function(){
adtmpl_editor.hide_preview();
});
</script>





<!-- Template Editor : Create new Phrase -->
<div class="overlay" id="modal_new_phrase">
<table style="width:100%;height:100%;">
	<tr>
		<td style="text-align:center;vertical-align:middle;">
			<center>
	<div class="container new_phrase_form_outer" style="width: 900px;">
		<form id="new_phrase_form">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box" colspan="2">
						<span class="opts_head">Add New Phrase<a href="http://www.adiinviter.com/docs/languages#create-new-phrase" class="adi_docs_link" target="_blank">Reference Documentation</a></span><br>
					</td>
				</tr>

				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Phrase Varname</span><br>
						<label class="opts_note">Enter unique variable name for this phrase.<br><span class="label_red_note">Note:</span> White spaces and special characters are not allowed.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg adi_limited_chars_only new_phrase_varname" name="new_phrase_form[phrase_varname]" value="" onlychars="mixtext" style="width: 392px;" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<?php
				// $all_themes_list = $adiinviter->get_themes_list();
				$installed_themes_list = $adiinviter->settings['adiinviter_themes_list']; 
				if(count($installed_themes_list) > 1)
				{
					?><tr>
						<td class="label_box">
							<span class="opts_head">Theme</span><br>
							<label class="opts_note">Select <span class="hglt1">All Themes</span> option to create global phrases.<br>
							<span class="label_red_note">Note:</span> Phrase will be removed if associated theme is uninstalled.</label>
						</td>
						<td>
<div class="adi_themes_list_outer">
<?php

$default_themeid = $adiinviter->default_themeid;
$options = array();
foreach($installed_themes_list as $theme_id => $details)
{
	if($theme_id == $adiinviter->default_themeid)
	{
		$options[] = array($theme_id => array(3, 'All Themes'));
	}
	else
	{
		$options[] = array($theme_id => (!empty($details['name'])?$details['name']:$theme_id));
	}
}
$details = array(
	'input_name'     => 'new_phrase_form[theme_id]',
	'input_class'    => 'new_phrase_theme_id',
	'default_option' => $default_themeid,
	'options'        => $options,
	'default_text'   => 'Select Theme',
);
echo adi_get_select_plugin($details, $type = "down");

?>
</div>
						</td>
					</tr>
					<?php
				}
				else
				{
					echo '<input type="hidden" name="new_phrase_form[theme_id]" value="'.$adiinviter->default_themeid.'">';
				}
				?>
				<tr>
					<td class="label_box">
						<span class="opts_head">Phrase Text</span><br>
						<label class="opts_note">Enter phrase text.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg new_phrase_text" name="new_phrase_form[phrase_text]" value="" style="width: 392px;" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>

			</table>
			<div style="padding: 10px 10px;">
				<input style="float: right;" type="submit" value="Save" class="btn_grn adi_btn_space_left">
				<input style="float: right;" type="button" value="Cancel" class="btn_org" id="new_phrase_form_cancel">
				<div class="new_phrase_form_response"></div>
				<div style="clear:both;"></div>
				<div class="clr"></div>
			</div>
		</form>
		</div>
			</center>
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">

$(document).ready(function(){
	// New Phrase form
	$('#new_phrase_form_cancel').click(function(){
		adi.hideNewPhraseForm();
	});
	$('#new_phrase_form').submit(function(){
		adi.submitNewPhraseForm(this);
		return false;
	});
});
</script>




<div class="adi_top_header_outer">
<center>
	<div class="adi_top_header">
		<table cellpadding="0" cellspacing="0" width="100%" class="adi_top_header_tble">
			<tr>
				<td style="width: 350px;" align="left">
					<div class="logo" style="margin-left: 40px;">
						<img src="adi_css/adiinviter_pro_logo.png" style="margin-top: 5px;">
					</div>
				</td>
				<td valign="middle" align="center">
					<div class="adi_top_bar_updates"><!-- 0 new updates. --></span>
				</td>
				<td align="right" class="admin-opts" style="padding-right: 8px;">
					<ul class="adi_top_bar_ul">
						<?php
						if(ADI_USE_CUSTOM_LOGIN)
						{
						?>
						<li class="adi_top_bar_li adi_top_bar_first"><a href="adi_login.php?do=logout">Log out</a></li>
						<li class="adi_top_bar_sep">|</li>
						<li class="adi_top_bar_li adi_top_bar_last"><a href="" onclick="return adi_reset.show();">Reset Password</a></li>
						<?php 
						}
						else
						{  
							$platform = 'Back To '.ucwords($adiinviter->current_platform).' Admincp';
							?>
						<li class="adi_top_bar_li adi_top_bar_first"><a href="<?php echo $adiinviter->set_platform_admin_url(); ?>"><?php echo $platform; ?></a></li>
						
						<?php 
						} ?>
					</ul>
				</td>
			</tr>
		</table>
	</div>
</center>
</div>





<center>
	<div class="body_table_outer" style="padding-top:20px;">


<?php

	
	// Remove Installation file notice
	$adi_installer_files = array();
	$addons_dir = dirname(__FILE__).ADI_DS.'adi_install';
	if(is_dir($addons_dir))
	{
		$adi_installer_files[] = $addons_dir;
	}
	if(count($adi_installer_files) > 0)
	{
		echo '<div class="adi_sticky_notif adi_notif_info" style="display:block;">
			<p class="adi_notif_info_txt"><b>Remove AdiInviter Pro installation directory ('.DIRECTORY_SEPARATOR.'adi_install) from your FTP : </b> ';
		foreach($adi_installer_files as $fname) {
			echo ' '.$fname." <br>\n";
		}
		echo '</div>';
	}


	// Email Queue Notice
	$mails_count = $adiinviter->getMailQueueCount();
	if($mails_count > 0)
	{
		$plugin_on_off = adi_getSetting('Adi_Plugin_Sendmail', 'plugin_on_off');
		if($plugin_on_off !== false && $plugin_on_off+0 !== 1)
		{
			echo '<div class="adi_sticky_notif adi_notif_info adi_notice_sendmail_cron" style="display:block;">
			<p class="adi_notif_info_txt"><b>You have <span class="adi_notif_info_txt adint_sendmail_cron_count" style="font-size:15px;">'.$mails_count.'</span> mails waiting in the mail queue. Either turn ON Scheduled Dispatch task or execute "Cron Based Or Scheduled Dispatch" task from the "Tasks" Tab.</b></div>';
		}
	}

?>



<div class="ad_admin_body">
<table cellpadding="0" cellspacing="0" class="body_table">
<tr>
	<td valign="top" class="left_menu_outer">
	<div class="left_menu_ul">
		<div class="left_menu_li left_menu_current" data="dashboard">
			<div class="lm_itm_img lm_itm_dashboard"></div>
		</div>
		<div class="left_menu_li" data="global"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_global"></div>
		</div>
		<div class="left_menu_li" data="db_info"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_database"></div>
		</div>
		<div class="left_menu_li" data="services"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_manage"></div>
		</div>
		<div class="left_menu_li" data="invitation"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_invitation"></div>
		</div>
		<div class="left_menu_li" data="campaign"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_content"></div>
		</div>
		<div class="left_menu_li" data="plugins"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_plugins"></div>
		</div>
		<div class="left_menu_li" data="permissions"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_user"></div>
		</div>
		<div class="left_menu_li" data="language"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_language"></div>
		</div>
		<div class="left_menu_li" data="themes"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_themes"></div>
		</div>
		<div class="left_menu_li" data="updates"><div class="left_menu_li_sep"></div>
			<div class="lm_itm_img lm_itm_updates"></div>
		</div>
	</div>



</td>
<td valign="top" class="adi_settings_outer">

<!-- Settings Tabs -->
<div class="section_header section_header_settings"><div class="section_header_cont_out"><div class="section_header_cont">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_checked sect_head_default" data="sect_system_settings">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
					<div class="sect_head_opt_txt">System Settings</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_website_details">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_middle">
					<div class="sect_head_opt_txt">Website Details</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_website_embedding">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
					<div class="sect_head_opt_txt">AdiInviter Pro URLs</div>
				</div>
			</td>
		</tr>
	</table>
</div></div></div>


<!-- Database details tab -->
<div class="section_header section_header_integration"><div class="section_header_cont_out"><div class="section_header_cont">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_checked sect_head_default" data="sect_users_integration">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
					<div class="sect_head_opt_txt">User System</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_usergroups_integration">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_middle">
					<div class="sect_head_opt_txt">Usergroup System</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_friends_integration">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
					<div class="sect_head_opt_txt">Friends/Followers System</div>
				</div>
			</td>
		</tr>
	</table>
</div></div></div>


<!-- Services Tabs -->
<div class="section_header section_header_services"><div class="section_header_cont_out"><div class="section_header_cont">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_checked sect_head_default" data="sect_manage_services">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
					<div class="sect_head_opt_txt">Manage Services</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_oauth_services">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
					<div class="sect_head_opt_txt">OAuth Settings</div>
				</div>
			</td>
		</tr>
	</table>
</div></div></div>


<!-- Services Tabs -->
<div class="section_header section_header_campaigns"><div class="section_header_cont_out"><div class="section_header_cont">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_checked sect_head_default" data="sect_campaign_settings">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
					<div class="sect_head_opt_txt">General Settings</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_campaign_database_config">
			<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
					<div class="sect_head_opt_txt">Database Integration</div>
				</div>
			</td>
		</tr>
	</table>
</div></div></div>


<!-- Language Tabs -->
<div class="section_header section_header_language"><div class="section_header_cont_out"><div class="section_header_cont">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_checked sect_head_default" data="sect_language_manager">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_first">
					<div class="sect_head_opt_txt">Language Manager</div>
				</div>
			</td>
			<td valign="top" class="sect_head_opt sect_db_sz sect_head_opt_unchecked" data="sect_search_in_phrases">
				<div class="sect_head_opt_txt_out sect_head_opt_txt_last">
					<div class="sect_head_opt_txt">Search in Phrases</div>
				</div>
			</td>
		</tr>
	</table>
</div></div></div>


<!-- Template Body & Template Subject Editor -->
<div class="adi_settings ad_teditor" style="display:none;">
</div>


<div class="adi_settings adi_settings_root">

	<div id="settings_panel">
<?php
	$get_code = 'dashboard';
	include(ADI_ADMIN_PATH.'adi_get.php');
?>
<script type="text/javascript">
	adi.currentSettings = 'dashboard';
</script>

	</div>
</div>

</td>
</tr>



<tr>
	<td></td>
	<td style="padding-top:10px;color:#686868;">
		<center>
<?php
$adiinviter->requireSettingsList('updates');
$build_id = isset($adiinviter->settings['adi_package_build_id']) ? $adiinviter->settings['adi_package_build_id'] : 2000;
$build_id = max($build_id+0, 2000);
$num = floor($build_id / 100);
$num = number_format($num/10, 1);
?>
			<div style="">Powered By <a href="http://www.adiinviter.com" target="_blank" class="pp1 lnk2">AdiInviter Pro v<?php echo $num; ?></a></div>
			<div style="">Build <?php echo $build_id; ?></div>
		</center>
	</td>
</tr>
</table>
</div>




</div>
</center>

</body>
</html>
