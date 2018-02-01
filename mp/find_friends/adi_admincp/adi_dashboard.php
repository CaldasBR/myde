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


$get_graph_codes = AdiInviterPro::POST('adi_graph', ADI_INT_VARS);

$get_widget_codes = AdiInviterPro::POST('get_widget', ADI_INT_VARS);

$adiinviter->requireSettingsList(array('global','db_info'));
$adiinviter->init_user();

$show_widget = ($get_widget_codes == 1) ? true : false;
$show_widget_outer = false;


if($adiinviter->user_registration_system == true)
{
	$joined_label = 'Joined';
}
else
{
	$joined_label = 'Visitors';
}


if($adiinviter->db_allowed)
{
	$first_date = AdiInviterPro::POST('first_date', ADI_STRING_VARS);
	$last_date  = AdiInviterPro::POST('last_date',  ADI_STRING_VARS);
	if(!AdiInviterPro::isPOST('first_date') || !AdiInviterPro::isPOST('last_date'))
	{
		$result = adi_build_query_read('get_invite_dates');
		while($row = adi_fetch_assoc($result))
		{
			$first_date = $row['fs'];
		}
		$first_date = adi_mktime(0,0,0,date("n",$first_date),date("j",$first_date),date("Y",$first_date));
		$last_date  = $adiinviter->adi_get_utc_timestamp();
		$last_date  = adi_mktime(23,59,59,date("n",$last_date),date("j",$last_date),date("Y",$last_date));
	}
	else
	{
		$first_date = strtotime($first_date);
		$last_date  = strtotime($last_date);
		if($first_date > $last_date) 
		{
			$t = $first_date;
			$first_date = $last_date; $last_date = $t;
		}
		$first_date = adi_mktime(0,0,0,date("n",$first_date),date("j",$first_date),date("Y",$first_date));
		$last_date  = adi_mktime(23,59,59,date("n",$last_date),date("j",$last_date),date("Y",$last_date));
	}
	$first_date_show = date('d M Y', $first_date);
	$last_date_show  = date('d M Y', $last_date);
}


