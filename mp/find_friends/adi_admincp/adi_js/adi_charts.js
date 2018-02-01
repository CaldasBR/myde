var data1,data,options,chart1,tt_txt,previousPoint,data2, data3, data_type1, data_type2, adi_check, highligh_pts = {0:{},1:{},2:{}}, adi_prim, previousPoint, loc_store;
var adi_graph = {
	renderGraph:0,
	startupCheck:0,
	all_data: {},
	highlight_pts: [],
	lastx: undefined,
	lasty: undefined,
	show_graph: function(type){
		if(type == 'accepted') {
			dd = [data2];
			chart1 = $.plot($('#adi_graph'), dd, options);
		}
		else if(type == 'blocked') {
			dd = [data3];
			chart1 = $.plot($('#adi_graph'), dd, options);
		}
		else {
			dd = [data1];
			chart1 = $.plot($('#adi_graph'), dd, options);
		}
		$(".adi_graph_tt").hide();
	},
	get_total_vals: function(){
		var total_vals = 19;
		if(adi_scr_mode == 1152) {
			total_vals = 22;
		}
		else if(adi_scr_mode == 1280) {
			total_vals = 26;
		}
		return total_vals;
	},
	options: {
		series: {
			 lines: { 
			 	show: true,
			 	fillColor: "#a7cf62"
			 },
		},
		
		grid: {
			hoverable: true, 
			clickable: true,
			backgroundColor: 'rgba(0,0,0,0)',
			borderWidth: 0
		},
		yaxis: {
			show: true,
			color: '#7f9d4c',
			position: "left",
			family: "Verdana",
			labelWidth: 30,
			tickSize: 7,
			tickLength: 1,
			// transform: function (v) { return parseInt(v); },
			min: 0,
			// ticks: 0,
			max: 36
		},
		xaxis: {
			show: true,
			tickSize: 2,
			font: {
				size: 7,
				family: "Verdana",
				margin: {
					top: 0,
					left: 10,
					bottom: 0,
					right: 0
				},
				align: "right"
			},
			color: "rgba(0,0,0,0)",
			labelWidth: 70,
			position: "bottom",
			family: "Verdana",
			min: 1,
			// tickLength: 0.1
		},
		legend: {
			show: false
		}
	},
	yaxis_max: 35,
	xaxis_lbls: [
		[1 ,""],
		[2 ,"Jul 12"],
		[3 ,""],
		[4 ,"Aug 12"],
		[5 ,""],
		[6 ,"Sep 12"],
		[7 ,""],
		[8 ,"Oct 12"],
		[9 ,""],
		[10,"Nov 12"],
		[11,""],
		[12,"Dec 12"],
		[13,""],
		[14,"Jan 13"],
		[14,""],
		[16,""],
		[17,""],
		[18,""]
	],
	tooltip_info:{},
	overlapping_pts:{},

	data_type1: {
		label: "Invitations",
		color : "#a7cf62",
		lines: { 
			lineWidth: 2.2,
			show: true
		},
		points: {
			show: true,
			fill: true,
		 	fillColor: "#2a2a2a",
			radius: 4.5
		},
		highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	data_type2: {
		label: "Invitations",
		color : "#a7cf62",
		lines: {
			show: true,
			fill: true,
			lineWidth: 2.2,
			fillColor: { colors: [ "rgba(156,193,92,0.1)", "rgba(80,132,34,0.3)" ] }
		},
		
		points: {
			show: true,
			fill: true,
			fillColor: "#2a2a2a",
			radius: 4.5
		},
		highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	// For Joined
	data_type3: {
		label: "Joined",
		color : "rgba(34,192,219,1)", // #22C0DB
		lines: { 
			lineWidth: 1.7,
			show: true
		},
		points: {
			show: true,
			fill: true,
			fillColor: "#2a2a2a",
			radius: 4.3
		},
		highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	data_type4: {
		label: "Joined",
		color : "rgba(34,192,219,1)", // #22C0DB
		lines: {
			show: true,
			fill: false,
			lineWidth: 1.7,
			fillColor: { colors: [ "rgba(156,193,92,0.1)", "rgba(80,132,34,0.3)" ] }
		},
		
		points: {
		 	show: true,
		 	fill: true,
		 	fillColor: "#2a2a2a",
		 	radius: 4.3
		 },
		 highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	// For Unsubscribed
	data_type5: {
		label: "Unsubscribed",
		color : "rgba(254,68,69,1)", // #FE4445
		lines: { 
			lineWidth: 1.7,
			show: true
		},
		points: {
			show: true,
			fill: true,
		 	fillColor: "#2a2a2a",
			radius: 4.0
		},
		highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	data_type6: {
		label: "Unsubscribed",
		color : "rgba(254,68,69,1)", // #FE4445
		lines: {
			show: true,
			fill: false,
			lineWidth: 1.7,
			fillColor: { colors: [ "rgba(156,193,92,0.1)", "rgba(80,132,34,0.3)" ] }
		},
		
		points: {
		 	show: true,
		 	fill: true,
		 	fillColor: "#2a2a2a",
		 	radius: 4.0
		 },
		 highlightColor: "rgba(255,0,0,1)",
		shadowSize : 3,
		hoverable: true,
		clickable: true
	},
	total_data : [],
	joined_data : [],
	unsubscribed_data : [],
};

function init_charts()
{
	// For Total
	var total_vals = adi_graph.get_total_vals();

	var total_data_obj=[];

	var t, cnt=1;
	for (var i = 0; i < total_vals; i++) 
	{
		if(i < adi_graph.total_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,adi_graph.total_data[i]],[cnt+1,adi_graph.total_data[i+1]]]}, adi_graph.data_type1);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,adi_graph.total_data[i]],[cnt+1,adi_graph.total_data[i+1]]]}, adi_graph.data_type2);
			}
		}
		else if(i >= adi_graph.total_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type1);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type2);
			}
		}
		total_data_obj.push(t)
		cnt++;
	};


	var joined_data_obj=[];
	cnt=1;
	for (var i = 0; i < total_vals; i++) 
	{
		if(i < adi_graph.joined_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,adi_graph.joined_data[i]],[cnt+1,adi_graph.joined_data[i+1]]]}, adi_graph.data_type3);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,adi_graph.joined_data[i]],[cnt+1,adi_graph.joined_data[i+1]]]}, adi_graph.data_type4);
			}
		}
		else if(i >= adi_graph.joined_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type3);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type4);
			}
		}
		joined_data_obj.push(t)
		cnt++;
	};
	

	var unsubscribed_data_obj=[];
	cnt=1;
	for (var i = 0; i < total_vals; i++) 
	{
		if(i < adi_graph.unsubscribed_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,adi_graph.unsubscribed_data[i]],[cnt+1,adi_graph.unsubscribed_data[i+1]]]}, adi_graph.data_type5);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,adi_graph.unsubscribed_data[i]],[cnt+1,adi_graph.unsubscribed_data[i+1]]]}, adi_graph.data_type6);
			}
		}
		else if(i >= adi_graph.unsubscribed_data.length)
		{
			if(i%2 == 0)
			{
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type5);
			}
			else {
				t = jQuery.extend(true, {data:[[cnt,-10], [cnt,-20]]}, adi_graph.data_type6);
			}
		}
		unsubscribed_data_obj.push(t)
		cnt++;
	};

	var all_data = [];
	var a1=false,a2=false,a3=false;
	$('.graph_selector_checked').each(function(){
		if($(this).attr('data') == 'blocked')
		{
			a1=true;
		}
		else if($(this).attr('data') == 'accepted')
		{
			a2=true;
		}
		else if($(this).attr('data') == 'total')
		{
			a3=true;
		}
	});
	if(a1){
		all_data = all_data.concat(unsubscribed_data_obj);
	}
	if(a2){
		all_data = all_data.concat(joined_data_obj);
	}
	if(a3){
		all_data = all_data.concat(total_data_obj);
	}

	chart1 = $.plot($('#adi_graph'), all_data, adi_graph.options);

	$(document).ready(function(){
		$('#adi_graph').hover(function(){},function(){
			// $(".adi_graph_tt").hide();
		});

		$('.graph_opt_out').click(function(){
			if(!$(this).hasClass('graph_opt_selected'))
			{
				$('.graph_opt_selected').removeClass('graph_opt_selected');
				$(this).addClass('graph_opt_selected');
				adi_graph.show_graph($(this).attr('data'));
			}
		});
	});

	$("#adi_graph_out").bind("plothover", function (event, pos, item) {
		if(item) 
		{
			if(previousPoint != item.datapoint)
			{
				previousPoint = item.datapoint;
				var x = item.datapoint[0].toFixed(2),y = item.datapoint[1].toFixed(2);
				showTooltip(item.datapoint, item.pageX, item.pageY, item.series.label);
			}
		}
		else 
		{
			$(".adi_graph_tt").hide();
			/*adi_last_tt_x=0;
			adi_last_tt_y=0;*/
			previousPoint = null;
		}
	});

	$('#graph_out').hover(function(){},function(){
		setTimeout(function(){
			hideTooltip();
		},50);
	});

}

