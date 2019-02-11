<?php 
$users = new Users($database);
?>
<div class="col-sm-8 white" style="text-align: center;">
	<span class="sechead"><?=$pgtitle?></span><br /><br />
	<a class="btn btn-danger btn-md" href="../911" role="button">Rescue 911</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a class="btn btn-primary btn-md" href="about.php" role="button">About Us</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a class="btn btn-primary btn-md" href="donate.php" role="button">Contribute</a>
	<!--<a class="btn btn-success btn-md" href="../donate" role="button">DONATE NOW - YC121 Fund Drive</a> -->
	<?php 
	require_once '../class/users.class.php';
	// display these buttons only to EvE-Scout pilots
	if (isset($_SESSION['auth_characteralliance'])) {
		if (Users::isAllianceUserSession()) {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;
				  <a class="btn btn-success btn-md" href="../esrc/search.php" 
					role="button">ESRC</a>&nbsp;&nbsp;&nbsp;&nbsp;
				  <a class="btn btn-success btn-md" href="../esrc/rescueoverview.php" 
					role="button">SAR</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			// only show Co-Pilot button if they are not already logged into Co-Pilot
			if (!isset($_SESSION['auth_copilot'])) {
				echo '<a class="btn btn-warning btn-md" href="../copilot/" 
					role="button" target="_blank">Allison</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			}
			// additional button for admin users
			if ($users->isAdmin($charname)) {
					echo '<a class="btn btn-danger btn-md" href="../admin/index.php" 
							role="button">Admin</a>';
			};
		}
	}
	?>
</div>