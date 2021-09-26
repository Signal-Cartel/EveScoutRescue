<?php 
$database = new Database();
$users = new Users($database);	?>

<div class="col-sm-8 white" style="text-align: center; padding: 12px;">
	<span class="sechead"><?=$pgtitle?></span>
		<p>
			<i>EvE-Scout Rescue operated by <a class="white" href="https://www.signalcartel.org/">Signal Cartel</a></i>
		</p>
		<p>
			<a class="btn btn-danger btn-md" href="../911" role="button">Rescue 911</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a class="btn btn-primary btn-md" href="../home/about.php" role="button">About Us</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a class="btn btn-primary btn-md" href="../home/donate.php" role="button">Contribute</a>
		</p>
	
	<?php 
	// display these buttons only to EvE-Scout pilots
	if (isset($_SESSION['auth_characteralliance'])) {
		if (Users::isAllianceUserSession()) {	?>
			
			&nbsp;&nbsp;&nbsp;&nbsp;
			<a class="btn btn-success btn-md" href="../esrc/search.php" role="button">
				ESRC</a>&nbsp;&nbsp;&nbsp;&nbsp;
			<a class="btn btn-success btn-md" href="../esrc/rescueoverview.php" role="button">
				SAR</a>&nbsp;&nbsp;&nbsp;&nbsp;
			
			<?php
			// only show Co-Pilot button if they are not already logged into Co-Pilot
			if (!isset($_SESSION['auth_copilot'])) {	?>

			<a class="btn btn-warning btn-md" href="../copilot/" role="button" target="_blank">
				Allison</a>&nbsp;&nbsp;&nbsp;&nbsp;

				<?php
			}

			// additional button for admin users
			if ($users->isAdmin($charname)) {	?>

			<a class="btn btn-danger btn-md" href="../admin/index.php" role="button">Admin</a>

			<?php
			}
		}
	}	?>

</div>