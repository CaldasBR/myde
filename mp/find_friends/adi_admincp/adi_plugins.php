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
$adiinviter->init_plugins();
$adiinviter->requireSettingsList(array('global','db_info','plugins'));

$adi_get = AdiInviterPro::POST('adi_get', ADI_STRING_VARS);
$adi_do  = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);


if($adi_do == 'install')
{
	$data = AdiInviterPro::POST('data');
	$plugin_id = $data;
	$plugin_filepath = ADI_PLUGINS_PATH . $data . '.php';

	include_once(ADI_LIB_PATH.'adiinviter_plugins.php');
	if(!empty($plugin_id) && file_exists($plugin_filepath))
	{
		include_once($plugin_filepath);
		$adi_plugins = new Adi_Plugin_Handler();
		$adi_plugins->init($adiinviter);

		if($plugin_id = $adi_plugins->install_plugin($plugin_id))
		{
			$action_result = true;
		}
		else
		{
			$action_result = "Failed to install '".$plugin_id."'.";
		}
	}
	exit;
}


if(empty($adi_get))
{

if($adiinviter->db_allowed)
{
	$all_plugins_list = $adiinviter->get_all_plugins_list();

	if(AdiInviterPro::isPOST('remove_plugin'))
	{
		$plugin_id = AdiInviterPro::POST('remove_plugin', ADI_STRING_VARS);
		
		if(!empty($plugin_id))
		{
			$plugins_handler = new Adi_Plugin_Handler();
			$plugins_handler->init($adiinviter);
			
			$list = $plugins_handler->uninstall_plugin($plugin_id);
			$plugins_handler->update_plugins_list();
		}
		exit;
	}

	if(AdiInviterPro::isPOST('execute_plugin'))
	{
		$adiinviter->cron_mode = true;
		
		$plugin_id = AdiInviterPro::POST('execute_plugin', ADI_STRING_VARS);
		
		if(!empty($plugin_id))
		{
			$settings = adi_getSetting($plugin_id);
			if(count($settings) > 0)
			{
				$settings['plugin_on_off'] = 1;
				$adiinviter->adi_execute_plugin($plugin_id, $settings, false);
			}
		}
		$mails_count = $adiinviter->getMailQueueCount();
		if($mails_count > 0)
		{
			echo ' $(".adint_sendmail_cron_count").html("'.$mails_count.'"); ';
		}
		else
		{
			echo ' $(".adi_notice_sendmail_cron").hide(); ';
		}
		exit;
	}

	?>
	<form method="post" action="" class="settings_list">
	<?php

	if(is_array($all_plugins_list) && count($all_plugins_list) > 0)
	{
	?>
	<div class="adi_plugins_list">

<div style="padding:10px 20px;">
	<div class="opts_head" style="padding: 10px 0px 15px 0px;">
	<!-- F j, Y, g:i a -->
		Current Server Time : <?php echo date('r', $adiinviter->adi_get_utc_timestamp()); ?>
		<a href="http://www.adiinviter.com/docs/cron-job" class="adi_docs_link" target="_blank">Reference Documentation</a>
	</div>

	<div class="" style="color: #444;">
	<table class="settings_table plugins_table" style="margin-bottom:20px;" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th align="left">Scheduled Tasks</th>
			<th width="150" align="left">Schedule</th>
			<th width="120" align="left">Next Execution</th>
		</tr>
	</thead>
	<tbody>
	<?php
	$odd = true;
	$css_cls = '';

	// Get Installed plugins list
	$plugin_settings = array(); $sorted_list = array();
	$result = adi_build_query_read('fetch_setting_groups_like');
	while($row = adi_fetch_array($result))
	{
		if(!isset($plugin_settings[$row['group_name']]))
		{
			$plugin_settings[$row['group_name']] = array();
		}
		$plugin_settings[$row['group_name']][$row['name']] = $row['value'];

		if($row['name'] == 'plugin_next_time')
		{
			$sorted_list[$row['group_name']] = $row['value'];
		}
	}
	asort($sorted_list);
	$sorted_list = array_keys($sorted_list);


	// Get Uninstalled plugins list
	foreach($all_plugins_list as $plugin_id)
	{
		if(!in_array($plugin_id, $sorted_list))
		{
			$sorted_list[] = $plugin_id;
		}
	}


	foreach($sorted_list as $plugin_group_id)
	{
		// $plugin_id = $row['group_name'];
		$plugin_id = $plugin_group_id;
		// $settings = adi_getSetting($plugin_id);
		$settings = array(); $is_installed = false;
		if(isset($plugin_settings[$plugin_group_id]))
		{
			$settings = $plugin_settings[$plugin_group_id];
			$is_installed = true;
		}
		else
		{
			$main_handler = new Adi_Scheduled_Plugin();

			$plugin_file = ADI_PLUGINS_PATH.$plugin_group_id.'.php';
			include_once($plugin_file);
			if(class_exists($plugin_group_id))
			{
				$handler = new $plugin_group_id();
				$settings = isset($handler->default_settings) ? $handler->default_settings : array();
				$settings = array_merge($main_handler->default_settings, $settings);
			}
		}
		
		if(is_array($settings) && count($settings) > 0)
		{
			$odd = !$odd;
			$css_cls = ($odd ? ' class="odd"' : '');

			?><tr <?php echo $css_cls; ?>><?php

			$plugin_title = $settings['plugin_title'];
			$plugin_description = $settings['plugin_description'];

			?></td>
			<td valign="top">
				<div style="min-height:50px;vertical-align:top;">
					<span class="opts_head"><?php echo $plugin_title; ?></span><br>
					<span class="opts_note"><?php echo $plugin_description; ?></span>
				</div>
				<div class="plugins_actions_out">
					<?php 
					if($is_installed)
					{ ?>
						<input style="float: left;" type="button" value="Settings" class="btn_blue btn_small plugin_edit" id="adi_save_settings" data="<?php echo $plugin_id; ?>">
						<input style="float: left;" type="button" value="Remove" class="btn_grn btn_small adi_btn_space_left plugin_remove" id="adi_save_settings" data="<?php echo $plugin_id; ?>">
						<input type="button" class="btn_blue1 btn_small adi_btn_space_left plugin_execute" rel="German" data="<?php echo $plugin_id; ?>" value="Execute">
					<?php } else { ?>
						<input style="float: left;" type="button" value="Install" class="btn_blue1 btn_small plugin_install" id="adi_save_settings" data="<?php echo $plugin_id; ?>">
					<?php } ?>
					<div class="clr"></div>
				</div>
			</td>
			<?php

			if((int)$settings['plugin_duration_type'] === 0)
			{
				echo '<td class="centered_td"><div>After each ';
				$txt = array();
				if($settings['plugin_num_days']+0 > 0) {
					$txt[] = '<b>'.$settings['plugin_num_days'].'</b> day(s)';
				}
				if($settings['plugin_num_hours']+0 > 0) {
					$txt[] = '<b>'.$settings['plugin_num_hours'].'</b> hour(s)';
				}
				if($settings['plugin_num_minutes']+0 > 0) {
					$txt[] = '<b>'.$settings['plugin_num_minutes'].'</b> minute(s)';
				}
				echo implode(' and ', $txt).'</div></td>';
			}
			else if((int)$settings['plugin_duration_type'] === 1)
			{
				$arr = array('','st','nd','rd'); $txt = 'th';
				if(isset($arr[$settings['plugin_date']]))
				{
					$txt = $arr[$settings['plugin_date']];
				}
				echo '<td class="centered_td"><div> On <b>'.$settings['plugin_date'].$txt.'</b> date and <b>'.$settings['plugin_hour'].':00</b> hour of the month.</div></td>';
			}
			else
			{
				echo '<td class="centered_td"><b>-</b></td>';
			}

			if($is_installed)
			{
				$cur_time = $adiinviter->adi_get_utc_timestamp();
				$plugin_next_time = $settings['plugin_next_time'];
				if($cur_time > $plugin_next_time)
				{
					// If next_time execution timestamp is older than current timestamp, then this plugin will be executed when cron executer is executed.
					$plugin_next_time = $cur_time + 60 - ($cur_time % 60);
				}
				echo '<td class="centered_td">'.date('r', $plugin_next_time).'</td>';
			}
			else
			{
				echo '<td class="centered_td"> - </td>';
			}

			?>
		</tr>
			<?php
		}
	}
	?>
	</tbody>
	</table>
</div>

</div>
	<?php
	}
	else
	{
		?>
		<br><br><br><br><br><br><br><br>
		<center>
			<div class="no_data_head" style="color: #686868;">AdiInviter Pro plugins are not installed</div>
			<div class="no_data_txt">0 plugins are available.</div>
		</center>
		<?php
	}
}

?>	</div>
</form>


<form method="post" class="adi_edit_plugin_form">
<input type="hidden" name="adi_get" value="save_edit_plugin_form">
<div class="adi_editplugin_outer" style="display:none;"></div>
</form>

<?php

}
else if($adi_get == 'save_edit_plugin_form')
{
	$subsettings = AdiInviterPro::POST('subsettings', ADI_ARRAY_VARS);
	// Submit edit plugin form
	if(AdiInviterPro::isPOST('subsettings') && is_array($subsettings) && count($subsettings) > 0)
	{
		foreach($subsettings as $plugin_id => $plugin_details)
		{
			$plugin_path = ADI_PLUGINS_PATH . $plugin_id.'.php';
			if(file_exists($plugin_path) )
			{
				include_once($plugin_path);
				$plugin = new $plugin_id();
				$plugin->adi = $adiinviter;

				$succ_flag = $plugin->save_settings($plugin_details);

				if($succ_flag === true) {
					echo 'adi_notif.show_success("Plugin settings stored Successfully.");';
				}
				else {
					echo 'adi_notif.show_failure("'.$succ_flag.'");';
				}
			}
			else
			{
				$adiinviter->throwErrorDesc('opt.save_edit_plugin_form : Plugin file not found : '.$plugin_path);
			}
		}
	}
}
else if($adi_get == 'edit_plugin_form')
{
	$plugin_id = AdiInviterPro::POST('plugin_id', ADI_STRING_VARS);

	if(!empty($plugin_id))
	{
		$settings = adi_getSetting($plugin_id);
		if(is_array($settings) && count($settings) > 0)
		{
			$found = false;
			$plugin_file = ADI_PLUGINS_PATH . $plugin_id.'.php';
			if(file_exists($plugin_file)) 
			{
				include_once($plugin_file);
				$found = true;
			}

		?>

		<div style="margin:15px 5px;">

		<div style="margin:0px 15px 25px 15px;">
			<label class="opts_head"><?php echo $settings['plugin_title']; ?></label><br>
			<label class="opts_note"><?php echo $settings['plugin_description']; ?></label>
		</div>

		<div class="adi_inner_sect" style="margin-bottom:35px;">
			<div class="adi_inner_sect_header">Task Details<?php
			if($plugin_id == 'Adi_Plugin_Sendmail')
			{
				echo '<a href="http://www.adiinviter.com/docs/references-scheduled" class="adi_docs_link" target="_blank">Reference Documentation</a>';
			}
			else if($plugin_id == 'Adi_Plugin_Invitation_Reminder')
			{
				echo '<a href="http://www.adiinviter.com/docs/references-reminders" class="adi_docs_link" target="_blank">Reference Documentation</a>';
			}
			else if($plugin_id == 'Adi_Plugin_Renew_Limit')
			{
				echo '<a href="http://www.adiinviter.com/docs/references-renew" class="adi_docs_link" target="_blank">Reference Documentation</a>';
			}
			?></div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table small_opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<span class="opts_head">File Name</span><br>
						<label class="opts_note">Task file name.</label><br>
					</td>
					<td valign="top">
						<div class=""><?php echo $plugin_id; ?>.php</div>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Class Name</span><br>
						<label class="opts_note">Task class name.</label><br>
					</td>
					<td valign="top">
						<div class=""><?php echo $plugin_id; ?></div>
					</td>
				</tr>

				<?php 
				if(isset($settings['warning_message']))
				{ ?>
					<tr>
						<td class="label_box">
							<span class="opts_head">Warning</span><br>
							<label class="opts_note">Developer warning.</label><br>
						</td>
						<td valign="top">
							<div class="plugins_warning_msg"><?php echo $settings['warning_message']; ?></div>
						</td>
					</tr>
				<?php }
				?>
				<tr>
					<td class="label_box">
						<span class="opts_head">Task On/Off</span><br>
						<label class="opts_note">Globally turn this task On/Off.</label>
					</td>
					<td>
						<?php
						$val = $settings['plugin_on_off'];
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
							<input type="hidden" name="subsettings[<?php echo $plugin_id; ?>][plugin_on_off]" value="<?php echo $val; ?>" class="switch_val">
						</p>
					</td>
				</tr>
			</table>
			</div>
		</div>

		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Execution Schedule Settings</div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Execution Schedule Type</span><br>
						<label class="opts_note">Choose if you want to execute this task monthly or periodically.</label><br>
					</td>
					<td valign="top">
						<?php 
						$duration = (int)$settings['plugin_duration_type']; 
						$adi_absolute = 'radio_btn'; $adi_relative='radio_btn';
						$adi_absolute_info = ''; $adi_relative_info = '';
						if($duration == 0) 
						{
							$adi_relative = 'radio_btn_current';
							$adi_relative_info = '';
							$adi_absolute_info = 'display:none;';
						}
						else 
						{
							$adi_absolute = 'radio_btn_current';
							$adi_relative_info = 'display:none;';
							$adi_absolute_info = '';
						}
						?>
						<div class="radio_buttons" name="subsettings[<?php echo $plugin_id; ?>][plugin_duration_type]">
							<span class="<?php echo $adi_relative; ?> adi_relative_duration"  data="0">Periodic Execution</span>
							<div class="radio_sep"></div>
							<span class="<?php echo $adi_absolute; ?> adi_absolute_duration" data="1">Time of the month</span>
						</div>
					</td>
				</tr>
			</table>

			<?php
				$plugin_num_days    = $settings['plugin_num_days']+0;
				$plugin_num_hours   = $settings['plugin_num_hours']+0;
				$plugin_num_minutes = $settings['plugin_num_minutes']+0;
			?>
			<div class="adi_relative_duration_div" style="<?php echo $adi_relative_info; ?>">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Duration In Days</span><br>
						<label class="opts_note">After how many days you want to execute this task.</label>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[<?php echo $plugin_id; ?>][plugin_num_days]" value="<?php echo $plugin_num_days; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Duration In Hours</span><br>
						<label class="opts_note">Specify number of hours after above days.</label>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[<?php echo $plugin_id; ?>][plugin_num_hours]" value="<?php echo $plugin_num_hours; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Duration In Minutes</span><br>
						<label class="opts_note">Specify number of minutes after above hours.</label>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[<?php echo $plugin_id; ?>][plugin_num_minutes]" value="<?php echo $plugin_num_minutes; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				</table>
			</div>
			
			<?php
				$plugin_date = min(31, max(1, $settings['plugin_date']+0));
				$plugin_hour = min(23, max(0, $settings['plugin_hour']+0));
			?>
			<div class="adi_absolute_duration_div" style="<?php echo $adi_absolute_info; ?>">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Day of the month</span><br>
						<label class="opts_note">Specify day of the month to execute this task.</label><br>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[<?php echo $plugin_id; ?>][plugin_date]" value="<?php echo $plugin_date; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Hour of the day</span><br>
						<label class="opts_note">Specify hour of the day in above setting.</label>
					</td>
					<td valign="top">
						<input type="textbox" class="txinput reg adi_limited_chars_only" name="subsettings[<?php echo $plugin_id; ?>][plugin_hour]" value="<?php echo $plugin_hour; ?>" onlychars="nums" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				</table>
			</div>

			</div>
		</div>

			<?php

			$opts_html = array();
			$containers = array();

			if($found)
			{
				$class_name = $plugin_id;
				$class_obj = new $class_name();

				$existing_lang_ids = $adiinviter->get_installed_lang_ids(); 

				$iframe_count = 1;
				if(is_array($class_obj->custom_settings) && count($class_obj->custom_settings) > 0)
				{
					foreach($class_obj->custom_settings as $varname => $option)
					{
						$bbcodes_html = '';
						if(isset($option['bbcodes']) && is_array($option['bbcodes']) && count($option['bbcodes']) > 0)
						{
							$bbcodes_html .= '
							<lable class="">You can use following markups : </label><br>
							<div class="perm_table_scroll" style="max-height:500px; overflow:auto">
							<table class="perm_table" style="margin-bottom:5px;">
							<thead><tr><th>Markup</th><th>Meaning</th></tr></thead>
							<tbody>';
							$td_cnt = 1;
							foreach($option['bbcodes'] as $bb_varname => $bb_detail)
							{
								if($td_cnt % 20 == 0) { $bbcodes_html .= '<tr><th style="border-top-style:solid;">Syntax</th><th style="border-top-style:solid;">Meaning</th></tr>';}
								$bbcodes_html .= '<tr class="odd"><td>['.$bb_varname.']</td><td>'.$bb_detail.'</td></tr>';
								$td_cnt++;
							}
							$bbcodes_html .= '</tbody></table></div>';
						}

						switch ($option['type']) 
						{
							case 'textbox':
								$opts_html[] = '<tr>
				<td class="label_box">
					<span class="opts_head">'.$option['name'].'</span><br>
					<label class="opts_note">'.$option['description'].'</label>
					'.$bbcodes_html.'
				</td>
				<td valign="top">
					<input type="textbox" class="txinput reg" name="subsettings['.$plugin_id.']['.$varname.']" value="'.$settings[$varname].'" spellcheck="false" autocomplete="off">
				</td>
			</tr>';
							break;

							case 'textarea' :
							$subject_editor  = isset($option['subject_editor']) ? $option['subject_editor'] : false;
							$template_editor = isset($option['template_editor']) ? $option['template_editor'] : false;

							if($subject_editor == true)
							{
								$iframe_count++;
								$translations = '';
								
								$message_subject = isset($settings[$varname.'_en']) ? $settings[$varname.'_en'] : '';
								$translations .= '<div class="template_code_en" data="template_code_iframe'.$iframe_count.'" style="display:none;"><!-- '.$message_subject.' --></div>';
$containers[] = '
<div class="adi_inner_sect">
	<div class="adi_inner_sect_header">Email Subject</div>
	<div class="adi_inner_sect_body">
<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
<tr>
	<td>
		<span class="opts_head">'.$option['name'].'</span><br>
		<label class="opts_note">'.$option['description'].'</label>
	</td>
</tr>
<tr>
	<td>
		<div class="template_preview">
			'.$translations.'
			<iframe id="template_code_iframe'.$iframe_count.'" class="tmpl_cd_iframe" width="100%" style="min-width:600px;max-height:50px;"></iframe>
		</div>
	</td>
</tr>
</table>
</div></div>

<div class="cont_submit" style="padding: 0px 10px 15px;">
	<!-- <input type="button" value="Edit" data="tmpl_cd_invitation_subject" class="btn_org tmpl_open_shorteditor"> -->
	<input type="button" value="Edit" data="tmpl_cd_invitation_subject" data-sg="'.$plugin_id.'" data-sn="'.$varname.'" data-sid="task" data-cupdate="template_code_iframe'.$iframe_count.'" class="btn_org tmpl_open_teditor">
</div>
';
							}
							else if($template_editor == true)
							{
								$iframe_count++;
								$translations = '';
								
								$template_body = isset($settings[$varname.'_en']) ? $settings[$varname.'_en'] : '';
								$translations .= '<div class="template_code_en" data="template_code_iframe'.$iframe_count.'" style="display:none;"><!-- '.$template_body.' --></div>';
$containers[] = '
<div class="adi_inner_sect">
	<div class="adi_inner_sect_header">Email Body</div>
	<div class="adi_inner_sect_body">
<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
<tr>
	<td class="label_box">
		<span class="opts_head">'.$option['name'].'</span><br>
		<label class="opts_note">'.$option['description'].'</label>
	</td>
</tr>
<tr>
	<td>
		<div class="template_preview">
			'.$translations.'
			<iframe id="template_code_iframe'.$iframe_count.'" class="tmpl_cd_iframe" width="100%"></iframe> 
		</div>
	</td>
</tr>
</table>
</div></div>

<div class="cont_submit" style="padding: 0px 10px 15px;">
	<!-- <input type="button" value="Edit" data="tmpl_cd_invitation_body" class="btn_org tmpl_open_editor"> -->
	<input type="button" value="Edit" data="tmpl_cd_invitation_body" data-sg="'.$plugin_id.'" data-sn="'.$varname.'" data-sid="task" data-cupdate="template_code_iframe'.$iframe_count.'" class="btn_org tmpl_open_teditor">
</div>';
							}
							else
							{

							}
							break;
							
							default: break;
						}
					}
				}
			}

			if(count($opts_html) > 0) 
			{
				echo '
				<div class="adi_inner_sect_sep"></div>
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header">Task Settings</div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">';
			echo implode("\n", $opts_html);
		echo '</table>
		</div></div>';
			}
			?>

			<div class="cont_submit" style="padding: 0px 10px 15px;">
				<span style="float: left;margin: 7px 10px;" id="adi_edit_plugin_response"></span>
				<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_settings">
				<input style="float: right;" type="button" value="Back" class="btn_org adi_plugins_cancel">
				<div class="clr"></div>
			</div>

			<?php
			if(count($containers) > 0)
			{
				echo implode('', $containers);
			}
			?>

			</div>
		<?php
		}
	}
	else {
		?><div class=""><font color="red"><i>Invalid plugin ID.</i></font></div><?php
	}

}


?>