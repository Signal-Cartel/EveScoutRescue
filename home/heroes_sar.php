<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 
require_once '../class/db.class.php';
require_once '../class/leaderboard_sar.class.php';
require_once '../class/users.class.php';

$database = new Database();
$users = new Users($database);
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
function printSARHeroes($type, $min, $max, $arrPilotCnt) 
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
					<th>Rescues</th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ($arrPilotCnt as $value) {
				if (intval($value['cnt']) < intval($min) || intval($value['cnt']) > intval($max)) { 
					continue; 
				}
				echo '<tr>';
				echo '	<td>'. $value['pilot'] .'</td>';
				echo '	<td align="right">'. $value['cnt'] .'</td>';
				echo '</tr>';
			}
			?>
			</tbody>
		</table>
	</div>
<?php 
}

$leaderBoard = new SARLeaderboard($database);
$rows = $leaderBoard->getTopRescueAgents();
?>

<div class="row">
	<?php 
	// Bronze Medal column
	printSARHeroes('Bronze Lifesaver', 1, 9, $rows);
	
	// Silver Medal column
	printSARHeroes('Silver Lifesaver', 10, 49, $rows);
	
	// Gold Medal column
	//printSARHeroes('Gold Lifesaver', 50, 999, $rows);
	?>
</div>

</div>
</body>
</html>