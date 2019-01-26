<?php
if(isset($path[2]) AND !empty($path[2])){
	$action = $path[2];
}else{
	$action = "profile";
}

if($action == "password"){
	$page = new page("Paroles maiņa", array("Iestatījumi", "Paroles maiņa"));
	if(isset($_POST['change_password'])){
		$errors = array();

		if(empty($_POST['old_password'])){
			$errors[] = "Tu aizmirsi ievadīt savu pašreizējo paroli!";
		}else{
			$get_salt = $db->fetch("SELECT `salt` FROM `users` WHERE `user_id` = ?", array($_SESSION['user_id']));
			$salt = $get_salt['salt'];
			$hash = user::hashPassword($_POST['old_password'], false, $salt);
			$test_login = $db->count("SELECT `user_id` FROM `users` WHERE `user_id` = ? AND `password` = ?", array($_SESSION['user_id'], $hash['hash']));
			if($test_login == 0){
				$errors[] = "Vecā parole nesakrīt ar datubāzi!";
			}
		}

		if(empty($_POST['new_password'])){
			$errors[] = "Tu aizmirsi ievadīt savu jauno paroli!";
		}else{
			if(!empty($_POST['old_password']) AND $_POST['old_password'] == $_POST['new_password']){
				$errors[] = "Jaunajai parolei ir jāatšķiras no pašreizējās paroles!";
			}else{
				if($_POST['new_password'] != $_POST['new_password_repeat']){
					$errors[] = "Jaunās paroles nesakrīt!";
				}
				if(strlen($_POST['new_password']) < 8){
					$errors[] = "Jūsu jaunajai parolei jāsastāv no vismaz 8 rakstzīmēm!";
				}
			}
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				echo page::alert($error, "danger");
			}
		}else{
			$hash = user::hashPassword($_POST['new_password'], true);
			$db->update("UPDATE `users` SET `password` = ?, `salt` = ? WHERE `user_id` = ?", array($hash['hash'], $hash['salt'], $_SESSION['user_id']));
			header("Location: " . $c['url'] . "/login");
		}
	}
	echo page::alert("Ja paroles maiņa būs veiksmīga, Tev būs jāautorizējās atkārtoti!", "info");
	?>
	<div id="usercp" style="margin-bottom: 10px;">
		<ul>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings">Rediģēt profilu</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/avatar">Mainīt bildi</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/password">Mainīt paroli</a></li>
		</ul>
	</div>
	<div class="form smaller">
		<form method="POST">
			<input type="password" name="old_password" class="control" placeholder="Pašreizējā parole">
			<input type="password" name="new_password" class="control" placeholder="Jaunā parole">
			<input type="password" name="new_password_repeat" class="control" placeholder="Jaunā parole atkārtoti">
			<button type="submit" name="change_password" class="blue">Mainīt paroli</button>
		</form>
	</div>
	<?php
}elseif($action == "avatar"){
	$page = new page("Profila bildes maiņa", array("Iestatījumi", "Profila bildes maiņa"));
	$user = user::data($_SESSION['user_id']);
	if(isset($_POST['save'])){
		$errors = array();

		if($_POST['avatar_type'] == "custom"){
			if($_FILES['custom_avatar']['name'] == ""){
				$errors[] = "Jūs aizmirsāt izvēlēties bildi!";
			}else{
				if(getimagesize($_FILES['custom_avatar']['tmp_name']) != TRUE){
					$errors[] = "Nederīgs bildes formāts!";
				}else{
					$image_size = getimagesize($_FILES["custom_avatar"]["tmp_name"]);
					$image_width = $image_size[0];
					$image_height = $image_size[1];
					if($image_width < 200){
						$errors[] = "Bilde ir pārāk šaura. Minimālais izmērs ir: 200x200";
					}
					if($image_height < 200){
						$errors[] = "Bilde ir pārāk īsa. Minimālais izmērs ir: 200x200";
					}

				}
			}
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			if($_POST['avatar_type'] == "custom"){
				$image_info = pathinfo($_FILES['custom_avatar']['name']);
				$image = "avatar-" . $user['user_id'] . "." . $image_info['extension'];
				move_uploaded_file($_FILES['custom_avatar']['tmp_name'], $c['dir'] . "uploads/profile/" . $image);
				$db->update("UPDATE `users` SET `avatar_type` = ?, `custom_avatar` = ? WHERE `user_id` = ?", array("custom", $image, $user['user_id']));
			}else{
				if(file_exists($c['dir'] . "uploads/profile/" . $user['custom_avatar'])){
					unlink($c['dir'] . "uploads/profile/" . $user['custom_avatar']);
				}
				$db->update("UPDATE `users` SET `avatar_type` = ?, `custom_avatar` = '' WHERE `user_id` = ?", array($_POST['avatar_type'], $user['user_id']));
			}
			page::alert("Bilde veiksmīgi atjaunota!", "success");
			$user = user::data($_SESSION['user_id']);
		}
	}
	?>
	<div id="usercp" style="margin-bottom: 10px;">
		<ul>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings">Rediģēt profilu</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/avatar">Mainīt bildi</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/password">Mainīt paroli</a></li>
		</ul>
	</div>
	<div class="form smaller">
		<form method="POST" enctype="multipart/form-data">
			<center style="margin: 30px;">
				<?php echo user::returnAvatar($user['user_id']); ?>
			</center>

			<label for="avatar_type" class="required">Bildes tips</label>
			<select type="text" name="avatar_type" class="control" onChange="avatarType(this)">
				<option disabled>Izvēlies bildes tipu</option>
				<option value="default"<?php echo ($user['avatar_type'] == "default") ? " selected" : ""; ?>>Noklusējuma</option>
				<option value="custom"<?php echo ($user['avatar_type'] == "custom") ? " selected" : ""; ?>>Pielāgots</option>
				<option value="gravatar"<?php echo ($user['avatar_type'] == "gravatar") ? " selected" : ""; ?>>Gravatar</option>
			</select>

			<div id="custom" style="display: <?php echo ($user['avatar_type'] == "custom") ? "block" : "none"; ?>;">
				<label for="custom_avatar" class="required">Izvēlies bildi</label>
				<input type="file" name="custom_avatar" class="control" accept="image/*">
			</div>

			<button type="submit" name="save" class="blue">Saglabāt</button>
		</form>
	</div>
	<?php
}else{
	$page = new page("Profila rediģēšana", array("Iestatījumi", "Profila rediģēšana"));
	$user = user::data($_SESSION['user_id']);
	if(isset($_POST['save'])){
		$errors = array();

		if(empty($_POST['name'])){
			$errors[] = "Jūs aizmirsāt ievadīt savu vārdu!";
		}

		if(empty($_POST['display_name'])){
			$errors[] = "Jūs aizmirsāt izvēlēties uzrādošo vārdu!";
		}else{
			if(($user['display_name'] != $_POST['display_name']) AND !user::hasFlag("admin")){
				if($user['display_name_changes'] == 3){
					$errors[] = "Uzrādošo vārdu ir atļauts mainīt tikai 3 reizes. Jums šīs reizes jau ir iztērētas!";
				}
			}else{
				if(strlen($_POST['display_name']) > 20){
					$errors[] = "Jūsu lietotājvārds nedrīkst būt garāks par 20 rakstzīmēm!";
				}elseif(strlen($_POST['display_name']) < 4){
					$errors[] = "Jūsu lietotājvārdam jāsastāv no vismaz 4 rakstzīmēm!";
				}
			}
		}

		if(empty($_POST['birthday']) === false){
			if(page::validateDate($_POST['birthday']) === false){
				$errors[] = "Dzimšanas dienas datums ir nepareizā formātā. Tam jābūt dd/mm/yy formātā!";
			}
		}

		if(count($errors) > 0){
			foreach($errors as $error){
				page::alert($error, "danger");
			}
		}else{
			$display_name_changes = (($user['display_name'] != $_POST['display_name']) AND !user::hasFlag("admin")) ? 1 : 0;
			$db->update("UPDATE `users` SET `name` = ?, `display_name` = ?, `display_name_changes` = `display_name_changes` + ?, `seo_name` = ?, `skype` = ?, `location` = ?, `birthday` = ?, `about` = ?, `signature` = ? WHERE `user_id` = ?", array($_POST['name'], $_POST['display_name'], $display_name_changes, page::createSlug($_POST['display_name']), $_POST['skype'], $_POST['location'], $_POST['birthday'], $_POST['about'], $_POST['signature'], $user['user_id']));
			page::alert("Izmaiņas veiksmīgi saglabātas!", "success");
			$user = user::data($_SESSION['user_id']);
		}
	}
	?>
	<div id="usercp" style="margin-bottom: 10px;">
		<ul>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings">Rediģēt profilu</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/avatar">Mainīt bildi</a></li>
			<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/settings/password">Mainīt paroli</a></li>
		</ul>
	</div>
	<div class="form smaller">
		<form method="POST">
			<label for="name" class="required">Vārds</label>
			<input type="text" name="name" class="control" placeholder="Vārds" value="<?php echo $user['name']; ?>">

			<label for="display_name" class="required">Uzrādošais vārds [ Iztērētas <?php echo $user['display_name_changes']; ?> no 3 ]</label>
			<input type="text" name="display_name" class="control" placeholder="Uzrādošais vārds" value="<?php echo $user['display_name']; ?>">

			<label for="email" class="required">E-pasta adrese</label>
			<input type="text" name="email" class="control" placeholder="E-pasta adrese" value="<?php echo $user['email']; ?>" disabled>

			<label for="skype">Skype</label>
			<input type="text" name="skype" class="control" placeholder="Skype" value="<?php echo $user['skype']; ?>">

			<label for="location">Atrašanās vieta</label>
			<input type="text" name="location" class="control" placeholder="Atrašanās vieta" value="<?php echo $user['location']; ?>">

			<label for="birthday">Dzimšanas diena [ dd/mm/yy ]</label>
			<input type="text" name="birthday" class="control" placeholder="Dzimšanas diena [ dd/mm/yy ]" value="<?php echo $user['birthday']; ?>">

			<label for="about">Par mani</label>
			<textarea type="text" name="about" class="control" placeholder="Par mani" rows="7"><?php echo $user['about']; ?></textarea>

			<label for="signature">Paraksts</label>
			<textarea type="text" name="signature" id="editor" class="control" placeholder="Paraksts" rows="7"><?php echo $user['signature']; ?></textarea>

			<button type="submit" name="save" class="blue">Saglabāt</button>
		</form>
	</div>
	<?php
}