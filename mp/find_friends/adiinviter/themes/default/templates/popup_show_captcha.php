<div class="adi_pp_captcha_out" id="adi_popup_recaptcha_panel">
	<div class="adi_block2_header">{adi:phrase adi_ip_captcha_block_header}</div>
	<center>
		<form method="POST" action="" style="margin:0px;padding:0px;" class="adi_clear_form adi_scc_form">
		{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
			<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
		{/adi:foreach}
		<div id="adi_security" class="adi_popup_inner_space adi_captcha_section_outer">
			<div id="adi_security_failed" class="adi_captcha_error" style="display:none;">{adi:phrase adi_ip_cap_wrong_answer_msg}</div>

			<div class="adi_captcha_container">
				<div class="adi_cap_loading_msg"><center>
					<div><center>{adi:phrase adi_default_message_for_all_popups}</center></div>
					<img class="adi_clear_img" style="margin-top:10px;" src="{adi:const THEME_URL}/images/loading.gif">
				</center></div>
				<div class="adi_google_recap_out"><center>
				<div class="g-recaptcha adi_google_recap" id="adi_ip_captcha_cont" data-sitekey="{adi:var $adiinviter->settings['captcha_public_key']}"></div>
				</center></div>
			</div>
		</div>
		<div class="adi_action_btns_out adi_captcha_btn_out">
			<input type="submit" class="adi_button adi_submit_captch_btn" name="submit_captcha" value="{adi:phrase adi_continue_btn_label}">
			<input type="button" class="adi_button adi_buttons_left_space adi_popup_ok" value="{adi:phrase adi_cancel_btn_label}">
		</div>
		</form>
	</center>
</div>

