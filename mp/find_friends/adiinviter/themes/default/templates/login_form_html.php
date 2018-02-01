{adi:set $adi_show_addressbook = 0}
{adi:set $adi_show_contact_file = 0}
{adi:set $adi_show_manual_inviter = 0}

{adi:if (!isset($importer_type))}
	{adi:set $importer_type = ''}
{/adi:if}

{adi:if ($importer_type == 'contact_file')}
	{adi:set $adi_show_contact_file = 1}
{adi:elseif ($importer_type == 'manual_inviter')}
	{adi:set $adi_show_manual_inviter = 1}
{adi:else}
	{adi:set $adi_show_addressbook = 1}
{/adi:if}


<div class="adi_login_form_width">

<!-- AddressBook Importer -->
<div class="{adi:if ($adi_show_addressbook == 1)}adi_current_section{adi:else}adi_other_section{/adi:if} adi_section_separator adi_nc_section_header">
	<div class="adi_section_header_txt">
		{adi:phrase adi_ab_top_header}
		{adi:if ($adi_current_model != 'inpage')}<div class="adi_pp_close_out adi_popup_ok"><img src="{adi:const THEME_URL}/images/cross.gif" class="adi_pp_close_ico adi_popup_ok"></div>{/adi:if}
	</div>

	<div class="adi_inner_section" style="{adi:if ($adi_show_addressbook == 1)}display:block;{/adi:if}">

	<div class="adi_err_outer"></div>

	<form action="" method="POST" class="adi_clear_form adi_nc_oauth_submit_form adi_nc_addressbook_form">

	<!-- Required Parameters -->
	<input type="hidden" name="adi_service_key_val" class="adi_service_key_val">
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="addressbook">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
	<input type="hidden" name="adi_captcha_text" class="adi_captcha_text_cls">
	<input type="hidden" name="adi_contacts_target" class="adi_cnttrgt" value="">
	<div class="adi_importer_cap_info_pass" style="display:none;"></div>

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<center>
		<table cellpadding="0" cellspacing="0" border="0" class="adi_clear_table adi_inner_section_table">
		<tr class="adi_clear_tr">
			<td valign="middle" align="center" class="adi_clear_td" style="padding-top: 22px;vertical-align:middle;">

				<center><table class="adi_clear_table adi_security_msg_out" cellspacing="0" cellpadding="0" border="0"><tr class="adi_clear_tr">
					<td valign="middle" class="adi_clear_td"><img src="{adi:const THEME_URL}/images/lock.png" class="adi_clear_img adi_security_icon"></td>
					<td valign="middle" class="adi_clear_td adi_security_text" style="padding-top: 6px;">{adi:phrase adi_ab_top_security_desc}</td>
				</tr></table></center>
				
				<table class="adi_clear_table">
				<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:8px;" colspan="2"></td></tr>
				<tr class="adi_clear_tr">
					<td class="adi_clear_td" style="vertical-align:middle;"><div class="adi_input_form_label">{adi:phrase adi_ab_email_field_label_txt}</div></td>
					<td class="adi_clear_td"><div class="adi_input_form_field"><input type="textbox" autocomplete="off" name="adi_user_email" size="20" class="adi_text_input_size adi_nc_user_email_input" value=""></div></td>
				</tr>
				<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:0px;" colspan="2"></td></tr>
				<tr class="adi_clear_tr">
					<td class="adi_clear_td" style="vertical-align:middle;"><div class="adi_input_form_label">{adi:phrase adi_ab_password_field_label_txt}</div></td>
					<td class="adi_clear_td"><div class="adi_input_form_field">
						<input type="password" name="adi_user_password" size="20" class="adi_text_input_size adi_nc_password_input" value="">
						<input type="textbox" name="adi_password_note" size="20" class="adi_text_input_size adi_nc_password_note" value="{adi:phrase addressbook_password_not_required}" disabled>
					</div></td>
				</tr>
				<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:0px;" colspan="2"></td></tr>
				<tr class="adi_clear_tr">
					<td class="adi_clear_td" style="vertical-align:middle;"><div class="adi_input_form_label">{adi:phrase adi_ab_service_field_label_txt}</div></td>
					<td class="adi_clear_td adi_service_input_out">
					<div class="adi_input_form_field adi_nc_service_name_outer">
						<img class="adi_clear_img adi_search_icon" src="{adi:const THEME_URL}/images/find_icon.png">
						<img class="adi_clear_img adi_nc_down_arrow" src="{adi:const THEME_URL}/images/dropdown_arrow.gif" data="{adi:var $adi_current_model}" style="display:block;">
						<img class="adi_clear_img adi_nc_up_arrow" src="{adi:const THEME_URL}/images/up_arrow.gif" data="{adi:var $adi_current_model}">
						<input type="textbox" name="adi_service_name" data="{adi:var $adi_current_model}" autocomeplete="off" size="20" class="adi_text_input_size adi_nc_service_input adi_nc_service_note" value="{adi:phrase adi_ab_service_field_default_txt}" autocomplete="off">
					</div></td>
				</tr>
				<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:0px;" colspan="2"></td></tr>
				<tr class="adi_clear_tr">
					<td class="adi_clear_td"></td>
					<td class="adi_clear_td" style="height:60px;vertical-align:middle;">
						<center>
							<img class="adi_clear_img adi_nc_submit_effect" style="display:none;margin-top:5px;" src="{adi:const THEME_URL}/images/loading.gif">
							<input class="adi_button adi_nc_submit_action adi_nc_submit_addressbook" type="submit" name="adi_submit_addressbook" value="{adi:phrase adi_ab_submit_form_btn_text}">
						</center>
					</td>
				</tr>
				<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:0px;" colspan="2"></td></tr>
				</table>
				
			</td>
			<td class="adi_clear_td" style="vertical-align:middle;">
				<div class="adi_psp_vertical_sep"></div>	
			</td>
			<td class="adi_clear_td" valign="middle" style="vertical-align:middle;">

			<!-- Popular Service Providers -->
			<!-- <div class="adi_psp_top_header">{adi:phrase adi_ab_oauth_services_header}</div> -->
			<div class="adi_psp_services_out">

				{adi:foreach $adidt_popular_services_final, $ind, $p_serv_key}
				<div class="adi_psp_service_box adi_psp_{adi:var $p_serv_key} adi_nc_external_login" service_id="{adi:var $p_serv_key}"><div class="adi_psp_service_box_iner"><img class="adi_clear_img" src="{adi:const THEME_URL}/images/{adi:var $p_serv_key}.png"></div></div>
				{/adi:foreach}
				
				<div class="adi_clear"></div>
			</div>
			</td>
		</tr>
		</table>
	</center>
	<input type="hidden" class="adi_oauth_submit" value="0">
	</form>
	</div>
