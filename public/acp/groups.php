<?php
if(isset($path[3]) AND !empty($path[3])){
	$action = $path[3];
}else{
	$action = "list";
}
?>
<?php if($action == "edit"){ ?>
<?php if(isset($path[4]) AND !empty($path[4])){
	$group_id = (int)$path[4];
	$find_group = $db->count("SELECT `group_id` FROM `groups` WHERE `group_id` = ?", array($group_id));
	if($find_group == 0){
		page::alert("Pieprasītā grupa netika atrasta!", "danger");
	}else{
		if(isset($_POST['edit'])){
			$errors = array();

			if(empty($_POST['description'])){
				$errors[] = "Tu aizmirsi ievadīt grupas nosaukumu!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$db->update("UPDATE `groups` SET `prefix` = ?, `description` = ?, `suffix` = ?, `other` = ?, `mod` = ?, `admin` = ? WHERE `group_id` = ?", 
					array(
						$_POST['prefix'],
						$_POST['description'],
						$_POST['suffix'],
						(isset($_POST['other'])) ? 1 : 0,
						(isset($_POST['mod'])) ? 1 : 0,
						(isset($_POST['admin'])) ? 1 : 0,
						$group_id
						));
				page::alert("Grupas informācija veiksmīgi rediģēta!", "success");

			}
		}
		$group = $db->fetch("SELECT * FROM `groups` WHERE `group_id` = ?", array($group_id)); ?>
		Piemērs: <?php echo $group['prefix'] . $group['description'] . $group['suffix']; ?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Priedēklis" name="prefix" value="<?php echo htmlspecialchars($group['prefix']); ?>">
			<input type="text" class="form-control" placeholder="Nosaukums" name="description" value="<?php echo $group['description']; ?>">
			<input type="text" class="form-control" placeholder="Piedēklis" name="suffix" value="<?php echo htmlspecialchars($group['suffix']); ?>">
			<div style="width: 400px; margin: 0 auto">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="other" <?php echo ($group['other'] == 1) ? " checked" : ""; ?>> OTHER
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="mod" <?php echo ($group['mod'] == 1) ? " checked" : ""; ?>> MOD
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="admin" <?php echo ($group['admin'] == 1) ? " checked" : ""; ?>> ADMIN
					</label>
				</div>
			</div>
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="edit">Rediģēt</button>
			</div>
		</form>
		<?php }
	}else{
		page::alert("Netika pieprasīta neviena grupa!", "danger");
	}
}elseif($action == "add"){
	if(isset($_POST['add'])){
		$errors = array();

		if(empty($_POST['description'])){
			$errors[] = "Tu aizmirsi ievadīt grupas nosaukumu!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$db->insert("INSERT INTO `groups` (`prefix`, `description`, `suffix`, `other`, `mod`, `admin`) VALUES(?, ?, ?, ?, ?, ?)", 
				array(
					$_POST['prefix'],
					$_POST['description'],
					$_POST['suffix'],
					(isset($_POST['other'])) ? 1 : 0,
					(isset($_POST['mod'])) ? 1 : 0,
					(isset($_POST['admin'])) ? 1 : 0
					));
			page::alert("Grupas veiksmīgi pievienota!", "success");

		}
	}?>
	<form method="POST">
		<input type="text" class="form-control" placeholder="Priedēklis" name="prefix">
		<input type="text" class="form-control" placeholder="Nosaukums" name="description">
		<input type="text" class="form-control" placeholder="Piedēklis" name="suffix">
		<div style="width: 400px; margin: 0 auto">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="other"> OTHER
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="mod"> MOD
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="admin"> ADMIN
				</label>
			</div>
		</div>
		<div class="form-button">
			<button type="submit" class="btn btn-primary btn-sm" name="add">Pievienot</button>
		</div>
	</form>
	<?php }else{ ?>
	<table class="table table-bordered">
		<thead>
			<th>#</th>
			<th>Priedēklis</th>
			<th>Grupa</th>
			<th>Piedēklis</th>
			<th>OTHER</th>
			<th>MOD</th>
			<th>ADMIN</th>
			<th>Darbības</th>
		</thead>
		<tbody>
			<?php $groups = $db->fetchAll("SELECT * FROM `groups` ORDER BY `group_id` ASC", array()); ?>
			<?php $i = 1; ?>
			<?php foreach($groups as $group): ?>
				<tr>
					<td><?php echo $i; ?></td>
					<td><?php echo htmlspecialchars($group['prefix']); ?></td>
					<td><?php echo $group['description']; ?></td>
					<td><?php echo htmlspecialchars($group['suffix']); ?></td>
					<td><span class="glyphicon glyphicon-<?php echo ($group['other'] == 1) ? 'ok' : 'remove' ?>"></span></td>
					<td><span class="glyphicon glyphicon-<?php echo ($group['mod'] == 1) ? 'ok' : 'remove' ?>"></span></td>
					<td><span class="glyphicon glyphicon-<?php echo ($group['admin'] == 1) ? 'ok' : 'remove' ?>"></span></td>
					<td>
						<a href="<?php echo $c['url']; ?>/acp/groups/edit/<?php echo $group['group_id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
					</td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php }