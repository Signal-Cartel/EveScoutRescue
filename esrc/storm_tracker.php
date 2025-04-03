<?php

// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);
session_start();
include_once '../includes/auth-inc.php';
include_once '../class/users.class.php';
require_once '../class/db.class.php';
require_once '../class/systems.class.php';
require_once '../class/storms.class.php';

$database = new Database();
$storms = new Storms($database);
$systems = new Systems($database);
$users = new Users($database);


// check if the user is alliance member
if (!Users::isAllianceUserSession())
{
	// check if last login was already a non auth user
	if (isset($_SESSION['AUTH_NOALLIANCE']))
	{
		// set redirect to root path
		$_redirect_uri = Config::ROOT_PATH;
	}
	else
	{
		// set redirect to requested path
		$_redirect_uri = $_SERVER['REQUEST_URI'];
	}

	// void the session entries on 'attack'
	session_unset();
	// save the redirect URL to current page
	$_SESSION['auth_redirect']=$_redirect_uri;
	// set a flag for alliance user failure
	$_SESSION['AUTH_NOALLIANCE'] = 1;
	// no, redirect to home page
	header("Location: ".Config::ROOT_PATH."auth/login.php");
	// stop processing
	exit;
}

// check for SAR Coordinator login
// TODO: Remove Sky Diamond here
$isCoord = ($users->isSARCoordinator($charname) ||
  $users->isAdmin($charname) ||
    $charname == "Sky Diamond");

// handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // delete record
    if (isset($_POST['formtype']) &&  $_POST['formtype'] == 'delrow') {
        $result = $storms->removeStormEntry($_POST['rowid']);
        if ($result === true) {
          $errmsg = 'Storm report removed from database.';
        } else {
            $errmsg = $result;
        }
    }
    // add new record; validate form inputs
    else {
        $observed_in_person = (isset($_POST['observed_in_person'])) ? true : false;
        $result = $storms->createStormEntry(
                $_SESSION['auth_characterid'],
                $_POST['observation_type'],
                $observed_in_person,
                $_POST['system_name']);

        // Broadcast any new storm to Discord
		/* TURNED OFF 11-SEP-2021
        $webHook = 'https://discordapp.com/api/webhooks/' . Config::DISCORDEXPLO;
        $user = 'Storm Tracker';
        $alert = 0;
        $message = "_New storm report from " . $_POST['pilot'] . "_\n" 
            . ' Strong Metaliminal '. $_POST['stormtype'] .' Ray Storm in '. $_POST['evesystem'];
        $skip_the_gif = 1;
        $result = Discord::sendMessage($webHook, $user, $alert, $message, $skip_the_gif);
		*/
        // redirect
      if ($result === true) {
        header('Location: ?observation_type=' . $_POST['observation_type']);
        exit;
      }

      switch ($result) {
        case "ESAPI-0035-0":
            $errmsg = "Invalid data format submitted ($result).";
            break;
        case "ESAPI-0036-0":
            $errmsg = "This is not a valid system name.";
          break;
        case "ESAPI-0039-0";
            $errmsg = "Storms can only appear in null sec space";
          break;
        case "ESAPI-0037-0":
            $errmsg = 'Storm is currently in this system. Duplicate entries are not required.';
          break;
        default:
            $errmsg = "Error saving data ($result)";
      }
    }
}

?>
<html>

