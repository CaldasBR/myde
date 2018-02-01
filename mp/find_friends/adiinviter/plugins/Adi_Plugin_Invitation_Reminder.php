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


/**
 * Plugin to send Invitation reminder emails.
 */


class Adi_Plugin_Invitation_Reminder extends Adi_Scheduled_Plugin
{
	public $plugin_id = 'Adi_Plugin_Invitation_Reminder';
	public $custom_settings = array(
	'plugin_reminder_duration' => array(
			'value' => '10',
			'name' => 'Send Invitation Reminder Emails After These Many Days',
			'description' => 'Specify after how many days you want to send invitation reminder email.',
			'type' => 'textbox',
		),
	'max_reminders_limit' => array(
		'value' => '5',
		'name' => 'Maximum Number Of Invitation Reminders To Be Sent After Above Duration',
		'description' => 'Specify maximum number of invitation reminders that can be sent to every invitation receiver.',
		'type' => 'textbox',
	),
	'plugin_subject' => array(
			'value' => array(
				'en' => '[Your Website] Invitation Reminder',
			),
			'name'  => 'Invitation Reminder Email Subject',
			'description' => 'Specify subject for invitation reminder emails.',
			'source_code_label' => 'Enter Invitation Reminder Subject For All Outgoing Email And Social Network Invitations',
			'markups_list_header' => 'Invitation Reminder Subject Markups',
			'markups_list_description' => 'You can use following markups under invitation reminder subject.',
			'type' => 'textarea',
			'subject_editor' => true,
			'bbcodes' => array(
				'receiver_name'  => "Receiver's Name",
				'receiver_email' => "Receiver's Email",
				'sender_username'    => "Sender's Username",
				'sender_email'   => "Sender's Email",
				'website_name'   => "Your Website Name",
			),
		),
	'plugin_email_body' => array(
			'value' => array(
				'en' => '<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="format-detection" content="telephone=no"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
<title>[Your Website] Invitation Reminder</title>

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
		<td style="padding: 50px 10px;border-left: 1px solid #DDD;border-right: 1px solid #DDD;" align="center">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 10px;" align="center"><div style="text-align:center;">

					<table border="0" cellpadding="0" cellspacing="0"  align="right" width="44%" class="width-500-100 mbot-30">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/browser.png" style="width:100%;max-width:243px;">
					</td></tr>
					</table>

					<table border="0" cellpadding="0" cellspacing="0"  align="left" width="56%" class="width-500-100">
					<tr><td align="left" style="padding-right: 20px;" class="leftright-text">
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;margin-bottom: 30px;">Write a short description about some great features in your website. This is just a simple test.</div>
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;">Write a short description about some great features in your website. This is just a simple test.</div>
					</td></tr>
					</table>

				</div></td>
			</tr>
			</table>
		</td>
	</tr>



	<tr>
		<td style="padding: 30px 10px;border: 1px solid #DDD;border-top:none;" align="center">
			<table border="0" cellpadding="0" cellspacing="0"  align="center" style="width:100%;">
			<tr>
				<td style="padding: 10px;" align="center"><div style="text-align:center;">

					<table border="0" cellpadding="0" cellspacing="0"  align="left" width="44%" class="width-500-100 mbot-30">
					<tr><td align="center">
						<img src="[invitation_assets_url]/default/browser.png" style="width:100%;max-width:243px;">
					</td></tr>
					</table>

					<table border="0" cellpadding="0" cellspacing="0"  align="right" width="56%" class="width-500-100">
					<tr><td align="left" style="padding-left: 20px;" class="leftright-text">
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;margin-bottom: 30px;">Write a short description about some great features in your website. This is just a simple test.</div>
						<div style="font-size:14px; font-weight: bold;color: #6a6a6a;margin-bottom: 10px;">Great Feature About Your Website</div>
						<div style="font-size:13px; line-height: 18px; color: #9d9d9d;">Write a short description about some great features in your website. This is just a simple test.</div>
					</td></tr>
					</table>

				</div></td>
			</tr>
			</table>
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
			),
			'name' => 'Invitation Reminder Email Body',
			'description' => 'Specify email body for invitation reminder emails.',
			'source_code_label' => 'Invitation Reminder Email Source Code (HTML Supported)',
			'markups_list_header' => 'Invitation Reminder Markups',
			'markups_list_description' => 'You can use following markups under invitation reminder message body.',
			'type' => 'textarea',
			'template_editor' => true,
			'bbcodes' => array(
				'receiver_name'  => "Receiver's Name",
				'receiver_email' => "Receiver's Email",

				'inviters_count' => "Number Of Inviters",

				'sender_username'  => "Sender's Username",
				'sender_email' => "Sender's Email",
				'sender_avatar_url'  => "Sender's Avatar URL",
				'sender_profile_url' => "Sender's Profile URL",

				'website_name' => "Your Website Name",
				'website_logo' => "Your Website Logo",

				'invitation_id' => "Unique Invitation Id",
				
				'service' => "Service Name",
				'service_id' => "Service Id",

				'issued_date' => "Issued Date of Invite",

				'elapsed_days' => "Elapsed Days Since Invite Was Sent",

				'reminders_count' => 'Invitation Reminder Emails Count',
			),
		),
	);

