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


$get_code = isset($get_code) ? $get_code : AdiInviterPro::POST('gname', ADI_STRING_VARS);

if(!AdiInviterPro::isPOST('gname') && empty($get_code))
{
	echo 'AdiInviter Error: Settings group name not specified.';
}
else 
{
	switch($get_code)
	{
		case 'dashboard':
			include($admin_path.ADI_DS.'adi_dashboard.php');
			$adiinviter->requireSettingsList('global','db_info');
			$adiinviter->init_user();
		break;

		case 'oauth' :
		break;

		case 'services':
			?>
			<div class="sect_out_div sect_manage_services">
			<?php include($admin_path.ADI_DS.'adi_services.php'); ?>
			</div>
			<div class="sect_out_div sect_oauth_services">
			<?php $adiinviter->requireSettingsList(array('db_info','oauth'));
			include($admin_path.ADI_DS.'adi_oauth_applications.php'); ?>
			</div>
			<?php
		break;

		case 'plugins':
			include($admin_path.ADI_DS.'adi_plugins.php');
		break;

		case 'invitation':
			$adiinviter->requireSettingsList(array('global','invitation'));
			?>
		<form method="post" action="" class="settings_list">
		<div style="margin:20px 10px;">

		<div style="margin:20px 10px 20px 10px;">

		<div style="margin:0px 0px 45px 10px;">
			<div class="ate_ho" style="margin-bottom:10px;">
				<div class="opts_head">Free Invitation Email Templates<a href="http://www.adiinviter.com/docs/references-invitations" class="adi_docs_link" target="_blank" style="font-weight: bold;">Invitations Reference Documents</a></div>
			</div>
			<div class="pp1">You can download ready-made invitation email templates from our official email templates directory. All you have to do is add your message, website logo and brand colors.</div>
			<br>
			<a href="http://www.adiinviter.com/addons/invitation-templates" target="_blank" class="pp1 lnk1">Download Invitation Templates</a>
		</div>

		<!-- Invitation Subject -->
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Invitation Subject</div>
			<div class="adi_inner_sect_body">

				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td>
						<span class="opts_head">Invitation Subject</span><br>
						<label class="opts_note">Invitation subject for all outgoing email and social networks invitations.</label>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:5px;">
						<div class="template_preview">
							<?php
							$invitation_subject = isset($adiinviter->settings['invitation_subject_en']) ? $adiinviter->settings['invitation_subject_en'] : '';
							echo '<div class="template_code_en" data="template_code_iframe3" style="display:none;"><!-- '.$invitation_subject.' --></div>';
							?>
							<iframe id="template_code_iframe3" class="tmpl_cd_iframe" width="100%" style="min-width:600px;max-height:50px;"></iframe>
						</div>
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="button" value="Edit" data="tmpl_cd_invitation_subject" data-sg="invitation" data-sn="invitation_subject" data-sid="invitation" data-cupdate="template_code_iframe3" class="btn_org tmpl_open_teditor">
		</div>

		<!-- Email Invitation Body -->
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Invitation Email</div>
			<div class="adi_inner_sect_body">

				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Invitation Message Body</span><br>
						<label class="opts_note">Modify or customize invitation email message body (HTML supported).</label>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:5px;">
						<div class="template_preview">
							<?php
							$invitation_body = isset($adiinviter->settings['invitation_body_en']) ? $adiinviter->settings['invitation_body_en'] : '';
							echo '<div class="template_code_en" data="template_code_iframe2" style="display:none;"><!-- '.$invitation_body.' --></div>';
							?>
							<iframe id="template_code_iframe2" class="tmpl_cd_iframe" width="100%"></iframe>
						</div>
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<!-- <input type="button" value="Edit" data="tmpl_cd_invitation_body" class="btn_org tmpl_open_editor"> -->
			<input type="button" value="Edit" data="tmpl_cd_invitation_body" data-sg="invitation" data-sn="invitation_body" data-sid="invitation" data-cupdate="template_code_iframe2" class="btn_org tmpl_open_teditor">
		</div>


<?php
$adi_services = adi_allocate_pack('Adi_Services');
$adi_services->get_service_details('all');
if($adi_services->social_importer_service_count > 0)
{
?>

		<!-- Social Invitation Body -->
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Social Network Invitations</div>
			<div class="adi_inner_sect_body">

				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Invitation Message Body For Social Networks</span><br>
						<label class="opts_note">Invitation body for outgoing social network invitations (No HTML).</label>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:5px;">
						<div class="template_preview">
							<?php
							$invitation_social_body = isset($adiinviter->settings['invitation_social_body_en']) ? $adiinviter->settings['invitation_social_body_en'] : '';
							echo '<div class="template_code_en" data="template_code_iframe1" style="display:none;"><!-- '.$invitation_social_body.' --></div>';
							?>
							<iframe id="template_code_iframe1" class="tmpl_cd_iframe" width="100%"></iframe>
						</div>
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="button" value="Edit" data="tmpl_cd_invitation_body" data-sg="invitation" data-sn="invitation_social_body" data-sid="invitation" data-cupdate="template_code_iframe1" class="btn_org tmpl_open_teditor">
		</div>

<?php } ?>


		<!-- Twitter Invitation Body -->
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Twitter Invitation Body</div>
			<div class="adi_inner_sect_body">

				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Invitation Message Body For Twitter</span><br>
						<label class="opts_note">Modify or customize invitation message body for Twitter (HTML NOT supported).</label>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom:5px;">
						<div class="template_preview">
							<?php
							$invitation_social_body_twitter = isset($adiinviter->settings['invitation_social_body_twitter_en']) ? $adiinviter->settings['invitation_social_body_twitter_en'] : '';
							echo '<div class="template_code_en" data="template_code_iframe1" style="display:none;"><!-- '.$invitation_social_body_twitter.' --></div>';
							?>
							<iframe id="template_code_iframe1" class="tmpl_cd_iframe" width="100%"></iframe>
						</div>
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="button" value="Edit" data="tmpl_cd_invitation_social_body_twitter" data-sg="invitation" data-sn="invitation_social_body_twitter" data-sid="invitation" data-cupdate="template_code_iframe1" class="btn_org tmpl_open_teditor">
		</div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Attach Note</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Attach Note To Invitations</span><br>
						<label class="opts_note">Allow users to attach a custom note to all outgoing invitations.</label>
					</td>
					<td class="value_box">
						<?php
						$val = $adiinviter->settings['invitation_attachment'];
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
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[invitation][invitation_attachment]" value="<?php echo $val; ?>" class="switch_val">
						</p>
						<div class="clr"></div>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Attach Note Character Limit</span><br>
						<label class="opts_note">Specify character limit for attach note.</label><br>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[invitation][attach_note_length_limit]" value="<?php echo $adiinviter->settings['attach_note_length_limit']; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="submit" value="Save Settings" class="btn_grn" id="adi_save_settings">
		</div>

		</div>
		</form>
		<?php
		break;

		case 'campaign':
			include(ADI_ADMIN_PATH . 'adi_campaign.php');
		break;

		case 'global' :
		$adiinviter->requireSettingsList(array('global','db_info'));
?>


	<div style="margin:10px;">
		<div class="sect_out_div sect_system_settings">

		<form method="post" action="" class="settings_list">

		<div style="margin: 20px 0px 0px 0px;">
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">AdiInviter Pro System Settings</div>
			<div class="adi_inner_sect_body">

			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">AdiInviter Pro System On/Off</span><br>
						<label class="opts_note">Globally turn AdiInviter Pro system On/Off.</label>
					</td>
					<td class="value_box">
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
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[global][adiinviter_onoff]" value="<?php echo $val; ?>" class="switch_val">
						</p>
					</td>
				</tr>
				
				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Default Server Path For Storing Cookies</span><br>
						<label class="opts_note">Enter path for storing cookies.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_cookie_path]" value="<?php echo $adiinviter->settings['adiinviter_cookie_path']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

			</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header" style="position: relative;">Contacts Importer Settings</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Maximum Number Of Contacts To Be Imported</span><br>
						<label class="opts_note">Enter maximum number of contacts to be imported.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][max_contacts_count]" value="<?php echo $adiinviter->settings['max_contacts_count']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Contacts File Size Limit</span><br>
						<label class="opts_note">Specify contacts file size limit in KBs.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][contact_file_size_limit]" value="<?php echo $adiinviter->settings['contact_file_size_limit']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Contacts List Limit</span><br>
						<label class="opts_note">Specify characters limit for manually entered contacts.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][contacts_list_length_limit]" value="<?php echo $adiinviter->settings['contacts_list_length_limit']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Email Sender Information</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">From Name</span><br>
						<label class="opts_note">Enter default "from name" for all outgoing emails.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_sender_name]" value="<?php echo $adiinviter->settings['adiinviter_sender_name']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">From Email Address</span><br>
						<label class="opts_note">Enter default "from email address" for all outgoing emails.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_email_address]" value="<?php echo $adiinviter->settings['adiinviter_email_address']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Additional Features</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Show Already Registered Contacts</span><br>
						<label class="opts_note">Show registered users in your website from imported contacts.</label>
					</td>
					<td class="value_box">
						<?php
						if ($adiinviter->db_allowed === false) {
							echo '<font color="red"><i>Database required.</i></font>';
						}
						else if ($adiinviter->user_system === false) {
							echo '<font color="red"><i>User system integration is required.</i></font>';
						}
						else
						{
							$val = $adiinviter->settings['adiinviter_show_already_registered'];
							if($val == '1') {
								$isOn  = 'selected'; $isOff = '';
							}
							else {
								$isOn  = ''; $isOff = 'selected';
							}
							?>
							<p class="switch">
								<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
								<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
								<input type="hidden" name="subsettings[global][adiinviter_show_already_registered]" value="<?php echo $val; ?>" class="switch_val">
							</p>
						<?php 
						}
						 ?>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Ask Not-Logged In User Details</span><br>
						<label class="opts_note">Ask not-logged in users to enter their details before sending invitations.</label>
					</td>
					<td class="value_box">
						<?php
						$val = $adiinviter->settings['adiinviter_store_guest_user_info'];
						if($val == '1') {
							$isOn = 'selected'; $isOff = '';
						}
						else {
							$isOn = ''; $isOff = 'selected';
						}
						?>
						<p class="switch">
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[global][adiinviter_store_guest_user_info]" value="<?php echo $val; ?>" class="switch_val">
						</p>
					</td>
				</tr>


				<tr>
					<td class="label_box">
						<span class="opts_head">Allow Users To Invite Already Invited Contacts</span><br>
						<label class="opts_note">Invite already invited contacts which are not signed up or unsubscribed yet.</label>
					</td>
					<td class="value_box">
						<?php
						$val = $adiinviter->settings['adiinviter_invite_already_invited'];
						if($val == '1') {
							$isOn = 'selected'; $isOff = '';
						}
						else {
							$isOn = ''; $isOff = 'selected';
						}
						?>
						<p class="switch">
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[global][adiinviter_invite_already_invited]" value="<?php echo $val; ?>" class="switch_val">
						</p>
					</td>
				</tr>

