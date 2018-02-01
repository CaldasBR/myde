var last_element;

$(document).ready(function()
{
	slide("#sliding-navigation", 5, 0, 150, .8);
	adi.reportLoadedSettings($('.left_menu_current').attr('data'));

	$('.adi_top_menu_link').click(function(){
		if($('.adi_top_menu_dropdown').css('display') != 'none')
		{
			$('.adi_top_menu_dropdown').hide();
		}
		else 
		{
			$('.adi_top_menu_dropdown').show();
		}
	});

	// Left menu navigation
	last_element = $('.left_menu_current');
	$('.left_menu_li').click(function(){
		if(adi.loadSettings($(this).attr('data')))
		{
			var curr = $(this);
			if(!curr.hasClass('left_menu_current'))
			{
				curr.toggleClass('left_menu_current');
				last_element.removeClass('left_menu_current');
				last_element = curr;
			}
		}
		return false;
	});

	// Global Click function
	$(document).click(function(e){
		var et = $(e.target);
		if(et.parents('.adi_invite_limit_cont').size() == 0 && !et.hasClass('.adi_invite_limit_cont'))
		{
			// $('.adi_invite_limit_out').hide();
			$('.adi_invite_limit_link_active').removeClass('adi_invite_limit_link_active');
		}
		
		var t=$(e.target).closest('.adi_services_outer');
		if(t.length)
		{
			if(t.hasClass('adi_on_services_out')) {
				$('.adi_off_services_out .service_clicked').removeClass('service_clicked');
			}
			else 
			{
				$('.adi_on_services_out .service_clicked').removeClass('service_clicked');
			}
		}
		else
		{
			$('.service_clicked').removeClass('service_clicked');
		}

		// DropDown Code
		if(et.parents('.adi_select_plugin_out').size() == 0 && !et.hasClass('.adi_select_plugin_out'))
		{
			adi_db_info.reset_select_plgs();
		}
		adi_notif.hide_notif();
	});

	// Global Keyup event
	$(document).keyup(function(e) {
		if(e.keyCode == 27)
		{
			adi.hideMsg();
			new_lang.hide();
			adtmpl_editor.hide_preview();

			$('#modal_new_phrase').hide();
		}
	});

});

// var last_element;
function slide(navigation_id, pad_out, pad_in, time, multiplier)
{
	// creates the target paths
	var list_elements = navigation_id + " li.sliding-element";
	var link_elements = list_elements + " a";
	last_element = $(navigation_id + ' .current');
	
	// initiates the timer used for the sliding animation
	var timer = 0;
	
	// creates the slide animation for all list elements 
	$(list_elements).each(function(i)
	{
		// margin left = - ([width of element] + [total vertical padding of element])
		$(this).css("margin-left","-180px");
		// updates timer
		timer = (timer*multiplier + time);
		//$(this).animate({ marginLeft: "0" }, timer);
		//$(this).animate({ marginLeft: "15px" }, timer);
		$(this).animate({ marginLeft: "0" }, timer);
	});

	// creates the hover-slide effect for all link elements 		
	$(link_elements).each(function(i)
	{
		$(this).hover(
		function()
		{
			$(this).stop(true).animate({ paddingLeft: pad_out }, 150);
		},		
		function()
		{
			$(this).stop(true).animate({ paddingLeft: pad_in }, 150);
		})
		.click(function(){
			if(adi.loadSettings($(this.parentNode).attr('data')))
			{
				var curr = $(this.parentNode);
				if(!curr.hasClass('current')) 
				{
					curr.toggleClass('current');
					last_element.removeClass('current');
					last_element = curr;
				}
			}
			return false;
		});
	});
}


