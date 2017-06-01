<div class="col-sm-8 black" style="text-align: center;">
	<div class="row">
		<div class="col-sm-3"></div>
		<div class="col-sm-5" style="text-align: left;">
			<form method="get" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
				<div class="form-group">
					<input type="text" name="sys" size="30" autoFocus="autoFocus" 
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
				$database = new Database();
				$systems = new Systems($database);
				// display wormhole info
				$row = $systems->getWHInfo($system);
				echo '<strong class="white">'.$row['Class'].'<br/>'.$row['Notes'].'</strong>';
			}
			?>
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
</script>