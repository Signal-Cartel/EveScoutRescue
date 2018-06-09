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
		<br /><span class="sechead">Bounce Method Instructions</span><br /><a href="index.php">English</a><br />
		Please join the in-game channel <span style="color: gold; font-weight: bold;">EvE-Scout</span> for further assistance.
	</div>
	<?php include_once '../includes/top-right.php'; ?>
</div>
<div class="ws"></div>
<div class="row">
	<div class="col-sm-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<p class="lead">Как же мне найти спасательный тайник?</p>
			</div>
			<div class="panel-body">
				<p>Очень просто. У нас есть специальный «метод прыжков»</p>
				<p><img src="../img/BounceMethod_ru.png" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
                <p>Прежде всего добавьте в обзор объект «Small Secure Container» - это поможет вам видеть тайник на сканере. Далее выполните следующие действия:</p>
				<ol>
					<li>Варп на 100 км планету-ориентир (она же «Align Planet»)</li>
					<li>Варп на 100 км на планету-тайник (она же «Rescue Cache Planet»)</li>
					<li>Во время варпа сделайте хотя бы одну закладку. Воспользуйтесь сочетанием клавиш Ctrl+B. Помните, что закладка создаётся в момент нажатия кнопки «сохранить», а не открытия окна постановки закладки. Постарайтесь сделать первую же закладку на расстоянии от 100.000 до 50.000 км до планеты-тайника. Так вы уменьшите количество «прыжков», которые вам придётся сделать. Чтобы как можно точнее поставить первую закладку, обратите внимание на скорость варпа корабля:<br />
						- при скорости 5 au/s сделайте закладку, когда корабль будет проходить отметку 1.000.000 км до планеты с тайником.<br />
						- при скорости 3 au/s сделайте закладку, когда корабль будет проходить отметку 100.000 / 200.000 км до планеты с тайником.<br /></li>
						Выйдя из варпа, переименуйте закладки соответственно расстоянию, на котором они находятся от планеты – например, «21.000», «43.000». Остановившись после варпа, сделайте ещё одну закладку, назовём её условно «Т2», ваша первая (дальняя от планеты) закладка будет называться условно «Т1». Поздравляем, первый проход (или прыжок) окончен. 
					<li>Теперь выполните варп обратно на закладку Т1, и в варпе сделайте новую закладку, назовём её «Т3».</li>
					<li>Проверьте расстояние между закладкой Т3 и планетой. Если оно составляет от 22000 до 50000 км, вы близки к цели. Наши пилоты располагают тайники в этом диапазоне. Если вы окажетесь примерно в 10.000 км от тайника, то увидите его в обзорной панели и сможете выполнить варп непосредственно на сам тайник. Пароль от тайника нужно узнать у пилота корпорации Signal Cartel, который помогает вам.</li>
					<li>Открыв тайник, заберите пробки и лаунчер. Если это будет для вас безопасно, то найдя выход из вормхола, постарайтесь вернуть их в тайник – их сможет использовать следующий пилот, потерявшийся в этой системе. Спасибо!</li>
				</ol>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>