var adi = {
	adi_debugger_mode: 0,
	frm_elements_ob: {},
	frm_elements: '',
	generate_url: function(fn){
		return fn;
	},
	prepare_elements: function(){
		return this.frm_elements_ob;
	},
	join_post_data: function(pd){
		var pel = this.prepare_elements()
		if(typeof pd == 'object') {
			return $.extend({}, pd, pel);
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
	currentSettings : 'global',
	isChanged : false,
	switch_changed : function(nm,ioff){
		if(nm == 'redirection_on_off') {
			(ioff == 1) ? $('.build_url_outer_div').slideDown() : $('.build_url_outer_div').slideUp();
		}
	},
	settingsChanged : function() {
		//this.isChanged = true;
	},
	settingsRestored : function() {
		//this.isChanged = false;
	},
	trim: function(m){
		if(typeof m != 'string') {
			m = '';
		}
		return m.replace(/^\s+|\s+$/g, '');
	},
	indexS : function(m, s) {
		// indexS(haystack, needle)
		if(typeof s != 'string') {
			for(var i=0 ; i<m.length ; i++) {
				if(m[i] == s) {
					return i;
				}
			}
		}
		else {
			for(var i=0 ; i<m.length ; i++) {
				if(m[i] == s[0]) {
					var st=true;
					for(var z=0 ; z<s.length ; z++){
						if(m[i+z] != s[z])
							st=false;
					}
					if((z==s.length) && st)
						return i;
				}
			}
		}
		return -1;
	},

	guest_unique_str: 'cggkdpxrvnazrbdn',
	verifyResponse : function(d) {
		if(typeof d == 'object')
			return true;
		if(this.indexS(d, this.guest_unique_str) == -1) // checking for the unique string in Login page HTML
			return true;
		else {
			adi.showMsg('<font color="red"><b>Admin session timed out.</b></font><br>redirecting to login page..');
			setTimeout(function(){
				var v = window.location.href.replace(/^\s+|\s+$|#+$/g, '')
				if(adi.indexS(window.location.href, '?') == -1) {
					window.location.replace(v+'?errno=2');
				}
				else {
					window.location.replace(v+'&errno=2');
				}
			},1000);
			return false;
		}
	},

	registerLanguageControls: function(){
		$('.adi_add_new_language').click(function(){
			new_lang.show();
		});
		adi.setAdiRadios();

		$('.adi_delete_language').click(function(){
			var lid = $('.adi_language_option').val();
			$('.adi_langauge_opt_response').html('');
			if(lid == 'en')
			{
				$('.adi_langauge_opt_response').html('<font color="red">English language cannot be deleted.</font>');
				return false;
			}
			if(lid != undefined && lid != '')
			{
				if(confirm("Do your really want to delete selected language?\n\nNote: All modifications will be lost permanantly."))
				{
					var URL = adi.generate_url('adi_post.php');
					adi.showMsg('Deleting language..');
					$.ajax({
						type: 'POST',
						url: URL,
						data: adi.join_post_data({adi_delete_language:lid}),
						success: function (data) {
							if(adi.verifyResponse(data))
							{
								adi.loadSettings(adi.currentSettings);
							}
							else {
								adi.hideMsg();
							}
						},
						error : function(d) { adi.reportError(d); },
						dataType: 'html',
					});
				}
			}
		});
	},

	current_phrase_page: 1,
	registerPhrasesControls: function(){
		$('.section_header_language').hide();

		// register inputs
		$('.add_new_phrase').click(function(){
			adi.showNewPhraseForm();
		});
		
		$('.adi_lang_show_all_phrases').click(function(){
			$(this).css('visibility', 'hidden');
			adi.search_query = '';
			adi.search_lang  = '';
			adi.search_type  = '';
			adi.loadPhrases({phrase_group_id: 0});
		});
		$('.phrase_group_id').change(function(e){
			if($(this).val() != 0) {
				$('.adi_lang_show_all_phrases').css('visibility', 'visible');
			}
			else {
				$('.adi_lang_show_all_phrases').css('visibility', 'hidden');
			}
			if($(this).val() != '-')
			{
				adi.search_query = '';
				adi.search_lang  = '';
				adi.search_type  = '';
				adi.loadPhrases({phrase_group_id: $(this).val()});
			}
		});
		$('.phrase_edit').click(function(e){
			return adi.editPhrase($(this).attr('rel'), $(this).attr('data'));
		});
		$('.phrase_remove').click(function(e){
			return adi.removePhrase(this, $(this).attr('data'));
		});

		// Phrases Pagination
		$('.paginate_node', $('.phrases_pagination')).click(function(e){
			if($(this).hasClass('paginate_page_active'))
			{
				adi.loadPhrases({phrases_page_no: $(this).attr('data')});
			}
			return false;
		});

		$('.settings_list').submit(function(){
			return adi.saveSettings(this);
		});
		$('.settings_list').removeClass('settings_list');

		var p = $('.section_header_language');
		if($('.sect_head_opt_checked', p).attr('data') == 'sect_search_in_phrases')
		{
			$('.adi_lang_back').attr('data', 'search');
		}
		else
		{
			$('.adi_lang_back').attr('data', 'language');
		}
		$('.adi_lang_back').click(function(){
			$('.section_header_language').show();
			if($(this).attr('data') == 'search') {
				$('.sect_search_in_phrases').show();
				$('.sect_language_manager').hide();
			}
			else {
				$('.sect_search_in_phrases').hide();
				$('.sect_language_manager').show();
				$('.adi_lang_options_out').show();
			}
			$('.adi_phrases_outer').html('');
		});
	},
	clear_sif_Form: function(){
		$('.adi_sif_query').val('');
		$('.adi_sif_lang').val('*');
		$('.adi_sif_type').val('*');
	},
	submit_sif_Form: function(f){
		if(adi.trim($('.adi_sif_query').val()) != '')
		{
			adi.search_query = $('.adi_sif_query').val();
			adi.search_lang  = $('.adi_sif_lang').val();
			adi.search_type  = $('.adi_sif_type').val();
			$('.phrase_group_id').val('-');
			$('.adi_lang_show_all_phrases').css('visibility', 'visible');
			adi.loadPhrases({phrase_group_id:0, phrases_page_no: 1});
		}
	},
	search_query: '',
	search_lang: '*',
	search_type: '3',
	lang_id: 'en',
	last_phrase_config: {},
	loadPhrases: function(pd){
		if(pd.phrase_group_id == undefined){
			pd.phrase_group_id = $('.phrase_group_id').val();
		}
		if(pd.search_query == undefined){ 
			pd.search_query = adi.search_query;
		}
		if(pd.search_lang == undefined){ 
			pd.search_lang  = adi.search_lang;
		}
		if(pd.search_type == undefined){ 
			pd.search_type  = adi.search_type;
		}
		if(pd.lang_id == undefined){
			pd.lang_id  = adi.lang_id;
		}
		var URL = adi.generate_url('adi_lang.php');
		this.showMsg('Loading Phrases..');
		this.last_phrase_config = pd;
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data(pd),
			success: function (data) {
				adi.hideMsg();
				if(adi.verifyResponse(data))
				{
					$('.sect_search_in_phrases').hide();
					$('.sect_language_manager').hide();
					$('.adi_lang_options_out').hide();
					$('.adi_phrases_outer').html(data);
					adi.registerPhrasesControls();
					adi.clear_sif_Form();
				}
			},
			error : function(d) { adi.reportError(d); },
			dataType: 'html',
		});
	},
	editPhrase: function(lang, ph_name){
		this.showMsg('Loading contents..');
		var URL = adi.generate_url('adi_lang.php');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data({phrase_name: ph_name, lang_id: lang, "adi_do": 'edit_phrase'}),
			success: function (data) {
				adi.hideMsg();
				if(adi.verifyResponse(data))
				{
					$('.adi_edit_phrase_outer').html(data);
					adi.registerEditPhraseControls();
				}
			},
			error : function(d) { adi.reportError(d); },
			dataType: 'html',
		});
	},
	registerEditPhraseControls: function(){
		$('.adi_phrases_outer').hide();
		$('.adi_edit_phrase_outer').show();

		$('#adi_edit_phrase_form').submit(function(){
			adi.showMsg('Updating phrase contents..');
			var URL = adi.generate_url('adi_post.php');
			$('#ediPhrase_response').html('');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data($(this).serialize()),
				success: function (data) {
					adi.hideMsg();
					adi.verifyResponse(data);
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'script',
			});
			return false;
		});

		$('.adi_edit_phrase_cancel').click(function(){
			adi.hideEditPhraseForm();
		});
	},
	hideEditPhraseForm: function(){
		$('.adi_phrases_outer').show();
		$('.adi_edit_phrase_outer').hide();
	},
	removePhrase : function(me, ph_name)
	{
		if(ph_name != '')
		{
			if(confirm("Click yes to remove this phrase from all AdiInviter Pro Language packs.\n\n Note: The action can not be undone."))
			{
				adi.showMsg('Deleting phrase..');
				var URL = adi.generate_url('adi_post.php');
				$('#ediPhrase_response').html('');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({remove_phrase: ph_name}),
					success: function (data) {
						adi.hideMsg();
						if(adi.verifyResponse(data))
						{
							$(me).parents('.lang_out').remove();
						}
					},
					error : function(d) { adi.hideMsg(); adi.reportError(d); },
					dataType: 'script',
				});
			}
		}
	},
	submitNewPhraseForm: function(f){
		$(".new_phrase_form_response").html('');
		var URL = adi.generate_url('adi_post.php');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data($(f).serialize()),
			success: function (data) {
				if(adi.verifyResponse(data)) {
					adi.loadPhrases({});
				}
			},
			error : function(d) { adi.reportError(d); },
			dataType: 'script',
		});
	},
	showNewPhraseForm : function(msg){
		$('#modal_mask').show();
		$('#modal_new_phrase').show();
		$('.new_phrase_group_id').val($('.phrase_group_id').val());
		$(".new_phrase_form_response").html('');
	},
	hideNewPhraseForm: function(){
		$('#modal_mask').hide();
		$('#modal_new_phrase').hide();
		$('.phrase_group_id').val($('.new_phrase_group_id').val());
		this.clearNewPhraseForm();
	},
	clearNewPhraseForm: function(){
		$('.new_phrase_group_id').val('');
		$('.new_phrase_varname').val('');
		$('.new_phrase_text').val('');
	},
	saveDBDetails : function(frm){
		if(frm != undefined)
		{
			var URL = adi.generate_url('adi_post.php');
			this.showMsg('Updating settings..');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data($(frm).serialize() + '&gname=' + this.currentSettings),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						adi_notif.show_success();
						adi.settingsRestored();
					}
				},
				error : function(d) { adi.hideMsg(); adi_notif.show_failure(); },
				dataType: 'script'
			});
		}
	},
	tabClicked: function(cls){
		if(cls == 'sect_search_in_phrases')
		{
			$('.adi_phrases_outer').html('').show();
			$('.adi_edit_phrase_outer').html('').show();
			adi.clear_sif_Form();
		}
		else if(cls == 'sect_language_manager')
		{
			$('.adi_phrases_outer').html('').show();
			$('.adi_edit_phrase_outer').html('').show();
			$('.adi_lang_options_out').show();
			// $('.adi_new_lang_form_out').hide();
		}

	},
	reportLoadedSettings : function(sett_name)
	{
		$('.ad_teditor').hide(); $('.adi_settings_root').show();
		if($('.sect_head_opt_checked').size() > 0)
		{
			$('.'+$('.sect_head_opt_checked').attr('data')).show();
		}

		$('.sect_head_opt').click(function(){
			if(!$(this).hasClass('sect_head_opt_checked'))
			{
				var p = $(this).parents('.section_header_cont');

				$('.sect_head_opt_checked', p).each(function(){
					$('.'+$(this).attr('data')).hide();
				});
				var attr = $(this).attr('data');
				adi.tabClicked(attr);
				$('.'+$(this).attr('data')).show();

				$('.sect_head_opt_checked', p).removeClass('sect_head_opt_checked').addClass('sect_head_opt_unchecked');
				$(this).removeClass('sect_head_opt_unchecked').addClass('sect_head_opt_checked');
			}
		});

		$('.section_header').hide(); $('.sect_out_div').hide();
		$('.sect_head_opt_checked').addClass('sect_head_opt_unchecked').removeClass('sect_head_opt_checked');
		$('.sect_head_default').addClass('sect_head_opt_checked').removeClass('sect_head_opt_unchecked');
		$('.sect_head_default').each(function(){
			$('.'+$(this).attr('data')).addClass('sect_head_opt_checked').removeClass('sect_head_opt_unchecked').show();
		});

		var regControlsFlag = true;
		switch(sett_name)
		{
			case 'db_info' :
				adi_db_info.register_controls();
			case 'dashboard':
				$('.graph_selector_opt').click(function(){
					if(!$(this).hasClass('graph_selector_checked'))
					{
						$(this).addClass('graph_selector_checked').removeClass('graph_selector_unchecked');
					}
					else
					{
						$(this).addClass('graph_selector_unchecked').removeClass('graph_selector_checked');
					}
					init_charts();
				});

				$('.graph_date_form').submit(function(){
					var fs = $('#graph_first_date').html(), ls = $('#graph_last_date').html();
					if(adi.graph_first_date != fs || adi.graph_last_date != ls)
					{
						adi.showMsg('Loading..');
						var URL = adi.generate_url('adi_get.php');
						$.ajax({
							type: 'POST',
							url: URL,
							data: adi.join_post_data({"adi_graph": 1, gname: 'dashboard', screen_size: adi_scr_mode, total_vars: adi_graph.get_total_vals(), first_date: fs , last_date: ls}),
							success: function (data) {
								adi.hideMsg();
								// init_charts();
							},
							error : function(d) { adi.hideMsg(); },
							dataType: 'script'
						});
					}
					return false;
				});


			break;
			case 'global' :
				$('.section_header_settings').show();
			break;

			case 'invitation' :
				adtmpl_editor.register_events();
			break;

			case 'permissions' :
				$('.section_header_permissions').show();
				adi_perms.settingsLoaded();
			break;

			case 'oauth' :
			break;

			case 'services' :
				$('.section_header_services').show();
				adi_services.setServicesForm();

				$('.adi_oauth_def_text').focus(function(e){
					if($(this).hasClass('adi_key_input_disabled'))
					{
						$(this).blur();
						$('.adi_aol_onoff_tooltip').show();
						e.preventDefault();
						return false;
					}
					var grd = $(this).attr('groupid');
					$('.'+grd).each(function(i,c){
						if(adi.trim($(c).val()) == $(c).attr('defaulttext'))
						{
							$(c).val('').removeClass('adi_oauth_def_text_format');
						}
					});
				}).blur(function(){
					var grd = $(this).attr('groupid');
					$('.'+grd).each(function(i,c){
						if(adi.trim($(c).val()) == '')
						{
							$(c).val($(c).attr('defaulttext')).addClass('adi_oauth_def_text_format');
						}
					});
				});
				adi.setAdiRadios();

				$('.adi_aol_onoff_tooltip').click(function(){
					$(this).fadeOut();
				});

			break;

			case 'campaign' :
				adi_campaign.registerControls();
			break;

			case 'plugins' :
				adi_plugins.registerPluginsList();
			break;

			case 'language' :
				$('#adi_reload_settings').click(function() {
					return adi.loadSettings(adi.currentSettings);
				});
				adi.registerLanguageControls();
				adi.registerPhrasesControls();
				regControlsFlag = false;

				adi.setSelectPlugin();

				adi_langs.set_langs_list();
				$('.section_header_language').show();
				break;

			case 'themes':
				adi_themes.set_themes_list();

				$('.adi_themes_list_outer').html($('.adi_themes_list_updater').html());
				$('.adi_themes_list_updater').html('');
			break;

			case 'updates' :
				$('.txt_def_box').focusin(function(e) {
					if($(this).val() == 'Email Address') {
						$(this).val('').removeClass('txt_with_def');
					}
				}).focusout(function(e) {
					if($(this).val() == '') {
						$(this).val('Email Address').addClass('txt_with_def');
					}
				});;
			break;

			default: break;
		}
		$('.settings_list').submit(function(){
			return adi.saveSettings(this);
		});
		$('.settings_list').removeClass('settings_list');
		if(regControlsFlag) {
			adi.registerControls();
		}
	},
	register_widget_paginate: function(){
		$('.adi_widget_paginate').click(function(){
			// var fs = $('#graph_first_date').html(), ls = $('#graph_last_date').html();
			var pn = $(this).attr('data');
			if(pn != undefined)
			{
				var dt = 'gname=dashboard&get_widget=1&w_page='+pn;
				adi.showMsg('Loading List..');
				var URL = adi.generate_url('adi_get.php');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data(dt),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							adi.hideMsg();
							$('.adi_widget_outer_div').html(data);
						}
					},
					error : function(d) { adi.hideMsg(); },
					dataType: 'html'
				});
			}
			return false;
		});
	},
	graph_first_date:'', 
	graph_last_date:'',
	registerImgCheckbox: function(){
		$('.adi_img_checkbox').click(function(){
			var m = $(this).children('input');
			if(m.attr('value') == '1')
			{	
				m.attr('value', '0');
				$(this).addClass('adi_opt_no').removeClass('adi_opt_yes');
			}
			else {
				m.attr('value', '1');
				$(this).addClass('adi_opt_yes').removeClass('adi_opt_no');
			}
		});
		$('.adi_img_checkbox').removeClass('adi_img_checkbox');
	},
	reportError : function(err){
		adi.hideMsg();
	},
	loadSettings : function(postdata, msg){
		hideTooltip();
		adi_notif.hide_notif();
		if(this.isChanged == true) 
		{
			if(confirm('Do you really want to discard the changes?') == false) {
				return false;
			}
		}
		if(typeof postdata == "string") {
			var sett_name = postdata;
			postdata = {gname: sett_name};
		}
		else {
			var sett_name = postdata['gname'];
		}
		if(msg == undefined) msg = 'Loading Settings..';
		this.showMsg(msg);

		var URL = adi.generate_url('adi_get.php');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data(postdata),
			success: function (data) {
				if(adi.verifyResponse(data))
				{
					$('#settings_panel').html(data);
					adi.settingsRestored();
					adi.currentSettings = sett_name;
					adi.hideMsg();
					window.scrollTo(0,0);
					adi.reportLoadedSettings(sett_name);
				}
			},
			error : function(d) { adi.hideMsg(); adi.reportError(d); },
			dataType: 'text'
		});
		return true;
	},
	saveSettings: function(f) 
	{
		var serv_frm = $(f).hasClass('adi_services_form');
		if(serv_frm)
		{
			var v = $('.on_services_order').val();
			v = v.replace(/\s/g,'');
			if(v == '')
			{
				// $('.services_form_response').html('<font color="red">Atleast 1 service must be turned ON.</font>');
				adi_notif.show_failure('Atleast 1 contacts importer service must be turned ON');
				return false;
			}
		}
		var db_frm   = $(f).hasClass('adi_db_conn_details');
		var lang_frm = $(f).hasClass('adi_new_lang_form');
		var cs_form = $(f).hasClass('adi_update_campaign_form');
		var tedit_form = $(f).hasClass('tedit_save_form');
		if(lang_frm)
		{
			if( typeof $('.adi_new_lang_name').val() != "string")
			{
				$('.new_lang_form_response').html('<font color="red"><i>No langauge selected.</i></font>');
				return false;
			}
		}
		var URL = adi.generate_url('adi_post.php');
		this.showMsg('Updating settings..');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data($(f).serialize() + '&gname=' + this.currentSettings),
			success: function (data) {
				if(adi.verifyResponse(data))
				{
					if(serv_frm)
					{
						$('.adi_on_services_ul .service_off').removeClass('service_off').addClass('service_on');
						$('.adi_off_services_ul .service_on').removeClass('service_on').addClass('service_off');
					}
					if(lang_frm)
					{
						new_lang.hide();
						window.location.reload();
					}
					if(tedit_form)
					{
						if(adtmpl_editor.cupdate)
						{
							var cd = $('.tedit_source_en').val();
							if(cd != '') {
								adtmpl_editor.hide();
								cd = adi_replace_markups(cd, adtmpl_editor.bbcodes, 'user_mode');
								adi_add_to_iframe(adtmpl_editor.cupdate, cd);
							}
						}
					}
					if(cs_form)
					{
						var ele = $('.adi_sharing_onoff_switch');
						if(ele.size() > 0)
						{
							var dt = ele.attr('data');
							if(ele.val() == 1)
							{
								$('.'+dt).removeClass('css_inactive').addClass('css_active');
							}
							else
							{
								$('.'+dt).removeClass('css_active').addClass('css_inactive');
							}
						}
					}
					if(db_frm)
					{
						adi.hideMsg();
					}
					else 
					{
						adi.hideMsg();
						adi_notif.show_success();
					}
					adi.settingsRestored();
				}
			},
			error : function(d) { adi.hideMsg(); adi_notif.show_failure(); },
			dataType: 'script'
		});
		return false;
	},
	registerControls : function() {
		/* Reload settings image */
		$('#adi_reload_settings').click(function() {
			return adi.loadSettings(adi.currentSettings);
		});
		if($('.adi_nc_select_plugin').size() > 0)
		{
			adi.setSelectPlugin();
		}


		$('.adi_limited_chars_only').keyup(function(e){
			var tp = $(this).attr('onlychars');
			if(tp != undefined && tp != '')
			{
				if(tp == 'nums')
				{
					var v =$(this).val();
					$(this).val(v.replace(/[^\d]/g, ''));
				}
				if(tp == 'mixtext')
				{
					var v =$(this).val();
					$(this).val(v.replace(/[^a-z0-9_]/ig, ''));
				}
			}
		});

		adi.setAdiRadios();

		/* Register File Upload controls */

		/* All input elements to report onChangedEvent */
		$('input').change(function(){ // Input elements
			adi.settingsChanged();
		});
		$('textarea').change(function(){ // textarea elements
			adi.settingsChanged();
		});
		$('select').change(function(){ // textarea elements
			adi.settingsChanged();
		});
		this.setAdiSwitch();
		/* Save settings */
		$('#adi_save_settings').click(function(){

		});

	}, 
	setSelectPlugin: function(){
		// DropDown Code
		$('.adi_nc_select_plugin').each(function(){
			// $(this).prepend('<input type="hidden" class="adi_select_input" name="'+$(this).attr('name')+'" value="">');
			var c=$('.adi_select_selected', this);
			var d=$('.adi_select_default', this);
			var f=$('.adi_select_option', this);
			var dt=$('.adi_default_text', this).html(),t=1;
			if(d.size() > 0) {
				adi_db_info.set_input_val(c, d.attr('data'), d.html());
				t = d.attr('type');
			}
			else {
				adi_db_info.set_input_val(c, '', dt);
				t = $('.adi_default_text', this).attr('type');
			}
			if(t != null) {
				$('.adi_select_selected', this).addClass('adi_select_option_type'+t);
			}
			$(this).click(function(e){
				var o = $(e.target);
				if(o.hasClass('adi_select_option'))
				{
					var c = $('.adi_select_selected', this);
					adi_db_info.set_input_val(c, o.attr('data'), o.html());
					c.removeClass('adi_select_option_type2').removeClass('adi_select_option_type3');
					if(o.hasClass('adi_select_option_type2'))
					{
						c.addClass('adi_select_option_type2');
					}
					else if(o.hasClass('adi_select_option_type3'))
					{
						c.addClass('adi_select_option_type3');
					}
				}
				
				if($(this).hasClass('adi_select_opened') && (o.hasClass('adi_select_selected') || o.hasClass('adi_select_option')))
				{
					$(this).removeClass('adi_select_opened');
					$('.adi_select_options_out', this).hide();
				}
				else if(o.hasClass('adi_select_selected'))
				{
					adi_db_info.reset_select_plgs();
					adi_db_info.reset_search_results($(this));
					$(this).addClass('adi_select_opened');
					$('.adi_options_search_val', this).val('');
					$('.adi_select_options_out', this).show();
					$('.adi_select_options_div', this).tinyscrollbar_update(0);
					$('.adi_options_search_val', this).focus();
					adi_db_info.select_plugin_opened(this);

					var ff = $(this).offset();
					var nn = (ff.top+$(this).height()+10)-$('#modal_mask').height();
					if($('body').scrollTop() < nn)
					{
						$('body').scrollTop(nn);
					}
				}
			});

			$('.adi_options_search_val').keyup(function(){
				adi_db_info.options_search(this); 
			});

			var ii=$('.adi_select_input', this);
			if(ii.hasClass('adi_usergroup_map_table_name') != '')
			{
				adi_db_info.cur_ug_mapping_table = ii.val();
			}
			if(ii.hasClass('adi_avatar_table_name') != '')
			{
				adi_db_info.cur_avatar_mapping_table = ii.val();
			}

			if(ii.hasClass('adi_content_table_name') != '')
			{
				adi_db_info.cur_content_table = ii.val();
			}

			$('.adi_select_options_div', this).tinyscrollbar();
			$('.adi_select_options_out_temp', this).removeClass('adi_select_options_out_temp');
			$(this).removeClass('adi_nc_select_plugin');
		});
		if($('.adi_usergroup_map_table_name').val() != '')
		{
			adi_db_info.cur_ug_mapping_table = $('.adi_usergroup_map_table_name').val();
		}
		if($('.adi_avatar_table_name').val() != '')
		{
			adi_db_info.cur_avatar_mapping_table = $('.adi_avatar_table_name').val();
		}
		if($('.adi_content_table_name').val() != '')
		{
			adi_db_info.cur_content_table = $('.adi_content_table_name').val();
		}
	},
	setAdiRadios: function(){
		// Register Radio Buttons
		$('.radio_buttons').each(function(i){
			var vl = $(this).find('.radio_btn_current').attr('data');
			if($('input', this).size() == 0)
			{
				if(vl != undefined) {
					$(this).append('<input type="hidden" name="' + $(this).attr('name') + '" value="' + vl + '">');
				}
				else {
					$(this).append('<input type="hidden" name="' + $(this).attr('name') + '">');
				}
			}
			$(this).click(function(e) {
				if($(e.target).hasClass('radio_btn'))
				{
					$(this).find('.radio_btn_current').removeClass('radio_btn_current').addClass('radio_btn');
					$(e.target).removeClass('radio_btn').addClass('radio_btn_current');
					var vl = $(e.target).attr('data');	
					$(this).find('input').attr('value', vl);
					adi.radioButton_clicked(this, $(this).find('input').attr('name'), vl);
					adi.settingsChanged();
				}
			});
		});
	},
	radioButton_clicked:function(btn, name, vl){

	},
	setAdiSwitch: function(){
		/* On/Off css switch */
		$(".adi_switch_on").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-disable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.switch_val',parent).attr('value', $(this).attr('data'));
			adi.switch_changed($('.switch_val',parent).attr('data-name'), 1);
			adi.settingsChanged();
		});
		$(".adi_switch_off").click(function(){
			var parent = $(this).parents('.switch');
			$('.cb-enable',parent).removeClass('selected');
			$(this).addClass('selected');
			$('.switch_val',parent).attr('value', $(this).attr('data'));
			adi.switch_changed($('.switch_val',parent).attr('data-name'), 0);
			adi.settingsChanged();
		});
		$(".adi_switch_on").removeClass('adi_switch_on');
		$(".adi_switch_off").removeClass('adi_switch_off');

	},
	/* Show modal box */
	showMsg : function(msg){
		if(msg == undefined || msg == '') {
			msg = 'processing request..';
		}
		$('#modal_mask').show();
		$('.body_table_outer').css('opacity', '0.9');
		$('#modal_message').show();
		$('#modal_msg_txt').html(msg);
	},
	hideMsg : function() {
		$('.body_table_outer').css('opacity', '1');
		$('#modal_mask').hide();
		$('#modal_message').hide();
	},

	/* Notifications functions */
	showSuccess : function(msg){
		if(msg == undefined || msg == ''){
			msg = 'All settings have been updated successfully.';
		}
		$('#adi_nf_done_msg').html(msg);
		$('#adi_nf_done').show();
	},
};

