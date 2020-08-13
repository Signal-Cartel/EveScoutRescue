<?php
// set nav strings
$nav_esrc = '<a class="payout" href="payoutadmin.php">ESRC</a>';
$nav_sardisp = '<a class="payout" href="payoutadmin_sar.php">SAR Dispatch</a>';
$nav_sarloc = '<a class="payout" href="payoutadmin_sar_rescue.php">SAR Locate/Rescue</a>';
if (strpos($_SERVER['PHP_SELF'], 'payoutadmin.php') > 0) { $nav_esrc = 'ESRC'; }
if (strpos($_SERVER['PHP_SELF'], 'payoutadmin_sar.php') > 0) { $nav_sardisp = 'SAR Dispatch'; }
if (strpos($_SERVER['PHP_SELF'], 'payoutadmin_sar_rescue.php') > 0) { $nav_sarloc = 'SAR Locate/Rescue'; }

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

<style>
	table {
		table-layout: fixed;
		word-wrap: break-word;
	}
	a.payout,
	a.payout:visited {
		color: white;
		text-decoration: underline;
	}
	a.payout:hover {
		color: aqua;
		text-decoration: none;
	}
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

<p style="font-size: 125%; font-weight: bold; color: white;">Payouts:
    <?=$nav_esrc?> &gt;&gt; <?=$nav_sardisp?> &gt;&gt; <?=$nav_sarloc?>
</p>
<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
	<div class="input-daterange input-group" id="datepicker" style="margin-bottom: 5px;">
		<input type="text" class="input-sm form-control" name="start" id="start" 
			value="<?php echo isset($startPD) ? $startPD : '' ?>" />
		<span class="input-group-addon">to</span>
		<input type="text" class="input-sm form-control" name="end" id="end" 
			value="<?php echo isset($endPD) ? $endPD : '' ?>" />
	</div>
	<div class="checkbox">
		<label class="white"><input type="checkbox" name="payout" value="yes"> Payout</label>
	</div>
	&nbsp;&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-sm">Search</button>

    <?php
    // only display this field on ESRC Payout
    if ($nav_esrc == 'ESRC') {  ?>
    
        <br /><br />
        <label class="white">Total Weekly Payout (ISK): </label>
        <input type="text" name="totamt" value="<?=$_REQUEST['totamt'] ?? '500000000';?>">

    <?php
    }    ?>

</form>


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
