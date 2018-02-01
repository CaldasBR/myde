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


// Admin Login Check file.
$login_check_file = dirname(__FILE__).DIRECTORY_SEPARATOR.'adi_login_check.php';
if(file_exists($login_check_file))
{
	include_once($login_check_file);
}

// Path to AdiInviter root directory
if(!isset($adi_lib_path) || $adi_lib_path == '') {
	$adi_lib_path = dirname(dirname(__FILE__));
}

// Init AdiInviter Pro
if(!defined('ADI_USE_CUSTOM_LOGIN')) {
	define('ADI_USE_CUSTOM_LOGIN', 0);
}
define('ADI_ADMIN_PANEL', 1);
require_once($adi_lib_path . DIRECTORY_SEPARATOR . 'adiinviter' . DIRECTORY_SEPARATOR . 'adiinviter_bootstrap.php');
if(!headers_sent())
{
	header("charset: UTF-8");
	header("Cache-Control: must-revalidate");
}

?>