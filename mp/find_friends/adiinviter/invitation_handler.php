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


class Adi_Invitations_Wrapper extends Adi_Internal_Errors
{
	public $config = array();
	public $service_key = array();

	public $contacts = array();
	public $contacts_count = 0;

	public $content_settings = array();
	public $cs_db_integration = false;

	public $adi = array();
	public $vars = array();
	public $initialized = false;
	public $invites_limit = 0;
	public $is_unlimited = false;
	public $sender_channel_initialized = false;

	public $content_handler = null;

	public $campaign_id = "";
	public $content_id = 0;

	public $subject = '';
	public $attached_note = '';
	public $body = '';

	public $guest_id = 0;

	public $required_settings = array('global', 'db_info');

	function set_invitation_type($campaign_id = "", $content_id = 0)
	{
		$this->campaign_id = $campaign_id;
		$this->content_id = $content_id;
		$this->vars['content_id'] = $content_id;
	}

	function set_sender($sender_email, $sender_name)
	{
		if(!empty($sender_email))
		{
			$this->vars['sender_email'] = $sender_email;
			if(empty($sender_name)) 
			{
				$new_name = preg_replace('/@.*/i', '', $sender_email);
				$this->vars['sender_name'] = $new_name;
				$this->vars['sender_username'] = $new_name;
			}
			else
			{
				$this->vars['sender_name']  = $sender_name;
				$this->vars['sender_username'] = $sender_name;
			}
		}
	}

	function set_service_configuration($config)
	{
		if(!is_array($config) || count($config) == 0) {
			$this->report_error($this->adi->phrases['invalid_service_configuration']);
			return false;
		}
		else {
			$this->config =& $config;
			$this->service_key = $this->config['service_key'];
			return true;
		}
	}

	function set_contacts($contacts)
	{
		if(!is_array($contacts) || count($contacts) == 0) {
			$this->report_error($this->adi->phrases['no_contacts_found']);
			return false;
		}
		else {
			$this->contacts =& $contacts;
			$this->contacts_count = count($this->contacts);
			return true;
		}
	}
	final function set_params()
	{
		global $adiinviter;
		$this->adi =& $adiinviter;

		// Set invitation id length
		$this->identifier_length = $this->adi->invitation_unique_id_length;
		$this->identifier_length = (!is_numeric($this->identifier_length) || $this->identifier_length < 16) ? 16 : $this->identifier_length;

		$this->adi->trace('fn.set_params : Unique ID length set to '.$this->identifier_length);
	}

	final function init($config, $contacts)
	{
		$this->set_params();

		$this->set_service_configuration($config);
		$this->set_contacts($contacts);

		$cs_types = adi_getSetting('campaigns', 'campaigns_list');
		if(isset($cs_types[$config['campaign_id']]) && !empty($config['campaign_id']))
		{
			$this->content_settings = adi_getSetting('campaign_'.$this->campaign_id);
			$this->init_content_settings();
		}
		else
		{
			$this->adi->requireSettingsList('invitation');
		}
		$this->adi->init_user();

		if(!$this->check_for_permissions_default()) {
			return false;
		}

		if(!$this->check_for_limits()) {
			return false;
		}

		// $this->get_invitation_vars();
		$this->get_common_vars_default();

		// Load invitation subject and body
		$this->load_invitation_settings();

		$this->subject = $this->parse_conditional_bbcodes($this->subject);
		$this->subject = $this->parse_bbcodes($this->subject);

		$this->attached_note = trim($this->parse_bbcodes($this->attached_note));
		$this->vars['attached_note'] = $this->attached_note;

		$this->body = $this->parse_conditional_bbcodes($this->body);
		$this->body = $this->parse_bbcodes($this->body);

		$this->issued_date = $this->adi->adi_get_utc_timestamp();

		// Initialize channel according to message sending type.
		$this->init_sender_channel();

		// Initialize sender in channel object.
		$this->invite_sender->set_sender($this->vars['sender_name'], $this->vars['sender_email']);
		return true;
	}

	function init_content_settings()
	{
		$cs_table_name = $this->content_settings['content_table']['table_name'];
		$cs_id_field   = $this->content_settings['content_table']['content_id'];

		$this->vars['content_title']    = '';
		$this->vars['content_body']     = '';
		$this->vars['category_id']      = '';
		$this->vars['alias_url_value']        = '';
		$this->vars['content_url'] = '';

		if(empty($cs_table_name) || empty($cs_id_field)) {
			$this->cs_db_integration = false;
		}
		else {
			$this->cs_db_integration = true;
		}
		$this->get_content_details();

		if(!empty($this->vars['content_url']))
		{
			$replace_vars = array(
				'website_root_url'    => $this->adi->settings['adiinviter_website_root_url'],
				'adiinviter_root_url' => $this->adi->settings['adiinviter_root_url'],
				'category_id'         => $this->vars['category_id'],
				'content_id'          => $this->vars['content_id'],
				'content_title'       => $this->vars['content_title'],
				'invite_sender_id'    => $this->adi->userid,
				'alias_url_value'     => $this->vars['alias_url_value'],
			);
			$this->vars['content_url'] = adi_replace_vars($this->vars['content_url'], $replace_vars);
		}
	}


