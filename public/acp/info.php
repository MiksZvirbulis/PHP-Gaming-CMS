<?php
if(isset($path[3]) AND !empty($path[3])){
	$action = $path[3];
}else{
	$action = "list";
}
?>

<?php if($action == "add"): ?>
	<?php
	if(isset($_POST['add'])){
		$errors = array();

		if(empty($_POST['text'])){
			$errors[] = "Tu aizmirsi ievadīt tekstu!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$db->insert(
				"INSERT INTO `information` (`text`, `updated`, `author_id`) VALUES (?, ?, ?)", array(
					$_POST['text'],
					time(),
					$_SESSION['user_id'],
					)
				);
			page::alert("Informācija veiksmīgi pievienota!", "success");
		}
	}
	?>
	<form method="POST">
		<label for="text" class="required">Teksts</label>
		<textarea type="text" name="text" id="editor" class="control" placeholder="Teksts" rows="15"></textarea>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add">Pievienot</button>
		</div>
	</form>
<?php elseif($action == "edit"): ?>
	<?php if(isset($path[4]) AND !empty($path[4])){
		$info_id = (int)$path[4];
		$find_info = $db->count("SELECT `id` FROM `information` WHERE `id` = ?", array($info_id));
		if($find_info == 0){
			page::alert("Pieprasītā informācija netika atrasta!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$errors = array();

				if(empty($_POST['text'])){
					$errors[] = "Tu aizmirsi ievadīt tekstu!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$db->update("UPDATE `information` SET `text` = ?, `updated` = ?, `author_id` = ? WHERE `id` = ?", array($_POST['text'], time(), $_SESSION['user_id'], $info_id,));
					page::alert("Informācija veiksmīgi rediģēta!", "success");
				}
			}
			$info = $db->fetch("SELECT `text` FROM `information` WHERE `id` = ?", array($info_id));
			?>
			<form method="POST">
				<label for="text" class="required">Teksts</label>
				<textarea type="text" name="text" id="editor" class="control" placeholder="Teksts" rows="15"><?php echo $info['text']; ?></textarea>
				<div class="form-button">
					<button type="submit" class="btn btn-primary btn-sm" name="edit">Rediģēt</button>
				</div>
			</form>
			<?php
		}
	}else{
		page::alert("Netika pieprasīta neviena informācija!", "danger");
	}
	?>
<?php elseif($action == "delete"): ?>
	<?php if(isset($path[4]) AND !empty($path[4])){
		$info_id = (int)$path[4];
		$find_info = $db->count("SELECT `id` FROM `information` WHERE `id` = ?", array($info_id));
		if($find_info == 0){
			page::alert("Pieprasītā informācija netika atrasta!", "danger");
		}else{
			$db->delete("DELETE FROM `information` WHERE `id` = ?", array($info_id));
			page::alert("Informācijas ieraksts veiksmīgi dzēsts!", "success");
			page::redirectTo($_SERVER['HTTP_REFERER'], array("external" => true, "time" => 3));
		}
	}else{
		page::alert("Netika pieprasīta neviena informācija!", "danger");
	}
	?>
<?php else: ?>
	<table class="table table-bordered">
		<thead>
			<th>Teksts</th>
			<th width="100px">Autors</th>
			<th>Atjaunots</th>
			<th width="140px">Darbības</th>
		</thead>
		<tbody>
			<?php $information = $db->fetchAll("SELECT * FROM `information` ORDER BY `updated` DESC", array()); ?>
			<?php foreach($information as $info): ?>
				<tr>
					<td><?php echo $info['text']; ?></td>
					<td><?php echo user::formatName($info['author_id'], true, true); ?></td>
					<td><?php echo date("d/m/Y", $info['updated']); ?> plkst. <?php echo date("H:i", $info['updated']); ?></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/info/edit/<?php echo $info['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
						<a href="<?php echo $c['url']; ?>/acp/info/delete/<?php echo $info['id']; ?>"><button class="btn btn-danger btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-remove" style="opacity: 0.4;"></span> Dzēst</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>