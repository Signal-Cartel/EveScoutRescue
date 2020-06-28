<?php
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
?>
<html>

<head>
<?php 
$pgtitle = 'Bounce Method';
include_once '../includes/head.php'; 
?>

<style>
.links a {
	font-size: 1.4em;
    color: rgba(255, 255, 255, 0.8);
}
</style>

</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 white links" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">Bounce Method Instructions</span><br /><br />
		<a href="index_en.php">English</a><br />
		<a href="index_fr.php">French</a><br />
		<a href="index_zh.php">中文</a><br />
		<a href="index_ja.php">日本語</a><br />
		<a href="index_ko.php">한국어</a><br />
		<a href="index_ru.php">русский</a><br />
		<br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>

</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>
