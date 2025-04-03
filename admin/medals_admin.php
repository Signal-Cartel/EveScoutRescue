<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include_once '../includes/auth-inc.php';// this establishes db connection as well
//require_once '../class/db.class.php';
//require_once '../class/leaderboard.class.php';
require_once '../class/output.class.php';

// create object instance(s)
$db = new Database();
//$lb = new Leaderboard($db);

$medals = [
    1 => [
        "name" => "SuperCacher",
		"act" => "for caching ",
        "min" => 100,
        "max" => 299
    ],
    2 => [
        "name" => "MegaCacher",
		"act" => "for caching ",
        "min" => 300,
        "max" => 499
    ],
    3 => [
        "name" => "HyperCacher",
		"act" => "for caching ",
        "min" => 500,
        "max" => 999
    ],
    4 => [
        "name" => "UltraCacher",
		"act" => "for caching ",
        "min" => 1000,
        "max" => 2999
    ],
    5 => [
        "name" => "Heroic Cacher",
		"act" => "for caching ",
        "min" => 3000,
        "max" => 4999
    ],
    6 => [
        "name" => "Insane Cacher",
		"act" => "for caching ",
        "min" => 5000,
        "max" => 9999
    ],
    7 => [
        "name" => "The Crinkle Crown",
		"act" => "for caching ",
        "min" => 10000,
        "max" => 49999
    ],
    8 => [
        "name" => "The Crinkle Crown",
		"act" => "for caching ",
        "min" => 50000,
        "max" => 999999
    ],
    11 => [
        "name" => "SAR Bronze",
		"act" => "for rescuing ",
        "min" => 1,
        "max" => 9
    ],
    12 => [
        "name" => "SAR Silver",
		"act" => "for rescuing ",
        "min" => 10,
        "max" => 49
    ],
    13 => [
        "name" => "SAR Gold",
		"act" => "for rescuing ",
        "min" => 50,
        "max" => 99
    ],
    14 => [
        "name" => "Beacon of Anoikis",
		"act" => "for rescuing ",
        "min" => 100,
        "max" => 499
    ],
    21 => [
        "name" => "Disp - Qualified",
		"act" => "for dispatching ",
        "min" => 5,
        "max" => 49
    ],
    22 => [
        "name" => "Disp - Proficient",
		"act" => "for dispatching ",
        "min" => 50,
        "max" => 99
    ],
    23 => [
        "name" => "Disp - Master",
		"act" => "for dispatching ",
        "min" => 100,
        "max" => 999
    ],
];




$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : 0;
$pilot = isset($_REQUEST['pilot']) ? $_REQUEST['pilot'] : 0;
$showall = isset($_REQUEST['showall']) ? intval($_REQUEST['showall']) : 0;
$medalid = isset($_REQUEST['medalid']) ? intval($_REQUEST['medalid']) : 1;
	$min = intval($medals[$medalid]['min']);
	$max = intval($medals[$medalid]['max']);
$arrow = '&nbsp;<i class="fa fa-arrow-left"></i>';



// HANDLE FORM SUBMIT
// add new medal for specified pilot
if ($rowid == -1) {
	// add new medal
	$db->query("INSERT INTO medals (pilot, medalid, dateawarded) VALUES (:pilot, :medalid, CURDATE())");
	$db->bind(':pilot', $_REQUEST['username']);
	$db->bind(':medalid', $_REQUEST['medalid']);
	$db->execute();
}
// edit existing medal
elseif ($rowid > 0) {
	// delete row
	if ($_REQUEST['del'] == 1) {
		$db->query("DELETE FROM medals WHERE id = :id");
		$db->bind(':id', $rowid);
		$db->execute();
	}
	// edit row
	else {
		$db->query("UPDATE medals SET medalid = :medalid, dateawarded = :dateawarded WHERE id = :id");
		$db->bind(':medalid', $_REQUEST['medalid']);
		$db->bind(':dateawarded', $_REQUEST['dateawarded']);
		$db->bind(':id', $rowid);
		$db->execute();
	}
}
?>
<html>

