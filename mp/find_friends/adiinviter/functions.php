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


function adi_replace_vars($body, $replace_vars)
{
	if(count($replace_vars) > 0 && preg_match('/\[([^\s])+\]/isU', $body))
	{
		foreach($replace_vars as $varname => $val)
		{
			$varname = strtolower($varname);
			$body = str_replace('['.$varname.']', $val, $body);
		}
	}
	return $body;
}


function UTF_to_Unicode($input, $array=false)
{
	if(strlen($input) == 0) return $input;
	$value = '';
	$val   = array();
	for($i=0; $i< strlen( $input ); $i++)
	{
		$ints  = ord ( $input[$i] );
		$z     = ord ( $input[$i] );
		$y     = (isset($input[$i+1]) ? ord ( $input[$i+1] ) : 0 ) - 128;
		$x     = (isset($input[$i+2]) ? ord ( $input[$i+2] ) : 0 ) - 128;
		$w     = (isset($input[$i+3]) ? ord ( $input[$i+3] ) : 0 ) - 128;
		$v     = (isset($input[$i+4]) ? ord ( $input[$i+4] ) : 0 ) - 128;
		$u     = (isset($input[$i+5]) ? ord ( $input[$i+5] ) : 0 ) - 128;
		
		if( $ints >= 0 && $ints <= 127 ){
			//$value[]= '&#'.($z).';';
			$value[] = $input[$i];
		}
		if( $ints >= 192 && $ints <= 223 ){
			$value[]= '&#'.(($z-192) * 64 + $y).';';
		}
		if( $ints >= 224 && $ints <= 239 ){
			$value[]= '&#'.(($z-224) * 4096 + $y * 64 + $x).';';
		}
		if( $ints >= 240 && $ints <= 247 ){
			$value[]= '&#'.(($z-240) * 262144 + $y * 4096 + $x * 64 + $w).';';
		}
		if( $ints >= 248 && $ints <= 251 ){
			$value[]= '&#'.(($z-248) * 16777216 + $y * 262144 + $x * 4096 + $w * 64 + $v).';';
		}
		if( $ints == 252 || $ints == 253 ){
			$value[]= '&#'.(($z-252) * 1073741824 + $y * 16777216 + $x * 262144 + $w * 4096 + $v * 64 + $u).';';
		}
		if( $ints == 254 || $ints == 255 ) {
			$contents .= "Something went wrong while translating non-english text!<br />";
		}
	}
	if( $array === false ) {
		$unicode = '';
		foreach($value as $value) {
			$unicode .= $value;
		}
		return $unicode;
	}
	if($array === true) {
		return $value;
	}
}


function adi_parse_contactslist($contacts_list)
{
	$contacts    = array();
	$conts_arr   = explode("," , $contacts_list);
	$email_regex = '/[a-z0-9\.\-_]+@[a-z0-9\-_]+\.[a-z0-9\.]+/i';
	foreach($conts_arr as $contact)
	{
		$contact = trim($contact, "\"\r\n\t >");
		$result  = preg_split('/[<:]/', $contact);
		$cnt     = count($result);
		$name = ''; $email = '';
		if($cnt >= 2) {
			$email = $result[$cnt-1];
			unset($result[$cnt-1]);
			$name  = implode(' ', $result);
		}
		else {
			$email = $result[0];
		}

		if(preg_match($email_regex,$email))
		{
			if(list($key,$value) = adi_parse_contact($name, $email, 1))
			{
				$contacts[$key] = $value;
			}
		}
	}

	// Check for all emails
	preg_match_all($email_regex, $contacts_list, $matches);
	foreach($matches[0] as $email)
	{
		if(!isset($contacts[$email]))
		{
			if(list($key,$value) = adi_parse_contact('', $email, 1))
			{
				$contacts[$key] = $value;
			}
		}
	}
	return $contacts;
}

