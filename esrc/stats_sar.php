<?php 
include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
include_once '../class/config.class.php';
include_once '../class/mmmr.class.php';
require_once '../class/leaderboard_sar.class.php';

// initialize objects
$database = new Database();
$rescue = new Rescue($database);
$sarleaderBoard = new SARLeaderboard($database);

// get count of all SAR rescues, max days
$ctrSARrescues = $rescue->getRescueCount('closed-rescued');
$maxdaysSARrescue = $sarleaderBoard->getRescueMaxDays('closed-rescued');

// get SAR wait time values array
$arrSARWaits = $rescue->getSARWaitTime();
// then get mean, median, and mode
$SARWaitMean = mmmr($arrSARWaits);
//$SARWaitMedian = mmmr($arrSARWaits, 'median');	// not sure if this is helpful, so hiding for now
$SARWaitMode = mmmr($arrSARWaits, 'mode');
$SARWaitModeCnt = mmmr($arrSARWaits, 'modecnt');
?>

 <div class="col-sm-4"> 
	 <span class="sechead" style="font-weight: bold; color: gold;">
		SAR Rescues: <span style="color: white;"><?=$ctrSARrescues?></span></span><br /><br />
		<span class="sechead" style="font-weight: bold;">Average Wait (days):</span><br /> 
	
	 <table id="tblSARWaitTime" class="table display" style="width: auto;"> 
		<thead> 
		 	<tr> 
		 		<th data-toggle="tooltip" data-placement="top" title="Excessively lengthy waits 
					will skew this number higher">Mean</th> 
		 		<!-- <th data-toggle="tooltip" data-placement="top" title="Not sure how helpful
					this number actually is">Median</th> --> 
		 		<th data-toggle="tooltip" data-placement="top" title="The most common wait time 
					(percentage of rescues that happen in this number of days)">Mode</th>
				<th data-toggle="tooltip" data-placement="top" title="Longest wait for a   
					successful rescue">Max</th> 
		 	</tr> 
		 </thead> 
		 <tbody> 
		 	<tr> 
		 		<td style="text-align: center;"><?=round(intval($SARWaitMean))?></td> 
		 		<!-- <td style="text-align: center;">'. round(intval($SARWaitMedian)) .'</td> --> 
	 			<td style="text-align: center;"><?=round(intval($SARWaitMode)) 
					.' ('. round(intval($SARWaitModeCnt) / max(intval($ctrSARrescues), 1) * 100)?>%)</td> 
				<td style="text-align: center;"><?=round(intval($maxdaysSARrescue))?></td>
	 		</tr> 
	 	</tbody> 
	 </table> 

<!-- LEADERBOARD -->
<?php 
$typeLB = isset($typeLB) ? $typeLB: 'helpful';
$daysrangeLB = isset($daysrangeLB) ? $daysrangeLB: '30';
$numberLB= isset($numberLB) ? $numberLB: '5';
if (isset($_REQUEST['LBtype'])) {
	$typeLB= htmlspecialchars_decode($_REQUEST['LBtype']);
}
if (isset($_REQUEST['daysrangeLB'])) { 
	$daysrangeLB= htmlspecialchars_decode($_REQUEST['daysrangeLB']);
}
if (isset($_REQUEST['numberLB'])) {
	$numberLB = htmlspecialchars_decode($_REQUEST['numberLB']);
}
?>

	<!-- LB type, date range and number selection form -->
	<span class="sechead" style="font-weight: bold;">LEADERBOARDS</span><br />
	<form id="LBform" name="LBform" method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
		<select class="form-control" name="LBtype" onchange="$('#LBform').submit();" 
			style="width: auto; margin: 5px;">
		    <option value="helpful"<?php if ($typeLB == 'helpful') { echo ' selected'; } ?>>
		    	Most Helpful 911 Operators</option>
		    <option value="mostsuccesrc"<?php if ($typeLB == 'mostsuccesrc') { echo ' selected'; } 
		    	?>>Most Successful ESRC Agents</option>
		    <option value="mostsuccdisp"<?php if ($typeLB == 'mostsuccdisp') { echo ' selected'; } 
		    	?>>Most Successful SAR Dispatchers</option>
		    <option value="mostloc"<?php if ($typeLB == 'mostloc') { echo ' selected'; } ?>>
		    	Most SAR Locates</option>
		    <option value="mostsuccloc"<?php if ($typeLB == 'mostsuccloc') { echo ' selected'; } ?>>
		    	Most Successful SAR Locates</option>
		    <!-- <option value="mostsar"<?php if ($typeLB == 'mostsar') { echo ' selected'; } ?>>
		    	Most Attempted Rescues</option>
		    <option value="mostsuccsar"<?php if ($typeLB == 'mostsuccsar') { echo ' selected'; } ?>>
		    	Most Successful Rescues</option> -->
		    <option value="admin"<?php if ($typeLB == 'admin') { echo ' selected'; } ?>>Top Admin</option>
		</select>
		Top <input type="text" name="numberLB" size="1" autocomplete="off" class="black"
				value="<?=$numberLB?>"> over the last <input type="text" name="daysrangeLB" 
				size="1" autocomplete="off" class="black" value="<?=$daysrangeLB?>"> days
		<input type="submit" style="display: none;">
	</form>
	<table class="table" style="width: auto;">
		<thead>
			<tr>
				<th>Pilot</th>
				<th align="right">Count</th>
			</tr>
		</thead>
		<tbody>
			<?php
			// return data for the LB specified by the user
			switch ($typeLB) {
				case 'helpful':
				default:
					$agenttype = 'startagent';
					$rows = $sarleaderBoard->getTop('%%', $agenttype, $numberLB, $daysrangeLB);
				break;
				case 'mostsuccdisp':
					$agenttype = 'startagent';
					$rows = $sarleaderBoard->getTop('closed-rescued', $agenttype, $numberLB, 
						$daysrangeLB);
					break;
				case 'mostsuccesrc':
					$agenttype = 'startagent';
					$rows = $sarleaderBoard->getTop('closed-esrc', $agenttype, $numberLB, 
						$daysrangeLB);
				break;
				case 'mostloc':
					$agenttype = 'locateagent';
					$rows = $sarleaderBoard->getTop('%%', $agenttype, $numberLB, $daysrangeLB);
				break;
				case 'mostsuccloc':
					$agenttype = 'locateagent';
					$rows = $sarleaderBoard->getTop('closed-rescued', $agenttype, $numberLB, 
						$daysrangeLB);
				break;
				case 'mostsar':
					$agenttype = 'rescueagent';
					$rows = $sarleaderBoard->getTop('closed-esrc', $agenttype, $numberLB, 
						$daysrangeLB);
				break;
				case 'mostsuccsar':
					$agenttype = 'rescueagent';
					$rows = $sarleaderBoard->getTop('closed-esrc', $agenttype, $numberLB, 
						$daysrangeLB);
				break;
				case 'admin':
					$agenttype = 'closeagent';
					$rows = $sarleaderBoard->getTop('%%', $agenttype, $numberLB, $daysrangeLB);
				break;
			}
			
			// display the specified LB
			foreach ($rows as $value) {
				// do not display rows with no agent name
				if (!empty($value[$agenttype])) {
					echo '<tr>';
					echo 	'<td>'. Output::htmlEncodeString($value[$agenttype]) .'</td>';
					echo 	'<td align="right">'. $value['cnt'] .'</td>';
					echo '</tr>';
				}
			}
			?>
		</tbody>
	</table>
 </div> 