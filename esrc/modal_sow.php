<!-- Sower Modal Form -->

<?php 
$locopts = $systems_top->getSowLocations($sysNoteRow['PlanetCount']);
// get cache information from database
$old_cache = $caches->getLastCacheInfo($system);
$script = 'var oldLoc, oldAlign, oldDist, oldPass; ';

if (!empty($old_cache)) {
	$oldHasfil = ($old_cache['has_fil'] == 1 ? 'true' : 'false');	
	$script .= "var oldHasfil = $oldHasfil;";
	$script .= 'var oldLoc = "' .$old_cache["Location"] . '";';
	$script .= 'var oldAlign = "' . $old_cache["AlignedWith"] . '";';
	$script .= 'var oldDist = "' . Output::htmlEncodeString($old_cache['Distance']) . '";';
	$script .= 'var oldPass = "' . Output::htmlEncodeString($old_cache['Password']) . '";';
}




?>
<div id="SowModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sower">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sower</h4>
      </div>

			
      <form name="sowform" id="sowform" action="process_sow.php" method="POST">
	      <div class="modal-body black">
			<div class="form-group">
				<label class="control-label" for="sys_sow">System: </label>
				<input type="hidden" name="sys_sow" value="<?php echo $system ?>" />
				<span class="subhead"><?php echo $system ?></span>
				<span class="descr" style="width: 100%;">
					<p id="cachename2">EvE-Scout Rescue Cache - If you are stranded in this wormhole you can request help in the EvE-Scout channel.<span id="copyclip" class="fa fa-clipboard" onclick="SelectAllCopy('cachename2')" 
							style="left: 5px;"></span>
					</p>
				</span>
			</div>
		<div style="display: block;">					
			<a class="btn btn-success btn-xs" tabindex="98" role="button" href="javascript: copyOldInfo();">Same as last Cache</a>
		</div>

		<div style="float: right;">					
			<a class="btn btn-warning btn-xs" tabindex="98" role="button" href="javascript: setUnaligned();">Unaligned</a>
		</div>
		
			<!--This is the beginning of the swap fields class="form-control" -------------->
			<div id="topfield">
				<div class="field form-group">
					<label class="control-label" for="alignedwith">Aligned with<span class="descr"><em>If somewhere other than a planet or star, please detail in notes.</em></span></label>
					<select  id="alignedwith" name="alignedwith" class="form-control" onchange="validatePlanets(this)" required>
						<option value="">- Select -</option>
						<?php 
						foreach ($locopts as $val) {
								echo '<option value="' . $val . '">' . $val . '</option>';
						}
						?>
					</select>
				</div>
			</div><!-- End topfield -->

			<div style="display: block; float: right;">					
				<a class="btn btn-success btn-xs" tabindex="99" role="button" href="javascript: swapThem();">Swap Align &lt;-&gt; Location fields</a>
			</div>

			<div id="bottomfield" >
				<div class="field form-group">
					<label class="control-label" for="location">Location celestial<span class="descr"><em>If somewhere other than a planet or star, please detail in notes.</em></span></label>
					<select id="location" name="location" class="form-control" onchange="validatePlanets(this)" required>
						<option value="">- Select -</option>
						<?php
						foreach ($locopts as $val) {
								echo '<option value="' . $val . '">' . $val . '</option>';
						}
						?>
					</select>
				</div>


			</div><!-- end bottomfield -->
			<!--This is the end of the swap fields------------------------>

			<div class="field form-group">
				<label class="control-label" for="distance">Distance from location(km)<span class="descr"><em>Must be between 22000 and 50000.</em></span></label>
				<input class="form-control " id="distance" name="distance" required />
			</div>

			<div class="field form-group">
				<label class="control-label" for="spassword">Password<span class="descr"><em>Generated password is pre-filled. Click to 
					paste your own password.</em></span></label>
				<input type="text" class="form-control" id="spassword" name="password" 
					value="<?=$cachepass?>" maxlength="15" onclick="select();" required />
			</div>
			
			<div>
				<label for="hasfil">
					<input id="hasfil" name="hasfil" type="checkbox" value="1" data-error="Does the cache contain a filament?">
					<strong style="background-color: red;">Filament: </strong>Does the cache contain a filament?
				</label>
			</div>
							
							
		  	<div class="field form-group">
				<label class="control-label" for="snotes">Notes<span class="descr">Other important information we need to know</span></label>
				<textarea class="form-control" id="snotes" name="notes" rows="3"></textarea>
			</div>
	      </div>
	      <div class="modal-footer">
	        <div class="form-actions">
				<input type="hidden" name="pilot" value="<?php echo isset($charname) ? $charname : 'charname_not_set' ?>" />
			    <button type="submit" class="btn btn-info">Submit</button>
			</div>
	      </div>   
      </form>
    </div>

  </div>
</div>

<script>
	/*
  $( document ).ready(function() {
    $("#sowform").validator();
  });
	*/
	

		
		
  if (typeof(Storage) !== "undefined"){
      if (localStorage.LocOnTop) {
          LocOnTop = (localStorage.LocOnTop == 'true');
      }
      else{
          localStorage.LocOnTop = 'false';
          LocOnTop = false;
      }
      if (LocOnTop){LocOnTop = true; swapThem();}
  }
  else{
      LocOnTop = false;
  }



<?=$script?>	

function copyOldInfo(){
	//oldLoc, oldAlign, oldDist, oldPass
	// location, alignedwith, distance, password
	document.getElementById('location').value = oldLoc;
	document.getElementById('alignedwith').value = oldAlign;
	document.getElementById('distance').value = oldDist;
	document.getElementById('snotes').value = "";
	//document.getElementById('password').value = oldPass;
}

	

function setUnaligned(){
	if (confirm('An unaligned cache requires prior approval. Has this been approved?')) {
		document.getElementById("location").value = 'Unaligned'; 
		document.getElementById("alignedwith").value = 'Unaligned'; 
		document.getElementById('distance').value = 'Unaligned';
		document.getElementById('snotes').value = 'Locate with corp bookmark. ';
	} 
}
	
  function validatePlanets(caller){ 
	var location = document.getElementById("location"); 
	var align = document.getElementById("alignedwith");  	 
	  if ((location.value != 'Unaligned') && location.value == align.value) {
		  alert("Location planet and align planet must be different."); 
		  caller.selectedIndex = -1; 
	  }
	 if (caller.value == 'Unaligned'){
		 if (confirm('An unaligned cache requires prior approval. Has this been approved?')) {
			document.getElementById("location").value = 'Unaligned'; 
			document.getElementById("alignedwith").value = 'Unaligned'; 
			document.getElementById('distance').value = 'Unaligned';
			document.getElementById('snotes').value = 'Locate with corp bookmark. ';
		} 
		else{
			caller.selectedIndex = 0;
		}		
	 }
  }

  function swapThem()
  {
		var tophtm = document.getElementById("topfield").innerHTML;
		var bottomhtm = document.getElementById("bottomfield").innerHTML;
		document.getElementById("topfield").innerHTML = bottomhtm;
		document.getElementById("bottomfield").innerHTML = tophtm;
		LocOnTop = !LocOnTop;
		if (typeof(Storage) !== "undefined"){
			localStorage.LocOnTop = LocOnTop;
		}	
	}
</script>