<!-- BEGIN top-right -->
<div class="col-sm-2" style="position: relative; float:right;">
	<div>
	
		<?php 
		echo isset($chardiv) ? $chardiv : '';
		$toprole = 'Signaleer';
		$toprole = (isset($_SESSION['is911']) and $_SESSION['is911'] == 1) ? '911 Operator' : $toprole;
		$toprole = (isset($_SESSION['isCoord']) and $_SESSION['isCoord'] == 1) ? 'ESR Coordinator' : $toprole;
		$toprole = (isset($_SESSION['isAdmin']) and $_SESSION['isAdmin'] == 1) ? 'Admin' : $toprole;

		echo "<p>Role: $toprole";
		if (isset($_SESSION['livedata']) and  $_SESSION['livedata'] == false){
			if ($_SERVER['HTTP_HOST'] == 'dev.evescoutrescue.com' and ($_SERVER['PHP_SELF'] == '/esrc/rescueoverview.php' or $_SERVER['PHP_SELF']== '/esrc/search.php')){
				$self = $_SERVER['PHP_SELF'];
				$system_uri = '';
				if (isset($system) && $system <>''){
					$system_uri = '&sys='.$system;
				}
				echo "<br>";
				echo "<a href='$self?r=a$system_uri'>Admin</a>&nbsp;|&nbsp;";
				echo "<a href='$self?r=c$system_uri'>Coord</a>&nbsp;|&nbsp;";
				echo "<a href='$self?r=9$system_uri'>911</a>&nbsp;|&nbsp;";
				echo "<a href='$self?r=l$system_uri'>Sig</a>";
			}
		}
		echo '</p>';
		?>
	</div>
</div>
<!-- END top-right -->