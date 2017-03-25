<?php
session_start();
if (isset($_SESSION['auth_characterid'])) {
	$charimg = '<img src="http://image.eveonline.com/Character/'.$_SESSION['auth_characterid'].'_64.jpg">';
	$charname = $_SESSION['auth_charactername'];
	$chardiv = '<div style="text-align: center;">'.$charimg.'<br /><span class="white">'.$charname.'</span><br /><span class="descr"><a href="/auth/logout.php">logout</a></span></div>';
	if (!$_SESSION['auth_characteralliance'] == 'EvE-Scout Enclave') {	//non-EvE-Scout logged in
		header("Location: /");
	}
}
else {	//not logged in
	$_SESSION['auth_redirect'] = 'http://evescoutrescue.com'.htmlentities($_SERVER['PHP_SELF']);
	header("Location: /auth/login.php");
}
?>
<html>

<head>
	<meta http-equiv="Content-Language" content="en-us">
	<title>EvE-Scout Rescue Cache :: Search</title>
	<meta charset="utf-8">
	<link href="/css/main.css" rel="stylesheet">
	<!-- Latest compiled and minified Bootstrap -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/js/typeahead.js"></script>

	<script>
        $(document).ready(function() {

            $('input.targetsystem').typeahead({
                name: 'targetsystem',
                remote: 'activecaches.php?query=%QUERY'

            });

        })
    </script>
</head>

<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/class/class.db.php';

if(isset($_POST['targetsystem'])) { 
	$targetsystem = filter_var($_POST['targetsystem'], FILTER_SANITIZE_STRING);
}
elseif (isset($_GET['system'])) {
	$targetsystem = htmlspecialchars($_GET["system"]);
}
?>
<body>
<div class="container">
<div class="row" id="header">
	<div class="col-md-2" style="padding-top: 10px;">
		<div style="text-align: center;">
			<a href="/"><img src="/img/eve-scout-logo.png" alt="EvE-Scout Rescue Cache" /></a>
		</div>
	</div>
	<div class="col-md-8" style="padding-top: 10px; text-align: center;">
		<span style="font-size: 125%; font-weight: bold; color: white;">Search Rescue Cache Database</span><a href="esrc_data_entry.php" class="btn btn-link" role="button">Go to Data Entry</a><br />
		<form method="post" class="form-inline" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<div class="form-group">
				<input type="text" name="targetsystem" size="30" autoFocus="autoFocus" class="targetsystem" placeholder="System" value="<?php echo isset($targetsystem) ? $targetsystem : '' ?>">
			</div>
			<button type="submit" class="btn btn-lg">Search</button>
		</form>
		<div class="clearit">
			<span class="white">If a system is not listed, no active cache is present.</span>
		</div>
	</div>
	<div class="col-md-2" style="text-align: center; padding-top: 10px;">
		<div style="text-align: right;">
			<?php echo isset($chardiv) ? $chardiv : '' ?>
		</div>
	</div>
</div>

<div class="ws"></div>
<?php
// display result for the selected system
if (isset($_POST['targetsystem']) || isset($_GET['system'])) { 
	echo '<div class="row" id="systableheader">';
	echo '<div class="col-sm-12">';
	echo '<div style="padding-left: 10px;">';
	//echo '<span style="font-weight: bold; font-size: 150%;">' . $targetsystem . ': </span>';
	echo '<a href="/esr/esrc_data_entry.php?tendsys='.$targetsystem.'" class="btn btn-success" role="button">Tend</a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="/esr/esrc_data_entry.php?adjsys='.$targetsystem.'" class="btn btn-warning" role="button">Adjunct</a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="https://tripwire.eve-apps.com/?system=' . $targetsystem . '" class="btn btn-info" role="button" target="_blank">Tripwire</a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="http://wh.pasta.gg/' . $targetsystem . '" class="btn btn-info" role="button" target="_blank">ww.pasta.gg</a>&nbsp;&nbsp;&nbsp;';
	echo '<a href="?" class="btn btn-link" role="button">clear result</a>';
	echo '</div></div></div>';
?>
<div class="row" id="systable">
	<div class="col-sm-12">
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Sown On</th>
					<th class="white">Location</th>
					<th class="white">Aligned With</th>
					<th class="white">Distance</th>
					<th class="white">Password</th>
					<th class="white">Status</th>
					<th class="white">Expires On</th>
				</tr>
			</thead>
			<tbody>
					<?php
	$db = new Db();
	$rows = $db -> select("SELECT * FROM cache WHERE System = " . $db->quote($targetsystem) . " AND Status <> 'Expired'");
	$strNotes = '';

	foreach ($rows as $value) {
	  echo '<tr>';
	  echo '<td class="white">'. date("Y-M-d", strtotime($value['InitialSeedDate'])) .'</td>';
	  echo '<td class="white">'. $value['Location'] .'</td>';
	  echo '<td class="white">'. $value['AlignedWith'] .'</td>';
	  echo '<td class="white">'. $value['Distance'] .'</td>';
	  echo '<td class="white">'. $value['Password'] .'</td>';
	  $statuscellformat = '';
	  if ($value['Status'] == 'Healthy') { $statuscellformat = ' style="background-color:green;color:white;"'; }
	  if ($value['Status'] == 'Upkeep Required') { $statuscellformat = ' style="background-color:yellow;"'; }
	  //if ($value['Status'] == 'Expired') { $statuscellformat = ' style="background-color:red;color:white;"'; }
	  echo '<td'.$statuscellformat.'>'. $value['Status'] .'</td>';
	  echo '<td class="white">'. date("Y-M-d", strtotime($value['ExpiresOn'])) .'</td>';
	  echo '</tr>';
	  $strNotes = $strNotes . $value['Note'];
	}
					?>
			</tbody>
		</table>
	</div>
</div>
<?php
	if (!empty($strNotes)) {
?>
<div class="ws"></div>
<div class="row" id="sysnotes">
	<div class="col-sm-12">
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">Notes</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="white"><?php echo $strNotes; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php
	}
}
// no system selected, so show compact list of all active caches
else {
?>
<div class="row" id="allsystable">
	<div class="col-sm-12">
		<table class="table" style="width: auto;">
			<thead>
				<tr>
					<th class="white">System</th>
					<th class="white">Status</th>
				</tr>
			</thead>
			<tbody>
			<?php
	$db = new Db();
	$rows = $db -> select("SELECT System, Status FROM cache WHERE Status <> 'Expired' ORDER BY System");
	$strNotes = '';

	foreach ($rows as $value) {
	  echo '<tr>';
	  echo '<td style="background-color: #cccccc;"><a href="?system='. $value['System'] .'">'. $value['System'] .'</a></td>';
	  $statuscellformat = '';
	  if ($value['Status'] == 'Healthy') { $statuscellformat = ' style="background-color:green;color:white;"'; }
	  if ($value['Status'] == 'Upkeep Required') { $statuscellformat = ' style="background-color:yellow;"'; }
	  //if ($value['Status'] == 'Expired') { $statuscellformat = ' style="background-color:red;color:white;"'; }
	  echo '<td'.$statuscellformat.'>'. $value['Status'] .'</td>';
	  echo '</tr>';
	}
					?>
			</tbody>
		</table>
	</div>
</div>
<?php
}
?>
</div>
</body>
</html>