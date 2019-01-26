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

		if(empty($_POST['title'])){
			$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
		}

		if(empty($_POST['content'])){
			$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				echo page::alert($error, "danger");
			}
		}else{
			$db->insert("INSERT INTO `rules` (`title`, `title_seo`, `content`, `last_updated`, `last_updated_by`) VALUES (?, ?, ?, ?, ?)", array(
				$_POST['title'],
				text::seostring($_POST['title']),
				$_POST['content'],
				time(),
				$_SESSION['user_id']
				));
			echo page::alert("Noteikumi veiksmīgi pievienoti!", "success");
		}
	}
	?>
	<div class="form smaller">
		<form method="POST" enctype="multipart/form-data">
			<label for="title" class="required">Nosaukums</label>
			<input type="text" name="title" class="control" placeholder="Nosaukums">

			<label for="text" class="required">Noteikumu saturs</label>
			<textarea type="text" id="editor" name="content" placeholder="Noteikumu saturs" rows="15"></textarea>

			<button type="submit" name="add" class="blue">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "edit"): ?>
	<?php
	if(isset($path[4]) AND !empty($path[4])){
		$rule_id = (int)$path[4];
		$find_rules = $db->count("SELECT `id` FROM `rules` WHERE `id` = ?", array($rule_id));
		if($find_rules == 0){
			echo page::alert("Neviens noteikums netika atrasts ar pieprasīto ID!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$errors = array();

				if(empty($_POST['title'])){
					$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
				}

				if(empty($_POST['content'])){
					$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						echo page::alert($error, "danger");
					}
				}else{
					$db->update("UPDATE `rules` SET `title` = ?, `title_seo` = ?, `content` = ?, `last_updated` = ?, `last_updated_by` = ? WHERE `id` = ?", array(
						$_POST['title'],
						text::seostring($_POST['title']),
						$_POST['content'],
						time(),
						$_SESSION['user_id'],
						$rule_id
						));
					echo page::alert("Noteikumi veiksmīgi pievienoti!", "success");
				}
			}
			$rule = $db->fetch("SELECT * FROM `rules` WHERE `id` = ?", array($rule_id));
			?>
			<div class="form smaller">
				<form method="POST" enctype="multipart/form-data">
					<label for="title" class="required">Nosaukums</label>
					<input type="text" name="title" class="control" placeholder="Nosaukums" value="<?php echo $rule['title']; ?>">

					<label for="text" class="required">Noteikumu saturs</label>
					<textarea type="text" id="editor" name="content" placeholder="Noteikumu saturs" rows="15"><?php echo $rule['content']; ?></textarea>

					<button type="submit" name="edit" class="blue">Rediģēt</button>
				</form>
			</div>
			<?php
		}
	}else{
		echo page::alert("Netika norādīts ID!", "danger");
	}
	?>
<?php else: ?>
	<table class="table table-bordered">
		<thead>
			<th>Nosaukums</th>
			<th>Saturs</th>
			<th>Pēdējo reizi atjaunoja</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $rules = $db->fetchAll("SELECT * FROM `rules` ORDER BY `title` ASC"); ?>
			<?php foreach($rules as $rule): ?>
				<tr>
					<td><?php echo $rule['title']; ?></td>
					<td><?php echo text::limit($rule['content'], 200); ?></td>
					<td>Pēdējo reizi atjaunoja <?php echo user::formatName($rule['last_updated_by'], true, true, true); ?> <?php echo page::formatTime($rule['last_updated']); ?></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/rules/edit/<?php echo $rule['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>