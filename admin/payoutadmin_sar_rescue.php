<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

require_once '../class/db.class.php';
include_once '../includes/auth-inc.php';
require_once '../class/output.class.php';
require_once '../class/users.class.php';

// if start and end dates are not set, set them to default values
if (!isset($_REQUEST['start'])) {
	$start = gmdate('Y-m-d', strtotime("- 7 day"));
	$startPD = gmdate('Y-M-d', strtotime("- 7 day")); // formatted for Pikaday widget
}
if (!isset($_REQUEST['end'])) {
	$end = gmdate('Y-m-d', strtotime("now"));
	$endPD = gmdate('Y-M-d', strtotime("now")); // formatted for Pikaday widget
}

// set start and end dates to submitted values (GET or POST)
if (isset($_REQUEST['start']) && isset($_REQUEST['end'])) {
	// start date
	$arrStart = explode('-', $_REQUEST['start']);
	$startYear = intval(substr($arrStart[0], -3)) + 1898;
	$startMonth = intval(date('m', strtotime($arrStart[1])));
	$startDay = intval($arrStart[2]);
	$start = gmdate('Y-m-d', strtotime($startYear. '-' . $startMonth. '-' . $startDay));
	
	// end date
	$arrEnd = explode('-', $_REQUEST['end']);
	$endYear = intval(substr($arrEnd[0], -3)) + 1898;
	$endMonth = intval(date('m', strtotime($arrEnd[1])));
	$endDay = intval($arrEnd[2]);
	$end = gmdate('Y-m-d', strtotime($endYear. '-' . $endMonth. '-' . $endDay));
	
	// special string for Pikaday widget
	$startPD = htmlspecialchars_decode(date("Y-M-d", strtotime($startYear. '-' . $startMonth. '-' . $startDay)));
	$endPD = htmlspecialchars_decode(date("Y-M-d", strtotime($endYear. '-' . $endMonth. '-' . $endDay)));
}

?>
<html>