	// Permissions Chcker
	final function check_for_permissions_default()
	{
		if(! $this->adi->can_use_adiinviter)
		{
			$this->report_error($this->adi->phrases['no_permission_to_use_adiinviter']);
			return false;
		}

		if($this->campaign_id != "")
		{
			// restricted Content ids
			if(!empty($this->content_settings['restricted_ids']))
			{
				$restricted_ids = array_map("trim", explode(',', $this->content_settings['restricted_ids']));
				if(in_array($this->content_id, $restricted_ids))
				{
					$this->report_error($this->adi->phrases['campaign_not_allowed_to_use']);
					return false;
				}
			}

			$category_id = isset($this->vars['category_id']) ? $this->vars['category_id'] : '';
			$categ_ids = is_array($category_id) ? $category_id : array($category_id);
			if(count($categ_ids) > 0 && !empty($this->content_settings['restricted_category_ids']))
			{
				// Restricted Category ids
				$r_ids = array_map("trim", explode(',', $this->content_settings['restricted_category_ids']));
				$ids = array_intersect($categ_ids, $r_ids);
				if(count($ids) > 0)
				{
					$this->report_error($this->adi->phrases['campaign_not_allowed_to_use']);
					return false;
				}
			}
		}

		$response = $this->check_for_permissions();
		return ($response !== false) ? true : false;
	}

	function get_content_details()
	{
		if($this->cs_db_integration === true)
		{
			$cs_table_name         = $this->content_settings['content_table']['table_name'];
			$cs_id_field           = $this->content_settings['content_table']['content_id'];
			$cs_body_field         = $this->content_settings['content_table']['content_body'];
			$cs_title_field        = $this->content_settings['content_table']['content_title'];
			$category_id_field     = $this->content_settings['content_table']['category_id'];
			$alias_url_value_field = $this->content_settings['content_table']['url_alias'];

			$result = adi_build_query_read('get_content_details', array(
				'content_table'   => $cs_table_name,
				'contentid_field' => $cs_id_field,
				'content_id'      => $this->content_id
			));
			if($row = adi_fetch_assoc($result))
			{
				$this->vars['content_title']   = (isset($row[$cs_title_field]) ? $row[$cs_title_field] : '');
				$this->vars['content_body']    = (isset($row[$cs_body_field])  ? $row[$cs_body_field]  : '');
				$this->vars['category_id']     = (isset($row[$category_id_field]) ? $row[$category_id_field] : '');
				$this->vars['alias_url_value'] = (isset($row[$alias_url_value_field]) ? $row[$alias_url_value_field] : '');
			}
		}

		$this->check_content_details();
		$this->crop_content_body_to_limit();
	}

	function check_content_details()
	{
		// if(!empty($this->campaign_id))
		{
			if(file_exists(ADI_PLATFORM_PATH.$this->campaign_id.'.php'))
			{
				include(ADI_PLATFORM_PATH.$this->campaign_id.'.php');
				$classname = 'Adi_Campaign_'.$this->campaign_id;
				if(class_exists($classname))
				{
					$cs_handler = new $classname();
					$cs_handler->adi =& $this->adi;

					// provide collected information
					$cs_handler->title         = $this->vars['content_title'];
					$cs_handler->body          = $this->vars['content_body'];
					$cs_handler->category_id   = $this->vars['category_id'];
					$cs_handler->url           = $this->content_settings['content_page_url'];
					$this->vars['content_url'] = $cs_handler->url;

					// Get refined values
					$this->vars['content_title'] = $cs_handler->get_content_title($this->content_id);
					$this->vars['content_body']  = $cs_handler->get_content_body($this->content_id);
					$this->vars['category_id']   = $cs_handler->get_category_id($this->content_id);
					$this->vars['content_url']   = $cs_handler->get_content_url($this->content_id);
				}
			}
		}
		return true;
	}
	function crop_content_body_to_limit()
	{
		$body = $this->vars['content_body'];
		$word_limit = is_numeric($this->content_settings['word_limit']) ? $this->content_settings['word_limit'] : 100;
		$manipulator = new HtmlWordManipulator();
		$this->vars['content_body'] = $manipulator->restoreTags($manipulator->truncate($body, $word_limit));
	}

	function check_for_permissions()
	{
		return true;
	}

	final function check_for_limits()
	{
		if(strtolower($this->adi->num_invites) == 'unlimited')
		{
			$this->invites_limit = $this->contacts_count;
			$this->is_unlimited = true;
		}
		else if(is_numeric($this->adi->num_invites))
		{
			if($this->adi->num_invites >= $this->contacts_count)
			{
				$this->invites_limit = $this->contacts_count;
			}
			else 
			{
				$this->invites_limit = $this->adi->num_invites;
			}
		}
		if($this->invites_limit <= 0) 
		{
			$this->invites_limit = 0;
			$this->report_error($this->adi->phrases['number_of_invitations_limit_reached']);
		}
		return ($this->invites_limit > 0);
	}


