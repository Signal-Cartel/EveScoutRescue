<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
?>

<html>
<head>
	<title>Admin Index</title>
</head>

<body>

<a href="testimonials_admin.php">Testimonials</a>
<br /><br />
<a href="payoutadmin.php">Payouts</a>
<br /><br />
<br /><br />

<a href="stats_records_admin.php">Stats Records</a>
<br /><br />
<a href="user_roles_admin.php">User Roles</a>
<br /><br />

</body>
</html>