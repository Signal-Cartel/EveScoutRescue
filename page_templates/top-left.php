<div class="col-sm-2">
	<a href="../index.php">
		<img src="../img/eve-scout-logo.png" alt="EvE-Scout Rescue" style="width: 100px;" /></a>
	<div id="timedisp" class="white"></div>
</div>

<script>
	function startTime() {
		var date = new Date(); // date object in local timezone
		var UTCstring = date.toUTCString();
		var isoTime = 'EVE TIME ' + addZ(date.getUTCHours()) + ':' + addZ(date.getUTCMinutes()) + ':' + addZ(date.getUTCSeconds());	

		function addZ(n) {
			return (n<10? '0' : '') + n;
		}

		document.getElementById('timedisp').innerHTML = isoTime;
		var t = setTimeout(startTime, 1000);
	}
	
	startTime();
</script>