	// Get Replacement vars
	final function get_common_vars_default()
	{
		// Markup Codes that may contain another markup
		$this->vars['adiinviter_root_url']   = $this->adi->adi_root_url;
		$this->vars['verify_invitation_url'] = $this->adi->verify_invitation_url;
		$this->vars['register_link']         = $this->adi->settings['adiinviter_website_register_url'];
		$this->vars['website_url']           = $this->adi->website_url;
		$this->vars['invitation_assets_url'] = $this->adi->adi_root_url.'/adi_invitations';

		// Common Markup Codes
		$this->vars['service']               = $this->config['service'];
		$this->vars['website_name']          = $this->adi->settings['adiinviter_website_name'];
		$this->vars['website_logo']          = $this->adi->settings['adiinviter_website_logo'];
		$this->vars['my_website_logo']       = $this->adi->settings['adiinviter_website_logo'];
		$this->vars['attached_note']         = $this->attached_note;
		$this->vars['sender_avatar_url']  = $this->adi->default_no_avatar;

		if($this->adi->userid != 0)
		{
			$this->vars['sender_name']     = $this->adi->userfullname;
			$this->vars['sender_userid']   = $this->adi->userid;
			$this->vars['sender_username'] = $this->adi->username;
			$this->vars['sender_email']    = empty($this->adi->email) ? $this->adi->settings['adiinviter_email_address'] : $this->adi->email;
			$this->vars['sender_profile_url'] = $this->adi->profile_page_url;
			$this->vars['sender_avatar_url']  = $this->adi->avatar;
		}
		else if(!isset($this->vars['sender_email']))
		{
			$this->vars['sender_name']     = $this->adi->settings['adiinviter_sender_name'];
			$this->vars['sender_userid']   = 0;
			$this->vars['sender_username'] = $this->adi->settings['adiinviter_sender_name'];
			$this->vars['sender_email']    = $this->adi->settings['adiinviter_email_address'];
		}

		$vars = $this->get_fixed_value_markups();
		if(is_array($vars) && count($vars) > 0)
		{
			$this->vars = array_merge($this->vars, $vars);
		}
	}

	function get_fixed_value_markups()
	{
	}

	function get_variable_value_markups($receiver_id='', $receiver_name='', $isEmail = true)
	{
	}

	// Invitation Settings
	function load_invitation_settings()
	{
		$this->subject = $this->get_invitation_subject();
		$this->body    = $this->get_invitation_body();
	}

	function get_invitation_subject()
	{
		if($this->campaign_id == "")
		{
			return self::get_translated_context($this->adi->settings, 'invitation_subject', $this->adi->current_language);
		}
		else
		{
			return self::get_translated_context($this->content_settings, 'invitation_subject', $this->adi->current_language);
		}
	}
	static function get_translated_context(&$settings, $default_name, $lang_id)
	{
		$invitation_body = $settings[$default_name.'_en'];
		$varname = $default_name.'_'.$lang_id;
		if($varname != $default_name.'_en')
		{
			if(isset($settings[$varname]) )
			{
				$ib = trim($settings[$varname], " \n\t\r");
				if(!empty($ib)) {
					$invitation_body = $ib;
				}
			}
		}
		return $invitation_body;
	}
	function get_invitation_body()
	{
		if($this->campaign_id == "")
		{
			if($this->config['invitation'] == 'email')
			{
				return self::get_translated_context($this->adi->settings, 'invitation_body', $this->adi->current_language);
			}
			else
			{
				$service_key = $this->config['service_key'];
				$sname = 'invitation_social_body_'.$service_key;
				if(!isset($this->adi->settings[$sname.'_en'])) {
					$sname = 'invitation_social_body';
				}
				return self::get_translated_context($this->adi->settings, $sname, $this->adi->current_language);
			}
		}
		else
		{
			if($this->config['invitation'] == 'email')
			{
				return self::get_translated_context($this->content_settings, 'campaign_email_body', $this->adi->current_language);
			}
			else
			{
				$service_key = $this->config['service_key'];
				$sname = 'campaign_social_body_'.$service_key;
				if(!isset($this->adi->settings[$sname.'_en'])) {
					$sname = 'social_body';
				}
				return self::get_translated_context($this->content_settings, $sname, $this->adi->current_language);
			}
		}
	}
	// Attached note settings and parsing. 
	function set_attached_note($attached_note = '')
	{
		$this->attached_note = !empty($attached_note) ? strip_tags($attached_note) : '';
	}

	function parse_bbcodes($content, $vars = null)
	{
		if(is_null($vars) || !is_array($vars)) {
			$vars = $this->vars;
		}
		if(empty($content)) 
		{
			$this->adi->trace('fn.parse_bbcodes : Empty content string was provided.');
		}
		else {
			return adi_replace_vars($content, $vars);
		}
		return $content;
	}

	function parse_conditional_bbcodes($body)
	{
		if(is_numeric($this->adi->userid) && $this->adi->userid != 0) // In user mode
		{
			$body = preg_replace('#\[guest_mode\].*\[/guest_mode\]#isU', '', $body);
			$body = preg_replace('#\[/?user_mode\]#isU', '', $body);
		}
		else // In guest mode
		{
			$body = preg_replace('#\[user_mode\].*\[/user_mode\]#isU', '', $body);
			$body = preg_replace('#\[/?guest_mode\]#isU', '', $body);
		}
		if($this->campaign_id == "")
		{
			if(empty($this->vars['attached_note']) || $this->adi->settings['invitation_attachment'] == 0)
			{
				$body = preg_replace('#\[attach_note_block\].*\[/attach_note_block\]#isU', '', $body);
			}
			else {
				$body = preg_replace('#\[/?attach_note_block\]#isU', '', $body);
			}
		}
		else
		{
			if(empty($this->vars['attached_note']) || $this->adi->settings['invitation_attachment'] == 0)
			{
				$body = preg_replace('#\[attach_note_block\].*\[/attach_note_block\]#isU', '', $body);
			}
			else {
				$body = preg_replace('#\[/?attach_note_block\]#isU', '', $body);
			}
		}
		return $this->parse_body($body);
	}

	function parse_body($body)
	{
		return $body;
	}
	
