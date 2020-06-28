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
	<div class="col-sm-8 white" style="text-align: center; vertical-align: middle;">
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
				<p class="lead">Alors, comment puis-je trouver une cache?</p>
			</div>
			<div class="panel-body">
				<p>Le processus &agrave; suivre pour localiser la cache dans l&rsquo;espace est plut&ocirc;t simple. On l&rsquo;appelle la m&eacute;thode du rebond (<em>bounce method</em>)</p>
				<p><img src="../img/BounceMethod.jpg" width="900px" style="vertical-align: middle; max-width: 100%; height: auto;" /></p>
				<ol type="1">
					<li>
						Warpez &agrave; 100 km de la plan&egrave;te d&rsquo;alignement (<em>Align Planet</em>).
					</li>
					<li>
						Warpez &agrave; 100 km de la seconde plan&egrave;te, celle o&ugrave; la cache se trouve (la &ldquo;<em>Rescue Cache Planet&rdquo;</em>).
					</li>
					<li>
						Durant l&rsquo;animation du warp, cr&eacute;er un emplacement (<em>bookmark</em>) entre environ 100 000 et 50 000km de la plan&egrave;te o&ugrave; se trouve la cache. Vous pouvez nommer cet emplacement &ldquo;T1&rdquo;. Ceci est l&rsquo;emplacement initial et est cr&eacute;&eacute; afin de r&eacute;duire le temps de voyage, mais aussi pour vous donner une id&eacute;e de la vitesse de votre vaisseau, &agrave; quelle vitesse l&rsquo;emplacement s&rsquo;enregistre, etc. Le lieu du premier emplacement est critique afin de r&eacute;duire le nombre de bonds que vous devrez faire. Voici quelques r&eacute;f&eacute;rences pour vous aider:
						<ol type="a">
							<li>
								Vaisseaux allant &agrave; 5 UA/s: Enregistrez votre emplacement quand vous franchissez une distance de 1 000 000 km de la plan&egrave;te afin que celui-ci s&rsquo;enregistre environ &agrave; 40 000 km.
							</li>
							<li>
								Vaisseaux allant &agrave; 3 UA/s: Enregistrez votre emplacement quand vous franchissez une distance de 100 000 &agrave; 200 000 km de la plan&egrave;te afin que celui-ci s&rsquo;enregistre environ &agrave; 40 000 km.
							</li>
						</ol>
					</li>
					<li>
						&Agrave; votre sortie de warp, enregistrez un deuxi&egrave;me emplacement. Appelez-le &ldquo;T2&rdquo;.
					</li>
					<li>
						C&rsquo;est maintenant le temps de faire une deuxi&egrave;me passe. Warpez vers T1.
					</li>
					<li>
						Durant le warp, enregistrer un nouvel emplacement entre T1 et T2 et appelez le &ldquo;T3&rdquo;.
					</li>
					<li>
						Quand vous arrivez &agrave; T1, v&eacute;rifier la distance &agrave; la plan&egrave;te o&ugrave; la cache se trouve. Si la distance se situe entre 22 000 km et 50 000 km, v&eacute;rifier votre scanner directionnel pour voir si vous arrivez &agrave; localiser la cache. Si ce n&rsquo;est pas le cas, warpez &agrave; T3 et v&eacute;rifiez &agrave; nouveau si vous vous situez &agrave; proximit&eacute; de la cache &agrave; l&rsquo;aide de votre scanner directionnel. Dans la n&eacute;gative, passez &agrave; la prochaine &eacute;tape.
					</li>
					<li>
						&Agrave; partir de T3, warpez &agrave; T2. Durant le warp, enregistrez &agrave; nouveau un emplacement, cette fois nomm&eacute;e &ldquo;T4&rdquo;.
					</li>
					<li>
						Quand vous arrivez &agrave; T2, vous avez compl&eacute;tez votre deuxi&egrave;me passe.
					</li>
					<li>
						Pour la troisi&egrave;me passe, revenez &agrave; T4.
					</li>
					<li>
						R&eacute;p&eacute;tez les &eacute;tapes 6-10; warpez vers l&rsquo;emplacement le plus pr&egrave;s et rapprochez-vous graduellement du lieu de la cache. Aussit&ocirc;t que votre vaisseau se trouve entre 22 000 et 50 000 km de la plan&egrave;te, commencez &agrave; v&eacute;rifier votre &eacute;cran radar (<em>Overview</em>). Aussit&ocirc;t que vous voyez la cache y apparaitre, warpez &agrave; celle-ci.
					</li>
					<li>
						Une fois localis&eacute;e, une personne dans le canal EVE-Scout saura vous donner le mot de passe pour acc&eacute;der &agrave; la cache.
					</li>
					<li>
						<strong>Une fois que vous aurez termin&eacute; d&rsquo;utiliser la cache, il serait appr&eacute;ci&eacute; si vous pouviez y retourner les fournitures afin qu&rsquo;elle soit pr&ecirc;te &agrave; aider le prochain pilote dans le besoin. Merci!</strong>
					</li>
				</ol>
				<p>
				Note: Quand vous enregistrez vos emplacement, nommez-les selon la distance &agrave; la plan&egrave;te o&ugrave; se trouve la cache (par exemple, &ldquo;23 801 km&rdquo;, &ldquo;41 087 km&rdquo;). Ceci vous permettra d&rsquo;identifier rapidement quel emplacement est le plus pr&egrave;s de chaque c&ocirc;t&eacute; de la cache. Continuez de faire l&rsquo;aller-retour: enregistrez de nouveaux emplacements, tout en supprimant les emplacements qui sont plus loin, jusqu&rsquo;&agrave; ce que vous puissiez voir la cache sur la grille et y warper. Ici, les emplacements sont nomm&eacute;s &ldquo;T1&rdquo;, &ldquo;T2&rdquo;, etc. par simplicit&eacute;. Vous avez maintenant fait votre premi&egrave;re passe!
				</p>
			</div>
		</div>
	</div>
</div>
</div>

<?php echo isset($charfooter) ? $charfooter : '' ?>

</body>
</html>
