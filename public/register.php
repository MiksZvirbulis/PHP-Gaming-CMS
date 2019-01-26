<?php
$page = new page("Reģistrācija", array("Reģistrācija"));
if(user::isLoggedIn()){
	page::alert("Jūs jau esat autorizēts!", "info");
}else{
	?>
	<div class="form smaller">
		<?php
		if(isset($_POST['register'])){
			$errors = array();

			if(empty($_POST['name'])){
				$errors[] = "Jūs aizmirsāt ievadīt savu vārdu!";
			}

			if(empty($_POST['username'])){
				$errors[] = "Jūs aizmirsāt izvēlēties lietotājvārdu!";
			}else{
				$find_username = $db->count("SELECT `user_id` FROM `users` WHERE `username` = ?", array($_POST['username']));
				if($find_username == 0){
					if(strlen($_POST['username']) > 16){
						$errors[] = "Jūsu lietotājvārds nedrīkst būt garāks par 16 rakstzīmēm!";
					}elseif(strlen($_POST['username']) < 4){
						$errors[] = "Jūsu lietotājvārdam jāsastāv no vismaz 4 rakstzīmēm!";
					}elseif(user::usernameIsValid($_POST['username']) == false){
						$errors[] = "Jūsu izvēlētais lietotājvārds neatbilst rakstzīmju noteikumiem!";
					}
				}else{
					$errors[] = "Jūsu izvēlētais lietotājvārds jau ir reģistrēts!";
				}
			}

			if(empty($_POST['display_name'])){
				$errors[] = "Jūs aizmirsāt izvēlēties uzrādošo vārdu!";
			}else{
				if(strlen($_POST['display_name']) > 20){
					$errors[] = "Jūsu lietotājvārds nedrīkst būt garāks par 20 rakstzīmēm!";
				}elseif(strlen($_POST['display_name']) < 4){
					$errors[] = "Jūsu lietotājvārdam jāsastāv no vismaz 4 rakstzīmēm!";
				}
			}

			if(empty($_POST['email'])){
				$errors[] = "Jūs aizmirsāt ievadīt savu e-pasta adresi!";
			}else{
				$find_email = $db->count("SELECT `user_id` FROM `users` WHERE `email` = ?", array($_POST['email']));
				if($find_email == 0){
					if(user::emailIsValid($_POST['email']) == false){
						$errors[] = "Jūsu norādītais e-pasts nav sastādīts pareizi!";
					}
				}else{
					$errors[] = "Jūsu norādītā e-pasta adrese jau ir reģistrēta!";
				}
			}

			if(empty($_POST['password'])){
				$errors[] = "Jūs aizmirsāt izvēlēties paroli!";
			}else{
				if($_POST['password'] != $_POST['repeat_password']){
					$errors[] = "Jūsu izvēlētās paroles nesakrīt!";
				}else{
					if(strlen($_POST['password']) < 8){
						$errors[] = "Jūsu parolei jāsastāv no vismaz 8 rakstzīmēm!";
					}
				}
			}

			if(empty($_POST['captcha'])){
				$errors[] = "Tu aizmirsi ievadīt captcha pārbaudes kodu!";
			}else{
				if($_SESSION['captcha_code'] != $_POST['captcha']){
					$errors[] = "Ievadītais captcha kods nesakrīt ar to, kas ir redzams bildē!";
				}
			}

			if(count($errors) > 0){
				foreach($errors as $error){
					page::alert($error, "danger");
				}
			}else{
				$hash = user::hashPassword($_POST['password'], true);
				$db->insert(
					"INSERT INTO `users` (`name`, `username`, `display_name`, `seo_name`, `avatar_type`, `registration_date`, `email`, `password`, `salt`, `group_id`) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
					array(trim($_POST['name']), trim($_POST['username']), $_POST['display_name'], page::createSlug($_POST['display_name']), "default", time(), trim($_POST['email']), $hash['hash'], $hash['salt'], 1)
					);
				page::addShout("Poise.lv reģistrējās jauniņais! Esi sveicināts, @" . $_POST['display_name'] . "!");
				page::alert("Reģistrācija noritēja veiksmīgi!", "success");
			}
		}
		?>
		<form method="POST">
			<input type="text" name="name" class="control" placeholder="Vārds" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ""; ?>">
			<input type="text" name="username" class="control" placeholder="Lietotājvārds" value="<?php echo isset($_POST['username']) ? $_POST['username'] : ""; ?>">
			<input type="text" name="display_name" class="control" placeholder="Uzrādošais vārds" value="<?php echo isset($_POST['display_name']) ? $_POST['display_name'] : ""; ?>">
			<input type="text" name="email" class="control" placeholder="E-pasta adrese" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ""; ?>">
			<input type="password" name="password" class="control" placeholder="Parole">
			<input type="password" name="repeat_password" class="control" placeholder="Parole atkārtoti">
			<div style="text-align: center;"><img src="<?php $c['url']; ?>/public/ajax/captcha.php"></div>
			<input type="text" name="captcha" class="control" placeholder="Kods">
			<button type="submit" name="register" class="blue">Reģistrēties</button>
		</form>
	</div>
	<?php
}