<?php 

if(isset($adiinviter->settings['adiinviter_invite_only_registrations']) && $adiinviter->user_registration_system == true) 
{
	?>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Invite Only Registration</span><br>
						<label class="opts_note">New registrations in your website are available from only AdiInviter Pro invitations.</label>
					</td>
					<td class="value_box">
						<?php
						if ($adiinviter->db_allowed === true)
						{
						$val = $adiinviter->settings['adiinviter_invite_only_registrations'];
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
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[global][adiinviter_invite_only_registrations]" value="<?php echo $val; ?>" class="switch_val">
						</p>
						<?php 
						}
						else {
							echo '<font color="red"><i>Database required.</i></font>';
						} ?>
					</td>
				</tr>
<?php } ?>


<?php 
if(isset($adiinviter->settings['adiinviter_force_receivers_email']) && $adiinviter->user_registration_system == true)
{
	?>
				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Force User To Sign Up With Email Address Which Was Invited.</span><br>
						<label class="opts_note">It is recommended to turn this setting OFF.</label>
					</td>
					<td class="value_box">
						<?php
						if ($adiinviter->db_allowed === true)
						{
						$val = $adiinviter->settings['adiinviter_force_receivers_email'];
						if($val == '1') {
							$isOn  = 'selected'; $isOff = '';
						}
						else {
							$isOn  = ''; $isOff = 'selected';
						}
						?>
						<p class="switch">
							<label class="adi_switch_on cb-enable <?php echo $isOn; ?>" data="1"><span>On</span></label>
							<label class="adi_switch_off cb-disable <?php echo $isOff; ?>" data="0"><span>Off</span></label>
							<input type="hidden" name="subsettings[global][adiinviter_force_receivers_email]" value="<?php echo $val; ?>" class="switch_val">
						</p>
						<?php 
						}
						else {
							echo '<font color="red"><i>Database required.</i></font>';
						} ?>
					</td>
				</tr>
<?php } ?>

				</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header" style="position: relative;">
			reCAPTCHA v2.0
			<div style="right: 10px; position: absolute; top: 3px;"><a href="https://www.google.com/recaptcha/admin" class="adi_link" target="_blank"><span class="label_red_note">reCAPTCHA Signup</span></a></div>
			</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td colspan="2" style="padding-bottom: 0;">
						<label class="opts_note"><span class="label_red_note">Note:</span> If you don't want to use reCAPTCHA, leave these settings blank.</label>
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Site Key</span><br>
						<label class="opts_note">Enter site key obtained from reCAPTCHA.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][captcha_public_key]" value="<?php echo $adiinviter->settings['captcha_public_key']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Secret Key</span><br>
						<label class="opts_note">Enter secret key obtained from reCAPTCHA.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][captcha_private_key]" value="<?php echo $adiinviter->settings['captcha_private_key']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</div></div>

		<!-- <hr class="bef_submit"> -->
		<div class="cont_submit" style="padding-right:8px;">
			<input type="submit" value="Save Settings" class="btn_grn" id="adi_save_settings">
		</div>
		</form>
	</div>
	</div>



	<div class="sect_out_div sect_website_details">
		<form method="post" action="" class="settings_list">
		<div style="margin: 20px 0px 0px 0px;">
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Website Information</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Website Name</span><br>
						<label class="opts_note">Enter your website name.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_website_name]" value="<?php echo $adiinviter->settings['adiinviter_website_name']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Website Logo (100x100 Max Size)</span><br>
						<label class="opts_note">For e.g. http://www.yourdomain.com/logo.png</label><br>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_website_logo]" value="<?php echo $adiinviter->settings['adiinviter_website_logo']; ?>" spellcheck="false" autocomplete="off">
						<?php 
						if($adiinviter->settings['adiinviter_website_logo'] != '') {
							echo '
							<div class="adi_logo_preview">
							<a href="'.$adiinviter->settings['adiinviter_website_logo'].'" target="_blank" class="adi_logo_prev" style="margin: 3px 0px; display: block;">
							<img src="' . $adiinviter->settings['adiinviter_website_logo'] . 
							'" class="adi_logo_img adi_logo_prev_img">
							</a>
							</div>
							';
						}
						else {
							echo '<div class="adi_no_logo"><span class="adi_no_logo_txt">No Logo</span><div class="clr"></div></div>';
						}
						?>
					</td>
				</tr>

				</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">URLs Information</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Website Root URL</span><br>
						<label class="opts_note">Enter full url to your website root without trailing slash (/). <br>
						e.g. http://www.yourdomain.com</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_website_root_url]" value="<?php echo $adiinviter->settings['adiinviter_website_root_url']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">AdiInviter Pro Root URL</span><br>
						<label class="opts_note">Enter full url to AdiInviter Pro root without trailing slash (/).<br>
						e.g. http://www.yourdomain.com/find_friends</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_root_url]" value="<?php echo $adiinviter->settings['adiinviter_root_url']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Website Register/Sign Up Page URL</span><br>
						<label class="opts_note">Enter full url to your website's register or sign up page.<br>
						For e.g. http://www.yourdomain.com/register.php?invitation_id=[invitation_id]<br>
						<span class="label_red_note">Note:</span> The parameter [invitation_id] is required to uniquely identify invitations.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_website_register_url]" value="<?php echo $adiinviter->settings['adiinviter_website_register_url']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Website Login Page URL</span><br>
						<label class="opts_note">Enter full url to your website's Login page.<br>
						e.g. http://www.yourdomain.com/login</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg" name="subsettings[global][adiinviter_website_login_url]" value="<?php echo $adiinviter->settings['adiinviter_website_login_url']; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->
				
			</table>
		</div></div></div>
			<!-- <hr class="bef_submit"> -->
			<div class="cont_submit" style="padding-right:8px;">
				<input type="submit" value="Save Settings" class="btn_grn" id="adi_save_settings">
			</div>
		</form>
	</div>

	<div class="sect_out_div sect_website_embedding">
		<div style="margin: 20px 0px 0px 10px;">

		<div class="chead1">Inpage Model</div>
		<div class="dccb" style="margin-bottom:40px;"><pre><?php
		echo adi_parse_code_block(rtrim($adiinviter->inpage_model_url, '?'));
		?></pre></div>

		<div class="chead1">Popup Model</div>
		<div class="dccb" style="margin-bottom:40px;"><pre><?php
		echo adi_parse_code_block(rtrim($adiinviter->popup_model_url, '?'));
		?></pre></div>

		<div class="chead1">Invite History</div>
		<div class="dccb" style="margin-bottom:40px;"><pre><?php
		echo adi_parse_code_block(rtrim($adiinviter->invite_history_url, '?'));
		?></pre></div>

		<div class="chead1">Verify Invitation</div>
		<div class="dccb" style="margin-bottom:40px;"><pre><?php
		echo adi_parse_code_block(rtrim($adiinviter->verify_invitation_url, '?'));
		?></pre></div>

		</div>
	</div> <!-- sect_website_embedding End -->

