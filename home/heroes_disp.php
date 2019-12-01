<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 
require_once '../class/db.class.php';
require_once '../class/pilot.class.php';
require_once '../class/output.class.php';

$database = new Database();
$pilot = new Pilot($database);
$rowsQual = $pilot->getMedals('21');
$rowsProf = $pilot->getMedals('22');
$rowsMaster = $pilot->getMedals('23');
?>
<html>

<head>
	<?php
	$pgtitle = "Hall of Dispatch Heroes";
	include_once '../includes/head.php';
	?>
</head>

<body class="white">
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
<?php
include_once '../includes/top-right.php';
include_once '../includes/top-left.php';
include_once '../includes/top-center.php';

?>
</div>
<div class="ws"></div>

<?php 
/**
 * Prepare HTML for list of ESRC hall of famers
 * @param unknown $type the type of hero list
 * @param int $min the minimum number of caches at this level
 * @param int $max the maximum number of caches at this level
 * @param int $listMax the maximum number of pilots to list
 * @return prepared HTML for a certain hero category
 */
function printESRCHeroes($type, $min, $rows) 
{ 
?>
	<div class="col-md-4">
		<h2 style="text-align: center;"><?=$type?></h2>
		<p style="text-align: center;">Awarded to a Signaleer for coming to the aid of  
			<?=$min?> or more stranded capsuleers.<br />
			<?php 
			$filename = '../img/'. $type .'.PNG';
			if (file_exists($filename)) {
				echo '<img height="216" src="'. $filename .'">';
			}
			?>
		</p>
		<table class="table">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Date Awarded</th>
				</tr>
			</thead>
		<tbody>
			<?php
			foreach ($rows as $value) {
				echo '<tr>';
				echo '	<td>'. $value['pilot'] .'</td>';
				echo '	<td>'. Output::getEVEdate($value['dateawarded']) .'</td>';
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
	</div>
<?php 
}
?>

<div class="row">
	<?php 
	// Master column
	printESRCHeroes('Master Dispatcher', 100, $rowsMaster);

	// Proficient column
	printESRCHeroes('Proficient Dispatcher', 50, $rowsProf);

	// Qualified column
	printESRCHeroes('Qualified Dispatcher', 5, $rowsQual);
	?>
</div>

</div>
</body>
</html>