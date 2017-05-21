<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

?>
<html>

<head>
	<?php 
	$pgtitle = 'SAR request overview';
	include_once '../includes/head.php'; 
	?>
	<script>
        $(document).ready(function() {
            $('input.system').typeahead({
                name: 'system',
                remote: '../data/typeahead.php?type=system&query=%QUERY'
            });
            $('input.targetsystem').typeahead({
                name: 'targetsystem',
                remote: '../data/typeahead.php?type=system&query=%QUERY'
            });
        })
    </script>
</head>

<?php
require_once '../class/db.class.php';
require_once '../class/caches.class.php';
require_once '../class/systems.class.php';
require_once '../class/output.class.php';

$database = new Database();

// create a cache object instance
$caches = new Caches($database);

if (isset($_REQUEST['system'])) {
	$system = htmlspecialchars_decode($_REQUEST["system"]);
}

// create an update action

?>
<body class="white">
<div class="container">

<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 black" style="text-align: center;">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-5" style="text-align: left;">
				<form method="post" action="./search.php">
					<div class="form-group">
						<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" 
							autocomplete="off" class="targetsystem" placeholder="System Name" 
							value="<?php echo isset($system) ? $system : '' ?>">
					</div>
					<div class="clearit">
						<button type="submit" class="btn btn-md">Search</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<a href="data_entry.php" class="btn btn-info" role="button">Go to Data Entry</a>
					</div>
				</form>
			</div>
			<div class="col-sm-4"></div>
		</div>
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<?php 

function displayLine($row)
{
	echo "<tr>";
	echo "<td>".Output::getEveDate($row['requestdate'])."</td>";
	echo "<td>".Output::htmlEncodeString($row['system'])."</td>";
	echo "<td>".Output::htmlEncodeString($row['pilot'])."</td>";
	echo "<td>".Output::htmlEncodeString($row['canrefit'])."</td>";
	echo "<td><a href=\"./rescueaction.php?action=Edit&request=".$row['id']."\">Manage the request</a></td>";
	echo "</tr>";
}

// get finished status parameter flag
$finished = $_REQUEST['finished'];
if (!isset($finished))
{
	$finished = 0;
}

// get requests from database
$database->query("select id, requestdate, system, pilot, canrefit, finished from rescuerequest where finished = :finished order by requestdate");
$database->bind(":finished", $finished);
// $database->execute();
$data = $database->resultset();
// echo "<pre>";
// print_r($database);
// echo "\n";
// print_r($data);
// echo "</pre>";
$database->closeQuery();
if (isset($data))
{
?>

<div>
<a href="./rescueaction.php?action=New">New SAR request</a>
</div>

<div>
<p>
<?php
if ($finished == 0)
{
?>
View active requests. Display <a href="./rescueoverview.php?finished=1">finished</a> requests.
<?php 	
}
else 
{
?>
View finished requests. Display <a href="./rescueoverview.php?finished=0">active</a> requests.
<?php
}
?>
</p>
<table width="90%">
<tr>
<th>Started</th><th>System</th><th>Pilot</th><th>refit</th><th>Manage</th>
</tr>
<?php 
	foreach ($data as $row) {
		if (isset($_REQUEST['system']))
		{
			if ($_REQUEST['system'] == $row['system'])
			{
				displayLine($row);
			}
		}
		else
		{
			displayLine($row);
		}
		
	}
}
else
{
	echo "No active rescure requests";
}
?>
</table>

</div>


</div>
</body>
</html>