</div>
		<?php
		break;

		case 'db_info' :

		$adiinviter->requireSettingsList('db_info');
		$adminconfig_file = ADI_ADMIN_PATH . 'adi_admin_config.php';
		include($adminconfig_file);
		?>

	<div style="margin:10px;">

	<form method="post" action="" class="adi_db_details adi_db_conn_details" data="checkConnDetails">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_db_type]" value="<?php echo $adiinviter_settings['adiinviter_db_type']; ?>">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_hostname]" value="<?php echo $adiinviter_settings['adiinviter_hostname']; ?>">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_username]" value="<?php echo $adiinviter_settings['adiinviter_username']; ?>">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_password]" value="<?php echo $adiinviter_settings['adiinviter_password']; ?>">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_dbname]" value="<?php echo $adiinviter_settings['adiinviter_dbname']; ?>">
		<input type="hidden" name="subsettings[adminconfig][adiinviter_table_prefix]" value="<?php echo $adiinviter_settings['adiinviter_table_prefix']; ?>">
	</form>

	<div class="sect_out_div sect_users_integration" style="min-height:1647px;">
		<?php
		$yes_value = $no_value = 'radio_btn'; $css_def = '';
		$clr_us_css = '';
		if($adiinviter->user_system) {
			$yes_value = 'radio_btn_current';
			$clr_us_css = 'display:none;';
		}
		else {
			$no_value = 'radio_btn_current';
			$css_def = 'display:none;';
		}
		?>
		
		<table style="width:100%; margin-top:15px;" class="opts_table" cellspacing="0" cellpadding="0">
		<tr class="first">
			<td class="label_box" colspan="2">
				<span class="opts_head">User System Integration<a href="http://www.adiinviter.com/docs/integration" class="adi_docs_link" target="_blank">Integration Documentation</a></span>
			</td>
		</tr>
		<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
		<tr>
			<td colspan="2">
				<div class="radio_buttons" name="adi_user_system_enabled">
					<span class="<?php echo $yes_value; ?> adi_user_system_toggle" data="1">I have a User System in my website.</span>
					<div class="radio_sep"></div>
					<span class="<?php echo $no_value; ?> adi_user_system_toggle" data="0">I do not have a User System in my website.</span>
				</div>
			</td>
		</tr>
		</table>

		<div class="adi_clr_user_system_out" style="<?php echo $clr_us_css;?>">
			<form action="" method="post" class="adi_clear_integration_form adi_clr_user_system_form">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
			</table>
			<div style="text-align:right;padding: 10px 0px;">
				<input type="hidden" name="subsettings[db_info][user_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][userfullname]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][userid]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][username]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][email]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][user_table][avatar]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][userid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][avatar_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][avatar_table][userid]" value="">
				<input type="hidden" name="subsettings[db_info][avatar_table][avatar]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][name]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][userid]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][friend_id]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][status]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][yes_value]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][pending_value]" value="">
				<input type="hidden" name="subsettings[db_info][adiinviter_avatar_url]" value="">
				<input type="hidden" name="subsettings[db_info][adiinviter_profile_page_url]" value="">
				<input style="margin-right: 10px;" type="submit" value="Save Settings" class="btn_grn">
			</div>
			</form>
		</div>

		<div class="adi_user_system_details" style="<?php echo $css_def;?>">
		<form method="post" action="" class="adi_db_details" data="checkUserDetails">

	<?php

	$result = adi_build_query_read('get_all_tables');
	$all_tables = array();
	while($row = adi_fetch_array($result))
	{
		$all_tables[] = array_shift($row);
	}



	?>
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">User Table
			<a href="http://www.adiinviter.com/docs/user-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a>
			</div>
			<div class="adi_inner_sect_body">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label_box">
							<span class="opts_head">User Table</span><br>
							<label class="opts_note">Select a table in your database which stores the information about users in your website.<br>For e.g. userid, username, email etc.</label>
						</td>
						<td>
