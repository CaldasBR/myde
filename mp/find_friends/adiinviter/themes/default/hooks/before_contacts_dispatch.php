<?php

if($adi_current_model == 'popup' && ($show_friend_adder || $show_invites_sender)) // For AdiInviter Popup Model
{
	$config_json = json_encode($config);
	$show_friend_adder_jsval   = var_export((bool)$show_friend_adder, true);
	$show_invites_sender_jsval = var_export((bool)$show_invites_sender, true);

	$chunk_size = 50;
	$registerd_conts_json_chunks = array(); 
	$nonregistered_conts_json_chunks = array();

	$friend_adder_container_html = '{html:""}';
	$profile_page_system_enabled = var_export((bool)$adiinviter->profile_page_system, true);
	$mutual_friends_json = '{}';

	// Friend Adder
	if($show_friend_adder)
	{
		// Get Container
		$container_html = eval(adi_get_template('friend_adder_html'));
		$friend_adder_container_html = json_encode(array('html' => $container_html));

		$profile_page_system_enabled = var_export((bool)$adiinviter->profile_page_system, true);

		// Create Chunks
		if(count($registered_contacts) > $chunk_size) {
			$reg_chunks = array_chunk($registered_contacts, $chunk_size, true);
		}
		else {
			$reg_chunks = array($registered_contacts);
		}

		$mutual_friends = array();
		foreach($reg_chunks as $reg_contacts)
		{
			$result = array();
			foreach($reg_contacts as $reg_id => $reg_details)
			{
				if(count($reg_details['friends']) > 0) 
				{
					foreach($reg_details['friends'] as $fr_id)
					{
						$mutual_friends[$fr_id] = array(
							'username' => $info[$fr_id]['username'],
							'avatar'   => $info[$fr_id]['avatar'],
							'profile_page' => $info[$fr_id]['profile_page_url'],
						);
					}
				}
				$tmp = array(
					$reg_id,                       /* userid        */
					$info[$reg_id]['username'],    /* username      */
					$reg_details['email'],         /* email         */
					$reg_details['name'],          /* name          */
					$info[$reg_id]['avatar'],      /* avatar        */
					$reg_details['friends'],       /* friends       */
					$reg_details['friend_status'], /* friend_status */
					adi_get_mutual_link_text(count($reg_details['friends'])), /* MF link text */
				);
				$result[] = $tmp;
			}
			$registerd_conts_json_chunks[] = json_encode($result);
		}
		$mutual_friends_json = json_encode($mutual_friends);
	}

	// Invite Sender
	$invite_sender_container_html = '{html:""}';
	if($show_invites_sender)
	{
		$container_html = eval(adi_get_template('invite_sender_html'));
		$invite_sender_container_html = json_encode(array('html' => $container_html));

		if(count($contacts) > $chunk_size) {
			$conts_chunks = array_chunk($contacts, $chunk_size, true);
		}
		else {
			$conts_chunks = array($contacts);
		}

		foreach($conts_chunks as $nonreg_contacts)
		{
			$result = array();
			foreach($nonreg_contacts as $id => $details)
			{
				$details['status'] = (isset($details['status']) && !empty($details['status']) ? $details['status'] : 'never_invited');
				if($adiinviter->settings['adiinviter_invite_already_invited'] == 1) {
					$selectable = (in_array($details['status'], array('blocked'))) ? 0 : 1;
				}
				else {
					$selectable = (in_array($details['status'], array('invitation_sent','blocked','waiting'))) ? 0 : 1;
				}
				if(!isset($details['avatar'])) { $details['avatar'] = ''; }

				$tmp = array(
					($config['email'] == 1) ? $id : '',                      /* 'email' => */
					($config['email'] != 1) ? $id : '',                      /* 'social_id' => */
					UTF_to_Unicode($details['name']),                        /* 'name' => */
					$details['avatar'],                                      /* 'avatar' => */
					$adiinviter_invitation_statuses[$details['status']],     /* 'status' => */
					($selectable == 1 ? 'reg_conts' : 'reg_conts_disabled'), /* 'class' => */
					$selectable,                                             /* 'is_selectable' => */
				);
				$result[] = $tmp;
			}
			$nonregistered_conts_json_chunks[] = json_encode($result);
		}
	}
	$contents .= eval(adi_get_template('popup_show_contacts'));
	unset($registerd_conts_json_chunks);
	unset($nonregistered_conts_json_chunks);
}



?>