<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';

// create object instance(s)
$db = new Database();
?>
<html>

<head>
	<?php
	$pgtitle = 'Individual User Stats';
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
			<span style="font-size: 125%; font-weight: bold; color: white;">Individual User Stats</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	//show list if no user is specified
	if (empty($_REQUEST['username'])) {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-12">
            <form method="post" action="user_stats.php">
				<div class="form-group">
					<input type="text" name="username" id="username" class="username" size="30" autoFocus="autoFocus" 
						autocomplete="off" placeholder="Player Name">&nbsp;&nbsp;&nbsp
					<button type="submit" class="btn btn-md">Look Up</button>
				</div>
			</form>			
		</div>
	</div>
	<?php
	}
	//show report if user is specified
	else {	
	?>
	<div class="row">
		<div class="col-sm-3">
			<?php 
            
			$db->query("SELECT COUNT(DISTINCT(System)) as cnt FROM `activity` WHERE Pilot = :username");
			$db->bind(':username', $_REQUEST['username']);
			$totcnt = $db->single();
			$db->closeQuery();

			$db->query("SELECT System, COUNT(System) as cnt FROM `activity` WHERE Pilot = :username GROUP BY System ORDER BY cnt DESC");
			$db->bind(':username', $_REQUEST['username']);
			$row = $db->resultset();
			$db->closeQuery();
			?>
			<p class="white">Unique systems visited: <?=$totcnt['cnt']?></p>
			<table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">System</th>
						<th class="white">Count</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    foreach ($row as $value) {
                        echo '<tr>';
                        echo '<td class="white text-nowrap">'. $value['System'] .'</td>';
                        echo '<td class="white text-nowrap">'. $value['cnt'] .'</td>';
                        echo '</tr>';
                    }
                    ?>
				</tbody>
			</table>
		</div>
        <div class="col-sm-9">
			<?php 
            
			$db->query("select Pilot, count(*) as ActionsPerDay, DATE(ActivityDate) as daydate
                        from activity
                        where Pilot = :username
                        group by Pilot, DATE(ActivityDate)
                        ORDER BY ActionsPerDay DESC
                        LIMIT 5");
			$db->bind(':username', $_REQUEST['username']);
			$mostday = $db->resultset();
			$db->closeQuery();

			$db->query("select Pilot, count(*) as ActionsPerWeek, yearweek(ActivityDate) as weekdate
                        from activity
                        where Pilot = :username
                        group by Pilot, yearweek(ActivityDate)
                        ORDER BY ActionsPerWeek DESC
                        LIMIT 5");
			$db->bind(':username', $_REQUEST['username']);
			$mostweek = $db->resultset();
			$db->closeQuery();
			?>
			<table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Actions/Day</th>
						<th class="white">Date</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    foreach ($mostday as $value) {
                        echo '<tr>';
                        echo '<td class="white text-nowrap">'. $value['ActionsPerDay'] .'</td>';
                        echo '<td class="white text-nowrap">'. $value['daydate'] .'</td>';
                        echo '</tr>';
                    }
                    ?>
				</tbody>
			</table>
            <div class="ws"></div>
            <table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Actions/Week</th>
						<th class="white">Week #</th>
					</tr>
				</thead>
				<tbody>
                    <?php
                    foreach ($mostweek as $value) {
                        echo '<tr>';
                        echo '<td class="white text-nowrap">'. $value['ActionsPerWeek'] .'</td>';
                        echo '<td class="white text-nowrap">'. $value['weekdate'] .'</td>';
                        echo '</tr>';
                    }
                    ?>
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
            "order": [[ 0, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

</body>
</html>