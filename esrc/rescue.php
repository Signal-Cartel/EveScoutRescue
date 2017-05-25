<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

?>
<html>

<head>
	<?php 
	$pgtitle = 'SAR new request';
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
<div class="ws">

<?php 
if (isset($dataOK) && $dataOK == FALSE)
{
?>
	<div class="red">
	Errors:<br />
	<?php 
	foreach ($error as $e)
	{
		echo Output::htmlEncodeString($e)."<br />";
	}
	?>	
	</div>
<?php 
}
?>

<form action="rescueaction.php" method="POST">
<p> System: <?php if (isset($system)) {?><input placeholder="System name" class="system tt-query black" type="text" autocomplete="off" name="system" value="<?=Output::htmlEncodeString($system)?>"/> <?php } else { ?> <input placeholder="System name" class="system tt-query black" type="text" name="system" class="system" autocomplete="off" /> <?php }?>
</p>
<p>Pilot: <input type="text" class="black" name="pilot"  placeholder="Pilot name" <?php if (isset($_REQUEST['pilot'])) { echo 'value="'.Output::htmlEncodeString($_REQUEST['pilot']).'"'; }?>/> &nbsp; Enter "Pilot Name (Contact Name)" if a intermediate contact is involved.
</p>
<p> 
Can refit: <input type="checkbox" name="canrefit" value="1" <?php if (isset($_REQUEST['canrefit'])) { echo 'checked="checked"'; } ?>/>
</p>
<p> 
Probe launcher: <input type="checkbox" name="launcher" value="1" <?php if (isset($_REQUEST['launcher'])) { echo 'checked="checked"'; } ?>/>
</p>
<p> 
Note: 					<textarea class="form-control black" id="notes" name="notes" rows="5"><?php if (isset($_REQUEST['notes'])) { echo $_REQUEST['notes']; }?></textarea>
</p>
<p>
Send: <input class="black" type="submit" name="action" value="Create"> &nbsp; Cancel: <input class="black" type="submit" name="action" value="View"> &nbsp; Show all <a href="./rescueoverview.php">active</a> requests.
</p>
</form>
</div>

</div>
</body>
</html>