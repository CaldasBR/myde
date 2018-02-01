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


$admincp_path = dirname(__FILE__);
$config_file_path = $admincp_path . DIRECTORY_SEPARATOR . 'adi_admin_config.php';
include($config_file_path);

$sess_name = md5($_SERVER['HTTP_HOST'].' : '.$_SERVER['HTTP_USER_AGENT'].' : '.$adiinviter_settings['controlpanel_password']);
session_name('adi'.substr($sess_name, 5, 15));
$sesssion_path = ini_get('session.save_path');

/*if(!file_exists($sesssion_path))
{
	echo 'session.save_path does not contain a valid path on your server.';
	exit;
}*/

session_start();
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$logged_in = false;
$error_msg = '';
if(!isset($_GET['do']))
{
	$_GET['do'] = ''; 
}

if($_GET['do'] == 'logout') // Code to log-out.
{
	$time_hash = md5('s'.time());
	unset($_SESSION['adi_pro_key']);
	$_SESSION['last_activity'] = 0;
	$_SESSION['adi_remember']  = 0;
}

if(isset($_SESSION['adi_pro_key']))
{
	if(isset($_SESSION['last_activity']) && isset($_SESSION['adi_remember']))
	{
		$expire_time = $_SESSION['last_activity'];
		$expire_time += ($_SESSION['adi_remember'] == 1) ? 630720000 : 1800;
		if(($expire_time - time()) > 0 )
		{
			$logged_in = true;
			header('Location: adi_index.php');
		}
	}
}

$has_password = true;
if(empty($adiinviter_settings['controlpanel_username']) || empty($adiinviter_settings['controlpanel_password']))
{
	$has_password = false;
}

$error_msg = '';
if(isset($_POST['adi-set-details'])) // Code to set username and password
{
	if( !empty($_POST['adi-username']) && !empty($_POST['adi-password']) && !empty($_POST['adi-conf-password']))
	{
		if($_POST['adi-password'] == $_POST['adi-conf-password'])
		{
			include($config_file_path);
			$adiinviter_settings['controlpanel_username'] = $_POST['adi-username'];
			$adiinviter_settings['controlpanel_password'] = md5($_POST['adi-password']);
			$code = '<?php
$adiinviter_settings = '.var_export($adiinviter_settings, true).';
?>';
			file_put_contents($config_file_path, $code);
			unset($_SESSION['adi_pro_key']);
			unset($_SESSION['last_activity']);
			unset($_SESSION['adi_remember']);
			$has_password = true;
		}
		else {
			$error_msg = 'Confirm password does not match.';
		}
	}
	else {
		$error_msg = 'Please fill up all the fields.';
	}
}

