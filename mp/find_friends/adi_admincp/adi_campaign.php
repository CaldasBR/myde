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


$adiinviter->requireSettingsList(array('global', 'db_info', 'campaigns'));


$campaigns_list = '';
$adi_get = AdiInviterPro::POST('adi_get', ADI_STRING_VARS);
$adi_get_values = array(
	'campaigns_list', 'campaign_settings'
);
if(!in_array($adi_get, $adi_get_values)) {
	$adi_get = '';
}

if(empty($adi_get) || $adi_get == 'campaigns_list')
{
	if(count($adiinviter->settings['campaigns_list']) > 0)
	{
		$campaigns_list .= '
		<form class="settings_list">

		<div class="" style="color: #444;margin: 0px 10px;">
		<table class="settings_table plugins_table" style="margin-bottom:20px;" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th align="left">Campaigns</th>
				<th width="215" align="center" class="pluginis_last_col"><center></center></th>
			</tr>
		</thead>
		<tbody>';
		$odd = true;
		$css_cls = '';

		foreach($adiinviter->settings['campaigns_list'] as $type_id => $type_name)
		{
			$odd = !$odd;
			$css_cls = ($odd ? ' class="odd"' : '');

			$val = adi_getSetting('campaign_'.$type_id, 'campaign_on_off');
			if($val == '1')
			{
				$onoff_class = 'css_active';
			}
			else
			{
				$onoff_class = 'css_inactive';
			}

			$campaigns_list .= '<tr '.$css_cls.'>
				<td valign="middle">
					<div style="vertical-align:top;">
						<div class="opts_head">'.$type_name.'</div>
						<div class="opts_note">Campaign Id : '.$type_id.'</div>
					</div>
				</td>
				<td class="centered_td pluginis_last_col">
				<div class="lang_actions" style="float:none;">
					<input type="button" class="btn_grn btn_left_space adi_delete_cs_btn" data="'.$type_id.'" value="Remove" style="float:right;">
					<input type="button" class="btn_org adi_edit_cs_btn" data="'.$type_id.'" value="Configure" style="float:right;">
					<div style="clear:both;"></div>
				</div>
				</td>
			</tr>';
		}
		$campaigns_list .= '
		</tbody>
		</table>
		
		<div style="">
			<!-- <input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left"> -->
			<input style="float: right;" type="button" value="New Campaign" class="btn_grn adi_show_new_cs_form">
			<span style="float: right;margin: 7px 10px;" id="adi_new_cs_response_2"></span>
			<div class="clr"></div>
		</div>
		</div>

		</form>';
	}
}



