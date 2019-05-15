<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 

require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';
require_once '../class/users.class.php';

$database = new Database();
$leaderBoard = new Leaderboard($database);
$users = new Users($database);
?>
<html>

<head>
	<?php
	$pgtitle = "About Us";
	include_once '../includes/head.php';
	?>
	<style>
		td.clean {
		    padding: 3px;
		}
		</style>
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
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">About Us</h2>
				<div class="btn-group pull-right" style="padding-top: 12px;">
			        <a class="btn btn-success btn-lg" 
			        	href="https://www.signalcartel.com/?s=EvE-Scout+Rescue" role="button">
			        	Rescue success stories!</a>
			    </div>
			</div>
			<div class="panel-body">
				<p>In early YC118, 
					<a href="https://evewho.com/pilot/Forcha%20Alendare">Forcha 
					Alendare</a> had a vision of a service that would provide a basic 
					emergency resource kit for capsuleers stranded in wormholes, 
					regardless of alliance, sovereignty, or play style. This vision
					quickly grew to become the Rescue Cache program as we know it today. 
					Originally managed manually via a spreadsheet and a web form, the 
					program had humble beginnings, but nonetheless managed to capture the 
					imagination of many Signal Cartel pilots.</p>
				<p>In late YC118, Forcha decided to move on to activities that would potentially 
					bring him into conflict with the 
					<a href="http://www.signalcartel.com/about">Credo</a>, so he chose to  
					leave the corp. The program languished without anyone tending it, and corp and 
					alliance leadership decided it was too valuable to be allowed to wither 
					away. <a href="https://evewho.com/pilot/Thrice%20Hapus">Thrice 
					Hapus</a> was brought in to manage day-to-day operations.</p>
				<p>Since then, the program has grown to what it is today. What started as one rescue 
					program has now grown to become an entire division within Signal Cartel and 
					encompasses both Search and Rescue operations and the original Rescue Cache 
					program.</p>
				<p>In December YC120, <a href="https://evewho.com/pilot/Igaze">Igaze</a> 
					assumed leadership of the Rescue division. While he oversees the division as a whole, it 
					takes a team to make it all happen. Our current roster includes the following dedicated 
					rescue pilots.</p>

				<div class="row">
				  <h2 style="text-align:center">911 Operators</h2>
				</div>
				<div class="row">
				<?php 
				$arrPilots = $users->getUsersByRole('3', true);
				$arrCount = count($arrPilots);
				$i = 0;
				foreach ($arrPilots as $val) {
					// every four loops, close last row and start a new one
					if ($i % 6 == 0) {
						echo '</div><div class="row">';
					}
					echo '<div class="col-md-2"><div class="thumbnail text-center">';
					echo '	<a href="https://evewho.com/pilot/'. urlencode($val['username']) .'">';
					echo '		<img src="https://image.eveonline.com/Character/'. urlencode($val['characterid']) .'_64.jpg" 
						alt="'. urlencode($val['username']) .'" style="width:100%">';
					echo '		<div class="caption"><strong>'. $val['username'] .'</strong></div></a></div></div>';
					// increment counter
					$i++;
				}
				?>
				</div>

				<div class="row">
				  <h2 style="text-align:center">EvE-Scout Rescue Coordinators</h2>
				</div>
				<div class="row">
				<?php 
				$arrPilots = $users->getUsersByRole('2', true);
				$arrCount = count($arrPilots);
				$i = 0;
				foreach ($arrPilots as $val) {
					$i++;
					// every four loops, close last row and start a new one
					if ($i % 5 == 0) {
						echo '</div><div class="row">';
					}
					echo '<div class="col-md-3"><div class="thumbnail text-center">';
					echo '	<a href="https://evewho.com/pilot/'. urlencode($val['username']) .'">';
					echo '		<img src="https://image.eveonline.com/Character/'. urlencode($val['characterid']) .'_128.jpg" 
						alt="'. urlencode($val['username']) .'" style="width:100%">';
					echo '		<div class="caption"><strong>'. $val['username'] .'</strong></div></a></div></div>';
				}
				?>
				</div>

				<div class="row">
				  <div class="col-md-1"></div>
				  <div class="col-md-4">
						<h2 style="text-align:center"><a href="https://www.signalcartel.com/copilot">ALLISON</a></h2>
				    <div class="thumbnail text-center">
				      <a href="https://evewho.com/pilot/A%20Dead%20Parrot">
				        <img src="https://image.eveonline.com/Character/96765374_256.jpg" alt="A.D. Parrot" style="width:100%">
				        <div class="caption">
				          <strong>A Dead Parrot</strong>
				        </div>
				      </a>
				    </div>
				  </div>
					<div class="col-md-2"></div>
				  <div class="col-md-4 text-center">
						<h2 style="text-align:center">Director</h2>
				    <div class="thumbnail text-center">
						<a href="https://evewho.com/pilot/Igaze">
				        <img src="https://image.eveonline.com/Character/1852974735_256.jpg" alt="Igaze" style="width:100%">
				        <div class="caption">
				          <strong>Igaze</strong>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-1"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-4 white">
		<strong>All participants last 30 days, most recent first</strong><br />
		<table class="white" style="width: auto;">
			<tbody>
				<?php
				$rows = $leaderBoard->getActivePilots(30);
				$a = 1;
				foreach ($rows as $value) {
					if (($a % 2) == 1) { echo '<tr>' ;}
					echo '<td class="clean text-nowrap">&nbsp;&nbsp;'. $value['Pilot'] .'</td>';
					if (($a % 2) == 0) { echo '</tr>' ;}
					$a++;
				}
				?>
			</tbody>
		</table>
	</div>
</div>

</div>
</body>
</html>
