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


	$campaign_id = (isset($campaign_id) && !empty($campaign_id)) ? $campaign_id : "";
	$content_id = (isset($content_id)) ? $content_id : "";
		
	$contacts = array(); $config = array();
	$registered_contacts = array(); $info = array();

	$adiinviter->requireSettingsList(array('global','db_info'));
	$adi_services = adi_allocate_pack('Adi_Services');

	$adi_session_id = isset($adi_session_id) ? $adi_session_id : '';

	$adi_process_contacts = isset($adi_process_contacts) ? $adi_process_contacts : true;


	// Get Contacts
	if($importer_type == 'addressbook')
	{
		$adiinviter_services = $adi_services->get_service_details($service);
		if(isset($adiinviter_services[$service]) && !empty($service) && count($adiinviter_services) > 0)
		{
			$config = $adiinviter_services[$service]['info'];
			$config['service_key'] = $service;

			$services_params = $adiinviter_services[$service]['params'];
			$config['is_oauth'] = ($services_params[2] !== 1 ? 0 : 1 );

			include_once(ADI_LIB_PATH . 'importer.php');

			$contacts_called = false;
			if($config['is_oauth'] == 1)
			{
				$adi_importer = new Adi_OAuth_Importer();
				if($adi_importer->init($service))
				{
					$contacts = $adi_importer->get_contacts();
					$contacts_called = true;
				}
			}
			else
			{
				// Login to service and get contacts
				if(empty($user_email))
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_msg_empty_email_address'], 'fl.get_contacts');
				}
				else if(empty($user_password))
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_msg_empty_password'], 'fl.get_contacts');
				}
				else
				{
					$adi_importer = new Adi_Importer();
					if(!$adi_importer->initService($service, $adi_session_id))
					{
						$adiinviter->trace('fl.get_contacts : Service initialization failed.');
					}
					if($adi_importer->local_error != '')
					{
						$adiinviter->error->report_error('Error : '.$adi_importer->local_error, 'fl.get_contacts');
					}
					else
					{
						if(!$adiinviter->isLoaded('invitation')) {
							$adiinviter->requireSettingsList('invitation');
						}
						$contacts = $adi_importer->fetchContacts($user_email, $user_password);
						$contacts_called = true;
					}
				}
			} // Non-OAuth service

			if($contacts_called && !$adiinviter->error->show_error)
			{
				if(count($contacts) <= 0 || $contacts == false)
				{
					if($config['email'] == 1)
					{
						$adiinviter->error->report_error($adiinviter->phrases['adiinviter_no_contacts_in_addressbook'], 'fl.get_contacts');
					}
					else
					{
						$adiinviter->error->report_error($adiinviter->phrases['adiinviter_no_friends'], 'fl.get_contacts');
					}
				}
			}
			
		}
		else
		{
			$adiinviter->error->report_error($adiinviter->phrases['adi_msg_invalid_service'], 'fl.get_contacts : '.$service);
		}
	}
	else if($importer_type == 'contact_file')
	{
		$adiinviter_services = $adi_services->get_service_details('csv_inviter');
		$config = $adiinviter_services['csv_inviter']['info'];
		$config['service_key'] = 'csv_inviter';

		if(empty($csv_file_contents))
		{
			$adiinviter->error->report_error($adiinviter->phrases['empty_contact_file'], 'fl.get_contacts');
		}
		else
		{
			$adiinviter->requireSettingsList('invitation');
			include(ADI_LIB_PATH.'csv_processor.php');
			$adiinviter->cf_parser = adi_allocate('Adi_Contact_File');
			$adiinviter->cf_parser->init();

			if(!in_array($csv_file_format, $adiinviter->cf_parser->supported_formats))
			{
				$adiinviter->error->report_error($adiinviter->phrases['adi_msg_invalid_contact_file_format'], 'fl.get_contacts : '.$csv_file_format);
			}
			else
			{
				$csv_file_format = empty($csv_file_format) ? 'csv' : $csv_file_format;
				$contacts = $adiinviter->cf_parser->get_contacts_from_file($csv_file_contents, $csv_file_format);

				if(count($contacts) <= 0 || $contacts == false)
				{
					$adiinviter->error->report_error($adiinviter->phrases['zero_contacts_found_in_contact_file'], 'fl.get_contacts');
				}
			}
			
		}
	}
	else if($importer_type == 'manual_inviter')
	{
		$adiinviter_services = $adi_services->get_service_details('manual_inviter');
		$config = $adiinviter_services['manual_inviter']['info'];
		$config['service_key'] = 'manual_inviter';
		$adiinviter->requireSettingsList('invitation');
		include(ADI_LIB_PATH.'csv_processor.php');
		$cl_parser = new Adi_ContactsReader();
		$adiinviter->mi_parser =& $cl_parser;

		if(empty($contacts_list))
		{
			$adiinviter->error->report_error($adiinviter->phrases['adi_msg_empty_contacts_list'], 'fl.get_contacts');
		}
		else
		{
			$cl_parser->init_parser($contacts_list);
			$cl_parser->reset_channel();
			$contacts = $cl_parser->contacts;
			if(count($contacts) <= 0 || $contacts == false)
			{
				$adiinviter->error->report_error($adiinviter->phrases['zero_contacts_found_in_contacts_list'], 'fl.get_contacts');
			}
		}
	}
	else
	{
		$adiinviter->error->report_error($adiinviter->phrases['invalid_importer_method'], 'fl.get_contacts : '.$importer_type);
	}

	$contacts = (is_array($contacts) && count($contacts) > 0) ? $contacts : array();

	$adiinviter->all_imported_contacts($config, $contacts);

	// Remove your own contact if exists
	$self_email_exists = false;
	if(count($config) > 0)
	{
		if($config['email'] == 1)
		{
			if(isset($contacts[$adiinviter->email]))
			{
				$self_email_exists = true;
				unset($contacts[$adiinviter->email]);
			}
		}
		else
		{
			$adi_process_contacts = true;
		}
	}


	$contacts_count = count($contacts);

	if($contacts_count > $adiinviter->max_contacts_count)
	{
		$chunk_contacts = array_chunk($contacts, $adiinviter->max_contacts_count, true);
		$contacts = $chunk_contacts[0];
		$contacts_count = count($contacts);
	}

	// Load defaults
	$show_friend_adder = false;
	$show_invites_sender = false;

	if(count($contacts) > 0)
	{
		if(count($config) == 0)
		{
			$adiinviter->trace('fl.get_contacts : Service type is invalid.');
		}
		else if(!is_array($contacts) || count($contacts) == 0)
		{
			$adiinviter->trace('fl.get_contacts : Contacts array is empty.');
		}
		else // Have some contacts to process
		{
			// Pre-initialize counters
			$config['campaign_id'] = $campaign_id;
			$config['content_id'] = $content_id;
			$config['all_non_registered_count'] = count($contacts);

			$config['blocked_count'] = 0;
			$config['waiting_count'] = 0;
			$config['sent_count'] = 0;

			$config['registered_count'] = 0;
			$config['pending_requests_count'] = 0;
			$config['my_friends_count'] = 0;

			if($adi_process_contacts == true)
			{
				if($adiinviter->user_system === true)
				{
					$adiinviter->getRegisteredContacts($contacts, $registered_contacts, $info, $config);
					if($adiinviter->settings['adiinviter_show_already_registered'] == 1)
					{
						$show_friend_adder = (count($registered_contacts) > 0 ? true : false);
					}
				}
			}

			$config['non_registered_count'] = count($contacts);

			if($adi_process_contacts == true)
			{
				if(count($contacts) > 0)
				{
					$adiinviter->getInvitedContacts($contacts, $config);
				}
				
				$adiinviter->registered_contacts($config, $info, $registered_contacts, $contacts);
			}
			$show_invites_sender = (count($contacts) > 0 ? true : false);


			// Error Checking.
			$non_registered_cnt = (int)$config['all_non_registered_count'];
			if((int)$config['registered_count'] > 0 && (int)$config['non_registered_count'] == 0)
			{
				if((int)$config['registered_count'] == (int)$config['my_friends_count'])
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_are_friends'], 'fl.get_contacts');
				}
				else if((int)$config['registered_count'] == (int)$config['pending_requests_count'])
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_has_pending_friend_reqs'], 'fl.get_contacts');
				}
				else if($adiinviter->settings['adiinviter_show_already_registered'] == 0)
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_are_already_registered'], 'fl.get_contacts');
				}
			}
			else if((int)$non_registered_cnt > 0 && (int)$config['non_registered_count'] == 0)
			{
				if($non_registered_cnt == (int)$config['sent_count'])
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_invited_already'], 'fl.get_contacts');
				}
				else if($non_registered_cnt == (int)$config['blocked_count'])
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_blocked_already'], 'fl.get_contacts');
				}
				else if($non_registered_cnt == (int)$config['waiting_count'])
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_all_contacts_in_mail_queue'], 'fl.get_contacts');
				}
				else if($non_registered_cnt == ((int)$config['waiting_count']+(int)$config['blocked_count']+(int)$config['sent_count']))
				{
					$adiinviter->error->report_error($adiinviter->phrases['adi_err_no_contacts_to_send_invitation'], 'fl.get_contacts');
				}
			}
		}
	}
	else 
	{
		if($self_email_exists == true)
		{
			$adiinviter->error->report_error($adiinviter->phrases['adi_err_own_email_address_only'], 'fl.get_contacts');
		}
	}


	// Cache Contacts
	$adi_conts_model = AdiInviterPro::POST('adi_conts_model', ADI_INT_VARS);
	$adi_conts_model = $adi_conts_model != 2 ? 1 : 2;

	$adi_enable_pagination = $adi_enable_typeahead = false;
	if($adi_conts_model == 2)
	{
		$adi_enable_pagination = false;
		$adi_enable_typeahead = true;
	}

	$adi_conts_list_id = '';
	$adi_conts_model = 1;
	$cache_data = $added_friends_ids = $invitation_sent_ids = array();
	if($adiinviter->enable_contacts_cache === true && $adiinviter->error->get_error_count() == 0 && (count($contacts) > 0 || count($registered_contacts) > 0 ) )
	{
		$cache_data = array(
			'conts'       => $contacts,
			'reg_conts'   => $registered_contacts,
			'reg_info'    => $info,
			'config'      => $config,
			'conts_model' => $adi_conts_model,
			'fr_adds'     => $added_friends_ids,
			'sent_ids'    => $invitation_sent_ids,
		);
		$result = adimt_cache_contacts($cache_data);
		if($result)
		{
			$adi_conts_list_id = $result;
		}
	}

	($adi_hook_code = adi_exec_hook_location('after_contacts_import')) ? eval($adi_hook_code) : false;

?>