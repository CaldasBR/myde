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


$adiinviter->requireSettingsList('global');
?>

<form method="post" action="" class="settings_list adi_services_form">

	
	<div class="adi_inner_sect">
		<div class="adi_inner_sect_header">Manage Importer Services<a href="http://www.adiinviter.com/docs/importer-services" class="adi_docs_link" target="_blank">Importer Services Reference Documentation</a></div>
			<div class="adi_inner_sect_body">
		<table style="width:100%;" class="opts_table" cellspacing="0" cellpadding="0">

		<tr class="first">
			<td class="label_box" colspan="2">

<ul style="margin-left: 18px;">
<li style="list-style: disc outside;line-height: 25px;" class="opts_note">Double click on service boxes to toggle them On/Off. </li>
<li style="list-style: disc outside;line-height: 25px;" class="opts_note">Use Shift + Click to select multiple services.</li>
</ul>

<?php

$adi_services = adi_allocate_pack('Adi_Services');
$adiinviter_services = $adi_services->get_service_details('all', 'info');

$all_services = $adiinviter->settings['services_onoff'];

?>


<!-- Do not remove this line -->
<input type="hidden" name="subsettings[global][services_onoff]" value="">

<div class="adi_srvs_carrier" style="display:none;"></div>
<center>
<table cellpadding="0" cellspacing="0" style="margin-top:35px;">
<tr>
	<td><div class="adi_serv_on_msg" style="width:468px;display:block;">Drag & drop services below to turn them ON</div></td>
	<td></td>
	<td><div class="adi_serv_off_msg" style="width:468px;display:block;">Drag & drop services below to turn them OFF</div></td>
</tr>
<tr>
	<!-- ON Services Outer -->
	<td style="vertical-align:top;">
		<div class="adi_services_outer adi_on_services_out" style="width:auto;">
			<ul class="adi_services_ul adi_on_services_ul"><?php
			foreach($all_services['on'] as $service_key)
			{
				echo '<li class="service_out service_on adi_ms_'.$service_key.'_out" data="'.$service_key.'"><span class="service_nm adi_ms_'.$service_key.'">'.$adiinviter_services[$service_key]['info']['service'].'</span></li>';
			}
			?></ul>
			<div class="clr"></div>
		</div>
	</td>


	<td style="width:0px;padding:0px;margin:0px;"></td>


	<!-- OFF Services Outer -->
	<td style="vertical-align:top;">
		<div class="adi_services_outer adi_off_services_out" style="width:auto;overflow-y: auto;overflow-x: hidden;">
	<?php 
	$off_services = $all_services['off'];
	?>
		
		<ul class="adi_services_ul adi_off_services_ul"><?php
		if(count($off_services) > 0)
		{
			foreach($off_services as $service_key)
			{
				echo '<li class="service_out service_off adi_ms_'.$service_key.'_out" data="'.$service_key.'"><span class="service_nm adi_ms_'.$service_key.'">'.$adiinviter_services[$service_key]['info']['service'].'</span></li>';
			}
		}
			?></ul>
			<div class="clr"></div>
		</div>
	</td>
</tr>
</table>
</center>


			</td>
		</tr>
	</table>
</div>
</div>




<div class="cont_submit" style="padding: 10px;">
	<span class="services_form_response"></span>
	<input type="submit" value="Save Settings" class="btn_grn" id="adi_save_settings">
</div>


<?php 

$on_services_order  = implode(',', $all_services['on']);
$off_services_order = implode(',', $all_services['off']);

?>

<textarea style="display:none;" name="vars[on_services_order]" class="on_services_order"><?php echo $on_services_order; ?></textarea>
<textarea style="display:none;" name="vars[off_services_order]" class="off_services_order"><?php echo $off_services_order; ?></textarea>

</form>

