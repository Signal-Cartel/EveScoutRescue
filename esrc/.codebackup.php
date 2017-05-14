// display table

<?php 

die();

$database->query("select id, requestdate, system, pilot, canrefit from rescuerequest where finished = 0 order by requestdate");
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
<table width="90%">
<tr>
<th>Started</th><th>System</th><th>Pilot</th><th>refit</th><th>Manage</th>
</tr>
<?php 
	foreach ($data as $row) {
		echo "<tr>";
		echo "<td>".Output::getEveDate($row['requestdate'])."</td>";
		echo "<td>".Output::htmlEncodeString($row['system'])."</td>";
		echo "<td>".Output::htmlEncodeString($row['pilot'])."</td>";
		echo "<td>".Output::htmlEncodeString($row['canrefit'])."</td>";
		echo "<td><a href=\"./rescueactions.php?action=View&request=".$row['id']."\">Manage the request</a></td>";
		echo "</tr>";
	}
}
else
{
	echo "No active rescure requests";
}
?>
</table>

</div>


--------------------

