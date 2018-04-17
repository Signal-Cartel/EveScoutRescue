<?php 
require_once '../class/output.class.php';
// get all approved testimonials
$database->query("SELECT * FROM testimonials WHERE Approved = 1 ORDER BY RescueDate DESC");
// get the result line
$arrTest = $database->resultset();
// close the query
$database->closeQuery();

$ctr = 0;
foreach ($arrTest as $val) {
	$ctr++;
	$act = $ctr == 1 ? ' active' : '';
	echo '<div class="item'. $act . '">';
	echo '<div class="carousel-content">';
	echo '<div style="width: 100%;">';
	// handle long notes
	$strNote = $val['Note'];
	$maxlen = 650;
	$len = strlen($val['Note']);
	if ($len > $maxlen) {
		$strNote = substr($val['Note'], 0, $maxlen) . '...<a href="testimonials_list.php#' . 
			$val['ID'] . '">more</a>';
	}
	echo $strNote. '<br /><br />';
	echo '<span class="pull-right" style="text-align: right;">';
	// handle anonymous pilots
	echo $val['Anon'] == 1 ? 'anonymous pilot' : $val['Pilot'];
	echo '<br />';
	echo '<span style="font-size: 85%;">rescued on ' . Output::getEveDate($val['RescueDate']) . '</span>';
	echo '</span></div></div></div>';
	
	if ($ctr == 5) { break; }
}
?>