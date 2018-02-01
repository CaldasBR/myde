<?php

/*********************** Theme Config *************************/
// Number of invites to be displayed on one page in Invite History.
$adi_invites_page_size = 25;

// Maximum width in pixels the contacts display interface can occupy. (Inpage Model Only)
$adidt_max_page_width = 1224;

// Maximum number of columns for contacts display interface. (Popup Model Only)
$adidt_max_conts_columns = 3;

// Popular Services limit
$adidt_popular_services_limit = 6;

// Popular services 
$adidt_popular_services = array(
	'gmail', 'hotmail', 'yahoo', 
	'twitter', 'aol', 'linkedin',
	'mailchimp', 'xing', 'eventbrite',
);

/**************************************************************/

if(isset($adi_current_model) && in_array($adi_current_model, array('popup', 'inpage')))
{
	$adiinviter->requireSettingsList(array('oauth'));
	$on_services = $adiinviter->settings['services_onoff']['on'];
	if(count($on_services) > 0 && count($adidt_popular_services) > 0)
	{
		$adidt_popular_services_final = array_intersect($adidt_popular_services, $on_services);
		foreach($adidt_popular_services_final as $ind => $service_key)
		{
			if(isset($adiinviter->settings[$service_key.'_importer_use_oauth']))
			{
				if((int)$adiinviter->settings[$service_key.'_importer_use_oauth'] === 0)
				{
					unset($adidt_popular_services_final[$ind]);
				}
			}
		}
		$adidt_popular_services_final = array_slice($adidt_popular_services_final, 0, $adidt_popular_services_limit);
	}
}

?>