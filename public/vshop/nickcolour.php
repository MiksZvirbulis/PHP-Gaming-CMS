<?php $user = user::data($_SESSION['user_id']); ?>
<?php $price = 3.50; ?>
<legend style="text-transform: uppercase; border: 0;"><center>pakalpojuma cena: <span class="glyphicon glyphicon-euro"></span><?php echo page::returnPrice($price); ?></center></legend>
<p>Lai mainītu sava uzrādošā vārda krāsu, seko instrukcijām zemāk.</p>
<p>
	Vispirms, izvēlies krāsu:
</p>
<div class="form smaller">
	<form method="POST">
		<center>
			<input type="text" class="control" name="new_nick_colour" value="#adff2f" data-color-format="hex" placeholder="HEX Krāsas kods" id="smallhsv" style="width: 150px; box-shadow: none;">
		</center>

		<legend style="text-transform: uppercase; border: 0; margin-top: 20px;"><center>izvēlētā krāsa</center></legend>
		<div style="width: 100px; margin: 0 auto;"><span id="selectedColour"><?php echo user::formatName($_SESSION['user_id'], false, false, false); ?></span></div>

		<button type="submit" name="change_colour" class="blue" style="margin-top: 25px;">Mainīt krāsu</button>
	</form>
</div>