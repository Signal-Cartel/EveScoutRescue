<!-- Sower Modal Form -->

<?php 
$locopts = $systems_top->getSowLocations($sysNoteRow['PlanetCount']);
// get cache information from database
$old_cache = $caches->getLastCacheInfo($system);

$script = 'var oldLoc, oldAlign, oldDist, oldPass; ';
$script .= "var sysName = '$system'; ";
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
  <div class="modal-dialog" style="width: 500px;">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sower">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Sower</h4>
      </div>

			
      <form name="sowform" id="sowform" action="process_sow.php" method="POST">
	    <div class="modal-body black" style="background-color: rgba(36, 64, 64, 0.8);">
			<div class="form-group">
				<label class="control-label" for="sys_sow">System: </label>
				<input type="hidden" name="sys_sow" value="<?php echo $system ?>" />
				<span class="subhead"><?php echo $system ?></span>
				<span class="descr" style="width: 100%;">
					<p id="cachename2">
						<span id="cachespan">EvE-Scout Rescue Cache - Stranded in this wormhole? Request help in the EvE-Scout channel</span>
						<span id="copyclipname" class="fa fa-clipboard" onclick="SelectInner('cachespan')" 
							style="margin-left: 16px;  cursor: pointer;"></span>
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
					<label class="control-label" for="alignedwith">Aligned with<span class="descr"></span></label>
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
					<label class="control-label" for="location">Location celestial<span class="descr"></span></label>
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
				<label class="control-label" for="sdistance">Distance from location(km)<span class="descr"><em>Must be between 22000 and 50000.</em></span></label>
				<input class="form-control" id="sdistance" name="distance" onchange="setBookmark()" required />
			</div>

			<div class="field form-group">
				<label class="control-label" for="spassword">Password<span class="descr"><em>Generated password is pre-filled. Click to 
					paste your own password.</em></span></label>
				<input type="text" class="form-control" id="spassword" name="password"  style="width: 160px; display: inline;"
					value="<?=$cachepass?>" maxlength="15" onclick="select();" required />
				<i id="copyclip" class="fa fa-clipboard" onclick="SelectAllCopy('spassword')"></i>	    
			</div>
			
			<div>
				<label for="hasfil-sow" style="margin-bottom: 20px">
					<input id="hasfil-sow" name="hasfil" type="checkbox" value="1" style="width: 40px; height: 24px;" data-error="Does the cache contain a filament?">
					<p style="display: inline; position: relative; top: -6px;">
					<strong style="background-color: rgba(128, 32, 32, 0.8); height: 1.5em; padding: 2px 6px 3px 6px;">Filament</strong> Does the cache contain a filament?
					</p>
				</label>
			</div>
							
							
		  	<div class="field form-group">
				<label class="control-label" for="snotes">Notes<span class="descr">Other important information we need to know</span></label>
				<textarea class="form-control" id="snotes" name="notes" rows="2"></textarea>
			</div>
			<span class="descr" style="width: 100%;">
			
					<p style="width:100%">
					
					Bookmark label:&nbsp; <span id="sowbookmark" style="background-color: rgba(128, 32, 32, 0.8); height: 1.5em; padding: 2px 6px 3px 6px;">not yet set</span><span id="copyclipbm" class="fa fa-clipboard" onclick="SelectInner('sowbookmark')" style="margin-left: 16px; cursor: pointer;"></span>
					</p>
													
								
				</span>
				
				
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
var noteText = 'Locate with corp bookmark.';

function copyOldInfo(){
	//oldLoc, oldAlign, oldDist, oldPass
	// location, alignedwith, distance, password
	document.getElementById('location').value = oldLoc;
	document.getElementById('alignedwith').value = oldAlign;
	document.getElementById('sdistance').value = oldDist;
	document.getElementById('snotes').value = "";
	//document.getElementById('password').value = oldPass;
	setBookmark();
}

	