<head>
	<?php
	$pgtitle = 'Payout Admin';
	include_once '../includes/head.php'; 
	?>
	<style>
	<!--
		table {
			table-layout: fixed;
			word-wrap: break-word;
		}
		a,
		a:visited,
		a:hover {
			color: aqua;
		}
	-->
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "order": [[ 0, "desc" ]],
		        "pagingType": "full_numbers",
		        "pageLength": 15
		    } );
		} );
	</script>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Payouts: 
				<a href="payoutadmin.php">ESRC</a> &gt;&gt; 
				<a href="payoutadmin_sar.php">SAR Dispatch</a> &gt;&gt; 
				SAR Locate/Rescue</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span><br />
			<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="input-daterange input-group" id="datepicker" style="margin-bottom: 5px;">
					<input type="text" class="input-sm form-control" name="start" id="start" 
						value="<?php echo isset($startPD) ? $startPD : '' ?>" />
					<span class="input-group-addon">to</span>
					<input type="text" class="input-sm form-control" name="end" id="end" 
						value="<?php echo isset($endPD) ? $endPD : '' ?>" />
				</div>
				<div class="checkbox">
					<label class="white"><input type="checkbox" name="details" value="yes"> Payout</label>
				</div>
				&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Search</button>
			</form>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	// display results for the selected date range
	$db = new Database();
		
	//show detailed records if "Payout" is not checked
	if (!isset($_POST['details']) && $_POST['details'] != 'yes') {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-10">
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Rescue Date</th>
						<th class="white">Aided Pilot</th>
						<th class="white">System</th>
						<th class="white">Locator</th>
						<th class="white">Rescuers</th>
					</tr>
				</thead>
				<tbody>
				<?php
				// pull all SAR rescues for specified period
				$db->query("SELECT id, LastUpdated, pilot, system, locateagent
							FROM rescuerequest
							WHERE status = 'closed-rescued' AND LastUpdated BETWEEN :start AND :end
							ORDER BY LastUpdated DESC");
				$db->bind(':start', $start);
				$db->bind(':end', $end);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					//$ctrtotact++;
					echo '<tr>';
					// add 4 hours to convert to UTC (EVE) for display; wonky during US-DST (should be 5 hours then)
					echo '<td class="white text-nowrap">'. 
							date("Y-m-d H:i:s", strtotime($value['LastUpdated'])+14400) .
						 '</td>';
					echo '<td><a target="_blank"
							href="https://evewho.com/pilot/'. $value['pilot'] .'">'.
							Output::htmlEncodeString($value['pilot']) .'</td>';
					echo '<td><a href="/esrc/rescueoverview.php?sys='. ucfirst($value['system']) .
							'" target="_blank">'. ucfirst($value['system']) .'</a></td>';
					if (!empty($value['locateagent'])) {
						echo '<td class="text-nowrap">
							<a target="_blank" href="/esrc/personal_stats.php?pilot='.
							urlencode($value['locateagent']) .'">'.
							$value['locateagent'] .'</a> - <a target="_blank"
							href="https://evewho.com/pilot/'. $value['locateagent'] .'">EG</a></td>';
					} 
					else {
						echo '<td>&nbsp;</td>';
					}
					// list rescuer(s)
					echo '<td class="white">';
						// pull all SAR rescues for specified period
						$db->query("SELECT pilot FROM rescueagents WHERE reqid = :id 
									ORDER BY EntryTime");
						$db->bind(':id', $value['id']);
						$arrRescuers = $db->resultset();
						foreach ($arrRescuers as $valr) {
							echo $valr["pilot"] .'<br />';
						}
					echo '</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?=gmdate('Y-m-d H:i:s', strtotime("now"))?> EVE<br /><br />
		</div>
	</div>
	<?php
	}
	//show payout data if "Payout" is checked
	else {			
	?>
	<div class="row" id="systable">
		<div class="col-sm-10">
			<table class="table" style="width: auto;">
				<thead>
					<tr>
						<th class="white">System</th>
						<th class="white">Locator</th>
						<th class="white">Rescuer</th>
						<th class="white">Payout</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// create instance object
					$user = new Users();
					//summary data
					$db->query("SELECT rr.id, rr.locateagent, rr.system, 
									datediff(rr.LastUpdated, rr.requestdate) AS daystosar, w.Class
								FROM rescuerequest rr, wh_systems w
								WHERE rr.system = w.System
								AND status = 'closed-rescued' 
								AND LastUpdated BETWEEN :start AND :end
								ORDER BY LastUpdated DESC");
					$db->bind(':start', $start);
					$db->bind(':end', $end);
					$rows = $db->resultset();
					$ctr = $totamt = 0;
					$basepay = 50000000; // base rate is 50 mil ISK
					$dailyincrease = 10000000; // daily increase is 10 mil ISK
					foreach ($rows as $value) {
						// skip record if no locateagent listed
						if (!empty($value['locateagent'])) {
						$ctr++;
						// get right-most number from WH class string for "WH Class multiplier"
						$whclassmult = intval(substr($value['Class'], -1));
						// max payout equation:
						// (base x WH class multiplier) + (Days until rescued x daily increase amt)
						$payoutmax = ($basepay*$whclassmult)+(intval($value['daystosar'])*$dailyincrease);
						echo '<tr>';
						echo '<td><a target="_blank"
								href="/esrc/rescueoverview.php?sys='. ucfirst($value['system']) .'">'.
								Output::htmlEncodeString(ucfirst($value['system'])) .'</a>
								<span class="white">(50mil x '. 
									intval(substr($value['Class'], -1)) .') + 
									(10mil x '. intval($value['daystosar']).')
								= '. number_format(intval($payoutmax)) .'</span></td>';
						echo '<td><a target="_blank" 
								href="https://evewho.com/pilot/'. $value['locateagent'] .'">'. 
								Output::htmlEncodeString($value['locateagent']) .'</a></td>';
						echo '<td>&nbsp;</td>';
						// Locator gets half of total payout amount; if ESR Coord, they get 0
						$payoutloc = intval($payoutmax/2);
						$actualpayloc = ($user->isSARCoordinator($value['locateagent']) === false) ?
							$payoutloc : 0;	
						echo '<td><input type="text" id="amt'.$ctr.'" width="100" value="'. 
								$actualpayloc .'" /><i id="copyclip" class="fa fa-clipboard" 
								onClick="SelectAllCopy(\'amt'.$ctr.'\')"></i></td>';
						$totamt = $totamt + $actualpayloc;
						echo '</tr>';
						}
						// pull all rescuers for specified system rescue, ordered oldest to newest
						$db->query("SELECT pilot FROM rescueagents WHERE reqid = :id
									ORDER BY EntryTime");
						$db->bind(':id', $value['id']);
						$arrRescuerPayout = $db->resultset();
						$payoutres = 0;
						foreach ($arrRescuerPayout as $val) {
							// do not pay Locator a second time
							if ($value['locateagent'] != $val['pilot']) {
								$ctr++;
								echo '<tr>';
								echo '<td></td><td></td>';
								echo '<td><a target="_blank" 
										href="https://evewho.com/pilot/'. $val['pilot'] .'">'. 
										Output::htmlEncodeString($val['pilot']) .'</a></td>';
								// first rescuer gets half of locator pay, then half again for each successive rescuer
								if ($payoutres == 0) {
									$payoutres = intval($payoutloc/2);
								} 
								else {
									$payoutres = intval($payoutres/2);
								}
								echo '<td><input type="text" id="amt'.$ctr.'" width="100" value="'. 
										intval($payoutres) .'" /><i id="copyclip" class="fa 
										fa-clipboard" onClick="SelectAllCopy(\'amt'.$ctr.'\')"></i></td>';
								$totamt = $totamt + $payoutres;
								echo '</tr>';
							}
						}
					}
					?>
					<tr>
						<td class="white" align="right">Participants:</td>
						<td class="white">&nbsp;<?=$ctr;?></td>
						<td class="white" align="right">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTAL: </td>
						<td class="white"><?=number_format($totamt);?> ISK</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
		</div>
	</div>
<?php
	}
?>
</div>

<script type="text/javascript">
	// datepicker
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    var startDate,
    endDate,
    updateStartDate = function() {
        startPicker.setStartRange(startDate);
        endPicker.setStartRange(startDate);
        endPicker.setMinDate(startDate);
    },
    updateEndDate = function() {
        startPicker.setEndRange(endDate);
        startPicker.setMaxDate(endDate);
        endPicker.setEndRange(endDate);
    },
    startPicker = new Pikaday({
        field: document.getElementById('start'),
        minDate: new Date('03/18/2017'),
        showMonthAfterYear: true,
        format: 'YYYY-MMM-DD',
        toString(date, format) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear() - 1898;
            return `YC${year}-${month}-${day}`;
        },
        onSelect: function() {
            startDate = this.getDate();
            updateStartDate();
        }
    }),
    endPicker = new Pikaday({
        field: document.getElementById('end'),
        minDate: new Date('03/18/2017'),
        showMonthAfterYear: true,
        format: 'YYYY-MMM-DD',
        toString(date, format) {
            const day = ("0" + date.getDate()).slice(-2);
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear() - 1898;
            return `YC${year}-${month}-${day}`;
        },
        onSelect: function() {
            endDate = this.getDate();
            updateEndDate();
        }
    }),
    _startDate = startPicker.getDate(),
    _endDate = endPicker.getDate();

    if (_startDate) {
        startDate = _startDate;
        updateStartDate();
    }

    if (_endDate) {
        endDate = _endDate;
        updateEndDate();
    }
</script>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
</script>

</body>
</html>