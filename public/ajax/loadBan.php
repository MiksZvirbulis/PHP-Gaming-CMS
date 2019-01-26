<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(isset($_POST['bid'])){
	$find_ban = $db->count("SELECT `bid` FROM `amx_bans` WHERE `bid` = ?", array($_POST['bid']));
	if($find_ban == 0){
		echo page::alert("Bans netika atrasts!", "danger");
	}else{
		$ban = $db->fetch("SELECT * FROM `amx_bans` WHERE `bid` = ?", array($_POST['bid']));
		?>
		<table class="table table-bordered">
			<tbody class="data">
				<tr>
					<td>Bana ID</td>
					<td><strong><?php echo $ban['bid']; ?></strong></td>
				</tr>
				<tr>
					<td>Spēlētājs</td>
					<td><?php echo $ban['player_nick']; ?></td>
				</tr>
				<tr>
					<td>IP Adrese</td>
					<td><?php echo $ban['player_ip']; ?></td>
				</tr>
				<tr>
					<td>Administrators</td>
					<td><?php echo $ban['admin_nick']; ?></td>
				</tr>
				<tr>
					<td>Iemesls</td>
					<td><?php echo $ban['ban_reason']; ?></td>
				</tr>
				<tr>
					<td>Laiks</td>
					<td><?php echo date("d/m/Y H:i", $ban['ban_created']); ?></td>
				</tr>
				<tr>
					<td>Ilgums</td>
					<td>
					<?php
					if($ban['ban_length'] == 0){
						$left = "Mūžīgs";
					}elseif($ban['ban_length'] < 0){
						$left = "Bans noņemts";
					}else{
						$left = $ban['ban_length'] . " minūtes";
					}
					echo $left;
					?>
					</td>
				</tr>
				<tr>
					<td>Beigsies</td>
					<td>
						<?php
						$ban_length = $ban['ban_created'] + ($ban['ban_length'] * 60);
						if($ban['ban_length'] == 0){
							$ban_end = "Bans nebeigsies";
						}elseif(date("U") >= $ban_length){
							$ban_end = "Bans ir beidzies";
						}else{
							$ban_end = date("d/m/Y H:i", $ban_length);
						}
						echo $ban_end;
						?>
					</td>
				</tr>
				<tr>
					<td>Serveris</td>
					<td><?php echo $ban['server_name']; ?></td>
				</tr>
				<tr>
					<td>Saite</td>
					<td><input type="text" style="margin: 0;" onClick="selectAll(this)" value="<?php echo $c['url']; ?>/banlist/<?php echo $ban['bid']; ?>" readonly></td>
				</tr>
				<?php if(user::hasFlag("other") AND $ban['ban_length'] >= 0): ?>
					<tr>
						<td>Darbības</td>
						<td><div class="form"><button type="button" class="red" onClick="deleteBan(<?php echo $ban['bid']; ?>)" style="float: left;" onClick="return alert('Vai tiešām vēļies noņemt šo banu?')">Noņemt banu</button></div></td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}
}else{
	exit;
}