var adi_last_tt_x = 0;
var adi_last_tt_y = 0;
function showTooltip(num, x, y, label) 
{
	// console.log(num[0]+':'+num[1]+'  '+x+':'+y);
	if(adi_graph.tooltip_info[num[0]] != undefined /*&& adi_last_tt_x != num[0] && adi_last_tt_y != num[1]*/)
	{
		adi_last_tt_x = num[0];
		adi_last_tt_y = num[1];
		var opts={};
		if(adi_graph.overlapping_pts[num[0]]!= undefined)
		{
			opts=adi_graph.overlapping_pts[num[0]];
		}

		$('.tt_val').hide();
		$('.tt_col').hide();
		if(label == 'Invitations' || (opts['i'] != undefined))
		{
			$('.tt_total_val').html(num[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")).show();
			$('.tt_total_lbl').show();
		}
		if(label == 'Joined' || label == 'Visits' || (opts['j'] != undefined))
		{
			$('.tt_joined_val').html(num[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")).show();
			$('.tt_joined_lbl').html(label).show();
		}
		if(label == 'Unsubscribed' || (opts['u'] != undefined))
		{
			$('.tt_unsubscribed_val').html(num[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")).show();
			$('.tt_unsubscribed_lbl').show();
		}

		$('.tt_title').removeClass('tt_title_blue');
		$('.tt_title').removeClass('tt_title_red');
		if(label == 'Unsubscribed') {
			$('.tt_title').addClass('tt_title_red');
		}
		if(label == 'Joined' || label == 'Visits') {
			$('.tt_title').addClass('tt_title_blue');
		}


		if(label == 'Invitations' && (opts['i'] == undefined))
		{
			$('.tt_val').hide();
			$('.tt_col').hide();
			$('.tt_total_val').show();
			$('.tt_total_lbl').show();
		}
		if((label == 'Joined' || label == 'Visits') && (opts['j'] == undefined))
		{
			$('.tt_val').hide();
			$('.tt_col').hide();
			$('.tt_joined_val').show();
			$('.tt_joined_lbl').show();
		}
		if(label == 'Unsubscribed' && (opts['u'] == undefined))
		{
			$('.tt_val').hide();
			$('.tt_col').hide();
			$('.tt_unsubscribed_val').show();
			$('.tt_unsubscribed_lbl').show();
		}

		$('.tt_title').html(adi_graph.tooltip_info[num[0]]);

		x = x - Math.ceil($(".adi_graph_tt").width() / 2) - 5;
		if((x + $(".adi_graph_tt").width()) > $("body").width())
		{
			x = $(".adi_graph_tt").width() - $(".adi_graph_tt").width() - 7;
		}
		else if(x < 0)
		{
			x = 0;
		}
		y = y - ($(".adi_graph_tt").height() + 25);
		$(".adi_graph_tt").css({
			top: y + 5,
			left: x + 5,
		}).show();
	}
}

function hideTooltip()
{
	$(".adi_graph_tt").hide();
}

$(window).focus(function(){
	if(adi_graph.startupCheck == 0)
	{
		if(adi.currentSettings == 'dashboard' && adi_graph.renderGraph == 1)
		{
			adi_graph.startupCheck = 1;
			adi_graph.renderGraph = 0;
			setTimeout(function(){
				init_charts();
			}, 100);
		}
	}
});

