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

if(!isset($adiinviter))
{
	$admin_path = dirname(__FILE__);
	include_once($admin_path.DIRECTORY_SEPARATOR.'adi_init.php');
	$admin_path = dirname(__FILE__);

	$adiinviter->requireSettingsList(array('global','db_info'));
}

$get_code = isset($get_code) ? $get_code : AdiInviterPro::POST('get_code', ADI_STRING_VARS);
$user_table_name = isset($user_table_name) ? $user_table_name : AdiInviterPro::POST('user_table_name', ADI_STRING_VARS);

$usergroup_mapping_html = '';
$avatar_mapping_html = '';

if($get_code == 'get_fields_list')
{
	sleep(1);
	$table_name = AdiInviterPro::POST('table_name', ADI_STRING_VARS);
	if(!empty($table_name))
	{
		$table_exists = false;
		$result = adi_build_query_read('check_for_table', array(
			'query_text' => $table_name,
		));
		$all_tables = array();
		while($row = adi_fetch_array($result))
		{
			$table_exists = true;
		}
		if($table_exists)
		{
			$result = adi_build_query_read('check_table_structure', array(
				'table_name' => $table_name,
			));
			$columns = array();
			while($row = adi_fetch_array($result))
			{
				$columns[] = $row['Field'];
			}

			$options   = array();
			foreach($columns as $cname)
			{
				$options[] = array($cname => $cname);
			}
			echo adi_get_options_list($options);
		}
	}
}




if($get_code == "user_table_details" || $get_code == "avatar_table_details")
{
	$avatar_table_name = $adiinviter->settings['avatar_table']['table_name'];
	$avatar_table_name = AdiInviterPro::isPOST('avatar_table_name', ADI_STRING_VARS) ? AdiInviterPro::POST('avatar_table_name', ADI_STRING_VARS) : $avatar_table_name;


	if(!empty($avatar_table_name))
	{
		$result = adi_build_query_read('check_table_structure', array(
			'table_name' => $avatar_table_name,
		));
		$avatar_columns = array();
		while($row = adi_fetch_array($result))
		{
			$avatar_columns[] = $row['Field'];
		}

	}

	$userid_field = $adiinviter->settings['avatar_table']['userid'];
	$avatar_field = $adiinviter->settings['avatar_table']['avatar'];

	$avatar_mapping_html .= '
	<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
	<tr>
		<td class="label_box">
			<span class="opts_head">User Id Field</span><br>
			<label class="opts_note">Select a field name in your <span class="dcvar adi_avatar_table_update_here">'.($avatar_table_name != '' ? $avatar_table_name : 'Avatar Table').'</span> table where <span class="dcvar2">User Ids</span> are stored.</label>
		</td>
		<td>';

	$options = array();
	// $options[] = array('' => 'Select Attribute');
	if(!empty($avatar_table_name))
	{
		foreach($avatar_columns as $cname)
		{
			$options[] = array($cname => $cname);
		}
	}
	$details = array(
		'input_name'     => 'subsettings[db_info][avatar_table][userid]',
		'input_class'    => 'adi_avatar_mapping_userid_field',
		'default_option' => $userid_field,
		'options'        => $options,
		'default_text'   => 'Select Attribute',
	);
	$avatar_mapping_html .= adi_get_select_plugin($details, $type = "down");

	$avatar_mapping_html .= '</td>
	</tr>
	<tr>
		<td class="label_box">
			<span class="opts_head">Avatar Value Field</span><br>
			<label class="opts_note">Select a field name in your <span class="dcvar adi_avatar_table_update_here">'.($avatar_table_name != '' ? $avatar_table_name : 'Avatar Table').'</span> table where <span class="dcvar2">Avatar Values</span> are stored.</label>
		</td>
		<td>';

	$options = array();
	// $options[] = array('' => 'Select Attribute');
	if(!empty($avatar_table_name))
	{
		foreach($avatar_columns as $cname)
		{
			$options[] = array($cname => $cname);
		}
	}
	$details = array(
		'input_name'     => 'subsettings[db_info][avatar_table][avatar]',
		'input_class'    => 'adi_avatar_mapping_avatar_field',
		'default_option' => $avatar_field,
		'options'        => $options,
		'default_text'   => 'Select Attribute',
	);
	$avatar_mapping_html .= adi_get_select_plugin($details, $type = "down");

	$avatar_mapping_html .= '
		</td>
	</tr>
	</table>';

	if($get_code == "avatar_table_details")
	{
		echo $avatar_mapping_html;
	}
}


