<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$leaderBoard = new Leaderboard($database);
$users = new Users($database);
$arrESRTeam = $users->getUsersByRole("'2', '3'", true, true);
$arrParticipants = $leaderBoard->getActivePilots(30);
$pgtitle = "About Us";


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<!-- LEFT COLUMN -->
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">About Us</h2>
				<div class="btn-group pull-right" style="padding-top: 12px;">
			        <a class="btn btn-success btn-lg" 
			        	href="https://www.signalcartel.org/?s=EvE-Scout+Rescue" role="button">
			        	Rescue success stories!</a>
			    </div>
			</div>
			<div class="panel-body">
				<p>In early YC118, <a href="https://www.signalcartel.org/">Signal Cartel</a> member 
					<a href="https://evewho.com/pilot/Forcha%20Alendare">Forcha Alendare</a> had a vision of a service that would provide a basic 
					emergency resource kit for capsuleers stranded in wormholes, regardless of alliance, sovereignty, or play style. This vision captured the 
					imagination of many <a href="https://www.signalcartel.org/">Signal Cartel</a> pilots and grew to become the Rescue Cache program we know today. 
					</p>
				<p>In late YC118, Forcha decided to move on to activities that would potentially 
					bring him into conflict with the 
					<a href="https://wiki.signalcartel.space/Public:About_Signal_Cartel">Credo</a>, so he chose to  
					leave the corporation and alliance leadership brought in 
					<a href="https://evewho.com/pilot/Thrice%20Hapus">Thrice 
					Hapus</a> to manage day-to-day operations.</p>
				<p>Since then, the program has grown to become an entire division within <a href="https://www.signalcartel.org/">Signal Cartel</a> and 
					encompasses both live Search and Rescue operations as well as the original Rescue Cache 
					program.</p>
				<p>When Thrice Hapus became CEO of Signal Cartel, <a href="https://evewho.com/pilot/Igaze">Igaze</a> 
					took over EvE Scout Rescue and guided the division from December YC120 until April YC123. Under his watch the division set many new records for number of rescues, caches in space, and 911 call volume. Then in YC123, <a href="https://evewho.com/pilot/Captain%20Crinkle">Captain Crinkle</a>, one of our most experienced rescue pilots, took the helm. Crinkle guided the division until May of YC124 when our current ESRC Manager, <a href="https://evewho.com/character/2112425049">Xalyar</a> assumed the lead role. While he oversees the division as a whole, it takes an entire team to make it all happen. Our current roster includes the following dedicated rescue pilots.</p>

				<!-- 911 Operators -->
				<div class="row">
				  <h2 style="text-align:center">911 Operators</h2>
				</div>
				<div class="row">

				<?php 
				$i = 0;
				foreach ($arrESRTeam as $val) {
					if ($val['roleid'] != '3') { continue; }	// skip row if not '911 Operator'
					
					$i++;
					if ($i == 7) {	// every six loops, close last row and start a new one	
						$i = 1;
					?>
						
						</div>
						<div class="row">

						<?php	
					}	?>
					
					<div class="col-md-2">
						<div class="thumbnail text-center">
							<a href="https://evewho.com/pilot/<?=urlencode($val['username'])?>">
								<img src="https://image.eveonline.com/Character/<?=urlencode($val['characterid'])?>_512.jpg" 
									alt="<?=urlencode($val['username'])?>" style="width:100%">
								<div class="caption"><strong><?=$val['username']?></strong></div>
							</a>
						</div>
					</div>
					
					<?php					
				}
				?>

				</div>

				<!-- ESR Coordinators -->
				<div class="row">
					<h2 style="text-align:center">EvE-Scout Rescue Coordinators</h2>
				</div>
				<div class="row">

				<?php 
				$i = 0;
				foreach ($arrESRTeam as $val) {
					if ($val['roleid'] != '2') { continue; }	// skip row if not 'ESR Coordinator'
					$i++;
					
					if ($i == 5) {	// every four loops, close last row and start a new one	
						$i=1;
					?>
						</div>
						<div class="row">

					<?php
					}	
					?>
					
					<div class="col-md-3">
						<div class="thumbnail text-center">
							<a href="https://evewho.com/pilot/<?=urlencode($val['username'])?>">
								<img src="https://image.eveonline.com/Character/<?=urlencode($val['characterid'])?>_512.jpg" 
									alt="<?=urlencode($val['username'])?>" style="width:100%">
								<div class="caption"><strong><?=$val['username']?></strong></div>
							</a>
						</div>
					</div>
					
					<?php
				}
				?>

				</div>

				<!-- Division Leadership -->
				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-4">
						<h3 style="text-align:center">Asst. Manager</h3>
						<div class="thumbnail text-center">
							<a href="https://evewho.com/character/2113316760">
								<img src="https://image.eveonline.com/Character/2113316760_512.jpg" 
									alt="Catbriar Chelien" style="width:80%">
								<div class="caption"><strong>Catbriar Chelien</strong></div>
							</a>
						</div>
				  	</div>
					<div class="col-md-2"></div>
				  	<div class="col-md-4 text-center">
						<h3 style="text-align:center">Asst. Manager</h3>
						<div class="thumbnail text-center">
							<a href="https://evewho.com/character/2114254786">
								<img src="https://image.eveonline.com/Character/2114254786_512.jpg" 
									alt="Tekufah" style="width:80%">
								<div class="caption"><strong>Tekufah</strong></div>
							</a>
						</div>
				  	</div>
				  	<div class="col-md-1"></div>
				</div>

				<div class="row">
					<div class="col-md-1"></div>
					<div class="col-md-4" style="margin-top: 24px;">
						<h3 style="text-align:center">
							<a href="https://www.evescoutrescue.com/copilot">ALLISON</a>
						</h3>
						<div class="thumbnail text-center">
							<a href="https://evewho.com/pilot/A%20Dead%20Parrot">
								<img src="https://image.eveonline.com/Character/96765374_512.jpg" 
									alt="A.D. Parrot" style="width:80%">
								<div class="caption"><strong>A Dead Parrot</strong></div>
							</a>
						</div>
				  	</div>
					<div class="col-md-2"></div>
				  	<div class="col-md-4 text-center">
						<h2 style="text-align:center">Manager</h2>
						<div class="thumbnail text-center">
							<a href="https://evewho.com/character/2112425049">
								<img src="https://image.eveonline.com/Character/2112425049_512.jpg" 
									alt="Xalyar" style="width:100%">
								<div class="caption"><strong>Xalyar</strong></div>
							</a>
						</div>
				  	</div>
				  	<div class="col-md-1"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- /LEFT COLUMN -->
	<!-- RIGHT COLUMN -->
	<div class="col-sm-4 white">
		<strong>All participants last 30 days, most recent first</strong><br />
		<table class="white" style="width: auto;">
			<tbody>

				<?php
				$a = 1;
				foreach ($arrParticipants as $value) {
					if (($a % 2) == 1) { echo '<tr>' ;}
					echo '<td class="clean text-nowrap">&nbsp;&nbsp;'. $value['Pilot'] .'</td>';
					if (($a % 2) == 0) { echo '</tr>' ;}
					$a++;
				}	?>
				
			</tbody>
		</table>
	</div>
	<!-- /RIGHT COLUMN -->
</div>

<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
