<?php $user = user::data($_SESSION['user_id']); ?>
<?php $price = 5.50; ?>
<?php $protected_nicknames = array("luckybeer-icon.gif", "nunce-icon.png"); ?>
<script type="text/javascript">
	function selectIcon(icon){
		$("input#icon").val($(icon).data("name"));
		$("span#selectedIcon").html('<img class="icon" src="' + $(icon).attr("src") + '">')
	}
</script>

<legend style="text-transform: uppercase; border: 0;"><center>pakalpojuma cena: <span class="glyphicon glyphicon-euro"></span><?php echo page::returnPrice($price); ?></center></legend>
<?php if(empty($user['icon'])): ?>
	<p>Tev pašlaik nav ikoniņas.</p>
<?php else: ?>
	<p>Tava ikoniņa pie Tava uzrādošā vārda izskatās šādi: <?php echo user::formatName($_SESSION['user_id'], true, true, true); ?>. Nepatīk? Tā nav problēma! Seko instrukcijām zemāk un tiec pie jaunas ikoniņas!</p>
<?php endif; ?>
<p>
	Vispirms, izvēlies jaunu ikoniņu no šī saraksta:
</p>
<?php
if(isset($_POST['change_icon'])){
	$errors = array();

	if(isset($_POST['icon']) AND !empty($_POST['icon'])){
		if(!file_exists($c['dir'] . "assets/images/icons/user/" . $_POST['icon'])){
			$errors[] = "Izvēlētā ikona neeksistē!";
		}
	}else{
		$errors[] = "Sistēma neatrada izvēlēto ikonu!";
	}

	if(user::checkMoney($_SESSION['user_id'], $price) === false){
		$errors[] = "Tev nav pietiekoši daudz naudas, lai iegādātos šo pakalpojumu!";
	}

	if(count($errors) > 0){
		foreach($errors as $error){
			echo page::alert($error, "danger");
		}
		page::logVshop(array("item" => $path[2], "price" => $price, "result" => false));
	}else{
		user::deductMoney($_SESSION['user_id'], $price);
		$db->update("UPDATE `users` SET `icon` = ? WHERE `user_id` = ?", array(strtolower($_POST['icon']), $_SESSION['user_id']));
		page::logVshop(array("item" => $path[2], "price" => $price, "result" => true));
		echo page::alert("Pakalpojums veiksmīgi iegādāts. Paldies par pirkumu!", "success");
	}
}
?>
<div class="form smaller">
	<form method="POST">
		<input type="hidden" name="icon" id="icon">
		<center style="width: 160px; margin: 0 auto;">
			<?php
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($c['dir'] . "assets/images/icons/user")) as $filename){
				if($filename->isDir()) continue;
				$filename = str_replace($c['dir'] . "assets/images/icons/user/", "", $filename);
				if(!in_array($filename, $protected_nicknames)){
					echo '<img class="icon pointer" style="padding: 5px;" src="' . $c['url'] . '/assets/images/icons/user/' . $filename . '" data-name="' . $filename . '" onClick="selectIcon(this)">';
				}
			}
			?>
		</center>
		<legend style="text-transform: uppercase; border: 0; margin-top: 20px;"><center>izvēlētā ikona</center></legend>
		<div style="width: 100px; margin: 0 auto;"><span id="selectedIcon"></span><?php echo user::formatName($_SESSION['user_id'], false, true, true); ?></div>
		<button type="submit" name="change_icon" class="blue" style="margin-top: 25px;">Mainīt ikonu</button>
	</form>
</div>