var new_lang = {
	show: function(){
		$('.sect_head_opt_checked').addClass('sect_head_opt_unchecked').removeClass('sect_head_opt_checked');
		$('.sect_head_default').addClass('sect_head_opt_checked').removeClass('sect_head_opt_unchecked');
		$('.sect_head_default').each(function(){
			$('.'+$(this).attr('data')).addClass('sect_head_opt_checked').removeClass('sect_head_opt_unchecked').show();
		});
		$('.import_lang_form_response').html('');
		$('.new_lang_form_response').html('');
		$('.sect_create_lang').show();
		$('.sect_import_lang').hide();
		$('#modal_mask').show();
		$('#new_language').show();
	},
	hide: function(){
		$('#modal_mask').hide();
		$('#new_language').hide();
	},
};


function update_iframe_size(id,w,h)
{
	var m = $('#'+id);
	m.width(w+(h>550?25:0));
	m.height(h);
	if(m.height() < h) {
		m.width(w+25);
	}
}
function adi_add_to_iframe(id, html)
{
	var ifrm = document.getElementById(id),bd;
	var m = $(ifrm);
	m.width(500);
	m.height(150);
	ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : ( (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument);
	ifrm.document.open();
	// ifrm.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/><script type="text/javascript" src="adi_js/jquery.min.js"></script></head><body style="margin:0px;padding:0px;min-width:500px;min-height:50px;width:auto;height:auto;font-family:verdana;font-size:13px;"> '+html+' <script type="text/javascript"> window.parent.update_iframe_size("'+id+'", $(document).width(), $(document).height()); </script></body></html>');
	ifrm.document.write(html+'<style type="text/css"> body { margin:0px;padding:0px;min-width:500px;min-height:50px;width:auto;height:auto;font-family:verdana;font-size:13px;background-color:#FFF; } </style><script type="text/javascript" src="adi_js/jquery.min.js"></script><script type="text/javascript"> $("a").click(function(e){ return e.preventDefault(); }); window.parent.update_iframe_size("'+id+'", Math.max($(document).width(), 600), $(document).height()); </script>');
	ifrm.document.close();
	var nn = ifrm.document.getElementsByTagName('body')[0];

	return ifrm;
}


var adi_perms = {
	page_no : 1,
	settingsLoaded: function(){
		this.page_no = 1;
		adi.registerImgCheckbox();
		$('#adi_ug_perms_form').submit(function(){
			$('#adi_ug_perms_resp').html('');
			$('#adi_new_cs_response').html('');
			adi.showMsg('Updating usergroup permissions..');
			var URL = adi.generate_url('adi_post.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data($(this).serialize()),
				success: function(data) 
				{
					if(adi.verifyResponse(data))
					{
						$('#adi_new_cs_response').html(data);
						adi_perms.update_dependencies();
						adi.hideMsg();
					}
				},
				error : function(d) { adi.reportError(d); },
				dataType: 'script'
			});
			return false;
		});

		this.register_invite_limit();

		this.setUserPermissionsList();
	},
	register_invite_limit: function(){
		$('.adi_invite_limit_dom').each(function(){
			var m = $(this);
			m.removeClass('adi_invite_limit_dom');

			$('.adi_invite_limit_link', m).mouseenter(function(){
				$('.adi_invite_limit_out').each(function(){
					var v = $('.txinput', this).val().replace(/[^0-9]+/g,'');
					if(v != '')
					{
						$(this).siblings('.adi_invite_limit_link').html(v);
						$('.adi_invite_limit_inp_hid', this).val(v);
					}
					$(this).hide();
				});
				var ot = $(this).siblings('.adi_invite_limit_out');
				ot.show();
				ot.children('.adi_invite_limit_inp').val('');
				ot.children('.adi_invite_limit_inp').focus();
			});
			
	  		$('.adi_invite_limit_cont', m).mouseleave(function(){
				var cc = $('.adi_invite_limit_cont_div', this);
				var v = $('.txinput', this).val().replace(/[^0-9]+/g,'');
				if(v != '')
				{
					$('.adi_invite_limit_link', this).html(v);
					$('.adi_invite_limit_inp_hid', this).val(v);
				}
				$('.adi_invite_limit_out', this).hide();
			});

			$('.set_invite_limit_unlimited', m).click(function(){
				$(this).siblings('.adi_invite_limit_inp').val('Unlimited');
				$(this).siblings('.adi_invite_limit_inp_hid').val('Unlimited');
				$('.adi_invite_limit_link', $(this).parents('.adi_invite_limit_cont')).html('Unlimited');
				$(this).parents('.adi_invite_limit_out').hide();
			});
		});
	},
	update_dependencies: function(){
		$('.adi_after_save').each(function(){
			if($(this).hasClass('adi_opt_no'))
			{
				var ch = '.'+$(this).attr('depid');
				if(ch != '')
				{
					ch = $(ch);
					$(ch).each(function(){
						var m = $(this).children('input');
						m.attr('value', '0');
						$(this).addClass('adi_opt_no').removeClass('adi_opt_yes');
					});
				}
			}
		});
	},
	setUserPermissionsList: function(){

	},
};



