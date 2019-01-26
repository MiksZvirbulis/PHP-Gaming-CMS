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

		if(empty($_POST['link'])){
			$errors[] = "Tu aizmirsi ievadīt saiti!";
		}else{
			$find_link = $db->count("SELECT `id` FROM `pages` WHERE `link` = ?", array($_POST['link']));
			if($find_link == 1){
				$errors[] = "Lapa ar šādu saiti jau pastāv!";
			}
		}

		if(empty($_POST['content'])){
			$errors[] = "Tu aizmirsi ievadīt saturu!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$db->insert("INSERT INTO `pages` (`link`, `title`, `content`, `side`) VALUES (?, ?, ?, ?)", array(
				$_POST['link'],
				$_POST['title'],
				$_POST['content'],
				(isset($_POST['side'])) ? 1 : 0
				));
			page::alert("Lapa veiksmīgi pievienota!", "success");
		}
	}
	?>
	<div class="form smaller">
		<form method="POST" enctype="multipart/form-data">
			<label for="title" class="required">Nosaukums</label>
			<input type="text" name="title" class="control" placeholder="Nosaukums">

			<label for="title" class="required">Saite</label>
			<input type="text" name="link" class="control" placeholder="Saite">

			<label for="text" class="required">Teksts</label>
			<textarea type="text" name="content" class="control" placeholder="Teksts" rows="15"></textarea>

			<div style="width: 400px; margin: 0 auto">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="side"> Iekļaut malu?
					</label>
				</div>
			</div>

			<button type="submit" name="add" class="blue">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "edit"): ?>
	<?php if(isset($path[4]) AND !empty($path[4])){
		$page_id = (int)$path[4];
		$find_page = $db->count("SELECT `id` FROM `pages` WHERE `id` = ?", array($page_id));
		if($find_page == 0){
			page::alert("Pieprasītā lapa netika atrasta!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$errors = array();

				if(empty($_POST['title'])){
					$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
				}

				if(empty($_POST['link'])){
					$errors[] = "Tu aizmirsi ievadīt saiti!";
				}else{
					$find_link = $db->count("SELECT `id` FROM `pages` WHERE `link` = ? AND `id` != ?", array($_POST['link'], $page_id));
					if($find_link == 1){
						$errors[] = "Lapa ar šādu saiti jau pastāv!";
					}
				}

				if(empty($_POST['content'])){
					$errors[] = "Tu aizmirsi ievadīt saturu!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$db->update("UPDATE `pages` SET `link` = ?, `title` = ?, `content` = ?, `side` = ? WHERE `id` = ?", array(
						$_POST['link'],
						$_POST['title'],
						$_POST['content'],
						(isset($_POST['side'])) ? 1 : 0,
						$page_id
						));
					page::alert("Lapa veiksmīgi rediģeta!", "success");
				}
			}
			$content = $db->fetch("SELECT * FROM `pages` WHERE `id` = ?", array($page_id));
			?>
			<div class="form smaller">
				<form method="POST" enctype="multipart/form-data">
					<label for="title" class="required">Nosaukums</label>
					<input type="text" name="title" class="control" placeholder="Nosaukums" value="<?php echo $content['title']; ?>">

					<label for="title" class="required">Saite</label>
					<input type="text" name="link" class="control" placeholder="Saite" value="<?php echo $content['link']; ?>">

					<label for="text" class="required">Teksts</label>
					<textarea type="text" name="content" class="control" placeholder="Teksts" rows="15"><?php echo $content['content']; ?></textarea>

					<div style="width: 400px; margin: 0 auto">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="side" <?php echo ($content['side'] == 1) ? "checked" : ""; ?>> Iekļaut malu?
							</label>
						</div>
					</div>
					<button type="submit" name="edit" class="blue">Rediģēt</button>
				</form>
			</div>
			<?php
		}
	}else{
		page::alert("Netika pieprasīts neviens jaunums!", "danger");
	}
	?>
<?php else: ?>
	<table class="table table-bordered">
		<thead>
			<th>Nosaukums</th>
			<th>Saite</th>
			<th>Mala</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $pages = $db->fetchAll("SELECT * FROM `pages` ORDER BY `title` ASC", array()); ?>
			<?php foreach($pages as $content): ?>
				<tr>
					<td><?php echo $content['title']; ?></td>
					<td>/<?php echo $content['link']; ?></td>
					<td><?php echo ($content['side'] == 1) ? "Iekļauta" : "Nav iekļauta"; ?></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/pages/edit/<?php echo $content['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>