if($get_code == "user_table_details")
{
	$result = adi_build_query_read('get_all_tables');
	$all_tables = array();
	while($row = adi_fetch_array($result))
	{
		$all_tables[] = array_shift($row);
	}

	if(!in_array($user_table_name, $all_tables))
	{
		echo '<div class="adi_db_info_err">"'.$user_table_name.'" table does not exist.</div><br><br>';
		exit;
	}

	$result = adi_build_query_read('check_table_structure', array(
		'table_name' => $user_table_name,
	));
	$columns = array();
	while($row = adi_fetch_array($result))
	{
		$columns[] = $row['Field'];
	}


	?>
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">User Table Fields<a href="http://www.adiinviter.com/docs/user-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">User Id Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where <span class="dcvar2">User Ids</span> are stored.</label>
					</td>
					<td>
<?php
$userid_field = $adiinviter->settings['user_table']['userid'];
$options = array();
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][userid]',
	'input_class'    => 'adi_user_table_name',
	'default_option' => $userid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<tr>
					<td class="label_box">
						<span class="opts_head">Username Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where <span class="dcvar2">Usernames</span> are stored.</label>
					</td>
					<td>
<?php
$username_field = $adiinviter->settings['user_table']['username'];

$options = array();
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][username]',
	'input_class'    => 'adi_username_field_name',
	'default_option' => $username_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");


?>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Email Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where <span class="dcvar2">Email Addresses</span> are stored.</label>
					</td>
					<td>
<?php

$email_field = $adiinviter->settings['user_table']['email'];

$options = array();
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][email]',
	'input_class'    => 'adi_user_table_name',
	'default_option' => $email_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="adi_inner_sect_sep"></div>

	<!-- Full Username field -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">Full Name (First Name + Last Name)<a href="http://www.adiinviter.com/docs/user-system-integration#user-full-name" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box" style="line-height:25px;">
<?php

$userfullname_field = trim($adiinviter->settings['user_table']['userfullname']);
$fullname_attrs = array(); $multi_columns = false; $multi_columns_css='display:none;';
if(empty($userfullname_field))
{
	
}
else
{
	$fullname_attrs = preg_split('/,\s*/', $userfullname_field);
	if(count($fullname_attrs) > 1)
	{
		$multi_columns = true;
		$multi_columns_css = 'display:block;';
		$userfullname_field = '';
	}
	else
	{
		$userfullname_field = array_shift($fullname_attrs);
	}
}
?>
						<span class="opts_head">Full Name Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where the <span class="dcvar2">Full Names</span> users are stored.</label><br>
						<label class="opts_note">- Select <span class="hglt">Not Present</span>, if Full Names are not stored in <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table.</label><br>
						<label class="opts_note">- Select <span class="hglt1">Custom Fields</span>, if First Names and Last Names are stored in separate fields.</label><br>
					</td>
					<td>
<?php


$options   = array();
$options[] = array('' => array(2,'Not Present'));
$options[] = array('adi_multi_columns' => array(3,'Custom Fields'));
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => '',
	'input_class'    => 'adi_fullname_initial_val',
	'default_option' => ($multi_columns) ? 'adi_multi_columns' : $userfullname_field,
	'options'        => $options,
	'default_text'   => array(2,'Not Present'),
);
echo adi_get_select_plugin($details, $type = "down");


?>
						<input type="hidden" name="subsettings[db_info][user_table][userfullname]" value="<?php echo $adiinviter->settings['user_table']['userfullname']; ?>" class="adi_fullname_value_hid">
					</td>
				</tr>
			</table>
			<div class="adi_fullname_secondary_out" style="<?php echo $multi_columns_css; ?>">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">First Name Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where the <span class="dcvar2">First Names</span> are stored.</label>
					</td>
					<td>
<?php

$firstname_field = array_shift($fullname_attrs);

$options = array();
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => '',
	'input_class'    => 'adi_fullname_secondary_val1',
	'default_option' => $firstname_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");