<?php

$options = array();
// $options[] = array('' => 'Select user table');
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][table_name]',
	'input_class'    => 'adi_user_table_name',
	'default_option' => $adiinviter->settings['user_table']['table_name'],
	'options'        => $options,
	'default_text'   => 'Select User Table',
);
echo adi_get_select_plugin($details);

?>
<div class="adi_inner_sect_actions">
	<input type="button" class="btn_org adi_load_user_table" value="Load Table">
</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		
		<div class="adi_inner_sect_sep"></div>

		<div class="adi_user_table_details_out">
		<?php 

		if($adiinviter->user_system)
		{
			$get_code = 'user_table_details';
			$user_table_name = $adiinviter->settings['user_table']['table_name'];
			include('adi_db_info.php');
		}
		?>
		</div>

		</form>
	</div>
	</div>







		<!-- Usergroup Details-->
	<div class="sect_out_div sect_usergroups_integration">
		<?php

		$usergroup_table = $adiinviter->settings['usergroup_table']['table_name'];
		$usergroup_usergroupid_field = $adiinviter->settings['usergroup_table']['usergroupid'];
		$usergroup_name_field = $adiinviter->settings['usergroup_table']['name'];

		$yes_value = $no_value = 'radio_btn'; $css_def = '';
		$clr_us_css = '';
		if($adiinviter->usergroup_system || (!empty($usergroup_table) && !empty($usergroup_usergroupid_field) && !empty($usergroup_name_field)))
		{
			$yes_value = 'radio_btn_current';
			$clr_us_css = 'display:none;';
		}
		else
		{
			$no_value = 'radio_btn_current';
			$css_def = 'display:none;';
		}
		?>
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
		<tr class="first">
			<td class="label_box" colspan="2">
				<span class="opts_head">Usergroup System Integration<a href="http://www.adiinviter.com/docs/usergroups-system-integration" class="adi_docs_link" target="_blank">Integration Documentation</a></span>
			</td>
		</tr>
		<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
		<tr>
			<td colspan="2">
				<div class="radio_buttons" name="adi_usergroup_system_enabled">
					<span class="<?php echo $yes_value; ?> adi_usergroup_system_toggle" data="1">I have a Usergroup System in my website.</span>
					<div class="radio_sep"></div>
					<span class="<?php echo $no_value; ?> adi_usergroup_system_toggle" data="0">I do not have a Usergroup System in my website.</span>
				</div>
			</td>
		</tr>
		</table>

		<div class="adi_clr_usergroup_system_out" style="<?php echo $clr_us_css;?>">
			<form action="" method="post" class="adi_clear_integration_form adi_clr_usergroup_system_form">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
			</table>
			<div style="text-align:right;padding: 10px 0px;">
				<span style="float:left;margin: 7px 10px;" id="checkUsergroup_resp"></span>
				<input type="hidden" name="subsettings[db_info][user_table][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][userid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_mapping][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][usergroupid]" value="">
				<input type="hidden" name="subsettings[db_info][usergroup_table][name]" value="">
				<input style="float:right;margin-right: 10px;" type="submit" value="Save Settings" class="btn_grn">
				<div class="clr"></div>
			</div>
			</form>
		</div>

		<div class="adi_usergroup_system_details" style="<?php echo $css_def;?>">
		<form method="post" action="" class="adi_db_details" data="checkUsergroupDetails">

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Usergroups Table<a href="http://www.adiinviter.com/docs/usergroups-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label_box">
							<span class="opts_head">Usergroups Table</span><br>
							<label class="opts_note">Select a table in your database which stores the information about usergroups in your website.</label>
						</td>
						<td>
