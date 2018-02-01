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
 * Processes invitation mail queue and sends out invitations.
 */
class Adi_Plugin_Twitter_Sendmail extends Adi_Scheduled_Plugin
{
	// Specify necessary default settings here. 
	public $default_settings = array(
		'plugin_id'            => 'adi_send_invitations',
		'plugin_on_off'        => 0,
		'plugin_title'         => 'Twitter : Cron Based Dispatch Of Invitation Messages',
		'plugin_description'   => "Configure scheduled dispatch of twitter invitation messages.",
		'plugin_duration_type' => 0,
		'plugin_num_days'      => '0',
		'plugin_num_hours'     => '0',
		'plugin_num_minutes'   => '15',
	);

	// Specify Custom settings here. These settings will be displayed inside control panel to modify according to user's requirement.
	public $custom_settings  = array(

	);

	function execute()
	{
		if((int)$this->settings['plugin_on_off'] !== 1)
		{
			$this->adi->trace('Adi_Plugin_Renew_Limit.execute : Plugin is turned Off.');
			return false;
		}
		if(!$this->adi->db_allowed)
		{
			return false;
		}
		$this->adi->requireSettingsList(array('global','db_info'));
		if(!class_exists('Adi_Send_Mail_Base'))
		{
			$file_path = ADI_LIB_PATH.'invitation_handler.php';
			require_once($file_path);
		}

		$mails_limit = 200;
		$cnt = 0;

		$social_invite_senders = array();
		$adi_services = adi_allocate_pack('Adi_Services');

		$token_counters = array();

		if($rs = adi_build_query_read('get_mails_from_twitter_queue', array(
			'mails_count' => $mails_limit,
		)))
		{
			while($row = adi_fetch_array($rs))
			{
				$sender_info = adi_json_decode($row['sender_info'], true);

				$service_key = isset($sender_info['service_key']) ? $sender_info['service_key'] : '';
				if($service_key !== 'twitter') {
					continue;
				}
				if(!isset($token_counters[$sender_info['acc_tok']])) {
					$token_counters[$sender_info['acc_tok']] = 0;
				}
				if($token_counters[$sender_info['acc_tok']] > 15) {
					continue;
				}
				$token_counters[$sender_info['acc_tok']]++;

				if(!isset($social_invite_senders[$service_key]))
				{
					$social_invite_senders[$service_key] = adi_allocate('Adi_Send_Message');

					$adiinviter_services = $adi_services->get_service_details($service_key, 'info');
					$config = $adiinviter_services[$service_key]['info'];
					$config['service_key'] = $service_key;

					$social_invite_senders[$service_key]->init($config);
				}

				$social_invite_senders[$service_key]->access_token = $sender_info['acc_tok'];
				$social_invite_senders[$service_key]->access_secret = $sender_info['acc_sec'];



				$receivers_data = array(
					$row['toemail'] => $sender_info,
				);
				$social_invite_senders[$service_key]->send($row['subject'], $row['message'], $receivers_data);

				adi_build_query_write('remove_from_mail_queue', array(
					'mqueueid' => $row['mqueueid']
				));

				if(!empty($row['invitation_id']))
				{
					adi_build_query_write('update_invite_status', array(
						'status' => 'invitation_sent',
						'invitation_id' => $row['invitation_id'],
					));
				}
			}
		}
		$this->log_text($cnt.' invitations are sent from Mail-Queue.');
	}
}

?>