	// Unique identifiers for invitations
	final function get_invitation_ids($total = 1)
	{
		$invitation_ids = array();
		do {
			$limit = ($total < 100) ? $total : 100;
			for($i = 1; $i <= $total; $i++)
			{
				$inv_id = $this->get_unique_id();
				if(!in_array($inv_id, $invitation_ids)) 
				{
					$invitation_ids[] = $inv_id;
				}
			}

			if($this->adi->db_allowed == true)
			{
				$result = adi_build_query_read('get_invitation_details', array(
					'invitation_ids' => $invitation_ids
				));

				while($row = adi_fetch_array($result))
				{
					if(in_array($row['invitation_id'], $invitation_ids))
					{
						$pos = array_search($row['invitation_id'], $invitation_ids);
						unset($invitation_ids[$pos]);
					}
				}
			}
		}while(count($invitation_ids) < $total);
		return $invitation_ids;
	}
	final function get_invitation_id()
	{
		$inv_id = '';
		do {
			$if_exists = false;
			$inv_id = $this->get_unique_id();
			if($this->adi->db_allowed == true)
			{
				$result = adi_build_query_read('get_invitation_details', array(
					'invitation_ids' => array($inv_id)
				));

				if($row = adi_fetch_array($result))
				{
					if($inv_id == $row['invitation_id'])
					{
						$if_exists = true;
					}
				}
			}
		}while($if_exists);
		return $inv_id;
	}
	final function get_unique_id($length = null)
	{
		$length = (is_numeric($length) && $length != $this->identifier_length) ? $length : $this->identifier_length;
		mt_srand();
		$possible = '0123456789'.'abcdefghjiklmnopqrstuvwxyz'.'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$last_ind = strlen($possible) - 1;
		$hash = "";
		while(strlen($hash) < $length)
		{
			$hash .= substr($possible, mt_rand(0, $last_ind), 1);
		}
		return $hash;
	}

	function init_sender_channel()
	{
		if(!$this->sender_channel_initialized)
		{
			if($this->config['invitation'] == 'email')
			{
				// For invitation mails
				$this->invite_sender = adi_allocate('Adi_Send_Mail');
				$this->invite_sender->init();
			}
			else
			{
				// For invitation messages
				$this->invite_sender = adi_allocate('Adi_Send_Message');
				$this->invite_sender->init($this->config);
			}
			$this->sender_channel_initialized = false;
		}
	}

	final function send_invitations()
	{
		$inv_count = 0; $invitation_id = '';
		$inv_ids = $this->get_invitation_ids($this->contacts_count);

		$this->guest_id = 0;
		if($this->adi->userid == 0 && $this->contacts_count > 0 && $this->adi->settings['adiinviter_store_guest_user_info'] == 1)
		{
			$sender_email = $this->vars['sender_email'];
			$sender_name  = $this->vars['sender_name'];

			$guest_exists = false;
			if($result = adi_build_query_read('check_guest_id', array(
				'field_name'  => 'email',
				'field_value' => $sender_email,
			)))
			{
				if($row = adi_fetch_array($result))
				{
					$this->guest_id = $row['guest_id'];
					$guest_exists = true;
				}
			}

			if(!$guest_exists)
			{
				$this->guest_id = 1; // guest_id for first guest inviter.
				$result = adi_build_query_read('get_max_guest_id');
				if($guest_row = adi_fetch_array($result))
				{
					$this->guest_id = $guest_row['mx'] + 1;
				}

				adi_build_query_write('add_guest_details', array(
					'guest_id'     => $this->guest_id,
					'sender_email' => $sender_email,
					'sender_name'  => $this->vars['sender_name'],
				));
			}
		}

		// Send Invitations through Emails
		if($this->config['invitation'] == 'email')
		{
			foreach($this->contacts as $id => $name)
			{
				if($inv_count < $this->invites_limit)
				{
					$invitation_id = null;
					if(is_array($inv_ids))
					{
						$invitation_id = array_shift($inv_ids);
					}
					if(is_null($invitation_id))
					{
						$invitation_id = $this->get_invitation_id();
					}
					$receiver_name = $name;
					$receiver_id = $id;

					$send_invitation_bool = adi_call_event('before_sending_invitation', array(
						'receiver_id'   => $receiver_id,
						'receiver_name' => $receiver_name,
						'service_info'  => $this->config,
						'campaign_id'   => $this->campaign_id,
						'content_id'    => $this->content_id,
						'subject'       => $this->subject,
						'body'          => $this->body,
					));
					if($send_invitation_bool !== false)
					{
						$new_vars = array(
							'invitation_id'  => $invitation_id,
							'receiver_name'  => $receiver_name,
							'receiver_email' => $id,
						);

						$vars = $this->get_variable_value_markups($receiver_id, $receiver_name, true);
						if(is_array($vars) && count($vars) > 0)
						{
							$new_vars = array_merge($new_vars, $vars);
						}

						$subject = $this->parse_bbcodes($this->subject, $new_vars);
						$body = $this->parse_bbcodes($this->body, $new_vars);

						$this->invite_sender->receiver_name = $receiver_name;
						$this->invite_sender->send($receiver_id, $subject, $body, $invitation_id);
					}

					$this->add_to_cache($invitation_id, $receiver_id, $receiver_name);
					$inv_count++;
				}
				else break;
			}
			$this->adi_flush_db_cache();
		}
		else // Send Invitation through Personal Messages (PM)
		{
			$social_invites_quota = $this->invite_sender->get_invites_quota();
			$sendmail_details = $this->invite_sender->get_sendmail_details();
			$quota_counter = 0;
			$receivers_data = array();
			foreach($this->contacts as $id => $name)
			{
				if($inv_count < $this->invites_limit) 
				{
					$invitation_id = null;
					if(is_array($inv_ids))
					{
						$invitation_id = array_shift($inv_ids);
					}
					if(is_null($invitation_id))
					{
						$invitation_id = $this->get_invitation_id();
					}

					$receiver_name = $name;
					$receiver_id = $id;

					$new_vars = array(
						'invitation_id' => $invitation_id,
						'receiver_name' => $receiver_name,
					);

					$vars = $this->get_variable_value_markups($receiver_id, $receiver_name, false);
					if(is_array($vars) && count($vars) > 0)
					{
						$new_vars = array_merge($new_vars, $vars);
					}

					if($quota_counter < $social_invites_quota)
					{
						$receivers_data[$receiver_id] = $new_vars;
						$quota_counter++;
					}
					else
					{
						$temp_body = adi_replace_vars($this->body, $vars);
						$temp_subject = adi_replace_vars($this->subject, $vars);
						$this->invite_sender->added_to_queue = true;
						$sender_info = array(
							'name'  => $this->vars['sender_name'],
							'email' => $this->vars['sender_email'],
							'social_invite' => 1,
							'service_key' => $this->config['service_key'],
						);
						$sender_info = array_merge($sender_info, $sendmail_details);
						$this->adi->add_to_mail_queue($invitation_id, $id, $temp_subject, $temp_body, $sender_info);
					}

					$this->add_to_cache($invitation_id, $receiver_id, $receiver_name);
					$inv_count++;
				}
				else break;
			}
			$this->adi_flush_db_cache();
			if(count($receivers_data) > 0)
			{
				$this->invite_sender->send($this->subject, $this->body, $receivers_data);
			}
		}

		if(!$this->is_unlimited && $this->adi->userid != 0)
		{
			$this->reduce_invites_limit($this->invites_limit);
		}

		return $inv_count;
	}