<?php

$options = array();
// $options[] = array('' => 'Select usergroup table');
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_table][table_name]',
	'input_class'    => 'adi_usergroup_table_name',
	'default_option' => $adiinviter->settings['usergroup_table']['table_name'],
	'options'        => $options,
	'default_text'   => 'Select usergroup table',
);
echo adi_get_select_plugin($details);

?>
<div class="adi_inner_sect_actions">
	<input type="button" class="btn_org adi_load_usergroup_table" value="Load Table">
</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_usergroup_table_details_out">
		<?php 
		if($adiinviter->usergroup_system || (!empty($usergroup_table) && !empty($usergroup_usergroupid_field) && !empty($usergroup_name_field)))
		{
			$get_code = 'usergroup_table_details';
			$usergroup_table_name = $adiinviter->settings['usergroup_table']['table_name'];
			include('adi_db_info.php');
		}
		?>
		</div>
		</form>
		</div>
	
	<div style="width:100%;height:180px;"></div>

	</div>






	<!-- Friends Table Details-->
	<div class="sect_out_div sect_friends_integration">
	<?php
		$no_system = $frd_system = $flr_system = 'radio_btn'; 
		$clear_form_div = $frd_fields_div = $flr_fields_div = 'display:none;';
		if($adiinviter->friends_system) 
		{
			$yes_value = 'radio_btn_current';
			if($adiinviter->settings['friends_table']['status'] != '' && 
				$adiinviter->settings['friends_table']['yes_value'] != '' && 
				$adiinviter->settings['friends_table']['pending_value'] != '' )
			{
				$frd_system = 'radio_btn_current';
				$frd_fields_div = '';
			}
			else
			{
				$flr_system = 'radio_btn_current';
				$flr_fields_div = '';
			}
		}
		else 
		{
			$no_system = 'radio_btn_current';
			$clear_form_div = '';
		}
	?>
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
		<tr class="first">
			<td class="label_box" colspan="2">
				<span class="opts_head">Friends/Follower System Integration<a href="http://www.adiinviter.com/docs/friends-system-integration" class="adi_docs_link" target="_blank">Integration Documentation</a></span>
			</td>
		</tr>
		<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
		<tr>
			<td colspan="2">
				<div class="radio_buttons" name="adi_friends_system_enabled">
					<span class="<?php echo $frd_system; ?> adi_friends_system_toggle" data="1">I have a Friends System in my website.</span>
					<div class="radio_sep"></div>
					<span class="<?php echo $flr_system; ?> adi_friends_system_toggle" data="2">I have a Followers System in my website.</span>
					<div class="radio_sep"></div>
					<span class="<?php echo $no_system; ?> adi_friends_system_toggle" data="0">I do not have Friends or Followers System in my website.</span>
				</div>
			</td>
		</tr>
		</table>


		<div class="adi_clr_friends_system_out" style="<?php echo $clear_form_div;?>">
			<form action="" method="post" class="adi_clear_integration_form adi_clr_friends_system_form">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
			</table>
			<div style="text-align:right;padding: 10px 0px;">
				<span style="float:left;margin: 7px 10px;" id="checkFriends_resp"></span>
				<input type="hidden" name="subsettings[db_info][friends_table][table_name]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][userid]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][friend_id]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][status]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][yes_value]" value="">
				<input type="hidden" name="subsettings[db_info][friends_table][pending_value]" value="">
				<input style="float:right;margin-right: 10px;" type="submit" value="Save Settings" class="btn_grn">
				<div class="clr"></div>
			</div>
			</form>
		</div>


		<div class="adi_friends_system_details" style="<?php echo $frd_fields_div;?>">
			<form method="post" action="" class="adi_db_details" data="checkFriendsDetails">
			<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Friends System<a href="http://www.adiinviter.com/docs/friends-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label_box">
							<span class="opts_head">Friends System Table</span><br>
							<div class="opts_note" style="max-width: 500px;">Select a table in your database which stores the information about friend relations between users in your website.</div>
						</td>
						<td>
