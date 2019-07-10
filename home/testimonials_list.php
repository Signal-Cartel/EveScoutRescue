<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 
require_once '../class/output.class.php';
require_once '../class/db.class.php';
$database = new Database();

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$sort = isset($_REQUEST['sort']) ? $_REQUEST['sort'] : 'DESC';

// check for querystring hacking
if ($type != 'ESRC' && $type != 'SAR') {
	$type = '';
}
if ($sort != 'ASC' && $sort != 'DESC') {
	$sort = 'DESC';
}
?>
<html>

<head>
	<?php
	$pgtitle = $type . " Testimonials";
	include_once '../includes/head.php';
	?>
</head>
<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
<?php
include_once '../includes/top-left.php';
include_once '../includes/top-center.php';
include_once '../includes/top-right.php';
?>
</div>
<div class="ws"></div>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">Testimonials from Rescued Pilots</h2>
				<span class="pull-right">
					Type: <a href="?type=ESRC&sort=<?=$sort?>">ESRC</a> 
						<a href="?type=SAR&sort=<?=$sort?>">SAR</a> 
						<a href="?type=&sort=<?=$sort?>">Both</a><br /><br />
					Sort: <a href="?sort=ASC&type=<?=$type?>">Oldest First</a> 
						<a href="?sort=DESC&type=<?=$type?>">Newest First</a></span>
			</div>
			<div class="panel-body">
<?php 
// set the query based on querystring parameter
if (!empty($type)) {
	$database->query("SELECT * FROM testimonials WHERE Approved = 1 AND Type = :type 
		ORDER BY RescueDate $sort");
	$database->bind(":type", $type);
}
else {
	$database->query("SELECT * FROM testimonials WHERE Approved = 1 ORDER BY RescueDate $sort");
}
// get the result line
$arrTest = $database->resultset();
// close the query
$database->closeQuery();

foreach ($arrTest as $val) {
	echo '<a name="' . $val['ID'] . '"></a> <p><strong>';
	// handle anonymous pilots
	echo $val['Anon'] == 1 ? 'anonymous pilot' : $val['Pilot'];
	echo '</strong>&nbsp;&nbsp;<span style="font-size: 85%;">rescued on ' . 
		Output::getEveDate($val['RescueDate']) . '</span><br />';
	echo $val['Note'] . '</p><br /><br />';
}
?>
			</div>
		</div>
	</div>
</div>

</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>