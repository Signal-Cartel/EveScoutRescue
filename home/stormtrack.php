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
            $rows = $storms->getStorms();
            if (empty($rows)) {
                echo 'No current reports';
            }
            else {  ?>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>System</th>
                            <th>Storm Type</th>
                            <th>Reported by</th>
                            <th>Last Report</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php
                    foreach($rows as $value) {
                        $dateobserved = date_create($value['dateobserved']);    ?>

                        <tr>
                            <td><?=$value['regionName']?></td>
                            <td><?=$value['evesystem']?></td>
                            <td><?=$value['stormstrength'] .' Metaliminal '. $value['stormtype'] .
                                ' Ray Storm' ?></td>
                            <td><?=$value['pilot']?></td>
                            <td><?=date_format($dateobserved, "M-d@H:i")?></td>
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
