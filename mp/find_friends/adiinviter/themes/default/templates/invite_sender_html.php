<form action="" method="POST" class="adi_clear_form adi_invite_sender_form">

{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
	<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
{/adi:foreach}


<!-- Required Parameters -->
<input type="hidden" name="adi_do" value="submit_invite_sender">
<input type="hidden" name="adi_service_key_val" class="adi_service_key_val" value="{adi:var $config['service_key']}">
<input type="hidden" name="campaign_id" value="{adi:var $config['campaign_id']}">
<input type="hidden" name="content_id" value="{adi:var $config['content_id']}">


<div class="adi_block_header" style="padding-left: 13px;">
	{adi:phrase adi_invite_block_header}
	{adi:if ($adi_current_model != 'inpage')}<div class="adi_pp_close_out adi_pp_close_out_no adi_popup_ok"><img src="{adi:const THEME_URL}/images/cross.gif" class="adi_pp_close_ico adi_popup_ok"></div>{/adi:if}
</div>


<div class="adi_block_section_outer adi_upper_section adi_nc_orientation_{adi:var $adiinviter->current_orientation}" style="padding-left: 12px;">
<table cellpadding="0" cellspacing="0" width="100%" class="adi_clear_table" style="width:100%;">
<tr class="adi_clear_tr">
	<td class="adi_clear_td" colspan="2">
	<div class="adi_top_description_text" style="margin-bottom: 14px;padding-left: 2px;">
		{adi:if (is_numeric($adiinviter->num_invites))}
			<div class="adi_num_invites_out">{adi:var $adiinviter_number_of_invitations_txt}</div>
		{/adi:if}
		{adi:phrase adi_invite_sender_top_message} <a class="adi_link adi_invite_preview_link" href="#">{adi:phrase invs_invitation_preview_link}</a>
	</div>
	</td>
</tr>
<tr class="adi_clear_tr">
	<td colspan="2" class="adi_clear_td adi_left">
	<table class="adi_clear_table" cellpadding="0" cellspacing="0" width="100%" style="width:100%;">
	<tr class="adi_clear_tr"><td class="adi_clear_td adi_conts_table_col adi_filter_conts_out" valign="middle">
	<div class="adi_filter_conts_inner">
		<div class="adi_search_user_out">
			<!-- Search In Contacts -->
			<img class="adi_clear_img adi_search_icon" src="{adi:const THEME_URL}/images/find_icon.png">
			<input type="textbox" class="adi_text_input adi_search_friend" value="{adi:phrase adi_search_contacts_default_text}">
		</div>
		<div class="adi_filter_buttons_out" style="margin-top:12px;">
				<!-- Go to Inviter Page -->
				<a href="{adi:if $adi_current_model != 'inpage'}{adi:var $adiinviter->inpage_model_url_rel}{/adi:if}" class="adi_link adi_goto_inviter_page_link">{adi:var $open_login_form_link}</a>
				<span class="adi_clear_span adi_link_sep">|</span>
				<!-- Select All link -->
				<a href="#" class="adi_link adi_select_all_link" rel="{adi:var $num_invtes_js_val}">{adi:phrase adi_select_all_link}</a>
		</div>
		<div style="clear:both;"></div>
	</div>
	</td></tr>
	</table>
</td></tr></table>
</div>




<div class="adi_conts_container_out adi_upper_section">
<table cellpadding="0" cellspacing="0" width="100%" class="adi_clear_table" style="width:100%;">
<tr class="adi_clear_tr">
	<td class="adi_clear_td" colspan="2">
		<table class="adi_clear_table" cellpadding="0" cellspacing="0" width="100%" style="width:100%;">
		<tr class="adi_clear_tr"><td class="adi_clear_td adi_conts_table_col adi_conts_outer_td">
		<div class="adi_conts_container">

			<div class="adcust_scrollbar"><div class="adcust_track"><div class="adcust_thumb"><div class="adcust_end"></div></div></div></div>
			<div class="adcust_viewport"><div class="adcust_overview">
				{adi:if $adi_current_model == 'inpage'}
					{adi:template non_registered_contacts}
				{/adi:if}
			</div></div>

		</div>
		</td></tr>
		</table>
	</td>
</tr>
</table>
</div>

<input type="hidden" class="adi_conts_input_pool" name="adi_conts_json" value="{}">

<div class="adi_block_section_outer">
<table class="adi_clear_table" cellpadding="0" cellspacing="0" width="100%" style="width:100%;">
<tr class="adi_clear_tr">
	<td class="adi_clear_td adi_conts_table_col" style="vertical-align:middle;">
		<!-- Preview Contact name -->
		<div class="adi_contacts_info"></div>
	</td>
	<td class="adi_clear_td adi_conts_table_col" style="vertical-align:middle;height:30px;">
		<div class="adi_conts_action_btns_out">
			<!-- Attach message linke -->
			{adi:if $adiinviter->settings['invitation_attachment'] == '1'}
				<a href="#" class="adi_link adi_attach_note_link">{adi:phrase adi_attach_note}</a><textarea name="adi_attach_note_txt_input" class="adi_attach_note_txt" style="display:none;"></textarea>
			{/adi:if}

			<!-- Back to friends adder -->
			{adi:if $adi_current_model == 'popup' && $show_friend_adder}
				<input type="button" class="adi_button adi_buttons_left_space adi_back_to_friend_adder" value="{adi:phrase invs_back_to_friend_adder_button}">
			{/adi:if}

			<!-- Invite Selected -->
			<input type="submit" class="adi_button adi_buttons_left_space adi_pe_btn adi_send_invites" value="{adi:phrase invs_send_invites_btn_txt}">
		</div>
	</td>
</tr>
</table>
</div>

</form>