var adi_services = {
	hover_on_services: false,
	hover_off_services: false,
	max_num_of_cols: 8,
	last_clicked_obj: undefined,
	updateOrder: function(){
		var ondata  = $(".adi_on_services_ul .service_out").map(function() { return $(this).attr('data'); }).get();
		var offdata = $(".adi_off_services_ul .service_out").map(function() { return $(this).attr('data'); }).get();
		$('.on_services_order').val(ondata.join(','));
		$('.off_services_order').val(offdata.join(','));
	},
	setServicesForm: function(){

		$('.adi_on_services_out').hover(function() {
			adi_services.hover_on_services=true;
		}, function() {
			adi_services.hover_on_services=false;
		});

		$('.adi_off_services_out').hover(function() {
			adi_services.hover_off_services=true;
		}, function() {
			adi_services.hover_off_services=false;
		});

		adi_drag_fn.init('.adi_on_services_out, .adi_off_services_out');

		this.adjust_service_cont();

		$('.service_out').dblclick(function(){
			if($(this).parents('.adi_on_services_out').size() != 0)
			{
				$(this).attr('list_index', $('.adi_off_services_out').attr('list_index'));
				$(this).appendTo('.adi_off_services_ul');
				adi_services.updateOrder();
			}
			else if($(this).parents('.adi_off_services_out').size() != 0)
			{
				$(this).attr('list_index', $('.adi_on_services_out').attr('list_index'));
				$(this).appendTo('.adi_on_services_ul');
				adi_services.updateOrder();
			}
		});
	},
	adjust_service_cont: function(){
		var ms = 4;
		if(adi_scr_mode == 1024) { ms = 2; }
		if(adi_scr_mode == 1152) { ms = 3; }
		$('.adi_on_services_out').width(($('.service_out').width()+12) * 4);
		var off_wd = ($('.service_out').width()+12) * ms;

		$('.adi_off_services_out').css('min-width', off_wd+'px');
		$('.adi_off_services_out').css('max-width', (off_wd+18)+'px');
		
		$('.adi_serv_off_msg').width($('.adi_off_services_out').width());

		var hh = $('.adi_on_services_out').height();
		hh = (hh < 290) ? 290 : hh;
		$('.adi_on_services_out').css('min-height', '290px');
		$('.adi_off_services_out').css('min-height', hh+'px').css('max-height', '576px');
	},
};




