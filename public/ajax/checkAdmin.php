<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
if(isset($_POST['username']) AND isset($_POST['password'])){
	$find_admin = $db->fetch("SELECT * FROM `amx_amxadmins` WHERE `username` = ? AND `password` = ?", array($_POST['username'], $_POST['password']));
	if(empty($find_admin)){
		page::alert("Vai nu admins neeksistē, vai Tu esi ievadījis kļūdainu informāciju!", "danger");
	}else{
		?>
		<table class="table table-bordered">
			<tbody>
				<tr>
					<td>Spēlētāja vārds</td>
					<td><?php echo $find_admin['username']; ?></td>
				</tr>
				<tr>
					<td>SteamID / IP / Vārds</td>
					<td><?php echo $find_admin['steamid']; ?></td>
				</tr>
				<tr>
					<td>Tips</td>
					<td><strong><?php echo $find_admin['flags']; ?></strong></td>
				</tr>
				<tr>
					<?php
					$vip = (strpos($find_admin['access'], "t") !== false) ? "<span class='orange'>VIP</span>" : "";
					$admin = (strpos($find_admin['access'], "z") !== false) ? "" : "<span class='green'>ADMIN</span>";
					?>
					<td>Pieejas flagi</td>
					<td><strong><?php echo $find_admin['access']; ?></strong> ( <?php echo $vip . $admin; ?> )</td>
				</tr>
				<tr>
					<?php $servers = $db->fetchAll("SELECT `server_id` FROM `amx_admins_servers` WHERE `admin_id` = ?", array($find_admin['id'])); ?>
					<td>Pieejas serveri</td>
					<td>
						<?php if(empty($servers)): ?>
							<b>Tu neesi reģistrēts nevienā serverī!</b>
						<?php else: ?>
							<?php foreach($servers as $server): ?>
								<?php $info = $db->fetch("SELECT `hostname` FROM `amx_serverinfo` WHERE `id` = ?", array($server['server_id'])); ?>
								<?php $server_info[] = $info['hostname']; ?>
							<?php endforeach; ?>
							<?php echo "<i>" . implode(", ", $server_info) . "</i>"; ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>Termiņš</td>
					<td>
						<?php
						if($find_admin['expired'] == 0){
							$left = "<span class='orange'>Neierobežots termiņš</span>";
						}elseif(time() > $find_admin['expired']){
							$left = "<span style='color: red;'>Termiņš ir beidzies</span>";
						}else{
							$difference = time() - $find_admin['expired'];
							$expires = abs(floor($difference / (60 * 60 * 24)));
							echo ($expires == 1) ? "<span class='green'>Atlikusi " . $expires . " diena</span>" : "<span class='green'>Atlikušas " . $expires . " dienas</span>";
							$left = "( " . date("d/m/Y H:i", $find_admin['created']) . " - " . date("d/m/Y H:i", $find_admin['expired']) . " )";
						}
						?>
						<?php echo $left; ?>
					</tr>
				</tbody>
			</table>
			<?php
		}
	}else{
		exit;
	}