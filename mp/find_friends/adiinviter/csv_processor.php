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





// To map characters in Windows (Western-1252) Encoding to UTF-8
$adi_win1252_mapping_chars = array();
$GLOBALS['adi_win1252_mapping_chars'] =& $adi_win1252_mapping_chars;
$adi_win1252_mapping_chars = array(
	128 => "\xe2\x82\xac",

	130 => "\xe2\x80\x9a",
	131 => "\xc6\x92",
	132 => "\xe2\x80\x9e",
	133 => "\xe2\x80\xa6",
	134 => "\xe2\x80\xa0",
	135 => "\xe2\x80\xa1",
	136 => "\xcb\x86",
	137 => "\xe2\x80\xb0",
	138 => "\xc5\xa0",
	139 => "\xe2\x80\xb9",
	140 => "\xc5\x92",

	142 => "\xc5\xbd",

	145 => "\xe2\x80\x98",
	146 => "\xe2\x80\x99",
	147 => "\xe2\x80\x9c",
	148 => "\xe2\x80\x9d",
	149 => "\xe2\x80\xa2",
	150 => "\xe2\x80\x93",
	151 => "\xe2\x80\x94",
	152 => "\xcb\x9c",
	153 => "\xe2\x84\xa2",
	154 => "\xc5\xa1",
	155 => "\xe2\x80\xba",
	156 => "\xc5\x93",

	158 => "\xc5\xbe",
	159 => "\xc5\xb8",
);

// "\xC2\xA0" to "\xC2\xBF"
for($i = 160 ; $i <= 191 ; $i++)
{
	$adi_win1252_mapping_chars[$i] = chr(194).chr($i);
}

// "\xC3\x80" to "\xC3\xBF"
for($i = 192 ; $i <= 255 ; $i++)
{
	$adi_win1252_mapping_chars[$i] = chr(195).chr($i-64);
}

function adi_utf8_chr_replace1($matches)
{
	global $adi_win1252_mapping_chars;

	$match = $matches[0];

	$fb = $match{0};
	$fb_ascii = ord($fb);

	$rem_str = ($ch = substr($match, 1)) ? $ch : '';

	if($fb == $rem_str)
	{
		$rem_str = '';
	}

	if(isset($adi_win1252_mapping_chars[$fb_ascii]))
	{
		return $adi_win1252_mapping_chars[$fb_ascii] . $rem_str;
	}

	return $match;
}




class Adi_CsvReader extends AdiInviter_Pro_Core
{
	public $pos = 0;
	public $csv;
	public $lines = array();
	public $n;
	public $delim = ',';
	public $enable_curl_session = false;
	function set_contents($csv, $delim = ',') 
	{
		$this->csv = $csv;
		$this->n = strlen($csv);
		$this->delim = $delim;
		$this->pos = 0;
	}
	function nextRow($case = false) 
	{
		$cells = array();
		$addCount = 0;
		$n = strlen($this->csv);
		$i = $this->pos;
		while (true) 
		{
			$sb = '';
			$inQuote = false;
			$eol = false;
			$quoteAllowed = true;
			$lastChar = '';
			$hasData = false;
			while (true) 
			{
				if ($i>=$n) {$eol = true;break;}
				$c = $this->csv[$i++];
				$z = ord($c);
				// if($z != 0 && $z != 254 && $z != 255)
				{
					$hasData = true;
					if ($lastChar === '"' && $c !== '"' && $inQuote)
					{
						$inQuote = false;
					}
					if ($c === $this->delim)
					{
						if ($inQuote)
						{
							if ($lastChar === '"') break;
							else $sb.=$c;
						} else {
							$lastChar = $c;
							break;
						}
					}
					else if ($c === '"')
					{
						if ($inQuote) {
							if ($lastChar === '"') {
								$sb.=$c;
								$c = '';
							}
						} else {
							if ($quoteAllowed) {
								$inQuote = true;
								$c = '';
							} else {
								$sb.=$c;
							}
						}
					}
					else if ($c === "\r")
					{
						if ($inQuote) $sb.=$c;
					}
					else if ($c === "\n")
					{
						if ($inQuote) {
							$sb.=$c;
						} else {
							$eol = true;
							break;
						}
					}
					else
					{
						$sb.=$c;
						$quoteAllowed = false;
					}
					$lastChar = $c;
				}
			}
			$this->pos = $i;
			if(!$hasData) return null;
			$cells[] = ($case) ? strtolower(trim($sb,' "')) : trim($sb,' "');
			if ($eol) return $cells;
		}
	}
}

