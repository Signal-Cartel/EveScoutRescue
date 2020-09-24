<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$storms = new Storms($database);
$pgtitle = "Storm Track";



// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left"><?=$pgtitle?></h2>
			</div>
			<div class="panel-body">

            <?php
            $rows = $storms->getRecentReports();
            if (empty($rows)) {
                echo 'No current reports';
            }
            else {  ?>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>System</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Last Report</th>
                            <th>Hours in System</th>
                            <th>Reported by</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach($rows as $value) {
                        $dateobserved = date_create($value['dateobserved']);    ?>

                        <tr>
                            <td><?=$value['regionName']?></td>
                            <td><?=$value['evesystem']?></td>
                            <td><?=Storms::getStormName($value['storm_id'])?></td>
                            <td><?=$value['stormtype']?></td>
                            <td><?=date_format($dateobserved, "M-d@H:i")?></td>
                            <td><?=$value['hours_in_system']?></td>
                            <td><?=$value['pilot']?></td>
                        </tr>

                    <?php
                    }   ?>

                    </tbody>
                </table>

                <?php
            }   ?>

			</div>
		</div>
	</div>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
