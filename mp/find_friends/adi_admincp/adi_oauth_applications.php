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

?>
<form method="post" action="" class="settings_list">

<!-- Google services -->
<div style="margin:10px;">
	<table style="width:100%;" class="opts_table oauth_table" cellspacing="0" cellpadding="0">

	<!-- Google Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_gmail">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/gmail.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Client ID</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$google_consumer_key_text = $adiinviter->settings['google_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/gmail-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Gmail OAuth App.</a>
						<input type="textbox" class="txinput reg adi_google_group adi_oauth_def_text" style="width:98%;" name="subsettings[oauth][google_consumer_key]" value="<?php echo $google_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width:200px;">
						<div class="opts_head">Client Secret</div>
					</td>
					<td valign="middle">
						<?php
						$google_consumer_secret_text = $adiinviter->settings['google_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_google_group adi_oauth_def_text" style="width:98%;" name="subsettings[oauth][google_consumer_secret]" value="<?php echo $google_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="padding: 10px 0px;"><hr class="bef_submit"></td>
	</tr>


	<!-- Hotmail Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_hotmail">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/hotmail.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Client ID</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$hotmail_consumer_key_text = $adiinviter->settings['hotmail_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/outlook-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Hotmail OAuth App.</a>
						<input type="textbox" class="txinput reg adi_hotmail_group adi_oauth_def_text" style="width:98%;" name="subsettings[oauth][hotmail_consumer_key]" value="<?php echo $hotmail_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Client Secret</div>
					</td>
					<td valign="middle">
						<?php
						$hotmail_consumer_secret_text = $adiinviter->settings['hotmail_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_hotmail_group adi_oauth_def_text" style="width:98%;" name="subsettings[oauth][hotmail_consumer_secret]" value="<?php echo $hotmail_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px;"><hr class="bef_submit"></td>
	</tr>

	<!-- Yahoo Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_yahoo">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/yahoo.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Key</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$yahoo_consumer_key_text = $adiinviter->settings['yahoo_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/yahoo-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Yahoo OAuth App.</a>
						<input type="textbox" class="txinput reg adi_yahoo_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][yahoo_consumer_key]" value="<?php echo $yahoo_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Secret Key</div>
					</td>
					<td valign="middle">
						<?php
						$yahoo_consumer_secret_text = $adiinviter->settings['yahoo_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_yahoo_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][yahoo_consumer_secret]" value="<?php echo $yahoo_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px;"><hr class="bef_submit"></td>
	</tr>

	<!-- Twitter Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_twitter">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/twitter.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Key</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$twitter_consumer_key_text = $adiinviter->settings['twitter_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/twitter-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Twitter OAuth App.</a>
						<input type="textbox" class="txinput reg adi_twitter_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][twitter_consumer_key]" value="<?php echo $twitter_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Secret Key</div>
					</td>
					<td valign="middle">
						<?php
						$twitter_consumer_secret_text = $adiinviter->settings['twitter_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_twitter_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][twitter_consumer_secret]" value="<?php echo $twitter_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px;"><hr class="bef_submit"></td>
	</tr>


	<!-- Mailchimp Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_mailchimp">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/mailchimp.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Client ID</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$mailchimp_consumer_key_text = $adiinviter->settings['mailchimp_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/mailchimp-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Mailchimp OAuth App.</a>
						<input type="textbox" class="txinput reg adi_mailchimp_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][mailchimp_consumer_key]" value="<?php echo $mailchimp_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Client Secret</div>
					</td>
					<td valign="middle">
						<?php
						$mailchimp_consumer_secret_text = $adiinviter->settings['mailchimp_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_mailchimp_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][mailchimp_consumer_secret]" value="<?php echo $mailchimp_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">API Key</div>
					</td>
					<td valign="middle">
						<?php
						$mailchimp_consumer_api_key_text = $adiinviter->settings['mailchimp_consumer_api_key'];
						?>
						<input type="textbox" class="txinput reg adi_mailchimp_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][mailchimp_consumer_api_key]" value="<?php echo $mailchimp_consumer_api_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px 0px 0px;"><hr class="bef_submit"></td>
	</tr>


	<!-- Xing Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_xing">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/xing.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Key</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$xing_consumer_key_text = $adiinviter->settings['xing_consumer_key'];
						$instr = 'Use adiinviter.com OAuth application Client ID';
						$css_cls = '';
						if(empty($xing_consumer_key_text))
						{
							$xing_consumer_key_text = $instr;
							$css_cls = 'adi_oauth_def_text_format';
						}
						?>
						<a href="http://www.adiinviter.com/docs/xing-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Xing OAuth App.</a>
						<input type="textbox" class="txinput reg adi_xing_group adi_oauth_def_text <?php echo $css_cls; ?>" style="width: 98%;" name="subsettings[oauth][xing_consumer_key]" value="<?php echo $xing_consumer_key_text; ?>" defaulttext="<?php echo $instr; ?>" groupid="adi_xing_group" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Consumer Secret</div>
					</td>
					<td valign="middle">
						<?php
						$xing_consumer_secret_text = $adiinviter->settings['xing_consumer_secret'];
						$instr = 'Use adiinviter.com OAuth application Client Secret';
						$css_cls = '';
						if(empty($xing_consumer_secret_text))
						{
							$xing_consumer_secret_text = $instr;
							$css_cls = 'adi_oauth_def_text_format';
						}
						?>
						<input type="textbox" class="txinput reg adi_xing_group adi_oauth_def_text <?php echo $css_cls; ?>" style="width: 98%;" name="subsettings[oauth][xing_consumer_secret]" value="<?php echo $xing_consumer_secret_text; ?>" defaulttext="<?php echo $instr; ?>" groupid="adi_xing_group" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px 0px 0px;"><hr class="bef_submit"></td>
	</tr>

	<!-- Eventbrite Services -->
	<tr>
		<td style="vertical-align:middle;width: 125px;">
			<div class="adi_psp_service_box adi_psp_eventbrite">
				<div class="adi_psp_inner"><img class="adi_clear_img" src="adi_css/oauth/eventbrite.png"></div>
			</div>
		</td>
		<td>
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">API Key</div>
					</td>
					<td style="vertical-align: middle;">
						<?php
						$eventbrite_consumer_key_text = $adiinviter->settings['eventbrite_consumer_key'];
						?>
						<a href="http://www.adiinviter.com/docs/eventbrite-oauth-branding" class="adi_oauth_doc_link" target="_blank">How to register Eventbrite OAuth App.</a>
						<input type="textbox" class="txinput reg adi_eventbrite_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][eventbrite_consumer_key]" value="<?php echo $eventbrite_consumer_key_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
				<tr>
					<td class="label_box" style="vertical-align: middle;width: 200px;">
						<div class="opts_head">Secret Key</div>
					</td>
					<td valign="middle">
						<?php
						$eventbrite_consumer_secret_text = $adiinviter->settings['eventbrite_consumer_secret'];
						?>
						<input type="textbox" class="txinput reg adi_eventbrite_group adi_oauth_def_text" style="width: 98%;" name="subsettings[oauth][eventbrite_consumer_secret]" value="<?php echo $eventbrite_consumer_secret_text; ?>" spellcheck="false" autocomplete="off">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan="2" style="padding: 10px 0px 0px 0px;"><hr class="bef_submit"></td>
	</tr>

	<tr>
		<td colspan="2" style="padding:0px;">
			<div class="cont_submit">
				<input type="submit" value="Save Settings" class="btn_grn" id="adi_save_settings">
			</div>
		</td>
	</tr>

	</table>
	
</div>

</form>