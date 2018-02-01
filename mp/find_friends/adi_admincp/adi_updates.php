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


$adiinviter->requireSettingsList(array('global','updates'));

// If Cron job is not set this code will check for new updates only once in a day.
$adiinviter->init_update_checker();

$download_updates_link = $adiinviter->settings['download_updates_link'];
$replace_vars = array(
	'build_id' => $adiinviter->settings['adi_package_build_id'],
);
$download_updates_link = adi_replace_vars($download_updates_link, $replace_vars);;

$final_list = array();
if(count($adiinviter->settings['adi_updates_list']) > 0)
{
	$all_build_ids = array_keys($adiinviter->settings['adi_updates_list']);
	rsort($all_build_ids, SORT_NUMERIC);
	foreach($all_build_ids as $build_id)
	{
		if(isset($adiinviter->settings['adi_updates_list'][$build_id]))
		{
			$updates = $adiinviter->settings['adi_updates_list'][$build_id];
			if($build_id > $adiinviter->settings['adi_package_build_id'])
			{
				$final_list[$build_id] = $updates;
			}
		}
	}
}

$updates_available = false;
if(count($final_list) > 0)
{
	$updates_available = true;
}
?>

<div style="margin:10px;">
	<form method="post" action="" class="settings_list">
	<table cellpadding="0" cellspacing="0" border="0" class="opts_table up_table_out" style="width:100%;">
		<tr class="first">
			<td>
				<?php
				$notif_email = $adiinviter->settings['adiinviter_email_notification'];
				$css_class = '';
				if(empty($notif_email))
				{
					$css_class = 'txt_with_def';
					$notif_email = 'Email Address';
				}
				?>
				Send Update Notifications To  : 
				<input type="text" class="txinput txt_def_box <?php echo $css_class; ?>" name="subsettings[global][adiinviter_email_notification]" value="<?php echo $notif_email; ?>" style="width: 200px;" spellcheck="false" autocomplete="off">
				<input type="submit" class="btn_grn" name="form_submit" value="Save" style="margin-left:8px;">
			</td>
			<td style="text-align:right;">
			<?php
			if($updates_available)
			{
				echo '<a href="'.$download_updates_link.'" class="adi_link" target="_blank"><span class="label_red_note">Click Here</span></a> to download updates.';
			}
			?>
			</td>
		</tr>
		<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
	</table>
<?php
if($updates_available)
{
?>
<div style="margin:10px;">
	<?php
	$total_releases_cnt = count($final_list);
	foreach($final_list as $build_id => $updates)
	{
	?>
	<table cellpadding="0" cellspacing="0" border="0" class="adi_up_update">
		<tr>
			<td valign="top" class="adi_aaa" style="padding-right: 3px;">
				<table width="100%" cellpadding="0" cellspacing="0" style="margin-right:2px;">
				<tr>
					<td valign="top" width="50">

						<center><span class="adi_up_subheader"><?php echo $updates['type']; ?></span></center>
						<span class="adi_up_header"><?php echo $updates['display_id']; ?></span>
						<center><span class="adi_up_subheader"><?php echo date("M j, Y", $updates['date']); ?></span></center>

					</td>
					<td class="adi_up_hpline"></td>
				</tr></table>
			</td>
			<td valign="top">
				<ul class="adi_up_points">
					<?php 
					$total_cnt = count($updates['updates'])-1;
					foreach($updates['updates'] as $ind => $details)
					{
						$css_cls = 'up_pl_middle';
						if($ind == 0) {
							$css_cls = 'up_pl_first';
						}
						else if($ind == $total_cnt) {
							$css_cls = 'up_pl_last';
						}
						echo '<li class="adi_bbbb up_pline '.$css_cls.'"><div class="adi_point adi_point_type'.$details[0].'">'.$details[1].'</div></li>';
					} ?>
				</ul>
			</td>
		</tr>
	</table>
	<?php } ?>
</div>
<?php
}
else
{
?>
	<br><br><br><br><br><br><br><br>
	<center>
		<img src="adi_css/no_updates_new.png">
		<div class="no_data_head" style="color: #686868;">AdiInviter Pro is up-to-date</div>
		<div class="no_data_txt">0 new updates are available.</div>
	</center>
	<?php
}
?>
</form>
</div>