<?php
if(isset($path[3]) AND !empty($path[3])){
	$action = $path[3];
}else{
	$action = "list";
}
?>
<?php if($action == "deleted" AND user::hasFlag("admin")){ ?>
	<ul class="nav nav-tabs margin">
		<li class="active"><a href="#topics" data-toggle="tab">Tēmas</a></li>
		<li><a href="#posts" data-toggle="tab">Ieraksti</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade in active" id="topics">
			<?php
			if(isset($_POST['delete'])){
				$topic_id = $_POST['topic_id'];
				foreach($_POST['delete'] as $id => $key){
					$db->update("DELETE FROM `topics` WHERE `tid` = ?", array($topic_id[$id]));
				}
				echo page::alert("Tēma veiksmīgi dzēsta!", "success");
			}
			if(isset($_POST['revert'])){
				$topic_id = $_POST['topic_id'];
				foreach($_POST['revert'] as $id => $key){
					$db->update("UPDATE `topics` SET `approved` = 1 WHERE `tid` = ?", array($topic_id[$id]));
				}
				echo page::alert("Tēma veiksmīgi atjaunota!", "success");
			}
			?>
			<table class="table table-bordered">
				<thead>
					<th>Tēmas nosaukums</th>
					<th>Tēmas saturs</th>
					<th>Tēmas autors</th>
					<th>Darbības</th>
				</thead>
				<tbody>
					<form method="POST">
						<?php $t = 1; ?>
						<?php $deleted_topics = $db->fetchAll("SELECT `tid`, `title`, `topic`, `author_id` FROM `topics` WHERE `approved` != 1 ORDER BY `topic_date` ASC"); ?>
						<?php foreach($deleted_topics as $topic): ?>
							<input type="hidden" name="topic_id[<?php echo $t; ?>]" value="<?php echo $topic['tid']; ?>">
							<tr>
								<td width="150px"><?php echo $topic['title']; ?></td>
								<td style="word-break: break-all;"><?php echo text::bbcode($topic['topic'], array("bbcode" => true, "emoticons" => true)); ?></td>
								<td width="100px"><?php echo user::formatName($topic['author_id'], true, true); ?></td>
								<td width="150px">
									<button class="btn btn-primary btn-xs dropdown-toggle" type="submit" name="revert[<?php echo $t; ?>]"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Atgriezt</button>
									<button class="btn btn-danger btn-xs dropdown-toggle" type="submit" name="delete[<?php echo $t; ?>]"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Dzēst</button>
								</td>
							</tr>
							<?php $t++; ?>
						<?php endforeach; ?>
					</form>
				</tbody>
			</table>
		</div>
		<div class="tab-pane fade" id="posts">
			<?php
			if(isset($_POST['delete'])){
				$post_id = $_POST['post_id'];
				foreach($_POST['delete'] as $id => $key){
					$db->update("DELETE FROM `posts` WHERE `pid` = ?", array($post_id[$id]));
				}
				echo page::alert("Ieraksts veiksmīgi dzēsts!", "success");
			}
			if(isset($_POST['revert'])){
				$post_id = $_POST['post_id'];
				foreach($_POST['revert'] as $id => $key){
					$db->update("UPDATE `posts` SET `approved` = 1 WHERE `pid` = ?", array($post_id[$id]));
				}
				echo page::alert("Ieraksts veiksmīgi atjaunots!", "success");
			}
			?>
			<table class="table table-bordered">
				<thead>
					<th>Tēma</th>
					<th>Ieraksta saturs</th>
					<th>Ieraksta autors</th>
					<th>Darbības</th>
				</thead>
				<tbody>
					<form method="POST">
						<?php $p = 1; ?>
						<?php $deleted_posts = $db->fetchAll("SELECT `pid`, `post`, `author_id`, `topic_id` FROM `posts` WHERE `approved` != 1 ORDER BY `post_date` ASC"); ?>
						<?php foreach($deleted_posts as $post): ?>
							<input type="hidden" name="post_id[<?php echo $p; ?>]" value="<?php echo $post['pid']; ?>">
							<tr>
								<?php $topic = $db->fetch("SELECT `tid`, `title`, `title_seo` FROM `topics` WHERE `tid` = ?", array($post['topic_id'])); ?>
								<td><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;" target="_blank"><?php echo text::limit($topic['title'], 20); ?></a></td>
								<td style="word-break: break-all;"><?php echo text::bbcode($post['post'], array("bbcode" => true, "emoticons" => true)); ?></td>
								<td width="100px"><?php echo user::formatName($post['author_id'], true, true); ?></td>
								<td width="150px">
									<button class="btn btn-primary btn-xs dropdown-toggle" type="submit" name="revert[<?php echo $p; ?>]"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Atgriezt</button>
									<button class="btn btn-danger btn-xs dropdown-toggle" type="submit" name="delete[<?php echo $p; ?>]"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Dzēst</button>
								</td>
							</tr>
							<?php $p++; ?>
						<?php endforeach; ?>
					</form>
				</tbody>
			</table>
		</div>
	</div>
	<?php }elseif($action == "edit"){ ?>
	<?php if(isset($path[4])){ ?>
	<?php
	$forum_id = (int)$path[4];
	$find_forum = $db->count("SELECT `id` FROM `forums` WHERE `id` = ?", array($forum_id));
	if($find_forum == 0){
		page::alert("Neviena sadaļa vai kategorija ar norādīto ID netika atrasta!", "danger");
	}else{
		$forum = $db->fetch("SELECT * FROM `forums` WHERE `id` = ?", array($forum_id)); ?>
		<?php if($forum['parent_id'] == 0){ ?>
		<?php
		if(isset($_POST['edit'])){
			$errors = array();

			if(empty($_POST['title'])){
				$errors[] = "Tu aizmirsi ievadīt kategorijas nosaukumu!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$db->update("UPDATE `forums` SET `title` = ? WHERE `id` = ?", array($_POST['title'], $forum_id));
				page::alert("Foruma kategorija veiksmīgi rediģēta!", "success");
			}
			$forum = $db->fetch("SELECT * FROM `forums` WHERE `id` = ?", array($forum_id));
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Nosaukums" name="title" value="<?php echo $forum['title']; ?>">
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="edit">Rediģēt</button>
			</div>
		</form>
		<?php }else{ ?>
		<?php
		if(isset($_POST['edit'])){
			$errors = array();

			if(empty($_POST['title'])){
				$errors[] = "Tu aizmirsi ievadīt sadaļas nosaukumu!";
			}

			if(empty($_POST['parent_id'])){
				$errors[] = "Tu aizmirsi izvēlēties kategoriju!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$last_pos = $db->fetch("SELECT `position` FROM `forums` WHERE `parent_id` = ? ORDER BY `position` DESC LIMIT 1", array($_POST['parent_id']));
				$db->update("UPDATE `forums` SET `title` = ?, `description` = ?, `parent_id` = ?, `position` = ?, `allow_topics` = ?, `allowed_groups` = ?, `mod_groups` = ? WHERE `id` = ?", array(
					$_POST['title'],
					$_POST['description'],
					$_POST['parent_id'],
					$last_pos['position'] + 1,
					isset($_POST['allow_topics']) ? 1 : 0,
					isset($_POST['allowed_groups']) ? implode(",", $_POST['allowed_groups']) : "",
					isset($_POST['mod_groups']) ? implode(",", $_POST['mod_groups']) : "",
					$forum_id
					));
				page::alert("Foruma sadaļa veiksmīgi rediģēta!", "success");
				$forum = $db->fetch("SELECT * FROM `forums` WHERE `id` = ?", array($forum_id));
			}
		}
		if(isset($_POST['delete'])){
			$db->update("UPDATE `topics` SET `approved` = 2 WHERE `forum_id` = ?", array($forum_id));
			$db->delete("DELETE FROM `forums` WHERE `id` = ?", array($forum_id));
			page::redirectTo("acp/forums", array("external" => false, "time" => 2));
			echo page::alert("Sadaļa veiksmīgi dzēsta!", "success");
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Nosaukums" name="title" value="<?php echo $forum['title']; ?>">
			<input type="text" class="form-control" placeholder="Apraksts" name="description" value="<?php echo $forum['description']; ?>">
			<select class="form-control" name="parent_id">
				<option disabled selected>Izvēlies kategoriju</option>
				<?php $main_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` != ? ORDER BY `position` ASC", array($forum['id'])); ?>
				<?php foreach($main_cat as $main): ?>
					<option value="<?php echo $main['id']; ?>" <?php echo ($forum['parent_id'] == $main['id']) ? "selected" : ""; ?>><?php echo $main['title']; ?></option>
				<?php endforeach; ?>
			</select>
			<div style="width: 400px; margin: 0 auto">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="allow_topics" <?php echo ($forum['allow_topics'] == 1) ? "checked" : ""; ?>> Uzrādīt tēmas?
					</label>
				</div>
			</div>
			<?php $groups = $db->fetchAll("SELECT `group_id`, `description` FROM `groups` ORDER BY `group_id` ASC"); ?>
			<label for="allowed_groups">Grupas, kurām atļauts izveidot tēmas</label>
			<select class="form-control" name="allowed_groups[]" multiple <?php echo ($forum['allow_topics'] == 0) ? "disabled" : ""; ?>>
				<?php $allowed_groups = explode(",", $forum['allowed_groups']); ?>
				<?php foreach($groups as $group): ?>
					<option value="<?php echo $group['group_id']; ?>" <?php echo (in_array($group['group_id'], $allowed_groups)) ? "selected" : ""; ?>><?php echo $group['description']; ?></option>
				<?php endforeach; ?>
			</select>
			<label for="allowed_groups">Grupas, kurām atļauts izmantot MOD iespējas</label>
			<select class="form-control" name="mod_groups[]" multiple>
				<?php $mod_groups = explode(",", $forum['mod_groups']); ?>
				<?php foreach($groups as $group): ?>
					<option value="<?php echo $group['group_id']; ?>" <?php echo (in_array($group['group_id'], $mod_groups)) ? "selected" : ""; ?>><?php echo $group['description']; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="form-button">
				<?php $find_subforums = $db->count("SELECT `id` FROM `forums` WHERE `parent_id` = ?", array($forum_id)); ?>
				<button type="submit" class="btn btn-primary btn-sm" name="edit">Rediģēt</button> <button type="submit" class="btn btn-danger btn-sm" name="delete" <?php echo ($find_subforums == 0) ? "" : "onClick=\"return confirm('Šim forumam ir apakšsadaļas. Vai tiešām vēlies turpināt?')\""; ?>>Dzēst</button>
			</div>
		</form>
		<?php } } ?>
		<?php } ?>
		<?php }elseif($action == "add"){ ?>
		<?php if(isset($path[4]) AND $path[4] == "cat"){ ?>
		<?php
		if(isset($_POST['add'])){
			$errors = array();

			if(empty($_POST['title'])){
				$errors[] = "Tu aizmirsi ievadīt kategorijas nosaukumu!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$last_pos = $db->fetch("SELECT `position` FROM `forums` WHERE `parent_id` = 0 ORDER BY `position` DESC LIMIT 1");
				$db->insert("INSERT INTO `forums` (`title`, `title_seo`, `parent_id`, `position`, `allow_topics`) VALUES (?, ?, ?, ?, ?)", array(
					$_POST['title'],
					text::seostring($_POST['title']),
					0,
					$last_pos['position'] + 1,
					0
					));
				page::alert("Foruma kategorija veiksmīgi pievienota!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Nosaukums" name="title">
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="add">Pievienot</button>
			</div>
		</form>
		<?php }else{ ?>
		<?php
		if(isset($_POST['add'])){
			$errors = array();

			if(empty($_POST['title'])){
				$errors[] = "Tu aizmirsi ievadīt sadaļas nosaukumu!";
			}

			if(empty($_POST['parent_id'])){
				$errors[] = "Tu aizmirsi izvēlēties kategoriju!";
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$last_pos = $db->fetch("SELECT `position` FROM `forums` WHERE `parent_id` = ? ORDER BY `position` DESC LIMIT 1", array($_POST['parent_id']));
				$db->insert("INSERT INTO `forums` (`title`, `title_seo`, `description`, `last_poster_id`, `last_post_id`, `parent_id`, `position`, `allow_topics`, `allowed_groups`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", array(
					$_POST['title'],
					text::seostring($_POST['title']),
					$_POST['description'],
					0,
					0,
					$_POST['parent_id'],
					$last_pos['position'] + 1,
					isset($_POST['allow_topics']) ? 1 : 0,
					isset($_POST['allowed_groups']) ? implode(",", $_POST['allowed_groups']) : ""
					));
				page::alert("Foruma sadaļa veiksmīgi pievienota!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" class="form-control" placeholder="Nosaukums" name="title">
			<input type="text" class="form-control" placeholder="Apraksts" name="description">
			<select class="form-control" name="parent_id">
				<option disabled selected>Izvēlies kategoriju</option>
				<?php $main_cat = $db->fetchAll("SELECT * FROM `forums` ORDER BY `position` ASC", array()); ?>
				<?php foreach($main_cat as $main): ?>
					<option value="<?php echo $main['id']; ?>"><?php echo ($main['parent_id'] == 0) ? "- " : ""; ?><?php echo $main['title']; ?></option>
				<?php endforeach; ?>
			</select>
			<div style="width: 400px; margin: 0 auto">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="allow_topics"> Uzrādīt tēmas?
					</label>
				</div>
			</div>
			<?php $groups = $db->fetchAll("SELECT `group_id`, `description` FROM `groups` ORDER BY `group_id` ASC"); ?>
			<label for="allowed_groups">Grupas, kurām atļauts izveidot tēmas</label>
			<select class="form-control" name="allowed_groups[]" multiple <?php echo ($forum['allow_topics'] == 0) ? "disabled" : ""; ?>>
				<?php foreach($groups as $group): ?>
					<option value="<?php echo $group['group_id']; ?>"><?php echo $group['description']; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="form-button">
				<button type="submit" class="btn btn-primary btn-sm" name="add">Pievienot</button>
			</div>
		</form>
		<?php } ?>
		<?php }else{ ?>
		<?php $main_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array(0)); ?>
		<?php foreach($main_cat as $main): ?>
			<script type="text/javascript">
				function moveForum(direction, position){
					var dataString = { direction: direction, position: position }; 
					$.ajax({
						type: "POST",
						url: "/public/ajax/moveForum.php",
						data: dataString,
						cache: false,
						async: false
					}).done(function(returned) {
						alert(returned);
					});
				}
			</script>
			<table class="table table-bordered" style="margin-top: 5px;">
				<thead>
					<th><a href="<?php echo $c['url']; ?>/acp/forums/edit/<?php echo $main['id']; ?>"><?php echo $main['title']; ?></a></th>
					<th width="100px">Darbības</th>
				</thead>
				<tbody>
					<?php $sub_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($main['id'])); ?>
					<?php if(empty($sub_cat)): ?>
						<tr>
							<td colspan="2">Kategorijā nav nevienas sadaļas</td>
						</tr>
					<?php else: ?>
						<form method="POST">
							<?php $i = 1; ?>
							<?php foreach($sub_cat as $sub): ?>
								<tr>
									<td>
										<a href="<?php echo $c['url']; ?>/acp/forums/edit/<?php echo $sub['id']; ?>"><?php echo $sub['title']; ?></a>
										<?php $subforums = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($sub['id'])); ?>
										<?php if(!empty($subforums)): ?>
											<div>
												<?php foreach($subforums as $forum): ?>
													<span class="glyphicon glyphicon-bookmark"></span> <a href="<?php echo $c['url']; ?>/acp/forums/edit/<?php echo $forum['id']; ?>"><?php echo $forum['title']; ?></a>
												<?php endforeach; ?>
											</div>
										<?php endif; ?>
									</td>
									<td>
										<a href="<?php echo $c['url']; ?>/acp/forums/edit/<?php echo $sub['id']; ?>"><button class="btn btn-primary btn-xs dropdown-toggle" type="button"><span class="glyphicon glyphicon-pencil" style="opacity: 0.4;"></span> Rediģēt</button></a>
										<span class="pull-right">
											<?php if($i > 1): ?>
												<i class="fa fa-chevron-circle-up pointer" onClick="moveForum('up', <?php echo $sub['position']; ?>)"></i><br />
											<?php endif; ?>
											<i class="fa fa-chevron-circle-down pointer" onClick="moveForum('down', <?php echo $sub['position']; ?>)"></i>
										</span>
									</td>
								</tr>
								<?php $i++; ?>
							<?php endforeach; ?>
						</form>
					<?php endif; ?>
				</tbody>
			</table>
		<?php endforeach; ?>
		<?php }