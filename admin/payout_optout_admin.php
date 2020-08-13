<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
//require_once '../class/db.class.php';
//require_once '../class/systems.class.php';

// create object instance(s)
$db = new Database();
$users = new Users($db);

// HANDLE FORM SUBMIT
// update No Sow date
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // prep user submitted value for db entry
    $username = Output::prepTextarea($_REQUEST['username']);
    $formAction = $_POST['action'] ?? '';

    // remove pilot from opt out, if requested
    if ($formAction == 'RemovePilot') {
        $db->query("DELETE FROM payout_optout WHERE id = :id");
        $db->bind(':id', $_POST['rowid']);
        $db->execute();
    }
    // otherwise, insert new pilot
    else {
        $db->query("INSERT INTO payout_optout (pilot, optout_type) VALUES (:pilot, :optout_type)");
        $db->bind(':pilot', $username);
        $db->bind(':optout_type', $_REQUEST['optout_type']);
        $db->execute();
    }
}
?>
<html>

<head>
	<?php
	$pgtitle = 'ESRC Payout Opt-out Admin';
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
			<span style="font-size: 125%; font-weight: bold; color: white;"><?=$pgtitle?></span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<div class="row" id="systable">
		<div class="col-sm-5">
            <form method="post" action="<?=htmlentities($_SERVER['PHP_SELF'])?>">
				<div class="form-group">
                    <input type="text" name="username" id="username" class="username" size="30" 
                        autoFocus="autoFocus" autocomplete="off" placeholder="Player Name"><br /><br />
                    <span class="white">Opt-out Type:</span> 
                    <select name="optout_type">
                        <option value="ESRC">ESRC</option>
						<option value="Stats">Stats</option>
                    </select>
                    <br /><br />
					<p class="text-center"><button type="submit" class="btn btn-md">Update</button></p>
				</div>
			</form>
		</div>
		<div class="col-sm-2 white"></div>
        <div class="col-sm-5">
            <div class="sechead white">Opted Out</div>
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Pilot</th>
                        <th class="white">Type</th>
                        <th></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$db->query("SELECT * FROM payout_optout ORDER BY optout_type, pilot");
				$rows = $db->resultset();
				$db->closeQuery();
				foreach ($rows as $value) {
					echo '<tr>';
					echo '  <td class="white text-nowrap">'. $value['pilot'] .'</a></td>';
                    echo '  <td class="white text-nowrap">'. $value['optout_type'] .'</a></td>';
                    echo '  <td>
                                <form method="post" class="form-inline" id="user_del'. $value['id'] .'" 
                                    action="'. htmlentities($_SERVER['PHP_SELF']) .'">
                                    <input type="hidden" name="rowid" value="'. $value['id'] .'">
				                    <input type="hidden" name="action" value="RemovePilot">
                                    <button type="submit" class="btn btn-xs btn-danger">X</button>
				                </form>
                            </td>';
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
        $('input.username').typeahead({
            name: 'username',
            remote: 'data_user_roles_lookup.php?query=%QUERY'
        });

		$('#example').DataTable( {
            "order": [[ 2, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

</body>
</html>