</div>




<!-- Contact File Importer  -->
<div class="{adi:if ($adi_show_contact_file == 1)}adi_current_section{adi:else}adi_other_section{/adi:if} adi_section_separator adi_nc_section_header">
	<div class="adi_section_header_txt">{adi:phrase adi_cf_top_header}</div>
	<div class="adi_inner_section" {adi:if ($adi_show_contact_file == 1)}style="display:block;"{/adi:if}>
	<div class="adi_err_outer"></div>

<form method="POST" enctype="multipart/form-data" class="adi_nc_contact_file_form" {adi:if ($adi_current_model == 'popup')} action="{adi:const ADI_ROOT_URL_REL}/adiinviter_ajax.php?adi_do=get_contacts" target="adi_submit_contact_file" {adi:else} action="" {/adi:if}>

	<!-- Required Parameters -->
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="contact_file">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
	<input type="hidden" name="adi_contacts_target" class="adi_cnttrgt" value="">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<center>
	<table class="adi_clear_table" style="margin: 0px 15px;" cellpadding="0" cellspacing="0" border="0">
		<tr class="adi_clear_tr">
			<td class="adi_clear_td" valign="middle">

		<table class="adi_clear_table" cellpadding="0" cellspacing="0" border="0">
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:7px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td"><div class="adi_section_subhead adi_centerd_text" style="margin-top: 25px;">{adi:phrase adi_cf_top_message}</div></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:7px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td adi_centerd_text">
			<center><table class="adi_clear_table adi_security_msg_out" cellspacing="0" cellpadding="0"><tr class="adi_clear_tr">
				<td class="adi_clear_td" valign="middle"><img src="{adi:const THEME_URL}/images/lock.png" class="adi_clear_img adi_security_icon"></td>
				<td valign="middle" class="adi_clear_td adi_security_text">{adi:phrase adi_cfmi_top_security_desc}</td>
			</tr></table></center>
		</td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:7px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td adi_centerd_text"><div class="adi_section_sub_message">{adi:phrase adi_cf_top_message_3}</div></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:11px;"></td></tr>
		<tr class="adi_clear_tr">
			<td class="adi_clear_td"><center><a class="adi_link adi_nc_show_csv_instruct">{adi:phrase adi_cf_show_instructions_link}</a><center></td>
		</tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:20px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td">
			
		<center>
			<table class="adi_clear_table adi_input_form_fill">
			<tr class="adi_clear_tr"><td class="adi_clear_td" colspan="2" style="height:3px;"></td></tr>
			<tr class="adi_clear_tr">
				<td class="adi_clear_td" style="vertical-align:middle;">
					<div class="adi_input_form_label">{adi:phrase adi_cf_contact_file_field_label}</div>
				</td>
				<td class="adi_clear_td" style="vertical-align:middle;">
					<div class="adi_input_form_field"><input type="file" name="adi_contact_file" size="20" class="adi_file_input adi_nc_contact_file"></div>
				</td>
			</tr>
			<tr class="adi_clear_tr"><td class="adi_clear_td" colspan="2" style="height:3px;"></td></tr>
			</table>
				<img class="adi_clear_img adi_nc_submit_effect" style="display:none;margin-top:25px;" src="{adi:const THEME_URL}/images/loading.gif">
				<input class="adi_button adi_nc_submit_action adi_nc_submit_contactfile" type="submit" name="adi_submit_conact_file" value="{adi:phrase adi_cf_submit_contact_file_btn_label}" style="margin-top:20px;">
		</center>
			
		</td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:20px;"></td></tr>
		</table>
	</td>
	</tr>
	</table>
	</center>
