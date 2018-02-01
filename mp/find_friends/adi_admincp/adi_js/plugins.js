
$('.adi_plugins_list').show();
$('.adi_editplugin_outer').show();
$('#adi_save_plugins_response').html();


var adi_plugins = {
	registerPluginsList: function(){
		adi.setAdiSwitch();
		$('.plugin_edit').click(function(){
			var nt_id = $(this).attr('data');
			var URL = adi.generate_url('adi_plugins.php');
			adi.showMsg('Loading plugin settings..');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data({adi_get: 'edit_plugin_form', plugin_id: nt_id}),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						$('.adi_editplugin_outer').html(data);
						$('.adi_plugins_list').hide();
						$('.adi_editplugin_outer').show();
						adi_plugins.registerEditForm();
						adi.hideMsg();
						adtmpl_editor.register_events();
					}
				},
				error : function(d) { adi.reportError(d); },
				dataType: 'text'
			});
		});


		$('.plugin_remove').click(function(){
			if(confirm("Do you really want to remove this plugin?\n\nNote: This process can not be undone."))
			{
				var nt_id = $(this).attr('data');
				var URL = adi.generate_url('adi_plugins.php');
				adi.showMsg('Deleting plugin..');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({remove_plugin: nt_id}),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							adi.loadSettings(adi.currentSettings);
							adi_notif.show_success('Plugin removed successfully.');
						}
					},
					error : function(d) { adi.reportError(d); },
					dataType: 'text'
				});
			}
			return false;
		});

		// Execute Plugin
		$('.plugin_execute').click(function(){
			if(confirm("Do you really want to execute this plugin?"))
			{
				var nt_id = $(this).attr('data');
				var URL = adi.generate_url('adi_plugins.php');
				adi.showMsg('Deleting plugin..');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data({execute_plugin: nt_id}),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							adi.loadSettings(adi.currentSettings);
							adi_notif.show_success('Plugin executed successfully.');
						}
					},
					error : function(d) { adi.reportError(d); },
					dataType: 'script'
				});
			}
			return false;
		});

		$('.plugin_install').click(function(){
			adi.showMsg('Installing plugin..');
			var URL = adi.generate_url('adi_plugins.php');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data('adi_do=install&data='+$(this).attr('data')),
				success: function(data)
				{
					if(adi.verifyResponse(data))
					{
						adi.loadSettings(adi.currentSettings);
						adi_notif.show_success('Plugin installed successfully.');
					}
				},
				error : function(d) { adi.hideMsg(); adi.reportError(d); },
				dataType: 'json'
			});
		});

		$('.adi_edit_plugin_form').submit(function(){
			var URL = adi.generate_url('adi_plugins.php');
			adi.showMsg('Updating plugin settings..');
			$('#adi_edit_plugin_response').html('');
			$.ajax({
				type: 'POST',
				url: URL,
				data: adi.join_post_data($(this).serialize()),
				success: function (data) {
					if(adi.verifyResponse(data))
					{
						adi.hideMsg();
						if(data == '')
						{
							$('.adi_plugins_list').show();
							$('.adi_editplugin_outer').hide();
						}
						else 
						{
							eval(data);
						}
					}
				},
				error : function(d) { adi.reportError(d); },
				dataType: 'text'
			});
			return false;
		});
	},
	registerEditForm: function(){
		adi.setAdiSwitch();
		adi.setAdiRadios();

		$('.adi_plugins_cancel').click(function(){
			$('.adi_plugins_list').show();
			$('.adi_editplugin_outer').hide();
		});

		$('.adi_relative_duration').click(function(){
			$('.adi_relative_duration_div').show();
			$('.adi_absolute_duration_div').hide();
		});

		$('.adi_absolute_duration').click(function(){
			$('.adi_relative_duration_div').hide();
			$('.adi_absolute_duration_div').show();
		});
	}
};