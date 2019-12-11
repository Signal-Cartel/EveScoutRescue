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
</head>

<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
	<?php include_once '../includes/top-left.php'; ?>
	<div class="col-sm-8 white" style="text-align: center; height: 100px; vertical-align: middle;">
		<br /><span class="sechead">Bounce Method Instructions</span><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">那么，我应该怎样寻找救援物资箱呢？</p>
			</div>
			<div class="panel-body">
				<p>方法如下：</p>
				<p><img src="../img/BounceMethod.jpg" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
				<ol>
					<li>先跃迁至Align Planet行星的100KM。</li>
					<li>跃迁至“Rescue Cache Planet”行星的100KM。</li>
					<li>跃迁途中，在距离Rescue Cache Planet这颗行星50,000—100,000KM时做一个临时坐标并取名为“T1”（见上图）。第一次跃迁是很重要的一步，如果点的位置做是正确的，他能让你省去很多次的来回跃迁。以下是不同速度的船递交坐标的时机：<br />
						- -如果你的跃迁速度是每秒5AU：在落地前1,000,000KM递交以得到离行星40,000KM左右的坐标。<br />
						- -如果你的跃迁速度是每秒3AU：在落地前100,000到200，000KM递交以得到离行星40,000KM左右的坐标。<br /></li>
						记得在这些点的名字后加上距离行星XXX千米以便识别。
					<li>落地时,保存另一个点命名为T2，完成第一轮跃迁。</li>
					<li>开始第二轮跃迁，跳回T1这个坐标</li>
					<li>在跃迁中，再次上传一个点命名为T3.</li>
					<li>当你到达T1，检查一下你离Rescue Cache Planet行星的距离是不是在22,000KM和50,000KM之间。如果是，使用你的d-scan检查一下有没有箱子。 如果有，跃迁至那个箱子并在“EvE-Scout”这个游戏内频道询问箱子的密码。如果没有箱子或无法跃迁至箱子，继续下面的步骤。</li>
					<li>从T3跃迁至T2，途中上传一个新坐标取名T4。</li>
					<li>等你到达T2，你已经完成了第二轮跃迁。</li>
					<li>第三轮开始，跃迁至T4</li>
					<li>重复上面的步骤(6-10)直到你慢慢的接近救援箱（直到可以跃迁他，无论是在D-scan上还是在总览上）。</li>
					<li>你可以在“EvE-Scout”这个游戏内频道询问到救援箱的密码</li>
					<li><strong>等你用完了这个箱子里的装备，请把他们原封不动的放回箱子里，这样下一个迷路的驾驶员还能继续用箱子里的装备。谢谢！</strong></li>
				</ol>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>