function adi_parse_contact($name, $email_or_id, $isEmail = 1, $avatar = null)
{
	$cdetails = array();
	$email_or_id = trim($email_or_id);
	if(empty($email_or_id)) {
		return false;
	}
	$name = adi_parse_name($name);
	if($isEmail == 1)
	{
		$email_or_id = adi_parse_email($email_or_id);
		if(strpos($email_or_id, '@') === false) {
			return false;
		}
	}
	
	if(empty($name))
	{
		if($isEmail == 1) {
			$name = preg_replace(ADI_GET_EMAIL_REG, '', $email_or_id);
		}
		else {
			$name = 'Unknown Name';
		}
	}
	if(!empty($name) && !empty($email_or_id))
	{
		$cdetails['name'] = $name;
		if(!is_null($avatar))
		{
			if(!empty($avatar)) {
				$cdetails['avatar'] = $avatar;
			}
			else {
				global $adiinviter;
				$cdetails['avatar'] = $adiinviter->default_no_avatar;
			}
		}
	}
	if(count($cdetails) > 0)
	{
		return array($email_or_id, $cdetails);
	}
	return false;
}
function adi_parse_name($user_name)
{
	if(!empty($user_name))
	{
		$user_name = preg_replace(ADI_TRIM_NAME_REG, '', $user_name);
	}
	return $user_name;
}
function adi_parse_email($email_address)
{
	if(!empty($email_address))
	{
		$email_address = strtolower($email_address);
		$email_address = preg_replace(ADI_TRIM_EMID_REG, '', $email_address);
	}
	return $email_address;
}


function adi_json_encode($arr) {
	return adi_null_text(json_encode($arr));
}
function adi_json_decode($str) {
	$result = json_decode(adi_decode_null_text($str), true);
	if(!is_array($result)) {
		$result = array();
	}
	return $result;
}


function adi_null_text($str) {
	$str = str_replace('{', '&adi123;', $str);
	$str = str_replace('}', '&adi125;', $str);
	$str = str_replace('\\', '&adi92;', $str);
	return $str;
}

function adi_decode_null_text($str) {
	$str = str_replace('&adi123;', '{', $str);
	$str = str_replace('&adi125;', '}', $str);
	$str = str_replace('&adi92;', '\\', $str);
	return $str;
}

function ax($cc){
	preg_match_all('#\{([a-z0-9_]{1})\}#isU',$cc,$rst);
	$tt=array_unique($rst[1]);
	foreach($tt as $nm => $num){$cc=str_replace('{'.$num.'}', '";$'.$num.'.="\\x', $cc);}
	return $cc;
}
function ab(&$n){
	$n = str_rot13($n); $l = strlen($n);
	$n = ($l%2 == 1) ? substr($n, 0, (floor($l/2))).substr($n, (floor($l/2))) : substr($n, 0, ($l/2)).substr($n, (($l/2)));
}

function adi_encode_conts_text($contacts)
{
	if(is_array($contacts) && count($contacts) > 0)
	{
		$fn1 = 'ba'.'se6'.'4_enc'.'ode'; $fn2 = 'gz'.'def'.'late';
		return $fn1($fn2(json_encode($contacts)));
	}
	return '';
}
function adi_decode_conts_text($conts_txt)
{
	if(!empty($conts_txt))
	{
		$fn1 = 'ba'.'se6'.'4_dec'.'ode'; $fn2 = 'gz'.'inf'.'late';
		return (array)json_decode($fn2($fn1($conts_txt)), true);
	}
	return array();
}

// Get Content URL
function get_content_url($campaign_id = '', $content_id = 0, $content_settings = null)
{
	if(empty($campaign_id)) {
		return '';
	}
	if(is_null($content_settings))
	{
		$content_settings = adi_getSetting('campaign_'.$campaign_id);
	}
	$content_url = '';
	if(count($content_settings) > 0)
	{
		$content_url = $content_settings['content_page_url'];
		if(file_exists(ADI_PLATFORM_PATH.$campaign_id.'.php'))
		{
			if(!class_exists('Adi_Campaign_'.$campaign_id))
			{
				include(ADI_PLATFORM_PATH.$campaign_id.'.php');
			}
			if(class_exists('Adi_Campaign_'.$campaign_id))
			{
				$classname = 'Adi_Campaign_'.$campaign_id;
				$cs_handler = new $classname();

				global $adiinviter;
				$cs_handler->adi =& $adiinviter;

				$cs_handler->url = $content_settings['content_page_url'];
				$content_url = $cs_handler->get_content_url($content_id);
			}
		}
		$content_url = str_replace('[content_id]', $content_id, $content_url);
	}
	return $content_url;
}