if(empty($adi_get))
{
	if(count($adiinviter->settings['campaigns_list']) > 0)
	{
		$css_txt = 'display:none;';
		$cancel_btn = '';
	}
	else 
	{
		$css_txt = 'display:block;';
		$cancel_btn = 'display:none;';
	}
	?>
	<!-- Sharing Systems list -->
	<div style="margin:10px;">
	<div class="adi_campaign_list">

		<div style="margin: 15px 15px 30px 15px;">
			<div class="ate_ho" style="margin-bottom:10px;">
				<div class="opts_head">Campaigns</div>
			</div>
			<div class="pp1 mb5">Campaigns allows you to create specialized invitation systems for your website. Following reference documentation contains detailed information about creating, managing and customizing campaigns in your website.</div>
			<a href="http://www.adiinviter.com/docs/campaigns" class="adi_docs_link" target="_blank" style="float:none;font-size:13px;padding-left: 21px;font-weight:bold;">Campaigns Reference Documentation</a>
		</div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_campaign_list_data">
			<?php echo $campaigns_list; ?>
		</div>
	</div>

	<!-- New sharing system type form -->
	<div class="adi_new_cs_form_outer" style="<?php echo $css_txt; ?>">
		<form method="post" id="adi_new_cs_form">
		<div style="padding-top:1px;">
			<div class="adi_inner_sect">
				<div class="adi_inner_sect_header">Create New Campaign</div>
				<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Campaign Title</span><br>
						<label class="opts_note">Specify unique title for Campaign.</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg1" name="new_campaign_form[name]" value="" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Campaign Id</span><br>
						<label class="opts_note">Specify unique alphanumeric Id for Campaign (Max 20 chars).</label>
					</td>
					<td>
						<input type="textbox" class="txinput reg1" name="new_campaign_form[id]" value="" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
			</div></div>
			<div style="text-align:right;margin: 0px 10px;">
				<input style="float: right;" type="submit" value="Create Campaign" class="btn_grn adi_btn_space_left" id="adi_save_settings">
				<input type="button" value="Back" class="btn_org adi_new_cs_cancel" style="float:right;<?php echo $cancel_btn; ?>">
				<span style="float: right;margin: 7px 10px;" id="adi_new_cs_response"></span>
				<div class="clr"></div>
			</div>

		</div>
		</form>
	</div>

	<div class="adi_cs_settings_outer">
	</div>
	<?php
}
else if($adi_get == 'campaigns_list')
{
	echo $campaigns_list;
}
else if($adi_get == 'campaign_settings')
{
	$campaign_id = AdiInviterPro::POST('campaign_id');
	if( !empty($campaign_id) && isset($adiinviter->settings['campaigns_list'][$campaign_id]) )
	{
		$settings = adi_getSetting('campaign_'.$campaign_id);
		$campaign_name = $adiinviter->settings['campaigns_list'][$campaign_id];
	?>
	<div class="sect_out_div sect_campaign_settings">

	<form method="post" class="settings_list adi_update_campaign_form">
	<div style="padding: 10px 0px 0px 0px;">

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">General Settings<a href="http://www.adiinviter.com/docs/campaigns-create" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">

			<tr>
				<td class="label_box">
					<span class="opts_head">Campaign Id</span><br>
					<label class="opts_note">Unique Id of this campaign.</label>
				</td>
				<td>
					<span class="opts_head"><?php echo $campaign_id; ?></span>
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Campaign On/Off</span><br>
					<label class="opts_note">Globally turn this campaign On/Off.</label>
				</td>
				<td>
					<?php
					$val = $settings['campaign_on_off'];
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
						<input type="hidden" name="subsettings[campaign_<?php echo $campaign_id; ?>][campaign_on_off]" value="<?php echo $val; ?>" class="switch_val adi_sharing_onoff_switch" data="adi_<?php echo $campaign_id; ?>_onoff">
					</p>
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Campaign Title</span><br>
					<label class="opts_note">Specify unique title for this campaign.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="edit_campaign[campaign_name]" value="<?php echo $campaign_name; ?>" spellcheck="false" autocomplete="off">
					<input type="hidden" name="edit_campaign[campaign_id]" value="<?php echo $campaign_id; ?>">
				</td>
			</tr>
			
			
		</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Redirect URL<a href="http://www.adiinviter.com/docs/campaigns-create#redirect-users-on-off" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Redirect Users On/Off</span><br>
					<label class="opts_note">Redirect users after they visit or signup from invitations sent by this campaign.</label>
				</td>
				<td>
					<?php
					$val = $settings['redirection_on_off'];
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
						<input type="hidden" name="subsettings[campaign_<?php echo $campaign_id; ?>][redirection_on_off]" value="<?php echo $val; ?>" class="switch_val" data-name="redirection_on_off">
					</p>
				</td>
			</tr>
		</table>
		<div class="build_url_outer_div" <?php if($settings['redirection_on_off'] != 1){ echo ' style="display:none;"'; } ?>>
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
		<tr>
			<td class="label_box">
				<span class="opts_head">Redirect URL</span><br>
				<label class="opts_note">Specify URL where you want to redirect users.<br>
For e.g.<br>
<div style="margin-left:10px; line-height:24px;">
http://www.yourdomain.com/offers.php<br>
http://www.yourdomain.com/events/<br>
http://www.yourdomain.com/node/[content_id]<br>
http://www.yourdomain.com/article.php?id=[content_id]<br>
http://www.yourdomain.com/[category_id]/[alias_url_value]<br>
http://www.yourdomain.com/profile.php?uid=[invite_sender_id]<br>
</div>
				</label>
			</td>
			<td>
				<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][content_page_url]" value="<?php echo $settings['content_page_url']; ?>" spellcheck="false" autocomplete="off">
<table class="perm_table" style="margin-top:10px;" cellspacing="0" cellpadding="0">
<thead><tr><th class="perm_table_th">Markup</th><th class="perm_table_th">Meaning</th></tr></thead>
<tbody>
<tr><td class="synt_table_td">[content_id]</td><td class="synt_table_td">Content Id</td></tr>
<tr class="odd"><td class="synt_table_td">[content_title]</td><td class="synt_table_td">Content Title</td></tr>
<tr><td class="synt_table_td">[category_id]</td><td class="synt_table_td">Category Id</td></tr>
<tr><td class="synt_table_td">[alias_url_value]</td><td class="synt_table_td">Alias URL Value</td></tr>
<tr><td class="synt_table_td">[website_root_url]</td><td class="synt_table_td">Website Root URL</td></tr>
<tr class="odd"><td class="synt_table_td">[invite_sender_id]</td><td class="synt_table_td">Invite Sender Id</td></tr>
</tbody></table>
			</td>
		</tr>
		</table></div>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		

		

		<div style="padding: 0px 10px 15px;">
			<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_settings">
			<input type="button" value="Back" class="btn_org cs_cancel_edit_form" style="float:right;">
			<div class="clr"></div>
		</div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Invitation Subject</div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<span class="opts_head">Invitation Subject</span><br>
					<label class="opts_note">Invitation subject for all outgoing email and social networks invitations sent by this campaign.</label>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:5px;">
					<div class="template_preview">
						<?php
						$invitation_subject = isset($settings['invitation_subject_en']) ? $settings['invitation_subject_en'] : '';
						echo '<div class="template_code_en" data="template_code_iframe3" style="display:none;"><!-- '.$invitation_subject.' --></div>';
						?>
						<iframe id="template_code_iframe3" class="tmpl_cd_iframe" width="100%" style="min-width:600px;max-height:50px;"></iframe>
					</div>
				</td>
			</tr>
			</table>
		</div></div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<!-- <input type="button" value="Edit" data="tmpl_cd_invitation_subject" class="btn_org tmpl_open_shorteditor"> -->
			<input type="button" value="Edit" data="tmpl_cd_invitation_subject" data-sid="campaign" data-sg="campaign_<?php echo $campaign_id; ?>" data-sn="invitation_subject" data-cupdate="template_code_iframe3" class="btn_org tmpl_open_teditor">
		</div>
			


		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Invitation Email<a href="http://www.adiinviter.com/docs/references-invitations" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Invitation Message Body For This Campaign</span><br>
					<label class="opts_note">Modify or customize invitation email message body (HTML supported).</label>
				</td>
				<td class="template_actions">
					
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:5px;">
					<div class="template_preview">
						<?php
						$campaign_email_body = isset($settings['campaign_email_body_en']) ? $settings['campaign_email_body_en'] : '';
						echo '<div class="template_code_en" data="template_code_iframe2" style="display:none;"><!-- '.$campaign_email_body.' --></div>';
						?>
						<iframe id="template_code_iframe2" class="tmpl_cd_iframe" width="100%"></iframe>
					</div>
				</td>
			</tr>
			</table>
		</div></div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<!-- <input type="button" value="Edit" data="tmpl_cd_invitation_body" class="btn_org tmpl_open_editor"> -->
			<input type="button" value="Edit" data="tmpl_cd_invitation_body" data-sg="campaign_<?php echo $campaign_id; ?>" data-sn="campaign_email_body" data-sid="campaign" data-cupdate="template_code_iframe2" class="btn_org tmpl_open_teditor">
		</div>
			


		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Twitter Invitation Body</div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Invitation Message Body For Twitter</span><br>
					<label class="opts_note">Modify or customize invitation message body for Twitter (HTML NOT supported).</label>
				</td>
				<td class="template_actions">
					
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:5px;">
					<div class="template_preview">
						<?php
						$campaign_twitter_body = isset($settings['campaign_twitter_body_en']) ? $settings['campaign_twitter_body_en'] : '';
						echo '<div class="template_code_en" data="template_code_iframe1" style="display:none;"><!-- '.$campaign_twitter_body.' --></div>';
						?>
						<iframe id="template_code_iframe1" class="tmpl_cd_iframe" width="100%"></iframe>
					</div>
				</td>
			</tr>
			</table>
		</div></div>

		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="button" value="Edit" data="tmpl_cd_invitation_body" data-sg="campaign_<?php echo $campaign_id; ?>" data-sn="campaign_twitter_body" data-sid="campaign" data-cupdate="template_code_iframe1" class="btn_org tmpl_open_teditor">
		</div>

	</div>

<?php
$adi_services = adi_allocate_pack('Adi_Services');
$adi_services->get_service_details('all');
if($adi_services->social_importer_service_count > 0)
{
?>
	<!-- Social Network Invitaiton -->
	<div class="">
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Social Network Invitations<a href="http://www.adiinviter.com/docs/references-invitations" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Invitation Message Body For Social Network Invitations Sent By This Campaign</span><br>
					<label class="opts_note">Invitation body for outgoing social network invitations (No HTML).</label>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:5px;">
					<div class="template_preview">
						<?php
						$campaign_social_body = isset($settings['campaign_social_body_en']) ? $settings['campaign_social_body_en'] : '';
						echo '<div class="template_code_en" data="template_code_iframe1" style="display:none;"><!-- '.$campaign_social_body.' --></div>';
						?>
						<iframe id="template_code_iframe1" class="tmpl_cd_iframe" width="100%"></iframe>
					</div>
				</td>
			</tr>
		</table>
		</div></div>
		<div class="cont_submit" style="padding: 0px 10px 15px;">
			<input type="button" value="Edit" data="tmpl_cd_social_invitation_body" data-sg="campaign_<?php echo $campaign_id; ?>" data-sn="campaign_social_body" data-sid="campaign" data-cupdate="template_code_iframe1" class="btn_org tmpl_open_teditor">
		</div>
<?php } ?>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Embed Code<a href="http://www.adiinviter.com/docs/campaigns-embedding" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<label class="opts_note">If you are inserting any dynamic contents into invitation messages, then write a code to replace <span class="opts_note hg2">&lt;CONTENT_ID&gt;</span> by current item's content id that is being inserted into invitation messages.</label>
					<br>
					<br>
					<label class="opts_note"><span class="label_red_note">For Example :</span> If you have created a campaign for inserting blog post/article contents into invitation messages, then write a code to replace <span class="opts_note hg2">&lt;CONTENT_ID&gt;</span> by post/article id in your website.</label>
				</td>
			</tr>
			<tr>
				<td style="padding-top:20px;">
					<div class="embed_code_cont">
					<div class="embed_code_switch">
						<div class="embed_code_head opts_head">HTML Code</div>
						<input type="button" class="btn_blue btn_small adi_btn_space_right embed_switch" data="html_embed_code" value="HTML Code">
						<input type="button" class="btn_blue1 btn_small embed_switch" data="php_embed_code" value="PHP Code">
						<div class="clr"></div>
					</div>
					<div class="embed_code_out dccb html_embed_code"><pre><?php
						$inpage_model_url = htmlentities($adiinviter->inpage_model_url_rel);


echo adi_parse_code_block('<[AWTAG]a[/AWTAG] [AWATTR]adi_content_id[/AWATTR]=[AWSTR]"[AWTAG]<CONTENT_ID>[/AWTAG]"[/AWSTR]
   [AWATTR]href[/AWATTR]=[AWSTR]"'.$inpage_model_url.'adi_campaign_id='.$campaign_id.'&adi_content_id=[AWTAG]<CONTENT_ID>[/AWTAG]"[/AWSTR]
   [AWATTR]class[/AWATTR]=[AWSTR]"adi_link adi_open_popup_model"[/AWSTR]
   [AWATTR]adi_campaign_id[/AWATTR]=[AWSTR]"'.$campaign_id.'"[/AWSTR]>
Share With Friends
</[AWTAG]a[/AWTAG]>');


					?></pre></div>
					<div class="embed_code_out dccb php_embed_code" style="display:none;"><pre><?php

$inpage_model_url = htmlentities($adiinviter->inpage_model_url_rel);
$bootstrap_path = ADI_LIB_PATH.'adiinviter_bootstrap.php';


echo adi_parse_code_block('<?php

[AWFUNC]include_once[/AWFUNC]([AWSTR][AWQT]'.$bootstrap_path.'[AWQT][/AWSTR]);
[AWVAR]$content_id[/AWVAR] = [AWSTR]"[AWTAG]<CONTENT_ID>[/AWTAG]"[/AWSTR];
[AWLANG]if[/AWLANG]([AWVAR]$adiinviter[/AWVAR]->[AWFUNC]is_campaign_allowed[/AWFUNC]([AWSTR]"'.$campaign_id.'"[/AWSTR], [AWVAR]$content_id[/AWVAR]))
{
   [AWLANG]echo[/AWLANG] [AWSTR]"
   <a adi_content_id=[AWQT]{$content_id}[AWQT]
     href=[AWQT]".$adiinviter->inpage_model_url_rel."adi_campaign_id='.$campaign_id.'&adi_content_id={$content_id}[AWQT]
     class=[AWQT]adi_link adi_open_popup_model[AWQT]
     adi_campaign_id=[AWQT]'.$campaign_id.'[AWQT]>
   Share With Friends
   </a>"[/AWSTR];
}

?>');



					?></pre></div>
					</div>
				</td>
			</tr>
		</table>
		</div></div>

		
	</div>
	</form>
	</div>

	<div class="sect_out_div sect_campaign_database_config">
	<form method="post" class="settings_list adi_update_campaign_form">
	<div style="padding: 10px 0px 0px 0px;">

	<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Database Configuration<a href="http://www.adiinviter.com/docs/campaigns-database-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			
			<tr>
				<td class="label_box">
					<span class="opts_head">Database Table</span><br>
					<label class="opts_note">Select a table from your database where dynamic content bodies and content titles are stored.<br>
					<span class="label_red_note">Note:</span> Choose Not Required, if you do not want to integrate campaign with database.</label>
				</td>
				<td>
<?php

$result = adi_build_query_read('get_all_tables');
$all_tables = array();
while($row = adi_fetch_array($result))
{
	$all_tables[] = array_shift($row);
}

$content_table_name = $settings['content_table']['table_name'];

$cs_columns = array();
if(!empty($content_table_name))
{
	$result = adi_build_query_read('check_table_structure', array(
		'table_name' => $content_table_name,
	));
	while($row = adi_fetch_array($result))
	{
		$cs_columns[] = $row['Field'];
	}
}

$options   = array();
$options[] = array('' => array(2,'Not Required'));
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][table_name]',
	'input_class'    => 'adi_content_table_name',
	'default_option' => $content_table_name,
	'options'        => $options,
	'default_text'   => array(2,'Not Required'),
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
			</tr>
		</table>
		<?php

		$css_ref = empty($content_table_name) ? 'display:none;' : '';
		?>
		<div class="adi_content_table_fields" style="<?php echo $css_ref;?>">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Content Id Field</span><br>
					<label class="opts_note">Select a field name in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table where Content Ids are stored.</label>
				</td>
				<td>
<?php

$content_id_field = $settings['content_table']['content_id'];
$options   = array();
foreach($cs_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][content_id]',
	'input_class'    => 'adi_content_id_field',
	'default_option' => $content_id_field,
	'options'        => $options,
	'default_text'   => 'Select Content Id Field',
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
			</tr>


			<tr>
				<td class="label_box">
					<span class="opts_head">Content Title Field</span><br>
					<label class="opts_note">Select a field name in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table where Content Titles are stored.<br>
					<span class="label_red_note">Note:</span> Choose Not Present, if you do not have Title in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table.
					</label>
				</td>
				<td>
<?php

$content_title_field = $settings['content_table']['content_title'];
$options = array();
$options[] = array('' => array(2,'Not Present'));
foreach($cs_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][content_title]',
	'input_class'    => 'adi_content_title_field',
	'default_option' => $content_title_field,
	'options'        => $options,
	'default_text'   => array(2,'Not Present'),
);
echo adi_get_select_plugin($details, $type = "down");


?>
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Content Body Field</span><br>
					<label class="opts_note">Select a field name in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table where Content Bodies are stored.</label>
				</td>
				<td>
<?php

$content_body_field = $settings['content_table']['content_body'];
$options = array();
foreach($cs_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][content_body]',
	'input_class'    => 'adi_content_body_field',
	'default_option' => $content_body_field,
	'options'        => $options,
	'default_text'   => 'Select Content Body Field',
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Category Id Field</span><br>
					<label class="opts_note">Select a field name in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table where Content Category Ids are stored.<br>
						<span class="label_red_note">Note:</span> Choose Not Present, if you do not have Category Ids in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table.
					</label>
				</td>
				<td>
<?php

$category_id_field = $settings['content_table']['category_id'];
$options = array();
$options[] = array('' => array(2,'Not Present'));
foreach($cs_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][category_id]',
	'input_class'    => 'adi_category_id_field',
	'default_option' => $category_id_field,
	'options'        => $options,
	'default_text'   => array(2,'Not Present'),
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">URL Alias Field</span><br>
					<label class="opts_note">Select a field name in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table where Alias values are stored.<br>
					<span class="label_red_note">Note:</span> Choose Not Present, if you do not have URL Alias in your <span class="adi_content_table_update_here"><?php echo $content_table_name; ?></span> table.
					</label>
				</td>
				<td>
<?php

$content_alias_field = $settings['content_table']['url_alias'];
$options = array();
$options[] = array('' => array(2,'Not Present'));
foreach($cs_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[campaign_'.$campaign_id.'][content_table][url_alias]',
	'input_class'    => 'adi_content_alias_field',
	'default_option' => $content_alias_field,
	'options'        => $options,
	'default_text'   => array(2,'Not Present'),
);
echo adi_get_select_plugin($details, $type = "down");

?>
				</td>
			</tr>
		</table>
		</div></div>
		</div>

		<div class="adi_inner_sect_sep"></div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Settings<a href="http://www.adiinviter.com/docs/campaigns-database-integration#number-of-words-to-extract" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
			<tr>
				<td class="label_box">
					<span class="opts_head">Number Of Words To Extract From Content Body</span><br>
					<label class="opts_note">Enter the maximum number of words (not characters) to be extracted.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][word_limit]" value="<?php echo $settings['word_limit']; ?>" spellcheck="false" autocomplete="off">
				</td>
			</tr>
			<tr>
				<td class="label_box">
					<span class="opts_head">Restricted Content Ids</span><br>
					<label class="opts_note">Enter comma separated list of content ids to restrict them from sharing.<br>
					<span class="label_red_note">Note:</span> Examples of content ids are post ids, topic ids, article ids, etc.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][restricted_ids]" value="<?php echo $settings['restricted_ids']; ?>" spellcheck="false" autocomplete="off">
				</td>
			</tr>

			<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

			<tr>
				<td class="label_box">
					<span class="opts_head">Restricted Category Ids</span><br>
					<label class="opts_note">Enter comma separated list of restricted category ids for this campaign.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][restricted_category_ids]" value="<?php echo $settings['restricted_category_ids']; ?>" spellcheck="false" autocomplete="off">
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Restricted Usergroup Ids</span><br>
					<label class="opts_note">Enter comma separated list of restricted usergroup ids for this campaign.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][restricted_usergroup_ids]" value="<?php echo $settings['restricted_usergroup_ids']; ?>" spellcheck="false" autocomplete="off">
				</td>
			</tr>

			<tr>
				<td class="label_box">
					<span class="opts_head">Restricted User Ids</span><br>
					<label class="opts_note">Enter comma separated list of restricted user ids for this campaign.</label>
				</td>
				<td>
					<input type="textbox" class="txinput reg1" name="subsettings[campaign_<?php echo $campaign_id; ?>][restricted_user_ids]" value="<?php echo $settings['restricted_category_ids']; ?>" spellcheck="false" autocomplete="off">
				</td>
			</tr>

		</table>
		</div></div>

		<div class="adi_inner_sect_sep"></div>

		<div style="padding: 0px 10px 15px;">
			<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_settings">
			<input type="button" value="Back" class="btn_org cs_cancel_edit_form" style="float:right;">
			<div class="clr"></div>
		</div>

	</div>
	</form>
	</div>

</div>
<?php
	}
}

?>