$graph_code = '';
if($get_graph_codes == 1)
{
	if(!headers_sent())
	{
		header("Content-type: text/javascript");
		header("charset: UTF-8");
		header("Cache-Control: must-revalidate");
	}
	$admin_path = dirname(__FILE__);
	include_once($admin_path.DIRECTORY_SEPARATOR.'adi_init.php');

	$screen_size = AdiInviterPro::POST('screen_size', ADI_INT_VARS);
	if(!in_array($screen_size, array(1024,1152,1280)))
	{
		$screen_size = 1024;
	}

	$total_vars = AdiInviterPro::POST('total_vars', ADI_INT_VARS);
	$total_vars = is_numeric($total_vars) ? $total_vars : 18;
	$total_vars--;

	$type = 'days';
	$yaxis_max = 0;

	// Decide type
	$total_data        = array();
	$joined_data       = array();
	$unsubscribed_data = array();
	$diff              = $last_date - $first_date;
	$days              = ceil($diff / 86400);
	$x_labels          = array();
	$y_labels          = array();
	$tooltip_info      = array();
	$overlapping_pts   = array();
	$cnt               = 1;
	$total_count       = 0;
	$joined_count      = 0;
	$unsubscribed_count = 0;

	if($days <= $total_vars)
	{
		if($days % 2 == 0) {
			$first_date = adi_mktime(0,0,0, date("n", $first_date), date("j", $first_date)-1, date("Y", $first_date));
		}

		$dt = adi_mktime(0,0,0, date("n", $first_date), date("j", $first_date), date("Y", $first_date));
		$interval = 86400;
		$type = 'days';
	}
	else
	{
		if($total_vars % 2 == 0) {
			$first_date = adi_mktime(0,0,0, date("n", $first_date), date("j", $first_date)-1, date("Y", $first_date));
		}
		
		$dt = adi_mktime(0,0,0, date("n", $first_date), date("j", $first_date), date("Y", $first_date));
		$interval = ceil($diff / $total_vars);
		$type = 'duration';
	}

	do
	{
		$ldt = $dt + $interval;
		$total=0; $joined=0; $unsubscribed = 0;

		if($result = adi_build_query_read('get_invites_for_duration', array(
			'start_date' => $dt,
			'last_date'  => $ldt,
			'duration'   => $interval,
		)))
		{
			while($row = adi_fetch_assoc($result))
			{
				if($row['invitation_sent'] != null)
				{
					$total = $row['invitation_sent'] + $row['accepted'] + $row['blocked'];
					if($adiinviter->user_registration_system == true)
					{
						$joined = is_numeric($row['accepted']) ? $row['accepted'] : 0;
					}
					else
					{
						$joined = is_numeric($row['visited']) ? $row['visited'] : 0;
					}
					$unsubscribed = is_numeric($row['blocked']) ? $row['blocked'] : 0;

					$total_count  += $total;
					$joined_count += $joined;
					$unsubscribed_count += $unsubscribed;
				}
			}
		}

		// Tooltip Head
		if($type == 'days')
		{
			$date_txt = '<div class="tt_month">'.strtoupper(date('M',$dt)).'</div>'.
			'<div class="tt_date">'.date('j',$dt).'</div>'.
			'<div class="tt_year">'.date('Y',$dt).'</div>';
		}
		else if($type == 'duration')
		{
			$date_txt = '<div class="tt_month">'.strtoupper(date('M',$dt)).'</div>'.
			'<div class="tt_date">'.date('j',$dt).'</div>'.
			'<div class="tt_year">'.date('Y',$dt).'</div>';
		}
		$tooltip_info[$cnt] = $date_txt;

		// Overlapping points
		if($total == $joined && $joined == $unsubscribed)
		{
			if($total != 0) {
				$overlapping_pts[$cnt] = array("i"=>$total,"j"=>$joined,"u"=>$unsubscribed);
			}
		}
		else if($total == $joined)
		{
			$overlapping_pts[$cnt] = array("i"=>$total,"j"=>$joined);
		}
		else if($total == $unsubscribed)
		{
			$overlapping_pts[$cnt] = array("i"=>$total,"u"=>$unsubscribed);
		}
		else if($joined == $unsubscribed)
		{
			$overlapping_pts[$cnt] = array("j"=>$joined,"u"=>$unsubscribed);
		}

		$ind = count($total_data);

		// graph values
		$total_data[$ind] = $total;
		$joined_data[$ind] = $joined;
		$unsubscribed_data[$ind] = $unsubscribed;

		// X-axis labels
		$rind = ($ind+1);
		if($days > 365) {
			$txt = date("M y", $dt);
		}
		else {
			$txt = date("d M", $dt);
		}
		$x_labels[] = array($rind, $txt);
		
		// Maximum value on Y-axis
		if($yaxis_max < $total) {
			$yaxis_max = $total;
		}
		$cnt++;

		$dt = $ldt;
		// break;
	} while($ldt <= $last_date);

	$factor = 0; $trim_last = false;
	if(count($x_labels) < 3)
	{
		$factor = 1;
	}

	for($i=0 ; $i < count($x_labels) ; $i++)
	{
		if($i%2 == $factor && isset($x_labels[$i][1])) 
		{
			$x_labels[$i][1] = '';
		}
	}


	// Y-axis labels
	$ticksize = ceil($yaxis_max/4);
	for($i=0; $i < $yaxis_max; $i+=$ticksize)
	{
		if($i >= 100000) {
			$k = floor($i/100000).' M';
		}
		else if($i >= 10000) {
			$k = floor($i/1000).' K';
		}
		else {
			$k = $i;
		}
		$y_labels[] = array($i, $i);
	}
	$y_max_inc=0;
	if(count($y_labels) == 4) {
		$y_max_inc = ($ticksize*4) - $yaxis_max;
	}

	$joined_label =  ($adiinviter->user_registration_system) ? 'Joined' : 'Visits';


	echo '
	adi_graph["data_type3"]["label"] = "'.$joined_label.'"; adi_graph["data_type4"]["label"] = "'.$joined_label.'";

	adi_graph.total_data = '.json_encode($total_data).';
	adi_graph.joined_data = '.json_encode($joined_data).';
	adi_graph.unsubscribed_data = '.json_encode($unsubscribed_data).';

	adi_graph.total_count = "'.$total_count.'";
	adi_graph.joined_count = "'.$joined_count.'";
	adi_graph.unsubscribed_count = "'.$unsubscribed_count.'";

	$(".invitations_legend").html("'.number_format($total_count,0,'.',',').'");
	$(".joined_legend").html("'.number_format($joined_count,0,'.',',').'");
	$(".unsubscribed_legend").html("'.number_format($unsubscribed_count,0,'.',',').'");

	adi_graph.options.xaxis.ticks = '.json_encode($x_labels).';
	adi_graph.options.yaxis.tickSize = '.$ticksize.';
	adi_graph.options.yaxis.max = '.($yaxis_max+$y_max_inc).';
	/*adi_graph.options.yaxis.ticks = '.json_encode($y_labels).';*/

	adi_graph.tooltip_info = '.json_encode($tooltip_info).';
	adi_graph.overlapping_pts = '.json_encode($overlapping_pts).';

	adi_graph.renderGraph = 1;
	init_charts();
	$("#graph_first_date").html("'.$first_date_show.'");
	$("#graph_last_date").html("'.$last_date_show.'");

	adi.graph_first_date = "'.$first_date_show.'";
	adi.graph_last_date = "'.$last_date_show.'";
	';
}
else if(!$show_widget)
{

	$total = 0;
	$invitation_sent = 0;
	$joined=0;
	$visited=0;
	$unsubscribed=0;
	if($adiinviter->db_allowed === true)
	{
		if($res = adi_build_query_read('get_invites_summary'))
		{
			if($row = adi_fetch_assoc($res))
			{
				if($adiinviter->user_registration_system == true)
				{
					$joined = (isset($row['accepted']) && !is_null($row['accepted'])) ? $row['accepted'] : 0;
				}
				else
				{
					$visited = (isset($row['visited']) && !is_null($row['visited'])) ? $row['visited'] : 0;
				}
				$unsubscribed = (isset($row['blocked']) && !is_null($row['blocked'])) ? $row['blocked'] : 0;
				$invitation_sent = (isset($row['invitation_sent']) && !is_null($row['invitation_sent'])) ? $row['invitation_sent'] : 0;
			}
		}
		$total = $joined + $unsubscribed + $invitation_sent;
	}

	$total        = $total;
	$joined       = $joined;
	$visited      = $visited;
	$unsubscribed = $unsubscribed;

	if($adiinviter->user_registration_system == false)
	{
		$joined = $visited;
	}

?>
<div style="margin:10px;">

<?php if($adiinviter->db_allowed === true) { ?>
	<div class="top_stats_cont">
		<center>
		<table cellpadding="0" cellspacing="0">
			<tr><td colspan="5" class="top_stats_sep_horizon"></td></tr>
			<tr>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="middle" align="center">
								<?php  if($total < 1) { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_total_zero.gif">
								<?php } else { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_total.gif">
								<?php } ?>
							</td>
						</tr>
						<tr><td style="width:15px;"></td></tr>
						<tr>
							<td align="center">
								<?php  if($total < 1) { ?>
									<div class="ts_bg_num"><?php echo number_format($total, 0,'.',','); ?></div>
								<?php } else { ?>
									<div class="ts_bg_num"><?php echo number_format($total, 0,'.',','); ?></div>
								<?php } ?>
								<div class="ts_sm_txt">Invitations</div>
							</td>
						</tr>
					</table>
				</td>
				<td class="top_stats_sep"></td>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="middle" align="center">
								<?php  if($joined < 1) { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_joined_zero.gif">
								<?php } else { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_joined.gif">
								<?php } ?>
							</td>
						</tr>
						<tr><td style="width:15px;"></td></tr>
						<tr>
							<td align="center">
								<?php  if($joined < 1) { ?>
									<div class="ts_bg_num"><?php echo number_format($joined, 0,'.',','); ?></div>
								<?php } else { ?>
									<div class="ts_bg_num"><?php echo number_format($joined, 0,'.',','); ?></div>
								<?php } ?>
								<div class="ts_sm_txt"><?php echo $joined_label; ?></div>
							</td>
						</tr>
					</table>
				</td>
				<td class="top_stats_sep"></td>
				<td>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td valign="middle" align="center">
								<?php  if($unsubscribed < 1) { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_unsubscribed_zero.gif">
								<?php } else { ?>
								<img class="ts_grph_img" src="adi_css/top_stats_unsubscribed.gif">
								<?php } ?>
							</td>
						</tr>
						<tr><td style="width:15px;"></td></tr>
						<tr>
							<td align="center">
								<?php  if($unsubscribed < 1) { ?>
									<div class="ts_bg_num"><?php echo number_format($unsubscribed, 0,'.',','); ?></div>
								<?php } else { ?>
									<div class="ts_bg_num"><?php echo number_format($unsubscribed, 0,'.',','); ?></div>
								<?php } ?>
								<div class="ts_sm_txt">Unsubscribed</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr><td colspan="5" class="top_stats_sep_horizon"></td></tr>
		</table>
		</center>
	</div>
<?php } ?>

<?php if($total < 1) { ?>
<table cellpadding="0" cellspacing="0" width="100%" height="600px">
	<tr>
		<td valign="middle">
			<center>
				<img class="no_data_img" src="adi_css/no_data_available.png">
				<div class="no_data_head">No Data Available</div>
				<?php if($adiinviter->db_allowed === true) { ?>
				<div class="no_data_txt">0 invitations sent so far.</div>
				<?php } else { ?>
				<div class="no_data_txt">Database integration is required.</div>
				<?php } ?>
			</center>
		</td>
	</tr>
</table>
<?php } else { ?>


<div style="width:100%;height:20px;"></div>

<div class="graph_selector_cont_out">
	<table cellpadding="0" cellspacing="0" class="graph_selector_cont" width="100%">
		<tr>
			<!-- <td style="width: 50px;"><span class="graph_selector_label">Show :</span></td> -->
			<td align="left">
				<form action="" method="post" class="graph_date_form">
				<div style="postition: relative;">
				<table>
				<tr>
					<td valign="middle"><div id="graph_first_date" class="graph_date_txt"><?php echo $first_date_show; ?></div></td>
					<td valign="middle" style="width: 5px"></td>
					<td valign="middle"><span class="graph_selector_label"> - </span></td>
					<td valign="middle" style="width: 5px"></td>
					<td valign="middle"><div id="graph_last_date" class="graph_date_txt"><?php echo $last_date_show; ?></div></td>
					<td valign="middle" style="width: 10px"></td>
				</tr>
				</table>
				</div>
				</form>
			</td>
			<td align="right" valign="middle">
				<div class="graph_selector_out">
					<div class="graph_selector_opt graph_selector_unchecked" data="blocked">Unsubscribed</div>
					<div class="graph_selector_opt graph_selector_unchecked" data="accepted"><?php echo $joined_label; ?></div>
					<div class="graph_selector_opt graph_selector_checked" data="total">Invitations</div>
					<div class="clr"></div>
				</div>
			</td>
		</tr>
	</table>
</div>

<div style="width:100%;height:10px;"></div>

	<div id="adi_graph_out">
		<div class="adi_graph_legend_out">
			<div class="adi_graph_legend_in">
				<center>
				<div class="adi_legend_cont">
					<div class="legend_nums_out"><div class="legend_num invitations_legend">0</div></div>
					<div class="legend_nums_out"><div class="legend_num joined_legend">0</div></div>
					<div class="legend_nums_out"><div class="legend_num unsubscribed_legend">0</div></div>
				</div>
				</center>
			</div>
		</div>
		<?php if($adiinviter->user_registration_system == true) { ?>
		<div id="adi_graph_overlay" style="">
		<?php } else { ?>
		<div id="adi_graph_overlay_visitors" style="">
		<?php } ?>
			<div id="adi_graph" style=""></div>
		</div>
	</div>



<script type="text/javascript">

	$(document).ready(function(){
		
		var URL = adi.generate_url('adi_get.php');
		$.ajax({
			type: 'POST',
			url: URL,
			data: adi.join_post_data({"adi_graph": 1, gname: 'dashboard', screen_size: adi_scr_mode, total_vars: adi_graph.get_total_vals()}),
			success: function (data) {
				// init_charts();
			},
			error : function(d) {  },
			dataType: 'script'
		});
	});

	$('#graph_first_date').Zebra_DatePicker({
		offset: [-180,290],
		always_show_clear: false,
		show_icon: false,
		direction: true,
		format: 'd M Y',
		// yyyy-mm-dd
		direction: ['<?php  echo $first_date_show; ?>', '<?php  echo $last_date_show; ?>'],
		pair: $('#graph_last_date'),
		onSelect: function(view, elements){
			$('.graph_date_form').submit();
		}
	});

	// $('#graph_last_date').Zebra_DatePicker({
	$('#graph_last_date').Zebra_DatePicker({
		always_show_clear: false,
		offset: [-180,290],
		show_icon: false,
		format: 'd M Y',
		// yyyy-mm-dd
		direction: ['<?php  echo $first_date_show; ?>', '<?php  echo $last_date_show; ?>'],
		onSelect: function(view, elements){
			$('.graph_date_form').submit();
		}
	});


</script>


<?php 


$show_widget = true;
$show_widget_outer = true;


} /* If data is available */ 

} /* Show Full HTML */

