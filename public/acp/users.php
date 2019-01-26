<?php
if(isset($path[3]) AND !empty($path[3])){
	$action = $path[3];
}else{
	$action = "list";
}
?>
<?php if($action == "edit"){ ?>
<?php if(isset($path[4]) AND !empty($path[4])){
	$user_id = (int)$path[4];
	$find_user = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ?", array($user_id));
	if($find_user == 0){
		page::alert("Pieprasītais lietotājs netika atrasts!", "danger");
	}else{
		if(isset($_POST['edit'])){
			$errors = array();

			if(empty($_POST['username'])){
				$errors[] = "Tu aizmirsi ievadīt lietotāja lietotājvārdu!";
			}

			if(empty($_POST['email'])){
				$errors[] = "Tu aizmirsi ievadīt lietotāja e-pasta adresi!";
			}

			if(empty($_POST['display_name'])){
				$errors[] = "Tu aizmirsi ievadīt lietotāja uzrādošo vārdu!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$db->update("UPDATE `users` SET `username` = ?, `email` = ?, `prefix` = ?, `display_name` = ?, `suffix` = ?, `title` = ?, `icon` = ?, `group_id` = ?, `blocked` = ? WHERE `user_id` = ?", 
					array(
						$_POST['username'],
						$_POST['email'],
						$_POST['prefix'],
						$_POST['display_name'],
						$_POST['suffix'],
						$_POST['title'],
						$_POST['icon'],
						$_POST['group_id'],
						(isset($_POST['blocked'])) ? 1 : 0,
						$user_id
						));
				page::alert("Lietotāja informācija veiksmīgi rediģēta!", "success");
			}
		}
		$user = user::data($user_id); ?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Lietotājvārds" name="username" value="<?php echo $user['username']; ?>">
			<input type="text" class="form-control" placeholder="E-pasta adrese" name="email" value="<?php echo $user['email']; ?>">
			<input type="text" class="form-control" placeholder="Priedēklis" name="prefix" value="<?php echo htmlspecialchars($user['suffix']); ?>">
			<input type="text" class="form-control" placeholder="Uzrādošais vārds" name="display_name" value="<?php echo $user['display_name']; ?>">
			<input type="text" class="form-control" placeholder="Piedēklis" name="suffix" value="<?php echo htmlspecialchars($user['suffix']); ?>">
			<input type="text" class="form-control" placeholder="Tituls" name="title" value="<?php echo htmlspecialchars($user['title']); ?>">
			<input type="text" class="form-control" placeholder="Ikona" name="icon" value="<?php echo $user['icon']; ?>">
		<select class="form-control" name="group_id">
				<option disabled>Izvēlies grupu</option>
				<?php $groups = $db->fetchAll("SELECT `group_id`, `description` FROM `groups` ORDER BY `group_id` ASC", array()); ?>
				<?php foreach($groups as $group): ?>
					<option value="<?php echo $group['group_id']; ?>"<?php echo ($group['group_id'] == $user['group_id']) ? " selected" : ""; ?>><?php echo $group['description']; ?></option>
				<?php endforeach; ?>
			</select>
			<div style="width: 400px; margin: 0 auto">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="blocked" <?php echo ($user['blocked'] == 1) ? " checked" : ""; ?>> Bloķēts
					</label>
				</div>
			</div>
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="edit">Rediģēt</button>
			</div>
		</form>
		<?php }
	}else{
		page::alert("Netika pieprasīts neviens lietotājs!", "danger");
	}
}else{ ?>
<?php
if(isset($_POST['search'])){
	if(!empty($_POST['query']) AND empty($_POST['group_id'])){
		$query = $_POST['query'];
		$query = "%$query%";
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `ip_address_list` LIKE ? OR `name` LIKE ? OR `display_name` LIKE ? OR `email` LIKE ? OR `skype` LIKE ? OR `about` LIKE ?", array($query, $query, $query, $query, $query, $query));
	}elseif(empty($_POST['group_id']) AND !empty($_POST['query'])){
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `group_id` = ?", array($_POST['group_id']));
	}else{
		$query = $_POST['query'];
		$query = "%$query%";
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `group_id` = ? AND (`ip_address_list` LIKE ? OR `name` LIKE ? OR `display_name` LIKE ? OR `email` LIKE ? OR `skype` LIKE ? OR `about` LIKE ?)", array($_POST['group_id'], $query, $query, $query, $query, $query, $query));
	}
}else{
	$users_count = $db->count("SELECT * FROM `users`");
	list($pager_template, $limit) = page::pagination(10, $users_count, $c['url'] . "/acp/users/list/page/", 5);
	$users = $db->fetchAll("SELECT * FROM `users` ORDER BY `user_id` ASC $limit");
}
?>
<div class="form" style="width: 500px; margin: 0 auto;">
	<form class="form-inline" method="POST">
		<div class="form-group">
			<select class="control" name="group_id" style="width: 200px;">
				<option selected disabled>Izvēlies grupu</option>
				<?php $groups = $db->fetchAll("SELECT `group_id`, `description` FROM `groups` ORDER BY `description` ASC"); ?>
				<?php foreach($groups as $group): ?>
					<option value="<?php echo $group['group_id']; ?>" <?php echo (isset($_POST['group_id']) AND $_POST['group_id'] == $group['group_id']) ? "selected" : ""; ?>><?php echo $group['description']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="form-group">
			<input type="text" class="control" name="query" placeholder="Vārds, uzrādošais vārds, e-pasts..." style="width: 200px;" value="<?php echo (isset($_POST['query'])) ? $_POST['query'] : ""; ?>">
		</div>
		<button type="submit" class="blue" name="search" style="float: none;">Meklēt</button>
	</form>
</div>
<table class="table table-bordered">
	<thead>
		<th>ID</th>
		<th>Lietotājvārds</th>
		<th>Uzrādošais vārds</th>
		<th>IP Adreses</th>
		<th>Darbības</th>
	</thead>
	<tbody>
		<?php foreach($users as $user): ?>
			<tr>
				<td><?php echo $user['user_id']; ?></td>
				<td><?php echo $user['username']; ?></td>
				<td><?php echo user::formatName($user['user_id'], true, true); ?></td>
				<td><?php echo $user['ip_address_list']; ?></td>
				<td>
					<a href="<?php echo $c['url']; ?>/acp/users/edit/<?php echo $user['user_id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php echo (isset($_POST['search'])) ? "" : $pager_template; ?>
<?php }