	function reduce_invites_limit($invites_limit)
	{
		if(!$this->is_unlimited && $this->adi->userid != 0)
		{
			$user_table  = $this->adi->user_table;
			$user_fields = $this->adi->user_fields;
			return adi_build_query_write('reduce_invites_limit', array(
				'user_table'  => $user_table,
				'num_invites' => $invites_limit,
				'field_name'  => $user_fields['userid'],
				'field_value' => $this->adi->userid,
			));
		}
	}


	public $delete_invites_cache = array();
	public $add_invitation_cache = array();
	public $cache_flush_counter = 0;
	public $cache_flush_limit = 100;

	function add_to_cache($invitation_id, $receiver_id, $receiver_name)
	{
		$inviter_id = $this->adi->userid;
		if($this->config['email'] == 1)
		{
			$receiver_email = $receiver_id;
			$receiver_social_id='';
		}
		else
		{
			$receiver_email = '';
			$receiver_social_id = $receiver_id;
		}

		$status = 'invitation_sent';
		if($this->invite_sender->added_to_queue)
		{
			$status = 'waiting';
		}

		$service_type   = $this->config['service_type'];
		$service_key    = $this->config['service_key'];
		$campaign_id    = $this->campaign_id;
		$content_id     = $this->content_id;
		$topic_redirect = (empty($campaign_id) ? '0' : '1');
		$visited        = 0;

		if($this->adi->userid != 0)
		{
			if(!isset($this->delete_invites_cache['inviter_id']))
			{
				$this->delete_invites_cache = array(
					'inviter_id'     => $this->adi->userid,
					'adi_query_conditions' => array(
						'campaign_id_check' => ($campaign_id != ""),
						'social_invitation' => ($this->config['email'] == 0),
						'email_invitation'  => ($this->config['email'] == 1),
					),
					'campaign_id'    => $campaign_id,
					'content_id'     => $content_id,
					'service_id'     => $service_key,
					'receiver_email' => array($receiver_email),
					'social_id'      => array($receiver_social_id),
				);
			}
			else
			{
				if($this->config['email'] == 0) {
					$this->delete_invites_cache['social_id'][] = $receiver_social_id;
				}
				else {
					$this->delete_invites_cache['receiver_email'][] = $receiver_email;
				}
			}

			$this->add_invitation_cache[] = array(
				'invitation_id'  => $invitation_id,
				'userid'         => $this->adi->userid,
				'guest_id'       => 0,
				'receiver_name'  => adi_escape_string($receiver_name),
				'social_id'      => $receiver_social_id,
				'receiver_email' => $receiver_email,
				'status'         => $status,
				'service_id'     => $this->config['service_key'],
				'issued_date'    => $this->issued_date,
				'service_type'   => $service_type,
				'campaign_id'    => $campaign_id,
				'content_id'     => $content_id,
				'topic_redirect' => $topic_redirect,
				'visited'        => $visited,
			);
		}
		else if($this->adi->userid == 0)
		{
			$this->add_invitation_cache[] = array(
				'invitation_id'  => $invitation_id,
				'userid'         => $this->adi->userid,
				'guest_id'       => $this->guest_id,
				'receiver_name'  => adi_escape_string($receiver_name),
				'social_id'      => $receiver_social_id,
				'receiver_email' => $receiver_email,
				'status'         => $status,
				'service_id'     => $this->config['service_key'],
				'issued_date'    => $this->issued_date,
				'service_type'   => $service_type,
				'campaign_id'     => $campaign_id,
				'content_id'     => $content_id,
				'topic_redirect' => $topic_redirect,
				'visited'        => $visited,
			);
		}
		$this->cache_flush_counter++;
		if($this->cache_flush_counter >= $this->cache_flush_limit)
		{
			$this->adi_flush_db_cache();
		}
		return true;
	}