function setUnaligned(){
	if (confirm('An unaligned cache requires prior approval. Has this been approved?')) {
		document.getElementById("location").value = 'Unaligned'; 
		document.getElementById("alignedwith").value = 'Unaligned'; 
		document.getElementById('sdistance').value = 'Unaligned';
		document.getElementById('snotes').value = noteText;
		setBookmark();
	} 
}
	
  function validatePlanets(caller){ 
	var locationp = document.getElementById("location"); 
	var align = document.getElementById("alignedwith");  
	var dist = document.getElementById("sdistance"); 
	var snotes = document.getElementById('snotes');

	  if ((locationp.value != 'Unaligned') && (locationp.selectedIndex != 0 && align.selectedIndex != 0) && locationp.value == align.value) {
		  alert("Location planet and align planet must be different."); 
		  caller.selectedIndex = -1; 
	  }
	 if (caller.value == 'Unaligned'){
		 if (confirm('An unaligned cache requires prior approval. Has this been approved?')) {
			locationp.value = 'Unaligned'; 
			align.value = 'Unaligned'; 
			dist.value = 'Unaligned';
			snotes.value = noteText;
		} 
		else{
			caller.selectedIndex = 0;
		}		
	 }
	 if (caller.value != 'Unaligned'){
			if (locationp.value == 'Unaligned') {locationp.selectedIndex = 0;}
			if (align.value == 'Unaligned') {align.selectedIndex = 0;}
			if (dist.value == 'Unaligned') {dist.value = "";}
			if (snotes.value == noteText) {snotes.value = "";}
	 }	 
	 setBookmark();

  }

function setBookmark(){
		function writeit(text){
			document.getElementById("sowbookmark").textContent=text;
		}
		var dist = document.getElementById("sdistance").value;
		var locationp = document.getElementById("location").value; 
		var align = document.getElementById("alignedwith").value; 		
		
		if (dist == "") {
			writeit('not yet set');
			return;
		}
		if (locationp == "" || align == ""){
			writeit('not yet set');
			return;
		}
		if (locationp == 'Unaligned' || align == 'Unaligned'){
			//writeit(sysName + ' > @Unaligned');
			writeit(sysName + ' Rescue Cache');
			return;
		}
		var romans ={
			Star: 'Star',
			I: 1,
			II: 2,
			III: 3,
			IV: 4,
			V: 5,
			VI: 6,
			VII: 7,
			VIII: 8,
			IX: 9,
			X: 10,
			XI: 11,
			XII: 12,
			XIII: 13,
			XIV: 14,
			XV: 15,
			XVI: 16,
			XVII: 17,
			XVIII: 18,
			XIX: 19,
			XX: 20,
			XXI: 21,
			XXII: 22,
			XXIII: 23,
			XXIV: 24,
			XXV: 25,
			XXVI: 26,
			XXVII: 27,
			XXVIII: 28,
			XXIX: 29,
			XXX: 30	
		};
		//var bm = sysName + " " + romans[align] + ">" + romans[locationp] + " @" +  dist;
		var bm = sysName + " Rescue Cache";
		writeit(bm);
		return;
}
		
	function SelectInner(id) {
		copytext = document.getElementById(id);
		  var textArea = document.createElement("textarea");
		  textArea.value = copytext.innerText;
		  // Avoid scrolling to bottom
		  textArea.style.top = "0";
		  textArea.style.left = "0";
		  textArea.style.position = "fixed";

		  copytext.appendChild(textArea);
		  textArea.focus();
		  textArea.select();

		try {
			var successful = document.execCommand("Copy");
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Fallback: Copying text command was ' + msg);
		} 
		catch (err) {
			console.error('Fallback: Oops, unable to copy', err);
		}

		copytext.removeChild(textArea);
	}

		
  function swapThem()
  {
		var alignVal = document.getElementById("alignedwith").value;
		var locationVal = document.getElementById("location").value;
		var tophtm = document.getElementById("topfield").innerHTML;
		var bottomhtm = document.getElementById("bottomfield").innerHTML;

		document.getElementById("topfield").innerHTML = bottomhtm;
		document.getElementById("bottomfield").innerHTML = tophtm;
		document.getElementById("alignedwith").value = alignVal;
		document.getElementById("location").value = locationVal;
		
		LocOnTop = !LocOnTop;
		if (typeof(Storage) !== "undefined"){
			localStorage.LocOnTop = LocOnTop;
		}	
	}
</script>