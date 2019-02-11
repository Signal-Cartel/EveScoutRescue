<?php 

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

//include_once '../includes/auth-inc.php'; 

//require_once '../class/db.class.php';
//require_once '../class/leaderboard.class.php';
//require_once '../class/users.class.php';

//$database = new Database();
//$leaderBoard = new Leaderboard($database);
//$users = new Users($database);
?>
<html>

<head>
	<?php
	$pgtitle = "YC121 Fund Drive";
	include_once '../includes/head.php';
	?>
	<style>
		td.clean {
		    padding: 3px;
		}
		</style>
</head>
<body>
<div class="container">
<div class="row" id="header" style="padding-top: 10px;">
<?php
include_once '../includes/top-left.php';
//include_once '../includes/top-center.php';
//include_once '../includes/top-right.php';
?>
    <div class="col-sm-8 white" style="text-align: center;"><br />
	    <span class="sechead">YC121 Fund Drive</span></div>
</div>
<div class="ws"></div>

<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading clearfix">
                <div>
                    <h3 class="pull-left">Raised: 63.45 of 60 billion ISK</h3>
                    <h3 class="pull-right">Thank You!</h3>
                </div>
                <br />
                <div class="dollartimes-pb" style="font-family: arial; width: auto; box-sizing: border-box; clear:both;">
                    <div>
                        <div class="dollartimes-pb-frame" title="10 billion ISK / 20% towards goal" style="border-radius: 5px; background-color: #ffffff;padding: 0px;border: 1px solid #000; height: 30px; margin: 2px 0 1px;">
                            <div class="dollartimes-pb-fill" style="width:100%; height: 100%; margin-top: 0px; background: repeating-linear-gradient(-45deg, rgba(74,134,232,1), rgba(74,134,232,1) 8px, rgba(74,134,232,0.8) 8px, rgba(74,134,232,0.8) 16px);">&nbsp;</div>
                        </div>
                        <span class="dollartimes-pb-caption" style="float: left; font-size: 12px;">63.45 billion ISK</span>
		                <span class="dollartimes-pb-caption" style="float: right; font-size: 12px;">60 billion ISK</span>
                    </div>
                    <div style="clear: both;"></div>
                </div>


			</div>
			<div class="panel-body">
				<p>Our rescue services are provided at no cost to those whom we set out to rescue. 
                The funds we aim to raise will be used to pay for rescue cache supplies and 
                compensate Signal Cartel pilots who help with our Rescue Cache and Search &amp; 
                Rescue programs. Our involved pilots receive a token amount of ISK for sowing and 
                tending caches or otherwise participating in rescue efforts. Corp leadership does 
                not benefit from raised funds unless they play a role in a rescue effort, in which 
                case they are compensated just as a line member would be. No one is getting rich 
                from doing this work, but we do like to thank our pilots for their service to 
                the community.</p>

                <h3>How to Donate</h3>
                <ol>
                    <li>Send ISK to the in-game corporation 
                    <a href="https://evewho.com/corp/Signal+Cartel">Signal Cartel</a></li>
                    <li>Be sure to remark *ESR Fund Drive” on the memo line so we know what the 
                    donation is for!</li>
                </ol>
                <p>Thank you for your interest in our Rescue program and any donation you can spare! 
                All identifiable donations of 10 million ISK or more will be acknowledged via an 
                in-game mail message and these donors will be listed to the right (unless you 
                send Thrice Hapus an evemail asking to remain anonymous). We do not reveal amount 
                donated.</p>

                <h3>How We Manage Our Budget</h3>
                <p>Our budget and payout formulas are described below. Tips given to rescue pilots 
                (if any) by rescued parties are personal transactions and not factored into the 
                totals listed below.</p>

                <h4>ESRC</h4>
                <p>We distribute 500 million ISK each week, split equally between all rescue caches 
                that are sown (initially anchored) and/or tended (container accessed to reset its 
                timer). Restrictions on sowing and tending caches are enforced by our support tools 
                so that there is no incentive to spam sowing or tending over and over. For example, 
                only one cache can exist per system, and caches can be tended only once every 24 
                hours, with a limit of once per week per cache for the same pilot.</p>

                <h4>SAR</h4>
                <p>We calculate ISK payouts for successful SAR operation as follows:</p>
                <p><strong>(50 million ISK x WH Class[max 8]) + (10 million ISK x # days active)</strong></p>

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

                <p>SAR Dispatchers (pilots who field rescue requests from EvE-Scout, our in-game 
                public channel and then enter requests into our request ticketing system) are paid 
                1 million ISK for each request they enter, regardless of the outcome of the rescue 
                operation.</p>

                <h3>Budget Details</h3>
                <p>Actual spending in YC120 and projections for YC121 spending can be found 
                <a href="https://docs.google.com/spreadsheets/d/16qeZzyS4h7pnT7NBHoxlWtaT-hKsAS2lT3YZ8Rj8d0w/edit?usp=sharing" 
                target="_blank">here</a>.</p>
			</div>
		</div>
	</div>
	<div class="col-sm-4 white">
        <div style="text-align: center;">
            <a class="btn btn-success btn-md" href="../home/testimonials_list.php" role="button">
            Read Testimonials from Rescued Pilots</a>    
        </div>
        <br />
		<h4>Donors</h4> <strong>as of 11 February YC121, 13:00</strong><br />
        (most recent donor listed first)
        <br /><br />
		<table class="white" style="width: auto;">
			<tbody>
                <tr>
                    <td class="clean text-nowrap">Vin Noir</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">&nbsp;</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Ulf Andersson</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Pai Shen-Lung</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Alexander Trekkos</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Dagmar Maulerant</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Julien Gray</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Boci</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Meril Rapier</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Takeshi Coldstream</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Aerilan</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Lizbeth Solare</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Cal Tamdar</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Tam Pollard</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Arthur Dentz</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Will Thethrill</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Istari Storm</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Lexington Braddock</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Pod Person</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Orsel Solette</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Malachite Ormand</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Angel Lafisques</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Mordino</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Cozy Glow</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Xandrosa</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Chaim Achasse</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">DaydreamBeliever</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Godezz</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">doratheexplorer</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">miruxa</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">oskarsh Rin</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Plucky Risa-Purcell</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Alexia Maxwell</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Aldar Roanaok</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Tais Sabaki</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Igaze</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Katia Sae</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Caleb Wolfram</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Tamayo</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Xalyar</td>
                </tr>
				<tr>
                    <td class="clean text-nowrap">Naelu Annages</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Captain Crinkle</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Asa Kansene</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Eratos Thellere</td>
                </tr>
                <tr>
                    <td class="clean text-nowrap">Sky Diamond</td>
                    <td width="15px">&nbsp;</td>
                    <td class="clean text-nowrap">Bliss Dwellerya</td>
                </tr>
			</tbody>
		</table>
	</div>
</div>

</div>
</body>
</html>