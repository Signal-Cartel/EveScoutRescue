<div class="col-sm-8 white" style="text-align: center;">
	<span class="sechead"><?=$pgtitle?></span><br /><br />
	<a class="btn btn-primary btn-md" href="about.php" role="button">About Us</a>&nbsp;&nbsp;&nbsp;&nbsp;
	<a class="btn btn-primary btn-md" href="donate.php" role="button">Contribute</a>
	<?php 
	// display these buttons only to EvE-Scout pilots
	if (isset($_SESSION['auth_characteralliance'])) {
		if ($_SESSION['auth_characteralliance'] == 'EvE-Scout Enclave') {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;
				  <a class="btn btn-success btn-md" href="../esrc/search.php" 
					role="button">ESRC</a>&nbsp;&nbsp;&nbsp;&nbsp;
				  <a class="btn btn-success btn-md" href="../esrc/rescueoverview.php" 
					role="button">SAR</a>&nbsp;&nbsp;&nbsp;&nbsp;
				  <a class="btn btn-warning btn-md" href="../copilot/" 
					role="button">Allison / CoPilot</a>&nbsp;&nbsp;&nbsp;&nbsp;';
			// additional button for admin users
			if (in_array($charname, $admins)) {
				echo '<a class="btn btn-danger btn-md" href="../esrc/payoutadmin.php" 
							role="button">ESRC Payout Admin</a>';
			};
		}
	}
	?>
</div>