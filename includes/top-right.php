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
				echo "<br><a href='$self?r=a'>Admin</a>&nbsp;|&nbsp;<a href='$self?r=c'>Coord</a>&nbsp;|&nbsp;<a href='$self?r=9'>911</a>&nbsp;|&nbsp;<a href='$self?r=l'>Sig</a>";
			}
		}
		echo '</p>';
		?>
	</div>
</div>
<!-- END top-right -->