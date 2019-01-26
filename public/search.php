<?php
if(isset($_POST['query'])){
	header("Location: " . $c['url'] . "/search/" . $_POST['query']);
}else{
	$page = new page("Meklēšana", array("Meklēšana"));
	if(isset($path[2]) AND !empty($path[2])){
		if($path[2] == "user" AND !empty($path[3])){
			$user_id = (int)$path[3];
			$query = $db->fetchAll("SELECT `title`, `title_seo`, `tid`, `author_id` FROM `topics` WHERE `author_id` = ? AND `approved` = 1", array($user_id));
			$replies = $db->fetchAll("SELECT `topic_id` FROM `posts` WHERE `author_id` = ? AND `approved` = 1", array($user_id));
		}else{
			$search = $path[2];
			$search = "%$search%";
			$query = $db->fetchAll("SELECT `title`, `title_seo`, `tid`, `author_id` FROM `topics` WHERE (`title` LIKE ? OR `topic` LIKE ?) AND `approved` = 1", array($search, $search));
			$replies = $db->fetchAll("SELECT `topic_id` FROM `posts` WHERE `post` LIKE ? AND `approved` = 1", array($search));
		}
		if(empty($query)){
			echo page::alert("Netika atrasta neviena tēma!", "danger");
		}else{
			?>
			<table class="forum topic" id="reply">
				<tbody>
					<tr class="title">
						<td colspan="2">Tēmas</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered">
				<thead>
					<th>Tēmas nosaukums</th>
					<th>Autors</th>
				</thead>
				<tbody>
					<?php foreach($query as $topic): ?>
						<tr>
							<td><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo text::limit($topic['title'], 20); ?></a></td>
							<td><?php echo user::formatName($topic['author_id'], true, true, true); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}
		if(empty($replies)){
			echo page::alert("Netika atrasta neviena atbilde!", "danger");
		}else{
			?>
			<table class="forum topic" id="reply">
				<tbody>
					<tr class="title">
						<td colspan="2">Atbildes</td>
					</tr>
				</tbody>
			</table>
			<table class="table table-bordered">
				<thead>
					<th>Tēmas nosaukums</th>
					<th>Autors</th>
				</thead>
				<tbody>
					<?php foreach($replies as $post): ?>
						<?php $topic = $db->fetch("SELECT `title`, `title_seo`, `tid`, `author_id` FROM `topics` WHERE `tid` = ?", array($post['topic_id'])); ?>
						<tr>
							<td><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo text::limit($topic['title'], 20); ?></a></td>
							<td><?php echo user::formatName($topic['author_id'], true, true, true); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php
		}
	}else{
		echo page::alert("Neviens kvērijs nav saņemts!", "danger");
	}
}