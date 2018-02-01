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

/*

https://ssl.captcha.qq.com/getimage?uin=adiinviter@qq.com&aid=522005705&cap_cd=0&0.7846555726137012

https://xui.ptlogin2.qq.com/cgi-bin/xlogin?appid=522005705&daid=4&s_url=https://mail.qq.com/cgi-bin/login?vt=passport%26vm=wpt%26ft=loginpage%26target=&style=25&low_login=1&proxy_url=https://mail.qq.com/proxy.html&need_qr=0&hide_border=1&border_radius=0&self_regurl=http://zc.qq.com/chs/index.html?type=1&app_id=11005?t=regist&pt_feedback_link=http://support.qq.com/discuss/350_1.shtml&css=https://res.mail.qq.com/zh_CN/htmledition/style/ptlogin_input24e6b9.css

*/

class Adi_Service_qq_com extends AdiInviter_Pro_Core
{
	public $version      = 1001;
	public $service_name = 'qq';
	public $media_key    = 'qq_com';
	public $use_ssl      = true;
	public $use_pm       = false;
	public $email_or_id  = 1;

	function get_captcha($captcha_id)
	{
		$url = 'https://ssl.captcha.qq.com/getimage?uin=adiinviter@qq.com&aid=522005705&cap_cd=0&'.(rand(0,100)/103);
		$res = $this->get($url, true);
		// var_dump($res);
		// echo '<img alt="Embedded Image" src="data:image/jpeg;base64,'.base64_encode($res).'">';

		echo $res;
		header('Content-type: text/html');

		var_dump($res);
		var_dump($this->last_info);

		/*
		header('Content-type: image/jpeg');
		*/
		// echo $res;
	}

	function fetchContacts()
	{
		$url = 'https://mail.qq.com/cgi-bin/loginpage';
		$this->get($url, false);

		$url = adi_get_text_around($this->res, 'cgi-bin/xlogin', '"', '"', true);
		$this->get($url, false);

		// https://ssl.ptlogin2.qq.com/check?regmaster=&pt_tea=1&pt_vcode=0&uin=adiinviter@qq.com&appid=522005705&js_ver=10136&js_type=1&login_sig=aNL0GPMluhplVze7*1dA3RlzYW7oNI2IB9v4uC8Tb7KD2ar2VnCDNuTRaPg9mHiV&u1=https%3A%2F%2Fmail.qq.com%2Fcgi-bin%2Flogin%3Fvt%3Dpassport%26vm%3Dwpt%26ft%3Dloginpage%26target%3D&r=0.1321127121336758

		/*
		regmaster=&
		pt_tea=1&
		pt_vcode=0&
		uin=adiinviter@qq.com&
		appid=522005705&
		js_ver=10136&
		js_type=1&
		login_sig=aNL0GPMluhplVze7*1dA3RlzYW7oNI2IB9v4uC8Tb7KD2ar2VnCDNuTRaPg9mHiV&
		u1=https%3A%2F%2Fmail.qq.com%2Fcgi-bin%2Flogin%3Fvt%3Dpassport%26vm%3Dwpt%26ft%3Dloginpage%26target%3D&
		r=0.1321127121336758
		*/

		// https://ssl.ptlogin2.qq.com/check?regmaster=&pt_tea=1&pt_vcode=0&uin=adiinviter@qq.com&appid=522005705&js_ver=10136&js_type=1&login_sig=aNL0GPMluhplVze7*1dA3RlzYW7oNI2IB9v4uC8Tb7KD2ar2VnCDNuTRaPg9mHiV&u1=https%3A%2F%2Fmail.qq.com%2Fcgi-bin%2Flogin%3Fvt%3Dpassport%26vm%3Dwpt%26ft%3Dloginpage%26target%3D&r=0.6240622927434742
	}

	function endSession()
	{
		
	}
}

?>