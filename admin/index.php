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

<a href="payoutadmin.php">Payouts</a>
<br /><br />
<a href="testimonials_admin.php">Testimonials</a>

</body>
</html>