<?php

$options = array();
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][table_name]',
	'input_class'    => 'adi_friends_table_name',
	'default_option' => $adiinviter->settings['friends_table']['table_name'],
	'options'        => $options,
	'default_text'   => 'Select Table',
);
echo adi_get_select_plugin($details);

?>
<div class="adi_inner_sect_actions">
	<input type="button" class="btn_org adi_load_friends_table" value="Load Table">
</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_friends_table_details_out">
		<?php 
		if($adiinviter->friends_system)
		{
			$get_code = 'friends_table_details';
			$friends_table_name = $adiinviter->settings['friends_table']['table_name'];
			include('adi_db_info.php');
		}
		?>
		</div>
		</form>
		</div>


		<div class="adi_followers_system_details" style="<?php echo $flr_fields_div;?>">
			<form method="post" action="" class="adi_db_details" data="checkFriendsDetails">
			<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Followers System<a href="http://www.adiinviter.com/docs/followers-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label_box">
							<span class="opts_head">Follower System Table</span><br>
							<div class="opts_note" style="max-width:510px;">Select a table in your database which stores the information about follower relations between users in your website..</div>
						</td>
						<td>
<?php

$options = array();
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][table_name]',
	'input_class'    => 'adi_followers_table_name',
	'default_option' => $adiinviter->settings['friends_table']['table_name'],
	'options'        => $options,
	'default_text'   => 'Select Table',
);
echo adi_get_select_plugin($details);