var adi_campaign = {
	registerControls : function(){
		$('.section_header_campaigns').hide();
		this.registerContShareList();
		// Cancel campaign form
		$('.adi_new_cs_cancel').click(function(){
			adi_campaign.hideNewContShareForm();
		});
		// Submit new campaign form
		$('#adi_new_cs_form').submit(function(){
			var URL = adi.generate_url('adi_post.php');
			$('#adi_new_cs_response').html(''); $('#adi_new_cs_response_2').html('');
			adi.showMsg('Creating new campaign..');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data($(this).serialize()),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
					}
				},
				error : function(d) { adi.reportError(d); },
				dataType: 'script'
			});
			return false;
		});

		// 
	},
	hideNewContShareForm: function(){
		$('.adi_new_cs_form_outer').hide();
		$('.adi_campaign_list').show();
	},
	registerContShareList: function(){
		adi.setAdiSwitch();

		// Add new campaign
		$('.adi_show_new_cs_form').click(function(){
			$('#adi_new_cs_response').html(''); $('#adi_new_cs_response_2').html('');
			$('.adi_new_cs_form_outer').show();
			$('.adi_new_cs_cancel').show();
			$('.adi_campaign_list').hide();
		});
		$('.settings_list').submit(function(){
			return adi.saveSettings(this);
		});
		$('.settings_list').removeClass('settings_list');

		// Edit campaign
		$('.adi_edit_cs_btn').click(function(){
			$('#adi_new_cs_response_2').html('');
			var cs_id = $(this).attr('data');
			if(typeof parseInt(cs_id) == 'number')
			{
				var URL = adi.generate_url('adi_campaign.php');
				adi.showMsg('Loading campaign settings..');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({adi_get: 'campaign_settings', campaign_id: cs_id}),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							$('.adi_cs_settings_outer').html(data);
							$('.adi_cs_settings_outer').show();
							$('.adi_campaign_list').hide();
							adi_campaign.last_cs_id = cs_id;
							adi_campaign.setContentSettingsForm();
							adi.hideMsg();
							setTimeout(function(){
								adtmpl_editor.register_events();
							}, 100);
							adi.reportLoadedSettings('campaign_settings');
							$('.section_header_campaigns').show();
						}
					},
					error : function(d) { adi.reportError(d); },
					dataType: 'html'
				});
			}
		});

		//Delete Campaign
		$('.adi_delete_cs_btn').click(function(){
			$('#adi_new_cs_response_2').html('');
			var cs_id = $(this).attr('data');
			if(typeof parseInt(cs_id) == 'number')
			{
				if(confirm("Do you really want to delete this campaign?\n\nNote: This process can not be undone."))
				{
					var URL = adi.generate_url('adi_post.php');
					adi.showMsg('Removing campaign..');
					$.ajax({
						type: 'POST',
						url: URL,
						data: adi.join_post_data({remove_campaign: cs_id}),
						success: function (data) {
							if(adi.verifyResponse(data))
							{
								adi_campaign.checkIfCurrent(cs_id);
								adi.hideMsg();
							}
						},
						error : function(d) { adi.reportError(d); },
						dataType: 'script'
					});
				}
			}
			return false;
		});
	},
	last_cs_id: -1,
	checkIfCurrent: function(cs_id){
		if(this.last_cs_id == cs_id)
		{
			$('.adi_cs_settings_outer').html('');
		}
	},
	updateContShareList: function(){
		var URL = adi.generate_url('adi_campaign.php');
		adi.showMsg('Updating campaigns list..');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data({adi_get: 'campaigns_list'}),
			success: function (data) {
				if(adi.verifyResponse(data))
				{
					var msg = $('#adi_new_cs_response_2').html();
					$('.adi_campaign_list_data').html(data);
					$('#adi_new_cs_response_2').html(msg);
					adi.hideMsg();
					adi_campaign.registerContShareList();

					if($('.adi_delete_cs_btn').size() == 0)
					{
						$('.adi_new_cs_form_outer').show();
					}
				}
			},
			error : function(d) { adi.reportError(d); },
			dataType: 'html'
		});

	},
	setContentSettingsForm: function(){
		$('.settings_list').submit(function(){
			return adi.saveSettings(this);
		});
		$('.settings_list').removeClass('settings_list');
		$('.cs_cancel_edit_form').click(function(){
			$('.adi_cs_settings_outer').hide();
			$('.adi_campaign_list').show();
			$('.section_header_campaigns').hide();
			window.scrollTo(0,0);
		});
		$('.embed_switch').click(function(){
			var dt = $(this).attr('data')
			var p = $(this).parents('.embed_code_cont');
			if(dt != '')
			{
				$('.embed_code_out', p).hide();
				$('.'+dt, p).show();
				if(dt == 'php_embed_code') {
					$('.embed_code_head', p).html('PHP Code');
				}
				else if(dt == 'html_embed_code') {
					$('.embed_code_head', p).html('HTML Code');
				}
			}
		});
		adi.registerControls();
	}
};


var adi_notif = {
	def_success: 'All settings have been updated successfully.',
	def_failure: 'Something went wrong.',
	show_success: function(msg)
	{
		if(msg == undefined || typeof msg != 'string') {
			msg = this.def_success;
		}
		$('.adi_notif_success_txt').html(msg);
		$('.top_notif').show();
		$('.adi_notif_success').slideDown();
	},
	show_failure: function(msg)
	{
		if(msg == undefined || typeof msg != 'string') {
			msg = this.def_failure;
		}
		$('.adi_notif_failure_txt').html(msg);
		$('.top_notif').show();
		$('.adi_notif_failure').slideDown();
	},
	hide_notif: function(){
		$('.top_notif').fadeOut(100);
		$('.adi_notif').fadeOut(100);
	},
};


var adi_themes = {
	vars: {},
	set_themes_list: function(){

		$('.themes_onoff').click(function(){
			if(!$(this).hasClass('theme_checked'))
			{
				$('.theme_checked').each(function(){
					$(this).removeClass('theme_checked').addClass('theme_unchecked');
					$('input', this).removeAttr('checked');
				});
				$(this).removeClass('theme_unchecked').addClass('theme_checked');
				$('input', this).attr('checked', 'true');
			}
		});

		$('.theme_install').click(function(){
			var URL = adi.generate_url('adi_themes.php');
			adi.showMsg('Installing AdiInviter theme..');
			var tid = $(this).attr('data');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data({adi_do: 'theme_install', theme_id: tid}),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
					}
				},
				error : function(d) { adi.reportError(d); },
				dataType: 'script'
			});
		});

		$('.theme_remove').click(function(){
			if(confirm("This will permanantly remove all phrases associated whith this theme. Continue?\n\n Note: This process can not be undone."))
			{
				var URL = adi.generate_url('adi_themes.php');
				adi.showMsg('Installing AdiInviter theme..');
				var tid = $(this).attr('data');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({adi_do: 'theme_remove', theme_id: tid}),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							adi.hideMsg();
							adi.loadSettings(adi.currentSettings);
						}
					},
					error : function(d) { adi.reportError(d); },
					dataType: 'script'
				});
			}
		});

	},
};


var adi_langs = {
	vars: {},
	set_langs_list: function(){

		$('.themes_onoff').click(function(){
			$(!$(this).hasClass('theme_checked'))
			{
				$('.theme_checked').each(function(){
					$(this).removeClass('theme_checked').addClass('theme_unchecked');
					$('input', this).removeAttr('checked');
				});
				$(this).removeClass('theme_unchecked').addClass('theme_checked');
				$('input', this).attr('checked', 'true');
			}
		});

		$('.lang_setdefault').click(function(){
			var URL = adi.generate_url('adi_post.php');
			adi.showMsg('Loading stylevars..');
			var tid = $(this).attr('data');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('subsettings[global][language]='+tid),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						// adi.hideMsg();
						adi.showMsg('Upading list..');
						adi.loadSettings(adi.currentSettings);
					}
				},
				error : function(d) { adi.hideMsg(); },
				dataType: 'html'
			});
		});

		$('.lang_edit').click(function(){
			// var tid = $(this).attr('data');
			adi.search_query = '';
			adi.search_lang  = $(this).attr('data');
			adi.search_type  = '3';
			adi.loadPhrases({});
		});

		$('.lang_remove').click(function(){
			if(confirm("Do you really want to remove '"+($(this).attr('rel'))+"' Language Pack?\n\n Note: This process can not be undone."))
			{
				var URL = adi.generate_url('adi_post.php');
				adi.showMsg('Removing language pack..');
				var tid = $(this).attr('data');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({adi_delete_language: tid}),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							window.location.reload();
						}
					},
					error : function(d) { adi.reportError(d); },
					dataType: 'script'
				});
			}
		});

		$('.lang_export').click(function(){
			if(confirm("Click 'OK' to export '"+($(this).attr('rel'))+"' Language Pack xml.."))
			{
				var URL = adi.generate_url('adi_lang.php');
				var tid = $(this).attr('data');
				URL += "adi_act=export_lang&lang_id=" + tid;
				$('#back_channel').attr('src', URL);
			}
		});

		adi.setAdiSwitch();
	},
};


var adi_reset = {
	show: function(){
		$('#modal_mask').show();
		$('#reset_password_popup').show();
		$('.reset_password_processing').html('');
		return false;
	},
	hide: function(){
		$('#modal_mask').hide();
		$('#reset_password_popup').hide();
		$('.reset_password_processing').html('');
		return false;
	},
	submit_form: function(f){
		$('.reset_password_processing').html('');
		var np = $('.rp_new_password').val(), cnp = $('.rp_confirm_password').val(), cp = $('.rp_cur_password').val() ;

		if(np == cnp && np != '')
		{
			if(np.length < 6)
			{
				$('.reset_password_processing').html('<font color="red">Password must be atleast 6 characters long.</font>');
			}
			else
			{
				$('.reset_password_processing').html('<i>Setting new password..</i>');
				var URL = adi.generate_url('adi_post.php');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data($(f).serialize()),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
						}
					},
					error : function(d) { $('.reset_password_processing').html(''); },
					dataType: 'script'
				});
			}
		}
		else if(np == '' || cnp == '' || cp == '')
		{
			$('.reset_password_processing').html('<font color="red">Please fill all the fields.</font>');
		}
		else
		{
			$('.reset_password_processing').html('<font color="red">New password entered does not match.</font>');
		}
	}
};