	function adi_flush_db_cache()
	{
		if(isset($this->delete_invites_cache['inviter_id']))
		{
			adi_build_query_write('delete_invitation', $this->delete_invites_cache);
		}

		if(count($this->add_invitation_cache) > 0)
		{
			adi_build_query_write('add_invitation', array(
				'repeat_for' => $this->add_invitation_cache,
			));
		}

		// Reset Params
		$this->delete_invites_cache = array();
		$this->add_invitation_cache = array();
		$this->cache_flush_counter = 0;
	}
}



class Adi_Send_Mail_Base 
{
	public $sender_name   = '';
	public $sender_email  = '';

	public $receiver      = '';
	public $receiver_name = '';

	public $headers_arr   = array();
	public $headers       = '';

	public $subject       = '';
	public $body          = '';
	public $raw_body      = '';
	public $html_body     = '';
	public $plain_body    = '';

	public $invitation_id = '';

	public $delimiter     = "\r\n";
	public $charset       = "UTF-8";

	public $start_time    = 0;
	public $exec_duration = 0;
	public $end_time      = 0;

	// Settings
	public $send_multipart_email = false;
	public $use_quoted_printable = false;
	
	// Flags
	public $cron_method    = false;
	public $added_to_queue = false;
	public $adi;

	public $tags_search = array(
		"/\r/",
		"/[\n\t]+/",
		'/<head[^>]*>.*?<\/head>/i',
		'/<script[^>]*>.*?<\/script>/i',
		'/<style[^>]*>.*?<\/style>/i',
		'/<p[^>]*>/i',
		'/<br[^>]*>/i',
		'/<i[^>]*>(.*?)<\/i>/i',
		'/<em[^>]*>(.*?)<\/em>/i',
		'/(<ul[^>]*>|<\/ul>)/i',
		'/(<ol[^>]*>|<\/ol>)/i',
		'/(<dl[^>]*>|<\/dl>)/i',
		'/<li[^>]*>(.*?)<\/li>/i',
		'/<dd[^>]*>(.*?)<\/dd>/i',
		'/<dt[^>]*>(.*?)<\/dt>/i',
		'/<li[^>]*>/i',
		'/<hr[^>]*>/i',
		'/<div[^>]*>/i',
		'/(<table[^>]*>|<\/table>)/i',
		'/(<tr[^>]*>|<\/tr>)/i',
		'/<td[^>]*>(.*?)<\/td>/i',
		'/<img[^>]*>/i',
		'/<span class="_html2text_ignore">.+?<\/span>/i'
	);
	public $tags_replace = array(
		'',
		' ',
		'',
		'',
		'',
		"\n\n",
		"\n",
		'_\\1_',
		'_\\1_',
		"\n\n",
		"\n\n",
		"\n\n",
		"\t* \\1\n",
		" \\1\n",
		"\t* \\1",
		"\n\t* ",
		"\n-------------------------\n",
		"<div>\n",
		"\n\n",
		"\n",
		"\t\t\\1\n",
		"",
		""
	);
	public $tags_search_callback = array(
		'/<(a) [^>]*href=("|\')([^"\']+)\2([^>]*)>(.*?)<\/a>/i',
		'/<(h)[123456]( [^>]*)?>(.*?)<\/h[123456]>/i',
		'/<(b)( [^>]*)?>(.*?)<\/b>/i',
		'/<(strong)( [^>]*)?>(.*?)<\/strong>/i',
		'/<(th)( [^>]*)?>(.*?)<\/th>/i',
	);

	function init()
	{
		global $adiinviter;
		$this->adi =& $adiinviter;

		$this->adi->requireSettingsList(array('global','db_info'));

		$sendmail_plugin_onoff = adi_getSetting('Adi_Plugin_Sendmail', 'plugin_on_off');

		$this->cron_method = ($sendmail_plugin_onoff+0 === 1 && $this->adi->cron_mode == 0);

		$this->start_time = ADI_TIME_NOW;
		set_time_limit(60);
		$this->exec_duration = ini_get('max_execution_time');

		$this->end_time = $this->start_time + $this->exec_duration;
	}
	
	function set_sender($sender_name, $sender_email)
	{
		$this->sender_name  = $sender_name;
		$this->sender_email = $sender_email;

		$this->mail_sender_name  = $this->adi->settings['adiinviter_sender_name'];
		$this->mail_sender_email = $this->adi->settings['adiinviter_email_address'];
	}

