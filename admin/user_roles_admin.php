<?php 
// Mark all entry pages with this definition. Includes need check check if this is defined
// and stop processing if called direct for security reasons.
define('ESRC', TRUE);

include_once '../includes/auth-inc.php';
require_once '../class/db.class.php';

// create object instance(s)
$db = new Database();

$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : 0;

// HANDLE FORM SUBMIT
// add new role to user
if ($rowid == -1) {
	// lookup userid
	$db->query("SELECT id FROM user WHERE character_name = :username");
	$db->bind(':username', $_REQUEST['username']);
	$row = $db->single();
	$db->closeQuery();
	$userid = $row['id'];

	// add new role
	$db->query("INSERT INTO user_roles (userid, username, roleid, rolename) VALUES (:userid, :username, :roleid, :rolename)");
	$db->bind(':userid', $userid);
	$db->bind(':username', $_REQUEST['username']);
	$db->bind(':roleid', $_REQUEST['roleid']);
	$db->bind(':rolename', $_REQUEST['rolename']);
	$db->execute();
}
// edit existing role
elseif ($rowid > 0) {
	// delete row
	if ($_REQUEST['del'] == 1) {
		$db->query("DELETE FROM user_roles WHERE id = :id");
		$db->bind(':id', $rowid);
		$db->execute();
	}
	// edit row
	else {
		$active = isset($_POST['active']) ? 1 : 0;
		$db->query("UPDATE user_roles SET roleid = :roleid, rolename = :rolename, active = :active WHERE id = :id");
		$db->bind(':roleid', $_REQUEST['roleid']);
		$db->bind(':rolename', $_REQUEST['rolename']);
		$db->bind(':active', $active);
		$db->bind(':id', $rowid);
		$db->execute();
	}
}
?>
<html>

<head>
	<?php
	$pgtitle = 'User Roles Admin';
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

		.tt-hint, .username {
			border: 2px solid #CCCCCC;
			border-radius: 8px 8px 8px 8px;
			font-size: .9em;
			height: 15px;
			line-height: 15px;
			outline: medium none;
			padding: 8px 4px;
			width: 100%;
		}

		.tt-dropdown-menu {
			width: 130%;
			margin-top: 5px;
			padding: 8px 0px;
			background-color: #fff;
			border: 1px solid #ccc;
			border: 1px solid rgba(0, 0, 0, 0.2);
			border-radius: 8px 8px 8px 8px;
			font-size: 100%;
			color: #111;
			background-color: #F1F1F1;
		}

		.tt-suggestion {
			padding: 3px 10px;
			font-size: 100%;
			line-height: 24px;
		}

		.tt-suggestion.tt-is-under-cursor { /* UPDATE: newer versions use .tt-suggestion.tt-cursor */
			color: #fff;
			background-color: #0097cf;

		}

		.tt-suggestion p {
			margin: 0;
		}
	-->
	</style>
</head>

