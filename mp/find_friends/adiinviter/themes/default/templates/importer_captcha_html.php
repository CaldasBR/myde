<div class="adi_block2_header">Captcha Challenge</div>
<div class="adi_block_section_outer adi_importer_captcha_sectouter">
	<form method="POST" action="" class="adi_clear_form adi_importer_captcha_form">
	{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}

	<div class="adi_importer_cap_info">
	{adi:foreach $adi_captcha_info, $elem_name, $elem_val}
		<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
	{/adi:foreach}
	</div>

	<div>
	<center>
	<div style="margin:25px 0px 15px 0px;">
		<img class="adi_clear_img" style="border: 1px solid #cdcdcd;" src="{adi:var $adi_captcha_url}">
	</div>
	<div style="margin:15px 0px 35px 0px;">
		<input type="textbox" autocomplete="off" name="" size="20" class="adi_text_input_size adi_importer_captcha_text">
	</div>
	</center>
	</div>
	<div class="adi_action_btns_out adi_captcha_btn_out">
		<input type="submit" class="adi_button adi_submit_captch_btn" name="submit_captcha" value="{adi:phrase adi_continue_btn_label}">
		<input type="button"  class="adi_button adi_popup_ok adi_buttons_left_space" value="{adi:phrase adi_cancel_btn_label}">
	</div>
	</form>
</div>

