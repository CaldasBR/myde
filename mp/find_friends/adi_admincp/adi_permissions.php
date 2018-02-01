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



$adi_get = AdiInviterPro::POST('adi_get', ADI_STRING_VARS);

if(empty($adi_get))
{

?>


<div style="margin:10px">


	<div style="margin: 15px 15px 40px 15px;">
		<div class="ate_ho" style="margin-bottom:10px;">
			<div class="opts_head">Permissions</div>
		</div>
		<div class="pp1 mb5">Assign custom permissions to users in your website. The number of invitations limit assigned to not-logged in users indicates how many contacts they can invite at a time.</div>
	</div>

	<center>
	<div class="adi_invite_limit_dom" style="color: #444;margin:20px 10px;">
	<form method="post" id="adi_ug_perms_form">
		<table class="settings_table plugins_table" style="margin-bottom:15px;" width="100%" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
				<th style="text-align:left;">Usergroups</th>
				<th width="110" align="center"><center>Use AdiInviter</center></th>
				<th width="165" align="left"><center>Invitations</center></th>
				<th width="110" align="center"><center>reCaptcha</center></th>
				<th width="110" align="center"><center>Delete Invites</center></th>
				<th width="110" align="center"><center>Download CSV</center></th>
			</tr>
		</thead>
		<tbody>
<?php  



$usergroups = $adiinviter->getAllUsergroupsInfo();

// $perms = $adiinviter->getUsergroupPermissions();
$perms = $adiinviter->permissions->getPermsForAllUsergroups(30);

$guest_usergroup_id = $adiinviter->getGuestUsergroupId();
$odd = true;
$css_cls = '';


foreach($usergroups as $gid => $name)
{
	$can_use_adiinviter = isset($perms[$gid]) ? $perms[$gid][$adiinviter->can_use_adiinviter_ind] : 1;
	$can_use_adiinviter = ($can_use_adiinviter != 1) ? 0 : 1;

	$last_num_invites   = isset($perms[$gid]) ? $perms[$gid][$adiinviter->last_num_invites_ind]   : 'Unlimited';
	$last_num_invites   = (strtolower($last_num_invites) == 'Unlimited' || is_numeric($last_num_invites)) ? $last_num_invites : 'Unlimited';
	
	$can_delete_invites = isset($perms[$gid]) ? $perms[$gid][$adiinviter->can_delete_invites_ind] : 1;
	$can_delete_invites = ($can_delete_invites != 1) ? 0 : 1;

	$can_download_csv   = isset($perms[$gid]) ? $perms[$gid][$adiinviter->can_download_csv_ind]   : 1;
	$can_download_csv   = ($can_download_csv != 1) ? 0 : 1;

	$show_recaptcha     = isset($perms[$gid]) ? $perms[$gid][$adiinviter->show_recaptcha_ind]     : 0;
	$show_recaptcha     = ($show_recaptcha != 1) ? 0 : 1;

	if($adiinviter->db_allowed == false)
	{
		$can_delete_invites = 0;
		$can_download_csv   = 0;
	}

	if($can_use_adiinviter == 0)
	{
		$can_delete_invites = 0;
		$can_download_csv = 0;
		$show_recaptcha = 0;
	}

	$odd = !$odd;
	$css_cls = ($odd ? ' class="odd"' : '');
	$num_invites_txt_cls = ($gid == $guest_usergroup_id) ? 'adi_perms_note_textbox' : '';


	echo '<tr'.$css_cls.'>
	<td align="left">'.$name.'</td>

	<td align="center"><center><div class="adi_after_save adi_img_checkbox '.($can_use_adiinviter == 1 ? 'adi_opt_yes' : 'adi_opt_no').'" depid="adi_ug_perms_'.$gid.'"><input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->can_use_adiinviter_ind.']" value="'.$can_use_adiinviter.'"></div></center></td>


	<td align="center" class="adi_invite_limit_cont">
	<div class="adi_invite_limit_cont_div">
		<div class="adi_invite_limit_link">'.$last_num_invites.'</div>
		<div class="adi_invite_limit_out">
			<input type="hidden" class="adi_invite_limit_inp_hid" name="usergroupPerms['.$gid.']['.$adiinviter->last_num_invites_ind.']" value="'.$last_num_invites.'">
			<input type="textbox" class="txinput sml_txinput adi_invite_limit_inp">
			<div class="set_invite_limit_unlimited">Unlimited</div>
		</div>
	</div>
	</td>


	<td align="center"><center><div class="adi_ug_perms_'.$gid.' adi_img_checkbox '.($show_recaptcha == 1 ? 'adi_opt_yes' : 'adi_opt_no').'"><input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->show_recaptcha_ind.']" value="'.$show_recaptcha.'"></div></center></td>

	<td align="center"><center>'.
	(($gid == $guest_usergroup_id) ? '-<input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->can_delete_invites_ind.']" value="0">' : '<div class="adi_ug_perms_'.$gid.' adi_img_checkbox '.($can_delete_invites == 1 ? 'adi_opt_yes' : 'adi_opt_no').'"><input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->can_delete_invites_ind.']" value="'.$can_delete_invites.'"></div>')
	.'</center></td>

	<td align="center"><center>'.
	(($gid == $guest_usergroup_id) ? '-<input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->can_download_csv_ind.']" value="0">' : '<div class="adi_ug_perms_'.$gid.' adi_img_checkbox '.($can_download_csv == 1 ? 'adi_opt_yes' : 'adi_opt_no').'"><input type="hidden" name="usergroupPerms['.$gid.']['.$adiinviter->can_download_csv_ind.']" value="'.$can_download_csv.'"></div>')
	.'</center></td>

	</tr>';
}

?>
</tbody>
</table>
<div>
	<input style="float: right;" type="submit" value="Save Settings" class="btn_grn btn_left_space">
	<span style="float: right;margin: 7px 10px;" id="adi_ug_perms_resp"></span>
	<div class="clr"></div>
</div>
</form>
</div>
</center>
	</div>

<?php

}

?>