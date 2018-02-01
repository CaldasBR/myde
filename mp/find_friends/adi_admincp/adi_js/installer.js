var adi_inst = {
	adi_debugger_mode: 0,
	db_details_saved: 0,
	set_step_1: function(){
		$('.inst_input_form').submit(function(){
			var v = $('#adi_license_val').val();
			if(typeof v == "string")
			{
				v = v.replace(/^\s*|\s*$/g,'');
				if(v != "" && v.length == 42)
				{
					return true;
				}
			}
			$('.action_err_msg').html('<font color="red">Invalid License Key</font>');
			return false;
		});
	},
	set_step_2: function(){
		adi_inst.register_check_form();
	},
	register_check_form: function(){
		$('.inst_input_form').submit(function(){
			if(adi_inst.db_details_saved == 0)
			{
				var URL = adi.generate_url('adi_ajax.php');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data($('.inst_input_form').serialize() + '&adi_do='+ $(this).attr('data')),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							if(data.length > 0)
							{
								var d=undefined;
								for(var i in data)
								{
									$('.action_err_msg').html(data[i]['error_msg']);
									if(data[i]['error_code'] == 0)
									{
										adi_inst.db_details_saved = 1;
										$('.inst_input_form').submit();
									}
									else
									{
										adi_inst.db_details_saved = 0;
									}
								}
							}
						}
					},
					error : function(d) {  },
					dataType: 'json'
				});
				return false;
			}
			else if(adi_inst.db_details_saved == 1)
			{
				var URL = adi.generate_url('adi_post.php');
				$.ajax({
					type: 'POST',
					url: URL,
					data: adi.join_post_data($(this).serialize() + '&do=' + $(this).attr('data')),
					success: function (data) {
						if(adi.verifyResponse(data))
						{
							adi_inst.db_details_saved = 2;
							$('.inst_input_form').submit();
						}
					},
					error : function(d) { adi_inst.db_details_saved = 0; },
					dataType: 'json'
				});
				return false;
			}
			else
			{
				adi_inst.db_details_saved = 0;
				return true;
			}
			return false;
		});

		$('input').keypress(function(){
			adi_inst.db_details_saved = 0;
		});
	},
	show_logo_preview: function(m,c){
		$('.'+c).attr('src', $(m).val());
	}
};