</form>

	</div>
</div>

<!-- Iframe fro submiting contact file from popup -->
<iframe id="adi_submit_contact_file" name="adi_submit_contact_file" src="" style="width:0;height:0;border:0px solid #fff;padding:0;margin:0;display:none;"></iframe>





<!-- Manual Inviter -->
<div class="{adi:if ($adi_show_manual_inviter == 1)}adi_current_section{adi:else}adi_other_section{/adi:if} adi_nc_section_header">
	<div class="adi_section_header_txt">{adi:phrase adi_mi_top_header}</div>
	<div class="adi_inner_section" {adi:if ($adi_show_manual_inviter == 1)}style="display:block;"{/adi:if}>
	<div class="adi_err_outer"></div>
	<form action="" method="POST" class="adi_nc_manual_form">

	<!-- Required Parameters -->
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="manual_inviter">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
	<input type="hidden" name="adi_contacts_target" class="adi_cnttrgt" value="">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<center>
	<table class="adi_clear_table" style="margin: 0px 5px;" cellpadding="0" cellspacing="0" border="0">
		<tr class="adi_clear_tr">
			<td class="adi_clear_td" valign="middle">

	<center>
		<table class="adi_clear_table" cellpadding="0" cellspacing="0" border="0">
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:4px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td adi_centerd_text">
			<center><table class="adi_clear_table adi_security_msg_out" cellspacing="0" cellpadding="0"><tr class="adi_clear_tr">
				<td class="adi_clear_td" valign="middle"><img src="{adi:const THEME_URL}/images/lock.png" class="adi_clear_img adi_security_icon"></td>
				<td valign="middle" class="adi_clear_td adi_security_text">{adi:phrase adi_cfmi_top_security_desc}</td>
			</tr></table></center>
		</td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:4px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td adi_centerd_text"><div class="adi_section_sub_message">{adi:phrase adi_mi_top_message}</div></td>
		</tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:11px;"></td></tr>
		<tr class="adi_clear_tr">
			<td class="adi_clear_td"><center><a class="adi_link adi_nc_show_formats">{adi:phrase adi_mi_show_formats}</a><center></td>
		</tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:15px;"></td></tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td">

			<center>
				<table class="adi_clear_table adi_input_form_fill">
				<tr class="adi_clear_tr">
					<td class="adi_clear_td"><div class="adi_input_form_field"><textarea name="adi_contacts_list" class="adi_contacts_list_input adi_nc_contacts_list" spellcheck=false>{adi:phrase adi_mi_contact_list_field_default_text}</textarea></div></td>
				</tr>
				</table>
			</center>

			</td>
		</tr>
		<tr class="adi_clear_tr"><td class="adi_clear_td" style="height:18px;"></td></tr>
		<tr class="adi_clear_tr">
			<td class="adi_clear_td">
				<center>
					<img class="adi_clear_img adi_nc_submit_effect" style="display:none;margin-top:5px;" src="{adi:const THEME_URL}/images/loading.gif">
					<input class="adi_button adi_nc_submit_action adi_nc_submit_manual" type="submit" name="adi_submit_conact_file" value="{adi:phrase adi_continue_btn_label}">
				</center>
			</td>
		</tr>
		</table>
	</center>
	</td>
	</tr>
	</table>
	</center>
	</form>
	</div>
