<div style="width: 100%; margin-left: 0; margin-bottom: 10px;">
	<div class="ctext" style="text-align: center;">
		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-info-sign"></span> Jaunumi <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/news/add">Pievienot</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/news/list">Saraksts</a></li>
				<li class="divider"></li>
				<li class="dropdown-header"><strong>Informācija</strong></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/info/add">Pievienot</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/info/list">Saraksts</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/reports">Kļūdu Reporti</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-user"></span> Lietotāji <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/users/list">Saraksts</a></li>
				<li class="divider"></li>
				<li class="dropdown-header"><strong>Grupas</strong></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/groups">Grupas</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/groups/add">Pievienot grupu</a></li>
			</ul>
		</div>

		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-list-alt"></span> Forums <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/forums/list">Foruma izkārtojums</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/forums/add/cat">Pievienot kategoriju</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/forums/add">Pievienot sadaļu</a></li>
				<?php if(user::hasFlag("admin")): ?>
					<li class="divider"></li>
					<li class="dropdown-header"><strong>Saturs</strong></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/forums/deleted">Dzēstais saturs</a></li>
				<?php endif; ?>
			</ul>
		</div>

		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-file"></span> Statiskās Lapas <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/pages/list">Saraksts</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/pages/add">Pievienot lapu</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-list"></span> Noteikumi <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/rules">Saraksts</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/rules/add">Pievienot</a></li>
			</ul>
		</div>
		<div class="btn-group">
			<button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
				<span class="glyphicon glyphicon-list"></span> Turnīri <span class="caret"></span>
			</button>
			<ul class="dropdown-menu">
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/tournaments">Saraksts</a></li>
				<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $c['url']; ?>/acp/tournaments/add">Pievienot</a></li>
			</ul>
		</div>
	</div>
</div>
<div class="alert alert-danger acpinfo" style="display: none;"></div>