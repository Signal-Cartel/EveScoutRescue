<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$testimonials = new Testimonials($database);
$type = $_REQUEST['type'] ?? 'ESRC';
$sort = $_REQUEST['sort'] ?? 'DESC';
$pgtitle = $type . " Testimonials";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left"><?=$pgtitle?></h2>
				<span class="pull-right">
					Type: <a href="?type=ESRC&sort=<?=$sort?>">ESRC</a> - 
						<a href="?type=SAR&sort=<?=$sort?>">SAR</a><br /><br />
					Sort: <a href="?sort=ASC&type=<?=$type?>">Oldest First</a> - 
						<a href="?sort=DESC&type=<?=$type?>">Newest First</a>
				</span>
			</div>
			<div class="panel-body">

			<?php 
			// get testimonials
			$arrTest = $testimonials->getTestimonials($type, $sort, 1, 1000);
			
			foreach ($arrTest as $val) {
				// handle anonymous pilots
				$strPilot = ($val['Anon'] == 1) ? 'anonymous pilot' : $val['Pilot'];	?>
				
				<p><strong><?=$strPilot?></strong>&nbsp;&nbsp;<span style="font-size: 85%;">rescued 
					on <?=Output::getEveDate($val['RescueDate'])?></span><br>
					<?=$val['Note']?></p><br>
			
				<?php
			}	?>

			</div>
		</div>
	</div>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
