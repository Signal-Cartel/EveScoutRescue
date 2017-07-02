<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php'; 

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
<?php 
require_once '../class/db.class.php';
require_once '../class/leaderboard.class.php';

$database = new Database();
$leaderBoard = new Leaderboard($database);
?>
<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<?php include_once '../includes/top-center.php'; ?>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
				<h2 class="pull-left">About Us</h2>
			</div>
			<div class="panel-body">
				<p>In early YC118, 
					<a href="https://gate.eveonline.com/Profile/Forcha%20Alendare">Forcha 
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
					away. <a href="https://gate.eveonline.com/Profile/Thrice%20Hapus">Thrice 
					Hapus</a> was brought in to manage day-to-day operations.</p>
				<p>Since then, the program has grown to what it is today. What started as one rescue 
					program has now grown to become an entire division within Signal Cartel and 
					encompasses both Search and Rescue operations and the original Rescue Cache 
					program. While Thrice oversees the division as a whole, it 
					takes a team to make it all happen. Our current roster includes:</p>
					
				<div class="row">
				  <div class="col-md-3">
				    <div class="thumbnail text-center">
				      <a href="https://gate.eveonline.com/Profile/A%20Dead%20Parrot">
				        <img src="https://image.eveonline.com/Character/96765374_128.jpg" alt="A Dead Parrot" style="width:100%">
				        <div class="caption">
				          <p><strong>A Dead Parrot</strong><br />Development<br />Co-Pilot</p>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-3 text-center">
				    <div class="thumbnail">
				      <a href="https://gate.eveonline.com/Profile/Igaze">
				        <img src="https://image.eveonline.com/Character/1852974735_128.jpg" alt="Igaze" style="width:100%">
				        <div class="caption">
				          <p><strong>Igaze</strong><br />Search &amp; Rescue<br />
				          	Asst. Coordinator</p>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-3 text-center">
				    <div class="thumbnail">
				      <a href="https://gate.eveonline.com/Profile/Lektro%20Illuminate">
				        <img src="https://image.eveonline.com/Character/2112082674_128.jpg" alt="Lektro" style="width:100%">
				        <div class="caption">
				          <p><strong>Lektro Illuminate</strong><br />Design<br />CSS</p>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-3 text-center">
				    <div class="thumbnail">
				      <a href="https://gate.eveonline.com/Profile/Lucas%20Ballard">
				        <img src="https://image.eveonline.com/Character/96491034_128.jpg" alt="Lucas" style="width:100%">
				        <div class="caption">
				          <p><strong>Lucas Ballard</strong><br />Search &amp; Rescue<br />
				          	Lead Coordinator</p>
				        </div>
				      </a>
				    </div>
				  </div>
				</div>
				<div class="row">
				  <div class="col-md-3">
				  </div>
				  <div class="col-md-3 text-center">
				    <div class="thumbnail">
				      <a href="https://gate.eveonline.com/Profile/Orsel%20Solette">
				        <img src="https://image.eveonline.com/Character/96975403_128.jpg" alt="Orsel" style="width:100%">
				        <div class="caption">
				          <p><strong>Orsel Solette</strong><br />Development<br />
				          	ESR Data Tools</p>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-3 text-center">
				    <div class="thumbnail">
				      <a href="https://gate.eveonline.com/Profile/Triffton%20Ambraelle">
				        <img src="https://image.eveonline.com/Character/93697245_128.jpg" alt="Triffton" style="width:100%">
				        <div class="caption">
				          <p><strong>Triffton Ambraelle</strong><br />Search &amp; Rescue<br />
				          	Asst. Coordinator</p>
				        </div>
				      </a>
				    </div>
				  </div>
				  <div class="col-md-3 text-center">
				  </div>
				</div>
				<p>We also rely on a dedicated group of pilots to place and maintain our
					rescue caches in space. All participating pilots over the last 30 days 
					are listed to the right. These are the true unsung heroes of New Eden.</p>
				<p><span class="lead">Hall of Heroes</span><br />
					Of special note are our	<a href="heroes.php">"Hall of Heroes"</a> pilots, 
					who have placed or maintained a minimum of 100 caches each. If you ever 
					see any of these pilots in Local, be sure to give them a wave. The next 
					ship they save might be yours! <a href="heroes.php">Visit the Hall of 
					Heroes now!</a></p>
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