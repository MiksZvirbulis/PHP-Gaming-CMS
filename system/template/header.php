<!--
Esi sveicināts Source Kodā! Es neiebilstu, ka Tu vēlies paņemt kādu izskatīgu elementu no šīs burvīgās mājas lapas, bet atceries, ka Tava mājas lapa neizskatīsies labāk, ja uz tās stāvēs zagti mājas lapas elementi. :)
Sistēmu, mājas lapu veidoja Miks "LuckyBeer" Zvirbulis. Dizainu, protams, mūsu speciālists Aigars "grans." Leicis.
Redzi kļūdu? Raksti man Skype: miksrolands
-->
<!DOCTYPE html>
<html lang="lv">
<head>
	<meta charset="utf-8">
	<title><?php echo $this->page; ?> - <?php echo $c['page']['title']; ?></title>
	<!-- SEO -->
	<meta name="description" content="POISE.LV ir vēl jauns spēļu portāls, kurš piedāvā Counter-Strike un Minecraft spēļu serverus. Izklaidējoši konkursi un turnīri, papildus ar dažādām balvām sākot no VIP, spēļu kuponiem līdz pat nauads summām!" />
	<meta name="keywords" content="Counter-Strike, CounterStrike, CS, Counter-Strike 1.6, CounterStrike 1.6, CS 1.6, spēles, portāls, komūns, forums, turnīrs, turnīri, konkurss, konkursi, izklaides, balvas, VIP, sms, veikals, Minecraft, MC, spēļu portāls, foruma spēles, frakcijas, būvniecība, laikraksti, GFX, Photoshop, Movie Making, demo, tirgus, klans, clan, duelis, Deathrun, deathrun, Surf, surf, dd2, Dust2Land, dust2land, dd2 serveris, surf serveris, deathrun serveris, minecraft serveris" />
	<meta http-equiv="Classification" content="Spēļu portāls" />
	<meta name="classification" content="Spēļu portāls" />
	<meta name="pagerank" content="10" />
	<meta name="ROBOTS" content="INDEX,FOLLOW" />
	<meta name="googlebot" content="all, index, follow" />
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/style2.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/fancybox.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/editor.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $c['url']; ?>/assets/css/bootstrap.colorpickersliders.min.css">
	<link rel="shortcut icon" href="<?php echo $c['url']; ?>/favicon.ico" />
	<link type="favicon" href="<?php echo $c['url']; ?>/favicon.ico">
