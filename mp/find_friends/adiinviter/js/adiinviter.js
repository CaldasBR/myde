if(typeof adjq == 'undefined' && typeof jQuery != 'undefined'){ var adjq=jQuery; }

var adi={},adipps={},adintrs={},adiinp={},adipop={},adiconts={};
adi=(function(j,a,pp,nt,acp){
	// Popup Wrapper
	var p = {
		isopen:0,state:0,openq:0,mskgr:1, bkp:1,
		id:'',cont:null,zind:20,
		postLoad:null,preShow:null,postShow:null,preHide:null,postHide:null,
		rt:null,cont:null,
		phtml:'', ntr:'', ntrf:null,dhtml:'',
		uact:'',uact_dt:'text',dt:{},
		hoc:0,show_def:0,
		init:function(){this._init();},
		_init: function(){
			var me=this,ht='';
			if(me.state == 0)
			{
				if(me.bkp==0) ht=a.wbphtml;
				else ht=a.pphtml;
				ht=ht.replace(/\[ADI_POPUP_ID\]/ig, me.id);
				j('body').prepend(ht);
				me.rt=j('#'+me.id);
				me.cont=j('.adi_nc_container', me.rt);
				me.rt.css('z-index', a.zs + me.zind + (me.mskgr==2?150:0));
				me.state=1;
				if(me.hoc==1) {
					me.rt.click(function(e){
						if(j(e.target).parents('.adi_nc_container').size() == 0)
						{
							me.hide();
						}
					});
				}
				me.ldata();
			}
		},
		ldata: function(){
			var m=this;
			if(m.state==1){
				if(m.uact!=''){
					m.state=2;
					if(m.openq==1){ m.show(); m.openq=0;}
					j.ajax({
						type: 'POST',
						url: a.ajaxUrl(m.uact),
						data: a.jpd(m.dt),
						success: function(data) {
							m.sdata(data);
						},
						error : function(d) {},
						dataType: m.uact_dt,
					});
				}
				else if(m.dhtml!='') {
					m.state=2;
					if(m.openq==1){ m.show(); m.openq=0;}
					m.sdata(m.dhtml);
				}
				else if(m.show_def == 1){
					m.state=2;
					if(m.openq==1){ m.show(); m.openq=0;}
					m.dhtml = m.cont.html();
					m.sdata(m.dhtml);
				}
			}
		},
		reset:function(){
			this.sdata(this.phtml);
		},
		sdata:function(data){
			var m=this;
			m.cont.html(data);
			m.phtml=data;
			m.state=3;
			m.postLoad && m.postLoad();
			if(m.ntr != '' && a.ntrs[m.ntr]){
				m.ntrf = a.ntrs[m.ntr];
				m.ntri = 'popup';
				m.ntrf();
			}
			j('.adi_popup_ok', m.rt).click(function(e){
				m.hide();
				e.preventDefault(); return false;
			});
			if(m.openq==1){ m.show(); m.openq=0; }
		},
		show:function(){ this._show(); },
		_show: function(){
			var m=this;
			if(m.state<2)
			{
				m.openq=1;
				m.init();
				return 1;
			}
			else if(m.isopen == 0)
			{
				if(m.preShow)
				{
					if(m.preShow() === false)
					{
						return false;
					}
				}
				m.isopen=1;
				a.msk[m.mskgr] && a.msk[m.mskgr].show();
				m.rt.show();
				m.postShow && m.postShow();
			}
		},
		hide: function(){
			var m=this;
			if(m.isopen==1)
			{
				m.preHide && m.preHide();
				a.msk[m.mskgr] && a.msk[m.mskgr].hide();
				m.rt.hide();
				m.isopen=0;
				m.postHide && m.postHide();
			}
		}
	};

	// Mask Wrapper
	var ms = {
		m:null, isopen:0, c:0,
		show:function(){
			if(this.isopen==0) { this.m.show();this.isopen=1; }
			this.c++;
		},
		hide:function(){
			if(this.isopen==1 && --this.c == 0) {this.m.hide(); this.isopen=0;}
		}
	};

	// Root Variable : Main Variable
	a = {
		zs:100, aurl:'', rurl:'', dfhtml:'',pphtml:'', wbphtml:'', ncols:4, mw:null, mc:5, regurl:'', ihurl:'',
		cflt:1048576,cllt:50000,orie:'ltr',
		cns:'', eval:function(cd){eval(cd);},
		msk:{}, ntrs:{},
		fel:{},
		pele: function(){
			return this.fel;
		},
		jpd: function(pd){
			var pel = this.pele();
			if(typeof pd == 'object') {
				
				if(adjq(pel).length > 0) {
					for(var i in pel) {
						if(typeof i == 'string') {
							pd[i] = pel[i];
						}
					}
				}
				var dl='',sp='';
				for(var i in pd){
					if(typeof i == 'string') {
						dl += sp+i+'='+encodeURIComponent(pd[i]);
						sp='&';
					}
				}
				return dl;
			}
			else if(typeof pd == 'string') {
				var dl = '',sp='';
				for(var i in pel) {
					if(typeof i == 'string') {
						dl += sp+i+'='+pel[i];sp='&';
					}
				}
				return pd + (dl==''?'':'&'+dl);
			}
			else {
				return pd;
			}
		},
		vWidth : function() { return j(document).width(); },
		vHeight : function() { return j(document).height(); },
		indexOf: function(m, s) {
			if(typeof s != 'string') { for(var i=0 ; i<m.length ; i++) { if(m[i] == s) return i; } }
			else {
				for(var i=0 ; i<m.length ; i++) {
					if(m[i] == s[0]) { var st=true; for(var z=0 ; z<s.length ; z++){ if(m[i+z] != s[z]) st=false; } if((z==s.length) && st) return i; }
				}
			}
			return -1;
		},
		parseName: function(n) {
			if(n.replace != undefined)
				return n.replace(/&amp;/g,'&').replace(/&quot;/g,'"');
			return n;
		},
		trim: function(m){ return (typeof m == 'string') ? m.replace(/^\s+|\s+$/g, '') : ''; },
		ajaxUrl: function(act){ return a.aurl+'adiinviter_ajax.php?adi_scc='+a.scc+'&'+act; },
		dataUrl: function(act){ return a.aurl+'adi_invite_history.php?'+act; },
		newPopup: function(id,vn,zi,mg,ec){
			pp[vn] = j.extend({},p,{id:id,zind:zi,mskgr:mg},ec);
		},
		init:function(){
			j('body').prepend('<div class="adi_all_pp_out"></div><div class="adi_stickBorders adiinviter_mask" id="adi_mask_1"></div><div class="adi_stickBorders adiinviter_mask" id="adi_mask_2"></div>');
			this.msk[1] = j.extend({},ms,{m:j('#adi_mask_1')});
			this.msk[1].m.css('z-index', a.zs);
			this.msk[2] = j.extend({},ms,{m:j('#adi_mask_2')});
			this.msk[2].m.css('z-index', a.zs+150);
		},
		allocate_intr:function(ntr,rt,dt){
			nt[ntr] || (nt[ntr] = {});
			nt[ntr].rt = rt;
			nt[ntr].ntrf = a.ntrs[ntr];
			dt && (nt[ntr] = j.extend(nt[ntr],dt));
			nt[ntr].ntri='inpage';
			nt[ntr].ntrf();
		},

		// Inpage Error Display
		show_ip_err:function(msg){
			if(msg!='')
			{
				var p = j('.adi_nc_inpage_panel_outer');
				j('.adi_err_outer', p).html(msg).slideDown(100);
			}
		},
		hide_ip_err:function(){
			j('.adi_err_outer').hide();
		},

		// Popup Error Display
		show_pp_err:function(msg){
			if(msg!='')
			{
				var p = j('#adi_main_panel_popup');
				j('.adi_err_outer', p).html(msg).slideDown(100);
			}
		},
		hide_pp_err:function(){
			j('.adi_err_outer').hide();
		},

		register_event: function(_nm,_fn){
			var m=this;
			if(m.global_notifiers[_nm] == undefined) { 
				m.global_notifiers[_nm] = {};
				m.global_notifiers[_nm].c = 0; 
			}
			m.global_notifiers[_nm]['fn_'+m.global_notifiers[_nm].c] = _fn;
			m.global_notifiers[_nm].c++;
		},
		global_notifiers: {},
		gi_called: false,
		call_event: function(_nm, dt) {
			dt = dt || {};
			var m=this;
			if(m.global_notifiers[_nm] != undefined && m.global_notifiers[_nm].c > 0)
			{
				for(var i=0;i<m.global_notifiers[_nm].c;i++){
					m.global_notifiers[_nm]['fn_'+i](dt);
				}
			}
			if(_nm=='global_init'){ this.gi_called=true; }
		}
	};


	a.ntrs={
		'tr':function(){
			var m=this;
			m.sr = j('.adi_redirect_timeout', m.rt).html();
			m.tfun = setInterval(function(){
				m.sr--;
				j('.adi_redirect_timeout', m.rt).html(m.sr);
				if(m.sr <= 0) {
					window.location.href = m.red_url;
				}
			},1000);
			adi.call_event('topic_redirect_loaded', {sel: m.rt});
		}
	};


	// Topic Redirect
	a.newPopup('adi_topic_redirect_popup', 'tr', 30, 1, {
		ntr:'tr',hoc:0,
		sr:0,red_url:'',tfun:null,
		uact:'adi_do=topic_redirect',
		postHide:function(){
			if(this.tfun != null)
			{
				clearTimeout(this.tfun);
			}
		}
	});

	return a;

})(adjq,adi,adipps,adintrs,adiconts);