?>
<div class="adi_inner_sect_actions">
	<input type="button" class="btn_org adi_load_followers_table" value="Load Table">
</div>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_followers_table_details_out">
		<?php 
		if($adiinviter->friends_system)
		{
			$get_code = 'followers_table_details';
			$followers_table_name = $adiinviter->settings['friends_table']['table_name'];
			include('adi_db_info.php');
		}
		?>
		</div>
		</form>
		</div>


	</div>
</div>

		<?php
		break;



		case 'language' :
		
			$available_lang_ids = $adiinviter->get_installed_lang_ids();
			$all_languages = $adiinviter->cache['language'];
			foreach($available_lang_ids as $lang_id => $lang_name)
			{
				if(isset($all_languages[$lang_id]))
				{
					unset($all_languages[$lang_id]);
				}
			}

		$available_lang_ids = $adiinviter->get_installed_lang_ids();
		?>
		<div class="adi_phrases_outer"></div>
		<div class="adi_edit_phrase_outer"></div>

		<div class="sect_out_div sect_language_manager">
		<form method="post" action="" class="settings_list">
		<div class="adi_lang_options_out" style="margin: 40px 20px 20px 20px;">

		<div style="margin:0px 0px 45px 10px;">
			<div class="ate_ho" style="margin-bottom:10px;">
				<div class="opts_head">Language Packs<a href="http://www.adiinviter.com/docs/languages" class="adi_docs_link" target="_blank" style="font-weight: bold;">Language Reference Documents</a></div>
			</div>
			<div class="pp1">You can download ready-made language packs for AdiInviter Pro from our official language packs directory.</div>
			<br>
			<a href="http://www.adiinviter.com/addons/langauge-packs" target="_blank" class="pp1 lnk1">Download Language Packs</a>
		</div>

		<?php if(count($available_lang_ids) > 10) { ?>
			<div style="text-align:right;margin-bottom:20px;">
				<button type="button" value="" class="btn_grn adi_add_new_language" style="padding: 0;"><span class="add_lang">Create/Import Language Pack</span></button>
			</div>
		<?php } ?>

			<table class="settings_table plugins_table" style="margin-bottom:15px;" width="100%" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th width="60" align="center"><center>Default</center></th>
					<th align="left">Name</th>
					<th width="230" align="center" class="pluginis_last_col"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$odd = true;
				$css_cls = '';
				$adiinviter->requireSettingsList('global');
				if(count($available_lang_ids) > 0)
				{
					foreach($available_lang_ids as $lang_id => $lang_name)
					{
						$odd = !$odd;
						$css_cls = ($odd ? ' class="odd"' : '');

						if($adiinviter->settings['language'] == $lang_id) 
						{
							$lang_cls = 'theme_checked';
							$radio_checked = ' checked="true"';
						}
						else
						{
							$lang_cls = 'theme_unchecked';
							$radio_checked = '';
						}

						echo '<tr '.$css_cls.'>
						<td valign="middle" align="center"><center><div class="themes_onoff '.$lang_cls.'"><input type="radio" name="subsettings[global][language]" value="'.$lang_id.'" '.$radio_checked.'></div></center></td>
						<td style="">'.$lang_name.' ('.$lang_id.')</td>
						<td class="centered_td pluginis_last_col">
							<input type="button" class="btn_blue btn_small lang_edit" data="'.$lang_id.'" value="Edit">
							<input type="button" class="btn_blue1 btn_small btn_left_space lang_export" rel="'.$lang_name.'" data="'.$lang_id.'" value="Export">
							';
						if($lang_id != 'en')
						{
							echo '
							<input type="button" class="btn_grn btn_small btn_left_space lang_remove" rel="'.$lang_name.'" data="'.$lang_id.'" value="Remove">';
						}
						echo '
							</td>
						</tr>';
					}
				}
				else {
					echo '<font color="red"><i>No Language installed.</i></font>';
				}
				?>
			</tbody>
			</table>

			<!-- <hr class="bef_submit"> -->
			<div class="cont_submit">
				<span class="adi_langauge_opt_response"></span>
				<button type="button" value="" class="btn_grn adi_add_new_language" style="padding: 0; float:left;"><span class="add_lang">Create/Import Language Pack</span></button>
				<input type="submit" value="Save Settings" class="btn_grn btn_left_space" id="adi_save_settings" style="margin-left: 15px;">
			</div>
		</div>
		</form>

		<div class="adi_new_lang_form_out" style="display:none;">
			
		</div>
		</div>

		<?php
		case 'phrase_groups' :
			?>
		<div class="sect_out_div sect_search_in_phrases">
		<div style="margin: 15px 0px;">
		<form id="adi_sif_form">
		<div style="margin: 20px 0px 0px 0px;">
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header" style="padding-left:15px;">Search In Phrases</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">Search For Text</span><br>
						<label class="opts_note">Enter text to search in phrases.</label>
					</td>
					<td>
						<textarea name="adi_search_phrases[search_text]" class="med adi_sif_query" spellcheck="false" style="resize:none;width: 392px;"></textarea>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Choose Language</span><br>
						<label class="opts_note">Choose language to search.</label>
					</td>
					<td>
