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
            $rows = $storms->getRecentReports("public", true);
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
                    foreach($rows as $value) { ?>

                        <tr>
                            <td><?=$value['region_name']?></td>
                            <td><?=$value['system_name']?></td>
                            <td><?=Storms::getStormName($value['observation_type'])?></td>
                            <td><? $type = explode(' ', Storms::getStormName($value['observation_type'])); echo $type[0]; ?></td>
                            <td><? $date = new DateTime($value['created_at']); echo $date->format("M-d@H:i"); ?></td>
                            <td><?=$value['hours_in_system']?></td>
                            <td><?=$value['created_by_name']?></td>
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

<div class="row">
	<div class="col-sm-6 col-md-6">
        <div class="panel panel-info">
			<div class="panel-heading">
				<strong>Electric</strong>
			</div>
			<div class="panel-body">
                <ul>
                    <li>EM Resists Penalty</li>
                    <li>Capacitor Recharge Time Bonus</li>
                    <li>Virus Coherence Bonus</li>
                    <li>Scan Probe Strength Bonus</li>
                    <li>Cloaking Disabled</li>
                    <li>Spawns Extra Relic Sites</li>
                </ul>
            </div>
        </div>
        <div class="panel panel-success">
			<div class="panel-heading">
				<strong>Gamma</strong>
			</div>
			<div class="panel-body">
                <ul>
                    <li>Explosive Resists Penalty</li>
                    <li>Shield HP Bonus</li>
                    <li>Remote Shield/Armor Rep Amount Penalty</li>
                    <li>Warp Disruptor/Scrambler Range Bonus</li>
                    <li>Signature Radius Bonus</li>
                    <li>Spawns Extra Rogue Drone Sites</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="panel panel-primary">
			<div class="panel-heading">
				<strong>Exotic</strong>
			</div>
			<div class="panel-body">
                <ul>
                    <li>Kinetic Resists Penalty</li>
                    <li>Scan Resolution Bonus</li>
                    <li>Mining Laser Cycle Time Bonus</li>
                    <li>Warp Speed Bonus</li>
                    <li>Local Armor/Shield Repairer Cycle Time Bonus</li>
                    <li>Spawns Extra Ore Anomalies</li>
                </ul>
            </div>
        </div>
        <div class="panel panel-danger">
			<div class="panel-heading">
				<strong>Plasma</strong>
			</div>
			<div class="panel-body">
                <ul>
                    <li>Thermal Resists Penalty</li>
                    <li>Armor HP Bonus</li>
                    <li>Weapon Damage Bonus</li>
                    <li>Turret/Drone Tracking Penalty</li>
                    <li>Missile/Fighter Explosion Radius Penalty</li>
                    <li>Spawns Extra Triglavian Sites</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
