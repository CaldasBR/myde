<?php
// define('ADI_DEV_MODE', 0);

if(!headers_sent())
{
	$inpage_init_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'find_friends'.DIRECTORY_SEPARATOR.'adi_inpage.php';
	include($inpage_init_path);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>

	<title>AdiInviter Pro - Inpage Model</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.5, minimum-scale=1.0, user-scalable=yes, width=device-width">

	<!-- <link href='https://fonts.googleapis.com/css?family=Montserrat:400' rel='stylesheet' type='text/css'> -->
	<!-- <link href='http://fonts.googleapis.com/css?family=Raleway:400' rel='stylesheet' type='text/css'> -->

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
	
<style type="text/css">
body::-webkit-scrollbar {
    width: 7px;
}

body::-webkit-scrollbar-track {
    /*-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);*/
    padding-right: 3px;
}

body::-webkit-scrollbar-thumb {
  background-color: #C2C2C2;
  /*margin-right: 2px;*/
  /*outline: 1px solid slategrey;*/
}

body	{
	background-color: #FFF;
}

</style>

</head>
<body>
<br>

	<center>
		<?php echo $contents; ?>
	</center>

</body>
</html>
