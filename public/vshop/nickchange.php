<?php $user = user::data($_SESSION['user_id']); ?>
<?php $price = 3.50; ?>
<script type="text/javascript">
	function selectIcon(icon){
		$("input#icon").val($(icon).data("name"));
		$("span#selectedIcon").html('<img class="icon" src="' + $(icon).attr("src") + '">')
	}
</script>

<legend style="text-transform: uppercase; border: 0;"><center>pakalpojuma cena: <span class="glyphicon glyphicon-euro"></span><?php echo page::returnPrice($price); ?></center></legend>
<?php if($user['display_name_changes'] == 3): ?>
	<p>Lai mainītu savu uzrādošo vārdu, seko instrukcijām zemāk.</p>
<?php else: ?>
	<p><a href="<?php $c['url']; ?>/settings">[ Iztērētas <?php echo $user['display_name_changes']; ?> no 3 bezmaksas uzrādošā vārda maiņas ]</a></p>
<?php endif; ?>
<p>
	Vispirms, izvēlies jaunu uzrādošo vārdu:
</p>
<?php
if(isset($_POST['change_name'])){
	$errors = array();

	if(empty($_POST['new_display_name'])){
		$errors[] = "Tu aizmirsi ievadīt savu jauno uzrādošo vārdu!";
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
		$db->update("UPDATE `users` SET `display_name` = ? WHERE `user_id` = ?", array($_POST['new_display_name'], $_SESSION['user_id']));
		page::logVshop(array("item" => $path[2], "price" => $price, "result" => true));
		echo page::alert("Pakalpojums veiksmīgi iegādāts. Paldies par pirkumu!", "success");
	}
}
?>
<div class="form smaller">
	<form method="POST">
		<input type="text" class="control" name="new_display_name" placeholder="Jaunais uzrādošais vārds">
		<button type="submit" name="change_name" class="blue" style="margin-top: 25px;">Mainīt uzrādošo vārdu</button>
	</form>
</div>