<head>
    <meta http-equiv="Content-Language" content="en-us">
	<title>Storm Tracker :: EvE-Scout Rescue</title>
	<meta charset="utf-8">
	<!-- CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="../css/main.css?v=1598555001">
	<link rel="stylesheet" href="../css/sticky-footer.css">
	<link rel="stylesheet" href="../css/datatables_custom.css">
	<link rel="stylesheet" href="../css/pikaday.css">

	<!-- Favicon -->
	<link rel="icon" type="image/png" href="/favicon.png">
	
    <!-- JS -->
    <script src="//code.jquery.com/jquery-3.5.1.min.js"></script>   <!--REQUIRED ON THIS PAGE-->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
    <script src="../js/typeahead.bundle.min.js"></script> <!--REQUIRED ON THIS PAGE-->
    <script src="../js/validator.js"></script>
    <script src="../js/moment.min.js"></script>
	<script src="../js/pikaday.js"></script> <!-- https://dbushell.com/Pikaday/ -->
    <!-- All background images courtesy of EvE-Scout Observatory:
    		- http://observatory.eve-scout.com/
    		- https://www.flickr.com/photos/eve-scout/ -->
 

    <?php
	//load a different bg image on each new session
	if (!isset($_SESSION['selectedBg'])) {
		$bg = array('bg01.jpg', 'bg02.jpg', 'bg03.jpg', 'bg04.jpg', 'bg05.jpg', 'bg06.jpg');
		$i = rand(0, count($bg)-1);		
		$_SESSION['selectedBg'] = ($_SERVER['HTTP_HOST'] == 'dev.evescoutrescue.com' ? 'bgDev.webp' : "$bg[$i]");
	}
	?>
    <style type="text/css">
		body::before {
			background: url(../img/<?php echo $_SESSION['selectedBg']; ?>);
			position: fixed;
			opacity: .5;
		}
	</style>

</head>

<?php

$database = new Database();

if (!isset($charname))
{
	// no, set a dummy char name
	$charname = 'charname_not_set';
}

if(isset($_REQUEST['errmsg'])) { $errmsg = $_REQUEST['errmsg']; }


?>
<body class="white" style="background-color: black;">
<div class="container">

    <div class="row" id="header" style="padding-top: 20px;">
        <?php include_once '../includes/top-right.php'; ?>
        <?php include_once '../includes/top-left.php'; ?>
        <?php //include_once 'top-middle.php'; ?>

    </div>
    <div class="ws"></div>

    <!-- NAVIGATION TABS -->
    <?php include_once 'navtabs.php'; ?>

    <div class="ws"></div>

<?php
// display error message if there is one
if (!empty($errmsg)) {
?>
	<div class="row" id="errormessage" style="background-color: #ff9999;" >
		<div class="col-md-12 message">
			<?php echo nl2br($errmsg); ?>
		</div>
	</div>
	<div class="ws"></div>
<?php
}
?>

<style>
    a,
    a:visited,
    a:hover {
        color: white;
    }
</style>