?>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Last Name Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where the <span class="dcvar2">Last Names</span> are stored.</label>
					</td>
					<td>
<?php

$lastname_field = array_shift($fullname_attrs);

$options = array();
// $options[] = array('' => 'Select Attribute');
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => '',
	'input_class'    => 'adi_fullname_secondary_val2',
	'default_option' => $lastname_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				</table>
			</div>
		</div>
	</div>

	<div class="adi_inner_sect_sep"></div>


	<!-- Avatar Details -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">User -> Avatar Mapping<a href="http://www.adiinviter.com/docs/avatar-system-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box" style="line-height:22px;">
						<span class="opts_head">Avatar Value Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where <span class="dcvar2">Avatar Values</span> are stored.<br>
						- Avatar Values can be full url to images, ids, image filenames, hashes, etc.<br>
						- Select <span class="hglt">Not Required</span>, if Avatar Values are not stored in <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table. <br>
						- Select <span class="hglt1">Custom Table</span>, if Avatar Values are stored in separate database table. <br>
						<span class="label_red_note">Note:</span> This setting specifies Avatar Value associated to each individual user in your website.
						</label>
					</td>
					<td>
<?php
$avatar_field = $adiinviter->settings['user_table']['avatar'];
$show_diff_table = false;
$avatar_table = $adiinviter->settings['avatar_table'];
if($avatar_field == '' && $avatar_table['table_name'] != '' && $avatar_table['userid'] != '' && $avatar_table['avatar'] != '')
{
	$show_diff_table = true;
}
$options = array();
// $options[] = array('' => 'Select Attribute');
$options[] = array('' => array(2,'Not Required'));
$options[] = array('adi_diff_table' => array(3,'Custom Table'));
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][avatar]',
	'input_class'    => 'adi_avatar_field_cls',
	'default_option' => $show_diff_table ? 'adi_diff_table' : $avatar_field,
	'options'        => $options,
	'default_text'   => array(2,'Not Required'),
);
echo adi_get_select_plugin($details, $type = "down");

?>					</td>
				</tr>
			</table>
			<?php
			$cls_txt = '';
			$avatar_table = $adiinviter->settings['avatar_table'];
			if($avatar_table['table_name'] == '' && $avatar_table['userid'] == '' && $avatar_table['avatar'] == '')
			{
				$cls_txt = 'display:none;';
			}
			?>
			<div class="adi_avatar_secondary_out" style="<?php echo $cls_txt; ?>">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Table Name</span><br>
						<label class="opts_note">Select a table from your database which stores the <span class="dcvar2">User Avatar</span> information.</label>
					</td>
					<td>
<?php

$avatar_table_table_name = $adiinviter->settings['avatar_table']['table_name'];

$options   = array();
// $options[] = array('' => 'Select Avatar Table');
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][avatar_table][table_name]',
	'input_class'    => 'adi_avatar_table_name',
	'default_option' => $avatar_table_table_name,
	'options'        => $options,
	'default_text'   => 'Select Avatar Table',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				</table>
				<div class="adi_avatar_table_fields" style="<?php echo empty($avatar_table_table_name) ? 'display:none;' : ''; ?>">
					<?php echo $avatar_mapping_html; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="adi_inner_sect_sep"></div>

	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">User Avatar URL<a href="http://www.adiinviter.com/docs/avatar-system-integration#build-avatar-url" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Avatar URL</span><br>
						<label class="opts_note">
Construct an avatar URL using given markups to fetch user avatars in your website.<br>
- If full avatar urls are stored in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table, then just write [avatar_value].<br>
<div style="margin-bottom:10px;"><span class="label_red_note">Note:</span> Ignore this setting if you do not have avatar system in your website.</div>
For e.g. <br>
<div style="margin-left:10px; line-height:22px;">
	http://www.yourdomain.com/image.php?u=[userid]<br>
	http://www.yourdomain.com/images/avatars/[username]/x100.jpg<br>
	http://www.yourdomain.com/avatar.php?e=[email]&s=100<br>
	http://www.yourdomain.com/profile_pic.php?[userid]-[username]<br>
	http://www.gravatar.com/avatar/[email_md5]?s=160<br>
