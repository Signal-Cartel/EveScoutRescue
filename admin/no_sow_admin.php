<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';
require_once '../class/systems.class.php';

// create object instance(s)
$db = new Database();
$systems = new Systems($db);

// if start date is not set, set to default value
if (!isset($_REQUEST['start'])) {
	$start = gmdate('Y-m-d', strtotime("3 months"));
	$startPD = gmdate('Y-M-d', strtotime("3 months")); // formatted for Pikaday widget
}

// HANDLE FORM SUBMIT
// update No Sow date
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // set date to format we can use for database
	$arrDate = explode('-', $_REQUEST['nosowdate']);
	$startYear = intval(substr($arrDate[0], -3)) + 1898;
	$startMonth = intval(date('m', strtotime($arrDate[1])));
	$startDay = intval($arrDate[2]);
    $nosowdate = gmdate('Y-m-d', strtotime($startYear. '-' . $startMonth. '-' . $startDay));

    $system = $_REQUEST['system'];
    
	// update [wh_systems]
	$db->query("UPDATE wh_systems SET DoNotSowUntil = :nosowdate WHERE System = :system");
    $db->bind(':nosowdate', $nosowdate);
    $db->bind(':system', $system);
    $db->execute();

    // add new note
    $systems->addSystemNote($system, $charname, $_REQUEST['notes']);
}
?>
<html>

<head>
	<?php
	$pgtitle = 'No Sow Admin';
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
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">No Sow Admin</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<div class="row" id="systable">
		<div class="col-sm-5">
            <form method="post" action="no_sow_admin.php">
				<div class="form-group">
					<input type="text" name="system" id="system" class="system" size="30" autoFocus="autoFocus" 
						autocomplete="off" placeholder="System Name" onclick="this.select()"><br /><br />
                    <span class="white">Do Not Sow Until:</span> <input type="text" name="nosowdate" id="nosowdate" size="10"
                        value="<?php echo isset($startPD) ? $startPD : '' ?>" /><br />
                    <br /><span class="white">Note:</span><br />
                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea><br />
					<p class="text-center"><button type="submit" class="btn btn-md">Update</button></p>
				</div>
			</form>
		</div>
		<div class="col-sm-2 white"></div>
        <div class="col-sm-5">
            <div class="sechead white">Current No Sow Systems</div>
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">System</th>
						<th class="white">No Sow Until</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$db->query("SELECT * FROM wh_systems WHERE DoNotSowUntil > CURDATE() ORDER BY DoNotSowUntil DESC");
				$rows = $db->resultset();
				$db->closeQuery();
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white text-nowrap"><a target="_blank" href="/esrc/search.php?sys=' . $value['System']. '">'. 
                        $value['System'] . '</a></td>';
					echo '<td class="white text-nowrap">'. $value['DoNotSowUntil'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
        $(document).ready(function() {
            $('input.system').typeahead({
                name: 'system',
                remote: '../data/typeahead.php?type=system&query=%QUERY'
            });
        })

		$('#example').DataTable( {
            "order": [[ 1, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

<script type="text/javascript">
	// datepicker
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    var startDate,
    endDate,
    updateStartDate = function() {
        startPicker.setStartRange(startDate);
    },
    startPicker = new Pikaday({
        field: document.getElementById('nosowdate'),
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
    _startDate = startPicker.getDate();

    if (_startDate) {
        startDate = _startDate;
        updateStartDate();
    }
</script>

</body>
</html>