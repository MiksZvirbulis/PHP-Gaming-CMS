<?php
$page = new page("Autorizācija", array("Autorizācija"));
if(user::isLoggedIn()){
	page::alert("Tu nevari autorizēties atkārtoti!", "info");
}else{
	if(isset($path[2]) AND $path[2] == "forgot" AND !isset($path[3])){
		if(isset($_POST['forgot'])){
			$errors = array();

			if(empty($_POST['username'])){
				$errors[] = "Tu aizmirsi ievadīt savu lietotājvārdu!";
			}

			if(empty($_POST['email'])){
				$errors[] = "Tu aizmirsi ievadīt savu e-pasta adresi!";
			}

			if(!empty($_POST['email']) AND !empty($_POST['username'])){
				$checkUser = $db->count("SELECT `user_id` FROM `users` WHERE `username` = ? AND `email` = ?", array($_POST['username'], $_POST['email']));
				if($checkUser == 0){
					$errors[] = "Šāds lietotājs netika atrasts sistēmā!";
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
					echo page::alert($error, "danger");
				}
			}else{
				$reset_key = md5($_POST['username'] . time());
				$db->update("UPDATE `users` SET `reset_key` = ? WHERE `username` = ?", array($reset_key, $_POST['username']));
				echo page::alert("Lūdzu pārbaudi e-pastu, kurā atradīsi saiti, lai izveidotu jaunu paroli. Tikai atceries, ka saite būs valīda tikai uz 15 minūtēm!", "success");
				$message = "<html><body>";
				$message .= "Sveiks, " . $_POST['username'] . "<br />";
				$message .= "Saprotam, ka esi pazaudējis savu paroli, bet tā nav problēma. Lūdzu lieto saiti, kas atrodama zemāk, lai izveidotu jaunu paroli, kuru Tu atcerēsies vieglāk!<br /><br />";
				$message .= "<a href='" . $c['url'] . "/login/forgot/{$reset_key}'>Spied šeit!</a><br /><br />";
				$message .= "Ar cieņu,<br />";
				$message .= "Poise.lv administrācija";
				$message .= "</html></body>";
				page::sendEmail($_POST['email'], "no-reply@poise.lv", $_POST['username'] . " no poise.lv aizmirsa paroli!", $message);
			}
		}
		?>
		<div class="form smaller">
			<form method="POST">
				<input type="text" name="username" class="control" placeholder="Lietotājvārds">
				<input type="text" name="email" class="control" placeholder="E-pasta adrese">
				<div style="text-align: center;"><img src="<?php $c['url']; ?>/public/ajax/captcha.php"></div>
				<input type="text" name="captcha" class="control" placeholder="Kods">
				<button type="submit" name="forgot" class="blue">Aizmirsu paroli</button>
			</form>
		</div>
		<?php
	}elseif(isset($path[2], $path[3]) AND $path[2] == "forgot" AND (strlen($path[3]) == 32)){
		$findUser = $db->fetch("SELECT `user_id` FROM `users` WHERE `reset_key` = ?", array($path[3]));
		if(empty($findUser)){
			echo page::alert("Kļūda! Lietotājs netika atrasts!", "danger");
		}else{
			if(isset($_POST['change_password'])){
				$errors = array();

				if(empty($_POST['new_password'])){
					$errors[] = "Tu aizmirsi izvēlēties savu jauno paroli!";
				}

				if(empty($_POST['new_password_repeated'])){
					$errors[] = "Tu aizmirsi ievadīt savu jauno paroli atkārtoti!";
				}

				if($_POST['new_password'] != $_POST['new_password_repeated']){
					$errors[] = "Jaunā parole nesakrīt!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						echo page::alert($error, "danger");
					}
				}else{
					$hash = user::hashPassword($_POST['new_password'], true);
					$db->update("UPDATE `users` SET `password` = ?, `salt` = ?, `reset_key` = '' WHERE `user_id` = ?", array($hash['hash'], $hash['salt'], $findUser['user_id']));
					echo page::alert("Parole veiksmīgi nomainīta!<br />Pārmetam uz autorizācijas lapu...", "success");
					page::redirectTo("login", array("external" => false, "time" => 3));
				}
			}
			echo page::alert("Izvēlies jaunu paroli zemāk.", "info");
			?>
			<div class="form smaller">
				<form method="POST">
					<input type="password" name="new_password" class="control" placeholder="Jaunā parole">
					<input type="password" name="new_password_repeated" class="control" placeholder="Jaunā parole atkārtoti">
					<button type="submit" name="change_password" class="blue">Mainīt paroli</button>
				</form>
			</div>
			<?php
		}
	}else{
		?>
		<div class="form smaller">
			<?php
			if(isset($_POST['login'])){
				$errors = array();

				if(empty($_POST['username'])){
					$errors[] = "Jūs aizmirsāt ievadīt savu lietotājvārdu!";
				}

				if(empty($_POST['password'])){
					$errors[] = "Jūs aizmirsāt ievadīt savu paroli!";
				}

				$get_salt = $db->fetch("SELECT `salt` FROM `users` WHERE `username` = ?", array($_POST['username']));
				$salt = $get_salt['salt'];
				$hash = user::hashPassword($_POST['password'], false, $salt);
				$test_login = $db->count("SELECT `user_id` FROM `users` WHERE `username` = ? AND `password` = ?", array($_POST['username'], $hash['hash']));
				if($test_login == 0){
					$errors[] = "Ievadītais lietotājvārds vai parole ir nepareizi ievadīta vai neeksistē mūsu sistēmā!";
				}

				if(count($errors) > 0){
					foreach($errors as $error){
						page::alert($error, "danger");
					}
				}else{
					$user = $db->fetch("SELECT `user_id`, `ip_address_list` FROM `users` WHERE `username` = ? AND `password` = ?", array($_POST['username'], $hash['hash']));
					$ip_address_list = (empty($user['ip_address_list'])) ? array() : explode(",", $user['ip_address_list']);
					if(!in_array(user::getIP(), $ip_address_list)){
						$ip_address_list[] = user::getIP();
						$new_ip_adderss_list = implode(", ", $ip_address_list);
						$db->update("UPDATE `users` SET `ip_address_list` = ? WHERE `user_id` = ?", array($new_ip_adderss_list, $user['user_id']));
					}
					$_SESSION['user_id'] = $user['user_id'];
					$_SESSION['password'] = $hash['hash'];
					if(isset($_GET['return']) AND !empty($_GET['return'])){
						page::redirectTo($_GET['return'], array("external" => true, "time" => false));
					}else{
						page::redirectTo($c['page']['default'], array("external" => false, "time" => false));
					}
				}
			}
			?>
			<form method="POST">
				<input type="text" name="username" class="control" placeholder="Lietotājvārds">
				<input type="password" name="password" class="control" placeholder="Parole">
				<button type="submit" name="login" class="blue">Autorizēties</button>
			</form>
			<a href="<?php $c['url']; ?>/login/forgot">Varbūt esi aizmirsis paroli? Neuztraucies. Spied šeit!</a>
		</div>
		<?php
	}
}