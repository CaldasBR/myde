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

class Adi_Session extends Adi_Session_Platform
{

	/**
	* PHP session name in your website.
	**/

	// public $session_name = "PHPSESSID";

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Initialize session for AdiInviter Pro.
	*
	* @return   nothing  : This function does not return anything.
	**/

	/*
	function init()
	{
		// Code to initialize session for AdiInviter Pro.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* This function verifies whether the session is initialized and active or not.
	*
	* @return  bool  : true -> Session is active || false -> Session is not active.
	**/

	/*
	function verify()
	{
		// Code to return true if session is active or false otherwise.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Retrieve values from currently active session for a given $key parameter.
	* 
	* @param   key   string   : Key for the values available in $_SESSION.
	* 
	* @return        string   : Value stored in $_SESSION variable.
	**/

	/*
	function get($key)
	{
		// Code to return requested session data.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Add or modify the values stored in currently active session.
	* 
	* @param   $key   string   : Key of the value stored in $_SESSION.
	* @param   $value string   : Value for the key in $_SESSION variable.
	* 
	* @return         nothing  : This function does not return anything.
	**/

	/*
	function set($key, $value)
	{
		// Code to add or modify the data stored in session.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Remove the values stored in currently active session.
	* 
	* @param   $key   string   : Key of the value stored in $_SESSION.
	* 
	* @return         bool     : true  -> Successfully removed the value.
	*                            false -> $key not found.
	**/

	/*
	function remove($key)
	{
		// Code to remove the data stored in session.
	}
	*/

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Check if the key exists in currently active session or not.
	* 
	* @param   $key   string   : Key of the value stored in $_SESSION.
	* 
	* @return         bool     : true  -> $key found.
	*                            false -> $key not found.
	**/

	/*
	function is_set($key)
	{
		// Code to check if the session key exists or not.
	}
	*/
}


?>