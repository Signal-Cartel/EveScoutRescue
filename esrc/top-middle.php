<?php 
// create object instances
$db_top = new Database();
$systems_top = new Systems($db_top);
$rescue_top = new Rescue($db_top);

// get rescue counts
$ctrESRCrescues = $rescue_top->getRescueCount('closed-esrc', '', '');
$ctrSARrescues = $rescue_top->getRescueCount('closed-rescued');
$ctrAllRescues = intval($ctrESRCrescues) + intval($ctrSARrescues);

$sysNoteRow = $systems_top->getWHInfo($system);
$arrSysnotes = $systems_top->getSystemNotes($system);
			
// get PHP page
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
					<button type="submit" class="btn btn-md">Search</button>
					&nbsp;&nbsp;&nbsp;<a href="?">clear system</a>
				</div>
			</form>
		</div>
		<div class="col-sm-4" style="text-align: left;">
			<?php
			if (isset($system) && $system!= '') {
				// display system info
				$row = $systems_top->getWHInfo($system);
				$whNotes = (!empty($row['Notes'])) ? '<br />' . utf8_encode($row['Notes']) : '';				
				echo '<strong class="white">'.$row['Class'] . $whNotes . '</strong><br />';
				
				/* static wh connection details */
				$staticConnections = $row['Statics'];
				if (!empty($staticConnections)) {

					$whTypes = explode(',', $staticConnections);
					echo '<div class="whStatics"><ul>';

					$whListItems = array();
					$whListIndex = 0;

					foreach ($whTypes as $whType) {
						$whTypeInfo = $systems_top->getWHType($whType);						
						$massDesc = '';

						$size = $whTypeInfo['Size'];
						if (!empty($size)) {

							$size = intval($size);

							if ($size <= 5000000) {
								$massDesc = "f/d";
							}
							else if ($size <= 20000000) {
								$massDesc = "bc";								
							}
							else if ($size <= 300000000) {
 								$massDesc = "bs";
							}
							else if ($size <= 1350000000) {
								 $massDesc = "cap";
							}
							else  {
								$massDesc = "scap";
							}

							$massDesc = '(' . strtoupper($massDesc) . ')';
						}

						$dest = $whTypeInfo['Destination'];
						
						$whListItems[$whListIndex++] = '<li class="whDest-' . $dest .'">' 
							. strtoupper($dest) . ' > ' . $whType . ' ' . $massDesc . '</li>';
					}

					sort($whListItems);

					foreach ($whListItems as $whListItem){
					    echo $whListItem;
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