class Adi_Contact_File_Base extends Adi_CsvReader
{
	// Must be full name of the field
	// Must be in small-letters (Non-Capital letters)
	public $name_attributes = array(
		array('name'),
		array('first name', 'middle name', 'last name'),
		array('first', 'middle', 'last'),
		array('firstname', 'middlename', 'lastname'),

		// French
		array('nom complet'),
		array('prénom', 'nom de famille'),

		// Polish
		array('nazwa', 'nazwisko'),

		// German
		array('vorname', 'nachname'),

		// Spanish
		array('nombre', 'segundo nome', 'apellido'),
		array('apodo'),

		// Italian
		array('nome', 'secondo nome', 'cognome'),
		array('Soprannome'),

		// common
		array('maidenname'),
		array('nickname'),
		array('short name'),
		array('maiden name'),
		array('alias'),
		array('contact name'),
		array('given name', 'additional name', 'family name'), 
	);

	// Must be in small-letters (Non-Capital letters)
	public $email_attributes = array(
		'email', 'e-mail', 'adresse', 'address', 'location', 'notes',
		'correo', 'posta', 'elettronica'//, 'Dirección de correo electrónico'
	);

	// Must be partial text to find in the field name
	// Must be in small-letters (Non-Capital letters)
	public $supported_formats = array('csv', 'vcf', 'txt', 'ldif');

	public $media_key     = '';
	public $columns       = array();
	public $name_indices  = array();
	public $email_indices = array();

	public $valid_cf_file = false;

	// Error Handling
	public $internal_error = '';

	final function get_contacts_from_file($contents = '', $format = 'csv', $delimiter = ',')
	{
		if($result = $this->set_contact_file_contents($contents, $format, $delimiter))
		{
			if($this->valid_cf_file)
			{
				$this->reset_channel();
			}
		}
		return $this->contacts;
	}
	function set_contact_file_contents($contents = '', $format = 'csv', $delimiter = ',')
	{
		if(empty($contents) || !is_string($contents))
		{
			$this->internal_error = 'Contact file is empty.';
			return ($this->valid_cf_file = false);
		}
		$format = strtolower($format);
		if(!in_array($format, $this->supported_formats))
		{
			$this->internal_error = 'Invalid file format.';
			return ($this->valid_cf_file = false);
		}

		$contents = $this->parse_encoding($contents);
		$this->media_key = $format.'_contacts';
		$this->set_contents($contents, $delimiter);
		$this->contacts = array();

		if(!empty($contents))
		{
			$this->valid_cf_file = true;
			$extracter = 'get_contacts_from_'.$format;
			if(method_exists($this,$extracter))
			{
				$result = $this->$extracter($contents, $delimiter);
			}
			return true;
		}
		return false;
	}