</head>
<body>
	<div id="container">
		<div id="header">
			<div id="nav">
				<ul>
					<li><a href="<?php echo $c['url']; ?>/"><span class="glyphicon glyphicon-home"></span> Sākums</a></li>
					<li><a href="<?php echo $c['url']; ?>/forum"><i class="fa fa-comments"></i> Forums</a></li>
					<li><a href="<?php echo $c['url']; ?>/users"><i class="fa fa-users"></i> Lietotāji</a></li>
					<li><a href="<?php echo $c['url']; ?>/videos"><i class="fa fa-film"></i> Video <b class="caret"></b></a>
						<ul>
							<li><a href="<?php echo $c['url']; ?>/videos"> &#8594; Saraksts</a></li>
							<?php if(user::isLoggedIn()): ?>
								<li><a href="<?php echo $c['url']; ?>/videos/add"> &#8594; Pievienot</a></li>
							<?php endif; ?>
						</ul>
					</li>
					<li><a href="<?php echo $c['url']; ?>/banlist"><i class="fa fa-ban"></i> Banliste<b class="caret"></b></a>
						<ul>
							<li><a href="<?php echo $c['url']; ?>/banlist"> &#8594; Saraksts</a></li>
							<li><a href="<?php echo $c['url']; ?>/amx"> &#8594; AMX Darbības</a></li>
							<li><a href="<?php echo $c['url']; ?>/amx/punished"> &#8594; Sodītie admini</a></li>
						</ul>
					</li>
					<li><a><i class="fa fa-bar-chart"></i> Stati <b class="caret"></b></a>
						<ul>
							<li><a href="<?php echo $c['url']; ?>/" target="_blank"> &#8594; CS 1.6</a></li>
							<li><a href="<?php echo $c['url']; ?>/" target="_blank"> &#8594; CS:GO</a></li>
						</ul>
					</li>

					<li><a href="<?php echo $c['url']; ?>/shop" style="color: orange;"><i class="fa fa-shopping-cart"></i> Veikals <b class="caret"></b></a>
						<ul>
							<li><a href="<?php echo $c['url']; ?>/"> &#8594; SMS</a></li>
							<li><a href="<?php echo $c['url']; ?>/vshop"> &#8594; Virtuālais</a></li>
						</ul>
					</li>
					<li><a href="<?php echo $c['url']; ?>/about"><i class="fa fa-info-circle"></i> Par Mums <b class="caret"></b></a>
						<ul>
							<li><a href="<?php echo $c['url']; ?>/rules"> &#8594; Noteikumi</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="load"><span class="glyphicon glyphicon-refresh"></span> Lapa tiek ielādēta...</div>
		</div>
		<div id="content">
			<div id="usercp">
				<?php if(user::isLoggedIn()): ?>
					<?php $user = user::data($_SESSION['user_id']); ?>
					<ul>
						<li><a href="<?php echo $c['url']; ?>/settings/avatar"><?php echo user::returnAvatar($_SESSION['user_id'], false, 32, 32, false); ?></a></li>
						<li><?php echo user::formatName($_SESSION['user_id'], false, true); ?> (<?php echo user::returnGroup($_SESSION['user_id']); ?>)</li>
						<li><a href="<?php echo $c['url']; ?>/settings"><span class="glyphicon glyphicon-cog"></span></a></li>
						<li><a href="<?php echo $c['url']; ?>/messages"><span class="glyphicon glyphicon-envelope" id="checkMessages"></span></a></li>
						<!--<li><a href="#"><span class="glyphicon glyphicon-bell"></span></a></li>-->
						<?php if(user::hasFlag("mod")): ?>
							<li><a href="<?php echo $c['url']; ?>/acp" style="color: red;">KP</a></li>
						<?php endif; ?>
						<?php if(user::hasFlag("other")): ?>
							<li><a href="<?php echo $c['url']; ?>/amx/admins" style="color: orange;">BT</a></li>
						<?php endif; ?>
						<li><a href="<?php echo $c['url']; ?>/logout"><span class="glyphicon glyphicon-off" style="color: #ccc;"></span></a></li>
					</ul>

					<div class="right">
						<div class="labels">
							<span class="label label-default" style="background: #333;"><span class="glyphicon glyphicon-pencil"></span> <?php echo user::returnStatistics($_SESSION['user_id'], "posts"); ?></span>
							<span class="label label-default" style="background: #333;"><span class="glyphicon glyphicon-euro"></span> <?php echo $userD->money; ?></span>
						</div>
					</div>
				<?php else: ?>
					<ul>
						<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/login/?return=<?php echo $c['full_url']; ?>">Ienākt savā profilā</a></li>
						<li style="margin-left: 10px;"><a href="<?php echo $c['url']; ?>/register">Reģistrēt jaunu kontu</a></li>
					</ul>
				<?php endif; ?>
			</div>
			<div class="full">
				<div class="navigation">
					<div class="left">
						<ul class="breadcrumbs">
							<li class="first home"><a><?php echo $c['page']['title']; ?></a></li>
							<?php
							$countBreadcrumbs = count($this->_breadcrumbs);
							$i = 0;
							?>
							<?php foreach($this->_breadcrumbs as $breadcrumb): ?>
								<?php if(++$i === $countBreadcrumbs): ?>
									<li class="last"><a><?php echo $breadcrumb[0]; ?></a></li>
								<?php else: ?>
									<li><a><?php echo $breadcrumb[0]; ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					</div>
					<div class="right">
						<form method="POST" action="<?php echo $c['url']; ?>/search">
							<input autocomplete="off" type="text" name="query" placeholder="Ko vēlies atrast?" style="text-indent: 5px;">
							<button id="search_button" type="submit"><span class="glyphicon glyphicon-search"></span></button>
						</form>
					</div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="info_bar" style="margin-bottom: 10px;">
				<div class="text pull-left">...</div>
				<small><div class="pull-right updated"></div></small>
				<div class="clear"></div>
			</div>
			<?php if($this->side === true): ?>
				<div class="news">
				<?php endif; ?>
				<?php if($path[1] == "forum"): ?>
					<div class="panel panel-default" style="margin: 0;">
						<div class="panel-body chat_box">
							<div class="chat_box_scroll" id="shoutbox"></div>
						</div>
					</div>
					<?php if(user::isLoggedIn()): ?>
						<div class="form" id="addShout">
							<div class="alert alert-danger" style="display: none;" id="shoutError"></div>
							<form method="POST" id="addShout">
								<input type="text" class="control" name="shout" autocomplete="off" placeholder="Tava ziņa...">
								<button type="submit" class="blue">Pievienot</button>
								<button type="button" class="blue" onClick="loadShouts()">Ielādēt</button>
								<button type="button" class="blue" onClick="toggleEmoticons()">Smaidiņi</button>
								<span id="emoticonBox">
									<?php foreach(text::$emoticons as $emoticon => $image): ?>
										<?php
										$extensions = array(".png", ".gif");
										foreach($extensions as $extension){
											if(file_exists($c['dir'] . "assets/images/emoticons/" . $image . $extension)){
												$display = true;
												$return_ext = $extension;
												break;
											}else{
												$display = false;
											}
										}
										?>
										<?php if($display === true): ?>
											<span class="smile">
												<img class="pointer" src="<?php echo $c['url']; ?>/assets/images/emoticons/<?php echo $image . $extension; ?>" onClick="addEmoticon('<?php echo $emoticon; ?>')">
											</span>
										<?php endif; ?>
									<?php endforeach; ?>
								</span>
							</form>
						</div>
					<?php endif; ?>
				<?php endif; ?>