<div class="row">
    <div class="col-md-7">
        <h2>Storm Tracker</h2>
        <p>Dear Signaleer, thank you for being out there chasing storms!</p>
        <p><strong>Remember: We only need the centre of the storm reported.</strong> You can find 
        it easily from your in-game map, as shown to the right. Since the centre is always of the STRONG 
        variety, that will automatically be added to your report.</p>
        <ul>
            <li>New reports can be filed as often as every 12 hours.</li>
            <li>If storm has not moved from "Last Known System," a new report is not needed.</li>
        </ul>
        <p>Thank you for your report! o7</p>

        <div class="ws"></div>

        <div class="row">
            <div class="col-md-4">
                <table class="table display" style="width: auto;">
                    <thead>
                        <tr>
                            <th class="white">Name</th>
                            <th class="white">Last Known System</th>
                            <th class="white">Type</th>
                            <th class="white">Date</th>
                            <th class="white">Hours in System</th>
                            <th class="white">Observation</th>
                            <th class="white">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    
                        <?php
                        $arrRecentReports = $storms->getRecentReports("private", false);
                        foreach ($arrRecentReports as $value) {
                            if ($value['observation_type'] == "toms_shuttle") {
                                $oddity = $value;
                                continue;
                            }
                        ?>
                        
                        <tr>
                            <td class="white text-nowrap">
                                <a href="?observation_type=<?=$value['observation_type']?>">
                                <?=Storms::getStormName($value['observation_type'])?></a></td>
                            <td class="white text-nowrap"><?=$value['system_name']?></td>
                            <td class="white text-nowrap"><? $type = explode(' ', $storms::getStormName($value['observation_type'])); echo $type[0]; ?></td>
                            <td class="white text-nowrap"><? $date = new DateTime($value['created_at']); echo $date->format("M-d"); ?></td>
                            <td class="white text-nowrap"><?=$value['hours_in_system']?></td>
                            <td class="white text-nowrap"><?=($value['observed_in_person'] === true) ? 'In-Person' : 'Map'?></td>
                            <td><?php if ($value['hours_in_system'] > 12) { ?>
                                <a type="button" class="btn btn-primary" role="button" 
                                    href="?new=1&observation_type=<?=$value['observation_type']?>">New Report</a>
                                <?php } else { echo '&nbsp;'; } ?>
                            </td>
                        </tr>

                            <?php
                        }	?>
                        <tr>
                            <td colspan="7">&nbsp;</td>
                        </tr>
                        <?php
                            $value = $oddity;
                            $observation_type = "toms_shuttle";
                        ?>
                        <tr>
                            <td class="white text-nowrap">
                                <a href="?observation_type=<?=$observation_type?>">Tom's Shuttle</a>
                            </td>
                            <td class="white text-nowrap"><?=is_null($value) ? "&nbsp;" : $value['system_name']?></td>
                            <td class="white text-nowrap"><?=Storms::getStormName($observation_type)?></td>
                            <td class="white text-nowrap"><? if (is_null($value)) { echo "&nbsp;"; } else { $date = new DateTime($value['created_at']); echo $date->format("M-d"); } ?></td>
                            <td class="white text-nowrap"><? if (!is_null($value) && $value['hours_in_system'] <= 72) {  echo $value['hours_in_system']; } else { echo "72+"; }?></td>
                            <td class="white text-nowrap"><?=(is_null($value) || $value['observed_in_person'] === true) ? 'In-Person' : 'Map'?></td>
                            <td><?php if (is_null($value) || $value['hours_in_system'] > 12) { ?>
                                    <a type="button" class="btn btn-primary" role="button"
                                       href="?new=1&observation_type=<?=$observation_type?>">New Report</a>
                              <?php } else { echo '&nbsp;'; } ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <img src="https://wiki.signalcartel.space/images/6/66/Storm_Tracking.png">
    </div>
</div>
<div class="ws"></div>

<?php
// display detail table when needed
if (isset($_REQUEST['observation_type']) &&
  Storms::getStormName($_REQUEST['observation_type']) != 'unknown') {
    $arrStormDetail = $storms->getStormReports($_REQUEST['observation_type']);  ?>

<hr class="white">

<div class="row" id="systable">
	<div class="col-sm-12">
        <p class="white">
            <?= Storms::getStormName($_REQUEST['observation_type'])?> (most recent report first)</p>
		<table class="table display" style="width: auto;">
			<thead>
				<tr>
                    <th class="white">Date</th>
                    <th class="white">System</th>
                    <th class="white">Pilot</th>
                    <th class="white">Observation</th>
                    <?php if ($isCoord) { echo '<th class="white">&nbsp;</th>'; } ?>
				</tr>
			</thead>
			<tbody>
			
			<?php
            if (empty($arrStormDetail)) {
                echo '<td colspan="5" class="white">No data available</td>';
            }
            else {
                $i = 0;
			    foreach ($arrStormDetail as $value) {	?>
				
				<tr>
                    <td class="white text-nowrap"><? $ts = new DateTime($value['created_at']); echo $ts -> format('Y-m-d H:i:s'); ?></td>
                    <td class="white text-nowrap"><?=$value['system_name']?></td>
                    <td class="white text-nowrap"><?=$value['created_by_name']?></td>
                    <td class="white text-nowrap"><?=$value['observed_in_person'] ? 'In-Person' : 'Map'?></td>
                    
                    <?php 
                    if ($isCoord) { // ESR Coords have option to delete entry
                    ?>

                    <td>
                        <form method="post" id="delform<?=$i?>" class="form-inline"
                            style="margin-bottom:-2px !important;"
                            action="<?=htmlentities($_SERVER['PHP_SELF'])?>">
                            <input type="hidden" name="formtype" value="delrow">
                            <input type="hidden" name="observation_type" value="<?=$_REQUEST['observation_type']?>">
                            <input type="hidden" name="rowid" value="<?=$value["id"]?>">
                            <button type="submit" class="btn btn-xs btn-danger">
                                <i class="fa fa-trash-o" aria-hidden="true"></i></button>
                        </form>
                    </td>

                    <?php
                    }   ?>

				</tr>

                    <?php
                    $i++;
                }
			}	?>

			</tbody>
		</table>
    </div>
</div>

<?php
}
?>