	function set_headers()
	{
		$this->boundary = '--'.str_shuffle('np5364b94522749').uniqid('', true).'--';
		$this->headers_arr = array();

		$this->headers_arr[] = 'MIME-Version: 1.0';
		$this->headers_arr[] = 'Date: ' . date('r', time());
		$this->headers_arr[] = 'From: '.$this->mail_sender_name.' <'.$this->mail_sender_email.'>';
		$this->headers_arr[] = 'Reply-To: '.$this->mail_sender_email;
		$this->headers_arr[] = 'Return-Path: '.$this->mail_sender_email;
		$this->headers_arr[] = 'Sender: '.$this->mail_sender_email;

		if( !empty($this->adi->settings['adiinviter_website_name']) )
		{
			$this->headers_arr[] = "X-Mailer: ".$this->adi->settings['adiinviter_website_name']." Mail";
		}
		
		$this->headers_arr[] = "Importance: High";

		if($this->send_multipart_email === true)
		{
			$this->headers_arr[] = 'Content-Type: multipart/alternative; boundary='.$this->boundary;
			// $this->headers_arr[] = 'Content-Transfer-Encoding: quoted-printable' . $this->delimiter;
		}
		else
		{
			$this->headers_arr[] = 'Content-Type: text/html; charset='.$this->charset;
			if($this->use_quoted_printable) {
				$this->headers_arr[] = 'Content-Transfer-Encoding: quoted-printable';
			}
			else {
				$this->headers_arr[] = 'Content-Transfer-Encoding: 8bit';
			}
		}
	}
	function get_encoded_body($body)
	{
		if($this->use_quoted_printable)
		{
			return QuotedPrintableEncode($body);
		}
		return $body;
	}
	final function send($receiver_email, $subject, $body, $invitation_id = '')
	{
		$this->receiver      = $receiver_email;
		$this->subject       = $subject;
		$this->raw_body      = $body;
		$this->invitation_id = $invitation_id;
		$this->set_headers();

		// Add HTML tag to html emails body.
		$this->html_body = $this->raw_body;
		if(strpos($this->html_body, '<body') === false)
		{
			$this->html_body = '<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset='.$this->charset.'" />
	<meta charset="'.$this->charset.'">
	<title>'.$this->subject.'</title>
</head>
<body>
'.$this->html_body.'
</body>
</html>';
		}

		if($this->send_multipart_email === true)
		{
			// Plain Text Body
			$this->plain_body = $this->generate_plain_text($this->raw_body);
			$message = $this->delimiter . $this->delimiter . "--" . $this->boundary . $this->delimiter;
			$message .= "Content-type: text/plain;charset=" . $this->charset . $this->delimiter;
			if($this->use_quoted_printable) {
				$message .= 'Content-Transfer-Encoding: quoted-printable' . $this->delimiter . $this->delimiter;
			}
			else {
				$message .= 'Content-Transfer-Encoding: 8bit' . $this->delimiter . $this->delimiter;
			}
			$message .= $this->get_encoded_body($this->plain_body);

			// HTML Body
			$message .= $this->delimiter . $this->delimiter . "--" . $this->boundary . $this->delimiter;
			$message .= "Content-type: text/html;charset=" . $this->charset . $this->delimiter;
			if($this->use_quoted_printable) {
				$message .= 'Content-Transfer-Encoding: quoted-printable' . $this->delimiter . $this->delimiter;
			}
			else {
				$message .= 'Content-Transfer-Encoding: 8bit' . $this->delimiter . $this->delimiter;
			}
			$message .= $this->get_encoded_body($this->html_body);

			// Boundary
			$message .= $this->delimiter . $this->delimiter . "--" . $this->boundary . "--";

			// Final Combined Message
			$this->body = $message;
		}
		else
		{
			$this->body = $this->get_encoded_body($this->html_body);
		}

		$this->headers = implode($this->delimiter, $this->headers_arr);

		if($this->adi->cron_mode == false && !$this->cron_method)
		{
			$time_now = $this->adi->adi_get_utc_timestamp();
			$diff = $this->end_time - $time_now;
			if($diff < 8)
			{
				$this->cron_method = true;
			}
		}

		if($this->cron_method && $this->adi->db_allowed)
		{
			$this->added_to_queue = true;
			return $this->add_to_queue();
		}
		else 
		{
			$this->adi->trace('fn.send : Email sent to '.$this->receiver);
			return $this->send_mail();
		}
	}
	
	function send_mail()
	{
		return mail($this->receiver, $this->subject, $this->body, $this->headers);
	}
	
	function add_to_queue()
	{
		if($this->adi->db_allowed)
		{
			$sender_info = array(
				'name'  => $this->sender_name,
				'email' => $this->sender_email
			);
			$sender_info_json = adi_json_encode($sender_info);
			return adi_build_query_write('add_to_mail_queue', array(
				'invitation_id'  => $this->invitation_id,
				'receiver_email' => adi_escape_string($this->receiver),
				'mail_subject'   => adi_escape_string($this->subject),
				'mail_body'      => adi_escape_string($this->body),
				'sender_info'    => adi_escape_string($sender_info_json),
			));
		}
	}

	function _preg_callback($matches)
	{
		switch (strtolower($matches[1]))
		{
			case 'b':
			case 'strong':
				return strtoupper($matches[3]);
			case 'th':
				return strtoupper("\t\t". $matches[3] ."\n");
			case 'h':
				return strtoupper("\n\n". $matches[3] ."\n\n");
			case 'a':
				$anchor_text = trim($matches[5]);
				if(!empty($anchor_text))
				{
					return $anchor_text.'('.$matches[3].')';
				}
				return '';
		}
	}
	public function generate_plain_text($html_code)
	{
		$plain_text = preg_replace($this->tags_search, $this->tags_replace, $html_code);
		$plain_text = preg_replace_callback($this->tags_search_callback, array($this, '_preg_callback'), $plain_text);
		$plain_text = strip_tags($plain_text);

		$plain_text = preg_replace('/\n[ \t]+/', "\n", $plain_text);
		$plain_text = preg_replace('/[\n]{2,}/', "\n\n", $plain_text);
		return $plain_text;
	}
}



class Adi_Send_Message_Base 
{
	public $sender_name   = '';
	public $sender_email  = '';

	public $adi;
	public $importer = null;
	public $added_to_queue = false;
	
