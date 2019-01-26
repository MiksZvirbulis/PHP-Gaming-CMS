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

		if(empty($_POST['information'])){
			$errors[] = "Tu aizmirsi ievadīt informāciju!";
		}

		if(empty($_POST['start_date'])){
			$errors[] = "Tu aizmirsi ievadīt sākuma datumu!";
		}

		if(empty($_POST['quart_map'])){
			$errors[] = "Tu aizmirsi ievadīt ceturtdaļfināla mapi!";
		}

		if(empty($_POST['semi_map'])){
			$errors[] = "Tu aizmirsi ievadīt pusfināla mapi!";
		}

		if(empty($_POST['final_map'])){
			$errors[] = "Tu aizmirsi ievadīt fināla mapi!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				echo page::alert($error, "danger");
			}
		}else{
			$db->insert("INSERT INTO `tournaments` (`title`, `title_seo`, `total_players`, `information`, `start_date`, `author_id`, `status`, `quart_map`, `semi_map`, `final_map`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
				$_POST['title'],
				text::seostring($_POST['title']),
				$_POST['total_players'],
				$_POST['information'],
				$_POST['start_date'],
				$_SESSION['user_id'],
				"open",
				$_POST['quart_map'],
				$_POST['semi_map'],
				$_POST['final_map']
				));
			echo page::alert("Turnīrs veiksmīgi pievienots!", "success");
		}
	}
	?>
	<div class="form smaller">
		<form method="POST" enctype="multipart/form-data">
			<label for="title" class="required">Nosaukums</label>
			<input type="text" name="title" class="control" placeholder="Nosaukums">

			<label for="total_players" class="required">Spēlētāju kopskaits</label>
			<select name="total_players" class="form-control">
				<!--<option value="4">4</option>-->
				<option value="8">8</option>
			</select>

			<label for="information" class="required">Informācija</label>
			<textarea type="text" name="information" id="editor" class="control" placeholder="Teksts" rows="15"></textarea>

			<label for="start_date" class="required">Sākuma datums</label>
			<input type="text" name="start_date" class="control" placeholder="Sākuma datums dd/mm/yyy">

			<label for="quart_map" class="required">Ceturtdaļfināla mape</label>
			<input type="text" name="quart_map" class="control" placeholder="Sākuma mape">

			<label for="semi_map" class="required">Pusfināla mape</label>
			<input type="text" name="semi_map" class="control" placeholder="Pusfināla mape">

			<label for="final_map" class="required">Fināla mape</label>
			<input type="text" name="final_map" class="control" placeholder="Fināla mape">

			<button type="submit" name="add" class="blue">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "edit"): ?>
	<?php
	if(isset($path[4]) AND is_numeric((int)$path[4])){
		$tournament_id = (int)$path[4];
		$find_tournament = $db->count("SELECT `id` FROM `tournaments` WHERE `id` = ?", array($tournament_id));
		if($find_tournament == 0){
		}else{
			$tournament = $db->fetch("SELECT * FROM `tournaments` WHERE `id` = ?", array($tournament_id));
			?>
			<div class="form smaller">
				<form method="POST" enctype="multipart/form-data">
					<label for="title" class="required">Nosaukums</label>
					<input type="text" name="title" class="control" placeholder="Nosaukums" value="<?php echo $tournament['title']; ?>">

					<label for="total_players" class="required">Spēlētāju kopskaits</label>
					<select name="total_players" class="form-control" disabled>
						<!--<option value="4">4</option>-->
						<option value="8">8</option>
					</select>

					<label for="information" class="required">Informācija</label>
					<textarea type="text" name="information" id="editor" class="control" placeholder="Teksts" rows="15"><?php echo $tournament['information']; ?></textarea>

					<label for="start_date" class="required">Sākuma datums</label>
					<input type="text" name="start_date" class="control" placeholder="Sākuma datums dd/mm/yyy" value="<?php echo $tournament['start_date']; ?>">

					<label for="quart_map" class="required">Ceturtdaļfināla mape</label>
					<input type="text" name="quart_map" class="control" placeholder="Sākuma mape" value="<?php echo $tournament['quart_map']; ?>">

					<label for="semi_map" class="required">Pusfināla mape</label>
					<input type="text" name="semi_map" class="control" placeholder="Pusfināla mape" value="<?php echo $tournament['semi_map']; ?>">

					<label for="final_map" class="required">Fināla mape</label>
					<input type="text" name="final_map" class="control" placeholder="Fināla mape" value="<?php echo $tournament['final_map']; ?>">

					<button type="submit" name="edit" class="blue">Rediģēt</button>
				</form>
				<hr />
				<legend>Spēlētāji</legend>
				<form method="POST">
					<table class="table table-bordered">
						<thead>
							<th>Spēlētājs</th>
							<th>Rezultāts</th>
						</thead>
						<tbody>
							<tr>
								<td colspan="2">Ceturtdaļfinālisti</td>
							</tr>
							<?php $quart_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'quart' AND `tournament_id` = ? ORDER BY `user_id` DESC", array($tournament_id)); ?>
							<?php foreach($quart_players as $player): ?>
								<tr>
									<td><?php echo user::formatName($player['user_id'], true, true); ?></td>
									<td><input type="text" class="control" name="player[<?php echo $player['id']; ?>]" value="<?php echo $player['score']; ?>" placeholder="Rezultāts"></td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<td colspan="2">Pusfinālisti</td>
							</tr>
							<?php $semi_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'semi' AND `tournament_id` = ? ORDER BY `user_id` DESC", array($tournament_id)); ?>
							<?php foreach($semi_players as $player): ?>
								<tr>
									<td><?php echo user::formatName($player['user_id'], true, true); ?></td>
									<td><input type="text" class="control" name="player[<?php echo $player['id']; ?>]" value="<?php echo $player['score']; ?>" placeholder="Rezultāts"></td>
								</tr>
							<?php endforeach; ?>
							<tr>
								<td colspan="2">Finālisti</td>
							</tr>
							<?php $final_players = $db->fetchAll("SELECT * FROM `tournaments_players` WHERE `position` = 'semi' AND `tournament_id` = ? ORDER BY `user_id` DESC", array($tournament_id)); ?>
							<?php foreach($final_players as $player): ?>
								<tr>
									<td><?php echo user::formatName($player['user_id'], true, true); ?></td>
									<td><input type="text" class="control" name="player[<?php echo $player['id']; ?>]" value="<?php echo $player['score']; ?>" placeholder="Rezultāts"></td>
								</tr>
							<?php endforeach; ?>

							<tr>
								<td colspan="2">Otrā/Trešā vieta</td>
							</tr>
						</tbody>
					</table>
					<button type="submit" name="save" class="blue">Saglabāt</button>
				</form>
			</div>
			<?php
		}
	}
	?>
<?php else: ?>
	<table class="table table-bordered">
		<thead>
			<th>Nosaukums</th>
			<th>Spēlētāju kopskaits</th>
			<th>Sākuma datums</th>
			<th>Statuss</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $tournaments = $db->fetchAll("SELECT * FROM `tournaments` ORDER BY `id` DESC"); ?>
			<?php foreach($tournaments as $tournament): ?>
				<tr>
					<td><a href="<?php echo $c['url']; ?>/tournaments/view/<?php echo $tournament['id'] . "-" . $tournament['title_seo']; ?>"><?php echo $tournament['title']; ?></a></td>
					<td>0 / <?php echo $tournament['total_players']; ?></td>
					<td><?php echo $tournament['start_date']; ?></td>
					<td><?php echo ($tournament['status'] == "open") ? "Pieteikšanās atvērta" : "Pieteikšanās slēgta"; ?></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/tournaments/edit/<?php echo $tournament['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>