</div>

</div>







<div class="adi_type2_instr_out" style="display:none;">

<!-- Linkedin CSV Instructions-->
<div class="adi_type2_instr_entity adi_linkedin_insrtuctions">
	<div class="adi_block2_header adi_type2_sect_header">{adi:phrase adi_linkedin_imp_sect_header}<div class="adi_pp_close_out adi_pp_close_out_no adi_popup_ok"><img src="{adi:const THEME_URL}/images/cross.gif" class="adi_pp_close_ico adi_popup_ok"></div></div>

	<form method="POST" enctype="multipart/form-data" class="adi_nc_contact_file_form" {adi:if ($adi_current_model == 'popup')} action="{adi:const ADI_ROOT_URL_REL}/adiinviter_ajax.php?adi_do=get_contacts" target="adi_submit_contact_file" {adi:else} action="" {/adi:if}>

	<!-- Required Parameters -->
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="contact_file">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
	<input type="hidden" name="adi_contacts_target" class="adi_cnttrgt" value="">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_block_section_outer adi_type2_sect_outer">

		<table width="100%" cellpadding="0" cellspacing="0" class="adi_linkedin_sect_tb" style="margin-bottom: 35px;">
		<tr>
			<td style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step1_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step1_desc}</div></td>
		</tr>
		<tr>
			<td></td>
			<td style="padding-bottom: 5px"><a href="https://www.linkedin.com/addressBookExport?exportNetwork=Export&outputType=microsoft_outlook" class="adi_link1" target="_blank">{adi:phrase adi_linkedin_imp_download_redirect_text}</a></td>
		</tr>
		<tr>
			<td></td>
			<td><div class="adi_plain_text" style="margin-bottom:5px;">{adi:phrase adi_linkedin_imp_step1_note}</div></td>
		</tr>
		<tr>
			<td  style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step2_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step2_desc}</div></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<div class="adi_type2_chfile" style="margin-bottom:5px;">
					<a href="" class="adi_link1" onclick="return false;">{adi:phrase adi_linkedin_imp_select_csv_btn_txt}</a>
					<input type="file" name="adi_contact_file" size="20" class="adi_file_input adi_type2_file_input">
					<div class="adi_type2_selected_file"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td  style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step3_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_linkedin_imp_step3_desc}</div></td>
		</tr>
		</table>

		<div class="adi_action_buttons adi_nc_submit_action">
			<div class="adi_lnkd_error_msg"></div>
			<a href="" class="adi_link adi_type2_cancel adi_popup_ok">{adi:phrase adi_cancel_btn_label}</a>
			<input type="submit" class="adi_button" value="{adi:phrase adi_ab_submit_form_btn_text}">
		</div>

		<div class="adi_action_buttons adi_nc_submit_effect" style="display:none;"><div class="adi_proc_effect">{adi:phrase adi_linkedin_imp_submit_msg_txt}</div></div>

	</div>
	</form>