var adi_db_info = {
	last_ug_out_status: undefined,
	last_fr_out_status: undefined,
	last_fl_out_status: undefined,
	register_controls: function(){
		$('.section_header_integration').show();

		$('.adi_clear_integration_form').submit(function(){
			if($(this).hasClass('adi_clr_user_system_form'))
			{
				adi_db_info.clear_user_details();
				adi_db_info.clear_usergroup_details();
				adi_db_info.clear_friends_details();
			}
			else if($(this).hasClass('adi_clr_usergroup_system_form'))
			{
				adi_db_info.clear_usergroup_details();
			}
			else if($(this).hasClass('adi_clr_friends_system_form'))
			{
				adi_db_info.clear_friends_details();
			}
			return adi.saveSettings(this);
		});
		
		$('.adi_load_user_table').click(function(){
			adi_db_info.adi_call_user_details();
		});

		$('.adi_load_usergroup_table').click(function(){
			adi_db_info.adi_call_usergroup_details();
		});

		$('.adi_load_friends_table').click(function(){
			adi_db_info.adi_call_friends_details();
		});

		$('.adi_load_followers_table').click(function(){
			adi_db_info.adi_call_followers_details();
		});

		$('.adi_db_details').submit(function(){
			$('#checkFriends_resp').html('');
			return adi_db_info.checkDBDetails(this, $(this).attr('data'));
		});

		$('#toggle_usergroup_mapping').click(function(){
			$('#toggle_usergroup_mapping_div').toggleClass('hid');
		});
		$('#toggle_avatar_table').click(function(){
			$('#toggle_avatar_table_div').toggleClass('hid');
		});

		$('.adi_user_system_toggle').click(function(){
			if($(this).attr('data') == 1)
			{
				$('.adi_user_system_details').show();
				$('.adi_clr_user_system_out').hide();
				if(adi_db_info.last_ug_out_status === false)
				{
					setTimeout(function(){
						$($('.adi_usergroup_system_toggle')[0]).click();
					},50);
				}
				if(adi_db_info.last_fr_out_status === false)
				{
					setTimeout(function(){
						$($('.adi_friends_system_toggle')[0]).click();
					},50);
				}
				if(adi_db_info.last_fl_out_status === false)
				{
					setTimeout(function(){
						$($('.adi_friends_system_toggle')[1]).click();
					},50);
				}
			}
			else
			{
				$('.adi_user_system_details').hide();
				$('.adi_clr_user_system_out').show();

				adi_db_info.last_ug_out_status = $('.adi_usergroup_system_details').css('display') == 'none';
				adi_db_info.last_fr_out_status = $('.adi_friends_system_details').css('display') == 'none';
				adi_db_info.last_fl_out_status = $('.adi_followers_system_details').css('display') == 'none';

				$($('.adi_usergroup_system_toggle')[1]).click();
				$($('.adi_friends_system_toggle')[2]).click();
			}
		});

		$('.adi_usergroup_system_toggle').click(function(){
			if($(this).attr('data') == 1 )
			{
				if($('.adi_user_system_toggle').siblings('input').val() == 0)
				{
					$('#checkUsergroup_resp').html('<font color="red">User integration is required.</font>');
					return false;
				} else $('#checkUsergroup_resp').html('');

				$('.adi_usergroup_system_details').show();
				$('.adi_clr_usergroup_system_out').hide();
			}
			else {
				$('.adi_usergroup_system_details').hide();
				$('.adi_clr_usergroup_system_out').show();
			}
		});

		$('.adi_friends_system_toggle').click(function(){
			if($(this).attr('data') == 1)
			{
				if($('.adi_user_system_toggle').siblings('input').val() == 0)
				{
					$('#checkFriends_resp').html('<font color="red">User integration is required.</font>');
					return false;
				} else $('#checkFriends_resp').html('');

				$('.adi_friends_system_details').show();
				$('.adi_followers_system_details').hide();
				$('.adi_clr_friends_system_out').hide();
			}
			else if($(this).attr('data') == 2)
			{
				if($('.adi_user_system_toggle').siblings('input').val() == 0)
				{
					$('#checkFriends_resp').html('<font color="red">User integration is required.</font>');
					return false;
				} else $('#checkFriends_resp').html('');

				$('.adi_friends_system_details').hide();
				$('.adi_followers_system_details').show();
				$('.adi_clr_friends_system_out').hide();
			}
			else {
				$('.adi_friends_system_details').hide();
				$('.adi_followers_system_details').hide();
				$('.adi_clr_friends_system_out').show();
			}
		});
	},
	select_plugin_opened: function(m) {
		var inp = $('.adi_select_input', m);
		if(inp.hasClass('adi_usergroupmapping_userid') || inp.hasClass('adi_usergroupmapping_usergroupid'))
		{
			this.adi_call_usergroup_map_details();
		}
		if(inp.hasClass('adi_avatar_mapping_userid_field') || inp.hasClass('adi_avatar_mapping_avatar_field'))
		{
			this.adi_call_avatar_map_details();
		}
		if(inp.hasClass('adi_content_id_field') || inp.hasClass('adi_content_body_field') || inp.hasClass('adi_content_title_field') || inp.hasClass('adi_category_id_field') || inp.hasClass('adi_content_alias_field'))
		{
			this.adi_call_content_table_details();
		}
	},
	set_input_val: function(p, val, lbl){
		if(!p.hasClass('adi_select_plugin'))
		{
			p = p.parents('.adi_select_plugin');
		}
		var s = $('.adi_select_selected', p);
		var inp = $('.adi_select_input', p);
		s.attr('data', val);
		s.html(lbl);
		inp.val(val);


		if(inp.hasClass('adi_username_field_name'))
		{
			var v = $('.adi_username_field_name').val();
			$('.adi_username_update_here').html((v!='' ? v : 'Username'));
		}

		if(inp.hasClass('adi_avatar_table_name'))
		{
			var v = $('.adi_avatar_table_name').val();
			$('.adi_avatar_table_update_here').html((v!='' ? v : 'Avatar'));
		}

		if(inp.hasClass('adi_usergroup_table_name'))
		{
			var v = $('.adi_usergroup_table_name').val();
			$('.adi_usergroup_table_update_here').html((v!='' ? v : 'Usergroup'));
		}

		if(inp.hasClass('adi_usergroup_map_table_name'))
		{
			var v = $('.adi_usergroup_map_table_name').val();
			$('.adi_usergroupmap_table_update_here').html((v!='' ? v : 'Usergroup'));
		}

		// Username fields
		if(inp.hasClass('adi_fullname_initial_val'))
		{
			if(inp.val() == 'adi_multi_columns')
			{
				$('.adi_fullname_secondary_out').slideDown();
			}
			else
			{
				$('.adi_fullname_secondary_out').slideUp();
			}
		}
		
		if(inp.hasClass('adi_fullname_initial_val') || inp.hasClass('adi_fullname_secondary_val1') || inp.hasClass('adi_fullname_secondary_val2'))
		{
			var v = $('.adi_fullname_initial_val').val();
			if(v == 'adi_multi_columns')
			{
				$('.adi_fullname_secondary_out').show();
				v=$('.adi_fullname_secondary_val1').val();
				if($('.adi_fullname_secondary_val2').val() != v)
				{
					if(v!= ''){ v+= ','; }
					v+=$('.adi_fullname_secondary_val2').val();
				}
				v= v.replace(/^[\s,]+|[\s,]+$/,'');
				if(v.match(/^[\s,]*$/) != null) {
					v = '';
				}
			}
			else
			{
				$('.adi_fullname_secondary_out').hide();
			}
			$('.adi_fullname_value_hid').val(v)
		}

		// Usergroup Mapping fields
		if(inp.hasClass('adi_usergroupdid_field'))
		{
			if(inp.val() == 'adi_diff_table')
			{
				$('.adi_usergroupid_secondary_out').slideDown();
			}
			else
			{
				$('.adi_usergroupid_secondary_out').slideUp();
			}
		}

		// user -> Usergroup mapping table
		if(inp.hasClass('adi_usergroup_map_table_name'))
		{
			if(inp.val() != adi_db_info.cur_ug_mapping_table && inp.val() != '')
			{
				var p = $('.adi_usergroupid_mapping_fields');
				$('.adi_select_plugin',p).each(function(){
					adi_db_info.set_input_val($(this), '', 'Select Attribute');
				});
			}
			if(inp.val() == '')
			{
				$('.adi_usergroupid_mapping_fields').slideUp();
			}
			else
			{
				$('.adi_usergroupid_mapping_fields').slideDown();
			}
		}

		// user -> Avatar mapping table.
		if(inp.hasClass('adi_avatar_table_name'))
		{
			if(inp.val() != adi_db_info.cur_avatar_mapping_table && inp.val() != '')
			{
				var p = $('.adi_avatar_table_fields');
				$('.adi_select_plugin',p).each(function(){
					adi_db_info.set_input_val($(this), '', 'Select Attribute');
				});
			}
			if(inp.val() == '')
			{
				$('.adi_avatar_table_fields').slideUp();
			}
			else
			{
				$('.adi_avatar_table_fields').slideDown();
			}
		}

		if(inp.hasClass('adi_content_table_name'))
		{
			if(inp.val() != adi_db_info.cur_content_table && inp.val() != '')
			{
				var p = $('.adi_content_table_fields');
				$('.adi_select_plugin',p).each(function(){
					adi_db_info.set_input_val($(this), '', 'Select Attribute');
					$('.adi_select_selected', this).removeClass('adi_select_option_type2');
				});
			}
			if(inp.val() == '')
			{
				$('.adi_content_table_fields').slideUp();
			}
			else
			{
				$('.adi_content_table_fields').slideDown();
			}
		}

		// Avatar Fields
		if(inp.hasClass('adi_avatar_field_cls'))
		{
			if(inp.val() == 'adi_diff_table')
			{
				$('.adi_avatar_secondary_out').slideDown();
			}
			else
			{
				$('.adi_avatar_secondary_out').slideUp();
				if(inp.val() == '')
				{
					var p = $('.adi_avatar_table_fields');
					$('.adi_select_plugin',p).each(function(){
						adi_db_info.set_input_val($(this), '', 'Select Attribute');
					});
				}
			}
		}

		// T_editor after choosing language
		if(inp.hasClass('teditor_source_lang'))
		{
			var trgt = $('.tedit_source_'+inp.val());
			if(trgt.length > 0)
			{
				$('.tedit_lang_name').html( adtmpl_editor.list[inp.val()] );
				$('.tedit_source_code').hide();
				trgt.show();
			}
		}
	},
	clear_user_details: function(){
		adi_db_info.set_input_val($('.adi_user_table_name'), '', 'Select user table');
		$('.adi_user_table_details_out').html('');
	},
	clear_usergroup_details: function(){
		adi_db_info.set_input_val($('.adi_usergroup_table_name'), '', 'Select usergroup table');
		$('.adi_usergroup_table_details_out').html('');
	},
	clear_friends_details: function(){
		$('.adi_friends_table_details_out').html('');
	},
	adi_call_user_details: function()
	{
		var ut_nm = $('.adi_user_table_name').val();
		if(ut_nm != '')
		{
			adi.showMsg('Fetching user table details..');
			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=user_table_details&user_table_name='+ut_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						$('.adi_user_table_details_out').html(data);
						// $('.adi_usergroup_html_code').html($('.adi_usergroup_html_code_source').html());
						adi.setSelectPlugin();
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},

	adi_call_usergroup_details: function()
	{
		var ut_nm = $('.adi_user_table_name').val();
		var ugt_nm = $('.adi_usergroup_table_name').val();
		if(ut_nm != '' && ugt_nm != '')
		{
			adi.showMsg('Fetching usergroup table details..');
			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=usergroup_table_details&user_table_name='+ut_nm+'&usergroup_table_name='+ugt_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						$('.adi_usergroup_table_details_out').html(data);
						adi.setSelectPlugin();
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},

	adi_call_friends_details: function()
	{
		var fs_nm = $('.adi_friends_table_name').val();
		if(fs_nm != '')
		{
			adi.showMsg('Fetching friends table details..');
			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=friends_table_details&friends_table_name='+fs_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						$('.adi_friends_table_details_out').html(data);
						adi.setSelectPlugin();
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},

	adi_call_followers_details: function()
	{
		var fs_nm = $('.adi_followers_table_name').val();
		if(fs_nm != '')
		{
			adi.showMsg('Fetching friends table details..');
			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=followers_table_details&followers_table_name='+fs_nm),
				success: function (data)
				{
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						$('.adi_followers_table_details_out').html(data);
						adi.setSelectPlugin();
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},

	cur_ug_mapping_table: '',
	cur_avatar_mapping_table: '',
	cur_content_table: '',

	adi_call_usergroup_map_details: function()
	{
		var ut_nm = $('.adi_usergroup_map_table_name').val();
		if(ut_nm != '' && ut_nm != this.cur_ug_mapping_table)
		{
			var p = $('.adi_usergroupid_mapping_fields');
			$('.adi_options_list_out', p).hide();
			$('.adi_loading_fields_list_msg', p).show();

			var p = $('.adi_usergroupid_mapping_fields');
			$('.adi_select_options_div', p).tinyscrollbar_update(0);

			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=get_fields_list&table_name='+ut_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi_db_info.cur_ug_mapping_table = ut_nm;
						var p = $('.adi_usergroupid_mapping_fields');
						$('.adi_options_list_out', p).html(data);
						$('.adi_options_list_out', p).show();
						$('.adi_loading_fields_list_msg', p).hide();
						$('.adi_select_options_div', p).tinyscrollbar_update(0);
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},
	adi_call_avatar_map_details: function()
	{
		var ut_nm = $('.adi_avatar_table_name').val();
		if(ut_nm != '' && ut_nm != this.cur_avatar_mapping_table)
		{
			var p = $('.adi_avatar_table_fields');
			$('.adi_options_list_out', p).hide();
			$('.adi_loading_fields_list_msg', p).show();
			$('.adi_select_options_div', p).tinyscrollbar_update(0);

			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=get_fields_list&table_name='+ut_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi_db_info.cur_avatar_mapping_table = ut_nm;
						var p = $('.adi_avatar_table_fields');
						$('.adi_options_list_out', p).html(data);
						$('.adi_options_list_out', p).show();
						$('.adi_loading_fields_list_msg', p).hide();
						$('.adi_select_options_div', p).tinyscrollbar_update(0);
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},
	adi_call_content_table_details: function()
	{
		var cs_nm = $('.adi_content_table_name').val();
		if(cs_nm != '' && cs_nm != this.cur_content_table)
		{
			var p = $('.adi_content_table_fields');
			$('.adi_options_list_out', p).hide();
			$('.adi_loading_fields_list_msg', p).show();
			$('.adi_select_options_div', p).tinyscrollbar_update(0);

			var URL = adi.generate_url('adi_db_info.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('get_code=get_fields_list&table_name='+cs_nm),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi_db_info.cur_content_table = cs_nm;
						var p = $('.adi_content_table_fields');
						$('.adi_options_list_out', p).html(data);
						$('.adi_options_list_out', p).show();
						$('.adi_loading_fields_list_msg', p).hide();
						$('.adi_select_options_div', p).tinyscrollbar_update(0);

						var p = $('.adi_category_id_field').parents('.adi_select_plugin');
						$('.adi_options_list_out', p).prepend('<div class="adi_select_option adi_select_option_type2" type="2" data="">Not Present</div>');

						var p = $('.adi_content_alias_field').parents('.adi_select_plugin');
						$('.adi_options_list_out', p).prepend('<div class="adi_select_option adi_select_option_type2" type="2" data="">Not Present</div>');

						var p = $('.adi_content_title_field').parents('.adi_select_plugin');
						$('.adi_options_list_out', p).prepend('<div class="adi_select_option adi_select_option_type2" type="2" data="">Not Present</div>');
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'text'
			});
		}
		else
		{

		}
	},
	checkDBDetails : function (frm, do_action) {
		// Pre-save Checks
		if(do_action == 'checkUserDetails')
		{
			if($('.adi_avatar_field_cls').val() == 'adi_diff_table')
			{
				$('.adi_avatar_field_cls').val('');
			}
		}
		if(do_action == 'checkUsergroupDetails')
		{
			if($('.adi_usergroupdid_field').val() == 'adi_diff_table')
			{
				$('.adi_usergroupdid_field').val('');
			}
		}

		// adi_fullname_initial_val [initial_drop_down]
		// adi_fullname_value_hid

		var URL = adi.generate_url('adi_ajax.php');
		adi.showMsg('Checking Database integration details..');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data($('.adi_db_details').serialize() + '&adi_do=' + do_action),
			success: function (data) {
				if(adi.verifyResponse(data))
				{
					$('#adi_save_conn_details').html('');
					$('#adi_save_user_details').html('');
					$('#adi_save_usergroups_details').html('');
					$('#adi_save_friends_details').html('');
					var m;
					if(do_action == 'checkConnDetails') { m = $('#adi_save_conn_details'); }
					if(do_action == 'checkUserDetails') { m = $('#adi_save_user_details'); }
					if(do_action == 'checkUsergroupDetails') { m = $('#adi_save_usergroups_details'); }
					if(do_action == 'checkFriendsDetails') { m = $('#adi_save_friends_details'); }
					if(data.length > 0)
					{
						var d=undefined;
						for(var i in data)
						{
							d = data[i];
							$('#'+d['resp_label']).html(d['error_msg']);
							if(data[i]['error_code'] == 0)
							{
								adi.saveDBDetails(frm);
								// adi.loadSettings(adi.currentSettings);
							}
							else
							{
								adi.hideMsg();
							}
						}
					}
				}
			},
			error : function(d) { adi.reportError(d); },
			dataType: 'json'
		});
		return false;
	},
	last_q : '',
	options_search: function(m)
	{
		var v = $(m).val().replace(/^\s|\s$/,'');
		if(v != '' && this.last_q != v)
		{
			var p =$(m).parents('.adi_select_options_out');
			pp = $('.adi_select_options_div', p);
			$('.adi_select_option', pp).show();
			$('.adi_search_results', p).html('');
			v=' '+v.replace(/_/g, ' ');
			$('.adi_select_option', pp).each(function(){
				var h = ' '+$(this).html().replace(/_/g, ' ');
				if(adi.indexS(h.toLowerCase(), v) != -1)
				{
					$('.adi_select_options_div', p).tinyscrollbar_update(0);
					$(this).hide();
					$('.adi_search_results', p).append('<div class="adi_select_option adi_search_result_option" data="'+$(this).attr('data')+'">'+$(this).html()+'</div>');
				}
			});
		}
		if(v == '')
		{
			var p =$(m).parents('.adi_select_options_out');
			adi_db_info.reset_search_results(p);
		}
	},

	reset_select_plgs: function()
	{
		$('.adi_select_opened').each(function(){
			$(this).removeClass('adi_select_opened');
			$('.adi_select_options_out', this).hide();
		});
	},
	reset_search_results: function(p)
	{
		$('.adi_search_results', p).html('');
		$('.adi_select_option', p).show();
	},
};




var adi_drag_fn = {
	init: function(selector){
		this.item_cords = {0:{}, 1:{}};
		this.reset_drag();
		$(selector).each(function(i, cont){
			$(cont).mousedown(adi_drag_fn.item_mousedown);
			$('.service_out',cont).attr('list_index', i);
			$(cont).attr('list_index', i);
		});
	},

	item_cords: {0:{}, 1:{}}, // {list_index: [top, left, height, width]}
	record_cords: function(){
		var me = adi_drag_fn;
		$('.adi_services_outer').each(function(ii,cc){
			var lid = $(cc).attr('list_index'), lkey, cnt=0;
			$('.service_out', cc).each(function(i,c){
				lkey = 'adi_ms_'+$(c).attr('data')+'_out';
				me.item_cords[lid][lkey] = [$(c).offset().top, $(c).offset().left,$(c).height(),$(c).width()];
				cnt++;
			});

			if(cnt > 0)
			{
				var litem = me.item_cords[lid][lkey];
				if(litem.length > 0)
				{
					var coff = $(cc).offset(), ioff=$('.'+lkey).offset();
					nitem = [litem[0], (litem[1]+litem[3]), litem[2]];
					nitem[3] = $(cc).width() - ((ioff.left+litem[3])-coff.left);
					me.item_cords[lid].adi_service_tail = nitem;
				}
			}
		});
	},

	md_loc: null,
	md_ob: null,
	md_last_ob: null,
	item_mousedown: function(e){
		var me = adi_drag_fn;
		me.md_ob = $(e.target).closest('.service_out');

		var item = $(e.target).closest('.service_out');
		if(item.hasClass('service_clicked'))
		{
			item.removeClass('service_clicked');
		}
		else
		{
			item.addClass('service_clicked');
		}
		if(me.dragStarted)
		{
			$('body').removeClass('adi_grabbed_cursor').addClass('adi_grabbed_cursor');
		}

		if(e.shiftKey && me.md_ob && me.md_last_ob)
		{
			if(me.md_ob.attr('list_index') == me.md_last_ob.attr('list_index'))
			{
				var find = me.md_last_ob.index(), sind = me.md_ob.index();
				if(Math.abs(find-sind)>1)
				{
					var sob = me.md_last_ob, ldt = me.md_ob.attr('data');
					if(find > sind)
					{
						sob = me.md_ob;
						ldt = me.md_last_ob.attr('data');
					}
					var cc = 0;
					while(1)
					{
						sob.removeClass('service_clicked').addClass('service_clicked');
						if(sob.attr('data') == ldt) break;
						sob = sob.next();
						if(sob.length==0) break;
					}
				}
			}
		}

		e=$.event.fix(e);
		if(me.md_ob.length > 0)
		{
			me.md_loc = [e.pageX, e.pageY];
			$(document).bind('mousemove', adi_drag_fn.doc_mousemove).bind('mouseup', adi_drag_fn.doc_mouseup);
		}
		e.preventDefault();
	},
	updateCarrier: false,
	doc_mousemove: function(e) {
		var me = adi_drag_fn;
		e = $.event.fix(e);
		var cPos = [e.pageX, e.pageY];
		if(me.md_loc != null)
		{
			if(Math.abs(me.md_loc[0]-cPos[0]) > 2 || Math.abs(me.md_loc[1]-cPos[1]) > 2)
			{
				if(!me.dragStarted)
				{
					me.doc_dragStart(cPos);
					$('.service_holder').show();
				}
			}
		}
		if(me.updateCarrier && me.md_ob)
		{
			$('.adi_drag_carrier').css({left: cPos[0]+10, top: cPos[1]+10});
			var ob = $(e.target);
			var isHolder = ob.closest('.service_holder').length > 0
			if(!isHolder)
			{
				var p = ob.closest('.adi_services_outer');
				var li = p.attr('list_index');
				var px=0,py=0,cords;
				var cob;
				if(p.length > 0 && me.item_cords[li] != undefined)
				{
					for(var i in me.item_cords[li])
					{
						if( (me.item_cords[li][i][1]<=cPos[0] && cPos[0] <= (me.item_cords[li][i][1]+me.item_cords[li][i][3]))
						&& (me.item_cords[li][i][0]<=cPos[1] && cPos[1] <= (me.item_cords[li][i][0]+me.item_cords[li][i][2])) )
						{
							if(i == 'adi_service_tail')
							{
								$('.service_holder', p).appendTo($('.adi_services_ul', p));
							}
							else
							{
								cob = $('.service_holder', p);
								$('.'+i, p).before(cob);
							}
						}
					}
				}
			}
		}
	},
	doc_dragStart: function(){
		var me = adi_drag_fn;
		if(!me.dragStarted)
		{
			me.md_ob.removeClass('service_clicked').addClass('service_clicked');
			me.dragStarted = true;
			
			// Lift selected Items
			if(me.md_ob)
			{
				var p = me.md_ob.closest('.adi_services_outer');
				// Add Service holders
				if(p.hasClass('adi_on_services_out'))
				{
					$('<li class="service_holder"><div>&nbsp;</div></li>').appendTo('.adi_off_services_ul');
				}
				else
				{
					$('<li class="service_holder"><div>&nbsp;</div></li>').appendTo('.adi_on_services_ul');
				}
				me.md_ob.before('<li class="service_holder" style="display:none;"><div>&nbsp;</div></li>');
				// me.md_ob.hide();
				var crr = $('.adi_drag_carrier');
				crr.show();
				var cnt = 0;
				$('.service_clicked', p).each(function(i,c){
					var srv = $(c).attr('data');
					$(c).before('<li class="service_alt service_place_hold_'+srv+'" style="display:none;"></li>');
					$(c).appendTo(crr);
					cnt++;
				});

				crr.width(Math.min(470, Math.min(4,cnt)*120));
				me.updateCarrier = true;

				me.record_cords();
			}
		}
	},
	doc_mouseup: function(e){
		var me = adi_drag_fn;
		if(me.dragStarted)
		{
			var trgt = $(e.target);
			var isHolder = trgt.closest('.service_holder').length > 0;
			if(isHolder)
			{
				trgt=trgt.closest('.service_holder');
			}
			else if(adi_services.hover_on_services || adi_services.hover_off_services)
			{
				isHolder = true;
				if(adi_services.hover_off_services) {
					trgt=$('.adi_off_services_out .service_holder');
				}
				else if(adi_services.hover_on_services) {
					trgt=$('.adi_on_services_out .service_holder');
				}
			}
			if(isHolder)
			{
				trgt=trgt.closest('.service_holder');
				// Move
				trgt.hide();
				var p = trgt.closest('.adi_services_outer'), lid = p.attr('list_index');
				var crr = $('.adi_drag_carrier');
				$('.service_out', crr).each(function(i,c){
					trgt.before(c);
					$(c).removeClass('service_clicked').attr('list_index', lid);
					srv = $(c).attr('data');
					$('.service_place_hold_'+srv).remove();
				});
			}
			else
			{
				// Restore
				var crr = $('.adi_drag_carrier'),srv;
				$('.service_out', crr).each(function(i,c){
					srv = $(c).attr('data');
					$('.service_place_hold_'+srv).before(c).remove();
					$(c).removeClass('service_clicked');
				});
			}

			$('.service_holder').remove();
			$('.service_out').show();
			$('body').removeClass('adi_grabbed_cursor');
		}
		$(document).unbind('mousemove', adi_drag_fn.doc_mousemove);
		$(document).unbind('mouseup', adi_drag_fn.doc_mouseup);
		me.reset_drag();
		adi_services.adjust_service_cont();
		adi_services.updateOrder();

	},
	dragStarted: false,
	reset_drag: function(){
		this.dragStarted=false;
		this.md_last_ob = this.md_ob;
		this.md_loc = null; 
		this.md_ob = null;
		this.updateCarrier = false;
	},
};



var adtmpl_editor = {
	list: {},
	cupdate: '',
	show_tabs_onexit: false,
	register_events: function(){
		$('.tmpl_open_teditor').click(function(){
			var sg_name = $(this).attr('data-sg'), sett_name = $(this).attr('data-sn'), sett_id = $(this).attr('data-sid'), code_update = $(this).attr('data-cupdate');
			adtmpl_editor.load_html(sg_name, sett_name, sett_id, 'en', code_update);
		});

		$('.template_preview').each(function(){
			var cd= $('.template_code_en', this).html(), ifid=$('.template_code_en', this).attr('data');
			cd=cd.replace(/^<!-- | -->$/g, '');
			cd = adi_replace_markups(cd, adtmpl_editor.bbcodes, 'user_mode');
			adi_add_to_iframe(ifid, cd);
		});
	},
	load_html: function(gnm, nm, sid, lng, cup){
		gnm = gnm || ''; nm = nm || ''; sid = sid || ''; lng = lng || ''; cup = cup || '';
		if(gnm != '' && nm != '' && lng != '')
		{
			adi.showMsg('Initializing Editor..');
			var URL = adi.generate_url('adi_tmpl_editor.php');
			$.ajax({
				type: 'POST', url: URL,
				data: adi.join_post_data('sett_id='+sid+'&sett_group_name='+gnm+'&setting_name='+nm+'&lang_id='+lng) ,
				success: function (data) {
					adi.hideMsg()
					if(adi.verifyResponse(data))
					{
						adtmpl_editor.show_data(data);
						adtmpl_editor.cupdate = cup;
					}
				},
				error : function(d) { adi.hideMsg() },
				dataType: 'html'
			});
		}
	},
	back_scrolltop: 0,
	show_data: function(data){

		$('.section_header_campaigns').hide();

		$('.ad_teditor').html(data).show();
		this.back_scrolltop = $('body').scrollTop();
		$('body').scrollTop(0);
		$('.adi_settings_root').hide();
		adi.setSelectPlugin();

		// Form
		$('.settings_list').submit(function(){
			return adi.saveSettings(this);
		});
		$('.settings_list').removeClass('settings_list');

		// Back Button
		$('.cancel_teditor').click(function(e){
			adtmpl_editor.hide();
		});

		// Invitation Preview Button
		var tpre = $('.show_tedit_preview');
		if(tpre.length > 0)
		{
			tpre.click(function(e) {
				var lng = $('.teditor_source_lang').val();
				var tsc = $('.tedit_source_'+lng);
				var cd = tsc.val();
				var md = $('.teditor_preview_mode').val()

				adtmpl_editor.show_preview(cd, md, lng)
			});
		}
	},
	hide: function(){
		$('.ad_teditor').hide();
		$('.adi_settings_root').show();
		$('body').scrollTop(this.back_scrolltop);
		if(this.show_tabs_onexit == true) {
			this.show_tabs_onexit = false;
			$('.section_header_campaigns').show();
		}
	},

	show_preview: function(cd, md, lng){
		$('#modal_mask').show();
		$('#tedit_show_preview').show();
		cd = adi_replace_markups(cd, adtmpl_editor.bbcodes, md);
		adi_add_to_iframe('tedit_preview_iframe', cd);
	},
	hide_preview: function(){
		$('#modal_mask').hide();
		$('#tedit_show_preview').hide();
	},
};




function adi_replace_markups(cd, mks, md)
{
	cd = cd || ''; mks = mks || {}; md = md || 'user_mode';
	if(cd == '') {
		return cd;
	}

	if(md=='user_mode') {
		cd = cd.replace(/\[\/?user_mode\]/gi, '');
	}
	else { 
		while( (i=adi.indexS(cd, '[user_mode]')) != -1)
		{
			ei=adi.indexS(cd, '[/user_mode]');
			cd = cd.substring(0,i) + cd.substring(ei+12);
		}
	}

	if(md=='guest_mode') {
		cd = cd.replace(/\[\/?guest_mode\]/gi, '');
	}
	else { 
		while((i=adi.indexS(cd, '[guest_mode]')) != -1)
		{
			ei=adi.indexS(cd, '[/guest_mode]');
			cd = cd.substring(0,i) + cd.substring(ei+13);
		}
	}

	var atnt = (mks['attach_note'] == undefined ? '' : mks['attach_note']);
	if(atnt !== '') {
			cd = cd.replace(/\[\/?attach_note_block\]/gi, '');
	}
	else { 
		while( (i=adi.indexS(cd, '[attach_note_block]')) != -1)
		{
			ei=adi.indexS(cd, '[/attach_note_block]');
			cd = cd.substring(0,i) + cd.substring(ei+20);
		}
	}

	for(var i in mks)
	{
		if(typeof mks[i] == 'string')
		{
			while(adi.indexS(cd, '['+i+']') != -1) 
			{
				cd = cd.replace('['+i+']', mks[i]);
			}
		}
	}

	return cd;
}



