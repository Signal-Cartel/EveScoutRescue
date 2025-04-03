<?php 
// create object instances
if (!isset($database)) {$database = new Database();}
if (!isset($systems_top)) {$systems_top = new Systems($database);}
//$rescue_top = new Rescue($db_top);

// get rescue counts
require_once 'hourly_data.php';	

$sysNoteRow = $systems_top->getWHInfo($system);
$arrSysnotes = $systems_top->getSystemNotes($system);

$phpPage = basename($_SERVER['PHP_SELF']);
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
					<button type="submit" class="btn btn-success">Search</button>
					<a class="btn btn-info" href="?"><small>Clear</small></a>
				</div>
			</form>
		</div>
		<div class="col-sm-4" style="text-align: left;">
			<?php

			if (isset($sysNoteRow)) {
				// display system info
				//$whNotes = (!empty($sysNoteRow['Notes'])) ? '<br />' . utf8_encode($sysNoteRow['Notes']) : '';
				$whNotes = (!empty($sysNoteRow['Notes'])) ? '<br />' . $sysNoteRow['Notes'] : '';
				echo '<strong class="white">'.$sysNoteRow['Class'] . $whNotes . '</strong><br />';
				
				if (!empty($sysNoteRow['StaticWhInfo'])) {

					echo '<div class="whStatics"><ul>';

					foreach (explode(',', $sysNoteRow['StaticWhInfo']) as $staticWhInfo) {
					
						$staticConnection = explode('/', $staticWhInfo);

						$dest = $staticConnection[1];
						$size = $staticConnection[2];
						$massDesc = (!empty($size)) ? Systems::getShipSizeLimit($size) : '';
						echo '<li class="whDest-' . $dest .'">'
							. $staticConnection[0] . ' > ' . strtoupper($dest) . ' ' . $massDesc 
							. '</li>';						
					}

					echo '</ul></div>';
				}
			}
			?>
			<span class="sechead-no-indent white" style="font-weight: bold;">
				<span style="color: gold;"><?php echo $ctrAllRescues; ?></span> Rescues
			</span>
		</div>
	</div>
</div>

<?php
// modal include
include 'modal_sysnotes.php';
include 'modal_sysnotes_edit.php';
?>

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

	// auto-display System Notes edit modal when "noteid" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('noteid=') != -1) {
	    $('#ModalSysNotesEdit').modal('show');
	}
</script>