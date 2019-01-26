</div>
<div class="side">
	<?php foreach($servers as $key => $value): ?>
		<div id="panels">
			<div id="counterstrike">
				<div id="monitoring"><div class="server" id="<?php echo $key; ?>"></div></div>
			</div>
		</div>
	<?php endforeach; ?>
	<div id="panels">
		<div class="block">
			<div class="title">
				10 jaunākie ieraksti/atbildes iekš mūsu foruma.
			</div>
			<?php $topics = $db->fetchAll("SELECT * FROM `topics` WHERE `approved` = 1 ORDER BY `last_post_date` DESC LIMIT 6"); ?>
			<?php if(empty($topics)): ?>
				<div class="message">Neviens ieraksts vēl nav izveidots!</div>
			<?php else: ?>
				<?php foreach($topics as $topic): ?>
					<div class="row">
						<div class="avatar">
							<?php echo user::returnAvatar($topic['last_poster_id'], false, 32, 32, false); ?>
						</div>
						<div class="info">
							<div class="main"><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>"><?php echo text::limit($topic['title'], 18); ?></a></div>
							<div class="information">atbildēja <?php echo user::formatName($topic['last_poster_id'], false, true, false, 12); ?></div>
						</div>
						<div class="other"><?php echo page::formatTime($topic['last_post_date']); ?></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div id="panels">
		<div class="block">
			<?php
			$online_users = $db->count("SELECT `session_id` FROM `sessions` WHERE `user_id` > 0");
			$online_visitors = $db->count("SELECT `session_id` FROM `sessions` WHERE `user_id` = 0");
			?>
			<div class="title">
				šobrīd mūsu portālu apmeklē - <?php echo $online_users; ?> <?php echo ($online_users == 1) ? "lietotājs" : "lietotāji"; ?> un <?php echo $online_visitors; ?> <?php echo ($online_visitors == 1) ? "ciemiņš" : "ciemiņi"; ?>.
			</div>

			<?php $sessions = $db->fetchAll("SELECT * FROM `sessions` WHERE `user_id` > 0"); ?>
			<?php if(empty($sessions)): ?>
				<div class="message">Neviens lietotājs nav tiešsaistē!</div>
			<?php else: ?>
				<div class="side">
					<?php foreach($sessions as $session): ?>
					<?php endforeach; ?>
				</div>
				<?php foreach($sessions as $session): ?>
					<div class="row">
						<div class="avatar">
							<?php echo user::returnAvatar($session['user_id'], false, 32, 32, false); ?>
						</div>
						<div class="info">
							<div class="main"><a href="#"><?php echo user::formatName($session['user_id'], false, true, false, 14); ?></a></div>
							<div class="information"><?php echo user::returnAction($session['user_id']); ?></div>
						</div>
						<div class="other"><?php echo user::returnGroup($session['user_id']); ?></div>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<div id="panels">
		<div class="block">
			<div class="title">
				Pēdējie 9 lietotāju pievienotie video
			</div>
			<?php $videos = $db->fetchAll("SELECT * FROM `videos` ORDER BY `time` DESC LIMIT 9"); ?>
			<div style="width: 240px; margin: 0 auto;">
				<?php foreach($videos as $video): ?>
					<a href="<?php $c['url']; ?>/videos/view/<?php echo $video['id'] . "-" . $video['title_seo']; ?>" style="margin: 2px; display: inline-block;">
						<img src="http://img.youtube.com/vi/<?php echo $video['video_id']; ?>/0.jpg" width="70px" height="45px" style="border: 1px solid black; border-radius: 3px;">
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>