<!-- Startup Errors -->
{adi:template inpage_error_display}

<table cellpadding="0" cellspacing="0" class="adi_clear_table">
<tr class="adi_clear_tr"><td class="adi_clear_td">
	<div class="adi_nc_inpage_panel_outer adiinviter" id="adi_inpage_recaptcha_panel">
		<div class="adi_block2_header">{adi:phrase adi_ip_captcha_block_header}</div>
		<div class="adi_block_section_outer adi_captcha_section_outer">
			<form method="POST" action="" class="adi_clear_form">
			{adi:foreach $adiinviter->form_hidden_elements, $elem_name, $elem_val}
				<input type="hidden" name="{adi:var $elem_name}" value="{adi:var $elem_val}">
			{/adi:foreach}
			<div id="adi_security">

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
			</div>
			</form>
		</div>
	</div>
</td></tr></table>

<script type="text/javascript">
adi.allocate_intr('rcip', adjq('#adi_inpage_recaptcha_panel'));
</script>
