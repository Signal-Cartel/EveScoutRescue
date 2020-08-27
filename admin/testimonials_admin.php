<?php 
// REQUIRED on all secured pages
define('ESRC', TRUE);
require '../page_templates/secure_initialization.php';

// PAGE VARS
$database = new Database();
$testimonials = new Testimonials($database);
$stat = $_REQUEST['stat'] ?? 0;
$strApproved = ($stat == 1) ? 'APPROVED' : '<a class="adminlink" href="?stat=1">Approved</a>';
$strUnapproved = ($stat == 0) ? 'UNAPPROVED' : '<a class="adminlink" href="?stat=0">Unapproved</a>';
$strErrMessage = '<p class="white">That is not a valid testimonial.</p>';
$pgtitle = 'Testimonials Admin';


// handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['delete'])) {	// delete testimonial
		$testimonials->removeTestimonial($_POST['ID']);
	}
	else {	// edit testimonial
		$approvedFlag = $_POST['approved'] ?? 0;
		$pilot = Output::prepTextarea($_POST['pilot']);
		$testimonial = Output::prepTextarea($_POST['testimonial']);

		$testimonials->updateTestimonial($_POST['ID'], $pilot, $testimonial, $approvedFlag);
		
		// Broadcast any approved testimonial to Discord
		if ($approvedFlag == 1) {		
			$webHook = 'https://discordapp.com/api/webhooks/' . Config::DISCORDEXPLO;
			$user = 'EvE-Scout Rescue';
			$alert = 0;
			$name_holder = ($_POST['anon'] == 1) ? "Anonymous" : $_POST['pilot'];
			$message = "_New testimonial from " . $name_holder . "_\n" . $_POST['testimonial'];
			$skip_the_gif = 1;

			$result = Discord::sendMessage($webHook, $user, $alert, $message, $skip_the_gif);
		}
	}
}


// HTML PAGE template - Begin
require '../page_templates/home_html-begin.php';	?>

<div class="row" id="systable">
	<div class="col-sm-12">

	<?php
	if (empty($_REQUEST['id'])) {	//show list if no ID is specified	?>
	
		<p class="white"><?=$strApproved?>&nbsp;&nbsp;&nbsp;<?=$strUnapproved?></p>
		<table id="example" class="table display" style="width: auto;">
			<thead>
				<tr>
					<th class="white">ID</th>
					<th class="white">Pilot</th>
					<th class="white">Entered</th>
					<th class="white">Type</th>
				</tr>
			</thead>
			<tbody>
			
			<?php
			$rows = $testimonials->getTestimonials('Both', 'DESC', $stat, 1000);
			foreach ($rows as $value) {	?>
				
				<tr>
					<td class="white text-nowrap">
						<a class="adminlink" href="?id=<?=$value['ID']?>"><?=$value['ID']?></a>
					</td>
					<td class="text-nowrap">
						<a class="adminlink" target="_blank" 
							href="https://evewho.com/pilot/<?=$value['Pilot']?>">
							<?=$value['Pilot']?></a>
					</td>
					<td class="white"><?=$value['RescueDate']?></td>
					<td class="white"><?=$value['Type']?></a></td>
				</tr>

				<?php
			}	?>

			</tbody>
		</table>
	

		<?php
	}
	else {	//show detail/edit form if ID is specified
		if (is_numeric($_REQUEST['id'])) {
			$row = $testimonials->getTestimonial($_REQUEST['id']);
			if (!empty($row)) {	?>

			<form name="testform" id="testform" action="testimonials_admin.php" method="POST">
		      	<div class="white">
					Pilot: <strong><input class="black" type="text" name="pilot" 
						value="<?=$row['Pilot']?>"></strong> 
						<input type="hidden" name="anon" value="<?=$row['Anon']?>">
						(<?=($row['Anon'] == 1) ? 'Anonymous' : 'Not anonymous'; ?>)<br />
					Rescue Method: <strong><?php echo $row['Type'];?></strong><br /><br />
					
					Approved to post (and share on discord): 
						<input type="checkbox" id="approved" name="approved" 
							value="1" <?=($row['Approved'] == 1) ? 'checked' : ''?>><br /><br />
					
					DELETE: <input type="checkbox" id="delete" name="delete" value="1"><br /><br />

					<div class="field form-group">
						<textarea class="form-control" id="testimonial" name="testimonial" rows="5"><?=$row['Note']?></textarea>
					</div>
		      	</div>
				<div class="modal-footer">
					<div class="form-actions">
						<input type="hidden" name="ID" id="ID" value="<?=$_REQUEST['id']?>" />
						<button type="submit" class="btn btn-primary">Submit</button>
						<a class="btn btn-danger btn-md" href="testimonials_admin.php" 
							role="button">Cancel</a>
					</div>
				</div>   
		    </form>

				<?php
			}
			else {	// querystring parameter returned no result
				echo $strErrMessage;
			}
		}
		else {	// querystring parameter is illegitimate
			echo $strErrMessage;
		}
	}	?>

	</div>
</div>


<?php
// HTML PAGE template - End
require '../page_templates/home_html-end.php';
?>
