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
<!doctype html>
<html lang="en"><!-- TODO: in other files - use proper html5 langauge attr instead of meta tag; same goes for !doctype -->
<head>
	<!-- TODO: possibly merge with /includes/head.php -->
	<title>Details :: EvE-Scout Rescue Cache</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/css/main.css" >
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<style>
body {
	/* TODO: move to /includes/head.php */
	background: url(../img/<?php echo $_SESSION['selectedBg']; ?>) no-repeat;
	background-attachment: fixed;
}
#sys-container {
	background: #fff;
	padding: 20px 14px; /* TODO: different from list.php */
	border-radius: 8px;
}
</style>
</head>
<body>
<div class="container">
<div class="row" id="header">
	<div class="col-md-2" style="padding-top: 10px;">
		<div style="text-align: center;">
			<a href="/"><img src="/img/eve-scout-logo.png" alt="EvE-Scout Rescue Cache"></a>
		</div>
	</div>
	<div class="col-md-8" style="padding-top: 10px; text-align: center;">
		<span style="font-size: 125%; font-weight: bold; color: white;">Search Rescue Cache Database</span><a href="/esrc/list.php" class="btn btn-link" role="button">Back to list</a><br>
	</div>
	<div class="col-md-2" style="text-align: center; padding-top: 10px;">
		<div style="text-align: right;">
			<?php echo isset($chardiv) ? $chardiv : '' ?>
		</div>
	</div>
</div><!-- #header -->
<div class="ws"></div>
<div class="row" id="sys-container">
<?php
$cache = strtoupper($_GET["cache"]);
include $_SERVER['DOCUMENT_ROOT'].'/class/class.db.php';
try {
	$db = new Db();
	$q = $db->prepare("SELECT System,Location,AlignedWith,Distance,Password,Status,ExpiresOn,Note,InitialSeedDate FROM cache WHERE System LIKE ? AND Status <> 'Expired' ORDER BY System");
	if ($cache && strlen($cache) == 7) {
		$q->execute([$cache]);
	}
	else {
		throw new Exception("System id is not valid!");
	}
	$r = $q->fetchAll(PDO::FETCH_ASSOC);
	if (count($r) < 1) {
		throw new Exception("Could not find cache in given system!");
	}
	$cache = $r[0];
	echo "<h3>".$cache["System"]."</h3>".
		 "<p>".
			"<b>Status:</b> ".$cache["Status"]."<br>".
			"<b>Location:</b> ".$cache["Location"]."<br>".
			"<b>Aligned with:</b> ".$cache["AlignedWith"]."<br>".
			"<b>Distance:</b> ".$cache["Distance"]."<br>".
			"<b>Password:</b> ".$cache["Password"]."<br>".
			"<b>Seeded on:</b> ".$cache["InitialSeedDate"]."<br>".
			"<b>Expires on:</b> ".$cache["ExpiresOn"]."<br>".
		 "</p>";
	if (!empty($cache["Note"])) {
		echo "<p>".$cache["Note"]."</p>";
	}
}
catch (Exception $e) {
	echo "<h3>Error has occurred</h3><p>".$e->getMessage()."</p>";
}
?>
</div><!-- #sys-container -->
<div class="ws"></div>
</div><!-- .container -->
</body>
</html>