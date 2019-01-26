<?php $page = new page("Noteikumi", array("Noteikumi")); ?>
<table class="table table-bordered">
	<tbody>
		<?php $rules = $db->fetchAll("SELECT * FROM `rules` ORDER BY `title` ASC"); ?>
		<?php foreach($rules as $rule): ?>
			<tr class="pointer" onClick="loadRules(<?php echo $rule['id']; ?>)">
				<td width="40%"><?php echo $rule['title']; ?></td>
				<td width="60%">Pēdējo reizi atjaunoja <?php echo user::formatName($rule['last_updated_by'], true, true, true); ?> <?php echo page::formatTime($rule['last_updated']); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<div id="rules" style="margin-top: 10px;"></div>