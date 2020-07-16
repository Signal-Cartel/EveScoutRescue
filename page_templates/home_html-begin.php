<?php
/**
 * Page(s) that include this page:
 * - all pages in /secure/
 * 
 * Required:
 * - None
 * 
 * Optional Parameters
 * - $pgtitle, string - page title
 * 
 * jmh - 20200716
 */

 // page cannot be accessed directly
if (!defined('ESRC')) { die ('Direct access not permitted'); }
?>

<!-- PAGE TEMPLATE BEGIN -->
<html>
<head>
	<!-- GLOBAL HEAD TAG -->
	<?php include 'head.php'; ?>
	<!-- PAGE HEAD TAG -->
	<title><?=$pgtitle ?? ''?> :: EvE-Scout Rescue</title>
</head>

<body>
<div class="container">
<div class="ws"></div>

<!-- PAGE TOP NAVIGATION -->
<div class="row" id="header">

    <?php
    include 'top-right.php';
    include 'top-left.php';
    include 'top-center.php';    ?>

</div>
<!-- /PAGE TOP NAVIGATION -->

<div class="ws"></div>
<!-- /PAGE TEMPLATE BEGIN -->

<!-- PAGE CONTENT -->