<!-- SAR New Modal Form -->
<div id="ModalNewReport" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title sechead">New Storm Report - <?=Storms::getStormName($_GET['observation_type'])?></h4>
        </div>
        
        <form name="stormform" id="stormform" action="<?=htmlentities($_SERVER['PHP_SELF'])?>" method="POST">
        
        <div class="field text-center" style="padding-top:10px;">
            <div id="sys_search">
                <input class="typeahead" placeholder="System Name" type="text" name="system_name"
                    autoFocus="autoFocus" onclick="this.select()">
            </div>
        </div>
          <?php
          if ($_REQUEST['observation_type'] == "toms_shuttle") {
            echo 
		<<<EOF
			<input type="hidden" name="observed_in_person" value="1">
EOF;
          } else {
            echo 
		<<<EOF
			<div class="field text-center">
				<label class="checkbox-inline">
					<input name="observed_in_person" type="checkbox" value="1">
					<strong>Confirmed in-person</strong>
				</label>
			</div>
EOF;
          }
        ?>
        <div class="field text-center">
            <input type="hidden" name="observation_type" value="<?=$_REQUEST['observation_type']?>">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
        
        </form>

    </div>
  </div>
</div>

<script type="text/javascript">
	// auto-display modal when "new" parameter provided in querystring
	var url = window.location.href;
	if(url.indexOf('new=') != -1) {
	    $('#ModalNewReport').modal('show');
	}

    <?php
/*     if (isset($_REQUEST['observation_type']) && $_REQUEST['observation_type'] === "toms_shuttle") {

      echo <<<EOL
    var storm_systems = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: '../resources/allSystems.json'
    });
EOL;
    } else {
      echo <<<EOL
    var storm_systems = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: '../resources/stormSystems.json'
    });
EOL;
    } */
if (isset($_REQUEST['observation_type']) && $_REQUEST['observation_type'] === "toms_shuttle") { 
    echo <<<EOL
var storm_systems = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: {
    url: '../resources/allSystems.json',
    ttl: 0
  }
});
EOL;
} else {
    echo <<<EOL
var storm_systems = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: {
    url: '../resources/stormSystems.json',
    ttl: 0
  }
});
EOL;
}

	
	
    ?>

    $('#sys_search .typeahead').typeahead(null, {
        name: 'storm_systems',
        limit: 15, 
        source: storm_systems
    });
</script>
<!-- /SAR New Modal Form -->

<style>
/* Typeahead */
.tt-menu {
    text-align: left;
}

.typeahead,
.tt-query,
.tt-hint {
    width: 100%;
    height: 30px;
    padding: 8px 12px;
    line-height: 30px;
    border: 2px solid #ccc;
    -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
            border-radius: 8px;
    outline: none;
}

.typeahead {
    background-color: #000;
}

.typeahead:focus {
    border: 2px solid #0097cf;
}

.tt-query {
    -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
}

.tt-hint {
    color: #999
}

.tt-menu {
    width: 200px;
    margin: 12px 0;
    padding: 8px 0;
    background-color: #000;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
            border-radius: 8px;
    -webkit-box-shadow: 0 5px 10px rgba(0,0,0,.2);
        -moz-box-shadow: 0 5px 10px rgba(0,0,0,.2);
            box-shadow: 0 5px 10px rgba(0,0,0,.2);
}

.tt-suggestion {
    padding: 3px 20px;
    line-height: 24px;
}

.tt-suggestion:hover {
    cursor: pointer;
    color: #fff;
    background-color: #0097cf;
}

.tt-suggestion.tt-cursor {
    color: #fff;
    background-color: #0097cf;
}

.tt-suggestion p {
    margin: 0;
}
</style>

</body>
</html>
