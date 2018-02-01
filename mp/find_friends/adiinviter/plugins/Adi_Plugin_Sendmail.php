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
class Adi_Plugin_Sendmail extends Adi_Scheduled_Plugin
{
	// Specify necessary default settings here. 
	public $default_settings = array(
		'plugin_id'            => 'adi_send_invitations',
		'plugin_on_off'        => 0,
		'plugin_title'         => 'Cron Based Or Scheduled Dispatch Of Invitation Emails',
		'plugin_description'   => "Configure scheduled dispatch of invitation emails.",
		'plugin_duration_type' => 0,
		'plugin_num_days'      => '0',
		'plugin_num_hours'     => '0',
		'plugin_num_minutes'   => '1',
	);

	// Specify Custom settings here. These settings will be displayed inside control panel to modify according to user's requirement.
	public $custom_settings  = array(
		'adiinviter_cron_hour_limit' => array(
			'value' => '20',
			'name' => 'Maximum Number Of Invitation Mails To Be Sent Per Execution',
			'description' => 'Specify maximum number of invitation mails to be sent per execution.',
			'type' => 'textbox',
		),
		'warning_message' => array(
			'value' => 'Turning this task ON will set your invitation dispatch method to cron based or scheduled dispatch.',
			'name' => '',
			'description' => '',
			'type' => 'hidden',
		),
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

		$invite_sender = adi_allocate('Adi_Send_Mail');
		$invite_sender->init();

		// Set counter to 0
		$invite_sender->mails_count = 0;

		$mails_limit = $this->settings['adiinviter_cron_hour_limit'];
		$cnt = 0;
		
		if($rs = adi_build_query_read('get_mails_from_queue', array(
			'mails_count' => $mails_limit,
		)))
		{
			while($row = adi_fetch_array($rs))
			{
				$sender_info = adi_json_decode($row['sender_info'], true);
				$invite_sender->set_sender($sender_info['name'], $sender_info['email']);
				$invite_sender->send($row['toemail'], $row['subject'], $row['message']);
				$cnt++;

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