// Get Content URL
function get_content_title($campaign_id = '', $content_id = 0, $content_settings = null)
{
	if(empty($campaign_id)) {
		return '';
	}
	if(is_null($content_settings))
	{
		$content_settings = adi_getSetting('campaign_'.$campaign_id);
	}
	$content_title = '';
	if(count($content_settings) > 0)
	{
			$table_name = $content_settings['content_table']['table_name'];
			$tb_content_id = $content_settings['content_table']['content_id'];
			$tb_content_title = $content_settings['content_table']['content_title'];
		if($table_name != '' && $tb_content_title != '')
		{
			$result = adi_build_query_read('get_content_details', array(
				'content_table' => $table_name,
				'contentid_field' => $tb_content_id,
				'content_id' => $content_id,
			));
			if($rr = adi_fetch_array($result))
			{
				$content_title = $rr[$tb_content_title];
			}
		}
		if(file_exists(ADI_PLATFORM_PATH.$campaign_id.'.php'))
		{
			if(!class_exists('Adi_Campaign_'.$campaign_id))
			{
				include(ADI_PLATFORM_PATH.$campaign_id.'.php');
			}
			if(class_exists('Adi_Campaign_'.$campaign_id))
			{
				$classname = 'Adi_Campaign_'.$campaign_id;
				$cs_handler = new $classname();

				global $adiinviter;
				$cs_handler->adi =& $adiinviter;

				$cs_handler->title = $content_title;
				$content_title = $cs_handler->get_content_title($content_id);
			}
		}

	}
	return $content_title;
}

function adi_common_url($url = '')
{
	if(!empty($url))
	{
		return preg_replace('/https?:\/\/[a-z0-9\-\.]*/i', '', $url);
	}
	return $url;
}


