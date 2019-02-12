<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

// for debug only
/*
 error_reporting(E_ALL);
 ini_set('display_errors', 'on');
*/

include_once '../includes/auth-inc.php'; 
require_once '../class/db.class.php';
require_once '../class/pilot.class.php';
require_once '../class/output.class.php';

$database = new Database();
$pilot = new Pilot($database);
$rowsBronze = $pilot->getMedals('11');
$rowsSilver = $pilot->getMedals('12');
$rowsGold = $pilot->getMedals('13');
?>
<html>

<head>
	<?php
	$pgtitle = "Hall of Heroes";
	include_once '../includes/head.php';
	?>
</head>

<body class="white">
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
<?php
include_once '../includes/top-left.php';
include_once '../includes/top-center.php';
include_once '../includes/top-right.php';
?>
</div>
<div class="ws"></div>

<?php 
/**
 * Prepare HTML for list of SAR medal recipients
 * @param unknown $type the type of hero list
 * @param int $min the minimum number of rescues at this level
 * @param int $max the maximum number of rescues at this level
 * @param int $listMax the maximum number of pilots to list
 * @return prepared HTML for a certain hero category
 */
function printSARHeroes($type, $min, $arrPilotCnt) 
{ 
?>
	<div class="col-sm-4">
		<h2 style="text-align: center;"><?=$type?></h2>
		<p style="text-align: center;">Awarded to pilots upon completing 
			<?=$min?> successful rescue <?php echo ($min == 1 ? 'mission' : 'missions');?>.<br />
			<?php 
			$filename = '../img/'. $type .'.PNG';
			if (file_exists($filename)) {
				echo '<img src="'. $filename .'" height="228">';
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
			foreach ($arrPilotCnt as $value) {
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
	// Gold Medal column
	printSARHeroes('Gold Lifesaver', 50, $rowsGold);
	
	// Silver Medal column
	printSARHeroes('Silver Lifesaver', 10, $rowsSilver);
	
	// Bronze Medal column
	printSARHeroes('Bronze Lifesaver', 1, $rowsBronze);
	?>
</div>

</div>
</body>
</html>

<?php 
function debug($variable){
	if(is_array($variable)){
		echo "<pre>";
		print_r($variable);
		echo "</pre>";
		exit();
	}
	else{
		echo ($variable);
		exit();
	}
}
?>