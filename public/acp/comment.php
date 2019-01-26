<?php
if(isset($path[3])){
	$action = $path[3];
}else{
	$action = "not found";
}

if($action == "del"){
	if(isset($path[4]) AND is_numeric($path[4])){
		$comment_id = (int)$path[4];
		$find_comment = $db->count("SELECT `id` FROM `comments` WHERE `id` = ?", array($comment_id));
		if($find_comment == 0){
			echo page::alert("Komentārs netika atrasts!", "danger");
		}else{
			$db->delete("DELETE FROM `comments` WHERE `id` = ?", array($comment_id));
			echo page::alert("Komentārs veiksmīgi dzēsts! Dodamies atpakaļ...", "success");
			page::redirectTo($_SERVER['HTTP_REFERER'], array("external" => true, "time" => 3));
		}
	}
}elseif($action == "edit"){
	if(isset($path[4]) AND is_numeric($path[4])){
		$comment_id = (int)$path[4];
		$find_comment = $db->count("SELECT `id` FROM `comments` WHERE `id` = ?", array($comment_id));
		if($find_comment == 0){
			echo page::alert("Komentārs netika atrasts!", "danger");
		}else{
			if(isset($_POST['edit'])){
				$errors = array();

				if(empty($_POST['text'])){
					$errors[] = "Neaizmirsti aizpildīt komentāru!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$db->update("UPDATE `comments` SET `text` = ? WHERE `id` = ?", array($_POST['text'], $comment_id));
					echo page::alert("Komentārs veiksmīgi rediģēts!", "success");
				}
			}
			$comment = $db->fetch("SELECT `text` FROM `comments` WHERE `id` = ?", array($comment_id));
			?>
			<div class="form smaller">
				<form method="POST" enctype="multipart/form-data">
					<label for="text" class="required">Komentārs</label>
					<textarea type="text" name="text" id="editor" class="control" placeholder="Komentārs..." rows="15"><?php echo $comment['text']; ?></textarea>

					<button type="submit" name="edit" class="blue">Rediģēt</button>
				</form>
			</div>
			<?php
		}
	}
}else{
	echo page::alert("Pieprasītā darbība netika atrasta!", "danger");
}