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
	<title>List :: EvE-Scout Rescue Cache</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="/css/main.css" >
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="//cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
	<script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
	$('#sys-table').DataTable({
		"ajax": '/esrc/activecaches.php',
		"language": {
			"search": "_INPUT_",
			"searchPlaceholder": "System..."
		},
		"orderClasses": false,
		"createdRow": function (row, data, dataIndex) {
			switch(data['Status']) {
				case 'Healthy':
					$(row).addClass('healthy');
					break;
				case 'Upkeep Required':
					$(row).addClass('upkeep');
					break;
				default:
					// ¯\_(ツ)_/¯
			}
		},
		"columns": [
			{
				"data": "System",
				"searchable": true,
				"orderable": true,
				"render": function (data, type, row, meta) {
					return '<a href="/esrc/details.php?cache='+data+'"><span class="glyphicon glyphicon-link" aria-hidden="true"></span>'+data+'</a>';
				}
			},
			{"data": "Location", "searchable": false, "orderable": false},
			{"data": "AlignedWith", "searchable": false, "orderable": false},
			{"data": "Distance", "searchable": false, "orderable": false},
			{"data": "Password", "searchable": false, "orderable": false},
			{"data": "Status", "searchable": false, "orderable": true}
		]
	});
});
</script>
<style>
/* TODO: move to /css/main.css? */
body {
	/* TODO: move to /includes/head.php */
	background: url(../img/<?php echo $_SESSION['selectedBg']; ?>) no-repeat;
	background-attachment: fixed;
}
#sys-container {
	background: #fff;
	padding: 20px 8px;
	border-radius: 8px;
}
table.dataTable tbody tr.odd.healthy {
	background-color: #d9ffd9;
}
table.dataTable tbody tr.even.healthy {
	background-color: #cfffcf;
}
table.dataTable tbody tr.odd.healthy:hover {
	background-color: #ffffff;
}
table.dataTable tbody tr.even.healthy:hover {
	background-color: #ffffff;
}
table.dataTable tbody tr.odd.upkeep {
	background-color: #ffffd9;
}
table.dataTable tbody tr.even.upkeep {
	background-color: #ffffcf;
}
table.dataTable tbody tr.odd.upkeep:hover {
	background-color: #ffffff;
}
table.dataTable tbody tr.even.upkeep:hover {
	background-color: #ffffff;
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
		<span style="font-size: 125%; font-weight: bold; color: white;">Search Rescue Cache Database</span><a href="esrc_data_entry.php" class="btn btn-link" role="button">Go to Data Entry</a><br>
	</div>
	<div class="col-md-2" style="text-align: center; padding-top: 10px;">
		<div style="text-align: right;">
			<?php echo isset($chardiv) ? $chardiv : '' ?>
		</div>
	</div>
</div><!-- #header -->
<div class="ws"></div>
<div class="row" id="sys-container">
	<!-- TODO: make compact version instead? add contribution statistics column -->
	<div class="col-sm-12">
		<table id="sys-table" class="display table">
			<thead>
				<tr>
					<th>System</th>
					<th>Location</th>
					<th>Aligned with</th>
					<th>Distance</th>
					<th>Password</th>
					<th>Status</th>
				</tr>
			</thead>
		</table>
	</div>
</div><!-- #sys-container -->
<div class="ws"></div>
</div><!-- .container -->
</body>
</html>