<?php 
// create object instances
$db_top = new Database();
$systems_top = new Systems($db_top);
$rescue_top = new Rescue($db_top);

// get rescue counts
$ctrESRCrescues = $rescue_top->getRescueCount('closed-esrc', '', '');
$ctrSARrescues = $rescue_top->getRescueCount('closed-rescued');
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);
?>
<div class="col-sm-8 black" style="text-align: center;">
	<div class="row">
		<div class="col-sm-3"></div>
		<div class="col-sm-5" style="text-align: left;">
			<form method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="form-group">
					<input type="text" name="sys" size="30" autoFocus="autoFocus" onclick="this.select()"
						autocomplete="off" class="sys" placeholder="System Name" 
						value="<?php echo isset($system) ? $system : '' ?>">
				</div>
				<div class="clearit">
					<button type="submit" class="btn btn-md">Search</button>
					&nbsp;&nbsp;&nbsp;<a href="?">clear system</a>
				</div>
			</form>
		</div>
		<div class="col-sm-4" style="text-align: left;">
			<?php 
			if (isset($system) && $system!= '') {
				// display system info and notes (if any)
				$row = $systems_top->getWHInfo($system);
				$arrSysnotes = $systems_top->getSystemNotes($system);
				$strSysnotes = '';
				if (!empty($arrSysnotes)) { 
					$sysnote = '';
					foreach ($arrSysnotes as $val) {
						$sysnote = $sysnote .'['. Output::getEveDate($val['notedate']) .']<br />'. $val['note'] .'<br />';
					}
					$strSysnotes = '&nbsp;&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#ModalSysNotes">
						<i class="white fa fa-sticky-note" style="vertical-align: bottom;" data-toggle="tooltip" data-html="true"
						data-placement="bottom" title="'. $sysnote .'"></i></a>'; 
				}
				echo '<strong class="white">'.$row['Class']. $strSysnotes .'<br/>'.
					utf8_encode($row['Notes']).'</strong>';
			}
			?>
			<br /><br />
			<span class="sechead white" style="font-weight: bold;">
				<span style="color: gold;"><?php echo $ctrAllRescues; ?></span> Rescues
			</span>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
        $('input.sys').typeahead({
            name: 'sys',
            remote: '../data/typeahead.php?type=system&query=%QUERY'
        });
    })

	// initialize tooltip display
	$(document).ready(function(){
	    $('[data-toggle="tooltip"]').tooltip({container: 'body'}); 
	});
</script>

<?php
// modal include
include 'modal_sysnotes.php';
?>