$widget_html = '';

if($show_widget)
{
	$w_page_no = max(1, AdiInviterPro::POST('w_page', ADI_INT_VARS));
	$page_size = 5;
	$offset = ($page_size * ($w_page_no-1));

	if($adiinviter->user_system) 
	{
		$user_count = 0;
		$serial_no = 1 + ($w_page_no - 1) * $page_size;
		$all_data = array(); //$b_total=0;$b_joined=0;$b_unsubscribed=0;
		if($res = adi_build_query_read('get_top_inviters',array(
			'offset' => $offset,
			'size' => $page_size+1
		)))
		{
			if($row = adi_fetch_assoc($res))
			{
				$widget_html .= <<<HTML
				<div class="adi_inner_sect_sep" style="height: 40px;"></div>
	<table cellpadding="0" cellspacing="0" width="100%" class="settings_table wid_data_out">
		<tr>
			<th valign="middle" align="center" width="40">Rank</th>
			<th>Top Invite Senders</th>
			<th></th>
			<th valign="middle" align="center" width="120"><span class="wid_col_title">Invitations</span></th>
			<th valign="middle" align="center" width="120"><span class="wid_col_title">$joined_label</span></th>
			<th valign="middle" align="center" width="120"><span class="wid_col_title">Unsubscribed</span></th>
		</tr>
HTML;

			$limiter = $page_size;
			$counter = 0;
			$odd = false;
			do 
			{
				$counter++;
				if($limiter-- == 0) { break; }

				$row['cnt']      = (isset($row['cnt']) && is_numeric($row['cnt'])) ? $row['cnt'] : 0;
				$row['accepted'] = (isset($row['accepted']) && is_numeric($row['accepted'])) ? $row['accepted'] : 0;
				$row['blocked']  = (isset($row['blocked']) && is_numeric($row['blocked'])) ? $row['blocked'] : 0;

				if(!isset($b_total))
				{
					$b_total        = $row['cnt'];
					if($adiinviter->user_registration_system)
					{
						$b_joined = $row['accepted'];
					}
					else
					{
						$b_joined = $row['visited'];
					}
					$b_unsubscribed = $row['blocked'];
				}

				$adi_user = $adiinviter->getUserInfo($row['inviter_id']);

				$avatar_txt = '';
				$avatar_url = $adiinviter->default_no_avatar;

				if($adiinviter->avatar_system && !empty($adi_user->avatar)) {
					$avatar_url = $adi_user->avatar;
				}
				$avatar_txt = '<img src="'.$avatar_url.'" style="max-height:48px;">';

				$username_txt = '<span class="widget_username">'.$adi_user->userfullname.'</span>';

				$user_email = $adi_user->email;

				$invitations_total = $row['cnt'];
				if($adiinviter->user_registration_system)
				{
					$joined_total = $row['accepted'];
				}
				else
				{
					$joined_total = $row['visited'];
				}
				$blocked_total     = $row['blocked'];

				$odd = !$odd;
				$css_cls = ($odd ? ' class="odd"' : '');
				
				$widget_html .= <<<HTML
				<tr $css_cls>
					<td class="wid_serial_no" align="center">$serial_no.</td>
					<td class="widget_username_out" valign="top">
						<table cellpadding="0" cellspacing="0"><tr>
							<td style="padding:0px;padding-right:8px;">$avatar_txt</td>
							<td style="padding:0px;padding-top:3px;vertical-align:top;">
								$username_txt
								<div class="widget_email">$user_email</div>
							</td>
						</tr></table>
					</td>
					<td></td>
					<td class="widget_nums_out" align="center">
						<div class="widget_bg_num">$invitations_total</div>
					</td>
					<td class="widget_nums_out" align="center">
						<div class="widget_bg_num">$joined_total</div>
					</td>
					<td class="widget_nums_out" align="center">
						<div class="widget_bg_num">$blocked_total</div>
					</td>
				</tr>
HTML;

				$b_total = $row['cnt'];
				$b_joined = $row['accepted'];
				$b_unsubscribed = $row['blocked'];

				$user_count++;
				$serial_no++;

			} while($row = adi_fetch_assoc($res));

				$prev_page = max(1, ($w_page_no- 1));
				$next_page = $w_page_no;

				$higher_rank_css = $lower_rank_css = 'visibility:hidden;';
				if($offset > 0) {
					$higher_rank_css = '';
				}
				if($counter > $page_size) {
					$lower_rank_css = '';
					$next_page = $w_page_no + 1;
				}
				$widget_html .= '</table>';
				if(!(!empty($higher_rank_css) && !empty($lower_rank_css)) )
				{
					$widget_html .= '
					<center>
						<table cellpadding="0" cellspacing="0" style="margin: 10px 0px;" width="100%">
						<tr>
							<td align="left">
								<input type="button" class="btn_grn adi_widget_paginate" value="Previous" data="'.$prev_page.'" style="'.$higher_rank_css.'">
							</td>
							<td align="right">
								<input type="button" class="btn_grn adi_widget_paginate" value="Next" data="'.$next_page.'" style="'.$lower_rank_css.'">
							</td>
						</tr>
						</table>
					</center>
					<script type="text/javascript">
						adi.register_widget_paginate()
					</script>
					';
				}
			}
		}

	}
	else 
	{

		$widget_html .= <<<HTML
		<div class="adi_inner_sect_sep" style="height: 40px;"></div>
	<table cellpadding="0" cellspacing="0" width="100%" class="settings_table wid_data_out">
		<tr>
			<th align="left" style="width:40%;">Month</th>
			<th></th>
			<th valign="middle" align="center" width="180"><span class="wid_col_title">Invitations</span></th>
			<th valign="middle" align="center" width="180"><span class="wid_col_title">$joined_label</span></th>
			<th valign="middle" align="center" width="180"><span class="wid_col_title">Unsubscribed</span></th>
		</tr>
HTML;

		$res = adi_build_query_read('get_invite_dates');
		if($row = adi_fetch_assoc($res))
		{
			$start_dt = $row['fs'];
			$last_dt = $row['ls'];
		}

		// $serial_no = $w_page_no;
		$all_pages = array();
		$fdosm = adi_mktime(0,0,0, date('n',$start_dt), 1, date('Y', $start_dt));

		$fdoem = adi_mktime(0,0,0, date('n',$last_dt), 1, date('Y', $last_dt));
		$fdom = $fdoem;

		do
		{
			$all_pages[] = array(date('n',$fdom), date('Y',$fdom));
			$fdom = $fdom - 86400;
			$fdom = adi_mktime(0,0,0, date('n',$fdom), 1, date('Y', $fdom));
		}
		while($fdosm <= $fdom);
		$page_chunks = array_chunk($all_pages, $page_size);
		
		if(count($page_chunks[$w_page_no-1]) > 0)
		{
			$odd = false;
			foreach($page_chunks[$w_page_no-1] as $m_details)
			{
				$st = adi_mktime(0,0,0, $m_details[0], 1, $m_details[1]);
				$et = adi_mktime(23,59,59, $m_details[0], date('t', $st), $m_details[1]);

				$b_total = $b_joined = $b_unsubscribed = 0;
				if($res = adi_build_query_read('get_short_invites_for_duration',array(
						'start_date' => $st,
						'last_date'  => $et,
					)))
				{
					if($s_row = adi_fetch_assoc($res))
					{
						$b_joined = is_numeric($s_row['accepted']) ? $s_row['accepted'] : 0;
						$b_unsubscribed = is_numeric($s_row['blocked']) ? $s_row['blocked'] : 0;
						$b_total = is_numeric($s_row['cnt']) ? $s_row['cnt'] : 0;
					}
				}

				$display_txt = date('F Y', $st);
				$odd = !$odd;
				$css_cls = ($odd ? ' class="odd"' : '');
				if($et > $start_dt)
				{
					$widget_html .= <<<HTML
					<tr $css_cls>
						<td class="widget_date_out" valign="top">$display_txt</td>
						<td></td>
						<td class="widget_nums_out" align="center">
							<div class="widget_bg_num">$b_total</div>
						</td>
						<td class="widget_nums_out" align="center">
							<div class="widget_bg_num">$b_joined</div>
						</td>
						<td class="widget_nums_out" align="center">
							<div class="widget_bg_num">$b_unsubscribed</div>
						</td>
					</tr>
HTML;
				}
			}
		}
		$newer_records_css = $older_records_css = 'visibility:hidden;';
		if($w_page_no > 1)
		{
			$newer_records_css = '';
		}
		if(isset($page_chunks[$w_page_no]))
		{
			$older_records_css = '';
			$next_page = $w_page_no + 1;
		}
		if(count($page_chunks) > 0)
		{
			$widget_html .= '</table>
	<center>
		<table cellpadding="0" cellspacing="0" style="margin: 15px 0px;" width="100%">
		<tr>
			<td align="left">
				<input type="button" class="btn_grn adi_widget_paginate" value="Previous" data="'.($w_page_no-1).'" style="'.$newer_records_css.'">
			</td>
			<td align="right">
				<input type="button" class="btn_grn adi_widget_paginate" value="Next" data="'.($w_page_no+1).'" style="'.$older_records_css.'">
			</td>
		</tr>
		</table>
	</center>
	<script type="text/javascript">
		adi.register_widget_paginate()
	</script>';
			
		}

	} /* show widget if user_system = true */ 
}

if($show_widget_outer)
{
	$widget_html = '<div class="adi_widget_outer_div">'.$widget_html.'</div>';
}

echo $widget_html;

?>