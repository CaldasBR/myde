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


$campain_default_settings = array(
	'campaign_on_off'     => '0',

	'restricted_ids'           => '',
	'restricted_category_ids'  => '',
	'restricted_usergroup_ids' => '',
	'restricted_user_ids'      => '',

	'redirection_on_off'       => '0',
	'content_page_url'         => '',

	'word_limit'               => '200',
	'content_table' => array(
		'table_name'    => '',
		'content_id'    => '',
		'content_body'  => '',
		'content_title' => '',
		'category_id'   => '',
		'url_alias'     => '',
	),

	'invitation_subject_en' => '[website_name] Campaign Invitation',

	// Invitation Email body
	'campaign_email_body_en' => '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="format-detection" content="telephone=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<title>[website_name] Campaign Invitation</title>

<style type="text/css">

@media all and (max-width: 400px) {
	.feature-column { width:100% !important; display: block; }
	.feature-text { margin-bottom: 50px; }
}
@media all and (max-width: 500px) {
	.feature-column { padding: 0px 10px !important; }
	.width-500-100 { width: 100% !important; }
	.mbot-30 { margin-bottom: 30px !important; }
	.leftright-text { padding-right: 0px !important; padding-left: 0px !important; }
}

</style>

</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" yahoo="fix" style="font-family: Verdana, Georgia, Times, serif; background-color:#F5F5F5; " bgcolor="F5F5F5">


<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
<tr><td align="center">

<div style="width:100%;max-width:600px;min-width:320px;">

	<table width="100%" border="0" cellpadding="0" cellspacing="0"  align="center" style="background-color: #FFFFFF;">


	<tr>
		<td style="background-color: #00BBF5;">

			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 15px;"><img src="[invitation_assets_url]/default/logo.png"></td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="font-size:14px; font-weight:bold;color: #ffffff;line-height: 21px;margin-bottom:25px;">INTRODUCING</div>
					<div style="font-size:38px;color: #ffffff;line-height: 25px;">Your Website</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="width:100%;height:100%;max-width:190px;max-height:190px;background: url([invitation_assets_url]/default/avatar_bg.png) 0 0 no-repeat;margin: 0px auto;">
						<div style=""><img src="[sender_avatar_url]" style="margin: 20px;max-height:150px;max-heigh:150px;border-radius:50%;"></div>
					</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<div style="font-size:18px;color: #ffffff;line-height: 25px;">Your friend [sender_name] invited you to Your Website.</div>
				</td>
			</tr>
			<tr>
				<td align="center" style="padding: 15px;">
					<a href="[verify_invitation_url]invitation_id=[invitation_id]&adi_do=accept" style="background-color:#007396;display:block;padding: 15px 15px;margin:0px 15px 20px 15px;width:100%;max-width:175px;font-size:15px;font-weight:bold;color: #ffffff;text-decoration: none;border-radius:4px;" class="">Get Started</a>
				</td>
			</tr>
			</table>

		</td>
	</tr>



	<tr>
		<td style="padding: 40px 20px;border: 1px solid #DDD;border-top: none;" align="center">
			<div style="display:inline;margin: 0 auto;">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;table-layout: fixed;">
			<tr>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/feature1.png" style="margin-bottom: 10px;">
						<div style="font-size:16px;line-height: 22px;color: #68B975;" class="feature-text">Your Website<br>Feature #1</div>
					</td></tr>
					</table>
				</td>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
					<img src="[invitation_assets_url]/default/feature2.png" style="margin-bottom: 10px;">
					<div style="font-size:16px;line-height: 22px;color: #ff7373;" class="feature-text">Your Website<br>Feature #2</div>
					</td></tr>
					</table>
				</td>
				<td style="padding: 0px 20px;" align="center" class="feature-column">
					<table border="0" cellpadding="0" cellspacing="0"  align="center">
					<tr><td align="center">
					<img src="[invitation_assets_url]/default/feature3.png" style="margin-bottom: 10px;">
					<div style="font-size:16px;line-height: 22px;color: #957bb7;" class="feature-text">Your Website<br>Feature #3</div>
					</td></tr>
					</table>
				</td>
			</tr>
			</table>
			</div>
		</td>
	</tr>


	<tr>
		<td style="padding: 50px 10px;border: 1px solid #DDD;border-top: none;" align="center">
			<a href="[content_url]" style="font-size:25px;color: #484848;line-height: 35px;text-decoration:none;">[content_title]</a>
			<div style="font-size:14px;color: #484848;margin: 35px 15px 0px 15px;padding:15px;text-align:left;">[content_body]</div>
		</td>
	</tr>


	<tr>
		<td style="padding: 45px 10px 20px 10px;border: 1px solid #DDD;border-top:none;" align="center">
			<div style="max-width:400px;">
				<a href=""><img src="[invitation_assets_url]/default/fb.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/g.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/tw.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/in.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/vm.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/be.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
				<a href=""><img src="[invitation_assets_url]/default/db.png" style="margin:0 auto;margin-bottom: 15px; margin-right: 20px;"></a>
			</div>
		</td>
	</tr>
	</table>



	<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;max-width:600px;">
	<tr>
		<td style="padding: 20px 10px 0px 10px;">
			<div style="color: #ababab;font-family:Verdana,Arial;font-size: 12px;text-align:left;line-height:17px;">
				This email was sent to you on behalf of [sender_email]&#39;s request. You can safely <a href="[verify_invitation_url]invitation_id=[invitation_id]&adi_do=unsubscribe" style="text-decoration:none;color:#999999;color:#0084b4;text-decoration:underline;">unsubscribe</a> from these emails.<br><br>
				Your Website, Inc. 1003 Market Street, Palo Alto, CA 94001.
			</div>
		</td>
	</tr>
	</table>
</div>
</td></tr>
</table> 


</body>
</html>',

	// Campaign Invitation Message for social Networks
	'campaign_social_body_en' => 'This is a Default Social campaign invitation message for social networks.',
	'campaign_twitter_body_en' => 'This is the invitation body for Twitter invitations.',
	
);



?>