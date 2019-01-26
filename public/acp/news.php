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

		if($_FILES['image']['name'] == ""){
			$errors[] = "Tu aizmirsi izvēlēties bildi!";
		}else{
			if(getimagesize($_FILES['image']['tmp_name']) != TRUE){
				$errors[] = "Nederīgs bildes formāts!";
			}
		}

		if(empty($_POST['text'])){
			$errors[] = "Tu aizmirsi ievadīt tekstu!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$image_info = pathinfo($_FILES['image']['name']);
			$image = time() . "." . $image_info['extension'];
			move_uploaded_file($_FILES['image']['tmp_name'], $c['dir'] . "uploads/news/" . $image);
			$db->insert(
				"INSERT INTO `news` (`title`, `seostring`, `added`, `text`, `author_id`, `image`) VALUES (?, ?, ?, ?, ?, ?)", array(
					$_POST['title'],
					text::seostring($_POST['title']),
					time(),
					$_POST['text'],
					$_SESSION['user_id'],
					$image
					)
				);
			page::alert("Jaunumi veiksmīgi pievienoti!", "success");
		}
	}
	?>
	<div class="form smaller">
		<form method="POST" enctype="multipart/form-data">
			<label for="title" class="required">Nosaukums</label>
			<input type="text" name="title" class="control" placeholder="Nosaukums">

			<label for="image" class="required">Izvēlies bildi</label>
			<input type="file" name="image" class="control" accept="image/*">

			<label for="text" class="required">Teksts</label>
			<textarea type="text" name="text" id="editor" class="control" placeholder="Teksts" rows="15"></textarea>

			<button type="submit" name="add" class="blue">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "edit"): ?>
	<?php if(isset($path[4]) AND !empty($path[4])){
		$news_id = (int)$path[4];
		$find_news = $db->count("SELECT `news_id` FROM `news` WHERE `news_id` = ?", array($news_id));
		if($find_news == 0){
			page::alert("Pieprasītie jaunumi netika atrasti!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$errors = array();

				if(empty($_POST['title'])){
					$errors[] = "Tu aizmirsi ievadīt nosaukumu!";
				}

				if($_FILES['image']['name'] == ""){
					$new_image = false;
				}else{
					$new_image = true;
					if(getimagesize($_FILES['image']['tmp_name']) != TRUE){
						$errors[] = "Nederīgs bildes formāts!";
					}
				}

				if(empty($_POST['text'])){
					$errors[] = "Tu aizmirsi ievadīt tekstu!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					if($new_image === true){
						$image_info = pathinfo($_FILES['image']['name']);
						$image = time() . "." . $image_info['extension'];
						move_uploaded_file($_FILES['image']['tmp_name'], $c['dir'] . "uploads/news/" . $image);
						$db->update("UPDATE `news` SET `title` = ?, `seostring` = ?, `text` = ?, `image` = ? WHERE `news_id` = ?", array($_POST['title'], text::seostring($_POST['title']), $_POST['text'], $image, $news_id));
					}else{
						$db->update("UPDATE `news` SET `title` = ?, `seostring` = ?, `text` = ?  WHERE `news_id` = ?", array($_POST['title'], text::seostring($_POST['title']), $_POST['text'], $news_id));
					}
					page::alert("Jaunumi veiksmīgi rediģēti!", "success");
				}
			}
			$news = $db->fetch("SELECT * FROM `news` WHERE `news_id` = ?", array($news_id));
			?>
			<div class="form smaller">
				<form method="POST" enctype="multipart/form-data">
					<label for="title" class="required">Nosaukums</label>
					<input type="text" name="title" class="control" placeholder="Nosaukums" value="<?php echo $news['title']; ?>">

					<label for="image">Izvēlies bildi</label>
					<input type="file" name="image" class="control" accept="image/*">

					<label for="text" class="required">Teksts</label>
					<textarea type="text" name="text" id="editor" class="control" placeholder="Teksts" rows="15"><?php echo $news['text']; ?></textarea>

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
			<th>ID</th>
			<th>Nosaukums</th>
			<th>Autors</th>
			<th>Pievienots</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $news = $db->fetchAll("SELECT * FROM `news` ORDER BY `added` DESC", array()); ?>
			<?php foreach($news as $new): ?>
				<tr>
					<td><?php echo $new['news_id']; ?></td>
					<td><?php echo $new['title']; ?></td>
					<td><?php echo user::formatName($new['author_id'], true, true); ?></td>
					<td><?php echo date("d/m/Y", $new['added']); ?> plkst. <?php echo date("H:i", $new['added']); ?></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/news/edit/<?php echo $new['news_id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>