</div>

<!-- QQ.com CSV Instructions-->
<div class="adi_type2_instr_entity adi_qq_com_insrtuctions">
	<div class="adi_block2_header adi_type2_sect_header">{adi:phrase adi_qq_com_imp_sect_header}<div class="adi_pp_close_out adi_pp_close_out_no adi_popup_ok"><img src="{adi:const THEME_URL}/images/cross.gif" class="adi_pp_close_ico adi_popup_ok"></div></div>

	<form method="POST" enctype="multipart/form-data" class="adi_nc_contact_file_form" {adi:if ($adi_current_model == 'popup')} action="{adi:const ADI_ROOT_URL_REL}/adiinviter_ajax.php?adi_do=get_contacts" target="adi_submit_contact_file" {adi:else} action="" {/adi:if}>

	<!-- Required Parameters -->
	<input type="hidden" name="adi_do" value="get_contacts">
	<input type="hidden" name="importer_type" value="contact_file">
	<input type="hidden" name="campaign_id" value="{adi:var $campaign_id}" class="adi_nc_campaign_id">
	<input type="hidden" name="content_id" value="{adi:var $content_id}" class="adi_nc_content_id">
	<input type="hidden" name="adi_contacts_target" class="adi_cnttrgt" value="">

	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_block_section_outer adi_type2_sect_outer">

		<table width="100%" cellpadding="0" cellspacing="0" class="adi_linkedin_sect_tb" style="margin-bottom: 35px;">
		<tr>
			<td style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step1_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step1_desc}</div></td>
		</tr>
		<tr>
			<td></td>
			<td style="padding-bottom: 5px"><a href="http://kf.qq.com/faq/120511z22Uzq130902E7ji6v.html" class="adi_link1" target="_blank">{adi:phrase adi_qq_com_imp_download_redirect_text}</a></td>
		</tr>
		<tr>
			<td></td>
			<td><div class="adi_plain_text" style="margin-bottom:5px;">{adi:phrase adi_qq_com_imp_step1_note}</div></td>
		</tr>
		<tr>
			<td  style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step2_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step2_desc}</div></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<div class="adi_type2_chfile" style="margin-bottom:5px;">
					<a href="" class="adi_link1" onclick="return false;">{adi:phrase adi_qq_com_imp_select_csv_btn_txt}</a>
					<input type="file" name="adi_contact_file" size="20" class="adi_file_input adi_type2_file_input">
					<div class="adi_type2_selected_file"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td  style="width:60px;"><div class="adi_plain_text"><b>{adi:phrase adi_linkedin_imp_step3_label}</b></div></td>
			<td><div class="adi_plain_text"> {adi:phrase adi_qq_com_imp_step3_desc}</div></td>
		</tr>
		</table>

		<div class="adi_action_buttons adi_nc_submit_action">
			<div class="adi_lnkd_error_msg"></div>
			<a href="" class="adi_link adi_type2_cancel adi_popup_ok">{adi:phrase adi_cancel_btn_label}</a>
			<input type="submit" class="adi_button" value="{adi:phrase adi_ab_submit_form_btn_text}">
		</div>

		<div class="adi_action_buttons adi_nc_submit_effect" style="display:none;"><div class="adi_proc_effect">{adi:phrase adi_qq_com_imp_submit_msg_txt}</div></div>

	</div>
	</form>
</div>

</div>

