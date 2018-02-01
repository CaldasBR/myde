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


if(!isset($adiinviter))
{
	$base_path = dirname(__FILE__);
	include_once($base_path.DIRECTORY_SEPARATOR.'adi_init.php');
}

$do = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);
$adiinviter->requireSettingsList(array('global','db_info'));

$themes_list = $adiinviter->get_themes_list(true);
if(empty($do))
{
	if(count($themes_list) < 1)
	{
		?>
		<br><br><br><br><br><br><br><br>
		<center>
			<div class="no_data_head" style="color: #686868;">You have not installed any themes</div>
			<div class="no_data_txt">0 themes found.</div>
		</center>
		<?php
	}
	else 
	{
		if(isset($themes_list[$adiinviter->default_themeid]))
		{
			$ttt = $themes_list[$adiinviter->default_themeid];
			$new_arr = array( $adiinviter->default_themeid => $ttt );
			unset($themes_list[$adiinviter->default_themeid]);
			$themes_list = array_merge($new_arr, $themes_list);
		}

		$all_themes = array();
		if(count($themes_list) > 0)
		{
			foreach($themes_list as $theme_id => $details)
			{
				$is_portable = (isset($details['is_portable']) && $details['is_portable'] == 1) ? true : false;
				$all_themes[$theme_id] = $details;
			}
		}

		$installed_themes_list = $adiinviter->settings['adiinviter_themes_list'];

		?>

		<div class="themes_list_form_out" style="padding:20px;">
		<form class="settings_list">

		<div style="margin:0px 0px 45px 10px;">
			<div class="ate_ho" style="margin-bottom:10px;">
				<div class="opts_head">Free Themes<a href="http://www.adiinviter.com/docs/themes" class="adi_docs_link" target="_blank" style="font-weight: bold;">Themes Reference Documents</a></div>
			</div>
			<div class="pp1">You can download ready-made themes for AdiInviter Pro from our official themes directory.</div>
			<br>
			<a href="http://www.adiinviter.com/addons/themes" target="_blank" class="pp1 lnk1">Download Free Themes</a>
		</div>

		<?php if(count($all_themes) > 0) { ?>
			<table class="settings_table plugins_table" style="margin-bottom:10px;" width="100%" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th width="40" align="center">Default</th>
					<th align="left">Name</th>
					<th width="80" align="center">Theme Id</th>
					<th width="150" align="center">Author</th>
					<?php if(count($all_themes) > 1) { ?>
					<th width="80" align="center" class="pluginis_last_col"><center></center></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php 
			$curr_theme_id = $adiinviter->current_themeid;

			$counter = 1;
			$odd = true;
			$css_cls = '';
			foreach($all_themes as $theme_id => $theme_details)
			{
				$odd = !$odd;
				$css_cls = ($odd ? ' class="odd"' : '');
				$css_id = '';

				$author_name = '-';
				$author_name = $theme_details['author'];

				if($adiinviter->settings['adiinviter_theme'] == $theme_id)
				{
					$theme_cls = 'theme_checked';
					$radio_checked = ' checked="true"';
				}
				else
				{
					$theme_cls = 'theme_unchecked';
					$radio_checked = '';
				}
  				
  				$css_id = 'adi_theme_'.$theme_id;
				
				echo '<tr'.$css_cls.'>
				<td valign="middle" align="center"><center>';
				if(isset($installed_themes_list[$theme_id]))
				{
					echo '<div class="themes_onoff '.$theme_cls.'" id="'.$css_id.'" id="'.$css_id.'"><input type="radio" name="subsettings[global][adiinviter_theme]" value="'.$theme_id.'" '.$radio_checked.'></div>';
				}
				else {
					echo '-';
				}

				echo '</center></td>
				<td style="height:35px;"><span>'.$theme_details['name'].'</span></td>
				<td><center>'.$theme_id.'</center></td>
				<td><center>'.$author_name.'</center></td>';

				if(count($all_themes) > 1)
				{
					echo '<td class="centered_td pluginis_last_col">
					<div class="lang_actions" style="float:none;text-align:left;">
						';
					if($theme_id != $adiinviter->default_themeid)
					{
						if(isset($installed_themes_list[$theme_id]))
						{
							echo '<input type="button" class="btn_grn btn_small theme_remove" data="'.$theme_id.'" value="Remove">';
						}
						else
						{
							echo '<input type="button" class="btn_grn btn_small theme_install" data="'.$theme_id.'" value="Install">';
						}
					}

					echo '</div>
					</td>';
				}
				
				echo '</tr>';
			}
			?>
			</tbody>
			</table>
		<?php } ?>


		<!-- <hr class="bef_submit"> -->
		<div class="cont_submit">
			<?php if(count($themes_list) > 1) { ?>
			<input type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_settings">
			<?php } ?>
		</div>
		</form>
	</div>


	<div class="adi_themes_list_updater">
	<?php 
$default_themeid = $adiinviter->default_themeid;
$options = array();
foreach($installed_themes_list as $theme_id => $details)
{
	$options[] = array($theme_id => (!empty($details['name'])?$details['name']:$theme_id));
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
	<?php
	}
}
else if($do == 'theme_install')
{
	$install_success = false;
	$theme_id = AdiInviterPro::POST('theme_id', ADI_STRING_VARS);
	if(!empty($theme_id))
	{
		$adi_themes = adi_allocate_pack('Adi_Themes');
		if($result = $adi_themes->install_theme($theme_id))
		{
			$install_success = true;
		}
	}

	if($install_success)
	{
		if(count($adiinviter->settings['adiinviter_themes_list']) > 2)
		{
			echo 'adi.loadSettings(adi.currentSettings);';
		}
		else
		{
			echo 'window.location.reload();';
		}
	}
	else
	{
		echo "adi_notif.show_failure('Failed to install AdiInviter theme.');";
	}
}
else if($do == 'theme_remove')
{
	$uninstall_success = false;
	$theme_id = AdiInviterPro::POST('theme_id', ADI_STRING_VARS);
	if(!empty($theme_id))
	{
		$adi_themes = adi_allocate_pack('Adi_Themes');
		if($result = $adi_themes->uninstall_theme($theme_id))
		{
			$uninstall_success = true;
		}
	}

	if($uninstall_success)
	{
		echo 'adi.loadSettings(adi.currentSettings);';
	}
	else
	{
		echo "adi_notif.show_failure('Failed to uninstall AdiInviter theme.');";
	}
}

?>