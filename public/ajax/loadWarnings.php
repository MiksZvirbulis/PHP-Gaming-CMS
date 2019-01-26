<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(isset($_POST['user_id']) AND user::hasFlag("mod") OR user::isLoggedIn() AND ($_POST['user_id'] == $_SESSION['user_id'])){
	$find_user = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ?", array($_POST['user_id']));
	if($find_user == 0){
		page::alert("Lietotājs nav atrasts!", "danger");
	}else{
		$find_warnings = $db->count("SELECT `id` FROM `warning_points` WHERE `user_id` = ?", array($_POST['user_id']));
		if($find_warnings == 0){
			page::alert("Šis lietotājs vēl nav ticis brīdināts!", "info");
		}else{
			?>
			<table class="table table-bordered">
				<thead>
					<th>Autors</th>
					<th>Datums</th>
					<th>Iemesls</th>
					<th>Punkti</th>
				</thead>
				<tbody>
					<?php $warning_points = $db->fetchAll("SELECT * FROM `warning_points` WHERE `user_id` = ? ORDER BY `date` DESC", array($_POST['user_id'])); ?>
					<?php $total_points = 0; ?>
					<?php foreach($warning_points as $warning): ?>
						<tr>
							<td><?php echo user::formatName($warning['author_id'], true, true); ?></td>
							<td><?php echo date("d/m/Y H:i", $warning['date']); ?></td>
							<td><?php echo $warning['reason']; ?></td>
							<td><?php echo $warning['points']; ?></td>
						</tr>
						<?php $total_points += $warning['points']; ?>
					<?php endforeach; ?>
					<tr>
						<td colspan="4" class="text-right"><strong>Brīdinājumu punktu kopskaits ( <?php echo $total_points; ?> )</strong></td>
					</tr>
				</tbody>
			</table>
			<?php
		}
		?>
		<?php if((int)$_SESSION['user_id'] != (int)$_POST['user_id']): ?>
			<hr />
			<div class="form modal-form">
				<div id="warningErrors"></div>
				<form method="POST" id="addWarning">
					<input type="hidden" name="user_id" value="<?php echo $_POST['user_id']; ?>">
					<label for="points" class="required">Punktu skaits</label>
					<input type="number" name="points" class="control" min="-10" max="10" value="1">

					<label for="reason" class="required">Iemesls</label>
					<textarea type="text" name="reason" class="control" placeholder="Iemesls" rows="3"></textarea>

					<button type="submit" class="blue">Brīdināt</button>
				</form>
			</div>
		<?php endif; ?>
		<?php
	}
}else{
	exit;
}