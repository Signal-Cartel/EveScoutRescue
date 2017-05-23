<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';

require_once '../class/output.class.php';
require_once '../class/db.class.php';

?>

<html>

<head>
<?php
$pgtitle = 'SAR request update';
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
// prepare DB object
$database = new Database();
// create the query
$database->query("select * from rescuerequest where id = :rescueid");
$database->bind(":rescueid", $_REQUEST['request']);
$row = $database->single();
$database->closeQuery();
?>

<div>
<div>

<a href="./rescueaction.php?action=View">Overview all</a> <a href="./rescueaction.php?action=View&system=<?=Output::htmlEncodeString($row['system'])?>">Overview <?=Output::htmlEncodeString($row['system'])?></a> &nbsp; <a href="./rescueaction.php?action=New">New SAR</a>
</div>


<p>
<form method="POST" action="./rescueaction.php">
<table border="1">
<tr>
<th>Key</th><th>Value</th>
</tr>
<tr>
<td>Pilot</td><td><?=Output::htmlEncodeString($row['pilot'])?></td>
</tr>
<tr>
<td>System</td><td><?=Output::htmlEncodeString($row['system'])?></td>
</tr>
<tr>
<td>Creation date</td><td><?=Output::getEveDate($row['requestdate'])?></td>
</tr>
<tr>
<td>Creating agent</td><td><?=Output::htmlEncodeString($row['startagent'])?></td>
</tr>
<?php
// display closin agent only if status is closed
if ($row['status'] === 'closed')
{
?>
<tr>
<td>Closing agent</td><td><?=Output::htmlEncodeString($row['closeagent'])?></td>
</tr>
<?php 
}
?>
<tr>
<td>Last contacted</td><td><?=Output::getEveDate($row['lastcontact'])?></td>
</tr>
<tr>
<td>Contacted (check box if contacted)</td><td><input type="checkbox" name="contacted" value="1" /></td>
</tr>
<tr>
<td>Reminder</td><td><?=Output::getEveDate($row['reminderdate'])?></td>
</tr>
<tr>
<td>Remind me</td><td><input class="black" type="text" name="reminder" size="5" placeholder="days" /></td>
</tr>
<tr>
<td>Status</td><td>
<?php if ($row['status'] === 'new') { ?>
<input type="radio" id="status_new" name="status" value="new" checked="checked" /> <label for="status_new"> new</label><br />
<?php } ?>
<fieldset>
<input type="radio" id="status_open" name="status" value="open" <?php if ($row['status'] === 'open') { echo ' checked="checked" '; } ?>/> <label for="status_open"> open</label><br>
<input type="radio" id="status_closed" name="status" value="closed"  <?php if ($row['status'] === 'closed') { echo ' checked="checked" '; } ?>/> <label for="status_closed">closed</label><br>
<input type="radio" id="status_pending" name="status" value="pending"  <?php if ($row['status'] === 'pending') { echo ' checked="checked" '; } ?>/> <label for="status_pending">pending</label>
</fieldset>
 </td>
</tr>

</table>
<input type="hidden" name="request" value="<?=Output::htmlEncodeString($_REQUEST['request'])?>" />
<input type="hidden" name="action" value="UpdateRequest" />
<input type="submit" value="Update" />
</form>
</p>

<p>
<table border="1">
<tr>
<th colspan="3">Notes</th>
</tr>
<?php 
$database->query("select notedate, agent, note from rescuenote where rescueid = :rescueid order by notedate desc");
$database->bind(":rescueid", $_REQUEST['request']);
$rows = $database->resultset();
$database->closeQuery();
foreach($rows as $row)
{
?>
<tr>
<td><?=Output::getEveDate($row['notedate'])?></td><td><?=Output::htmlEncodeString($row['agent'])?></td><td><?=Output::htmlEncodeString($row['note'])?></td>
</tr>
<?php 
}
?>
</table>
</p>
<p>
Add a note<br />
<form method="POST" action="./rescueaction.php">
<p> 
Note: <textarea class="form-control black" id="notes" name="notes" rows="5"></textarea>
<input type="hidden" name="request" value="<?=Output::htmlEncodeString($_REQUEST['request'])?>" />
<input type="hidden" name="action" value="AddNote" />
<input type="submit" name="Click" value="Save" />
</p>



</form>

</p>

</div>

</div> <!-- div container -->
</body>
</html>
