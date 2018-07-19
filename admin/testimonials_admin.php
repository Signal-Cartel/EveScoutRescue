<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';

// create object instance(s)
$db = new Database();

$stat = isset($_REQUEST['stat']) ? $_REQUEST['stat'] : 0;

// handle form submit
if (isset($_POST['testimonial'])) {
	// delete testimonial
	if (isset($_POST['delete'])) {
		$db->beginTransaction();
		$db->query("DELETE FROM testimonials WHERE ID = :id");
		$db->bind(':id', $_POST['ID']);
		$db->execute();
		$db->endTransaction();
	}
	// edit testimonial
	else {
		$app = isset($_POST['approved']) ? 1 : 0;
		$db->beginTransaction();
		$db->query("UPDATE testimonials SET Pilot = :pilot, Note = :note, Approved = :app WHERE ID = :id");
		$db->bind(':pilot', $_POST['pilot']);
		$db->bind(':note', $_POST['testimonial']);
		$db->bind(':app', $app);
		$db->bind(':id', $_POST['ID']);
		$db->execute();
		$db->endTransaction();
	}
}
?>
<html>

<head>
	<?php
	$pgtitle = 'Testimonials Admin';
	include_once '../includes/head.php'; 
	?>
	<style>
	<!--
		table {
			table-layout: fixed;
			word-wrap: break-word;
		}
		a,
		a:visited,
		a:hover {
			color: aqua;
		}
	-->
	</style>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">Testimonials Admin</span>
			<span class="pull-right"><a class="btn btn-danger btn-md" href="index.php" role="button">
				Admin Index</a></span>
		</div>
		<?php include_once '../includes/top-right.php'; ?>
	</div>
	<div class="ws"></div>
	<?php
	//show list if no ID is specified
	if (empty($_REQUEST['id'])) {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-10">
			<a href="?stat=1">Approved</a>&nbsp;&nbsp;&nbsp;<a href="?stat=0">Unapproved</a>
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
				$db->query("SELECT * FROM testimonials WHERE Approved = :stat
							ORDER By RescueDate DESC");
				$db->bind(':stat', $stat);
				$rows = $db->resultset();
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white text-nowrap"><a href="?id=' . $value['ID']. '">'. 
						$value['ID'] . '</a></td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="https://evewho.com/pilot/'. $value['Pilot'] .'">'. 
							$value['Pilot'] .'</a></td>';
					echo '<td class="white">'. $value['RescueDate'] .'</td>';
					echo '<td class="white">' . $value['Type'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
		</div>
	</div>
	<?php
	}
	//show detail/edit form if ID is specified
	else {	
	?>
	<div class="row" id="systable">
		<div class="col-sm-10">
			<?php 
			$db->query("SELECT * FROM testimonials WHERE ID = :id");
			$db->bind(':id', $_REQUEST['id']);
			$row = $db->single();
			$db->closeQuery();
			?>
		
			<form name="testform" id="testform" action="testimonials_admin.php" method="POST">
		      <div class="white">
				Pilot: <strong><input class="black" type="text" name="pilot" value="<?=$row['Pilot']?>"></strong> 
					(<?php echo ($row['Anon'] == 1) ? 'A' : 'Not a'; ?>nonymous)<br />
				Rescue Method: <strong><?php echo $row['Type'];?></strong><br /><br />
				
				Approved to post: <input type="checkbox" id="approved" name="approved" 
					value="1" <?php echo ($row['Approved'] == 1) ? 'checked' : '';?>>
				<br /><br />
				
				DELETE: <input type="checkbox" id="delete" name="delete" value="1">
				<br /><br />

			  	<div class="field form-group">
					<textarea class="form-control" id="testimonial" name="testimonial" rows="5"><?=$row['Note']?></textarea>
				</div>
		      </div>
		      <div class="modal-footer">
		        <div class="form-actions">
					<input type="hidden" name="ID" id="ID" value="<?=$_REQUEST['id']?>" />
				    <button type="submit" class="btn">Submit</button>
				</div>
		      </div>   
		    </form>
		    
		</div>
		<div class="col-sm-2 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
		</div>
	</div>
<?php
	}
?>
</div>

<script type="text/javascript">
	function SelectAllCopy(id) {
	    document.getElementById(id).focus();
	    document.getElementById(id).select();
	    document.execCommand("Copy");
	}
</script>

</body>
</html>