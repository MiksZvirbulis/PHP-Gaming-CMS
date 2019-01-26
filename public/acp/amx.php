<?php
if(isset($_POST['approve'])){
	$log_id = $_POST['log_id'];
	foreach($_POST['approve'] as $id => $key){
		$log = $db->fetch("SELECT `admin_id`, `flag` FROM `admin_edit_log` WHERE `id` = ?", array($log_id[$id]));
		$admin = $db->fetch("SELECT `access` FROM `amx_amxadmins` WHERE `id` = ?", array($log['admin_id']));
		$access = str_replace($log['flag'], "", $admin['access']);
		$db->update("UPDATE `amx_amxadmins` SET `access` = ? WHERE `id` = ?", array($access, $log['admin_id']));
		$db->update("UPDATE `admin_edit_log` SET `approved` = 1, `new_flags` = ? WHERE `id` = ?", array($access, $log_id[$id]));
	}
	echo page::alert("Pieprasījums veiksmīgi apstiprināts!", "success");
}

if(isset($_POST['delete'])){
	$log_id = $_POST['log_id'];
	foreach($_POST['delete'] as $id => $key){
		$db->delete("DELETE FROM `admin_edit_log` WHERE `id` = ?", array($log_id[$id]));
	}
	echo page::alert("Pieprasījums veiksmīgi dzēsts!", "success");
}
?>
<ul class="nav nav-tabs margin">
	<li class="active"><a href="#unapproved" data-toggle="tab">Neapstiprinātie</a></li>
	<li><a href="#approved" data-toggle="tab">Apstiprinātie</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane fade in active" id="unapproved">
		<table class="table table-bordered">
			<thead>
				<th>Autors</th>
				<th>Laiks</th>
				<th>Admins</th>
				<th>Flags, kuru noņemt</th>
				<th>Iemesls</th>
				<th>Saite ar pierādījumu</th>
				<th>Darbības</th>
			</thead>
			<tbody>
				<form method="POST">
					<?php $logs = $db->fetchAll("SELECT * FROM `admin_edit_log` WHERE `approved` = 0 ORDER BY `time` ASC"); ?>
					<?php foreach($logs as $log): ?>
						<tr>
							<td><?php echo user::formatName($log['author_id']); ?><input type="hidden" name="log_id[<?php echo $log['id']; ?>]" value="<?php echo $log['id']; ?>"></td>
							<td><?php echo date("d/m/Y H:i", $log['time']); ?></td>
							<td><?php echo $log['admin_nickname']; ?></td>
							<td><?php echo $log['flag']; ?> (<?php echo $log['new_flags']; ?>)</td>
							<td><?php echo $log['reason']; ?></td>
							<td><?php echo $log['link']; ?></td>
							<td><button type="submit" class="btn btn-primary" name="approve[<?php echo $log['id']; ?>]">Apstiprināt</button> <button type="submit" class="btn btn-danger" name="delete[<?php echo $log['id']; ?>]">Dzēst</button></td>
						</tr>
					<?php endforeach; ?>
				</form>
			</tbody>
		</table>
	</div>
	<div class="tab-pane fade in" id="approved">
		<table class="table table-bordered">
			<thead>
				<th>Autors</th>
				<th>Laiks</th>
				<th>Admins</th>
				<th>Flags, kuru noņemt</th>
				<th>Iemesls</th>
				<th>Saite ar pierādījumu</th>
			</thead>
			<tbody>
				<form method="POST">
					<?php $logs = $db->fetchAll("SELECT * FROM `admin_edit_log` WHERE `approved` = 1 ORDER BY `time` ASC"); ?>
					<?php foreach($logs as $log): ?>
						<tr>
							<td><?php echo user::formatName($log['author_id']); ?><input type="hidden" name="log_id[<?php echo $log['id']; ?>]" value="<?php echo $log['id']; ?>"></td>
							<td><?php echo date("d/m/Y H:i", $log['time']); ?></td>
							<td><?php echo $log['admin_nickname']; ?></td>
							<td><?php echo $log['flag']; ?> (<?php echo $log['new_flags']; ?>)</td>
							<td><?php echo $log['reason']; ?></td>
							<td><?php echo $log['link']; ?></td>
						</tr>
					<?php endforeach; ?>
				</form>
			</tbody>
		</table>
	</div>
</div>