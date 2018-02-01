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


$adiinviter_services = array(
	'gmail' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Gmail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Gmail', 1, ),
		'domains' => array(
			'gmail.com',
			'googlemail.com',
		),
	),
	'yahoo' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Yahoo',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Yahoo', 1, ),
		'domains' => array(
			'yahoo.com', 'ymail.com', 'rocketmail.com', 'yahoo.at', 'yahoo.be', 'yahoo.ca', 'yahoo.cl', 'yahoo.cn', 'yahoo.co.id', 'yahoo.co.in', 'yahoo.co.kr', 'yahoo.co.nz', 'yahoo.co.ru', 'yahoo.co.th', 'yahoo.co.tw', 'yahoo.co.uk', 'yahoo.com.ar', 'yahoo.com.au', 'yahoo.com.br', 'yahoo.com.cn', 'yahoo.com.co', 'yahoo.com.es', 'yahoo.com.hk', 'yahoo.com.kr', 'yahoo.com.mx', 'yahoo.com.my', 'yahoo.com.no', 'yahoo.com.pe', 'yahoo.com.ph', 'yahoo.com.ru', 'yahoo.com.se', 'yahoo.com.sg', 'yahoo.com.tr', 'yahoo.com.tw', 'yahoo.com.ve', 'yahoo.com.vn', 'yahoo.de', 'yahoo.dk', 'yahoo.es', 'yahoo.fr', 'yahoo.gr', 'yahoo.ie', 'yahoo.in', 'yahoo.it', 'yahoo.kr', 'yahoo.no', 'yahoo.pl', 'yahoo.ro', 'yahoo.ru', 'yahoo.se', 'yahoo.tw', 'yahoo.co',
		),
	),
	'hotmail' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Hotmail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Hotmail', 1, ),
		'domains' => array(
			'hotmail.com', 'hotmail.co', 'live.co', 'live.at', 'live.ba', 'live.be', 'live.biu.ac.il', 'live.ca', 'live.cl', 'live.cn', 'live.co.kr', 'live.co.uk', 'live.co.za', 'live.com', 'live.com.ar', 'live.com.au', 'live.com.co', 'live.com.mx', 'live.com.my', 'live.com.pe', 'live.com.ph', 'live.com.pk', 'live.com.pt', 'live.com.sg', 'live.com.ve', 'live.de', 'live.dk', 'live.fi', 'live.fr', 'live.hk', 'live.ie', 'live.in', 'live.it', 'live.jp', 'live.lagcc.cuny.edu', 'live.mcl.edu.ph', 'live.nl', 'live.no', 'live.ru', 'live.se', 'live.uem.es', 'live.vu.edu.au',
		),
	),
	'twitter' => array(
		'info' => array(
			'avatar' => 1, 'email' => 0, 'id' => 1,
			'service' => 'Twitter',
			'service_type' => 'Social',
			'invitation' => 'pm',
		),
		'params' => array(1, 'Twitter', 1, ),
		'domains' => array(
			'*',
		),
	),
	'facebook' => array(
		'info' => array(
			'avatar' => 1, 'email' => 1, 'id' => 0,
			'service' => 'Facebook',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Facebook', 1, ),
		'domains' => array(
			'*',
		),
	),
	'aol' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'AOL',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'AOL', 1, ),
		'domains' => array(
			'aol.com',
			'love.com',
			'love2exercise.com',
			'love2workout.com',
			'lovefantasysports.com',
			'lovetoexercise.com',
			'luvfishing.com',
			'luvgolfing.com',
			'luvsoccer.com',
		),
	),
	'linkedin' => array(
		'info' => array(
			'avatar' => 1, 'email' => 0, 'id' => 1,
			'service' => 'Linkedin',
			'service_type' => 'Social',
			'invitation' => 'email',
		),
		'params' => array(1, 'Linkedin', 2, ),
		'domains' => array(
			'*',
		),
	),
	'icloud' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'iCloud',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'iCloud', 0, ),
		'domains' => array(
			'me.com',
		),
	),
	'mailchimp' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'MailChimp',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'MailChimp', 1, ),
		'domains' => array(
			'*',
		),
	),
	'mail_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Mail.com',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Mail.com', 0, ),
		'domains' => array(
			'mail.com', 'email.com', 'usa.com', 'myself.com', 'consultant.com', 'post.com', 'europe.com', 'london.com', 'asia.com', 'iname.com', 'writeme.com', 'dr.com', 'engineer.com', 'cheerful.com', 'accountant.com', 'techie.com', 'linuxmail.org', 'lawyer.com', 'uymail.com', 'contractor.net', 'accountant.com', 'activist.com', 'adexec.com', 'allergist.com', 'alumni.com', 'alumnidirector.com', 'angelic.com', 'appraiser.net', 'archaeologist.com', 'arcticmail.com', 'artlover.com', 'asia.com', 'auctioneer.net', 'bartender.net', 'bikerider.com', 'birdlover.com', 'brew-meister.com', 'cash4u.com', 'chef.net', 'chemist.com', 'clerk.com', 'clubmember.org', 'collector.org', 'columnist.com', 'comic.com', 'computer4u.com', 'consultant.com', 'contractor.net', 'coolsite.net', 'counsellor.com', 'cyberservices.com', 'deliveryman.com', 'diplomats.com', 'disposable.com', 'doctor.com', 'dr.com', 'engineer.com', 'execs.com', 'fastservice.com', 'financier.com', 'fireman.net', 'gardener.com', 'geologist.com', 'graduate.org', 'graphic-designer.com', 'groupmail.com', 'hairdresser.net', 'homemail.com', 'hot-shot.com', 'instruction.com', 'instructor.net', 'insurer.com', 'job4u.com', 'journalist.com', 'lawyer.com', 'legislator.com', 'lobbyist.com', 'minister.com', 'musician.org', 'myself.com', 'net-shopping.com', 'optician.com', 'orthodontist.net', 'pediatrician.com', 'photographer.net', 'physicist.net', 'planetmail.com', 'planetmail.net', 'politician.com', 'post.com', 'presidency.com', 'priest.com', 'programmer.net', 'publicist.com', 'qualityservice.com', 'radiologist.net', 'realtyagent.com', 'registerednurses.com', 'repairman.com', 'representative.com', 'rescueteam.com', 'revenue.com', 'salesperson.net', 'scientist.com', 'secretary.net', 'socialworker.net', 'sociologist.com', 'solution4u.com', 'songwriter.net', 'surgical.net', 'teachers.org', 'tech-center.com', 'techie.com', 'technologist.com', 'theplate.com', 'therapist.net', 'toothfairy.com', 'tvstar.com', 'umpire.com', 'webname.com', 'worker.com', 'workmail.com', 'writeme.com', 'activist.com', 'aircraftmail.com', 'artlover.com', 'atheist.com', 'bikerider.com', 'birdlover.com', 'blader.com', 'boardermail.com', 'brew-master.com', 'brew-meister.com', 'bsdmail.com', 'catlover.com', 'chef.net', 'clubmember.org', 'collector.org', 
			'cutey.com', 'dbzmail.com', 'doglover.com', 'gardener.com', 'greenmail.net', 'hackermail.com', 'hilarious.com', 'keromail.com', 'kittymail.com', 'linuxmail.org', 'lovecat.com', 'marchmail.com', 'musician.org', 'nonpartisan.com', 'petlover.com', 'photographer.net', 'snakebite.com', 'songwriter.net', 'techie.com', 'theplate.com', 'toke.com', 'uymail.com', 'computer4u.com', 'consultant.com', 'contractor.net', 'coolsite.net', 'cyberdude.com', 'cybergal.com', 'cyberservices.com', 'cyber-wizard.com', 'engineer.com', 'fastservice.com', 'graphic-designer.com', 'groupmail.com', 'homemail.com', 'hot-shot.com', 'housemail.com', 'humanoid.net', 'iname.com', 'inorbit.com', 'mail-me.com', 'myself.com', 'net-shopping.com', 'null.net', 'physicist.net', 'planetmail.com', 'planetmail.net', 'post.com', 'programmer.net', 'qualityservice.com', 'rocketship.com', 'scientist.com', 'solution4u.com', 'tech-center.com', 'techie.com', 'technologist.com', 'webname.com', 'workmail.com', 'writeme.com', 'acdcfan.com', 'angelic.com', 'artlover.com', 'atheist.com', 'chemist.com', 'diplomats.com', 'discofan.com', 'elvisfan.com', 'execs.com', 'hiphopfan.com', 'housemail.com', 'kissfans.com', 'madonnafan.com', 'metalfan.com', 'minister.com', 'musician.org', 'ninfan.com', 'oath.com', 'ravemail.com', 'reborn.com', 'reggaefan.com', 'snakebite.com', 'songwriter.net', 'bellair.net', 'californiamail.com', 'dallasmail.com', 
			'nycmail.com', 'pacific-ocean.com', 'pacificwest.com', 'sanfranmail.com', 'usa.com', 'africamail.com', 'arcticmail.com', 'asia.com', 'asia-mail.com', 'australiamail.com', 'berlin.com', 'brazilmail.com', 'chinamail.com', 'dublin.com', 'dutchmail.com', 'englandmail.com', 'europe.com', 'europemail.com', 'germanymail.com', 'irelandmail.com', 'israelmail.com', 'italymail.com', 'japan.com', 'koreamail.com', 'london.com', 'madrid.com', 'mexicomail.com', 'moscowmail.com', 'munich.com', 'polandmail.com', 'rome.com', 'safrica.com', 'samerica.com', 'scotlandmail.com', 'singapore.com', 'spainmail.com', 'swedenmail.com', 'swissmail.com', 'tokyo.com', 'torontomail.com', 'angelic.com', 'atheist.com', 'disciples.com', 'innocent.com', 'minister.com', 'muslim.com', 'oath.com', 'priest.com', 'protestant.com', 'reborn.com', 'reincarnate.com', 'religious.com', 'saintly.com',
		),
	),
	'eventbrite' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Eventbrite',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Eventbrite', 1, ),
		'domains' => array(
			'*',
		),
	),
	'plaxo' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Plaxo',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Plaxo', 0, ),
		'domains' => array(
			'*',
		),
	),
	'zoho_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Zoho.com',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Zoho.com', 0, ),
		'domains' => array(
			'zoho.com',
		),
	),
	'lycos' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Lycos',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Lycos', 0, ),
		'domains' => array(
			'lycos.com', 'lycos.at', 'lycos.be', 'lycos.ch', 'lycos.co.kr', 'lycos.co.uk', 'lycos.de', 'lycos.es', 'lycos.fr', 'lycos.it', 'lycos.nl',
		),
	),
	'viadeo' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Viadeo',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Viadeo', 0, ),
		'domains' => array(
			'*',
		),
	),
	'laposte' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Laposte',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Laposte', 0, ),
		'domains' => array(
			'laposte.net',
		),
	),
	'terra' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Terra',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Terra', 0, ),
		'domains' => array(
			'terra.com',
			'terra.com.br',
			'terra.es',
		),
	),
	'bol_com_br' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Bol.com.br',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Bol.com.br', 0, ),
		'domains' => array(
			'bol.com.br',
		),
	),
	'sapo' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Sapo',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Sapo', 0, ),
		'domains' => array(
			'sapo.pt',
		),
	),
	'iol_pt' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'iOL.pt',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'iOL.pt', 0, ),
		'domains' => array(
			'iol.pt',
		),
	),
	'clix_pt' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Clix.pt',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Clix.pt', 0, ),
		'domains' => array(
			'clix.pt',
			'optimus.clix.pt',
			'oninet.pt',
			'oniduo.pt',
			'oninetspeed.pt',
		),
	),
	'atlas' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Atlas',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Atlas', 0, ),
		'domains' => array(
			'atlas.cz',
			'mujmail.cz',
		),
	),
	'gmx_net' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Gmx.net',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Gmx', 0, ),
		'domains' => array(
			'gmx.de',
			'gmx.net',
			'loveyouforever.de',
			'maennerversteherin.com',
			'gmx.com',
			'gmx.us',
		),
	),
	'freenet_de' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Freenet',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Freenet', 0, ),
		'domains' => array(
			'freenet.de',
		),
	),
	'web_de' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Web.de',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Web.de', 0, ),
		'domains' => array(
			'web.de',
		),
	),
	'tonline' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 't-online.de',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'T-online', 0, ),
		'domains' => array(
			't-online.de',
		),
	),
	'xing' => array(
		'info' => array(
			'avatar' => 1, 'email' => 1, 'id' => 0,
			'service' => 'Xing',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Xing', 1, ),
		'domains' => array(
			'*',
		),
	),
	'wpl' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'WPL',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Wpl', 0, ),
		'domains' => array(
			'wp.pl',
		),
	),
	'onet_pl' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Onet',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Onet', 0, ),
		'domains' => array(
			'onet.pl', 'op.pl', 'poczta.onet.pl', 'onet.eu', 'vp.pl', 'autograf.pl', 'vip.onet.pl', 'spoko.pl', 'opoczta.pl', 'onet.com.pl',
		),
	),
	'interia' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Interia',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Interia', 0, ),
		'domains' => array(
			'interia.pl', 'poczta.fm', 'interia.eu', '1gb.pl', '2gb.pl', 'vip.interia.pl', 'serwus.pl', 'akcja.pl', 'czateria.pl', 'znajomi.pl',
		),
	),
	'o2' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'O2',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'O2', 0, ),
		'domains' => array(
			'o2.pl',
			'tlen.pl',
			'go2.pl',
			'prokonto.pl',
		),
	),
	'virgilio' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Virgilio',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Virgilio', 0, ),
		'domains' => array(
			'virgilio.it',
		),
	),
	'libero' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Libero',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Libero', 0, ),
		'domains' => array(
			'libero.it', 'inwind.it', 'iol.it', 'blu.it',
		),
	),
	'email_it' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Email.it',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Email.it', 0, ),
		'domains' => array(
			'email.it',
		),
	),
	'excite_it' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Excite.it',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Excite.it', 0, ),
		'domains' => array(
			'excite.it',
		),
	),
	'mynet' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Mynet',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(3, 'Mynet', 0, ),
		'domains' => array(
			'mynet.com',
		),
	),
	'freemail' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Freemail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Freemail', 0, ),
		'domains' => array(
			'freemail.hu',
		),
	),
	'citromail_hu' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'CitroMail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'CitroMail', 0, ),
		'domains' => array(
			'citromail.hu',
		),
	),
	'india' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'India',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'India', 0, ),
		'domains' => array(
			'zmail.com', 'timepass.com', 'imail.com', 'india.com', 'tadka.com', 'indiawrites.com', 'dvaar.com', 'takdhinadhin.com',
		),
	),
	'mail_in' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Mail.in',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'In.com', 0, ),
		'domains' => array(
			'in.com',
		),
	),
	'rediff' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Rediff',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Rediff', 0, ),
		'domains' => array(
			'rediffmail.com', 'rediff.com',
		),
	),
	'qip' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Qip.ru',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Qip.ru', 0, ),
		'domains' => array(
			'qip.ru', 'fromru.com', 'front.ru', 'hotbox.ru', 'hotmail.ru', 'krovatka.su', 'land.ru', 'mail15.com', 'mail333.com', 'newmail.ru', 'nightmail.ru', 'nm.ru', 'pisem.net', 'pochtamt.ru', 'pop3.ru', 'rbcmail.ru', 'smtp.ru', '5ballov.ru', 'aeterna.ru', 'ziza.ru', 'memori.ru', 'photofile.ru', 'fotoplenka.ru', 'pochta.com',
		),
	),
	'mail_ru' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Mail.ru',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Mail.ru', 0, ),
		'domains' => array(
			'mail.ru',
			'inbox.ru',
			'bk.ru',
			'list.ru',
		),
	),
	'rambler' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Rambler',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Rambler', 0, ),
		'domains' => array(
			'rambler.ru',
			'lenta.ru',
			'myrambler.ru',
			'autorambler.ru',
			'ro.ru',
		),
	),
	'yandex' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Yandex',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Yandex', 0, ),
		'domains' => array(
			'yandex.ru',
		),
	),
	'pochta' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Pochta',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Pochta', 0, 1,
		),
		'domains' => array(
			'pochta.ru',
		),
	),
	'meta' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Meta',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Meta', 0, ),
		'domains' => array(
			'*',
		),
	),
	'abv' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'ABV',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'ABV', 0, ),
		'domains' => array(
			'abv.bg',
			'gyuvetch.bg',
			'gbg.bg',
		),
	),
	'azet' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Azet',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Azet', 0, ),
		'domains' => array(
			'azet.sk',
		),
	),
	'qq_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'qq',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'QQ', 2, ),
		'domains' => array(
			'qq.com',
			'vip.qq.com',
			'foxmail.com',
		),
	),
	'naver_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Naver',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Naver', 0, ),
		'domains' => array(
			'naver.com',
		),
	),
	'yeah' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Yeah',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Yeah.net', 0, ),
		'domains' => array(
			'yeah.net',
			'188.com',
		),
	),
	'ost_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => '163.com',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, '163.com', 0, ),
		'domains' => array(
			'163.com',
			'vip.163.com',
		),
	),
	'ots_com' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => '126.com',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, '126.com', 0, ),
		'domains' => array(
			'126.com',
			'vip.126.com',
		),
	),
	'daum_net' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Daum',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Daum', 0, ),
		'domains' => array(
			'daum.net',
		),
	),
	'sohu' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Sohu.com',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Sohu.com', 0, ),
		'domains' => array(
			'sohu.com',
		),
	),
	'evite' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Evite',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Evite', 0, ),
		'domains' => array(
			'*',
		),
	),
	'operamail' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Operamail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Operamail', 0, ),
		'domains' => array(
			'operamail.com',
		),
	),
	'fastmail' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Fastmail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Fastmail', 0, ),
		'domains' => array(
			'fastmail.fm', 'fastmail.cn', 'fastmail.co.uk', 'fastmail.com.au', 'fastmail.es', 'fastmail.in', 'fastmail.jp', 'fastmail.net', 'fastmail.to', 'fastmail.us', '123mail.org', 'airpost.net', 'eml.cc', 'fmail.co.uk', 'fmgirl.com', 'fmguy.com', 'mailbolt.com', 'mailcan.com', 'mailhaven.com', 'mailmight.com', 'ml1.net', 'mm.st', 'myfastmail.com', 'proinbox.com', 'promessage.com', 'rushpost.com', 'sent.as', 'sent.at', 'sent.com', 'speedymail.org', 'warpmail.net', 'xsmail.com', '150mail.com', '150ml.com', '16mail.com', '2-mail.com', '4email.net', '50mail.com', 'allmail.net', 'bestmail.us', 'cluemail.com', 'elitemail.org', 'emailcorner.net', 'emailengine.net', 'emailengine.org', 'emailgroups.net', 'emailplus.org', 'emailuser.net', 'f-m.fm', 'fast-email.com', 'fast-mail.org', 'fastem.com', 'fastemail.us', 'fastemailer.com', 'fastest.cc', 'fastimap.com', 'fastmailbox.net', 'fastmessaging.com', 'fea.st', 'fmailbox.com', 'ftml.net', 'h-mail.us', 'hailmail.net', 'imap-mail.com', 'imap.cc', 'imapmail.org', 'inoutbox.com', 'internet-e-mail.com', 'internet-mail.org', 'internetemails.net', 'internetmailing.net', 'jetemail.net', 'justemail.net', 'letterboxes.org', 'mail-central.com', 'mail-page.com', 'mailandftp.com', 'mailas.com', 'mailc.net', 'mailforce.net', 'mailftp.com', 'mailingaddress.org', 'mailite.com', 'mailnew.com', 'mailsent.net', 'mailservice.ms', 'mailup.net', 'mailworks.org', 'mymacmail.com', 'nospammail.net', 'ownmail.net', 'petml.com', 'postinbox.com', 'postpro.net', 'realemail.net', 'reallyfast.biz', 'reallyfast.info', 'speedpost.net', 'ssl-mail.com', 'swift-mail.com', 'the-fastest.net', 'the-quickest.com', 'theinternetemail.com', 'veryfast.biz', 'veryspeedy.net', 'yepmail.net', 'your-mail.com',
		),
	),
	'orange' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Orange',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Orange', 0, ),
		'domains' => array(
			'orange.com',
		),
	),
	'sify' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Sify Mail',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Sify Mail', 0, ),
		'domains' => array(
			'sify.com',
		),
	),
	'ok_ru' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Ok.ru',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Ok.ru', 0, ),
		'domains' => array(
			'ok.ru',
		),
	),
	'data_bg' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Data.bg',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Data.bg', 0, ),
		'domains' => array(
			'data.bg',
		),
	),

	'manual_inviter' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Manually',
			'service_type' => 'Webmail',
			'invitation' => 'email',
		),
		'params' => array(1, 'Manually', 0, ),
		'domains' => array(
			'*',
		),
	),

	'csv_inviter' => array(
		'info' => array(
			'avatar' => 0, 'email' => 1, 'id' => 0,
			'service' => 'Contact File',
			'service_type' => 'CSV',
			'invitation' => 'email',
		),
		'params' => array(1, 'Contact File', 0, ),
		'domains' => array(
			'*',
		),
	),
);


$adi_captcha_services = array(
	'qq_com',
);

?>