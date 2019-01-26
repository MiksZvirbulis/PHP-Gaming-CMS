<?php
class addons{
	public static function returnComments($parent_type, $parent_id){
		global $db;
		global $c;
		if(isset($_POST['add'])){
			$errors = array();

			if(empty($_POST['text'])){
				$errors[] = "Tu aizmirsi pievienot pašu komentāru!";
			}else{
				if(strlen($_POST['text']) < 5){
					$errors[] = "Lūdzu izsakies garāk, savādāk uzskatīsim to par spamu!";
				}elseif(strlen($_POST['text']) > 300){
					$errors[] = "Ierobežo savu sakāmo. Mēģini iekļauties 300 rakstzīmēs!";
				}
			}

			if(count($errors) == 0){
				$db->insert(
					"INSERT INTO `comments` (`added`, `text`, `author_id`, `parent_type`, `parent_id`) VALUES (?, ?, ?, ?, ?)", array(
						time(),
						$_POST['text'],
						$_SESSION['user_id'],
						$parent_type,
						$parent_id
						)
					);
				$added = true;
			}
		}
		$comments = $db->fetchAll("SELECT * FROM `comments` WHERE `parent_type` = ? AND `parent_id` = ? ORDER BY `added` DESC", array($parent_type, $parent_id));
		?>
		<div class="clear"></div>
		<div id="comments">
			<?php
			if(empty($comments)){
				page::alert("Neviens komentārs vēl nav izveidots. Vēlies būt pirmais?", "danger");
			}else{
				foreach($comments as $comment){
					$user = user::data($comment['author_id']);
					?>
					<div id="comment">
						<div class="left">
							<a href="<?php echo $c['url']; ?>/user/<?php echo $user['user_id'] . "-" . $user['seo_name']; ?>/"><?php echo user::returnAvatar($comment['author_id'], false, 32, 32, false); ?></a>
						</div>
						<div class="right">
							<div class="info">
								<?php if(user::isLoggedIn()): ?>
									<a class="pointer author_nick_<?php echo $comment['id']; ?>" onclick="at(this)">@<?php echo user::formatName($comment['author_id'], false, false); ?></a> @ <?php echo date("d/m/Y", $comment['added']); ?> plkst. <?php echo date("H:i", $comment['added']); ?>
								<?php else: ?>
									<a href="<?php echo $c['url']; ?>/user/<?php echo $user['user_id'] . "-" . $user['seo_name']; ?>/"><?php echo user::formatName($comment['author_id'], false, true); ?></a> @ <?php echo date("d/m/Y", $comment['added']); ?> plkst. <?php echo date("H:i", $comment['added']); ?>
								<?php endif; ?>
								<span class="id">
									<span class="glyphicon glyphicon-comment"></span>
									<?php if(user::hasFlag("admin")): ?>
										<a href="<?php echo $c['url']; ?>/acp/comment/del/<?php echo $comment['id']; ?>"><span class="glyphicon glyphicon-trash"></span></a>
										<a href="<?php echo $c['url']; ?>/acp/comment/edit/<?php echo $comment['id']; ?>"><span class="glyphicon glyphicon-edit"></span></a>
									<?php endif; ?>
								</span>
							</div>
							<div class="text">
								<?php echo text::bbcode($comment['text'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php
				}
			}
			?>
			<?php if(user::isLoggedIn()): ?>
				<hr />
				<legend>Pievienot komentāru</legend>
				<div class="form smaller">
					<?php
					if(isset($errors) AND count($errors) > 0){
						foreach($errors as $error){
							page::alert($error, "danger");
						}
					}
					if(isset($added) AND $added === true){
						page::alert("Komentārs veiksmīgi pievienots!", "success");
					}
					?>
					<form method="POST">
						<textarea type="text" name="text" id="editor" class="control comment-input" placeholder="Komentāra teksts..." rows="7"><?php echo (isset($_POST['text']) AND !isset($added)) ? $_POST['text'] : ""; ?></textarea>
						<button type="submit" name="add" class="blue">Pievienot</button>
					</form>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public static function returnCommentCount($parent_type, $parent_id){
		global $db;
		return $db->count("SELECT `id` FROM `comments` WHERE `parent_type` = ? AND `parent_id` = ?", array($parent_type, $parent_id));
	}
}