	function init($config = array())
	{
		global $adiinviter;
		$this->adi =& $adiinviter;
		$this->config = $config;

		$this->adi->requireSettingsList(array('global','db_info'));

		if(isset($this->config['service_key']))
		{
			$service_key = $this->config['service_key'];

			$adi_services = adi_allocate_pack('Adi_Services');
			$services_params = $adi_services->get_service_details($service_key, 'params');

			include_once(ADI_LIB_PATH.'importer.php');

			if(isset($services_params[$service_key]))
			{
				if($services_params[$service_key]['params'][2] == 1)
				{
					// For OAuth request
					$this->importer = new Adi_OAuth_Importer();
					$this->importer->init($service_key);
				}
				else
				{
					// For Curl request
					$this->importer = new Adi_Importer();
					$adi_sid = AdiInviterPro::POST('adi_csid', ADI_STRING_VARS, '0-9');
					$this->importer->initService($service_key, $adi_sid);
				}
			}
		}
	}

	function get_invites_quota()
	{
		return $this->adi->importer->get_invites_quota();
	}

	function get_sendmail_details()
	{
		return $this->adi->importer->get_sendmail_details();
	}

	function set_sender($sender_name, $sender_email)
	{
		$this->sender_name  = $sender_name;
		$this->sender_email = $sender_email;
	}

	function send($subject, $body, $receivers_data)
	{
		$this->importer->sendInvitations($subject, $body, $receivers_data);
	}
}

class Adi_Invitations_Base extends Adi_Invitations_Wrapper
{
	public $required_settings = array('global', 'db_info', 'invitation');
}

class Adi_Campaign_Base extends Adi_Invitations_Wrapper
{
	public $required_settings = array('global', 'db_info', 'campaigns');
}



function EncodeCharacter($matches)
{
	return sprintf('=%02X', Ord($matches[1]));
}

function QuotedPrintableEncode($text, $header_charset='', $break_lines=1, $email_header = 0)
{
	$line_break="\n";
	$ln=strlen($text);
	$h=(strlen($header_charset)>0);
	if($h)
	{
		$encode = array(
			'='=>1,
			'?'=>1,
			'_'=>1,
			'('=>1,
			')'=>1,
			'<'=>1,
			'>'=>1,
			'@'=>1,
			','=>1,
			';'=>1,
			'"'=>1,
			'\\'=>1,
			'['=>1,
			']'=>1,
			':'=>1,
/*
			'/'=>1,
			'.'=>1,
*/
		);
		$s=($email_header ? $encode : array());
		$b=$space=$break_lines=0;
		for($i=0; $i<$ln; ++$i)
		{
			$c = $text[$i];
			if(IsSet($s[$c]))
			{
				$b=1;
				break;
			}
			switch($o=Ord($c))
			{
				case 9:
				case 32:
					$space=$i+1;
					$b=1;
					break 2;
				case 10:
				case 13:
					break 2;
				default:
					if($o<32
					|| $o>127)
					{
						$b=1;
						$s = $encode;
						break 2;
					}
			}
		}
		if($i==$ln)
			return($text);
		if($space>0)
			return(substr($text,0,$space).($space<$ln ? QuotedPrintableEncode(substr($text,$space), $header_charset, $break_lines, $email_header) : ""));
	}
	elseif(function_exists('quoted_printable_encode'))
	{
		$different = strcmp($line_break, "\r\n");
		if($different)
			$text = str_replace($line_break, "\r\n", str_replace("\r\n", $line_break, $text));
		$encoded = preg_replace_callback('/^(f|F|\\.)/m', 'EncodeCharacter', quoted_printable_encode($text));
		if($different)
			$encoded = str_replace("\r\n", $line_break, $encoded);
		return $encoded;
	}
	for($w=$e='',$n=0, $l=0,$i=0;$i<$ln; ++$i)
	{
		$c = $text[$i];
		$o=Ord($c);
		$en=0;
		switch($o)
		{
			case 9:
			case 32:
				if(!$h)
				{
					$w=$c;
					$c='';
				}
				else
				{
					if($b)
					{
						if($o==32)
							$c='_';
						else
							$en=1;
					}
				}
				break;
			case 10:
			case 13:
				if(strlen($w))
				{
					if($break_lines
					&& $l+3>75)
					{
						$e.='='.$this->line_break;
						$l=0;
					}
					$e.=sprintf('=%02X',Ord($w));
					$l+=3;
					$w='';
				}
				$e.=$c;
				if($h)
					$e.="\t";
				$l=0;
				continue 2;
			case 46:
			case 70:
			case 102:
				$en=(!$h && ($l==0 || $l+1>75));
				break;
			default:
				if($o>127
				|| $o<32
				|| !strcmp($c,'='))
					$en=1;
				elseif($h
				&& IsSet($s[$c]))
					$en=1;
				break;
		}
		if(strlen($w))
		{
			if($break_lines
			&& $l+1>75)
			{
				$e.='='.$this->line_break;
				$l=0;
			}
			$e.=$w;
			++$l;
			$w='';
		}
		if(strlen($c))
		{
			if($en)
			{
				$c=sprintf('=%02X',$o);
				$el=3;
				$n=1;
				$b=1;
			}
			else
				$el=1;
			if($break_lines
			&& $l+$el>75)
			{
				$e.='='.$this->line_break;
				$l=0;
			}
			$e.=$c;
			$l+=$el;
		}
	}
	if(strlen($w))
	{
		if($break_lines
		&& $l+3>75)
			$e.='='.$this->line_break;
		$e.=sprintf('=%02X',Ord($w));
	}
	if($h
	&& $n)
		return('=?'.$header_charset.'?q?'.$e.'?=');
	else
		return($e);
}





?>