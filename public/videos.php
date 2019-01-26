<?php
if(isset($path[2]) AND !empty($path[2])){
	$action = $path[2];
}else{
	$action = "list";
}
?>

<?php if($action == "add" AND user::isLoggedIn()): ?>
	<?php $page = new page("Pievienot video", array("Pievienot video")); ?>
	<?php
	if(isset($_POST['add'])){
		$errors = array();

		if(empty($_POST['title'])){
			$errors[] = "Tu aizmirsi izvēlēties video nosaukumu!";
		}

		if(empty($_POST['youtube_id'])){
			$errors[] = "Tu aizmirsi ievadīt video saiti!";
		}else{
			parse_str(parse_url($_POST['youtube_id'], PHP_URL_QUERY), $youtube);
			if(!isset($youtube['v'])){
				$errors[] = "Iekš šīs saites, netika atrasts YouTube video ID!";
			}
		}

		if(empty($_POST['description'])){
			$errors[] = "Tu aizmirsi aprakstīt šo video!";
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				echo page::alert($error, "danger");
			}
		}else{
			$db->insert("INSERT INTO `videos` (`author_id`, `time`, `video_id`, `title`, `title_seo`, `description`) VALUES (?, ?, ?, ?, ?, ?)", array(
				$_SESSION['user_id'],
				time(),
				$youtube['v'],
				$_POST['title'],
				text::seostring($_POST['title']),
				$_POST['description']
				));
			$new_video = $db->fetch("SELECT `id` FROM `videos` ORDER BY `id` DESC LIMIT 1");
			user::addMoney($_SESSION['user_id'], "0.05");
			page::addShout("Es pievienoju jaunu video " . $_POST['title'] . ". Apskati to šeit: " . $c['url'] . "/videos/view/" . $new_video['id'] . "-" . text::seostring($_POST['title']), $_SESSION['user_id']);
			header("Location: " . $c['url'] . "/videos/view/" . $new_video['id'] . "-" . text::seostring($_POST['title']));
		}
	}
	?>
	<div class="form">
		<form method="POST">
			<input type="text" class="control" name="title" placeholder="Video nosaukums" max="255">
			<input type="text" class="control" name="youtube_id" placeholder="YouTube video saite">
			<textarea type="text" id="editor" name="description" placeholder="Video apraksts"></textarea>
			<button type="submit" class="green" name="add">Pievienot</button>
		</form>
	</div>
<?php elseif($action == "view" AND isset($path[3]) AND !empty($path[3])): ?>
	<?php
	$request = $path[3];
	$explode = explode("-", $request);
	$remove_id = array_slice($explode, 1);
	$title_seo = urldecode(implode("-", $remove_id));
	$video_id = $explode[0];
	$find_video = $db->count("SELECT `id` FROM `videos` WHERE `id` = ? AND `title_seo` = ?", array($video_id, $title_seo));
	if($find_video == 0){
		$page = new page("Video netika atrasts!", array("Video netika atrasts!"));
		echo page::alert("Video netika atrasts!", "danger");
	}else{
		$video = $db->fetch("SELECT * FROM `videos` WHERE `id` = ? AND `title_seo` = ?", array($video_id, $title_seo));
		$page = new page($video['title'], array("Video saraksts", $video['title']));
		forum::updateViews("video", $video_id);
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><?php echo $video['title']; ?></div>
			<div class="panel-body">
				<iframe width="560" height="450" src="http://www.youtube.com/embed/<?php echo $video['video_id']; ?>?theme=light" frameborder="0" allowfullscreen></iframe>
				<div class="info_bar" style="margin-top: 10px;">
					Video pievienoja <?php echo user::formatName($video['author_id'], true, true, true); ?> <?php echo page::formatTime($video['time']); ?>
				</div>
				<div class="well"><?php echo text::bbcode($video['description'], array("bbcode" => true, "emoticons" => true)); ?></div>
			</div>
		</div>
		<?php addons::returnComments("videos", $video['id']); ?>
		<?php
	}
	?>
<?php else: ?>
	<?php $page = new page("Video saraksts", array("Video saraksts")); ?>
	<?php
	$videos_count = $db->count("SELECT `id` FROM `videos`");
	list($pager_template, $limit) = page::pagination(9, $videos_count, $c['url'] . "/videos/page/", 3);
	$videos = $db->fetchAll("SELECT * FROM `videos` ORDER BY `time` DESC $limit");
	?>
	<div class="row" style="width: 670px; margin: 0 auto;">
		<?php if(user::isLoggedIn()): ?>
			<div class="form" style="width: 150px; margin: 10px auto;"><a href="<?php $c['url']; ?>/videos/add"><button type="button" class="green" style="float: none;">Pievienot video</button></a></div>
		<?php endif; ?>
		<?php foreach($videos as $video): ?>
			<div class="col-sm-6 col-md-3">
				<div class="thumbnail" style="height: 270px;">
					<a href="<?php $c['url']; ?>/videos/view/<?php echo $video['id'] . "-" . $video['title_seo']; ?>" class="thumbnail" style="margin: 0;"><img src="http://img.youtube.com/vi/<?php echo $video['video_id']; ?>/0.jpg"></a>
					<div class="caption" style="padding: 0 5px 0 5px; text-align: left;">
						<h6 style="text-align: center; font-weight: bold; font-size: 8pt;"><?php echo text::limit($video['title'], 20); ?></h6>
						<p><?php echo text::limit($video['description'], 100); ?></p>
						<p style="position: absolute; bottom: 15px; left: 50%;"><a style="position: relative; left: -50%;" href="<?php $c['url']; ?>/videos/view/<?php echo $video['id'] . "-" . $video['title_seo']; ?>" class="btn btn-info" role="button">Apskatīt</a></p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php echo $pager_template; ?>
<?php endif; ?>