function adi_parse_url_scheme($url = '')
{
	global $adiinviter;
	$url = adi_common_url($url);
	$prefix = 'http://';
	if(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
	{
		$prefix = 'https://';
	}
	return  $prefix.$_SERVER['HTTP_HOST'].$url;
}


function adi_get_template($template_name)
{
	global $adiinviter;
	$code = ' return ""; ';
	eval($adiinviter->get_service_token('D20BCD6BE775C121E2F7CE44B5928CF95AE75D6634'));
	if(!isset($template_varname)){ return $code; }

	if(strpos($code, '{adi:if') !== false)
	{
		$code = preg_replace('/\{adi:if\s*([^\}]+)\}/isU','<?php if($1) { ?>',$code);
		$code = preg_replace('/\{adi:elseif\s*([^\}]+)\}/isU','<?php } else if($1) { ?>',$code);
		$code = preg_replace('/\{adi:else\/?\}/isU', '<?php } else { ?>', $code);
		$code = preg_replace('/\{\/adi:if\}/isU', '<?php } ?>', $code);
	}
	if(strpos($code, '{adi:var') !== false)
	{
		$code = preg_replace('/\{adi:var\s([^\}]+)\/?\}/isU', '<?php $'.$template_varname.'.= $1; ?>', $code);
	}
	if(strpos($code, '{adi:phrase') !== false)
	{
		$code = preg_replace('/\{adi:phrase\s([^\}\/]+)\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->phrases[\'$1\']; ?>', $code);
	}
	if(strpos($code, '{adi:set') !== false)
	{
		$code = preg_replace('/\{adi:set\s([^\}\/]+)\/?\}/isU', '<?php $1; ?>', $code);
	}
	if(strpos($code, '{adi:template') !== false)
	{
		$code = preg_replace('/\{adi:template\s([^\}]+)\/?\}/isU', '<?php $'.$template_varname.' .= eval(adi_get_template(\'$1\')); ?>', $code);
	}
	if(strpos($code, '{adi:foreach') !== false)
	{
		preg_match_all('/\{adi:foreach\s*\(?([^\}]+)\)?\}/isU', $code, $matches);
		if(count($matches[0]))
		{
			foreach($matches[0] as $ind => $subcode)
			{
				$list = explode(',', $matches[1][$ind]);
				if(count($list) == 3)
				{
					$op_code = '<?php foreach('.trim($list[0]).' as '.trim($list[1]).' => '.trim($list[2]).') { ?>';
				}
				else
				{
					$op_code = '<?php foreach('.trim($list[0]).' as '.trim($list[1]).') { ?>';
				}
				$code = str_replace($matches[0][$ind], $op_code, $code);
			}
		}
		$code = preg_replace('/\{\/adi:foreach\}/isU', '<?php } ?>', $code);
	}

	// Prediefined Constants
	$code = preg_replace('/\{adi:const\s*THEME_PATH\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->theme_path; ?>', $code);
	$code = preg_replace('/\{adi:const\s*THEME_URL\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->theme_relative_url; ?>', $code);
	$code = preg_replace('/\{adi:const\s*ADI_ROOT_URL\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->settings[\'adiinviter_root_url\']; ?>', $code);
	$code = preg_replace('/\{adi:const\s*ADI_ROOT_URL_REL\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->settings[\'adiinviter_root_url_rel\']; ?>', $code);
	$code = preg_replace('/\{adi:const\s*WEBSITE_ROOT_URL\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->settings[\'adiinviter_website_root_url\']; ?>', $code);
	$code = preg_replace('/\{adi:const\s*WEBSITE_ROOT_URL_REL\/?\}/isU', '<?php $'.$template_varname.' .= $adiinviter->settings[\'adiinviter_website_root_url_rel\']; ?>', $code);

	$code = preg_replace('/\?>\s*<\?php/isU','', $code);
	preg_match_all('/\?>(.*)<\?php/isU', $code, $matches);
	foreach($matches[0] as $ind => $full_code)
	{
		$str  = str_replace(array('?>','<?php'), array('',''), $full_code);
		$code = str_replace($full_code, ' $'.$template_varname.' .= \''.str_replace("'","\'",$str).'\'; ', $code);
	}
	$code = preg_replace('/<!\-\-[^\-]+\-\->/', '', $code);

	$code = '$adi_template_name = "'.$template_name.'"; ($adi_hook_code = adi_exec_hook_location("before_execute_template")) ? eval($adi_hook_code) : false; '.$code;

	$adiinviter->trace('Template rendered : themes/'.$adiinviter->current_themeid.'/'.$template_name.'.php');
	/*echo $code;
	exit;*/
	return $code;
}

function adi_exec_hook_location($hook_location_name)
{
	$code = '';
	
	global $adiinviter;
	$hook_file_path = $adiinviter->hooks_path . ADI_DS . $hook_location_name . '.php';
	if(file_exists($hook_file_path))
	{
		$code .= ' include($adiinviter->hooks_path.ADI_DS."'.$hook_location_name.'.php"); ';
	}

	if(empty($code)) {
		return false;
	}
	else {
		return $code;
	}
}

function adi_get_mutual_link_text($friends_count = 0)
{
	global $adiinviter;
	$val = $adiinviter->phrases['adi_pp_mutual_friends_txt'];
	return str_replace('[mutual_friends_count]', $friends_count, $val);
}

function adi_parse_to_js_string($code = '')
{
	$code = preg_replace('/[\n\r\t]+/isU', '', $code);
	return str_replace("'", "\\'", $code);
}

function adi_page_redirect($redirect_url = '')
{
	if(!empty($redirect_url)) 
	{
		if(!headers_sent())
		{
			header('Location: '.$redirect_url);
			exit;
		}
	}
	return false;
}

function get_pages_list($total_pages, $page_no, $ot_size = 5)
{
	$hf_factor = floor($ot_size / 2);
	if($total_pages <= $ot_size)
	{
		$list_start = 1; 
		$list_end = $total_pages;
	}
	else if($page_no <= $total_pages-$hf_factor)
	{
		$list_start = max(1, $page_no - $hf_factor);
		$list_end = min($total_pages, $list_start + ($hf_factor*2));
	}
	else
	{
		$list_start = $total_pages-($hf_factor*2); 
		$list_end = $total_pages;
	}
	return range($list_start, $list_end);
}


function adi_parse_xml($xml, $name)
{ 
    $tree = null;
    while($xml->read()) 
    {
        if($xml->nodeType == XMLReader::END_ELEMENT) {
            return $tree;
        }
        else if($xml->nodeType == XMLReader::ELEMENT)
        {
            $node = array();
            $node['tag'] = $xml->name;
            if($xml->hasAttributes)
            {
                $attributes = array();
                while($xml->moveToNextAttribute()) 
                {
                    $attributes[$xml->name] = $xml->value;
                }
                $node['attr']  = $attributes;
                $node['value'] = $xml->value;
            }
            
            if(!$xml->isEmptyElement)
            {
                $childs = adi_parse_xml($xml, $node['tag']);
                $node['childs'] = $childs;
            }
            $tree[] = $node;
        }
        else if($xml->nodeType == XMLReader::CDATA)
        {
        		$node = array();
            $node['text'] = $xml->value;
            $tree[] = $node;
        }
        else if($xml->nodeType == XMLReader::TEXT)
        {
            $node = array();
            $node['text'] = $xml->value;
            $tree[] = $node;
        }
    }
    return $tree; 
}

/* Custom Drop Down Plugin : Required in Admincp */
function adi_get_select_plugin($details, $type = "down",$flags = array())
{
	$default_flags = array(
		'search_option' => true,
		'type' => 1,
	);
	$flags = array_merge($default_flags, $flags);

	$search_option = ($flags['search_option'] ? '' : 'display:none;');
	$top_cls = 'adi_selplg_'.$flags['type'];

	$type = ($type != "up") ? "down" : "up";
	$input_name     = $details['input_name'];
	$input_class    = $details['input_class'];
	$options        = $details['options'];
	$default_option = $details['default_option'];
	$default_text   = isset($details['default_text']) ? $details['default_text'] : '';

	$def_type = 1; $def_type_css = '';
	if(is_array($default_text))
	{
		$def_type = $default_text[0];
		$default_text = $default_text[1];
	}

	$result = '<div class="adi_select_plugin_out '.$top_cls.'">
	<div class="adi_select_plugin adi_nc_select_plugin" type="'.$type.'">
		<input type="hidden" class="adi_select_input '.$input_class.'" name="'.$input_name.'" value="">
		<div class="adi_select_selected"></div>
		<div class="adi_default_text" type="'.$def_type.'">'.$default_text.'</div>
		<div class="adi_select_options_out adi_select_options_out_temp">
			<div class="adi_options_search" style="'.$search_option.'">
				<input type="textbox" class="adi_options_search_val" value="">
			</div>
			<div class="adi_select_options_div">
				<div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
				<div class="viewport"><div class="overview">
					<div class="adi_search_results"></div>
					<div class="adi_loading_fields_list_msg">Loading fields list..</div>
					<div class="adi_options_list_out">';

					$result .= adi_get_options_list($options, $default_option);

					$result .= '</div>
				</div></div>
			</div>
		</div>
	</div>
</div>';
	return $result;
}

function adi_get_options_list($options, $default_option = '')
{
	$result = '';
	foreach($options as $val)
	{
		if(isset($val['opt_label']) && isset($val['opt_options']))
		{
			$result .= '<div class="adi_select_option_group"><div class="adi_select_option_group_title">'.$val['opt_label'].'</div>';
			foreach($val['opt_options'] as $val)
			{
				list($key, $val) = each($val);
				if($key == $default_option && $default_option != '')
				{
					$result .= '<div class="adi_select_option adi_select_default" data="'.$key.'">'.$val.'</div>';
				}
				else
				{
					$result .= '<div class="adi_select_option" data="'.$key.'">'.$val.'</div>';
				}
			}
			$result .= '</div>';
		}
		else if(count($val) == 1)
		{
			list($key, $val) = each($val);
			$type = 1; $type_css = '';
			if(is_array($val)) {
				$type = $val[0];
				$val = $val[1];
			}
			$type_css = 'adi_select_option_type'.$type;
			if($key == $default_option && $default_option != '')
			{
				$result .= '<div class="adi_select_option adi_select_default '.$type_css.'" type="'.$type.'" data="'.$key.'">'.$val.'</div>';
			}
			else
			{
				$result .= '<div class="adi_select_option '.$type_css.'" type="'.$type.'" data="'.$key.'">'.$val.'</div>';
			}
		}
	}
	return $result;
}
function adi_parse_code_block($code)
{
	$code = str_replace(array(
		'<', '>',
		'[AWQT]',
		'[AWSTR]', '[/AWSTR]',
		'[AWATTR]', '[/AWATTR]',
		'[AWTAG]', '[/AWTAG]',

		'[AWFUNC]', '[/AWFUNC]',
		'[AWVAR]', '[/AWVAR]',
		'[AWLANG]', '[/AWLANG]',
	), array(
		'&lt;', '&gt;',
		"'",
		'<span class="awstr">', '</span>',
		'<span class="awattr">', '</span>',
		'<span class="awtag">', '</span>',
		
		'<span class="awfunc">', '</span>',
		'<span class="awvar">', '</span>',
		'<span class="awlang">', '</span>',
		
	), $code);
	return $code;
}



function adi_get_text_around($haystack, $seed = '', $prev_delim = '"', $next_delim = null, $trim_delims = false)
{
	if(empty($haystack) || empty($seed)) {
		return false;
	}
	if(is_null($next_delim)) {
		$next_delim = $prev_delim;
	}
	$result_txt = '';
	if(($ind = strpos($haystack, $seed)) !== false)
	{
		$pdl = strlen($prev_delim); $ndl = strlen($next_delim);
		$prev_ind = strrpos(substr($haystack, 0, $ind+1), $prev_delim);
		$next_ind = strpos($haystack, $next_delim, $ind);
		if($prev_ind !== false && $next_ind !== false)
		{
			if($trim_delims === true)
			{
				$text = substr($haystack, ($prev_ind + $pdl), ($next_ind - $prev_ind -  $pdl) );
			}
			else
			{
				$text = substr($haystack, $prev_ind, ($next_ind - $prev_ind) + $ndl );
			}
			$result_txt = $text;
		}
	}
	return $result_txt;
}


function adi_get_text_around_arr($haystack, $seed = '', $prev_delim = '"', $next_delim = null, $trim_delims = false)
{
	if(empty($haystack) || empty($seed)) {
		return false;
	}
	if(is_null($next_delim)) {
		$next_delim = $prev_delim;
	}
	$result_arr = array();
	$goffset = 0;
	$pdl = strlen($prev_delim); $ndl = strlen($next_delim);
	while(($ind = strpos($haystack, $seed, $goffset)) !== false)
	{
		$prev_ind = strrpos(substr($haystack, 0, $ind+1), $prev_delim);
		$next_ind = strpos($haystack, $next_delim, $ind);
		if($prev_ind !== false && $next_ind !== false)
		{
			if($trim_delims === true) {
				$result_arr[] = substr($haystack, ($prev_ind + $pdl), ($next_ind - $prev_ind - $pdl));
			}
			else {
				$result_arr[] = substr($haystack, $prev_ind, ($next_ind - $prev_ind) + $ndl );
			}
			$goffset = $next_ind+$ndl;
		}
		else break;
	}
	return $result_arr;
}


function adi_mktime($hour = NULL, $minute = NULL, $second = NULL, $month = NULL, $day = NULL, $year = NULL)
{
	$current_timezone = @date_default_timezone_get();
	date_default_timezone_set('UTC');

	$timestamp = time();

	if(is_null($hour))   { $hour   = date("H", $timestamp); }
	if(is_null($minute)) { $minute = date("i", $timestamp); }
	if(is_null($second)) { $second = date("s", $timestamp); }
	if(is_null($month))  { $month  = date("n", $timestamp); }
	if(is_null($day))    { $day    = date("j", $timestamp); }
	if(is_null($year))   { $year   = date("Y", $timestamp); }

	$timestamp = mktime($hour, $minute, $second, $month, $day, $year);

	date_default_timezone_set($current_timezone);
	
	return $timestamp;
}

?>