// Code to log-In to AdiInviter Control Panel.
$login_success = false;
if(isset($_POST['adi-username']) && isset($_POST['adi-password']) && isset($_POST['adi-login']))
{
	$login_failed = true;
	if(trim($_POST['adi-username']) == $adiinviter_settings['controlpanel_username'] && 
		md5(trim($_POST['adi-password'])) == $adiinviter_settings['controlpanel_password'] && 
		$has_password )
	{
		$expire_time = 0;
		if(isset($_POST['remember-me']) && $_POST['remember-me'] === '1') {
			$_SESSION['adi_remember'] = 1;
			//$expire_time = 630720000;
		}
		else {
			$_SESSION['adi_remember'] = 0;
			//$expire_time = 1800;
		}
		
		$time_hash = md5('s'.time());
		//setcookie("adi_pro_key", $time_hash, time() + $expire_time);
		$_SESSION['adi_pro_key'] = $time_hash;
		$_SESSION['last_activity'] = time();
		//ob_end_flush();
		if($adiinviter_settings['first_login'] == 1)
		{
			header('Location: adi_install.php');
		}
		else {
			header('Location: adi_index.php');
		}
		$login_success = true;	
	}
	else {
		$error_msg = 'The username or password you entered is incorrect.';
	}
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=us-ascii" />
	<meta charset="UTF-8">
	<link href='adi_css/adiinviter.png' rel='shortcut icon'/>
	<title>AdiInviter Pro v1.0 - Admin Control Panel</title>
	<!-- <link href='http://fonts.googleapis.com/css?family=Nunito' rel='stylesheet' type='text/css'> -->
	<link type="text/css" href="adi_css/style.css" rel="stylesheet" />
	<script type="text/javascript">
	function checkResetForm() 
	{
		var adi_password = document.getElementById('adi_password');
		var adi_password_confirm = document.getElementById('adi_password_confirm');
		//var reset_error = document.getElementById('reset_error');
		if(adi_password.value != adi_password_confirm.value) {
			alert('Confirm password does not match.');
			return false;
		}
		else {
			//reset_error.innerHTML = '';
			return true;
		}
	}
	function remember_me_changed(m)
	{
		var v = document.getElementById('adi_remember_me');
		if(m.className == 'clicked') {
			m.className = '';
			v.value = '0';
		}
		else {
			m.className = 'clicked';
			v.value = '1';
		}
		return true;
	}
	</script>
</head>
<body class="login_body">
<!-- AdiInviter Login page key : cggkdpxrvnazrbdn --> 
	<div class="ver-off">
	<center>
		<div class="login-outer">
			<img src="adi_css/adiinviter_pro_logo.png" class="logo">
			<?php if($login_success == true) : ?>
				<font color="#fff">Redirecting you to AdiInviter Control Panel..</font>
				<script type="text/javascript">
				 setTimeout(function(){
				 	window.location = 'adi_index.php';
				 },2000);
				</script>
			<?php else : ?>
			<div class="outer_bx" style="position:relative;">
				<?php if($has_password == true) : ?>
					<div class="inner_bx_opct"></div>
					<div class="inner_bx">			<!-- Login Form : HTML Code -->
						<center>
						<form action="adi_login.php" method="post">
						<ul class="login-opts">
							<li class="label">Username</li>
							<li><input type="textbox" name="adi-username" class="txt-input" autocomplete="off" tabindex="1" id="first_focus"></li>
							<li class="sep"></li>

							<li class="label">Password</li>
							<li><input type="password" name="adi-password" class="txt-input" tabindex="2"></li>
							<li class="sep"></li>

							<li class="submit-btns">
								<div style="float: left;padding-top: 5px;">
									<!-- <input type="checkbox" name="remember-me" value="1" id="adi_remember_me" style="float: left;" tabindex="3" checked> -->
									<input type="hidden" name="remember-me" value="0" id="adi_remember_me">
									<label style="float: left;" onclick="return remember_me_changed(this);">Stay Logged In</label>
								</div>
								<input type="submit" class="submit-btn" name="adi-login" value="Login" tabindex="3">
							</li>
						</ul>
						</form>
						</center>
						<script type="text/javascript"> document.getElementById("first_focus").focus(); </script>
					<?php
					if(! empty($error_msg)) 
					{
						echo '<br><br><font class="adi_admin_err">'.$error_msg.'</font>';
					}
					?>
					<div class="login-powered">Powered By <a href="http://www.adiinviter.com" target="_blank" class="pp1 lnk2">AdiInviter Pro</a></div>
					</div>



					<?php //endif ; ?>
				<?php else : ?>
				<div class="inner_bx_opct" style="height:290px;"></div>
				<div class="inner_bx"> <!-- Pre-set Username and Password : HTML Code -->
					<center>
					<form action="adi_login.php" method="post" onsubmit="return checkResetForm();">
					<ul class="login-opts">
						<li class="sep"></li>

						<li class="label">Username</li>
						<li><input type="textbox" name="adi-username" class="txt-input" autocomplete="off" id="first_focus" tabindex="1"></li>
						<li class="sep"></li>

						<li class="label">Password</li>
						<li><input type="password" name="adi-password" class="txt-input" id="adi_password" tabindex="2"></li>
						<li class="sep"></li>

						<li class="label">Confirm Password</li>
						<li><input type="password" name="adi-conf-password" class="txt-input" id="adi_password_confirm" tabindex="3"></li>
						<li class="sep"></li>
						
						<li class="submit-btns">
							<input type="submit" class="submit-btn" name="adi-set-details" value="Save" tabindex="4">
						</li>
					</ul>
					</form>
					</center>
					<script type="text/javascript"> document.getElementById("first_focus").focus(); </script>
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
	</center>
	</div>
</body>
</html>