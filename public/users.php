<?php
$page = new page("Lietotāju saraksts", array("Lietotāju saraksts"), false);
if(isset($_POST['search'])){
	if(!empty($_POST['query']) AND empty($_POST['group_id'])){
		$query = $_POST['query'];
		$query = "%$query%";
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `name` LIKE ? OR `display_name` LIKE ? OR `email` LIKE ? OR `skype` LIKE ? OR `about` LIKE ?", array($query, $query, $query, $query, $query));
	}elseif(empty($_POST['group_id']) AND !empty($_POST['query'])){
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `group_id` = ?", array($_POST['group_id']));
	}else{
		$query = $_POST['query'];
		$query = "%$query%";
		$users = $db->fetchAll("SELECT * FROM `users` WHERE `group_id` = ? AND (`name` LIKE ? OR `display_name` LIKE ? OR `email` LIKE ? OR `skype` LIKE ? OR `about` LIKE ?)", array($_POST['group_id'], $query, $query, $query, $query, $query));
	}
}else{
	$users_count = $db->count("SELECT * FROM `users`");
	list($pager_template, $limit) = page::pagination(10, $users_count, $c['url'] . "/users/page/", 3);
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
<table class="table table-bordered table-centered table-vmiddle">
	<thead>
		<th></th>
		<th>Uzrādošais vārds</th>
		<th>Vārds</th>
		<th>Grupa</th>
		<th>Pēdējā aktivitāte</th>
		<th>Apskatīt profilu</th>
	</thead>
	<tbody class="data">
		<?php foreach($users as $user): ?>
			<tr>
				<td><?php echo user::returnAvatar($user['user_id'], false, 16, 16); ?></td>
				<td><?php echo user::formatName($user['user_id'], true, true); ?></td>
				<td><?php echo $user['name']; ?></td>
				<td><?php echo user::returnGroup($user['user_id']); ?></td>
				<td><a title="<?php echo date("d/m/Y H:i", $user['last_seen_date']); ?>" class="pointer"><?php echo page::formatTime($user['last_seen_date']); ?></a></td>
				<td>
					<a href="<?php echo $c['url']; ?>/user/<?php echo $user['user_id'] . "-" . $user['seo_name']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button">Profils</button></a>
					<?php if(user::isLoggedIn() AND ($_SESSION['user_id']) != $user['user_id']): ?>
						<a class="pointer" data-toggle="modal" data-target="#sendMessageWindow" data-name="<?php echo $user['display_name']; ?>" data-receiver="<?php echo $user['user_id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button">Sūtīt vēstuli</button></a>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php echo (isset($_POST['search'])) ? "" : $pager_template; ?>
<legend>Tiešsaistes TOP 3</legend>
<table class="table table-bordered table-centered table-vmiddle">
	<thead>
		<th></th>
		<th>Uzrādošais vārds</th>
		<th>Vārds</th>
		<th>Grupa</th>
		<th>Pēdējā aktivitāte</th>
		<th>Apskatīt profilu</th>
	</thead>
	<tbody class="data">
		<?php $top_users = $db->fetchAll("SELECT * FROM `users` ORDER BY `online_time` DESC LIMIT 3"); ?>
		<?php foreach($top_users as $user): ?>
			<tr>
				<td><?php echo user::returnAvatar($user['user_id'], false, 16, 16); ?></td>
				<td><?php echo user::formatName($user['user_id'], true, true); ?></td>
				<td><?php echo $user['name']; ?></td>
				<td><?php echo user::returnGroup($user['user_id']); ?></td>
				<td><a title="<?php echo date("d/m/Y H:i", $user['last_seen_date']); ?>" class="pointer"><?php echo page::formatTime($user['last_seen_date']); ?></a></td>
				<td>
					<?php echo text::secondsToTime($user['online_time'] * 60); ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>