adjq(document).ready(function(){
	adi.init();
	adi.call_event('global_init');
});

var adi_parse_cf_resp = function(cd){
	adi.eval(cd);
};

var adi_oauth_resp = {
	respond: function(msg)
	{
		if(typeof msg == 'string' && msg != '1')
		{
			if(adipps.lg && adipps.lg.isopen == true)
			{
				adi.show_pp_err(msg);
			}
			else
			{
				adi.show_ip_err(msg);
			}
		}
		else if(parseInt(msg) == 1)
		{
			if(adipps.lg && adipps.lg.isopen == true)
			{
				adipps.lg.oauth_submit = true;
				var frm = adjq('.adi_nc_oauth_submit_form',adipps.lg.rt);
				adi_sending_effect(frm.get(0),0);
				adjq('.adi_oauth_submit',frm).val(1);
				adjq('.adi_nc_oauth_submit_form',adipps.lg.rt).submit();
			}
			else
			{
				var frm = adjq('.adi_nc_oauth_submit_form',adintrs.lgip.rt);
				adi_sending_effect(frm.get(0),0);
				adjq('.adi_oauth_submit',frm).val(1);
				adjq('.adi_nc_oauth_submit_form', adintrs.lgip.rt).submit();
			}
		}
	}
};

function adi_captcha_init()
{
	if(typeof grecaptcha != 'undefined') {
		adi_on_captcha_load();
	}
	else {
		adjq.getScript("https://www.google.com/recaptcha/api.js?onload=adi_on_captcha_load&render=explicit");
	}
}

var adi_on_captcha_load = function()
{
	var ct = adjq("#adi_ip_captcha_cont");
	if(ct.size() > 0)
	{
		grecaptcha.render(ct.get(0), {
			"sitekey" : adjq("#adi_ip_captcha_cont").attr('data-sitekey')
		});
		ct.removeAttr('id');
	}
};
var adi_open_popup_model = function(){
	return true;
};

