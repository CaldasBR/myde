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


$init_file_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'adi_init.php';
include_once($init_file_path);
$adiinviter->requireSettingsList(array('global','db_info'));

if(!headers_sent())
{
	header("Content-type: text/css");
	header("charset: UTF-8");
	header("Cache-Control: must-revalidate");
}


$adi_services = adi_allocate_pack('Adi_Services');
$adiinviter_services = $adi_services->get_service_details('all', 'logos');


foreach($adiinviter_services as $service_key => $logos)
{
	echo '.adi_ms_'.$service_key.' { background-image: url('.$adiinviter->settings['adiinviter_root_url_rel'].'/adi_services/'.$service_key.'.png); }';
	echo "\n";
}

// $ts = time();
$ts='';

echo <<<HTML

/* Left Menu icons CSS */

.lm_itm_dashboard { background-image: url(adi_css/images/left_menu/home.png?$ts); }
.lm_itm_dashboard:hover, .left_menu_current .lm_itm_dashboard { 
	background-image: url(adi_css/images/left_menu/home_hover.png?$ts); 
}


.lm_itm_global { background-image: url(adi_css/images/left_menu/settings.png?$ts); }
.lm_itm_global:hover, .left_menu_current .lm_itm_global { 
	background-image: url(adi_css/images/left_menu/settings_hover.png?$ts); 
}

.lm_itm_database { background-image: url(adi_css/images/left_menu/integration.png?$ts); }
.lm_itm_database:hover, .left_menu_current .lm_itm_database { 
	background-image: url(adi_css/images/left_menu/integration_hover.png?$ts); 
}

.lm_itm_manage { background-image: url(adi_css/images/left_menu/services.png?$ts); }
.lm_itm_manage:hover, .left_menu_current .lm_itm_manage { 
	background-image: url(adi_css/images/left_menu/services_hover.png?$ts); 
}

.lm_itm_invitation { background-image: url(adi_css/images/left_menu/invitations.png?$ts); }
.lm_itm_invitation:hover, .left_menu_current .lm_itm_invitation { 
	background-image: url(adi_css/images/left_menu/invitations_hover.png?$ts); 
}

.lm_itm_content { background-image: url(adi_css/images/left_menu/campaigns.png?$ts); }
.lm_itm_content:hover, .left_menu_current .lm_itm_content { 
	background-image: url(adi_css/images/left_menu/campaigns_hover.png?$ts); 
}

.lm_itm_plugins { background-image: url(adi_css/images/left_menu/tasks.png?$ts); }
.lm_itm_plugins:hover, .left_menu_current .lm_itm_plugins { 
	background-image: url(adi_css/images/left_menu/tasks_hover.png?$ts); 
}

.lm_itm_user { background-image: url(adi_css/images/left_menu/permissions.png?$ts); }
.lm_itm_user:hover, .left_menu_current .lm_itm_user { 
	background-image: url(adi_css/images/left_menu/permissions_hover.png?$ts); 
}

.lm_itm_language { background-image: url(adi_css/images/left_menu/language.png?$ts); }
.lm_itm_language:hover, .left_menu_current .lm_itm_language { 
	background-image: url(adi_css/images/left_menu/language_hover.png?$ts); 
}

.lm_itm_themes { background-image: url(adi_css/images/left_menu/themes.png?$ts); }
.lm_itm_themes:hover, .left_menu_current .lm_itm_themes { 
	background-image: url(adi_css/images/left_menu/themes_hover.png?$ts); 
}

.lm_itm_updates { background-image: url(adi_css/images/left_menu/updates.png?$ts); }
.lm_itm_updates:hover, .left_menu_current .lm_itm_updates { 
	background-image: url(adi_css/images/left_menu/updates_hover.png?$ts); 
}
HTML;


?>