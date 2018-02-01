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

class Adi_Send_Mail extends Adi_Send_Mail_Platform
{
	/**
	* Send multipart emails.
	**/

	// public $send_multipart_email = true;

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Encode emails using Quoted Printable Encoding.
	**/

	// public $use_quoted_printable = false;

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* MIME headers delimiter string.
	**/

	// public $delimiter = "\r\n";

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Charset value for MIME header content-type.
	**/

	// public $charset = "UTF-8";

/*+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-*/

	/**
	* Email sending function of AdiInviter Pro.
	*
	* @see       : Following properties are available in this function :
	*
	* @property  : $this->receiver    : Receiver's email address.
	* @property  : $this->subject     : Subject of email.
	* @property  : $this->body        : Final email body.
	* @property  : $this->headers_arr : MIME headers array
	* @property  : $this->headers     : Final MIME headers string built by 
	*                                   combining $this->headers_arr with 
	*                                   the delimiter $this->delimiter.
	*
	* @property  : $this->plain_body  : Plain text body. 
	*                                   If $this->send_multipart_email = true
	*
	* @property  : $this->html_body   : HTML content body.
	**/

	/*
	function send_mail()
	{
		//Code for sending emails.
	}
	*/
}


?>