	public $default_settings = array(
		'plugin_title' => 'Invitation Reminder Emails',
		'plugin_description' => 'Send invitation reminder emails to invitation receivers.',
		'plugin_duration_type' => 0,
			'plugin_num_days'   => '1',
			'plugin_num_hours'  => '0',
	);

	function settings_filter()
	{
		$reminder_duration = (int)$this->settings['plugin_reminder_duration'];
		if( $reminder_duration == 0 ) 
		{
			return false;
		}
		return true;
	}

	function execute()
	{
		if((int)$this->settings['plugin_on_off'] !== 1)
		{
			$this->log_text('Plugin execution stopped. Plugin is turned Off.');
			return false;
		}
		if($this->adi->user_system === false)
		{
			$this->log_text('Plugin execution stopped. User system is turned Off.');
			return false;
		}
		$max_reminders = (int)$this->settings['max_reminders_limit'];
		if($max_reminders == 0)
		{
			$this->log_text('Plugin execution stopped. Invalid reminders limit('.$max_reminders.').');
			return false;
		}
		$days = (int)$this->settings['plugin_reminder_duration'];
		if($days === 0)
		{
			$this->log_text('Plugin execution stopped. Invalid reminder duration('.$days.').');
			return false;
		}

		if(!class_exists('Adi_Invitations_Wrapper'))
		{
			require_once(ADI_LIB_PATH . 'invitation_handler.php');
		}

		$plugin_num_days = (int)$this->settings['plugin_num_days'];
		$plugin_num_hours = (int)$this->settings['plugin_num_hours'];
		$plugin_num_minutes = (int)$this->settings['plugin_num_minutes'];

		$offset_diff = ($plugin_num_days * 86400) + ($plugin_num_hours * 3600) + ($plugin_num_minutes * 60);

		if($max_reminders > 0 && $days > 0)
		{
			$query = 'SELECT * FROM '.ADI_TABLE_PREFIX.'adiinviter WHERE ';
			$curr_time = $this->adi->adi_get_utc_timestamp();
			$diff = ($days * 86400);

			// Initialize sendmail calls 
			$this->init_sendmail();
			$plugin_subject = Adi_Invitations_Wrapper::get_translated_context($this->settings, 'plugin_subject', $this->adi->current_language);
			$plugin_email_body = Adi_Invitations_Wrapper::get_translated_context($this->settings, 'plugin_email_body', $this->adi->current_language);
			$this->set_email_details($plugin_subject, $plugin_email_body);

			$cond = array();
			for ($i=1 ; $i <= $max_reminders ; $i++)
			{
				$ff = $diff * $i;
				$last_timestamp = $curr_time - $ff;

				$cur_query = $query . " receiver_email != '' AND issued_date < ".$last_timestamp.' AND issued_date > '.($last_timestamp - $offset_diff);

				$ss = adi_query_read($cur_query);
				while($rr = adi_fetch_assoc($ss))
				{
					$this->invite_details = $rr;
					$this->prepare_replace_vars();
					$this->set_reminders_count($i);

					$this->send_email($rr['receiver_email']);
				}
			}
			$query .= implode(' OR ', $cond);
		}
		// echo $this->log;
	}
}

?>