<head>
	<?php
	$pgtitle = 'Medals Admin';
	include_once '../includes/head.php'; 
	?>
	<style>
	a {color: #65bbff;}
	</style>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Medals Admin</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
			<div class="white">
            <p class="sechead white">Medals</p>
			<div style="display: flex; justify-content: space-evenly; flex-wrap: wrap;">
				<div>
				<p>
				<a href="?medalid=1">SuperCacher</a><?php echo ($medalid==1)? $arrow : '' ?><br>
				<a href="?medalid=2">MegaCacher</a><?php echo ($medalid==2)? $arrow : '' ?><br>
				<a href="?medalid=3">HyperCacher</a><?php echo ($medalid==3)? $arrow : '' ?><br>
				<a href="?medalid=4">UltraCacher</a><?php echo ($medalid==4)? $arrow : '' ?><br>
				<a href="?medalid=5">Heroic Cacher</a><?php echo ($medalid==5)? $arrow : '' ?><br>
				<a href="?medalid=6">Insane Cacher</a><?php echo ($medalid==6)? $arrow : '' ?><br>
				<a href="?medalid=7">The Crinkle Crown</a><?php echo ($medalid==7)? $arrow : '' ?><br>
				<a href="?medalid=8">The Renek Regalia</a><?php echo ($medalid==8)? $arrow : '' ?>
				</p>
				</div>
				<div>
				<p>
				<a href="?medalid=11">SAR Bronze</a><?php echo ($medalid==11)? $arrow : '' ?><br>
				<a href="?medalid=12">SAR Silver</a><?php echo ($medalid==12)? $arrow : '' ?><br>
				<a href="?medalid=13">SAR Gold</a><?php echo ($medalid==13)? $arrow : '' ?><br>
				<a href="?medalid=14">Beacon of Anoikis</a><?php echo ($medalid==14)? $arrow : '' ?>
				</p>
				</div>
				<div>
				<p>
				<a href="?medalid=21">Dispatch - Qualified</a><?php echo ($medalid==21)? $arrow : '' ?><br>
				<a href="?medalid=22">Dispatch - Proficient</a><?php echo ($medalid==22)? $arrow : '' ?><br>
				<a href="?medalid=23">Dispatch - Master</a><?php echo ($medalid==23)? $arrow : '' ?>
				</p>
				</div>
			</div>
		</div>
		
	<?php
	//show list if no ID is specified
	if ($pilot == 0) {	
	?>
    <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
		<div style ="background: #21435e;text-align: center;width: 72%;margin: 8px;">
			<p class="sechead white text-uppercase" style="margin-bottom: -8px;"><?= $medals[$medalid]['name'] ?></p>
			<p class="sechead white" style="font-size: smaller;">(<? echo $medals[$medalid]['act'] . $medals[$medalid]['min'] . " to " . $medals[$medalid]['max'];?>)</p>
		</div>
	</div>

    <div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
		<div>

            <p class="white" style="text-align: center;">Awarded</p>
			<table id="" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">Pilot</th>
                        
						<th class="white">Date</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$db->query("SELECT * FROM medals WHERE medalid = ". $medalid . " ORDER BY dateawarded DESC");
				$rows = $db->resultset();
				$db->closeQuery();
				
				$awarded_pilots = Array();
				foreach ($rows as $value) {
					$awarded_pilots[] = strtolower($value['pilot']);
					echo '<tr>';
					echo '<td class="white text-nowrap">' . $value['id'] . '</td>';
					echo '<td class="text-nowrap"><a href="?medalid='. $medalid .'&pilot='. $value['pilot'] .'">'. 
							$value['pilot'] .'</a></td>';
					echo '<td class="white">' . $value['dateawarded'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
        <div>
            <p class="white"style="text-align: center;">Qualifying Activity 
			<?
			if($showall){
				?>
				
				<small><a href="?medalid=<?=$medalid ?>&showall=0">(show qualifying)</a></small>
				
				<?
				}
				else{
				?>
				
				<small><a href="?medalid=<?=$medalid ?>&showall=1">(show all)</a></small>
				
				<?	
			}			
			?>
			</p>
			<style>
			.spin {
				  animation: rotate 1.0s;
				}

			@keyframes rotate {
			  0%   {color: #65bbff;}
			  25%  {color: black;}
			  50%  {color: #65bbff;}
			  100% {color: black;}
			}
			</style>
			<table id="" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">Pilot</th>
						<th class="white">Count</th>
                        <th class="white">Last Action</th>
						<th>*</th>
					</tr>
				</thead>
				<tbody>
				<?php
				// ESRC Medals
                if ($medalid < 10) {
					$sql = "SELECT COUNT(*) AS cnt, Pilot, max(ActivityDate) as act FROM activity
                        WHERE EntryType IN ('sower', 'tender') AND ActivityDate BETWEEN '2017-03-01' AND NOW()
                        GROUP BY Pilot ORDER BY cnt desc, act DESC";	
                }
                // SAR Medals
                elseif ($medalid > 10 && $medalid < 20) {
                    $sql = "SELECT COUNT(ra.pilot) AS cnt, ra.pilot AS Pilot, MAX(ra.entrytime) AS act 
                        FROM rescuerequest rr, rescueagents ra 
						WHERE rr.status = 'closed-rescued' AND rr.id=ra.reqid
						GROUP BY ra.pilot ORDER BY cnt DESC, act DESC";
				}
				// Dispatcher medals
				elseif ($medalid > 20) {
                    $sql = "SELECT COUNT(startagent) as cnt, startagent as Pilot, max(requestdate) as act FROM rescuerequest
                        WHERE requestdate BETWEEN '2017-03-01' AND NOW()
						GROUP BY startagent ORDER BY COUNT(startagent) DESC, act DESC";
                }
				//echo $sql;
				$db->query($sql);
				$rows = $db->resultset();
				$db->closeQuery();
				$row_counter = 0;
				foreach ($rows as $value) {
					
					
                    if (intval($value['cnt']) < $min || intval($value['cnt']) > $max) { 
                        continue; 
                    }
					$pilot_name = $value['Pilot'];
					
					if (!$showall and in_array(strtolower($pilot_name),$awarded_pilots)){
						continue;
					}
					echo '<tr>';
					echo '<td class="text-nowrap">';
					echo '<a href="?medalid='. $medalid .'&showall=' . $showall . '&pilot='. $pilot_name .'" style="margin-right: 0.3em;">'. $pilot_name .'</a>';
					
					echo '<input type="text" id="name'.$row_counter.'" style="display: none;" value="'. $pilot_name .'" />';
					echo '<i id="copyclip" class="fa fa-clipboard" style="color:white;"  onClick="SelectAllCopy(\'name'.$row_counter.'\')"></i></td>';
					
                    echo '<td class="white text-nowrap">'. $value['cnt'] . '</td>';
					echo '<td class="white text-nowrap">' . substr($value['act'],0,10) .'</a></td>';
					if(in_array(strtolower($pilot_name),$awarded_pilots)){						
						echo '<td class="white text-nowrap">Awarded</td>';
					}
					else{
						echo '<td class="white text-nowrap"><a href="?rowid=-1&medalid='.$medalid.'&username='.$pilot_name.'">Give award</a></td>';						
					}
					echo '</tr>';
					$row_counter++;
				}
				?>
				</tbody>
			</table>
		</div>
		
	</div>
	<?php
	}
	else {
	//show pilot history is specified		
	?>
	<div style="display: flex; justify-content: space-around; flex-wrap: wrap;">
		<div>
			<?php 
			$db->query("SELECT * FROM medals WHERE pilot = :pilot ORDER BY medalid");
			$db->bind(':pilot', $pilot);			
			$rows = $db->resultset();
			$db->closeQuery();
			if (count($rows) == 0 ){
					echo '<p class="sechead white">' . $pilot . ' has no ESR medals :-(</p>';
			}
			else{
				echo '<p class="sechead white">Awards earned by ' . $pilot . '</p>';
			?>
			
			<table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">Medal</th>
						<th class="white">Awarded</th>
						<th class="white">DEL</th>
                        <th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				<?php

					foreach ($rows as $value) {
						echo '<tr>';
						echo '<td class="white">' . $value['id'] . '</td>';			
						echo '<td class="white">' . $medals[$value['medalid']]['name'] . '</td>';
						
						
						echo '<td class="white">' . $value['dateawarded'] .'</td>';
						
						echo '<td><a href="?del=1&rowid=' . $value['id'] . '&medalid='. $medalid .'&pilot='. $value['pilot'] .'"> Delete </a></td>';
						
						echo '</tr>';
					}
				}
				$linkurl = '?medalid=' . $medalid . '&showall=' . $showall;
				?>

				</tbody>
			</table>
			<p><a href="<?=$linkurl?>"><< BACK</a></p>
		</div>
	</div>
<?php
	}
?>
</div>

<script type="text/javascript">

	function SelectAllCopy(id) {
		var copyText = document.getElementById(id);
		var icon = copyText.nextElementSibling;
		copyText.select();
		copyText.setSelectionRange(0, 99999); // For mobile devices
		navigator.clipboard.writeText(copyText.value);
		

		icon.addEventListener('click', () => {
		  icon.classList.add('spin');
		});
		icon.addEventListener('animationend', ()=>{
			icon.classList.remove('spin');
		});


		
	    //document.getElementById(id).focus();
	    //document.getElementById(id).select();
	    //document.execCommand("Copy");
	}

	$(document).ready(function() {
        $('input.username').typeahead({
            name: 'username',
            remote: 'data_user_roles_lookup.php?query=%QUERY'
        });

		$('#example').DataTable( {
            "order": [[ 3, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });

        $('#example2').DataTable( {
            "order": [[ 2, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

</body>
</html>