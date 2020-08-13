<?php
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$pgtitle = 'Admin Index';


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<style>
	a,
	a:visited,
	a:hover {
		color: white;
	}
</style>

<a href="testimonials_admin.php">Testimonials</a>
<br /><br />
<a href="payoutadmin.php">Payouts</a>
<br /><br />
<a href="payout_optout_admin.php">ESRC Payout Opt-out</a>
<br /><br />
<a href="medals_admin.php">Medals</a>
<br /><br />
<br /><br />

<a href="no_sow_admin.php">No Sow</a>
<br /><br />
<a href="stats_records_admin.php">Stats Records</a>
<br /><br />
<a href="user_roles_admin.php">User Roles</a>
<br /><br />
<a href="user_stats.php">Individual User Stats</a>
<br /><br />


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
