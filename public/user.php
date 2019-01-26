<?php
if(isset($path[2]) AND !empty($path[2])){
	# Action
	$request = $path[2];
	$explode = explode("-", $request);
	$remove_id = array_slice($explode, 1);
	# Data
	$seo_name = implode("-", $remove_id);
	$user_id = $explode[0];
	$find_user = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ? AND `seo_name` = ?", array($user_id, $seo_name));
	if($find_user == 0){
		$page = new page("Lietotāji", array("Lietotāji", "Lietotājs netika atrasts"));
		page::alert("Lietotājs ar norādīto informāciju netika atrasts!", "danger");
	}else{
		$user = user::data($user_id);
		$page = new page($user['display_name'] . " profils", array("Lietotāji", $user['display_name']));
		forum::updateViews("user", $user_id);
		?>
		<style>
			tr td:first-child{
				width: 200px;
			}
		</style>
		<table>
			<tbody class="data">
				<tr class="title">
					<td colspan="2"><?php echo user::formatName($user_id, false); ?> Profils</td>
				</tr>
				<tr>
					<td>Vārds</td>
					<td><?php echo $user['name']; ?></td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="padding-top: 10px;">
						<?php echo user::returnAvatar($user_id, true); ?>
					</td>
				</tr>
				<tr class="title">
					<td colspan="2">Par <?php echo user::formatName($user_id, false); ?></td>
				</tr>
				<tr>
					<td>Reģistrācijas datums</td>
					<td><a title="<?php echo date("d/m/Y H:i", $user['registration_date']); ?>" class="pointer"><?php echo page::formatTime($user['registration_date']); ?></a></td>
				</tr>
				<tr>
					<td>Pēdējā aktivitāte</td>
					<td><a title="<?php echo date("d/m/Y H:i", $user['last_seen_date']); ?>" class="pointer"><?php echo page::formatTime($user['last_seen_date']); ?></a></td>
				</tr>
				<tr>
					<td>Kopā laiks tiešsaistē</td>
					<td><?php echo text::secondsToTime($user['online_time'] * 60); ?></td>
				</tr>
				<tr>
					<td>Brīdinājuma līmenis</td>
					<td><?php echo user::returnWarning($user['user_id']); ?></td>
				</tr>
				<tr>
					<td>Ieraksti</td>
					<td><?php echo user::returnStatistics($user['user_id'], "posts"); ?></td>
				</tr>
				<tr>
					<td>Grupa</td>
					<td><?php echo user::returnGroup($user_id); ?></td>
				</tr>
				<tr>
					<td>Atrašanās vieta</td>
					<td><?php echo (empty($user['location'])) ? "Nav norādīta" : $user['location']; ?></td>
				</tr>
				<tr>
					<td>Vecums</td>
					<td><?php echo (empty($user['birthday'])) ? "Nav norādīts" : user::returnAge($user_id); ?></td>
				</tr>
				<tr>
					<td>Par sevi</td>
					<td><?php echo nl2br(strip_tags(htmlspecialchars($user['about']))); ?></td>
				</tr>
				<tr>
					<td>Paraksts</td>
					<td><?php echo text::bbcode($user['signature'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?></td>
				</tr>
				<tr class="title">
					<td colspan="2">Kontaktinformācija</td>
				</tr>
				<tr>
					<td>Skype</td>
					<td>
						<?php if(empty($user['skype'])): ?>
							Nav norādīts
						<?php else: ?>
							<a href="skype:<?php echo $user['skype']; ?>?chat"><?php echo $user['skype']; ?></a>
						<?php endif; ?>
					</td>
				</tr>
				<?php if(user::isLoggedIn() AND $_SESSION['user_id'] != $user_id): ?>
					<tr>
						<td>Rakstīt vēstuli</td>
						<td><a class="pointer" data-toggle="modal" data-target="#sendMessageWindow" data-name="<?php echo $user['display_name']; ?>" data-receiver="<?php echo $user_id; ?>">Sūtīt vēstuli</a></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php addons::returnComments("users", $user['user_id']); ?>
		<?php
	}
}else{
	$page = new page("Nekas netika atrasts");
	page::alert("Lapa netika atrasta!", "danger");
}