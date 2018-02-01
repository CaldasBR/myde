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
 * Auto renew number of invitations limit.
 */
class Adi_Plugin_Renew_Limit extends Adi_Scheduled_Plugin
{
	public $custom_settings  = array();
	public $default_settings = array(
		'plugin_id'            => 'adi_renew_limit',
		'plugin_on_off'        => 1,
		'plugin_title'         => 'Renew Number Of Invitations Limit For Users And Usergroups',
		'plugin_description'   => "This task will renew number of invitations limit set for users and usergroups.",
		'plugin_duration_type' => 1,
		'plugin_date'          => '1',
		'plugin_hour'          => '0',
	);
	function execute()
	{
		if((int)$this->settings['plugin_on_off'] !== 1)
		{
			$this->adi->trace('Adi_Plugin_Renew_Limit.execute : Plugin is turned Off.');
			return false;
		}
		if($this->adi->user_system === true && $this->adi->db_allowed === true)
		{
			// Assign Limit according to usergroup permissions
			$perms = $this->adi->permissions->getPermsForAllUsergroups(-1);

			$user_table  = $this->adi->user_table;
			$user_fields = $this->adi->user_fields;

			if(!empty($this->adi->user_fields['usergroupid']))
			{
				// Update Limit in user table
				foreach ($perms as $gid => $ug_perms) 
				{
					$num_invites = $ug_perms[$this->adi->last_num_invites_ind];
					adi_build_query_write('update_invites_limit', array(
						'user_table' => $user_table,
						'num_invites' => $num_invites,
						'field_name' => $user_fields['usergroupid'],
						'field_value' => $gid,
					));
				}
			}
			else if(!empty($this->adi->usergroup_mapping_table))
			{
				$table_name   = $this->adi->usergroup_mapping_table;
				$table_fields = $this->adi->usergroup_mapping_fields;
				// Update using separate mapping table.
				foreach ($perms as $gid => $ug_perms) 
				{
					$num_invites  = $ug_perms[$this->adi->last_num_invites_ind];
					adi_build_query_write('update_invites_limit_mapping', array(
						'user_table' => $user_table,
						'usergroup_mapping_table' => $table_name,
						'user_userid_field' => $user_fields['userid'],
						'mapping_userid_field' => $table_fields['userid'],
						'num_invites' => $num_invites,
						'mapping_usergroupid_field' => $table_fields['usergroupid'],
						'usergroupid' => $gid,
					));
				}
			}
			else
			{
				if(count($perms) == 2)
				{
					$guest_usergroupid = $this->adi->getGuestUsergroupId();
					unset($perms[$guest_usergroupid]);

					foreach($perms as $gid => $ug_perms)
					{
						$num_invites = $ug_perms[$this->adi->last_num_invites_ind];
						adi_build_query_write('update_invites_limit_all', array(
							'user_table' => $user_table,
							'num_invites' => $num_invites,
						));
					}
				}
				$this->adi->trace('Adi_Plugin_Renew_Limit.execute : usergroup system is turned off.');
				$this->log_text('Usergroup system is turned off.');
			}
			
		}
	}
}

?>