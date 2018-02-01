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


$v = AdiInviterPro::GET('v', ADI_INT_VARS);
header("Cache-Control: public");
header("Content-Description: File Transfer");

$fileName = array(
	"",
	"Sample TXT Comma Separated.txt",
	"Sample TXT Tab Delimited.txt",
	"Sample vCard.vcf",
	"Sample LDIF.ldif",
	"Sample Generic CSV.csv",
	"Sample CSV.csv",
	"Contacts.csv",
);
header('Content-Disposition: attachment; filename="'.$fileName[$v].'"');
header("Content-Type: application/csv");
header("Content-Transfer-Encoding: binary");

switch($v)
{
	case 1 :
		echo 'sales@adiinviter.com,support@adiinviter.com,name@domain.com';
		break;
	case 2 :
		echo 'Contact Name	Email address
AdiInviter Support	support@adiinviter.com
AdiInviter Sales	sales@adiinviter.com
Your Name	name@domain.com
';
		break;
	case 3 :
		echo 'BEGIN:VCARD
VERSION:3.0
FN:AdiInviter Support
N:Support;AdiInviter;;;
EMAIL;TYPE=INTERNET;TYPE=HOME:support@adiinviter.com
END:VCARD

BEGIN:VCARD
VERSION:3.0
FN:AdiInviter Sales
N:Sales;AdiInviter;;;
EMAIL;TYPE=INTERNET;TYPE=HOME:sales@adiinviter.com
END:VCARD

BEGIN:VCARD
VERSION:3.0
FN:Your Name
N:Name;Your;;;
EMAIL;TYPE=INTERNET;TYPE=HOME:name@domain.com
END:VCARD';
		break;
	case 4 :
		echo 'dn: cn=AdiInviter Support,mail=support@adiinviter.com
objectclass: top
objectclass: person
objectclass: organizationalPerson
objectclass: inetOrgPerson
objectclass: mozillaAbPersonAlpha
givenName: AdiInviter
sn: Support
cn: AdiInviter Support
mail: support@adiinviter.com
modifytimestamp: 0Z

dn: cn=AdiInviter Sales,mail=sales@adiinviter.com
objectclass: top
objectclass: person
objectclass: organizationalPerson
objectclass: inetOrgPerson
objectclass: mozillaAbPersonAlpha
givenName: AdiInviter
sn: Sales
cn: AdiInviter Sales
mail: sales@adiinviter.com
modifytimestamp: 0Z

dn: cn=Your Name,mail=name@domain.com
objectclass: top
objectclass: person
objectclass: organizationalPerson
objectclass: inetOrgPerson
objectclass: mozillaAbPersonAlpha
givenName: Your
sn: Name
cn: Your Name
mail: name@domain.com
modifytimestamp: 0Z';
		break;
	case 5 :
		echo 'person Name,person Email
AdiInviter Support, support@adiinviter.com
AdiInviter Sales, sales@adiinviter.com
Your Name, name@domain.com';
		break;
	case 6 :
		echo 'alias,email_address,first_name,last_name,middle_name,home_address,home_city,home_state,home_zip,home_country,work_company,work_title,work_address,work_city,work_state,work_zip,work_country,home_phone,work_phone,pager,cell_phone,fax,other,alt_email_address,icq_uin,home_website,work_website,birthday,anniversary,comment,facebook_id,twitter_id,twitter_name,facebook_name
AdiInviter Support,support@adiinviter.com,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
AdiInviter Sales,sales@adiinviter.com,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
Your Name,name@domain.com,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,';
		break;
}

?>