</div>
</label>
					</td>
					<td style="padding-top:20px;" valign="top">
						<input type="textbox" class="txinput reg1" name="subsettings[db_info][adiinviter_avatar_url]" value="<?php echo $adiinviter->settings['adiinviter_avatar_url']; ?>" spellcheck="false" autocomplete="off">
<table class="perm_table" style="margin-top:10px;" cellspacing="0" cellpadding="0">
<thead><tr><th class="perm_table_th">Markup</th><th class="perm_table_th">Meaning</th></tr></thead>
<tbody>
<tr><td class="synt_table_td">[userid]</td><td class="synt_table_td">User Id</td></tr>
<tr class="odd"><td class="synt_table_td">[username]</td><td class="synt_table_td">Username</td></tr>
<tr><td class="synt_table_td">[email]</td><td class="synt_table_td">Email Address</td></tr>
<tr><td class="synt_table_td">[email_md5]</td><td class="synt_table_td">Email Address MD5 Hash</td></tr>
<tr class="odd"><td class="synt_table_td">[avatar_value]</td><td class="synt_table_td">Avatar Value</td></tr>
</tbody></table>
					</td>
				</tr>
			</table>

		</div>
	</div>

	<div class="adi_inner_sect_sep"></div>

	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">User Profile URL<a href="http://www.adiinviter.com/docs/profile-page-integration" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Profile URL</span><br>
						<label class="opts_note">
Construct a user profile URL using given markups.<br>
<div style="margin-bottom:10px;"><span class="label_red_note">Note:</span> Ignore this setting if you do not have profile page system in your website.</div>
For e.g.<br>
<div style="margin-left:10px; line-height:22px;">
	http://www.yourdomain.com/[userid]<br>
	http://www.yourdomain.com/profile.php?[username]<br>
	http://www.yourdomain.com/user.php?e=[email] 
</div>
						</label>
					</td>
					<td style="padding-top:20px;" valign="top">
						<input type="textbox" class="txinput reg1" name="subsettings[db_info][adiinviter_profile_page_url]" value="<?php echo $adiinviter->settings['adiinviter_profile_page_url']; ?>" spellcheck="false" autocomplete="off">
<table class="perm_table" style="margin-top:10px;" cellspacing="0" cellpadding="0">
<thead><tr><th class="perm_table_th">Markup</th><th class="perm_table_th">Meaning</th></tr></thead>
<tbody>
<tr><td class="synt_table_td">[userid]</td><td class="synt_table_td">User Id</td></tr>
<tr class="odd"><td class="synt_table_td">[username]</td><td class="synt_table_td">Username</td></tr>
<tr><td class="synt_table_td">[email]</td><td class="synt_table_td">Email Address</td></tr>
</tbody></table>
					</td>
				</tr>
			</table>
		</div>
	</div>



	<div style="display:none;">

		<div class="adi_usergroup_html_code">
			
		</div>

		<div class="adi_friends_html_code">
			
		</div>
		
	</div>


	<!-- Save Button -->
		<tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr>
	</table>
	<div style="padding: 0px 10px 15px;">
		<span style="margin: 7px 0px;" id="checkUser_resp"></span>
		<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_user_details">
		<div class="clr"></div>
	</div>
	<?php
}

