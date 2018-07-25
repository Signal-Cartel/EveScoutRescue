<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/output.class.php';

// create object instance(s)
$db = new Database();
$lb = new Leaderboard($db);

$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : 0;
$medalid = isset($_REQUEST['medalid']) ? $_REQUEST['medalid'] : 1;
$min = isset($_REQUEST['min']) ? $_REQUEST['min'] : 100;
$max = isset($_REQUEST['max']) ? $_REQUEST['max'] : 299;
$arrow = '&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-left"></i>';

// HANDLE FORM SUBMIT
// add new medal for specified pilot
if ($rowid == -1) {
	// add new medal
	$db->query("INSERT INTO medals (pilot, medalid, dateawarded) VALUES (:pilot, :medalid, CURDATE())");
	$db->bind(':pilot', $_REQUEST['username']);
	$db->bind(':medalid', $_REQUEST['medalid']);
	$db->execute();
}
// edit existing medal
elseif ($rowid > 0) {
	// delete row
	if ($_REQUEST['del'] == 1) {
		$db->query("DELETE FROM medals WHERE id = :id");
		$db->bind(':id', $rowid);
		$db->execute();
	}
	// edit row
	else {
		$db->query("UPDATE medals SET medalid = :medalid, dateawarded = :dateawarded WHERE id = :id");
		$db->bind(':medalid', $_REQUEST['medalid']);
		$db->bind(':dateawarded', $_REQUEST['dateawarded']);
		$db->bind(':id', $rowid);
		$db->execute();
	}
}
?>
<html>

