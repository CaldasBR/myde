<?php

	$extended_options = array(
		'mw' => $adidt_max_page_width,
		'mc' => $adidt_max_conts_columns,
	);
	$contents .= 'adjq.extend(adi,'.json_encode($extended_options).');'."\n\n";

if($adiinviter->show_recaptcha)
{
	$contents .= '
adi_captcha_init();
';
}


?>