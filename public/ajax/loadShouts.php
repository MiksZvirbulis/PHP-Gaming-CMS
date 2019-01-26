<?php
session_start();
define("IN_SYSTEM", true);
include $_SERVER['DOCUMENT_ROOT'] . "/system/config.inc.php";
?>
<?php if(isset($_POST['safe']) AND $_POST['safe']): ?>
	<?php $find_shouts = $db->count("SELECT `id` FROM `shoutbox`"); ?>
	<?php if($find_shouts == 0): ?>
		<?php page::alert("Čatā nav neviena ieraksta", "info"); ?>
	<?php else: ?>
		<?php $shouts = $db->fetchAll("SELECT * FROM `shoutbox` ORDER BY `time` DESC LIMIT ?", array(30)); ?>
		<?php foreach($shouts as $shout): ?>
			<?php $user = user::data($shout['user_id']); ?>
			<div class="chtmsg">
				<table class="chat">
					<tr>
						<td width="21px" class="uAvatar"><?php echo user::returnAvatar($shout['user_id'], false, 21, 21); ?></td>
						<td width="100px">
							<?php if($shout['user_id'] > 0): ?>
								<a class="pointer" onClick="shoutAt('<?php echo user::formatName($shout['user_id'], false, false); ?>')" >@</a> <?php echo user::formatName($shout['user_id'], true, true, true, 10); ?></td>
							<?php else: ?>
								@ Informācija
							<?php endif; ?>
							<td width="1px">:</td>
							<td><?php echo text::bbcode($shout['shout'], array("bbcode" => true, "emoticons" => true, "media" => false)); ?></td>
							<td width="150px" class="txt-right">(<?php echo date("d/m/Y H:i", $shout['time']); ?>)</td>
							<?php if(user::hasFlag("mod")): ?>
								<td width="22px" class="text-center"><span class="glyphicon glyphicon-remove pointer" title="Delete Post" onClick="deleteShout(<?php echo $shout['id']; ?>)"></span></td>
							<?php endif; ?>
						</tr>
					</table>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php else: ?>
		<?php page::redirectTo("news"); ?>
	<?php endif; ?>