<head>
	<?php
	$pgtitle = 'Medals Admin';
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

		.tt-hint, .username {
			border: 2px solid #CCCCCC;
			border-radius: 8px 8px 8px 8px;
			font-size: .9em;
			height: 15px;
			line-height: 15px;
			outline: medium none;
			padding: 8px 4px;
			width: 100%;
		}

		.tt-dropdown-menu {
			width: 130%;
			margin-top: 5px;
			padding: 8px 0px;
			background-color: #fff;
			border: 1px solid #ccc;
			border: 1px solid rgba(0, 0, 0, 0.2);
			border-radius: 8px 8px 8px 8px;
			font-size: 100%;
			color: #111;
			background-color: #F1F1F1;
		}

		.tt-suggestion {
			padding: 3px 10px;
			font-size: 100%;
			line-height: 24px;
		}

		.tt-suggestion.tt-is-under-cursor { /* UPDATE: newer versions use .tt-suggestion.tt-cursor */
			color: #fff;
			background-color: #0097cf;

		}

		.tt-suggestion p {
			margin: 0;
		}
	-->
	</style>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Medals Admin</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	//show list if no ID is specified
	if (empty($_REQUEST['id'])) {	
	?>
    <div class="row">
        <div class="col-sm-10">
            <form method="post" action="medals_admin.php">
                <div class="form-group">
                    <input type="text" name="username" id="username" class="username" size="30" autoFocus="autoFocus" 
                        autocomplete="off" placeholder="Player Name">&nbsp;&nbsp;&nbsp
                    <input type="text" name="medalid" size="5" placeholder="Medal ID">&nbsp;&nbsp;&nbsp
                    <button type="submit" class="btn btn-md">Add New</button>
                    <input type="hidden" name="rowid" id="rowid" value="-1">
                </div>
            </form>
        </div>
    </div>
    <div class="ws"></div>
    <div class="row" id="systable">
		<div class="col-sm-5">
            <p class="sechead white">Medals Awarded</p>
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">Pilot</th>
                        <th class="white">Medal ID</th>
						<th class="white">Date Awarded</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$db->query("SELECT * FROM medals WHERE medalid = ". $medalid);
				$rows = $db->resultset();
				$db->closeQuery();
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white text-nowrap"><a href="?id=' . $value['id']. '">'. $value['id'] . '</a></td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="https://evewho.com/pilot/'. $value['pilot'] .'">'. 
							$value['pilot'] .'</a></td>';
                    echo '<td class="white text-nowrap">'. $value['medalid'] . '</td>';
					echo '<td class="white">' . $value['dateawarded'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
        <div class="col-sm-5">
            <p class="sechead white">Recent Qualifying Activity</p>
			<table id="example2" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Pilot</th>
						<th class="white">Count</th>
                        <th class="white">Last Action</th>
					</tr>
				</thead>
				<tbody>
				<?php
                // ESRC Medals
                if (intval($medalid) < 10) {
                    $db->query("SELECT COUNT(*) AS cnt, Pilot, max(ActivityDate) as act FROM activity
                        WHERE EntryType IN ('sower', 'tender') AND ActivityDate BETWEEN '2017-03-01' AND NOW()
                        GROUP BY Pilot ORDER BY cnt desc, act DESC");
                }
                // SAR Medals
                else {
                    $db->query("SELECT COUNT(ra.pilot) AS cnt, ra.pilot AS Pilot, MAX(ra.entrytime) AS act 
                        FROM rescuerequest rr, rescueagents ra 
						WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid
						GROUP BY ra.pilot ORDER BY cnt DESC, ra.entrytime DESC");
                }
				$rows = $db->resultset();
				$db->closeQuery();
				foreach ($rows as $value) {
                    if (intval($value['cnt']) < intval($min) || intval($value['cnt']) > intval($max)) { 
                        continue; 
                    }
					echo '<tr>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="https://evewho.com/pilot/'. $value['Pilot'] .'">'. 
							$value['Pilot'] .'</a></td>';
                    echo '<td class="white text-nowrap">'. $value['cnt'] . '</td>';
					echo '<td class="white">' . $value['act'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
            <p class="sechead white">Medals</p>
            1 - <a href="?medalid=1&min=100&max=299">SuperCacher</a><?php echo ($medalid==1)? $arrow : '' ?><br />
            2 - <a href="?medalid=2&min=300&max=499">MegaCacher</a><?php echo ($medalid==2)? $arrow : '' ?><br />
            3 - <a href="?medalid=3&min=500&max=999">HyperCacher</a><?php echo ($medalid==3)? $arrow : '' ?><br />
            4 - <a href="?medalid=4&min=1000&max=2999">UltraCacher</a><?php echo ($medalid==4)? $arrow : '' ?><br />
            5 - <a href="?medalid=5&min=3000&max=4999">Heroic Cacher</a><?php echo ($medalid==5)? $arrow : '' ?><br />
            6 - <a href="?medalid=6&min=5000&max=9999">Insane Cacher</a><?php echo ($medalid==6)? $arrow : '' ?><br />
            11 - <a href="?medalid=11&min=1&max=9">SAR Broanze</a><?php echo ($medalid==11)? $arrow : '' ?><br />
            12 - <a href="?medalid=12&min=10&max=49">SAR Silver</a><?php echo ($medalid==12)? $arrow : '' ?><br />
            13 - <a href="?medalid=13&min=50&max=99">SAR Gold</a><?php echo ($medalid==13)? $arrow : '' ?><br />
		</div>
	</div>
	<?php
	}
	//show detail/edit form if ID is specified
	else {	
	?>
	<div class="row">
		<div class="col-sm-12">
			<?php 
			$db->query("SELECT * FROM medals WHERE ID = :id");
			$db->bind(':id', $_REQUEST['id']);
			$row = $db->single();
			$db->closeQuery();
			?>
			
			<table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">Pilot</th>
						<th class="white">Medal ID</th>
						<th class="white">Date Awarded</th>
                        <th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<form name="editform" id="editform" action="medals_admin.php" method="POST">
							<td class="white text-nowrap"><?=$row['id']?></td>
							<td class="white text-nowrap"><?=$row['pilot']?></td>
							<td class="text-nowrap"><input name="medalid" type="text" value="<?=$row['medalid']?>"></td>
							<td class="text-nowrap"><input name="dateawarded" type="text" value="<?=$row['dateawarded']?>"></td>
							<td><button type="submit" class="btn">Update</button></td>
							<input type="hidden" name="rowid" id="rowid" value="<?=$_REQUEST['id']?>" />
                            <input type="hidden" name="pilot" id="pilot" value="<?=$row['pilot']?>" />
						</form>
					</tr>
					<tr><td colspan="6">&nbsp;</td></tr>
					<tr>
						<form name="delform" id="delform" action="medals_admin.php" method="POST">
							<td class="white text-nowrap"><a href="?">&lt;&lt; back</a></td>
							<td class="white text-nowrap">&nbsp;</td>
							<td class="text-nowrap">&nbsp;</td>
							<td class="text-nowrap">&nbsp;</td>
							<td><button type="submit" class="btn btn-danger">Delete</button></td>
							<input type="hidden" name="rowid" id="rowid" value="<?=$_REQUEST['id']?>" />
							<input type="hidden" name="del" id="del" value="1" />
						</form>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
<?php
	}
?>
</div>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}

	$(document).ready(function() {
        $('input.username').typeahead({
            name: 'username',
            remote: 'data_user_roles_lookup.php?query=%QUERY'
        });

		$('#example').DataTable( {
            "order": [[ 3, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });

        $('#example2').DataTable( {
            "order": [[ 2, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

</body>
</html>