if($get_code == "usergroup_table_details")
{
	$user_table_name = isset($user_table_name) ? $user_table_name : AdiInviterPro::POST('user_table_name', ADI_STRING_VARS);
	$usergroup_table_name = isset($usergroup_table_name) ? $usergroup_table_name : AdiInviterPro::POST('usergroup_table_name', ADI_STRING_VARS);

	$result = adi_build_query_read('get_all_tables');
	$all_tables = array();
	while($row = adi_fetch_array($result))
	{
		$all_tables[] = array_shift($row);
	}

	$columns = array();
	if(!empty($user_table_name) && in_array($user_table_name, $all_tables))
	{
		$result = adi_build_query_read('check_table_structure', array(
			'table_name' => $user_table_name,
		));
		while($row = adi_fetch_array($result))
		{
			$columns[] = $row['Field'];
		}
	}

	$ug_columns = array();
	if(!empty($usergroup_table_name) && in_array($usergroup_table_name, $all_tables))
	{
		$result = adi_build_query_read('check_table_structure', array(
			'table_name' => $usergroup_table_name,
		));
		while($row = adi_fetch_array($result))
		{
			$ug_columns[] = $row['Field'];
		}
	}

	?>
	<!-- Usergroup Table Details -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">Usergroups Table Fields<a href="http://www.adiinviter.com/docs/usergroups-system-integration#usergroups-table" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Usergroup Id Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_usergroup_table_update_here"><?php echo $usergroup_table_name; ?></span> table where <span class="dcvar2">Usergroup Ids</span> are stored.</label><br>
					</td>
					<td>
<?php

$ug_usergroupid_field = $adiinviter->settings['usergroup_table']['usergroupid'];

$options = array();
foreach($ug_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_table][usergroupid]',
	'input_class'    => 'adi_ug_usergroupdid_field',
	'default_option' => $ug_usergroupid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Usergroup Name Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_usergroup_table_update_here"><?php echo $usergroup_table_name; ?></span> table where <span class="dcvar2">Usergroup Names</span> are stored.</label><br>
					</td>
					<td>
<?php

$ug_usergroupname_field = $adiinviter->settings['usergroup_table']['name'];

$options = array();
foreach($ug_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_table][name]',
	'input_class'    => 'adi_ug_usergroupdname_field',
	'default_option' => $ug_usergroupname_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="adi_inner_sect_sep"></div>

	<!-- Usergroupid Details -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">User -> Usergroup Mapping<a href="http://www.adiinviter.com/docs/usergroups-system-integration#user-usergroup-mapping" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Usergroup Id</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_user_table_update_here"><?php echo $user_table_name; ?></span> table where <span class="dcvar2">Usergroup Ids</span> are stored.
						<div style="margin-bottom:10px;"><span class="label_red_note">Note:</span> This setting specifies Usergroup associated to User in your website.</div></label>

						<label class="opts_note">Select <span class="hglt1">Custom Table</span>, if your website has separate table for mapping User -> Usergroup.</label><br>
					</td>
					<td>
<?php

$usergroupid_field = $adiinviter->settings['user_table']['usergroupid'];
$mapping_table = $adiinviter->settings['usergroup_mapping'];

$options = array();
$options[] = array('adi_diff_table' => array(3,'Custom Table'));
foreach($columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][user_table][usergroupid]',
	'input_class'    => 'adi_usergroupdid_field',
	'default_option' => ($mapping_table['table_name'] != '') ? 'adi_diff_table' : $usergroupid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
			</table>

			<?php
			$cls_txt = '';
			$mapping_table = $adiinviter->settings['usergroup_mapping'];
			if($mapping_table['table_name'] == '' && $mapping_table['userid'] == '' && $mapping_table['usergroupid'] == '')
			{
				$cls_txt = 'display:none;';
			}
			?>
			<div class="adi_usergroupid_secondary_out" style="<?php echo $cls_txt; ?>">
				<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Table Name</span><br>
						<label class="opts_note">Select a table in your database which stores User -> Usergroup mapping details.</label>
					</td>
					<td>
<?php

$usergroup_mapping_table_name = $adiinviter->settings['usergroup_mapping']['table_name'];

$options   = array();
foreach($all_tables as $tname)
{
	$options[] = array($tname => $tname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_mapping][table_name]',
	'input_class'    => 'adi_usergroup_map_table_name',
	'default_option' => $usergroup_mapping_table_name,
	'options'        => $options,
	'default_text'   => 'Select Usergroup Table',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				</table>
				<?php
				$cls_txt = '';
				$mapping_table = $adiinviter->settings['usergroup_mapping'];
				if($mapping_table['table_name'] == '' || $mapping_table['userid'] == '' || $mapping_table['usergroupid'] == '')
				{
					$cls_txt = 'display:none;';
				}
				?>
				<div class="adi_usergroupid_mapping_fields" style="<?php echo $cls_txt; ?>">
					<?php 
$usergroup_mapping_table_name = $adiinviter->settings['usergroup_mapping']['table_name'];
$usergroup_mapping_table_name = AdiInviterPro::isPOST('usergroup_mapping_table_name', ADI_STRING_VARS) ? AdiInviterPro::POST('usergroup_mapping_table_name', ADI_STRING_VARS) : $usergroup_mapping_table_name;

if(!empty($usergroup_mapping_table_name))
{
	$result = adi_build_query_read('check_table_structure', array(
		'table_name' => $usergroup_mapping_table_name,
	));
	$ugm_columns = array();
	while($row = adi_fetch_array($result))
	{
		$ugm_columns[] = $row['Field'];
	}
}

$userid_field = $adiinviter->settings['usergroup_mapping']['userid'];
$usergroupid_field = $adiinviter->settings['usergroup_mapping']['usergroupid'];
					?>

					<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
					<tr>
						<td class="label_box">
							<span class="opts_head">User Id Field</span><br>
							<label class="opts_note">Select a field name in your <span class="dcvar adi_usergroupmap_table_update_here"><?php echo $usergroup_mapping_table_name; ?></span> table where <span class="dcvar2">User Ids</span> are stored.</label>
						</td>
						<td>
<?php
$options = array();
if(!empty($usergroup_mapping_table_name))
{
	foreach($ugm_columns as $cname)
	{
		$options[] = array($cname => $cname);
	}
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_mapping][userid]',
	'input_class'    => 'adi_usergroupmapping_userid',
	'default_option' => $userid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");
?>
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Usergroup Id Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_usergroupmap_table_update_here"><?php echo $usergroup_mapping_table_name; ?></span> table where <span class="dcvar2">Usergroup Ids</span> are stored.</label>
					</td>
					<td>
<?php
$options = array();
if(!empty($usergroup_mapping_table_name))
{
	foreach($ugm_columns as $cname)
	{
		$options[] = array($cname => $cname);
	}
}
$details = array(
	'input_name'     => 'subsettings[db_info][usergroup_mapping][usergroupid]',
	'input_class'    => 'adi_usergroupmapping_usergroupid',
	'default_option' => $usergroupid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");
?>
					</td>
				</tr>
				</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Save Button -->
	</table>
	<div style="padding: 0px 10px 15px;">
		<span style="margin: 7px 0px;" id="checkUser_resp"></span>
		<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_usergroups_details">
		<div class="clr"></div>
	</div>

	<?php

} // Usergroup details ends here


if($get_code == "friends_table_details")
{
	$friends_table_name = isset($friends_table_name) ? $friends_table_name : AdiInviterPro::POST('friends_table_name', ADI_STRING_VARS);

	$result = adi_build_query_read('get_all_tables');
	$all_tables = array();
	while($row = adi_fetch_array($result))
	{
		$all_tables[] = array_shift($row);
	}

	$frd_columns = array();
	if(!empty($friends_table_name) && in_array($friends_table_name, $all_tables))
	{
		$result = adi_build_query_read('check_table_structure', array(
			'table_name' => $friends_table_name,
		));
		while($row = adi_fetch_array($result))
		{
			$frd_columns[] = $row['Field'];
		}
	}

	?>
	<!-- Friends Table Details -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">Friends Table Fields<a href="http://www.adiinviter.com/docs/friends-system-integration#friends-system-table" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">User Id Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_friends_table_update_here"><?php echo $friends_table_name; ?></span> table where <span class="dcvar2">User Ids</span> are stored.</label><br>
					</td>
					<td>
<?php

$frd_userid_field = $adiinviter->settings['friends_table']['userid'];

$options = array();
foreach($frd_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][userid]',
	'input_class'    => 'adi_frd_userid_field',
	'default_option' => $frd_userid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Friend Id Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_friends_table_update_here"><?php echo $friends_table_name; ?></span> table where <span class="dcvar2">Friend Ids</span> are stored.</label><br>
					</td>
					<td>
<?php

$friendid_field = $adiinviter->settings['friends_table']['friend_id'];

$options = array();
foreach($frd_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][friend_id]',
	'input_class'    => 'adi_frd_friendid_field',
	'default_option' => $friendid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Friendship Status Field</span><br>
						<label class="opts_note">Select a field name in your <span class="dcvar adi_friends_table_update_here"><?php echo $friends_table_name; ?></span> table where <span class="dcvar2">Friend Request Status</span> are stored.</label><br>
					</td>
					<td>
<?php

$status_field = $adiinviter->settings['friends_table']['status'];

$options = array();
foreach($frd_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][status]',
	'input_class'    => 'adi_frd_status_field',
	'default_option' => $status_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Status Value When Friend Request Is Sent And Waiting For Approval</span><br>
						<label class="opts_note">For e.g. Yes, No, Pending, Sent, Numeric values like 0, 1, etc.</label><br>
					</td>
					<td style="padding-top:20px;" valign="top">
						<input type="textbox" class="txinput reg1" name="subsettings[db_info][friends_table][pending_value]" value="<?php echo $adiinviter->settings['friends_table']['pending_value']; ?>" spellcheck="false" autocomplete="off">
						<div class="adi_input_field_info">*Case Sensitive</div>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<span class="opts_head">Status Value When Friend Request Is Accepted</span><br>
						<label class="opts_note">For e.g. Yes, No, Pending, Sent, Numeric values like 0, 1, etc.</label><br>
					</td>
					<td style="padding-top:20px;" valign="top">
						<input type="textbox" class="txinput reg1" name="subsettings[db_info][friends_table][yes_value]" value="<?php echo $adiinviter->settings['friends_table']['yes_value']; ?>" spellcheck="false" autocomplete="off">
						<div class="adi_input_field_info">*Case Sensitive</div>
					</td>
				</tr>

			</table>
		</div>
	</div>

	<!-- Save Button -->
	<div style="padding: 0px 10px 15px;">
		<span style="margin: 7px 0px;" id="checkFriends_resp"></span>
		<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_friends_details">
		<div class="clr"></div>
	</div>

	<?php

}





if($get_code == "followers_table_details")
{
	$followers_table_name = isset($followers_table_name) ? $followers_table_name : AdiInviterPro::POST('followers_table_name', ADI_STRING_VARS);

	$result = adi_build_query_read('get_all_tables');
	$all_tables = array();
	while($row = adi_fetch_array($result))
	{
		$all_tables[] = array_shift($row);
	}

	$frd_columns = array();
	if(!empty($followers_table_name) && in_array($followers_table_name, $all_tables))
	{
		$result = adi_build_query_read('check_table_structure', array(
			'table_name' => $followers_table_name,
		));
		while($row = adi_fetch_array($result))
		{
			$frd_columns[] = $row['Field'];
		}
	}

	?>
	<!-- Friends Table Details -->
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">Follower Table Fields<a href="http://www.adiinviter.com/docs/followers-system-integration#follower-system-table" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
		<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr>
					<td class="label_box">
						<span class="opts_head">Follower User Id Field</span><br>
						<label class="opts_note">Select field in your <span class="dcvar adi_followers_table_update_here"><?php echo $followers_table_name; ?></span> table which stores <span class="dcvar2">User Id</span> of the user sending follow request.</label><br>
					</td>
					<td>
<?php

$frd_userid_field = $adiinviter->settings['friends_table']['userid'];

$options = array();
foreach($frd_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][userid]',
	'input_class'    => 'adi_frd_userid_field',
	'default_option' => $frd_userid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
				<tr>
					<td class="label_box">
						<span class="opts_head">Followed User Id Field</span><br>
						<label class="opts_note">Select field in your <span class="dcvar adi_followers_table_update_here"><?php echo $followers_table_name; ?></span> table which stores <span class="dcvar2">User Id</span> of the user being followed.</label><br>
					</td>
					<td>
<?php

$friendid_field = $adiinviter->settings['friends_table']['friend_id'];

$options = array();
foreach($frd_columns as $cname)
{
	$options[] = array($cname => $cname);
}
$details = array(
	'input_name'     => 'subsettings[db_info][friends_table][friend_id]',
	'input_class'    => 'adi_frd_friendid_field',
	'default_option' => $friendid_field,
	'options'        => $options,
	'default_text'   => 'Select Attribute',
);
echo adi_get_select_plugin($details, $type = "down");

?>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<input type="hidden" name="subsettings[db_info][friends_table][status]" value="">
	<input type="hidden" name="subsettings[db_info][friends_table][pending_value]" value="">
	<input type="hidden" name="subsettings[db_info][friends_table][yes_value]" value="">

	<!-- Save Button -->
	<div style="padding: 0px 10px 15px;">
		<span style="margin: 7px 0px;" id="checkFriends_resp"></span>
		<input style="float: right;" type="submit" value="Save Settings" class="btn_grn adi_btn_space_left" id="adi_save_followers_details">
		<div class="clr"></div>
	</div>

	<?php
}


?>