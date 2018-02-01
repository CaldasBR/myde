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


$table_html = ''; $extra_code = ''; $save_all='';
$breadcrumbs = array();


// Edit Phrase form
$adi_do = AdiInviterPro::POST('adi_do', ADI_STRING_VARS);
$adi_act = AdiInviterPro::GET('adi_act', ADI_STRING_VARS);

if($adi_act == 'export_lang')
{
	$lang_id = AdiInviterPro::GET('lang_id', ADI_STRING_VARS);

	$adi_installer = adi_allocate_pack('Adi_Installer');
	$xml_code = $adi_installer->export_language($lang_id);
	if($xml_code !== false)
	{
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: attachment; filename=".$lang_id.".xml");
		header("Content-Type: application/xml; charset=UTF-8");
		header("Content-Transfer-Encoding: binary");
		echo $xml_code;
	}
}
else if($adi_do == 'import_language')
{
	header("Content-Type: text/html; charset=UTF-8");
	$allow_overwrite = AdiInviterPro::POST('allow_overwrite', ADI_INT_VARS);
	$file_name = $_FILES['lang_xml']['tmp_name'];
	if(file_exists($file_name))
	{
		$real_name = $_FILES['lang_xml']['name'];
		$file_format = substr(strrchr($real_name, "."), 1);
		if(strtolower($file_format) == 'xml')
		{
			$xml_contents = file_get_contents($file_name);
		}
	}
	$adi_installer = adi_allocate_pack('Adi_Installer');
	$result = $adi_installer->import_language($xml_contents, (bool)$allow_overwrite);
	if($result)
	{
		echo '<html><head><script type="text/javascript">window.top.location.reload();</script></head><body></body></html>';
	}
	else
	{
		echo '<html><head><script type="text/javascript">window.top.$(".import_lang_form_response").html("Failed to import Language pack.");</script></head><body></body></html>';
	}
}
else if($adi_do == 'edit_phrase')
{
	$adiinviter->loadPhrases();
	$adiinviter->loadCache('language');

	$varname = AdiInviterPro::POST('phrase_name', ADI_STRING_VARS);
	$lang_id = AdiInviterPro::POST('lang_id', ADI_STRING_VARS);
	if( !empty($varname) && isset($adiinviter->phrases[$varname]) && count($adiinviter->cache['language']) > 0)
	{
		$adiinviter->loadGlobalPhrases();
		?>
		<div style="margin:10px;">
		<form id="adi_edit_phrase_form" method="post">

		<div style="margin: 20px 0px 0px 0px;">
		<div class="adi_inner_sect">
			<div class="adi_inner_sect_header" style="padding-left:15px;">Edit Phrase<a href="http://www.adiinviter.com/docs/languages#edit-phrases-in-language" class="adi_docs_link" target="_blank">Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
			<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">
				<tr class="first">
					<td class="label_box">
						<label class="opts_head">Phrase Varname</label><br>
						<span class="opts_note">Variable name of the phrase.</span>
					</td>
					<td>
						<?php echo $varname; ?>
						<input type="hidden" name="edit_phrase_form[phrase_varname]" value="<?php echo $varname; ?>">
					</td>
				</tr>
				<?php 
					$instlled_themes_list = $adiinviter->settings['adiinviter_themes_list'];
					$theme_name = isset($instlled_themes_list[$adiinviter->default_themeid]['name'])? $instlled_themes_list[$adiinviter->default_themeid]['name']:$adiinviter->default_themeid;
					if($result = adi_build_query_read('get_phrases', array(
						'phrase_varnames' => array($varname),
						'lang_id' => $lang_id,
					)))
					{
						if($phrase_details = adi_fetch_array($result))
						{
							if(isset($instlled_themes_list[$phrase_details['theme_id']]))
							{
								$theme_name = isset($instlled_themes_list[$phrase_details['theme_id']]['name']) ? $instlled_themes_list[$phrase_details['theme_id']]['name'] : $theme_name;
							}
						}
					}
				?>
				<tr class="first">
					<td class="label_box">
						<label class="opts_head">Theme</label><br>
						<span class="opts_note">Associated theme.</span>
					</td>
					<td>
						<?php echo $theme_name; ?>
					</td>
				</tr>

				<tr>
					<td class="label_box">
						<label class="opts_head">Default Text</label><br>
						<label class="opts_note">Default english text.</label>
					</td>
					<td>
						<textarea class="edit_phrase_txtarea disabled_phrase_txt" spellcheck="false" autocomplete="off" cols="40" rows="4" disabled><?php echo $adiinviter->global_phrases[$varname]; ?></textarea>
					</td>
				</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

				<?php
					$lang_name = $adiinviter->cache['language'][$lang_id];
					$phrases = $adiinviter->loadPhrases($varname, $lang_id);
					$style = 'direction: ltr;';
					if(in_array($lang_id, $adiinviter->rtl_lang_codes))
					{
						$style = 'direction: rtl;';
					}
					?><tr>
						<td class="label_box">
							<label class="opts_head"><?php echo $lang_name; ?> Translation</label><br>
							<label class="opts_note">Specify <?php echo $lang_name; ?> translation for this phrase.</label>
						</td>
						<td>
							<textarea class="edit_phrase_txtarea" cols="40" rows="4" spellcheck="false" autocomplete="off" name="edit_phrase_form[phrase_text][<?php echo $lang_id; ?>]" style="<?php echo $style; ?>"><?php echo $phrases[$varname]; ?></textarea>
						</td>
					</tr>

				<!-- <tr><td colspan="2" class="hr_sep_td"><hr class="sep"></td></tr> -->

			</table>

			</div></div>
		</div>
			<div style="padding: 0px 10px;">
				<input style="float: right;" type="submit" value="Save Phrase" class="btn_grn adi_btn_space_left">
				<input style="float: right;" type="button" value="Back" class="btn_org adi_edit_phrase_cancel ">
				<span style="float: right;margin: 7px 10px;" id="ediPhrase_response"></span>
				<div class="clr"></div>
			</div>
		</form>
		</div>
		<?php 
	}
	else
	{
		echo "<font color='red'><i>Invalid Phrase name : ".$varname."</i></font>";
	}

	exit;
}
else
{
	// Phrases listing
	$adiinviter->loadCache('language');

	$c_pageno = AdiInviterPro::POST('phrases_page_no', ADI_INT_VARS);
	$cur_page_no = 1; $page_size = 33;
	if(AdiInviterPro::isPOST('phrases_page_no') && is_numeric($c_pageno))
	{
		$cur_page_no = $c_pageno;
	}

	$final_phrases = array(); $search_results = false; $phrases_cnt = 0;
	$search_query = AdiInviterPro::POST('search_query', ADI_STRING_VARS);

	$search_lang  = AdiInviterPro::POST('search_lang', ADI_STRING_VARS);
	$search_lang  = ($search_lang != '*' ? $search_lang : '*');

	if(AdiInviterPro::isPOST('search_query') && $search_query != '')
	{
		$search_results = true;
		$search_type  = AdiInviterPro::POST('search_type', ADI_INT_VARS);
		if(!in_array($search_type, array(1,2,3)))
		{
			$search_type = 3;
		}

		$conditions   = '';
		if(in_array($search_type, array(1,2,3)))
		{
			$result = adi_build_query_read('search_in_phrases', array(
				'lang_id'      => $search_lang,
				'serach_query' => $search_query,
				'adi_query_conditions' => array(
					'in_language'    => ($search_lang != '*'),
					'search_in_vars' => ($search_type == 1 || $search_type == 3),
					'search_in_both' => ($search_type == 3),
					'search_in_text' => ($search_type == 2 || $search_type == 3),
				),
			));
			while($row = adi_fetch_assoc($result))
			{
				$final_phrases[] = array(
					'lang_id' => $row['lang_id'],
					'name'    => $row['name'],
				);
			}
		}
	}
	else
	{
		$all_phrases = $adiinviter->loadPhrases(array(), $search_lang);
		foreach($all_phrases as $name => $val)
		{
			$final_phrases[] = array(
				'lang_id' => $search_lang,
				'name'    => $name,
			);
		}
	}

	$phrases_cnt = count($final_phrases);
	$offset = ($page_size * ($cur_page_no - 1));

	?>
	<div class="" style="margin-top:20px;">
	
	<form action="" method="post" id="phrases_list">
	<center>

	<div style="margin: 5px 10px;text-align: left;">

	<table width="100%">
		<tr><td valign="center">
	<?php if($search_results == true) 
	{ 
		echo $phrases_cnt.' phrases including "'.$search_query.'" are found.';
	} 
	else 
	{
		$style = '';
		if($search_results == true)
		{
			$style = 'style="visibility:visible;"';
		}
		?><a href="#" class="adi_link adi_lang_show_all_phrases"<?php echo $style; ?>>Show All</a><?php 
	} ?>
		</td>

		<td style="text-align:right;" valign="middle">
			<input type="button" class="btn_org adi_lang_back btn_left_space" value="Back" style="padding: 9px 12px;float:right;">
			<input type="button" class="btn_grn add_new_phrase" value="Create New Phrase" style="padding: 9px 12px;float:right;">
			<div style="clear:both;"></div>
		</td>
	</tr></table>


	<?php
		if($phrases_cnt > 0)
		{
			// Phrases List
			$phrase_names_full = array_keys($final_phrases);
			sort($phrase_names_full);
			$phrase_names_arr  = array_slice($phrase_names_full, $offset, $page_size);

			echo '<div style="margin: 15px 0px 0px 0px;text-align: left;border: 1px solid #DDD;border-bottom:none;">
			<table cellspacing="0" cellpadding="0" width="100%">';

			$odd = true;
			foreach($phrase_names_arr as $ind)
			{
				$details     = $final_phrases[$ind];
				$phrase_name = $details['name'];
				$lang_id     = $details['lang_id'];
				$lang_name   = isset($adiinviter->cache['language'][$lang_id]) ? $adiinviter->cache['language'][$lang_id] : '';
				$class_name  = ($odd) ? 'lang_odd' : '';
				$odd = !$odd;

				echo '<tr class="lang_out">';
				if($search_results == true)
				{
					echo '<td class="'.$class_name.'" style="padding-left:10px;border-bottom: 1px solid #DDD;border-right: 1px solid #DDD;width:130px;" align="center">'.$lang_name.' ('.$lang_id.')</td>';
				}
				echo '
				<td class="'.$class_name.'" style="padding-left:10px;border-bottom: 1px solid #DDD;">'.$phrase_name.'</td>
				<td class="'.$class_name.'" style="border-bottom: 1px solid #DDD;padding: 5px;">

					<div class="btn_grn btn_small phrase_remove adi_btn_space_left" rel="'.$lang_id.'" data="'.$phrase_name.'" style="float:right;">Remove</div>
					<div class="btn_blue btn_small phrase_edit" rel="'.$lang_id.'" data="'.$phrase_name.'" style="float:right;">Edit</div>
					<div class="clr"></div>

					<!-- <div class="lang_actions" style="width:135px;">
						<a class="actions_small phrase_edit" rel="'.$lang_id.'" data="'.$phrase_name.'" style="padding:2px;">
							<div class="ico_edit wtext"></div>
							<div class="ico_txt">Edit</div>
							<div class="clr"></div>
						</a>
						<a class="actions_small phrase_remove" data="'.$phrase_name.'" style="padding:2px;margin-left:5px;">
							<div class="ico_remove wtext"></div>
							<div class="ico_txt">Delete</div>
							<div class="clr"></div>
						</a>
					</div> -->

				</td>
				</tr>';
			}
			echo'</table></div>';
		}
		else {
			echo '<div style="margin-top:15px;"><font color="red"><i>No Phrases found..</i></font></div>';
		}
		
		$total_pages = ceil($phrases_cnt / $page_size);
		if($total_pages > 1)
		{
			?><div class="paginate_out phrases_pagination"><?php

			$css_css = ($cur_page_no > 1) ? 'paginate_page_active' : 'paginate_page_disabled';
			echo '<a href="#" class="paginate_node '.$css_css.'" data="1">First</a>';
			echo '<a href="#" class="paginate_node '.$css_css.'" data="'.($cur_page_no-1).'">Prev</a>';

			if($cur_page_no > 2) {
				echo '<a href="#" class="paginate_node paginate_page_active" data="'.($cur_page_no-2).'">'.($cur_page_no-2).'</a>';
			}
			if($cur_page_no > 1) {
				echo '<a href="#" class="paginate_node paginate_page_active" data="'.($cur_page_no-1).'">'.($cur_page_no-1).'</a>';
			}
			echo '<a href="#" class="paginate_node paginate_page_current" data="'.($cur_page_no).'">'.($cur_page_no).'</a>';
			if($cur_page_no < $total_pages) {
				echo '<a href="#" class="paginate_node paginate_page_active" data="'.($cur_page_no+1).'">'.($cur_page_no+1).'</a>';
			}
			if($cur_page_no < $total_pages-1) {
				echo '<a href="#" class="paginate_node paginate_page_active" data="'.($cur_page_no+2).'">'.($cur_page_no+2).'</a>';
			}

			$css_css = ($cur_page_no < $total_pages) ? 'paginate_page_active' : 'paginate_page_disabled';
			echo '<a href="#" class="paginate_node '.$css_css.'" data="'.($cur_page_no+1).'">Next</a>';
			echo '<a href="#" class="paginate_node '.$css_css.'" data="'.($total_pages).'">Last</a>';

			?></div><?php
		}
		
	?>
	</div>
	</center>
	</form>
	</div>
	<?php 
}



?>