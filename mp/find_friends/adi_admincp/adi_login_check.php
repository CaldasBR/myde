<?php

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$base_path = dirname(__FILE__);
include($base_path.DIRECTORY_SEPARATOR.'adi_admin_config.php');


$sess_name = md5($_SERVER['HTTP_HOST'].' : '.$_SERVER['HTTP_USER_AGENT'].' : '.$adiinviter_settings['controlpanel_password']);
session_name('adi'.substr($sess_name, 5, 15));
session_start();


$session_vars = array(
	'adi_pro_key'   => '',
	'last_activity' => 0,
	'adi_remember'  => 0,
);
$session_vars = array_merge($session_vars, $_SESSION);

define('ADI_USE_CUSTOM_LOGIN', 1);

$proceed_key = $session_vars['adi_pro_key'];
if( ! isset($_SESSION['adi_pro_key']) || empty($_SESSION['adi_pro_key']) ||
	 ! isset($_SESSION['last_activity']) || empty($_SESSION['last_activity']) ||
	 ! isset($_SESSION['adi_remember']) )
{
	header('Location: adi_login.php'); exit;
}
else
{
	$duration = $_SESSION['adi_remember'] == 1 ? 630720000 : 1800;
	$expire_time = $_SESSION['last_activity'] + $duration;
	if(($expire_time - time()) <= 0)
	{
		header('Location: adi_login.php'); exit;
	}
	else
	{
		$_SESSION['last_activity'] = time();
	}
}

?>