<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$pgtitle = "YC124 Fund Drive";
$amtRaised = 61; // in billions
$amtGoal = 60;  // in billions
$percentComplete = round(($amtRaised/$amtGoal)*100);
$last_modified = date('d F', filemtime(__FILE__)) . ' YC124, ' . date('H:i', filemtime(__FILE__));  // 28 September YC124, 12:40

// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';
?>

<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
                <p class="text-center h2"><?=$amtRaised?> of <?=$amtGoal?> billion ISK<br></p>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped active" role="progressbar"
                        aria-valuenow="<?=$amtRaised?>" aria-valuemin="0" aria-valuemax="100" 
                        style="width:<?=($percentComplete > 15) ? $percentComplete : 15?>%">
                        <?=$percentComplete?>% of goal
                    </div>
                </div>
                <p class="text-center small">as of <?=$last_modified?></p>
			</div>
			<div class="panel-body">
				<p>Our rescue services are provided at no cost to those whom we set out to rescue. The funds we aim to raise will be used to pay for rescue cache supplies and compensate Signal Cartel pilots who help with our Rescue Cache and Search & Rescue programs. Our involved pilots receive a token amount of ISK for sowing and tending caches or otherwise participating in rescue efforts. Corp leadership does not benefit from raised funds unless they play a role in a rescue effort, in which case they are compensated just as a line member would be. No one is getting rich from doing this work, but we do like to thank our pilots for their service to the community.</p>

                <h3>How to Donate</h3>
                <ol>
                    <li>Send ISK to the in-game corporation 
                    <a href="https://evewho.com/corp/Signal+Cartel">Signal Cartel</a></li>
                    <li>Be sure to remark "ESR Fund Drive" on the memo line so we know what the 
                    donation is for!</li>
                </ol>
                <p>Thank you for your interest in our Rescue program and any donation you can spare! All identifiable donations of 10 million ISK or more will be acknowledged via an in-game mail message and these donors will be listed to the right (unless you send Katia Sae an evemail asking to remain anonymous). We do not reveal the amount donated.</p>

                <h3>How We Manage Our Budget</h3>
                <p>Our budget and payout formulas are described below. Tips given to rescue pilots 
                (if any) by rescued parties are personal transactions and not factored into the 
                totals listed below.</p>

                <h4>ESRC</h4>
                <p>We distribute 500 million ISK each week, split equally between all rescue caches that are sown (initially anchored) and/or tended (container accessed to reset its timer). Restrictions on sowing and tending caches are enforced by our support tools so that there is no incentive to spam sowing or tending over and over. For example, only one cache can exist per system, and caches can be tended only once every 24 hours, with a limit of once per week per cache for the same pilot.</p>

                <h4>Dispatch</h4>
                <p>Every time one of our pilots works with a stranded pilot, regardless of result, we count it as a dispatch. Every dispatch pays 1 million ISK.</p>

                <h4>SAR</h4>
                <p>Every time a pilot is successfully rescued directly by one or more of our pilots there are payouts for all involved. We calculate a possible total payout as follows:

                <p><strong>(Base Amount * Class) + (Per Day Increase * Number of Days)</strong></p>
                <ul>
                    <li><em>Base Amount:</em> 20 million ISK for same-day rescues; 50 million ISK if rescue takes longer than 24 hours</li>
                    <li><em>Per Day Increase:</em> 10m ISK</li>
                    <li><em>Class:</em> 1-6; C13 (shattered) pays out as C3; C14-C18 (Drifter) pay out as C6.</li>
                </ul>

                <p>In practice this means that rescues involving higher class wormholes pay more, as 
                do rescue requests that have been open longer. This incentivizes would-be rescue 
                pilots to look first for harder-to-access wormhole systems and requests where 
                the stranded pilot has been waiting the longest.</p>

                <p>When a rescue pilot finds a target system, half of the total payout pool is held 
                in escrow for them until the rescue is verified as successful by one of our ESR 
                Coordinators. Then the remainder of the payout pool is split between any other 
                pilots in the target system at the time the rescue is complete. The second pilot 
                in the system receives half of the Locator payout, the third pilot receives half 
                of that, etc.</p>

                <p>It is valuable to have multiple rescue pilots in system for coverage across 
                multiple time zones, in case one is rolled out, etc. However, the goal is to rescue 
                pilots, not to make ISK. Thus, diminishing returns kick in very quickly after 
                about the fourth pilot in system. And, if the stranded pilot is not rescued 
                successfully, no ISK is paid out at all.</p>

                <h3>Budget Details</h3>
                <p>(Yearly, based on the past 12 months using current payout formulas)</p>

                <h4>Expenses (Estimated)</h4>
                <ul>
                    <li><em>ESRC:</em> 26 billion ISK</li>
                    <li><em>Dispatch:</em> 2 billion ISK</li>
                    <li><em>SAR:</em> 40 billion ISK</li>
                    <li><em>Events:</em> While events sometimes draw from the ESR budget we generally try and self fund them with donations before the event.</li>
                </ul>

			</div>
		</div>
	</div>
	<div class="col-sm-4 white">
        <div style="text-align: center;">
            <a class="btn btn-success btn-md" href="../home/testimonials_list.php" role="button">
            Read Testimonials from Rescued Pilots</a>    
        </div>
        <br />
		<h4>Donors</h4> <strong>as of <?=$last_modified?></strong><br />
        <br /><br />
		<table class="white" style="width: auto;">
			<tbody>
                <tr>
                    <td class="clean text-nowrap">Tekufah</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Kendra Prime</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Lortex Gemini</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Iclil</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">IucI</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Nac Audene</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Vega Blazar</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Echerie Saissore</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Beau Gus</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Mehk'tor</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Oddore Despayre</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Bon G0</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Arianna Oksaras</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Rea Mortana</td>
                </tr>
				
				
                <tr>
                    <td class="clean text-nowrap">Meznyx Saroyan</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Mako Koskanaiken</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Das d'Alembert</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Xalyar</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Sparkler Cadelanne</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Woody Hi</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Captain Crinkle</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Tamayo</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Lexington Braddock</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Okamainen al'Anzomi</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Lixiana Vor'shan</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Kai Gray Dwayne</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Pria Tymar</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Sir Fiddle Sticks</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Amos Nakrar</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">like-shadows-into the-night</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">D'addex</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Xavec</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Alvar Weweai</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Nubris Bramblesnatc</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Igaze</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Krallia Etug</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Iralen Tarzal</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">&nbsp;</td>
                </tr>
			</tbody>
		</table>
	</div>
</div>

<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
