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
		<br /><span class="sechead">跳ね返りの説明</span><br /><a href="index_ko.php">한국어</a> - <a href="index_ja.php">日本語</a> - <a href="index_ru.php">русский</a><br />
		お問い合わせはゲーム内チャンネル<span style="color: gold; font-weight: bold;">EvE-Scout</span>でお願いいたします。
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">さて、どうやって救難コンテナにたどり着くのか</p>
			</div>
			<div class="panel-body">
				<p>救難コンテナを見つけ出す方法はそこまで難しくないです。「跳ね返り」("Bounce")といいます。</p>
				<p><img src="../img/BounceMethod.jpg" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
				<ol>
					<li>軸合わせ惑星("Align Planet")から100km以内にワープします。</li>
					<li>目的惑星("Rescue Cache Planet")から100km以内にワープします。</li>
					<li>ワープ中、目的惑星からおよそ100,000kmから50,000km離れてるタイミングで「位置を保存...」っていうショートカットで新規位置を作ります。例としてT1を名づけます。この最初の位置はワープする距離を短くする他、自分の船のワープ速度に慣れるにも大事です。例えば：<br />
						・ ワープ速度が5AU/sの場合、1,000,000kmを通った瞬間で位置を保存したら惑星から40,000kmになります。<br />
						・ ワープ速度が3AU/sの場合、100,000kmから200,000kmを通った瞬間で位置を保存したら惑星から40,000kmになります。<br /></li>
						<i>参考：</i> 普段は位置を保存するときに目的惑星からの距離で名前を付けると便利です。跳ね返り続けながら不要になった数字の高い位置を削除したらスムーズに救難コンテナに近づきます。この説明では簡潔にするために位置の名前を「T1」「T2」などにします。 
					<li>ワープが終わったら、「T2」ていう位置を保存します。一周目が終わりました！</li>
					<li>二周目を始めます。「T1」に戻ります。</li>
					<li>ワープ中、「T1」と「T2」の間に「T3」という位置を保存します。</li>
					<li>「T1」に着いたら、目的惑星からの距離を確認します。22,000kmから50,000kmだったら、救難コンテナがないか指向性スキャンで探します。距離が合ってない場合、「T3」にワープして次に進みます。</li>
					<li>「T3」から「T2」にワープします。ワープ中、「T4」という位置を保存します。</li>
					<li>「T2」に着いたら、二周目が終わりました！</li>
					<li>三周目を始めます。「T4」にワープします。</li>
					<li>目的の距離に近づきながら、ステップ６から１０を繰り返します。救難コンテナ(小型セキュアコンテナ)がオーバービューに現れたら、直接ワープします。</li>
					<li>たどり着いたら、ゲーム内チャンネルEvE-Scoutの誰かがパスワードを教えます。</li>
					<li><strong>救難コンテナを使い終わったら、中身を補充していただけたら幸いです。次に遭難されてしまった方が助けられるように、ご協力を感謝いたします！</strong></li>
				</ol>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>