<?php

$options   = array();
$available_lang_ids = $adiinviter->get_installed_lang_ids();

$options[] = array('*' => array(3,'All Lanaguages'));

if(count($available_lang_ids) > 0)
{
	foreach($available_lang_ids as $id => $lang_name)
	{
		$options[] = array($id => $lang_name);
	}
}
// $options[] = $groups;
$details = array(
	'input_name'     => 'adi_search_phrases[lang_name]',
	'input_class'    => 'adi_sif_lang',
	'default_option' => '*',
	'options'        => $options,
	'default_text'   => 'All Lanaguages',
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Search In</span><br>
						<label class="opts_note">Choose more specific option for quick results.</label>
					</td>
					<td>
						<div class="radio_buttons" name="adi_search_phrases[search_in]">
							<span class="radio_btn" data="1">Phrase text only</span>
							<div class="radio_sep"></div>
							<span class="radio_btn" data="2">Phrase varname only</span>
							<div class="radio_sep"></div>
							<span class="radio_btn_current" data="3">Phrase text and phrase varname</span>
							<input type="hidden" name="adi_search_phrases[search_in]" value="3" class="adi_sif_type">
						</div>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

			</table>

			</div></div>
		</div>
			<div style="padding: 5px 10px;">
				<input style="float: right;" type="submit" value="Search" class="btn_grn">
				<div class="adi_sif_form_response"></div>
				<div style="clear:both;"></div>
				<div class="clr"></div>
			</div>
		</form>
		</div>

		<script type="text/javascript">

		$(document).ready(function(){
			// New Phrase form
			$('#adi_sif_form').submit(function(){
				adi.submit_sif_Form(this);
				return false;
			});
		});

		</script>
		</div>


		<?php
		break;

		case 'permissions' :
			include(ADI_ADMIN_PATH.'adi_permissions.php');
		break;

		case 'themes' :
			include(ADI_ADMIN_PATH.'adi_themes.php');
		break;

		case 'extensions' :
			include(ADI_ADMIN_PATH.'adi_extensions.php');
		break;

		case 'updates' :
			include(ADI_ADMIN_PATH.'adi_updates.php');
		break;
	}
}



?>