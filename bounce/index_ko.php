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
        <a href="index.php">English</a> - <a href="index_ru.php">русский</a><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">그래서, 어떻게 구조용 캐시 (Rescue Cache) 를 찾을 수 있나요?</p>
			</div>
			<div class="panel-body">
				<p>아래에 적혀진 내용은 우주에서 어떻게 구조용 캐시 (Rescue Cache) 를 찾는지 그 프로세스에 대한 내용입니다. 이 방법을 “바운싱 방법” (bounce method) 이라고 부릅니다.</p>
				<p><img src="../img/BounceMethod.jpg" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
				<ol>
					<li>행성 (Align Planet) 에 100km 워프를 합니다.</li>
					<li>또 다른 행성 (Rescue Cache Planet) 에 100km 워프를 합니다.</li>
					<li>워프하는 중에, Rescue Cache Planet 의 50,000km ~ 100,000km 사이에 북마크를 만들고 이를 “T1” 이라고 이름짓습니다. 처음 만들어진 이 북마크는 워프 시간을 줄이고 정확도를 올리기 위해 필요합니다. 이 첫번째 북마크의 위치는 바운싱 횟수를 줄이는 것에 큰 영향을 미칩니다. 아래 가이드라인에 따라 북마크를 만들어주세요.<br />
						- 5AU/s 배들: 배가 1,000,000 km 를 지날 때 북마크를 만들면(Submit 버튼을 누르면), 40,000 km 근처 지점에 북마크가 만들어집니다.<br />
						- 3AU/s 배들: 배가 100,000 과 200,000 km 사이를 지날 때 북마크를 만들면(Submit 버튼을 누르면), 40,000 km 근처 지점에 북마크가 만들어집니다.<br /></li>
						<i>노트:</i> 북마크를 정할 때, 북마크의 이름을 행성과의 거리로 정하세요. (i.e. "23,801km", "42,087km"). 이 과정을 통해 어떤 북마크가 구조용 캐시(Rescue Cache) 에서 가장 가까이 있는지 쉽게 알 수 있습니다. 바운싱을 계속하면서 캐시에 도달할 때까지, 캐시에서 거리가 먼 북마크들은 지워나가십시오. 설명하는 과정에서 "T1", "T2" 와 같은 북마크 이름을은 설명의 편의를 위해 사용됩니다. 
					<li>워프를 끝냈다면, 그 곳에 북마크를 만들고 "T2" 와 같은 이름을 짓습니다. 이것으로 pass #1 을 끝냈습니다.</li>
					<li>이제 pass #2 를 진행해야 합니다. T1 북마크로 워프해 돌아가세요</li>
					<li>워프하는 도중에, T1 과 T2 사이에 북마크를 만들고, 이를 "T3" 로 이름짓습니다.</li>
					<li>T1 북마크게 도착하게 되면, Rescue Cache Planet 과의 거리를 체크합니다. 거리가 22,000 과 50,000 km 사이인가요? 만약 그렇다면, D-Scan 을 이용하여 캐시를 찾으십시오. 그렇지 않다면, T3 로 돌아가서 다음 스텝을 진행하십시오.</li>
					<li>T3 에서 T2 로 워프합니다. 워프하는 도중에, 북마크를 만들고 이를 "T4" 로 이름짓습니다.</li>
					<li>T2 에 도착했으면, 당신은 pass #2 를 끝낸 것입니다.</li>
					<li>이제 pass #3 를 진행해야 합니다. T4 북마크로 워프해 돌아가세요</li>
					<li>6-10 단계를 반복하면, 반복할 때마다 가까운 북마크로 워프하면서 점점 원하는 거리에 가까워지게 됩니다. Rescue Cache Plaent 에서 22,000 ~ 50,000 km 사이에 도달했다면, D-Scan 을 통해 구조용 캐시(Rescue Cache) 를 찾으십시오. 오버뷰에 구조용 캐시 (Rescue Cache) 가 나타나면 그 곳으로 워프하십시오.</li>
					<li>당신이 구조용 캐시(Rescue Cache) 를 발견했다면, EvE-Scout 채널의 누군가가 당신에게 캐시에 접근할 수 있는 패스워드를 줄 것입니다.</li>
					<li><strong>구조용 캐시(Rescue Cache) 사용을 끝냈을 때, 가능하다면 발견했던 장소로 가서 썼던 물건들을 다시 돌려주시기 바랍니다. 그렇게 함으로써, 다음번 길을 잃은 파일럿이 캐시를 재사용할 수 있게 될 겁니다. 감사합니다!</strong></li>
				</ol>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>