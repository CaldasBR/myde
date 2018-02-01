<?php
include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'find_friends'.DIRECTORY_SEPARATOR.'adi_invite_history.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

	<title>AdiInviter Pro - Invite History</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0, user-scalable=yes, width=device-width">

	<link rel="stylesheet" type="text/css" href="find_friends/adiinviter_css.php">
	<script type="text/javascript" src="find_friends/adiinviter/js/jquery.min.js"></script>
	<script type="text/javascript" src="find_friends/adiinviter/js/adiinviter.js"></script>
	<script type="text/javascript" src="find_friends/adiinviter_params.php"></script>

	<!-- Hotjar Tracking Code for queromarita.com.br -->
	<script>
		(function(h,o,t,j,a,r){
			h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
			h._hjSettings={hjid:543281,hjsv:5};
			a=o.getElementsByTagName('head')[0];
			r=o.createElement('script');r.async=1;
			r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
			a.appendChild(r);
		})(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
	</script>

</head>
<body>
<br>
	<center>
		<?php echo $contents; ?>
	</center>

</body>
</html>