<body>
<div class="container">
	<div class="row" id="header" style="padding-top: 10px;">
		<?php include_once '../includes/top-left.php'; ?>
		<div class="col-sm-8" style="text-align: center; height: 100px; vertical-align: middle;">
			<span style="font-size: 125%; font-weight: bold; color: white;">User Roles Admin</span>
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
		<div class="col-sm-9">
            <form method="post" action="user_roles_admin.php">
				<div class="form-group">
					<input type="text" name="username" id="username" class="username" size="30" autoFocus="autoFocus" 
						autocomplete="off" placeholder="Player Name">&nbsp;&nbsp;&nbsp
                    <input type="text" name="roleid" size="5" placeholder="Role ID">&nbsp;&nbsp;&nbsp
                    <input type="text" name="rolename" size="30" placeholder="Role Name">&nbsp;&nbsp;&nbsp
					<button type="submit" class="btn btn-md">Add New</button>
					<input type="hidden" name="rowid" id="rowid" value="-1">
				</div>
			</form>
            <div class="ws"></div>
			<table id="example" class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">User</th>
						<th class="white">Role</th>
                        <th class="white">Role ID</th>
						<th class="white">Active</th>
					</tr>
				</thead>
				<tbody>
				<?php
				$db->query("SELECT * FROM user_roles");
				$rows = $db->resultset();
				$db->closeQuery();
				foreach ($rows as $value) {
					echo '<tr>';
					echo '<td class="white text-nowrap"><a href="?id=' . $value['id']. '">'. $value['id'] . '</a></td>';
					echo '<td class="text-nowrap">
							<a target="_blank" href="https://evewho.com/pilot/'. $value['username'] .'">'. 
							$value['username'] .'</a></td>';
                    echo '<td class="white text-nowrap">'. $value['rolename'] .'</td>';
                    echo '<td class="white text-nowrap">'. $value['roleid'] . '</td>';
					echo '<td class="white">' . $value['active'] .'</a></td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-3 white">
			<?php echo gmdate('Y-m-d H:i:s', strtotime("now"));?><br /><br />
            <p class="sechead white">Roles</p>
            1 <input type="text" id="role1" value="Admin" class="black" />
                <i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('role1')"></i><br />
            2 <input type="text" id="role2" value="ESR Coordinator" class="black" />
                <i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('role2')"></i><br />
            3 <input type="text" id="role3" value="911 Operator" class="black" />
                <i id="copyclip" class="fa fa-clipboard" onClick="SelectAllCopy('role3')"></i><br />
		</div>
	</div>
	<?php
	}
	//show detail/edit form if ID is specified
	else {	
	?>
	<div class="row">
		<div class="col-sm-12">
			<?php 
			$db->query("SELECT * FROM user_roles WHERE ID = :id");
			$db->bind(':id', $_REQUEST['id']);
			$row = $db->single();
			$db->closeQuery();
			?>
			
			<table class="table display" style="width: auto;">
				<thead>
					<tr>
						<th class="white">ID</th>
						<th class="white">User</th>
						<th class="white">Role</th>
						<th class="white">Role ID</th>
						<th class="white">Active</th>
						<th class="white">&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<form name="editform" id="editform" action="user_roles_admin.php" method="POST">
							<td class="white text-nowrap"><?=$row['id']?></td>
							<td class="white text-nowrap"><?=$row['username']?></td>
							<td class="text-nowrap"><input name="rolename" type="text" value="<?=$row['rolename']?>"></td>
							<td class="text-nowrap"><input name="roleid" type="text" value="<?=$row['roleid']?>"></td>
							<td><input type="checkbox" id="active" name="active"  value="1" 
								<?php echo ($row['active'] == 1) ? 'checked' : '';?>></td>
							<td><button type="submit" class="btn">Update</button></td>
							<input type="hidden" name="rowid" id="rowid" value="<?=$_REQUEST['id']?>" />
						</form>
					</tr>
					<tr><td colspan="6">&nbsp;</td></tr>
					<tr>
						<form name="delform" id="delform" action="user_roles_admin.php" method="POST">
							<td class="white text-nowrap"><a href="?">&lt;&lt; back</a></td>
							<td class="white text-nowrap">&nbsp;</td>
							<td class="text-nowrap">&nbsp;</td>
							<td class="text-nowrap">&nbsp;</td>
							<td>&nbsp;</td>
							<td><button type="submit" class="btn btn-danger">Delete</button></td>
							<input type="hidden" name="rowid" id="rowid" value="<?=$_REQUEST['id']?>" />
							<input type="hidden" name="del" id="del" value="1" />
						</form>
					</tr>
				</tbody>
			</table>
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

	$(document).ready(function() {
        $('input.username').typeahead({
            name: 'username',
            remote: 'data_user_roles_lookup.php?query=%QUERY'
        });

		$('#example').DataTable( {
            "order": [[ 0, "desc" ]],
            "pagingType": "full_numbers",
            "pageLength": 10
        });
    })
</script>

</body>
</html>