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


include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'adi_init.php');
$base_path = dirname(__FILE__);

global $adiinviter;
include_once($base_path.DIRECTORY_SEPARATOR.'adiinviter'.DIRECTORY_SEPARATOR.'adiinviter_bootstrap.php');

if(!headers_sent())
{
	header("Content-Type: text/css; charset: UTF-8");
	header("charset: UTF-8");
	header("Cache-Control: must-revalidate");
}
if($adiinviter->adiinviter_installed !== true)
{
	echo '/* AdiInviter Pro is not installed yet. */';
}
else
{
	$adiinviter->requireSettingsList(array('global', 'db_info', 'oauth'));
	$adiinviter->init_user();
	($adi_hook_code = adi_exec_hook_location('init_complete')) ? eval($adi_hook_code) : false;

	$theme_id    = $adiinviter->current_themeid; // Fetch current theme ID
	$orientation = $adiinviter->current_orientation; // Get HTML orientation (LTR or RTL)

	$adi_themes = adi_allocate_pack('Adi_Themes');
	echo $adi_themes->load_parsed_css($theme_id, $orientation);
}


?>