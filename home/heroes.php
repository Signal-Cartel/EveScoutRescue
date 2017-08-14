<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
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
 * Prepare HTML for list of ESRC hall of famers
 * @param unknown $type the type of hero list
 * @param int $min the minimum number of caches at this level
 * @param int $max the maximum number of caches at this level
 * @param int $listMax the maximum number of pilots to list
 * @return prepared HTML for a certain hero category
 */
function printESRCHeroes($type, $min, $max, $listMax) 
{ 
	$leaderBoard = new Leaderboard($database);
?>
	<div class="col-sm-3">
		<h2 style="text-align: center;"><?=$type?></h2>
		<p style="text-align: center;">Awarded to pilots upon sowing or tending 
			<?=$min?> rescue caches.<br />
			<?php 
			$filename = '../img/'. $type .'.PNG';
			if (file_exists($filename)) {
				echo '<img src="'. $filename .'">';
			}
			?>
		</p>
		<table class="table">
			<thead>
				<tr>
					<th>Pilot</th>
					<th>Caches</th>
				</tr>
			</thead>
		<tbody>
			<?php
			$rows = $leaderBoard->getAllHigh(intval($listMax));
			
			foreach ($rows as $value) {
				if (intval($value['cnt']) < intval($min) || intval($value['cnt']) > intval($max)) { 
					continue; 
				}
				echo '<tr>';
				echo '	<td>'. $value['Pilot'] .'</td>';
				echo '	<td align="right">'. $value['cnt'] .'</td>';
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
	// SuperCacher column
	printESRCHeroes('SuperCacher', 100, 299, 100);
	
	// MegaCacher column
	printESRCHeroes('MegaCacher', 300, 499, 30);
	
	// HyperCacher column
	printESRCHeroes('HyperCacher', 500, 999, 10);
	
	// UltraCacher column
	printESRCHeroes('UltraCacher', 1000, 2999, 10);
	
	// HeroCacher column
	//printESRCHeroes('HeroCacher', 3000, 4999, 5);
	
	// InsaneCacher column
	//printESRCHeroes('HeroCacher', 5000, 9999, 5);
	?>
</div>

</div>
</body>
</html>