	function parse_encoding($contents = '')
	{
		$line_separator = "\r\n";
		$current_encoding = $base_encoding = '';
		$msb_offset = 1;
		$lsb_offset = 0;
		if(strpos($contents, "\xef\xbb\xbf") !== false) // UTF-8
		{
			$contents = substr($contents, 3);
			$line_separator = "\x0d\x0a";
			if(strpos($contents, "\x0d\x0a") !== false) {
				$line_separator = "\x0d\x0a";
			}
			else if(strpos($contents, "\x0a") !== false) {
				$line_separator = "\x0a";
			}
			else if(strpos($contents, "\x0d") !== false) {
				$line_separator = "\x0d";
			}
			$current_encoding = 'utf8';
		}
		else if(strpos($contents, "\xff\xfe") !== false) // UTF-16 LE
		{
			$contents = substr($contents, 2);
			$line_separator = "\x0d\x00\x0a\x00";
			if(strpos($contents, "\x0d\x00\x0a\x00") !== false) {
				$line_separator = "\x0d\x00\x0a\x00";
			}
			else if(strpos($contents, "\x0a\x00") !== false) {
				$line_separator = "\x0a\x00";
			}
			else if(strpos($contents, "\x0d\x00") !== false) {
				$line_separator = "\x0d\x00";
			}
			$current_encoding = 'utf16';
			$base_encoding = 'UTF-16LE';
		}
		else if(strpos($contents, "\xfe\xff") !== false) // UTF-16 BE
		{
			$contents = substr($contents, 2);
			$line_separator = "\x00\x0d\x00\x0a";
			if(strpos($contents, "\x00\x0d\x00\x0a") !== false) {
				$line_separator = "\x00\x0d\x00\x0a";
			}
			else if(strpos($contents, "\x00\x0a") !== false) {
				$line_separator = "\x00\x0a";
			}
			else if(strpos($contents, "\x00\x0d") !== false) {
				$line_separator = "\x00\x0d";
			}
			$current_encoding = 'utf16';
			$base_encoding = 'UTF-16BE';
			$msb_offset = 0;
			$lsb_offset = 1;
		}
		else
		{
			if(strpos($contents, "\r\n") !== false) {
				$line_separator = "\r\n";
			}
			else if(strpos($contents, "\n") !== false) {
				$line_separator = "\n";
			}
			else if(strpos($contents, "\r") !== false) {
				$line_separator = "\r";
			}
		}

		if($current_encoding == 'utf16')
		{
			$lines = explode($line_separator, $contents);
			$ln_cnt = 0;
			foreach ($lines as $ind => $ln) 
			{
				$nln = '';
				$len = strlen($ln);
				for($i=0; $i < $len; $i+=2)
				{
					if(ord($ln[$i+$msb_offset]) > 0)
					{
						$nln .= '&#x'.bin2hex($ln[$i+$msb_offset]).bin2hex($ln[$i+$lsb_offset]).';';
					}
					else
					{
						if(ord($ln[$i+$lsb_offset]) > 127)
						{
							$nln .= '&#x00'.bin2hex($ln[$i+$lsb_offset]).';';
						}
						else
						{
							$nln .= $ln[$i+$lsb_offset];
						}
					}
				}
				$lines[$ind] = $nln;
			}
			$contents = implode("\r\n", $lines);
		}
		else 
		{
			if($line_separator != "\r\n")
			{
				$contents = str_replace($line_separator, "\r\n", $contents);
			}

			// Check if the file has characters from Windows(Western-1252) Encoding.
			$contents = preg_replace_callback('/[\xC2-\xFF]([^\x80-\xBF]|$)/', 'adi_utf8_chr_replace1', $contents);
		}

		if(preg_match('/[^\r\n][\r\n]{2}[^\r\n]/', $contents, $matches) == 0)
		{
			$contents = preg_replace('/([\r\n]{2}){2}/i', '$1', $contents);
		}
		return $contents;
	}

	// Parsing functions for CSV format
	function get_contacts_from_csv($contents = '', $delimiter = ',')
	{
		$this->columns = $this->nextRow(true);
		$email_regex = '/[a-z0-9\.\-_]+@[a-z0-9\-_]+\.[a-z0-9\.]+/i';
		if(count($this->columns) <= 1)
		{
			$this->set_contents($contents, ';');
			$this->columns = $this->nextRow(true);
		}
		if(count($this->columns) > 1)
		{
			$this->get_indices($this->columns);
		}
		if(count($this->name_indices) == 0)
		{
			$this->internal_error = 'Name Field not found.';
		}
		if(count($this->email_indices) == 0)
		{
			$this->internal_error = 'Email field not found.';
		}
		return true;
	}
	function get_indices($cells)
	{
		if(count($cells) > 2)
		{
			$cells = array_map("strtolower", $cells);
			foreach($this->name_attributes as $group_id => $fields)
			{
				$result = array_intersect($cells, $fields);
				if(count($result) > 0)
				{
					foreach($result as $index => $field)
					{
						$this->name_indices[$group_id][$field] = $index;
					}
				}
			}
			foreach($cells as $index => $attr_name)
			{
				foreach($this->email_attributes as $email_attr_name)
				{
					if(strpos($attr_name, $email_attr_name) !== false)
					{
						$this->email_indices[$index] = $attr_name;
					}
				}
			}
		}
		else if(count($cells) == 2)
		{
			$this->name_indices  = array( 1 => array('default' => 0));
			$this->email_indices = array( 1 => array(1));
		}
		else if(count($cells) == 1)
		{
			$this->name_indices  = array( 1 => array('default' => 3));
			$this->email_indices = array( 1 => array(1));
		}
		else
		{
			$this->internal_error = '0 attributes found.';
			return false;
		}
	}
}

class Adi_ContactsReader extends AdiInviter_Pro_Core
{
	public $media_key = 'manual_inviter';
	public $enable_curl_session = false;
	public $contacts = array();

	function init_parser($contacts_list = '')
	{
		$this->contacts_list = $contacts_list;
		$this->init();
	}
}
?>