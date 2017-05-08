<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

?>
<html>

<head>
	<?php 
	$pgtitle = 'Search';
	include_once '../includes/head.php'; 
	?>
	<script>
        $(document).ready(function() {
            $('input.system').typeahead({
                name: 'system',
                remote: '../data/typeahead.php?type=system&query=%QUERY'
            });
        })
        $(document).ready(function() {
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
	$targetsystem = htmlspecialchars_decode($_REQUEST["system"]);
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] === 'Create')
{
	// add value checks!
	// system ok
	// all values are set
	// same request is not active (system, pilot)
	
	$database->beginTransaction();
	$database->query("insert into rescuerequest(system, pilot, canrefit, note) values(:system, :pilot, :canrefit, :note)");
	$database->bind(":system", $_REQUEST['system']);
	$database->bind(":pilot", $_REQUEST['pilot']);
	$database->bind(":canrefit", $_REQUEST['canrefit']);
	$database->bind(":note", $_REQUEST['notes']);
	
	$database->execute();
	
	$database->endTransaction();
	
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
				<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
					<div class="form-group">
						<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" 
							autocomplete="off" class="targetsystem" placeholder="System Name" 
							value="<?php echo isset($targetsystem) ? $targetsystem : '' ?>">
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
<div class="ws"></div>
<form target="rescue.php" method="POST">
System: <?php if (isset($targetsystem)) {?><input class="black" type="text" autocomplete="off" name="system" value="<?=Output::htmlEncodeString($targetsystem)?>"/> <?php } else { ?> <input class="black" type="text" name="system" autocomplete="off" /> <?php }?> 
Pilot: <input type="text" class="black" name="pilot"  /> 
Can refit: <select class="black" name="canrefit" ><option value="0" default>No</option><option value="1">Yes</option></select> 
Note: 					<textarea class="form-control black" id="notes" name="notes" rows="3"></textarea>
Send: <input class="black" type="submit" name="action" value="Create"> 

</form>


</div>
</body>
</html>