

adi=(function(j,a,pp,nt,acp){

	// Default Interfaces
	a.ntrs=j.extend({}, a.ntrs, {
		// Login Page - Popup
		'lgpp':function(){
			var m=this;
			// Open OAuth Service Login Popup
			j('.adi_nc_external_login').each(function(){
				if(!j(this).hasClass('adi_nc_marked'))
				{
					j(this).click(function(e){
						var sd = j(this).attr('service_id');
						if(typeof sd == 'string' && sd != '')
						{
							if(adi.services != undefined && adi.services[sd] != undefined && (adi.services[sd][0][2] == 1 || adi.services[sd][0][2] == 2))
							{
								adipps.sp.setKey(sd)
								adi_open_oauth(sd);
							}
						}
						e.preventDefault();
						return false;
					});
					j(this).addClass('adi_nc_marked');
				}
			});
			a.ntrs.lg(m);
			j('.adi_nc_addressbook_form', m.rt).submit(function(e){
				var sk = j('.adi_service_key_val', m.rt).val();
				var os = j('.adi_oauth_submit',m.rt).val() || 0;
				if(sk != '' && a.services[sk] && (a.services[sk][0][2] == 1 || a.services[sk][0][2] == 2) && os == 0)
				{
					adi_open_oauth(sk);
					e.preventDefault();
					return false;
				}
				var eb=j('.adi_nc_user_email_input', m.rt).val(), pb=j('.adi_nc_password_input', m.rt).val();
				if( (eb!=''&& pb!='' && sk!='') || os == 1 )
				{
					j('.adi_oauth_submit',m.rt).val(0);
					var frm = this;
					adi_sending_effect(frm,1);

					if(a.services[sk][0][3] == 1)
					{
						var frm = this;
						if(j('.adi_captcha_text_cls', m.rt).val() == '')
						{
							pp.irc.reset(frm, m.rt);
							return false;
						}
					}

					j.ajax({
						type: 'POST', data: j(frm).serialize(), 
						url: a.ajaxUrl('adi_do=get_contacts'),
						success: function(code)
						{
							adi_sending_effect(frm,0);
							adi.eval(code);
							j('.adi_captcha_text_cls', frm).val('');
						},
						error : function(d) {
							adi_sending_effect(frm,0);
						},
						dataType: 'text'
					});
				}
				return false;
			});
			j('.adi_nc_contact_file_form', m.rt).submit(function(){
				var errmsg='', err=false,n,frmt,finp=j('.adi_nc_contact_file',m.rt);
				if(finp.val() == '') {
					err=true;
				}
				else if(!finp.val().toLowerCase().match(/\.csv$|\.ldif$|\.vcf$|\.txt$/)) {
					errmsg = adi.phrases['adi_msg_invalid_contact_file_format'];
					err=true;
				}
				else if(finp.get(0).files[0].size > adi.cflt) {
					errmsg = adi.phrases['adi_msg_contact_file_size_limit_exceeded'];
					err = true;
				}
				if(err == true) {
					if(errmsg != ''){a.show_pp_err(errmsg);}
				}
				else {
					adi_sending_effect(this,1);
					return true;
				}
				return false;
			});
			j('.adi_nc_manual_form', m.rt).submit(function(){
				var errmsg='',err=false,cl = a.trim(j('.adi_nc_contacts_list', m.rt).val());
				if(cl == '' || a.trim(adi.phrases['manualinv_textarea_default_txt']) == cl) {
					err=true;
				}
				else if(cl.length > adi.cllt) {
					errmsg = adi.phrases['adi_error_contact_list_length_limit_exceeded'];
					err = true;
				}
				if(err == true) {
					if(errmsg != ''){a.show_pp_err(errmsg);}
				}
				else
				{
					var frm = this;
					adi_sending_effect(frm,1);
					j.ajax({
						type: 'POST', data: j(frm).serialize(), 
						url: a.ajaxUrl('adi_do=get_contacts'),
						success: function(code)
						{
							adi_sending_effect(frm,0);
							adi.eval(code);
						},
						error : function(d) {
							adi_sending_effect(frm,0);
						},
						dataType: 'text'
					});
				}
				return false;
			});
			adi.call_event('login_form_load', {sel: m.rt});
		},
		// Login Page - Inpage
		'lgip':function(){
			var m=this;
			a.ntrs.lg(m);
			j('.adi_nc_addressbook_form', m.rt).submit(function(e){
				var sk = j('.adi_service_key_val', m.rt).val();
				var os = j('.adi_oauth_submit',m.rt).val() || 0;
				if(sk != '' && a.services[sk] && (a.services[sk][0][2] == 1 || a.services[sk][0][2] == 2) && os == 0)
				{
					adi_open_oauth(sk);
					e.preventDefault();
					return false;
				}
				var eb=j('.adi_nc_user_email_input', m.rt).val(), pb=j('.adi_nc_password_input', m.rt).val();
				if( (eb!='' && pb!='' && sk!='') || os == 1)
				{
					j('.adi_oauth_submit',m.rt).val(0);
					adi_sending_effect(this,1);
					if(a.services[sk][0][3] == 1)
					{
						var frm = this;
						if(j('.adi_captcha_text_cls', m.rt).val() == '')
						{
							pp.irc.reset(frm, m.rt);
							return false;
						}
					}
					return true;
				}
				return false;
			});
			j('.adi_nc_contact_file_form', m.rt).submit(function(){
				var errmsg='', err=false,n,frmt,finp=j('.adi_nc_contact_file',m.rt);
				if(finp.val() == '') {
					err=true;
				}
				else if(!finp.val().toLowerCase().match(/\.csv$|\.ldif$|\.vcf$|\.txt$/)) {
					errmsg = adi.phrases['adi_msg_invalid_contact_file_format'];
					err=true;
				}
				else if(finp.get(0).files[0].size > adi.cflt) {
					errmsg = adi.phrases['adi_msg_contact_file_size_limit_exceeded'];
					err = true;
				}
				if(err == true) {
					if(errmsg != ''){a.show_ip_err(errmsg);}
					return false;
				}
				adi_sending_effect(this,1);
				return true;
			});
			j('.adi_nc_manual_form', m.rt).submit(function(){
				var errmsg='',err=false,cl = a.trim(j('.adi_nc_contacts_list', m.rt).val());
				if(cl == '' || a.trim(adi.phrases['manualinv_textarea_default_txt']) == cl) {
					err=true;
				}
				else if(cl.length > adi.cllt) {
					errmsg = adi.phrases['adi_error_contact_list_length_limit_exceeded'];
					err = true;
				}
				if(err == true) {
					if(errmsg != ''){a.show_ip_err(errmsg);}
					return false;
				}
				adi_sending_effect(this,1);
				return true;
			});
			adi.call_event('login_form_load', {sel: m.rt});
		},
		// Login Page - Common
		'lg': function(m){
			m.stxt='';
			m.type_search_time_fn=undefined;
			m.last_tp_search='';
			j('.adi_menuitem', m.rt).click(function(){
				j('.adi_inner_section', m.rt).hide(); j('.'+j(this).attr('data'), m.rt).show();
				j(this).siblings('.current').removeClass('current').addClass('adi_other');
				j(this).removeClass('adi_other').addClass('current');
			});
			j('.adi_nc_contacts_list', m.rt).focusin(function(){
				if(j(this).val()==a.phrases['manualinv_textarea_default_txt']) j(this).val('');
			}).focusout(function(){
				if(j(this).val()=='') j(this).val(a.phrases['manualinv_textarea_default_txt']);
			});
			pp.sp.init();
			j('.adi_nc_down_arrow',m.rt).click(function(){
				pp.sp.show(m.rt);
				j('.adi_nc_up_arrow', m.rt).show();
				j('.adi_nc_down_arrow', m.rt).hide();
				j('.adi_nc_service_input', m.rt).focus();
			});
			j('.adi_nc_up_arrow',m.rt).click(function(){
				pp.sp.hide();
				j('.adi_nc_up_arrow', m.rt).hide();
				j('.adi_nc_down_arrow', m.rt).show();
				j('.adi_nc_service_input', m.rt).blur();
			});
			j('.adi_nc_service_input', m.rt).focusin(function(){
				pp.sp.show(m.rt);
				if(j(this).val()==adi.phrases['adi_ab_service_field_default_txt'])
				{
					j(this).val('').removeClass('adi_nc_service_note');
					m.stxt='';
				}
				var sk = j('.adi_service_key_val', m.rt).val();
				j(this).removeClass(sk+'_si').val('');
				j('.adi_search_icon', m.rt).show();

				j('.adi_nc_up_arrow', m.rt).show();
				j('.adi_nc_down_arrow', m.rt).hide();
			}).focusout(function(){
				var sk = j('.adi_service_key_val', m.rt).val();
				if(j('.adi_nc_service_select_hoverd').size() > 0)
				{
					sk=j('.adi_nc_service_select_hoverd').parent().attr('data');
					pp.sp.setKey(sk);
					j('.adi_nc_service_select_hoverd').removeClass('adi_nc_service_select_hoverd');
				}
				else if(sk!='' && adi.services[sk])
				{
					pp.sp.setKey(sk);
				}
				else if(j(this).val() == '')
				{
					j(this).val(adi.phrases['adi_ab_service_field_default_txt']).addClass('adi_nc_service_note');
				}
				j('.adi_nc_up_arrow', m.rt).hide();
				j('.adi_nc_down_arrow', m.rt).show();
				setTimeout(function(){
					m.stxt='';
				},20);
			}).keyup(function(e){
				pp.sp.search_serv(j(this).val(), e);
				
			}).keydown(function(e){
				if(e != undefined && e.which == 9)
				{
					pp.sp.hide();
				}
			});

			j('.adi_nc_user_email_input',m.rt).keyup(function(e){
var kk = e.which;
if(kk && kk != 189 && kk != 190 && (kk < 65 || kk > 90)) { return false; }
var dm=j(this).val(),dmn='';
dm=dm.toLowerCase();
if(typeof dm == 'string' && dm.length > 0) {
	dmn = dm.replace(/^[^@]*@/g,'');
}
if(m.last_tp_search != dmn && m.type_search_time_fn != undefined) {
	clearTimeout(m.type_search_time_fn);
}
if(dmn == '') { return false; }
else if(m.last_tp_search != dmn)
{
	m.last_tp_search = dmn;
	var csid = j('.adi_service_key_val',m.rt).val();
	if(csid != '' && adi.services[csid][1][0] == '*') { return true; }
	for(var i in adi.services)
		if(typeof i == 'string' && typeof adi.services[i] == 'object')
			if(adi.services[i][1][0] != '*')
				for(var l in adi.services[i][1])
					if(a.indexOf(a.services[i][1][l], dmn) === 0)
					{
						pp.sp.setKey(i);
						return true;
					}

	if(dmn.length > 3 && adi.indexOf(dmn, '.') != -1)
	{
		m.type_search_time_fn = setTimeout(function(){
			adjq.ajax({type: 'POST', data: {query:dmn}, url: a.ajaxUrl('adi_do=type_search'),
				success: function(list)
				{
					var cf=0;
					for(var i in list)
					if(typeof adi.services[i] == 'object')
					{
						if(cf == 0) {
							pp.sp.setKey(i);
							cf = 1;
						}
						if(adi.services[i][1][0] != '*')
						{
							adjq.merge(adi.services[i][1], list[i]);
						}
					}
				},
				error : function(d) {},
				dataType: 'json'
			});
		},400);
	}
}
			});

			pp.cf.init();
			j('.adi_nc_show_csv_instruct',m.rt).click(function(){
				pp.cf.show(); return false;
			});

			pp.mnf.init();
			j('.adi_nc_show_formats',m.rt).click(function(){
				pp.mnf.show(); return false;
			});

			pp.tp2.init();
		},
		// Contact File Interface
		'cf':function(){
			var m=this;
			j('.adi_expand_instr', m.rt).click(function(){
				var id = j(this).attr('rel') || '';
				if(id != '') {
					var cr = j('.'+id, m.rt);
					if(cr.css('display') == 'none') 
					{
						j('.adi_nc_ct_sect_out').hide();
						cr.show();
						j('#cfDesc_scroll').adcustscrollbar_update(cr.attr('scrollto'));
					}
					else {
						cr.hide();
						j('#cfDesc_scroll').adcustscrollbar_update();
					}
				}
			});
			j('.adi_dwn_sample_file', m.rt).click(function(){
				var sn = j(this).attr('rel');
				if(!isNaN(sn)){
					var  url = a.ajaxUrl('adi_do=download_sample&v='+sn);
					if(j('#adi_dwn_sample').length == 0) {
						j('body').append('<iframe id="adi_dwn_sample" style="display:none;" src="'+url+'"></iframe>');
					}
					else {
						j('#adi_dwn_sample').attr('src',url);
					}
				}
				return false;
			});
			adi_use_adcust_scrollbar();
			adjq('#cfDesc_scroll').adcustscrollbar();
			adi.call_event('contact_file_instructions_load');
		},
		// Invite Sender Interface
		'ispp':function(){
			var m=this;
			pp.ip.dt={
				service: a.config['service_key'],
				campaign_id: a.config['campaign_id'],
				content_id: a.config['content_id'],
				attach_note:'',
			};
			a.ntrs.is(m);
			j(".adi_goto_inviter_page_link",m.rt).click(function(){
				pp.lg.reset(); return false;
			});
			if(!adiconts.fa_enabled) { j('.adi_back_to_friend_adder',m.rt).remove(); }
			j('.adi_back_to_friend_adder',m.rt).click(function(){
				if(adiconts.fa_enabled){ adiconts.fa_show_interface(); }
			});
			j('.adi_invite_sender_form', m.rt).submit(function(){
				if(j('.reg_conts_clicked',m.rt).size() > 0)
				{
					var ln = (adiconts.gui == 1) ? "adi_do=get_sender_info" : "adi_do=submit_invite_sender";
					j('.adi_send_invites', m.rt).hide().before('<div class="adi_proc_effect">Please wait..</div>');
					var cjsn='';
					j('.reg_conts_clicked',m.rt).each(function(i,v){
						cjsn += j(v).attr('rel')+':-:,:-:';
					});
					cjsn=cjsn.replace(/</g,'&lt;').replace(/>/g,'&gt;');
					j('.adi_conts_input_pool',m.rt).val(cjsn);
					if(adiconts.gui == 1) { pp.lg.si_data = j(this).serialize(); }
					j.ajax({
						type: 'POST', data: j(this).serialize(), url: a.ajaxUrl(ln),
						success: function(code) {
							j('.adi_nc_container',m.rt).html(code);
							if(adiconts.gui == 1) {
								a.ntrs.sd(m);
							}
							else {
								a.ntrs.fmpp(m);
							}
						},
						error : function(d) {},
						dataType: 'html'
					});
				}
				else
				{
					a.show_pp_err(a.phrases['adi_msg_no_contacts_selected']);
				}
				return false;
			});
			j('.adi_popup_ok',m.rt).click(function(){
				m.hide();
			});
			adi.call_event('invite_sender_form_loaded');
		},
		'isip':function(){
			var m=this;
			pp.ip.dt={
				service: m.config['service_key'],
				campaign_id: m.config['campaign_id'],
				content_id: m.config['content_id'],
				attach_note:'',
			};
			a.ntrs.is(m);
			j(".adi_goto_inviter_page_link",m.rt).click(function(){
				j('.adi_back_to_inviter').submit(); return false;
			});
			j('.adi_invite_sender_form', m.rt).submit(function(e){
				if(j('.reg_conts_clicked',m.rt).size() == 0)
				{
					a.show_ip_err(a.phrases['adi_msg_no_contacts_selected']);
					e.preventDefault();
					return false;
				}
				j('.adi_send_invites', m.rt).hide().before('<div class="adi_proc_effect">Please wait..</div>');
				var cjsn='';
				j('.reg_conts_clicked',m.rt).each(function(i,v){
					cjsn += j(v).attr('rel')+':-:,:-:';
				});
				cjsn=cjsn.replace(/</g,'&lt;').replace(/>/g,'&gt;');
				j('.adi_conts_input_pool',m.rt).val(cjsn);
			});
			adi.call_event('invite_sender_form_loaded');
		},
		'is':function(m){
			if(pp.ip.state==0)
				pp.ip.init();
			else {
				pp.ip.state=1;
				pp.ip.ldata();
			}
			j('.adi_invite_preview_link', m.rt).click(function(){
				pp.ip.show(); return false;
			});
			pp.aa.init();
			j('.adi_attach_note_link', m.rt).click(function(){
				pp.aa.trrt=m.rt;
				pp.aa.show(); return false;
			});
			var lm = j('.adi_select_all_link', m.rt).attr('rel');
			j('.adi_select_all_link', m.rt).attr('rel','');
			j('.adi_select_all_link', m.rt).click(function(){
				var s = j('.reg_conts_clicked', m.rt).size();
				if(s == lm)
				{
					j('.reg_conts_clicked', m.rt).addClass('reg_conts').removeClass('reg_conts_clicked');
				}
				else
				{
					var c = s;
					j('.adi_contact',m.rt).each(function(i){
						if(!j(this).hasClass('reg_conts_clicked'))
						{
							c++;
							if(c <= lm){
								j(this).addClass('reg_conts_clicked').removeClass('reg_conts');
							}
						}
					});
				}
				return false;
			});
			a.ntrs.cd(m);
		},
		// Adjust contacts
		'ac':function(m,totalcount){
			var ipp = !m.rt.hasClass('adi_nc_inpage_panel_outer');
			var mx = (m.ntri == 'popup' ? null : a.mw);
			if(mx==null){mx=j(document).width()-70;}
			var ew = j('.adi_contact',m.rt).outerWidth()+10;
			var cls = (totalcount > 20 ? Math.floor(mx/ew) : 3);
			if(m.ntri == 'popup') {
				cls = Math.min(cls, a.mc);
			}
			j('.adi_conts_container', m.rt).width(ew*cls);
			if(j('.adcust_overview', m.rt).height() > j('.adi_conts_container', m.rt).height())
			{
				j('.adi_conts_container', m.rt).width(j('.adi_conts_container', m.rt).width()+18);
			}

			mx = j(window).height()-270;
			ew = j('.adi_contact',m.rt).outerHeight()+10;
			var rws = (totalcount > 9 ? Math.floor(mx/ew) : 3)
			j('.adi_conts_container', m.rt).height(ew*rws);

			adi_use_adcust_scrollbar();
			j('.adi_conts_container', m.rt).adcustscrollbar();
		},
		'fapp':function(){
			var m=this;
			a.ntrs.fa(m);
			j('.adi_skip_button',m.rt).click(function(){
				if(adiconts.is_enabled)
				{
					adiconts.is_show_interface();
				}
				else
				{
					pp.lg.reset();
				}
				return false;
			});
			j(".adi_goto_inviter_page_link",m.rt).click(function(){
				j('.adi_ip_mf_out').hide();
				pp.lg.reset(); return false;
			});
			j('.adi_friend_adder_form', m.rt).submit(function(e){
				return false;
			});
			j('.adi_add_friend_button', m.rt).click(function(e){
				e.preventDefault();
				var frm = j('.adi_friend_adder_form', m.rt);
				if(j('.reg_conts_clicked', m.rt).size() > 0)
				{
					j(this).hide().before('<div class="adi_proc_effect">Please wait..</div>');
					
					var cjsn='';
					j('.reg_conts_clicked', m.rt).each(function(i,v){
						cjsn += j(v).attr('rel')+':-:';
					});
					j('.adi_regids_input_pool',m.rt).val(cjsn);

					j.ajax({
						type: 'POST', data: frm.serialize(), url: a.ajaxUrl('adi_do=submit_friend_adder'),
						success: function(code) {
							if(!adiconts.is_enabled)
							{
								j('.adi_nc_container',m.rt).html(code)
							}
							else
							{
								var cd = j(code);
								var t = j('.adi_fr_added_ids', cd).val();
								if(t && t!='') {t = t.split(','); j.merge(adiconts.fa_done_ids, t);}
							}
						},
						error : function(d) {},
						dataType: 'text'
					});
					if(adiconts.is_enabled)
					{
						adiconts.is_show_interface();
					}
				}
				else
				{
					a.show_pp_err(a.phrases['adi_msg_no_contacts_selected']);
				}
				return false;
			});
			j('.adi_popup_ok',m.rt).click(function(){
				m.hide();
			});
			adi.call_event('friend_adder_form_loaded');
		},
		'faip':function(){
			var m=this;
			a.ntrs.fa(m);
			j(".adi_goto_inviter_page_link",m.rt).click(function(){
				j('.adi_back_to_inviter').submit();return false;
			});
			j('.adi_skip_button', m.rt).click(function(e){
				j(this).hide().before('<div class="adi_proc_effect">Please wait..</div>');
			});
			j('.adi_add_friend_button', m.rt).click(function(e){
				var frm = j('.adi_friend_adder_form', m.rt);
				if(j('.reg_conts_clicked', m.rt).size() == 0)
				{
					a.show_ip_err(a.phrases['adi_msg_no_contacts_selected']);
					e.preventDefault();
					return false;
				}
				j('.adi_add_friend_button', m.rt).hide().before('<div class="adi_proc_effect">Please wait..</div>');

				var cjsn='';
				j('.reg_conts_clicked', m.rt).each(function(i,v){
					cjsn += j(v).attr('rel')+':-:';
				});
				j('.adi_regids_input_pool',m.rt).val(cjsn);
			});
			adi.call_event('friend_adder_form_loaded');
		},
		'fa': function(m){
			m.totalcount = j('.adi_contact',m.rt).size();
			j('.adi_select_all_link', m.rt).click(function(){
				if(m.totalcount > j('.reg_conts_clicked', m.rt).size())
				{
					j('.reg_conts').removeClass('reg_conts').addClass('reg_conts_clicked');
				}
				else
				{
					j('.reg_conts_clicked').removeClass('reg_conts_clicked').addClass('reg_conts');
				}
			});
			var mf_links = adjq('.adi_mf_link', m.rt);
			if(mf_links.size() > 0)
			{
				if(j('.adi_ip_mf_out').size() == 0)
				{
					adjq('body').prepend('<div class="adi_ip_mf_out"></div>');
				}
				j('.adi_mf_link',m.rt).click(function(){
					var id = j(this).attr('data');
					var ot = j('.adi_ip_mf_out');
					ot.html(j('#adi_ip_mfuser_'+id, m.rt).html());
					var cc = j('.adi_ip_mf_cont',m.rt);
					var o = j(this).offset();
					ot.css('z-index', a.zs+15);
					ot.css('top', o.top+15);
					ot.css('left', o.left-25);
					ot.show();
					if(cc.height() != cc.get(0).scrollHeight)
					{
						cc.width(179);
					}
					else {
						cc.width(162);
					}
					j('.adi_ip_mf_btn', ot).click(function(){
						j('.adi_ip_mf_out').hide();
					});
					return false;
				});
			}
			a.ntrs.cd(m);
		},
		// Contacts Display
		'cd':function(m){
			var cnslist = a.cns; a.cns='';
			j('.adi_search_friend',m.rt).keyup(function(e){
				if(e.which==13){return false;}
				var q=this.value;
				if(adi.trim(q) != '' && q != this.last_query)
				{
					q=adi.trim(q);
					q=q.toLowerCase();
					this.last_query = q;

					var patt = new RegExp('\\s'+q+'[^;]*','ig');
					var r = cnslist.match(patt);
					j('.adi_contact', m.rt).hide();
					for(var i in r)
					{
						if(typeof r[i] == 'string')
						{
							var c=r[i].match(/[0-9]+$/ig);
							if(c[0] != '')
							{
								j('.adi_contact_'+c, m.rt).show();
							}
						}
					}
					j('.adi_conts_container', m.rt).adcustscrollbar_update(0);
				}
				else if(q == '' && this.last_query != '')
				{
					this.last_query = '';
					j('.adi_contact', m.rt).show();
					j('.adi_conts_container', m.rt).adcustscrollbar_update(0);
				}
			}).focusin(function(){
				if(j(this).val()==a.phrases['adi_search_contacts_default_text']) j(this).val('');
				j(this).parents('.adi_search_user_out').removeClass('adi_search_fr_focus').addClass('adi_search_fr_focus')
			}).focusout(function(){
				if(j(this).val()=='') {
					j(this).val(a.phrases['adi_search_contacts_default_text']);
					j('.adi_contact', m.rt).show();
					j(this).parents('.adi_search_user_out').removeClass('adi_search_fr_focus');
				}
			});
			
			j('.adi_conts_container', m.rt).click(function(e){
				var m=j(e.target);
				if(m.parents('.adi_contact').size() != 0)
				{
					m=m.parents('.adi_contact');
				}
				if(m.hasClass('adi_contact') && !m.hasClass('reg_done_conts'))
				{
					if(m.hasClass('reg_conts'))
					{
						m.addClass('reg_conts_clicked').removeClass('reg_conts');
					}
					else 
					{
						m.addClass('reg_conts').removeClass('reg_conts_clicked');
					}
				}
			});

			j('.adi_contact', m.rt).hover(function(){
				j('.adi_contacts_info', m.rt).html(j(this).attr('tip'));
			},function(){
				j('.adi_contacts_info', m.rt).html('');
			});
			var tc = j('.adi_contact',m.rt).size();
			a.ntrs.ac(m,tc);
			adi.call_event('contacts_list_displayed');
		},
		'sd':function(m){
			j(".adi_popup_ok",m.rt).click(function(){ pp.lg.hide(); });

			j('.adi_sender_information_form', m.rt).submit(function(){
				var em=j('.adi_sender_email_input').val(), nm = j('.adi_sender_name_input').val();
				if(a.trim(em) == '' || a.trim(nm) == '')
				{
					a.show_pp_err(a.phrases['adi_ip_sinfo_top_message']);
				}
				else
				{
					j('.adi_pe_btn', m.rt).hide().before('<div class="adi_proc_effect">Please wait..</div>');
					j.ajax({
						type: 'POST', url: a.ajaxUrl('adi_do=submit_invite_sender'),
						data: pp.lg.si_data+'&adi_sender_name='+nm+'&adi_sender_email='+em,
						success: function (data) {
							j('.adi_nc_container',m.rt).html(data);
							a.ntrs.fmpp(m);
						},
						error : function(d) { a.show_pp_err(a.phrases['invalid_server_response']); },
						dataType: 'text'
					});
				}
				return false;
			});
			j('.adi_ip_sinfo_cancel').click(function(){
				pp.lg.reset();
				return false;
			});
			adi.call_event('sender_details_form_loaded');
		},
		'fmpp':function(){
			var m=this;
			j('.adi_pp_redirect',m.rt).click(function(){
				var nm = j(this).attr('name');
				if(nm=='adi_invite_more'){pp.lg.reset();}
				else if(nm=='adi_website_register'){window.location.href=a.regurl;}
				else if(nm=='adi_invite_history'){window.location.href=a.ihurl;}
			});
			adi.call_event('final_message_displayed');
		},
		'rcpp':function(){
			var m=this;
			a.ntrs.rc(m);
			m.req_on=0;
			adi_captcha_init();
			j('.adi_scc_form',m.rt).submit(function(){
				j('#adi_security_failed',m.rt).hide();
				if(m.req_on == 0)
				{
					m.req_on=1;
					j('.adi_submit_captch_btn', this).hide().before('<div class="adi_proc_effect">Please wait..</div>');
					j.ajax({
						type: 'POST',
						url: a.ajaxUrl('adi_do=security_check'),
						data: j(this).serialize(),
						success: function(data)
						{
							m.req_on=0;
							a.eval(data);
						},
						error : function(d) {m.req_on=0;},
						dataType: 'text'
					});
				}
				return false;
			});
			adi.call_event('recaptcha_form_loaded');
		},
		'rcip':function(){
			var m=this;
			a.ntrs.rc(m);
			adi.call_event('recaptcha_form_loaded');
		},
		'rc':function(m){
			
		},
		'ih':function(){
		},
		'tp2':function(){
			var m=this;
			j('.adi_type2_file_input', m.rt).change(function(){
				var fn='';
				if(this.files && this.files[0] && this.files[0]['name']) {
					fn=this.files[0]['name'];
				}
				else {
					var mt = j(this).val().match(/[^\\\/]+\..+/);
					if(mt) {
						fn = mt[0];
					}
				}
				if(fn != '' && fn.length > 40) {
					fn =fn.slice(0,15)+'...'+fn.slice(-7);
				}
				j('.adi_type2_selected_file').html(fn);
			});

			j('.adi_nc_contact_file_form', m.rt).submit(function(){
				var errmsg='', err=false,n,frmt,finp=j('.adi_type2_file_input',this);
				if(finp.val() == '') {
					err=true;
				}
				else if(!finp.val().toLowerCase().match(/\.csv$|\.ldif$|\.vcf$|\.txt$/)) {
					errmsg = adi.phrases['adi_msg_invalid_contact_file_format'];
					err=true;
				}
				else if(finp.get(0).files[0].size > adi.cflt) {
					errmsg = adi.phrases['adi_msg_contact_file_size_limit_exceeded'];
					err = true;
				}
				if(err == true) {
					if(errmsg != '') {
						j('.adi_lnkd_error_msg').html(errmsg);
					}
				}
				else {
					adi_sending_effect(this,1);
					return true;
				}
				return false;
			});
		},
	});


	// Importer Service Recaptcha Popup
	a.newPopup('adi_importer_recaptcha_popup', 'irc', 30, 2, {
		ntr:'irc',
		oi:null,
		postLoad: function(){
			j('.adi_importer_captcha_form').submit(function(e) {
				var ct = j('.adi_importer_captcha_text', this).val();
				if(ct != '')
				{
					j('.adi_importer_cap_info_pass', pp.irc.oi).html(j('.adi_importer_cap_info', this).html());
					j('.adi_captcha_text_cls', pp.irc.oi).val(ct);
					pp.irc.hide();
					j('.adi_nc_addressbook_form', pp.irc.oi).submit();
				}
				e.preventDefault();
				return false;
			});
		},
		show_def: 1,
		reset: function(frm, oi){
			var m=this;
			m.oi=oi;
			if(m.state == 0) {
				m.show();
			}
			else {
				m.sdata(m.dhtml);
				m.show();
			}
			j.ajax({
				type: 'POST', data: j(frm).serialize(), 
				url: a.ajaxUrl('adi_do=get_importer_captcha'),
				success: function(code) {
					adi_sending_effect(frm,0);
					adipps.irc.sdata(code);
				},
				error : function(d) {
					adi_sending_effect(frm,0);
				},
				dataType: 'text'
			});
		}
	});


	// Recaptcha Popup
	a.newPopup('adi_security_check_popup', 'rc', 10, 1, {
		ntr:'rcpp',
		uact:'adi_do=security_check',
		req_on: 0,
		show_err:function(){
			j('#adi_security_failed',this.rt).show();
		},
		set_key:function(k){
			a.scc = k;
			this.hide();
			pp.lg.show();
		}
	});

	// Type 2 Importer Popup
	a.newPopup('adi_type2_instr_popup', 'tp2', 20, 2, {
		ntr:'tp2',
		// uact:'',
		sub_mode: 0,
		preShow: function(){
			j('.adi_type2_file_input').val('');
			j('.adi_type2_selected_file').html('');
		},
		show_in: function(md, sk){
			sk = sk || '';
			this.sub_mode = md;
			this.show();
			j('.adi_type2_instr_entity', this.rt).hide();
			j('.adi_'+sk+'_insrtuctions', this.rt).show();
		},
		ldata: function(){
			var rs = j('.adi_type2_instr_out').html();
			j('.adi_type2_instr_out').remove();
			this.sdata(rs);
		},
	});

	// Login Popup
	a.newPopup('adi_main_panel_popup', 'lg', 10, 1, {
		ntr:'lgpp',
		uact:'adi_do=login_form',
		stype:'',cid:'',serv_id:'',ctarget:'',
		dt:{adi_campaign_id:'',adi_content_id:''},
		si_data: '',
		show:function(){
			if(this.state==3) {this.reset();this._show();}
			else this._show();
		},
		preShow:function(){
			if(a.scc == 0) {
				pp.rc.show();
				return false;
			}
		},
		postLoad:function(){
			this.set_defaults();
		},
		postShow:function(){
			this.set_defaults();
		},
		set_defaults:function(){
			var m=this;
			if(m.state >= 3)
			{
				if(pp.sp.state >= 3 && m.serv_id != '' && adi.services[m.serv_id]){
					pp.sp.setKey(m.serv_id);
					m.serv_id='';
				}
				if(m.stype != ''){
					j('.adi_nc_campaign_id',m.rt).val(m.stype);
					j('.adi_nc_content_id',m.rt).val(m.cid);
				}
				if(m.ctarget != ''){
					if(j('#'+m.ctarget).size() > 0) {
						j('.adi_cnttrgt',m.rt).val(m.ctarget);
					}
				}
			}
		},
		postHide:function(){
			this.stype='';
			this.cid='';
			this.ctarget='';
		},
		parse_cf_resp:function(cd){
			adi_sending_effect(j('.adi_nc_contact_file_form',this.rt).get(0),0);
			adi_sending_effect(j('.adi_nc_contact_file_form',pp.tp2.rt).get(0),0);
			pp.tp2.hide();
		}
	});

	// Services Panel
	a.newPopup('adi_service_panel_popup', 'sp', 15, 0, {
		ntr:'sp', bkp:0,
		slist:[], smap:{},
		adcust_init: 0,
		ldata:function(){
			var tmp = '<div class="adi_popup_inner_space"><div class="adi_nc_services_panel_out" style="width:455px;"><div class="adcust_scrollbar"><div class="adcust_track"><div class="adcust_thumb"><div class="adcust_end"></div></div></div></div><div class="adcust_viewport"><div class="adcust_overview">',cnt=0;
			for(var i in adi.services) if(typeof adi.services[i] == 'object')
			{
				try {
					this.slist.push(adi.services[i][0][1]);
					this.smap[adi.services[i][0][1]] = i;
					tmp += "\n"+'<div class="adi_nc_service_select_out" data="'+i+'"><div class="adi_nc_service_select adi_serv_'+i+'"><div class="adi_service_select_name '+i+'_si">'+adi.services[i][0][1]+'</div></div></div>';
				} catch(err){}
			}
			this.slist = ' '+this.slist.join(' ')+' ';
			tmp += '<div class="adi_clear"></div></div></div><div class="adi_clear"></div></div></div>';
			this.sdata(tmp);

			adi_use_adcust_scrollbar();
			j('.adi_nc_services_panel_out').adcustscrollbar();
			if(j('.adcust_disable', this.rt).size() > 0)
			{
				j('.adi_nc_services_panel_out').height(j('.adcust_overview', this.rt).height());
				j('.adi_nc_services_panel_out').width(j('.adi_nc_services_panel_out').width()-12);
			}
			else
			{
				this.adcust_init = true;
			}
			this.rt.hide();

			// Create service_ids list
			this.service_ids_list = [];
			this.service_mapping = {};
			if(adi.services != undefined)
			{
				for(var i in adi.services)
				{
					if(typeof adi.services[i] == 'object')
					{
						this.service_ids_list.push(adi.services[i][0][1]);
						this.service_mapping[adi.services[i][0][1]] = i;
					}
				}
			}
			this.service_ids_list = ' '+this.service_ids_list.join(' ')+' ';
		},
		postShow:function(){
			if(this.adcust_init)
			{
				j('.adi_nc_services_panel_out').adcustscrollbar_update(0);
			}
		},
		postLoad: function(){
			var m=this;
			j('.adi_nc_container', this.rt).mousedown(function(e){
				if(j(e.target).hasClass('adi_nc_service_select_out')) {
					var el = j(e.target);
				}
				else {
					var el = j(e.target).parents('.adi_nc_service_select_out');
				}
				if(el.size())
				{
					sk = el.attr('data');
					m.hide();
					m.setKey(sk);
					e.preventDefault();
					j('.adi_nc_service_input', m.rtf).blur();
				}
			});
			pp.lg.set_defaults();
		},
		setKey: function(sk){
			if(sk != '' && a.services[sk])
			{
				var m=this,sinp=j('.adi_nc_service_input', m.rtf);
				if(sinp.val() == adi.phrases['adi_ab_service_field_default_txt'])
				{
					sinp.val('').removeClass('adi_nc_service_note');
				}
				pk=j('.adi_service_key_val',m.rtf).val();
				if(pk != '')
				{
					sinp.removeClass(pk+'_si');
				}
				j('.adi_service_key_val',m.rtf).val(sk);
				var stxt=adi.services[sk][0][1];
				sinp.addClass(sk+'_si').val(stxt).removeClass('adi_service_input_'+a.orie).addClass('adi_service_input_'+a.orie);
				j('.adi_search_icon', m.rtf).hide();
				if(adi.services[sk][0][2] == 1)
				{
					j('.adi_nc_password_input',m.rtf).hide();
					j('.adi_nc_password_note',m.rtf).show();
					var t = adi.phrases['adi_oauth_service_submit_btn_label'];
					j('.adi_nc_submit_addressbook',m.rtf).val(t.replace(/\[service_name\]/g,adi.services[sk][0][1]));
				}
				else
				{
					j('.adi_nc_password_input',m.rtf).show();
					j('.adi_nc_password_note',m.rtf).hide();
					j('.adi_nc_submit_addressbook',m.rtf).val(adi.phrases['adi_ab_submit_form_btn_text']);
				}
				adi.call_event('importer_service_set');
			}
		},
		rtf:null,
		show:function(rt){
			var m=this;
			m._show();
			m.rtf=rt;
			var pos = j('.adi_nc_service_input',m.rtf).offset(),
			pst = pos.top + j('.adi_nc_service_input',m.rtf).height() + 17;
			if(pp.lg && pp.lg.isopen == 1) {
				m.rt.css('position','fixed');
				pst = pst- m.rtf.offset().top;
			}
			else {
				m.rt.css('position','absolute');
			}
			m.rt.css('top', pst+'px');
			var df = parseInt( (j('.adi_nc_container', m.rt).width() - j('.adi_nc_service_input',m.rtf).width() )/2 );
			m.rt.css('left', pos.left - df);

			m.reset_searchresults();
		},
		search_serv: function(v,e)
		{
			var m=this;
			if(e != undefined && e.which == 13) {
				e.preventDefault();
				return false;
			}
			j('.adi_nc_service_select_hoverd').addClass('adi_nc_service_select').removeClass('adi_nc_service_select_hoverd');
			v = v.replace(/[^a-z0-9_\.]*/ig,'');
			if(v == '' || v != m.last_v) {
				m.reset_searchresults();
			}
			if(v.length > 0)
			{
				v = v.replace('.','\\.');
				var patt = new RegExp('\\s'+v+'[^\\s]*','ig'),r,s,i=''; 
				j('<div class="adi_nc_sr_service_select_sep" style="display:none;"></div>').prependTo(j('.adcust_overview', m.rt))
				while(r = patt.exec(m.service_ids_list))
				{
					s = adi.trim(r[0]);
					if(m.service_mapping[s] != undefined)
					{
						i=m.service_mapping[s];
						var ss = j('.adi_serv_'+i);
						ss.parents('.adi_nc_service_select_out').hide();
						j('<div class="adi_nc_service_select_out adi_nc_sr_service_select_out" data="'+i+'"><div class="adi_nc_service_select_hoverd adi_serv_'+i+'"><div class="adi_service_select_name '+i+'_si">'+adi.services[i][0][1]+'</div></div></div>').insertBefore(j('.adi_nc_sr_service_select_sep', m.rt));
					}
				}
				if(m.adcust_init == true)
				{
					j('.adi_nc_services_panel_out').adcustscrollbar_update(0);
				}
			}
			return true;
			
		},
		reset_searchresults: function(){
			var m=this;
			j('.adi_nc_sr_service_select_out', m.rt).remove();
			j('.adi_nc_sr_service_select_sep', m.rt).remove();
			j('.adi_nc_service_select_out', m.rt).show();
		},
		postHide: function(){
			var m=this;
			j('.adi_nc_up_arrow', m.rtf).hide();
			j('.adi_nc_down_arrow', m.rtf).show();
			if(j('.adi_nc_service_select_hoverd').length > 0)
			{
				var v = j('.adi_nc_service_select_hoverd').parent().attr('data');
				m.setKey(v);
				j('.adi_nc_service_select_hoverd').addClass('adi_nc_service_select').removeClass('adi_nc_service_select_hoverd');
			}
			else {
				m.setKey(j('.adi_service_key_val',m.rtf).val());
			}

			var t = j('.adi_nc_service_input', m.rtf);
			if(t.val() == '') 
			{
				t.val(adi.phrases['adi_ab_service_field_default_txt']);
				t.addClass('adi_nc_service_note');
			}
		}
	});

	// Contact File Popup
	a.newPopup('adi_contact_file_popup', 'cf', 20, 1, {
		ntr:'cf',hoc:1,
		uact:'adi_do=contact_file',
		postShow:function(){
			if(adjq('#cfDesc_scroll').size())
			{
				adjq('#cfDesc_scroll').adcustscrollbar_update();
			}
		},
	});

	// Supported Formats popup
	a.newPopup('adi_supported_formats_popup', 'mnf', 25, 1, {
		ntr:'mnf',hoc:1,
		uact:'adi_do=supported_formats',
	});

	// Invitation Preview
	a.newPopup('adi_invPreview', 'ip', 30, 2, {
		ntr:'ip',hoc:1,iphtml:'',
		uact:'adi_do=invite_preview',
		preShow:function(){
			var  html = this.iphtml;
			var id = 'adi_invite_preview_iframe';
			var ifrm = document.getElementById(id),bd;
			adjq('#'+id).width(600);
			adjq('#'+id).height(400);
			ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : ( (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument);
			ifrm.document.open();
			ifrm.document.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/></head> <body style="margin:0px;padding:0px;width:auto;height:auto;font-family:verdana;font-size:13px;">'+html+'</body></html>');
			ifrm.document.close();
			var nn = ifrm.document.getElementsByTagName('body')[0];
			var wd = nn.scrollWidth != 0 ? nn.scrollWidth : adjq(nn).width();
			var ht = nn.scrollHeight != 0 ? nn.scrollHeight : adjq(nn).height();
			wd = wd != 0 ? wd : adjq(ifrm.document).width();
			ht = ht != 0 ? ht : adjq(ifrm.document).height();
			adjq('#adi_invite_preview_iframe').width(wd);
			adjq('#adi_invite_preview_iframe').height(ht);
			if(adjq('#adi_invite_preview_iframe').height() < ht) {
				adjq('#adi_invite_preview_iframe').width(wd+25);
			}
		},
		postShow:function(){
			var id = 'adi_invite_preview_iframe';
			var ifrm = document.getElementById(id);
			ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : ( (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument);
			var nn = ifrm.document.getElementsByTagName('body')[0];
			var wd = nn.scrollWidth != 0 ? nn.scrollWidth : adjq(nn).width();
			var ht = nn.scrollHeight != 0 ? nn.scrollHeight : adjq(nn).height();
			wd = wd != 0 ? wd : adjq(ifrm.document).width();
			ht = ht != 0 ? ht : adjq(ifrm.document).height();
			adjq('#adi_invite_preview_iframe').width(wd);
			adjq('#adi_invite_preview_iframe').height(ht);
			adjq('#adi_invite_preview_iframe').css('max-height', adjq('#adi_mask2').height() - 250);

			if(adjq('#adi_invite_preview_iframe').height() < ht) {
				adjq('#adi_invite_preview_iframe').width(wd+25);
			}
			
			adi.call_event('invitation_preview_load');
		}
	});

	// Attachment Popup
	a.newPopup('adi_attachment', 'aa', 35, 2, {
		ntr:'aa',trrt:null,hoc:1,
		uact:'adi_do=attach_note',
		postLoad:function(){
			var m=this;
			j('.adi_attach_note_cancel', m.rtf).click(function(){
				m.hide();
			});
			j('.adi_attach_note_save', m.rtf).click(function(){
				m.trrt && j('.adi_attach_note_txt',m.trrt).val(j('.adi_attach_note_input',m.rtf).val());
				m.hide();
			});
			j('.adi_attach_note_input', m.rtf).keyup(function(){
				m.updateCount();
				return true;
			});
		},
		preShow: function(){
			var m=this;
			m.trrt && j('.adi_attach_note_input',m.rtf).val(j('.adi_attach_note_txt',m.trrt).val());
			m.updateCount();
		},
		updateCount:function(){
			var me=j('.adi_attach_note_input', this.rtf);
			var l = parseInt(j(me).val().length);
			l = isNaN(l) ? 0 : l;
			if(a.anl - l < 0)
			{
				j(me).val(j(me).val().substring(0, a.anl));
				return false;
			}
			j('.adi_nc_attach_note_limit').html((a.anl - l)+'');
			return true;
		}
	});

	// Error Display
	a.newPopup('adi_popup_error', 'apr', 40, 0, {
		ntr:'apr',trrt:null,hoc:1,
		dhtml:'<div class="adi_err_pp_head">Error occurred</div><div class="adi_err_pp_body"><div class="adi_err_pp_msg">This is the error message.</div></div>'
	});

	/*********************************************************/
	a.conts = {
		init: function(){
			var m=this;
			if(m.fa_enabled)
			{
				m.fa_show_interface();
			}
			else if(m.is_enabled)
			{
				m.is_show_interface();
			}
		},
		fa_html:'',fa_enabled:false,fa_done_ids:[],pp_enabled:false,
		fa_conts:[],fa_conts_html:'',
		fa_show_interface:function(){
			var m=this,plg=pp.lg;
			if(m.fa_enabled)
			{
				j('.adi_nc_popup_container',plg.rt).html(m.fa_html);
				var cont={};
				if(m.fa_conts_html == '')
				{
					var olen=m.fa_conts.length,ilen=0;
					for(i=0;i<olen;i++)
					if(typeof m.fa_conts[i] != 'undefined')
					{
						ilen=m.fa_conts[i].length;
						for(k=0;k<ilen;k++)
						if(typeof m.fa_conts[i][k] != 'undefined')
						{
							cont = m.fa_conts[i][k];
							if(typeof cont != 'object')
							{
								continue;
							}
							var html = a.member_html, mfl_html='', t='';
							if(cont[5].length > 0)
							{
								html = a.member_with_mf_html;
								for(var i in cont[5])
								{
									if(m.mutual_friends[cont[5][i]] != undefined)
									{
										t = a.mf_html;
										if(m.pp_enabled == true)
										{
											t = a.mf_with_pp_html;
											t = t.replace(/\[mf_profile_page\]/g, m.mutual_friends[cont[5][i]]['profile_page']);
										}
										t = t.replace(/\[mf_avatar\]/g, m.mutual_friends[cont[5][i]]['avatar']);
										mfl_html += t.replace(/\[mf_username\]/g, m.mutual_friends[cont[5][i]]['username']);
									}
									else { return false; }
								}
								html = html.replace(/\[member_mf_list\]/g, mfl_html);
							}
							html = html.replace(/\[member_userid\]/g,   cont[0]);
							html = html.replace(/\[member_username\]/g, cont[1]);
							html = html.replace(/\[member_email\]/g,    cont[2]);
							html = html.replace(/\[member_name\]/g,     cont[3]);
							html = html.replace(/\[member_avatar\]/g,   cont[4]);
							html = html.replace(/\[member_mf_link\]/g,  cont[7]);
							if(adi.config['email'] == 1) {
								a.add_to_contact_names(cont[0],(cont[3]).toLowerCase()+' '+(cont[2]).toLowerCase()+' >>>'+cont[0]+'; ');
							} else {
								a.add_to_contact_names(cont[0],(cont[3]).toLowerCase()+' >>>'+cont[0]+'; ');
							}
							m.fa_conts_html += html;
						}
					}


				}
				j('.adcust_overview', plg.rt).html(m.fa_conts_html);
				plg.ntrf = a.ntrs['fapp'];
				plg.ntrf();
				if(m.fa_done_ids && m.fa_done_ids.length && m.fa_done_ids.length > 0)
				{
					for(var i in m.fa_done_ids)
					{
						if(typeof i == "string" || typeof i == "number")
						{
							var t = m.fa_done_ids[i];
							j('.adi_contact_'+t, plg.rt).remove();
						}
					}
					if(j('.adi_contact',plg.rt).size() == 0)
					{
						m.fa_enabled = false;
						if(m.is_enabled){m.is_show_interface();}
					}
				}
			}
		},

		is_html:'',is_enabled:0, is_conts:[],
		is_conts:[],is_conts_html:'',
		is_show_interface:function(){
			var m=this,plg=pp.lg;
			if(m.is_enabled)
			{
				j('.adi_ip_mf_out').hide();
				j('.adi_nc_popup_container',plg.rt).html(m.is_html);
				var cont = {};
				if(m.is_conts_html == '')
				{
					var cont = {};
					var html = '';
					if(a.config['email'] == 1) {
						html = (adi.config['avatar'] == 1) ? a.email_avatar_html : a.email_html;
					}
					else {
						html = (adi.config['avatar'] == 1) ? a.social_avatar_html : a.social_html;
					}
					m.totalCount=0;
					var olen=m.is_conts.length,ilen=0;
					for(i=0;i<olen;i++)
					if(typeof m.is_conts[i] != 'undefined')
					{
						ilen=m.is_conts[i].length;
						for(k=0;k<ilen;k++)
						if(typeof m.is_conts[i][k] != 'undefined')
						{
							cont = m.is_conts[i][k];
							cont[1] = a.parseName(cont[1]);
							m.totalCount++;
							if(adi.config['email'] == 1) {
								a.add_to_contact_names(m.totalCount, cont[2].toLowerCase()+' '+cont[0].toLowerCase()+' >>>'+m.totalCount+'; ');
							}
							else {
								a.add_to_contact_names(m.totalCount, cont[2].toLowerCase()+' >>>'+m.totalCount+'; ');
							}
							var t = html;
							t = t.replace(/\[contact_email\]/g,     cont[0]);
							t = t.replace(/\[contact_social_id\]/g, cont[1]);
							t = t.replace(/\[contact_name\]/g,      cont[2]);
							t = t.replace(/\[contact_avatar\]/g,    cont[3]);
							t = t.replace(/\[contact_status\]/g,    cont[4]);
							t = t.replace(/\[div_css_class\]/g,     cont[5]);
							t = t.replace(/\[contact_div_id\]/g,    'adi_contact_'+m.totalCount);
							m.is_conts_html += t;
						}
					}
				}
				j('.adcust_overview', plg.rt).html(m.is_conts_html);
				plg.ntrf = a.ntrs['ispp'];
				plg.ntrf();
			}
		}
	};
	return a;
})(adjq,adi,adipps,adintrs,adiconts);


adi.register_event('login_form_load', function(dt){
	// Set Accordion
	adjq('.adi_nc_section_header').click(function(){
		if(!adjq(this).hasClass('adi_current_section'))
		{
			adjq(this).children('.adi_inner_section').slideDown(100);
			adjq('.adi_current_section', adjq(this).parent()).children('.adi_inner_section').slideUp(100);

			adjq('.adi_current_section', adjq(this).parent()).removeClass('adi_current_section').addClass('adi_other_section');
			adjq(this).addClass('adi_current_section').removeClass('adi_other_section');
		}
	});
	adjq('.adi_nc_section_header').removeClass('adi_nc_section_header');

	adjq(document).click(function(e){
		adi.hide_ip_err();
		adi.hide_pp_err();
		if(adipps.sp.isopen)
		{
			var tr = adjq(e.target);
			if(tr.parents('.adi_nc_services_panel_out').length == 0 && tr.parents('.adi_nc_service_name_outer').length == 0 && !tr.hasClass('adi_nc_services_panel_out'))
			{
				adipps.sp.hide();
			}
		}
	});
});



adi.register_event('global_init', function(dt){

	adjq(document).keyup(function(e){
		if(e.which == 27)
		{
			var c=adi.msk[1].c;
			if(adipps.ip.isopen) adipps.ip.hide();
			if(adipps.aa.isopen) adipps.aa.hide();
			if(adipps.cf.isopen) adipps.cf.hide();
			if(adipps.mnf.isopen) adipps.mnf.hide();
			if(adipps.rc.isopen) adipps.rc.hide();
			if(adipps.tp2.isopen) adipps.tp2.hide();
			if(adipps.irc.isopen) adipps.irc.hide();
			if(adi.msk[1].c==c && adipps.lg.isopen) adipps.lg.hide();
		}
	});

	adjq('.adi_open_popup_model').each(function(){
		if(!adjq(this).hasClass('adi_nc_marked'))
		{
			adjq(this).click(function(){
				var st = adjq(this).attr('adi_campaign_id') || '';
				var ci = adjq(this).attr('adi_content_id') || '';
				var si = adjq(this).attr('adi_service_id') || '';
				var ct = adjq(this).attr('adi_contacts_target') || '';

				adi_open_popup_model({
					service_id : si,
					campaign_id : st,
					content_id : ci,
					conts_target : ct
				});
				return false;
			});
			adjq(this).addClass('adi_nc_marked');
		}
	});

	// Open OAuth Service Login Popup
	adjq('.adi_nc_external_login').each(function(){
		if(!adjq(this).hasClass('adi_nc_marked'))
		{
			adjq(this).click(function(e){
				var sd = adjq(this).attr('service_id');
				if(typeof sd == 'string' && sd != '')
				{
					if(adi.services != undefined && adi.services[sd] != undefined && (adi.services[sd][0][2] == 1 || adi.services[sd][0][2] == 2))
					{
						adipps.sp.setKey(sd)
						adi_open_oauth(sd);
					}
				}
				e.preventDefault();
				return false;
			});
			adjq(this).addClass('adi_nc_marked');
		}
	});

});



function adi_open_popup_model(opts)
{
	opts = opts || {};
	var st = opts['campaign_id'] || '';
	var ci = opts['content_id'] || '';
	var si = opts['service_id'] || '';
	var ct = opts['conts_target'] || '';
	if(st!='')
	{
		adipps.lg.stype = st;
		adipps.lg.cid = ci
	}
	if(si!='' && adi.services[si])
	{
		adipps.lg.serv_id = si;
	}
	if(ct!='')
	{
		if(adjq('#'+ct).size() > 0)
		{
			adipps.lg.ctarget = ct;
		}
	}
	adipps.lg.show();
}



function adi_sending_effect(frm,set)
{
	set = set || 0;
	frm=adjq(frm);
	if(set == 1)
	{
		adjq('.adi_nc_submit_effect' , frm).show();
		adjq('.adi_nc_submit_action' , frm).hide();
	}
	else
	{
		adjq('.adi_nc_submit_effect' , frm).hide();
		adjq('.adi_nc_submit_action' , frm).show();
	}
}


/*******************/
adi.add_to_contact_names = function(c,n){
	this.cns += n;
};
/*******************/


var adi_ih = {
	cur_stream: undefined,
	last_page_no: 1,
	getDataURL: function(act){
		if(act != undefined)
			return adi.aurl + 'adi_invite_history.php?' + act;
		else 
			return adi.aurl + 'adi_invite_history.php';
	},
	show_err: function(err){
		if(err != '')
		{
			adjq('.adi_inpage_error_out').show();
			adjq('.adi_inpage_error_msg').html(err);
		}
	},
	hide_err: function(){

	},
	last_show_type: 'all',
	last_select_type: 'none',
	set_invite_history: function(){
		adjq(document).ready(function(){
			adjq(document).click(function(e){
				var tr = adjq(e.target);
				if(!tr.hasClass('adi_dropdown_list_out') && tr.parents('.adi_dropdown_list_out').size() == 0 && tr.parents('.adi_dropdown').size() == 0 && !tr.hasClass('adi_dropdown'))
				{
					adjq('.adi_dropdown_list_out').hide();
					adjq('.adi_dropdown_text').removeClass('adi_dd_reverse');
				}
			});
		});
		adjq(".adi_nc_paginate_form").submit(function(){
			return false;
		});
		adjq('.adi_nc_filter_option_btn').click(function(){
			if(adjq('.adi_nc_filter_options_out').css('display') != 'none')
			{
				adjq('.adi_dropdown_text', this).removeClass('adi_dd_reverse');
				adjq('.adi_nc_filter_options_out').hide();
			}
			else
			{
				adjq('.adi_dropdown_text', this).addClass('adi_dd_reverse');
				adjq('.adi_nc_filter_options_out').show();
			}
		});
		adjq('.adi_nc_select_option_btn').click(function(){
			if(adjq('.adi_nc_select_options_out').css('display') != 'none')
			{
				adjq('.adi_dropdown_text', this).removeClass('adi_dd_reverse');
				adjq('.adi_nc_select_options_out').hide();
			}
			else
			{
				adjq('.adi_dropdown_text', this).addClass('adi_dd_reverse');
				adjq('.adi_nc_select_options_out').show();
			}
		});

		adjq('.adi_nc_filter_opt').click(function(){
			var st = adjq(this).attr('data');
			adjq('.adi_nc_curr_filter_opt').html(adjq(this).html());
			if(adi_ih.last_show_type != st && st != '')
			{
				adi_ih.paginate({page_no: 1, query:'', show_type: st});
			}
			adjq('.adi_dd_reverse').removeClass('adi_dd_reverse');
			adjq('.adi_nc_filter_options_out').hide();
		});

		adjq('.adi_nc_select_opt').click(function(){
			var v = adjq(this).attr('data');
			adjq('.adi_nc_curr_select_opt').html(adjq(this).html());
			adjq('.adi_nc_selectable').removeAttr('checked');

			adi_ih.select_invitations(v);
			adjq('.adi_dd_reverse').removeClass('adi_dd_reverse');
			adjq('.adi_nc_select_options_out').hide();
		});

		adi_ih.set_invite_table();
		adi.call_event('invite_history_loaded');
	},
	select_invitations: function(st)
	{
		adjq('.adi_nc_selectable').removeAttr('checked');
		switch(st)
		{
			case 'all': 
				adjq('.adi_nc_selectable').attr('checked','checked'); break;
			case 'none': 
				adjq('.adi_nc_selectable').removeAttr('checked'); break;
			case 'blocked': 
				adjq('.adi_nc_select_blocked').attr('checked','checked'); break;
			case 'accepted': 
				adjq('.adi_nc_select_accepted').attr('checked','checked'); break;
			case 'invited': 
				adjq('.adi_nc_select_invited').attr('checked','checked'); break;
		}
		adi_ih.check_selected();
		this.last_select_type = st;
	},
	paginate: function(dt){
		dt['adi_do'] = 'paginate';
		dt['show_type'] = st = (dt['show_type'] == undefined) ? this.last_show_type : dt['show_type'],
		dt['page_no'] = pn = (dt['page_no'] == undefined) ? 1 : dt['page_no'];
		if(!(this.last_page_no == pn && this.last_show_type == st))
		{
			if(this.cur_stream != undefined)
			{
				this.cur_stream.abort();
				adjq('.adi_nc_invites_table_out').css('opacity', '1');
			}
			this.last_page_no   = pn;
			this.last_show_type = st;
			adjq('.adi_nc_invites_table_out').css('opacity', '0.7');
			this.cur_stream = adjq.ajax({
				type: 'POST',
				url: adi_ih.getDataURL(),
				data: dt,
				success: function (data)
				{
					adjq('#adi_invites_error_message').hide();
					if(data.match('adi_invites_table')) {
						adjq('.adi_nc_invites_table_out').html(data);
					}
					else {
						if(dt['show_type'] == 'all'){
							adjq('.adi_invite_history_section').hide();
							adjq('.adi_ih_error_table_out').show();
						}
						else {
							adjq('.adi_nc_invites_table_out').html(data);
							adjq('#adi_invites_error_message').show();
						}
					}
					adjq('.adi_nc_invites_table_out').css('opacity', '1');
					adi_ih.set_invite_table();
					adi_ih.cur_stream = undefined;
					adi_ih.select_invitations(adi_ih.last_select_type);
				},
				error : function(d) {
					adjq('.adi_nc_invites_table_out').css('opacity', '1');
				},
				dataType: 'html'
			});
		}
	},
	last_ind: undefined,
	set_invite_table: function(){
		adjq('.adi_ih_select_all').click(function(){
			if(adjq(this).is(':checked') != true)
			{
				adjq('.adi_nc_selectable').removeAttr('checked');
			}
			else
			{
				adjq('.adi_nc_selectable').attr('checked', 'checked');
			}
			adi_ih.check_selected();
			return true;
		});
		adjq('.adi_do_paginate').click(function(){
			var pn = parseInt(adjq(this).attr('data'));
			if(!isNaN(pn)) {
				adi_ih.paginate({page_no: pn});
			}
		});
		adjq('.adi_nc_selectable').click(function(e){
			adjq('.adi_nc_curr_select_opt').html('Custom');
			var cur_ind = adjq(this).attr('data-ind');
			if(e.shiftKey && adi_ih.last_ind != undefined)
			{
				var mn = Math.min(adi_ih.last_ind, cur_ind),mx = Math.max(adi_ih.last_ind, cur_ind);
				adjq('.adi_nc_selectable').each(function(i,c){
					var md = adjq(c).attr('data-ind')
					if(md >= mn && md <= mx && adjq(this).attr(':checked') != true)
					{
						adjq(c).attr('checked', 'checked');
					}
				});
			}
			adi_ih.last_ind = cur_ind;
			adi_ih.check_selected();
		});

		adjq('.adi_delete_selected').click(function(){
			ch = true;
			adjq('.adi_nc_selectable').each(function(){
				if(this.checked == true)
				{
					ch = true;
				}
			});
			if(ch == true)
			{
				if(confirm(adi.phrases['adi_acknowledgement_message_before_delete_inv']))
				{
					adjq('.adi_nc_invites_table_out').css('opacity', '0.7');
					adjq.ajax({
						type: 'POST',
						url: adi_ih.getDataURL(),
						data: adjq('.adi_nc_paginate_form').serialize()+'&adi_do=delete_invites',
						success: function (data)
						{
							var st = adi_ih.last_show_type, pg = adi_ih.last_page_no;
							adi_ih.last_page_no = 0; adi_ih.last_show_type = '';
							adi_ih.paginate({show_type: st, page_no: pg});
						},
						error : function(d) { 
							adjq('.adi_nc_invites_table_out').css('opacity', '1');
						},
						dataType: 'html'
					});
				}
			}
		});

		
	},
	fcheck: false,
	echeck: false,
	check_selected: function(){
		this.fcheck = false;
		this.echeck = false;
		adjq('.adi_nc_selectable').each(function(){
			if(this.checked == true) 
			{
				adi_ih.fcheck = this.checked || adi_ih.fcheck;
			}
			else {
				adi_ih.echeck = true;
			}
		});
		if(adi_ih.fcheck) {
			adjq('.adi_delete_selected').css('visibility', 'visible');
		}
		else {
			adjq('.adi_delete_selected').css('visibility', 'hidden');
		}
		if(adi_ih.echeck)
		{
			adjq('.adi_ih_select_all').removeAttr('checked');
		}
		else {
			adjq('.adi_ih_select_all').attr('checked','checked');
		}
	}
};


function adi_open_oauth(sk)
{
	var tp = adi.services[sk][0][2];
	if(tp == 2)
	{
		adipps.tp2.show_in(adipps.lg.isopen?1:0, sk);
		return false;
	}
	var w = 750, h = 492;
	var pageURL = adi.ajaxUrl('adi_do=oauth_login&adi_s=start&adi_service='+sk);
	var title = adi.phrases['adi_oauth_service_submit_btn_label'] || '';
	title = title.replace(/\[service_name\]/g, adi.services[sk][0][1]);
	var left = (adjq(window).width()/2)-(w/2);
	var top = (adjq(window).height()/2)-(h/2);
	left += window.screenLeft;
	top += window.screenTop + 70;
	var wob = window.open(pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
	return wob;
}


function adi_use_adcust_scrollbar()
{
	if(adjq.adcust != undefined)
		return true;
	(function (a) {
		a.adcust = a.adcust || {};
		a.adcust.scrollbar = {
			options: {
				axis: "y",
				wheel: 40,
				scroll: true,
				lockscroll: true,
				size: "auto",
				sizethumb: "auto",
				invertscroll: false
			}
		};
		a.fn.adcustscrollbar = function (d) {
			var c = a.extend({}, a.adcust.scrollbar.options, d);
			this.each(function () {
				a(this).data("tsb", new b(a(this), c))
			});
			return this
		};
		a.fn.adcustscrollbar_update = function (c) {
			return a(this).data("tsb").update(c)
		};

		function b(q, g) {
			var k = this,
				t = q,
				jj = {
					obj: a(".adcust_viewport", q)
				}, h = {
					obj: a(".adcust_overview", q)
				}, d = {
					obj: a(".adcust_scrollbar", q)
				}, m = {
					obj: a(".adcust_track", d.obj)
				}, p = {
					obj: a(".adcust_thumb", d.obj)
				}, l = g.axis === "x",
				n = l ? "left" : "top",
				v = l ? "Width" : "Height",
				r = 0,
				y = {
					start: 0,
					now: 0
				}, o = {}, e = "ontouchstart" in document.documentElement;

			function c() {
				k.update();
				s();
				return k
			}
			this.update = function(z)
			{
				jj[g.axis] = jj.obj[0]["offset" + v];
				h[g.axis] = h.obj[0]["scroll" + v];
				h.ratio = jj[g.axis] / h[g.axis];
				if(h.ratio >= 1)
				{
					d.obj.removeClass('adcust_disable').addClass('adcust_disable');
				}
				else
				{
					d.obj.removeClass('adcust_disable')
				}
				m[g.axis] = g.size === "auto" ? jj[g.axis] : g.size;
				p[g.axis] = Math.min(m[g.axis], Math.max(0, (g.sizethumb === "auto" ? (m[g.axis] * h.ratio) : g.sizethumb)));
				d.ratio = g.sizethumb === "auto" ? (h[g.axis] / m[g.axis]) : (h[g.axis] - jj[g.axis]) / (m[g.axis] - p[g.axis]);
				r = (z === "relative" && h.ratio <= 1) ? Math.min((h[g.axis] - jj[g.axis]), Math.max(0, r)) : 0;
				r = (z === "bottom" && h.ratio <= 1) ? (h[g.axis] - jj[g.axis]) : isNaN(parseInt(z, 10)) ? r : parseInt(z, 10);
				w()
			};

			function w() {
				var z = v.toLowerCase();
				p.obj.css(n, r / d.ratio);
				h.obj.css(n, -r);
				o.start = p.obj.offset()[n];
				d.obj.css(z, m[g.axis]);
				m.obj.css(z, m[g.axis]);
				p.obj.css(z, p[g.axis])
			}

			function s() {
				if (!e) {
					p.obj.bind("mousedown", i);
					m.obj.bind("mouseup", u)
				} else {
					jj.obj[0].ontouchstart = function (z) {
						if (1 === z.touches.length) {
							i(z.touches[0]);
							z.stopPropagation()
						}
					}
				} if (g.scroll && window.addEventListener) {
					t[0].addEventListener("DOMMouseScroll", x, false);
					t[0].addEventListener("mousewheel", x, false);
					t[0].addEventListener("MozMousePixelScroll", function (z) {
						z.preventDefault()
					}, false)
				} else {
					if (g.scroll) {
						t[0].onmousewheel = x
					}
				}
			}

			function i(A) {
				a("body").addClass("adcust_noSelect");
				var z = parseInt(p.obj.css(n), 10);
				o.start = l ? A.pageX : A.pageY;
				y.start = z == "auto" ? 0 : z;
				if (!e) {
					a(document).bind("mousemove", u);
					a(document).bind("mouseup", f);
					p.obj.bind("mouseup", f)
				} else {
					document.ontouchmove = function (B) {
						B.preventDefault();
						u(B.touches[0])
					};
					document.ontouchend = f
				}
			}

			function x(B) {
				if (h.ratio < 1) {
					var A = B || window.event,
						z = A.wheelDelta ? A.wheelDelta / 120 : -A.detail / 3;
					r -= z * g.wheel;
					r = Math.min((h[g.axis] - jj[g.axis]), Math.max(0, r));
					p.obj.css(n, r / d.ratio);
					h.obj.css(n, -r);
					if (g.lockscroll || (r !== (h[g.axis] - jj[g.axis]) && r !== 0)) {
						A = a.event.fix(A);
						A.preventDefault()
					}
				}
			}

			function u(z) {
				if (h.ratio < 1) {
					if (g.invertscroll && e) {
						y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + (o.start - (l ? z.pageX : z.pageY)))))
					} else {
						y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + ((l ? z.pageX : z.pageY) - o.start))))
					}
					r = y.now * d.ratio;
					h.obj.css(n, -r);
					p.obj.css(n, y.now)
				}
			}

			function f() {
				a("body").removeClass("adcust_noSelect");
				a(document).unbind("mousemove", u);
				a(document).unbind("mouseup", f);
				p.obj.unbind("mouseup", f);
				document.ontouchmove = document.ontouchend = null
			}
			return c()
		}
	}(adjq));
}



adi_parse_cf_resp = function(cd){
	adipps.lg.parse_cf_resp();
	adi.eval(cd);
};


/*
Events List :

	global_init                    : After AdiInviter Pro javscript library initializes.
	login_form_load                : After Login form is loaded.
	contact_file_instructions_load : Contacts file intstructions.
	invite_sender_form_loaded      : After Invite Sender form loaded.
	friend_adder_form_loaded       : Friend Adder form loaded
	contacts_list_displayed        : After Contacts list is displayed
	sender_details_form_loaded     : After Sender details form is loaded.
	final_message_displayed        : After final message is displayed.
	recaptcha_form_loaded          : After reCaptcha form is loaded.
	importer_service_set           : After importing service is selected.
	invitation_preview_load        : After Invitation Preview
	invite_history_loaded          : After Invite History is displayed.
	topic_redirect_loaded          : After topic redirect popup is displayed.
	contacts_dispatched            : After contacts list is dispatched to the target DOM element.

*/