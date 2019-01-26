<?php $page = new page("Vēstules", array("Vēstules", "Saraksts")); ?>
<?php if(isset($path[2]) AND !empty($path[2]) AND $path[2] != "delete"): ?>
	<?php
	$request = $path[2];
	$explode = explode("-", $request);
	$remove_id = array_slice($explode, 1);
	$title_seo = implode("-", $remove_id);
	$conversation_id = $explode[0];
	$find_conversation = $db->count("SELECT `id` FROM `conversations` WHERE `id` = ? AND `title_seo` = ? AND (`author_id` = ? OR `participants` = ?)", array($conversation_id, $title_seo, $_SESSION['user_id'], $_SESSION['user_id']));
	?>
	<?php if($find_conversation == 0): page::alert("Pieprasītā saruna netika atrasta!", "danger"); else: ?>
		<?php $conversation = $db->fetch("SELECT * FROM `conversations` WHERE `id` = ?", array($conversation_id)); ?>
		<?php $deleted = (empty($conversation['deleted'])) ? array() : explode(",", $conversation['deleted']); ?>
		<?php if(in_array($_SESSION['user_id'], $deleted)): page::alert("Tu esi dzēsis šo sarunu!", "danger"); else: ?>
			<div style="color: #323232; font-size: 20pt; text-indent: 20px; background: #efefef; border: 1px solid #ccc; text-shadow: #fff 0px 1px 0px; padding: 10px;">
				<?php echo $conversation['title']; ?>
			</div>
			<?php
			if(user::isLoggedIn()){
				if(isset($_POST['sendMessage'])){
					$errors = array();

					if(empty($_POST['message'])){
						$errors[] = "Tu aizmirsi ievadīt ziņas saturu!";
					}

					if(count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}else{
						$db->insert("INSERT INTO `messages` (`conversation_id`, `author_id`, `date`, `message`) VALUES (?, ?, ?, ?)", array(
							$conversation['id'],
							$_SESSION['user_id'],
							time(),
							$_POST['message']
							));
						$db->update("UPDATE `conversations` SET `last_activity` = ? WHERE `id` = ?", array(time(), $conversation['id']));
					}
				}
			}
			?>
			<table class="messages topic">
				<tbody>
					<?php $messages = $db->fetchAll("SELECT * FROM `messages` WHERE `conversation_id` = ? ORDER BY `date` ASC", array($conversation_id)); ?>
					<?php $i = 1; ?>
					<?php foreach($messages as $message): ?>
						<?php if($message['author_id'] != $_SESSION['user_id']){ $db->update("UPDATE `messages` SET `read_by_receiver` = 1 WHERE `message_id` = ?", array($message['message_id'])); } ?>
						<?php $user = user::data($message['author_id']); ?>
						<?php if($i == 1): ?>
							<tr class="title">
								<td align="left" valign="middle">
									<img src="<?php echo $c['url']; ?>/assets/images/icons/<?php echo (user::isOnline($user['user_id'])) ? "online" : "offline"; ?>.png" style="vertical-align: middle;">
									<?php echo user::formatName($user['user_id'], true, true, false); ?>
								</td>
								<td align="right">Saruna aizsākta <?php echo page::formatTime($conversation['date']); ?></td>
							</tr>
						<?php else: ?>
							<tr class="title">
								<td align="left" valign="middle">
									<img src="<?php echo $c['url']; ?>/assets/images/icons/<?php echo (user::isOnline($user['user_id'])) ? "online" : "offline"; ?>.png" style="vertical-align: middle;">
									<?php echo user::formatName($user['user_id'], true, true, false); ?>
								</td>
								<td align="right" style="padding-right: 20px;">
									Atbilde pievienota <?php echo page::formatTime($message['date']); ?>
									<span class="pointer" onClick="quote('<?php echo $user['display_name']; ?>', <?php echo $i; ?>)">[CITĒT]</span>
								</td>
							</tr>
						<?php endif; ?>
						<tr>
							<td width="250px" align="center" valign="top" rowspan="<?php echo (empty($user['signature'])) ? 1 : 2; ?>" style="padding-bottom: 30px;">
								<?php echo user::returnAvatar($user['user_id'], false, 150, 150); ?>
								<div><?php echo $user['title']; ?></div>
								<div>---</div>
								<div><?php echo user::returnGroup($user['user_id']); ?></div>
								<div>Pēdējo reizi manīts <?php echo page::formatTime($user['last_seen_date']); ?></div>
								<div>Ieraksti: <?php echo user::returnStatistics($user['user_id'], "posts"); ?></div>
								<div>Nauda: <?php echo user::getMoney($user['user_id']); ?></div>
								<?php if(!empty($user['skype'])): ?>
									<div>Skype: <a href="skype:<?php echo $user['skype']; ?>?chat"><?php echo $user['skype']; ?></a></div>
								<?php endif; ?>
								<?php if(user::isLoggedIn() AND $_SESSION['user_id'] != $user['user_id']): ?>
									<div><a class="pointer" data-toggle="modal" data-target="#sendMessageWindow" data-name="<?php echo $user['display_name']; ?>" data-receiver="<?php echo $user['user_id']; ?>">Sūtīt vēstuli</a></div>
								<?php endif; ?>
							</td>
							<td valign="top">
								<div id="post_<?php echo $i; ?>"><?php echo text::bbcode($message['message'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?></div>
							</td>
						</tr>
						<?php if(!empty($user['signature'])): ?>
							<tr>
								<td style="padding-top: 10px; padding-bottom: 10px;" valign="top">
									<?php echo text::bbcode($user['signature'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>
								</td>
							</tr>
						<?php endif; ?>
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>

			<table class="messages topic">
				<tbody>
					<tr class="title">
						<td colspan="2">Atbildēt uz šo sarunu</td>
					</tr>
					<tr>
						<td width="32px;" valign="top">
							<?php echo user::returnAvatar($_SESSION['user_id'], false, 100, 100); ?>
						</td>
						<td>
							<div class="form">
								<form method="POST">
									<textarea type="text" class="control" id="editor" name="message" placeholder="Tava ziņa..." rows="10"></textarea>
									<button type="submit" name="sendMessage" class="blue" style="margin-bottom: 10px;">Atbildēt</button>
								</form>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		<?php endif; ?>
	<?php endif; ?>
<?php elseif(isset($path[2]) AND $path[2] == "delete" AND isset($path[3]) AND !empty($path[3])): ?>
	<?php
	$request = $path[3];
	$explode = explode("-", $request);
	$remove_id = array_slice($explode, 1);
	$title_seo = implode("-", $remove_id);
	$conversation_id = $explode[0];
	$find_conversation = $db->count("SELECT `id` FROM `conversations` WHERE `id` = ? AND `title_seo` = ? AND (`author_id` = ? OR `participants` = ?)", array($conversation_id, $title_seo, $_SESSION['user_id'], $_SESSION['user_id']));
	if($find_conversation == 0){
		page::alert("Pieprasītā saruna netika atrasta!", "danger");
	}else{
		$conversation = $db->fetch("SELECT `deleted` FROM `conversations` WHERE `id` = ?", array($conversation_id));
		$deleted = (empty($conversation['deleted'])) ? array() : explode(",", $conversation['deleted']);
		if(in_array($_SESSION['user_id'], $deleted)){
			page::alert("Pieprasīto saruna jau ir dzēsta!", "danger");
		}else{
			array_push($deleted, $_SESSION['user_id']);
			$deleted = implode(",", $deleted);
			$db->update("UPDATE `messages` SET `read_by_receiver` = 1 WHERE `conversation_id` = ?", array($conversation_id));
			$db->update("UPDATE `conversations` SET `deleted` = ? WHERE `id` = ?", array($deleted, $conversation_id));
			echo page::alert("Saruna veiksmīgi dzēsta! Dodamies atpakaļ...", "success");
		}
	}
	page::redirectTo("messages", array("external" => false, "time" => 3));
	?>
<?php else: ?>
	<table class="messages">
		<tbody>
			<tr class="title">
				<td colspan="3">
					Sarunas, kurās Tu esi iesaistīts
				</td>
			</tr>
			<tr class="top">
				<td>Temats</td>
				<td width="50px" class="text-center">Atbildes</td>
				<td width="200px" class="text-center">Pēdējā ziņa</td>
			</tr>
			<?php $conversations = $db->fetchAll("SELECT * FROM `conversations` WHERE (`author_id` = ? OR `participants` = ?) ORDER BY `last_activity` DESC", array($_SESSION['user_id'], $_SESSION['user_id'])); ?>
			<?php foreach($conversations as $conversation): ?>
				<?php $deleted = (empty($conversation['deleted'])) ? array() : explode(",", $conversation['deleted']); ?>
				<?php if(!in_array($_SESSION['user_id'], $deleted)): ?>
					<tr>
						<?php $count_unread = $db->count("SELECT `message_id` FROM `messages` WHERE `conversation_id` = ? AND `author_id` != ? AND `read_by_receiver` = 0", array($conversation['id'], $_SESSION['user_id'])); ?>
						<td>
							<a href="<?php echo $c['url']; ?>/messages/<?php echo $conversation['id'] . "-" . $conversation['title_seo']; ?>" style="font-weight: bold;"><?php echo $conversation['title']; ?> <?php if($count_unread > 0): ?><span class="label label-info pull-right" style="margin-left: 5px;">NELASĪTS</span><?php endif; ?></a>
							<a href="<?php echo $c['url']; ?>/messages/delete/<?php echo $conversation['id'] . "-" . $conversation['title_seo']; ?>"><span class="label label-danger pull-right">DZĒST</span></a>
							<div>Sarunu aizsāka <?php echo user::formatName($conversation['author_id'], false, true, false); ?> ar <?php echo user::formatName($conversation['participants'], false, true, false); ?> <?php echo page::formatTime($conversation['date']); ?></div>
						</td>
						<td class="text-center" width="50px"><?php echo $db->count("SELECT `message_id` FROM `messages` WHERE `conversation_id` = ?", array($conversation['id'])); ?></td>
						<td width="240px">
							<?php $message = $db->fetch("SELECT `author_id`, `date` FROM `messages` WHERE `conversation_id` = ? ORDER BY `date` DESC LIMIT 1", array($conversation['id'])); ?>
							<div style="float: left; margin-top: 3px;">
								<?php echo user::returnAvatar($message['author_id'], false, 32, 32); ?>
							</div>
							<div style="text-indent: 10px; margin-top: 6px; text-align: left;">
								<div>No <?php echo user::formatName($message['author_id'], false, true, false); ?> <?php echo page::formatTime($message['date']); ?></div>
								<div class="pull-right">
									<a href="<?php echo $c['url']; ?>/messages/<?php echo $conversation['id'] . "-" . $conversation['title_seo']; ?>" style="font-weight: bold;">Lasīt...</a>
								</div>
							</div>
						</td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>