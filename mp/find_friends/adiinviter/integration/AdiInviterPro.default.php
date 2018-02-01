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
+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+*/


/*
x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x
IMPLEMENT OR MODIFY PROPERTIES OF THIS CLASS TO CREATE CUSTOM FUNCTIONALITIES OR 
TO OVERRIDE DEFAULT BEHAVIOUR OF ADIINVITER PRO SYSTEM.
x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x-x
*/

class AdiInviterPro extends AdiInviterPro_Platform
{

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Absolute path to your website root directory.
	* This will be prefixed to $adi_admincp_folder value for obtaining path to AdiInviter admincp directory.
	* If empty value is provided, then outer directory of ADI_BASE_PATH will be considered as website_root_path.
	*
	* NOTE : Do not specify trailing slash.
	**/

	//public $website_root_path = '';

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Relative path to AdiInviter admincp directory from website root path.
	*
	* NOTE : Do not specify trailing slash.
	**/

	//public $adi_admincp_folder = 'find_friends/adi_admincp';

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* z-index for lowest or deepest popup interface in AdiInviter Pro.
	**/

	//public $lowest_zindex = 50;

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Format for displaying invitation issued date.
	* @see : http://php.net/manual/en/function.date.php#refsect1-function.date-parameters
	**/

	//public $date_display_format = 'M d, Y';

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Invitation id length.
	*
	* Minimum Allowed value : 16
	* Maximum Allowed value : 50
	**/

	//public $invitation_unique_id_length = 16;

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Toggle between User registration and Visitors system.
	*
	* @var 	boolean 	: 
	* 			true  : For registration system
	* 			false : For Visitors system (If your website does not have sign up facility)
	**/

	//public $user_registration_system = true;

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Custom URLS to AdiInviter Pro interfaces in your website.
	*
	* @see   To change the value of [website_root_url] markup :
	*        Go to AdiInviter Pro Admin Panel -> Settings -> Website Details.
	**/

	// public $popup_model_url       = '[website_root_url]/inviter_popup.php';
	// public $inpage_model_url      = '[website_root_url]/inviter_inpage.php';
	// public $invite_history_url    = '[website_root_url]/invite_history.php';
	// public $verify_invitation_url = '[website_root_url]/verify_invitation.php';

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* This function returns the userid of a currently logged in user in your website.
	*
	* @return   int   : userid of a currently logged in user.
	**/

	/*
	function getLoggedInUserId()
	{
		//Code to fetch and return the userid of a currently logged in user in your website.		
		//Return 0 for not logged in user.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/	

	/**
	* Function to return the Usergroup id of not-logged in users in your website.
	* By default usergroup id for not-logged in users is considered 0.
	* 
	* @return   mixed(int or string)   : Usergroup id of not-logged in users.
	**/

	/*
	function getGuestUsergroupId()
	{
		return 0;
		//Or write a code to fetch and return the Usergroup Id of not-logged in users.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/	

	/**
	* This function returns an absolute url (full url) to user's avatar or profile picture image.
	* Return no-avatar or default avatar image url if user doesn't have any avatar set.
	*
	* @param    int          : Userid of a user.
	* @param    string       : Username of a user.
	* @param    string       : Email address of a user.
	* @param    string       : Avatar Value for requested userid
	*
	* @Avatar Value setting  : Go to following location under AdiInviter Pro Admin Panel :
	* AdiInviter Pro Admin Panel -> Integration -> User System -> User -> Avatar Mapping
	*
	* @return   string(non empty string)   : Absolute URL to user's avatar image.
	**/

	/*
	function getUserAvatarUrl($userid, $username = '', $email = '', $avatar_value = '')
	{
		// Code to fetch and return the full url (absolute url) to user's avatar image.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/	

	/**
	* This function returns an absolute url (full url) to user's profile page in your website.
	*
	* @param    array     : Associative array of user details. 
	*
	* @example            : Example array of user details passed to this function :
	*
	* array["userid"]    ->  Userid of a user.
	* array["username"]  ->  Username of a user.
	* array["email"]     ->  Email address of a user.
	*
	* @return   string(non empty string)   : Absolute URL to user's profile page.
	*/

	/*
	function getProfilePageURL($params = array())
	{
		//Code to fetch and return the full url (absolute url) to user's profile page.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/		

	/**
	* This function returns an associative array of all usergroups in your website.
	* 
	* @return   array(non empty associative array)  : All usergroups array.
	*
	* @example : Return example 
	*
	*        array(
	*           "[usergroup_id #1]" => "[usergroup_name #1]",
	*           "[usergroup_id #2]" => "[usergroup_name #2]",
	*            ...
	*           "[usergroup_id #n]" => "[usergroup_name #n]",
	*        );
	*
	**/

	/*
	function getAllUsergroupsInfo()
	{
		//Code to fetch and return an associative array of all usergroups in your website.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/	

	/**
	* Function to create a friend, follower or similar connection between any 2 users in your website.
	*
	* @param    int   : Userid of a user requesting friend/follower connection.
	* @param    int   : Userid of a user receiving friend/follower connection.
	**/

	/*
	function add_friend_request_record($userid, $friend_id)
	{
		//Code to create a friend, follower or any similar connection between users
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Function to choose AdiInviter Pro language translation on the fly.
	* You can choose a language based on following example settings in your website
	* 
	* @example   : User account settings in your website.
	* @example   : User's preferred language settings in your website
	* @example   : User's localization or regional settings in your website
	* @example   : Default language chosen by user to view your website.
	*
	* @return   string   : Valid AdiInviter Pro language id.
	*
	* @see   List of valid language ids   :   ./adiinviter/adi_cache.php
	* @see   For default language id, use :   $this->current_language
	**/

	/*
	function get_lang_id()
	{
		//Code to return AdiInviter Pro language id.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Function to choose AdiInviter Pro theme on the fly.
	* You can choose a theme based on following example settings in your website
	* 
	* @example   : User account settings in your website.
	* @example   : User's preferred style settings in your website
	* @example   : User's layout or device settings identified in your website
	*
	* @return   string   : Valid AdiInviter Pro theme id.
	*
	* @see   To see the list of valid theme ids, go to :
	*        AdiInviter Pro Admin Panel -> Themes
	*        Refer to Theme Id column there
	*
	* @see   For default theme id, use :   $this->default_themeid 
	*
	**/

	/*
	function get_theme_id()
	{
		//Code to return AdiInviter Pro theme id for a user in your website.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

}

?>