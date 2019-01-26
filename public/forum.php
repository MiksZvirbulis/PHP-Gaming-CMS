<?php

if(isset($path[2]) AND !empty($path[2])){

	$action = $path[2];

}else{

	$action = "main";

}



if($action == "new"){

	if(isset($path[3]) AND !empty($path[3])){

		if(user::isLoggedIn()){

			$request = $path[3];

			$explode = explode("-", $request);

			$remove_id = array_slice($explode, 1);

			$title_seo = implode("-", $remove_id);

			$cat_id = $explode[0];



			$find_cat = $db->count("SELECT `id` FROM `forums` WHERE `title_seo` = ? AND `id` = ?", array($title_seo, $cat_id));

			if($find_cat == 0){

				$page = new page("Sadaļa netika atrasta", array("Forums", "Sadaļa netika atrasta"), false);

				page::alert("Sadaļa netika atrasta!", "danger");

			}else{

				$cat = $db->fetch("SELECT `title`, `allowed_groups` FROM `forums` WHERE `id` = ?", array($cat_id));

				$page = new page("Jauna tēma", array($cat['title']), false);



				$user = user::data($_SESSION['user_id']);

				$allowed_groups = explode(",", $cat['allowed_groups']);

				if(in_array($user['group_id'], $allowed_groups)){

					if(user::isLoggedIn()){

						if(isset($_POST['new'])){

							$errors = array();



							if(empty($_POST['title'])){

								$errors[] = "Tu aizmirsi izvēlēties tēmas nosaukumu!";

							}else{

								if(strlen($_POST['title']) < 5){

									$errors[] = "Tēmas nosaukumam jābūt vismaz 5 rakstzīmju garumā!";

								}

							}



							if(empty($_POST['topic'])){

								$errors[] = "Tu aizmirsi izvēlēties tekstu savā tēmā!";

							}else{

								if(strlen($_POST['topic']) < 55){

									$errors[] = "Tēmas tekstam jābūt vismaz 55 rakstzīmju garumā!";

								}

							}



							if(count($errors) > 0){

								foreach($errors as $error){

									page::alert($error, "danger");

								}

							}else{

								$db->insert(

									"INSERT INTO `topics` (`title`, `title_seo`, `forum_id`, `author_id`, `approved`, `topic_date`, `topic`, `last_poster_id`, `last_post_date`, `state`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 

									array(

										$_POST['title'],

										text::seostring($_POST['title']),

										$cat_id,

										$_SESSION['user_id'],

										1,

										time(),

										$_POST['topic'],

										$_SESSION['user_id'],

										time(),

										"open"

										));

								$new_topic = $db->fetch("SELECT `tid`, `topic_date`, `title_seo` FROM `topics` ORDER BY `tid` DESC LIMIT 1");

								$db->update("UPDATE `forums` SET `last_poster_id` = ?, `last_post_date` = ?, `last_topic_id` = ? WHERE `id` = ?", array($_SESSION['user_id'], $new_topic['topic_date'], $new_topic['tid'], $cat_id));

								$forum = $db->fetch("SELECT `parent_id` FROM `forums` WHERE `id` = ?", array($cat_id));

								$db->update("UPDATE `forums` SET `last_poster_id` = ?, `last_post_date` = ?, `last_topic_id` = ? WHERE `id` = ?", array($_SESSION['user_id'], $new_topic['topic_date'], $new_topic['tid'], $forum['parent_id']));

								user::addMoney($_SESSION['user_id'], "0.10");

								page::addShout("Es izveidoju jaunu tēmu " . $_POST['title'] . ". Izlasi to šeit: " . $c['url'] . "/forum/topic/" . $new_topic['tid'] . "-" . $new_topic['title_seo'], $_SESSION['user_id']);

								header("Location: " . $c['url'] . "/forum/topic/" . $new_topic['tid'] . "-" . $new_topic['title_seo']);

							}

						}

					}

					?>

					<table class="forum topic">

						<tbody>

							<tr class="title">

								<td colspan="2">Izveidot jaunu tēmu sadaļā <strong><i><?php echo $cat['title']; ?></i></strong></td>

							</tr>

							<tr>

								<td width="32px;" valign="top">

									<?php echo user::returnAvatar($_SESSION['user_id'], false, 100, 100); ?>

								</td>

								<td>

									<div class="form">

										<form method="POST">

											<label for="title">Tēmas nosaukums</label>

											<input type="text" class="control" name="title" placeholder="Tēmas nosaukums..." value="<?php echo (isset($_POST['title'])) ? $_POST['title'] : ""; ?>">

											<textarea type="text" class="control" id="editor" name="topic" placeholder="Tēmas saturs..." rows="20"><?php echo (isset($_POST['topic'])) ? $_POST['topic'] : ""; ?></textarea>

											<button type="submit" name="new" class="blue">Izveidot</button>

										</form>

									</div>

								</td>

							</tr>

						</tbody>

					</table>

					<?php

				}else{

					page::alert("Tev nav pieejas veidot šajā sadaļā tēmu!", "danger");

				}

			}

		}else{

			$page = new page("Autorizējies, lai izveidotu tēmu!", array("Forums", "Autorizējies, lai izveidotu tēmu!"), false);

			page::alert("Lai izveidotu tēmu, autorizējies vai reģistrējies!", "danger");

		}

	}else{

		$page = new page("Netika pieprasīta sadaļa", array("Forums", "Netika pieprasīta sadaļa"), false);

		page::alert("Netika pieprasīta neviena sadaļa!", "danger");

	}

}elseif($action == "topic"){

	if(isset($path[3]) AND !empty($path[3])){

		$request = $path[3];

		$explode = explode("-", $request);

		$remove_id = array_slice($explode, 1);

		$title_seo = implode("-", $remove_id);

		$topic_id = $explode[0];



		$find_topic = $db->count("SELECT `tid` FROM `topics` WHERE `title_seo` = ? AND `tid` = ? AND `approved` = ?", array($title_seo, $topic_id, 1));

		if($find_topic == 0){

			$page = new page("Tēma netika atrasta", array("Forums", "Tēma netika atrasta"), false);

			page::alert("Tēma netika atrasta!", "danger");

		}else{

			forum::updateViews("topic", $topic_id);

			$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

			$forum = $db->fetch("SELECT `title` FROM `forums` WHERE `id` = ?", array($topic['forum_id']));

			$page = new page($topic['title'], array("Forums", $forum['title'], $topic['title']), false);

			$user = user::data($topic['author_id']);

			if(user::isLoggedIn() AND $topic['state'] == "open" OR user::hasFlag("mod")){

				if(isset($_POST['comment'])){

					$errors = array();



					if(empty($_POST['post'])){

						$errors[] = "Tu aizmirsi ievadīt tekstu savā atbildē!";

					}else{

						if(!user::hasFlag("mod")){

							$lastComment = $db->fetch("SELECT `post_date` FROM `posts` WHERE `author_id` = ? AND `topic_id` = ?", array($_SESSION['user_id'], $topic_id));

							if(!empty($lastComment)){

								if((time() - $lastComment['post_date']) <= 60){

									$errors[] = "Jaunu atbildi drīkst veikt tikai minūti pēc iepriekšējās atbildes!";

								}

							}

						}

					}



					if(count($errors) > 0){

						foreach($errors as $error){

							page::alert($error, "danger");

						}

					}else{

						$db->insert("INSERT INTO `posts` (`author_id`, `approved`, `post_date`, `post`, `topic_id`) VALUES (?, ?, ?, ?, ?)", array($_SESSION['user_id'], 1, time(), $_POST['post'], $topic_id));

						$new_post = $db->fetch("SELECT `pid`, `post_date` FROM `posts` ORDER BY `pid` DESC LIMIT 1");

						$db->update("UPDATE `forums` SET `last_poster_id` = ?, `last_post_date` = ?, `last_post_id` = ?, `last_topic_id` = ? WHERE `id` = ?", array(

							$_SESSION['user_id'],

							$new_post['post_date'],

							$new_post['pid'],

							$topic_id,

							$topic['forum_id']

							));

						$forum = $db->fetch("SELECT `parent_id` FROM `forums` WHERE `id` = ?", array($topic['forum_id']));

						$db->update("UPDATE `forums` SET `last_poster_id` = ?, `last_post_date` = ?, `last_topic_id` = ? WHERE `id` = ?", array($_SESSION['user_id'], $new_post['post_date'], $topic['tid'], $forum['parent_id']));

						$db->update("UPDATE `topics` SET `last_poster_id` = ?, `last_post_date` = ? WHERE `tid` = ?", array($_SESSION['user_id'], $new_post['post_date'], $topic_id));

						$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

						user::addMoney($_SESSION['user_id'], "0.05");

						page::addShout("Es atbildēju uz tēmu " . $topic['title'] . ". Izlasi to šeit: " . $c['url'] . "/forum/topic/$topic_id-$title_seo", $_SESSION['user_id']);

					}

				}

			}



			if(user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])){

				if(isset($_POST['changeState'])){

					$state = ($topic['state'] == "open") ? "closed" : "open";

					$db->update("UPDATE `topics` SET `state` = ? WHERE `tid` = ?", array($state, $topic_id));

					$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

				}

			}

			if(user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])){

				if(isset($_POST['pin'])){

					$pinned = ($topic['pinned'] == 1) ? 0 : 1;

					$db->update("UPDATE `topics` SET `pinned` = ? WHERE `tid` = ?", array($pinned, $topic_id));

					$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

				}

			}

			?>

			<div style="color: #323232; font-size: 20pt; text-indent: 20px; background: #efefef; border: 1px solid #ccc; text-shadow: #fff 0px 1px 0px; padding: 10px;">

				<?php echo $topic['title']; ?>

				<div class="form" style="float: right;">

					<?php if((user::isLoggedIn() AND $_SESSION['user_id'] == $topic['author_id'] AND $topic['state'] == "open") OR user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])): ?>

						<a href="<?php echo $c['url']; ?>/forum/edit/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>"><button type="button" class="green">Rediģēt</button></a>

					<?php endif; ?>

					<?php if(user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])): ?>

						<form method="POST" style="display: inline;">

							<button type="submit" class="blue" name="changeState"><?php echo ($topic['state'] == "open") ? "Slēgt" : "Atvērt"; ?></button>

							<button type="submit" class="orange" name="pin"><?php echo ($topic['pinned'] == 1) ? "Atspraust" : "Piespraust"; ?></button>

						</form>

						<a href="<?php echo $c['url']; ?>/forum/delete/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" class="confirm"><button type="button" class="red">Dzēst</button></a>

					<?php else: ?>

						<button type="button" class="<?php echo ($topic['state'] == "open") ? "blue" : "red"; ?>" disabled>Tēma <?php echo ($topic['state'] == "open") ? "atvērta" : "slēgta"; ?></button>

					<?php endif; ?>

				</div>

			</div>

			<table class="forum topic">

				<tbody>

					<tr class="title">

						<td align="left" valign="middle">

							<img src="<?php echo $c['url']; ?>/assets/images/icons/<?php echo (user::isOnline($user['user_id'])) ? "online" : "offline"; ?>.png" style="vertical-align: middle;">

							<?php echo user::formatName($user['user_id'], true, true, false); ?>

						</td>

						<td align="right">Tēma pievienota <?php echo page::formatTime($topic['topic_date']); ?></td>

					</tr>

					<tr>

						<td width="250px" align="center" valign="top" rowspan="<?php echo (empty($user['signature'])) ? 1 : 2; ?>" style="padding-bottom: 30px;">

							<?php echo user::returnAvatar($user['user_id'], false, 150, 150); ?>

							<div><?php echo $user['title']; ?></div>

							<div>---</div>

							<div><?php echo user::returnWarning($user['user_id']); ?></div>

							<div><?php echo user::returnGroup($user['user_id']); ?></div>

							<div>Pēdējo reizi manīts <?php echo page::formatTime($user['last_seen_date']); ?></div>

							<div>Ieraksti: <?php echo user::returnStatistics($user['user_id'], "posts"); ?></div>

							<div>Nauda: <?php echo user::getMoney($user['user_id']); ?></div>

							<?php if(!empty($user['skype'])): ?>

								<div>Skype: <a href="skype:<?php echo $user['skype']; ?>?chat"><?php echo $user['skype']; ?></a></div>

							<?php endif; ?>

							<?php if(user::isLoggedIn() AND $_SESSION['user_id'] != $user['user_id']): ?>

								<div><a class="pointer" data-toggle="modal" data-target="#sendMessageWindow" data-name="<?php echo $user['display_name']; ?>" data-receiver="<?php echo $user['user_id']; ?>">Sūtīt vēstuli</a></div>

							<?php endif; ?>

						</td>

						<td valign="top">

							<?php echo text::bbcode($topic['topic'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>

							<?php if(!empty($topic['last_edit'])): ?>

								<div style="margin-top: 20px;">

									<div class="alert alert-warning" style="font-size: 11px;">Pēdējo reizi rediģēja <?php echo user::formatName($topic['last_edit_by'], false, true); ?> <?php echo page::formatTime($topic['last_edit']); ?> <?php if(!empty($topic['last_edit_reason'])): ?><strong>Iemesls:</strong> <?php echo $topic['last_edit_reason']; ?><?php endif; ?></div>

								</div>

							<?php endif; ?>

						</td>

					</tr>

					<?php if(!empty($user['signature'])): ?>

						<tr>

							<td style="padding-top: 10px; padding-bottom: 10px;">

								<?php echo text::bbcode($user['signature'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>

							</td>

						</tr>

					<?php endif; ?>

					<?php $posts = $db->fetchAll("SELECT * FROM `posts` WHERE `topic_id` = ? AND `approved` = ? ORDER BY `post_date` ASC", array($topic_id, 1)); ?>

					<?php $i = 1; ?>

					<?php foreach($posts as $post): ?>

						<?php $post_user = user::data($post['author_id']); ?>

						<tr class="title">

							<td align="left" valign="middle">

								<img src="<?php echo $c['url']; ?>/assets/images/icons/<?php echo (user::isOnline($post_user['user_id'])) ? "online" : "offline"; ?>.png" style="vertical-align: middle;">

								<?php echo user::formatName($post_user['user_id'], true, true, false); ?>

							</td>

							<td align="right" style="padding-right: 20px;">

								Atbilde pievienota <?php echo page::formatTime($post['post_date']); ?>

								<span class="pointer" onClick="quote('<?php echo $post_user['display_name']; ?>', <?php echo $i; ?>)">[CITĒT]</span>

								<?php if(user::isLoggedIn() AND $post['author_id'] == $_SESSION['user_id'] OR user::hasFlag("mod")): ?>

									<a href="<?php $c['url']; ?>/forum/edit/post/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>/<?php echo $post['pid']; ?>"><span class="glyphicon glyphicon-edit"></span></a>

								<?php endif; ?>

								<?php if(user::hasFlag("mod")): ?>

									<a href="<?php $c['url']; ?>/forum/delete/post/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>/<?php echo $post['pid']; ?>" class="confirm"><span class="glyphicon glyphicon-trash"></span></a>

								<?php endif; ?>

							</td>

						</tr>

						<tr>

							<td width="250px" align="center" valign="top" rowspan="<?php echo (empty($post_user['signature'])) ? 1 : 2; ?>" style="padding-bottom: 30px;">

								<?php echo user::returnAvatar($post_user['user_id'], false, 150, 150); ?>

								<div><?php echo $post_user['title']; ?></div>

								<div>---</div>

								<div><?php echo user::returnWarning($post_user['user_id']); ?></div>

								<div><?php echo user::returnGroup($post_user['user_id']); ?></div>

								<div>Pēdējo reizi manīts <?php echo page::formatTime($post_user['last_seen_date']); ?></div>

								<div>Ieraksti: <?php echo user::returnStatistics($post_user['user_id'], "posts"); ?></div>

								<div>Nauda: <?php echo user::getMoney($post_user['user_id']); ?></div>

								<?php if(!empty($post_user['skype'])): ?>

									<div>Skype: <a href="skype:<?php echo $post_user['skype']; ?>?chat"><?php echo $post_user['skype']; ?></a></div>

								<?php endif; ?>

								<?php if(user::isLoggedIn() AND $_SESSION['user_id'] != $post_user['user_id']): ?>

									<div><a class="pointer" data-toggle="modal" data-target="#sendMessageWindow" data-name="<?php echo $post_user['display_name']; ?>" data-receiver="<?php echo $post_user['user_id']; ?>">Sūtīt vēstuli</a></div>

								<?php endif; ?>

							</td>

							<td valign="top">

								<div id="post_<?php echo $i; ?>"><?php echo text::bbcode($post['post'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?></div>

								<?php if(!empty($post['last_edit'])): ?>

									<div style="margin-top: 20px;">

										<div class="alert alert-warning" style="font-size: 11px;">Pēdējo reizi rediģēja <?php echo user::formatName($post['last_edit_by'], false, true); ?> <?php echo page::formatTime($post['last_edit']); ?> <?php if(!empty($post['last_edit_reason'])): ?><strong>Iemesls:</strong> <?php echo $post['last_edit_reason']; ?><?php endif; ?></div></div>

									</div>

								<?php endif; ?>

							</td>

						</tr>

						<?php if(!empty($post_user['signature'])): ?>

							<tr>

								<td style="padding-top: 10px; padding-bottom: 10px;" valign="top">

									<?php echo text::bbcode($post_user['signature'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>

								</td>

							</tr>

						<?php endif; ?>

						<?php $i++; ?>

					<?php endforeach; ?>

				</tbody>

			</table>

			<?php if($topic['state'] == "closed" AND user::hasFlag("mod")): ?>

				<div style="margin: 10px 0 10px 0;">

					<?php echo page::alert("Šī tēma ir slēgta. Toties, tākā Tev ir pieeja pie MOD flaga, Tu uz šo tēmu vari turpināt atbildēt!", "warning"); ?>

				</div>

			<?php endif; ?>

			<?php if(user::isLoggedIn() AND $topic['state'] == "open" OR user::hasFlag("mod")): ?>

				<table class="forum topic" id="reply">

					<tbody>

						<tr class="title">

							<td colspan="2">Atbildēt uz šo tēmu</td>

						</tr>

						<tr>

							<td width="32px;" valign="top">

								<?php echo user::returnAvatar($_SESSION['user_id'], false, 100, 100); ?>

							</td>

							<td>

								<div class="form">

									<form method="POST">

										<textarea type="text" class="control" id="editor" name="post" placeholder="Tava atbilde..." rows="10"></textarea>

										<button type="submit" name="comment" class="blue" style="margin-bottom: 10px;">Atbildēt</button>

									</form>

								</div>

							</td>

						</tr>

					</tbody>

				</table>

			<?php endif; ?>

			<table>

				<tbody class="statistics">

					<tr class="title">

						<td colspan="2">

							<i class="fa fa-pie-chart"></i> Statistika

						</td>

					</tr>

					<tr>

						<td colspan="2" style="text-align: left;" valign="top">

							<?php $reading = $db->fetchAll("SELECT `user_id` FROM `sessions` WHERE `user_id` > 0 AND `path_1` = ? AND `path_2` = ? AND `path_3` = ?", array(

								$path[1],

								$path[2],

								$path[3]

								)); ?>

								<i class="fa fa-eye fa-lg"></i> Tēmu pašlaik lasa(<?php echo count($reading); ?>): 

								<?php if(empty($reading)){ echo "<i>Neviens lietotājs pašlaik nelasa šo tēmu!</i>"; }else{ ?>

								<?php $list = array(); ?>

								<?php foreach($reading as $user): ?>

									<?php $list[] = user::formatName($user['user_id'], true, true); ?>

								<?php endforeach; ?>

								<?php echo implode(", ", $list); ?>

								<?php } ?>

							</tr>

							<tr>

								<td colspan="2" style="text-align: left;" valign="top">

									<?php $read = $db->fetchAll("SELECT `user_id` FROM `views` WHERE `user_id` > 0 AND `parent_type` = ? AND `parent_id` = ?", array(

										"topic",

										$topic_id

										)); ?>

										<i class="fa fa-bar-chart fa-lg"></i> Tēmu ir lasījuši(<?php echo count($read); ?>): 

										<?php if(empty($read)){ echo "<i>Neviens lietotājs nav vēl lasījis šo tēmu!</i>"; }else{ ?>

										<?php $list = array(); ?>

										<?php foreach($read as $user): ?>

											<?php $list[] = user::formatName($user['user_id'], true, true); ?>

										<?php endforeach; ?>

										<?php $list = array_unique($list); ?>

										<?php echo implode(", ", $list); ?>

										<?php } ?>

									</tr>

								</tbody>

							</table>

							<?php

						}

					}else{

						$page = new page("Netika pieprasīta tēma", array("Forums", "Netika pieprasīta tēma"), false);

						page::alert("Netika pieprasīta neviena tēma!", "danger");

					}

				}elseif($action == "cat"){

					if(isset($path[3]) AND !empty($path[3])){

						$request = $path[3];

						$explode = explode("-", $request);

						$remove_id = array_slice($explode, 1);

						$title_seo = implode("-", $remove_id);

						$cat_id = $explode[0];



						$find_cat = $db->count("SELECT `id` FROM `forums` WHERE `title_seo` = ? AND `id` = ?", array($title_seo, $cat_id));

						if($find_cat == 0){

							$page = new page("Netika pieprasīta sadaļa", array("Forums", "Netika pieprasīta sadaļa"), false);

							page::alert("Sadaļa netika atrasta!", "danger");

						}else{

							$cat = $db->fetch("SELECT * FROM `forums` WHERE `id` = ?", array($cat_id));

							if($cat['parent_id'] == 0){

								$page = new page($cat['title'], array("Forums", $cat['title']), false);

								?>

								<?php $sub_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($cat['id'])); ?>

								<div style="color: #323232; font-size: 20pt; text-indent: 20px; background: #efefef; border: 1px solid #ccc; text-shadow: #fff 0px 1px 0px; padding: 10px;"><?php echo $cat['title']; ?></div>

								<table class="forum">

									<tbody>

										<tr class="title">

											<td colspan="5">

												<a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $cat['id'] . "-" . $cat['title_seo']; ?>"><?php echo $cat['title']; ?></a>

											</td>

										</tr>

										<tr class="top">

											<td width="32px"></td>

											<td>Sadaļa</td>

											<td width="75px" class="text-center">Tēmas</td>

											<td width="75px" class="text-center">Atbildes</td>

											<td width="150px" class="text-center">Pēdējais ieraksts</td>

										</tr>

										<?php foreach($sub_cat as $sub): ?>

											<tr>

												<td>

													<img src="<?php $c['url'] ?>/assets/images/icons/f_icon.png" class="middle">

												</td>

												<td>

													<a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $sub['id'] . "-" . $sub['title_seo']; ?>" style="font-weight: bold;"><?php echo $sub['title']; ?></a><br />

													<?php echo $sub['description']; ?>

													<?php $subforums = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($sub['id'])); ?>

													<?php if(!empty($subforums)): ?>

														<div style="margin-left: 10px;">

															<?php foreach($subforums as $forum): ?>

																&#9679; <a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $forum['id'] . "-" . $forum['title_seo']; ?>"><?php echo $forum['title']; ?></a>

															<?php endforeach; ?>

														</div>

													<?php endif; ?>

												</td>

												<td class="text-center"><?php echo forum::returnStatistics($sub['id'], "topics"); ?></td>

												<td class="text-center"><?php echo forum::returnStatistics($sub['id'], "fposts"); ?></td>

												<td width="200px">

													<?php if($sub['last_poster_id'] == 0): ?>

														<div class="text-center">Neviena jauna ieraksta</div>

													<?php else: ?>

														<div style="float: left; margin-top: 2px;">

															<?php echo user::returnAvatar($sub['last_poster_id'], false, 32, 32); ?>

														</div>

														<div style="margin-top: 4px; float: left; margin-left: 5px;">

															<?php $topic = $db->fetch("SELECT `tid`, `title`, `title_seo` FROM `topics` WHERE `tid` = ?", array($sub['last_topic_id'])); ?>

															<a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo text::limit($topic['title'], 20); ?></a><br />

															<small>No <?php echo user::formatName($sub['last_poster_id'], false, true, false, 12); ?> <?php echo page::formatTime($sub['last_post_date']); ?></small>

														</div>

													<?php endif; ?>

												</td>

											</tr>

										<?php endforeach; ?>

									</tbody>

								</table>

								<?php

							}else{

								$parent_cat = $db->fetch("SELECT `title` FROM `forums` WHERE `id` = ?", array($cat['parent_id']));

								$page = new page($cat['title'], array("Forums", $parent_cat['title'], $cat['title']), false);

								?>

								<div style="color: #323232; font-size: 20pt; text-indent: 20px; background: #efefef; border: 1px solid #ccc; text-shadow: #fff 0px 1px 0px; padding: 10px;">

									<?php echo $cat['title']; ?>

									<?php if($cat['allow_topics'] == 1): ?>

										<div class="form" style="float: right;">

											<?php if(user::isLoggedIn()): ?>

												<a href="<?php echo $c['url']; ?>/forum/new/<?php echo $cat['id'] . "-" . $cat['title_seo']; ?>"><button type="button" class="blue">Jauna tēma</button></a>

											<?php endif; ?>

										</div>

									<?php endif; ?>

								</div>

								<?php $subforums = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($cat['id'])); ?>

								<?php if(!empty($subforums)): ?>

									<table class="forum">

										<tbody>

											<tr class="title">

												<td colspan="5">

													Apakšsadaļas

												</td>

											</tr>

											<tr class="top">

												<td width="32px"></td>

												<td>Sadaļa</td>

												<td width="75px" class="text-center">Tēmas</td>

												<td width="75px" class="text-center">Atbildes</td>

												<td width="150px" class="text-center">Pēdējais ieraksts</td>

											</tr>

											<?php foreach($subforums as $forum): ?>

												<tr>

													<td align="center">

														<img src="<?php $c['url'] ?>/assets/images/icons/f_icon.png" class="middle">

													</td>

													<td>

														<a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $forum['id'] . "-" . $forum['title_seo']; ?>" style="font-weight: bold;"><?php echo $forum['title']; ?></a><br />

														<?php echo $forum['description']; ?>

														<?php $subsubforums = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($forum['id'])); ?>

														<?php if(!empty($subsubforums)): ?>

															<?php foreach($subsubforums as $forum): ?>

																<div style="margin-left: 10px;">

																	<span class="glyphicon glyphicon-bookmark"></span> <a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $forum['id'] . "-" . $forum['title_seo']; ?>"><?php echo $forum['title']; ?></a>

																</div>

															<?php endforeach; ?>

														<?php endif; ?>

													</td>

													<td class="text-center"><?php echo forum::returnStatistics($forum['id'], "topics"); ?></td>

													<td class="text-center"><?php echo forum::returnStatistics($forum['id'], "fposts"); ?></td>

													<td>

														<?php if($forum['last_poster_id'] == 0): ?>

															<div class="text-center">Neviena jauna ieraksta</div>

														<?php else: ?>

															<div style="float: left; margin-top: 3px;">

																<?php echo user::returnAvatar($forum['last_poster_id'], false, 32, 32); ?>

															</div>

															<div style="margin-top: 4px; float: left; margin-left: 5px;">

																No <?php echo user::formatName($forum['last_poster_id'], false, true, false); ?><br />

																<?php echo page::formatTime($forum['last_post_date']); ?>

															</div>

														<?php endif; ?>

													</td>

												</tr>

											<?php endforeach; ?>

										</tbody>

									</table>

								<?php endif; ?>

								<?php if($cat['allow_topics'] == 1): ?>

									<table class="forum">

										<tbody>

											<tr class="title">

												<td colspan="6">

													<?php echo $cat['title']; ?>

												</td>

											</tr>

											<tr class="top">

												<td width="16px"></td>

												<td width="16px"></td>

												<td>Nosaukums</td>

												<td width="75px" class="text-center">Atbildes</td>

												<td width="75px" class="text-center">Skatījumi</td>

												<td width="150px">Pēdējais ieraksts</td>

											</tr>

											<?php $pinned_topics = $db->fetchAll("SELECT * FROM `topics` WHERE `forum_id` = ? AND `pinned` = 1 AND `approved` = 1 ORDER BY `last_post_date` DESC", array($cat['id'])); ?>

											<?php if(!empty($pinned_topics)): ?>

												<?php foreach($pinned_topics as $topic): ?>

													<tr>

														<td>

															<img src="<?php echo $c['url'] ?>/assets/images/icons/<?php echo ($topic['state'] == "closed") ? "locked" : "unlocked" ?>.png" class="middle">

														</td>

														<td>

															<img src="<?php echo $c['url'] ?>/assets/images/icons/pinned.png" class="middle">

														</td>

														<td>

															<div><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo $topic['title']; ?></a></div>

															<div>Tēmu aizsāka <?php echo user::formatName($topic['author_id'], true, true); ?> <?php echo page::formatTime($topic['topic_date']); ?></div>

														</td>

														<td class="text-center">

															<?php echo forum::returnStatistics($topic['tid'], "posts"); ?>

														</td>

														<td class="text-center">

															<?php echo forum::returnStatistics($topic['tid'], "views"); ?>

														</td>

														<td width="200px">

															<div style="float: left; margin-top: 3px;">

																<?php echo user::returnAvatar($topic['last_poster_id'], false, 32, 32); ?>

															</div>

															<div style="margin-top: 4px; float: left; margin-left: 5px;">

																No <?php echo user::formatName($topic['last_poster_id'], false, true, false); ?><br />

																<?php echo page::formatTime($topic['last_post_date']); ?>

															</div>

															<div class="clear"></div>

														</td>

													</tr>

												<?php endforeach; ?>

												<tr class="top">

													<td colspan="6">Ieraksti</td>

												</tr>

											<?php endif; ?>

											<?php $topics = $db->fetchAll("SELECT * FROM `topics` WHERE `forum_id` = ? AND `pinned` = 0 AND `approved` = 1 ORDER BY `last_post_date` DESC", array($cat['id'])); ?>

											<?php if(empty($topics)): ?>

												<tr>

													<td colspan="6" style="padding: 5px;">Sadaļā nav nevienas tēmas</td>

												</tr>

											<?php else: ?>

												<?php foreach($topics as $topic): ?>

													<tr>

														<td>

															<img src="<?php echo $c['url'] ?>/assets/images/icons/<?php echo ($topic['state'] == "closed") ? "locked" : "unlocked" ?>.png" class="middle">

														</td>

														<td></td>

														<td>

															<div><a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo $topic['title']; ?></a></div>

															<div>Tēmu aizsāka <?php echo user::formatName($topic['author_id'], true, true); ?> <?php echo page::formatTime($topic['topic_date']); ?></div>

														</td>

														<td class="text-center">

															<?php echo forum::returnStatistics($topic['tid'], "posts"); ?>

														</td>

														<td class="text-center">

															<?php echo forum::returnStatistics($topic['tid'], "views"); ?>

														</td>

														<td width="200px">

															<div style="float: left; margin-top: 3px;">

																<?php echo user::returnAvatar($topic['last_poster_id'], false, 32, 32); ?>

															</div>

															<div style="margin-top: 4px; float: left; margin-left: 5px;">

																No <?php echo user::formatName($topic['last_poster_id'], false, true, false); ?><br />

																<?php echo page::formatTime($topic['last_post_date']); ?>

															</div>

															<div class="clear"></div>

														</td>

													</tr>

												<?php endforeach; ?>

											<?php endif; ?>

										</tbody>

									</table>

								<?php endif; ?>

								<?php

							}

						}

					}else{

						$page = new page("Netika pieprasīta sadaļa", array("Forums", "Netika pieprasīta sadaļa"), false);

						page::alert("Netika pieprasīta neviena sadaļa!", "danger");

					}

				}elseif($action == "edit"){

					if(user::isLoggedIn()){

						if(isset($path[3]) AND !empty($path[3])){

							if($path[3] == "topic"){

								if(isset($path[4]) AND !empty($path[4])){

									$request = $path[4];

									$explode = explode("-", $request);

									$remove_id = array_slice($explode, 1);

									$title_seo = implode("-", $remove_id);

									$topic_id = $explode[0];



									$find_topic = $db->count("SELECT `tid` FROM `topics` WHERE `title_seo` = ? AND `tid` = ? AND `approved` = ?", array($title_seo, $topic_id, 1));

									if($find_topic == 0){

										$page = new page("Tēma netika atrasta", array("Forums", "Tēma netika atrasta"), false);

										page::alert("Tēma netika atrasta!", "danger");

									}else{

										$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

										if($topic['author_id'] == $_SESSION['user_id'] OR user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])){

											$page = new page("Rediģēt tēmu " . $topic['title'], array("Forums", "Rediģēt tēmu <i>" . $topic['title'] . "</i>"), false);

											if(isset($_POST['edit'])){

												$errors = array();



												if(empty($_POST['title'])){

													$errors[] = "Tēmas tekstu nedrīkst atstāt tukšu!";

												}else{

													if(strlen($_POST['title']) < 5){

														$errors[] = "Tēmas nosaukumam jābūt vismaz 5 rakstzīmju garumā!";

													}

												}



												if(empty($_POST['topic'])){

													$errors[] = "Tēmas nosaukumu nedrīkst atstāt tukšu!";

												}else{

													if(strlen($_POST['topic']) < 55){

														$errors[] = "Tēmas tekstam jābūt vismaz 55 rakstzīmju garumā!";

													}

												}



												if(count($errors) > 0){

													foreach($errors as $error){

														page::alert($error, "danger");

													}

												}else{

													$db->update("UPDATE `topics` SET `title` = ?, `title_seo` = ?, `topic` = ?, `last_edit_by` = ?, `last_edit` = ?, `last_edit_reason` = ? WHERE `tid` = ?", array(

														$_POST['title'],

														text::seostring($_POST['title']),

														$_POST['topic'],

														$_SESSION['user_id'],

														time(),

														$_POST['last_edit_reason'],

														$topic_id

														));

													page::alert("Tēma veiksmīgi rediģēta!", "success");

													$topic = $db->fetch("SELECT * FROM `topics` WHERE `tid` = ?", array($topic_id));

												}

											}

											?>

											<table class="forum topic">

												<tbody>

													<tr class="title">

														<td colspan="2">Rediģēt tēmu <strong><i><?php echo $topic['title']; ?></i></strong></td>

													</tr>

													<tr>

														<td width="32px;" valign="top">

															<?php echo user::returnAvatar($_SESSION['user_id'], false, 100, 100); ?>

														</td>

														<td>

															<div class="form">

																<form method="POST">

																	<label for="title">Tēmas nosaukums</label>

																	<input type="text" class="control" name="title" placeholder="Tēmas nosaukums..." value="<?php echo $topic['title']; ?>">

																	<textarea type="text" class="control" id="editor" name="topic" placeholder="Tēmas saturs..." rows="20"><?php echo (isset($_POST['topic'])) ? $_POST['topic'] : $topic['topic']; ?></textarea>

																	<label for="title">Rediģēšanas iemesls</label>

																	<input type="text" class="control" name="last_edit_reason" placeholder="Rediģēšanas iemesls..." value="<?php echo $topic['last_edit_reason']; ?>">

																	<button type="submit" name="edit" class="blue">Rediģēt</button>

																</form>

															</div>

														</td>

													</tr>

												</tbody>

											</table>

											<?php

										}else{

											$page = new page("Tev nav pieejas!", array("Forums", "Tev nav pieejas!"), false);

											page::alert("Tev nav pieejas rediģēt šo tēmu!", "danger");

										}

									}

								}else{

									$page = new page("Netika pieprasīta tēma", array("Forums", "Netika pieprasīta tēma"), false);

									page::alert("Netika pieprasīta neviena tēma!", "danger");

								}

							}elseif($path[3] == "post"){

								if(isset($path[4]) AND !empty($path[4]) AND isset($path[5]) AND !empty($path[5])){

									$request = $path[4];

									$explode = explode("-", $request);

									$remove_id = array_slice($explode, 1);

									$title_seo = implode("-", $remove_id);

									$topic_id = $explode[0];

									$post_id = $path[5];



									$find_topic = $db->count("SELECT `tid` FROM `topics` WHERE `title_seo` = ? AND `tid` = ? AND `approved` = ?", array($title_seo, $topic_id, 1));

									$find_post = $db->count("SELECT `pid` FROM `posts` WHERE `pid` = ? AND `topic_id` = ?", array($post_id, $topic_id));

									if($find_topic == 0 OR $find_post == 0){

										$page = new page("Atbilde netika atrasta", array("Forums", "Atbilde netika atrasta"), false);

										page::alert("Atbilde netika atrasta!", "danger");

									}else{

										$post = $db->fetch("SELECT `author_id`, `post`, `last_edit_reason` FROM `posts` WHERE `pid` = ?", array($post_id));

										$topic = $db->fetch("SELECT `title` FROM `topics` WHERE `tid` = ?", array($topic_id));

										if($post['author_id'] == $_SESSION['user_id'] OR user::hasFlag("mod")){

											$page = new page("Rediģēt atbildi iekš " . $topic['title'], array("Forums", "Rediģēt atbildi iekš <i>" . $topic['title'] . "</i>"), false);

											if(isset($_POST['edit'])){

												$errors = array();



												if(empty($_POST['post'])){

													$errors[] = "Tu aizmirsi ievadīt tekstu savā atbildē!";

												}



												if(count($errors) > 0){

													foreach($errors as $error){

														page::alert($error, "danger");

													}

												}else{

													$db->update("UPDATE `posts` SET `post` = ?, `last_edit_by` = ?, `last_edit` = ?, `last_edit_reason` = ? WHERE `pid` = ?", array(

														$_POST['post'],

														$_SESSION['user_id'],

														time(),

														$_POST['last_edit_reason'],

														$post_id

														));

													page::alert("Atbilde veiksmīgi rediģēta!", "success");

													$post = $db->fetch("SELECT `author_id`, `post`, `last_edit_reason` FROM `posts` WHERE `pid` = ?", array($post_id));

												}

											}

											?>

											<table class="forum topic">

												<tbody>

													<tr>

														<td width="32px;" valign="top">

															<?php echo user::returnAvatar($_SESSION['user_id'], false, 100, 100); ?>

														</td>

														<td>

															<div class="form">

																<form method="POST">

																	<textarea type="text" class="control" id="editor" name="post" placeholder="Atbildes saturs..." rows="20"><?php echo (isset($_POST['post'])) ? $_POST['post'] : $post['post']; ?></textarea>

																	<label for="title">Rediģēšanas iemesls</label>

																	<input type="text" class="control" name="last_edit_reason" placeholder="Rediģēšanas iemesls..." value="<?php echo $post['last_edit_reason']; ?>">

																	<button type="submit" name="edit" class="blue">Rediģēt</button>

																</form>

															</div>

														</td>

													</tr>

												</tbody>

											</table>

											<?php

										}else{

											$page = new page("Tev nav pieejas!", array("Forums", "Tev nav pieejas!"), false);

											page::alert("Tev nav pieejas rediģēt šo atbildi!", "danger");

										}

									}

								}else{

									$page = new page("Netika pieprasīta atbilde", array("Forums", "Netika pieprasīta atbilde"), false);

									page::alert("Netika pieprasīta neviena atbilde!", "danger");

								}

							}else{

								$page = new page("Darbība netika atrasta", array("Forums", "Darbība netika atrasta"), false);

								page::alert("Darbība netika atrasta!", "danger");

							}

						}else{

							$page = new page("Netika pieprasīta darbība", array("Forums", "Netika pieprasīta darbība"), false);

							page::alert("Netika pieprasīta neviena darbība!", "danger");

						}

					}else{

						$page = new page("Autorizējies, lai izveidotu tēmu!", array("Forums", "Autorizējies, lai izveidotu tēmu!"), false);

						page::alert("Lai izveidotu tēmu, autorizējies vai reģistrējies!", "danger");

					}

				}elseif($action == "delete"){

					if(user::isLoggedIn()){

						if(isset($path[3]) AND !empty($path[3])){

							if($path[3] == "topic" AND isset($_SERVER['HTTP_REFERER'])){

								if(isset($path[4]) AND !empty($path[4])){

									$request = $path[4];

									$explode = explode("-", $request);

									$remove_id = array_slice($explode, 1);

									$title_seo = implode("-", $remove_id);

									$topic_id = $explode[0];



									$find_topic = $db->count("SELECT `tid` FROM `topics` WHERE `title_seo` = ? AND `tid` = ? AND `approved` = ?", array($title_seo, $topic_id, 1));

									if($find_topic == 0){

										$page = new page("Tēma netika atrasta", array("Forums", "Tēma netika atrasta"), false);

										page::alert("Tēma netika atrasta!", "danger");

									}else{

										$topic = $db->fetch("SELECT `title`, `author_id`, `forum_id` FROM `topics` WHERE `tid` = ?", array($topic_id));

										if($topic['author_id'] == $_SESSION['user_id'] OR user::hasFlag("mod") OR user::hasForumMod($topic['forum_id'])){

											$page = new page("Dzēst tēmu " . $topic['title'], array("Forums", "Dzēst tēmu <i>" . $topic['title'] . "</i>"), false);

											$latest_topic = $db->fetch("SELECT `tid`, `last_poster_id`, `last_post_date` FROM `topics` WHERE `forum_id` = ? AND `approved` = ? ORDER BY `last_post_date` DESC LIMIT 1", array($topic['forum_id'], 1));

											if(empty($latest_topic)){

												$db->update("UPDATE `forums` SET `last_topic_id` = ?, `last_post_date` = ?, `last_poster_id` = ? WHERE `id` = ?", array(

													$latest_topic['tid'],

													$latest_topic['last_post_date'],

													$latest_topic['last_poster_id'],

													$topic['forum_id']

													));

											}else{

												$db->update("UPDATE `forums` SET `last_topic_id` = ?, `last_post_date` = ?, `last_poster_id` = ? WHERE `id` = ?", array(

													0,

													0,

													0,

													$topic['forum_id']

													));

											}

											$db->update("UPDATE `topics` SET `approved` = ? WHERE `tid` = ?", array(2, $topic_id));

											page::alert("Tēma veiksmīgi dzēsta!", "success");

											page::redirectTo("forum", array("external" => false, "time" => 3));

										}else{

											$page = new page("Tev nav pieejas!", array("Forums", "Tev nav pieejas!"), false);

											page::alert("Tev nav pieejas dzēst šo tēmu!", "danger");

										}

									}

								}else{

									$page = new page("Netika pieprasīta tēma", array("Forums", "Netika pieprasīta tēma"), false);

									page::alert("Netika pieprasīta neviena tēma!", "danger");

								}

							}elseif($path[3] == "post" AND isset($_SERVER['HTTP_REFERER'])){

								if(isset($path[4]) AND !empty($path[4]) AND isset($path[5]) AND !empty($path[5])){

									$request = $path[4];

									$explode = explode("-", $request);

									$remove_id = array_slice($explode, 1);

									$title_seo = implode("-", $remove_id);

									$topic_id = $explode[0];

									$post_id = $path[5];



									$find_topic = $db->count("SELECT `tid` FROM `topics` WHERE `title_seo` = ? AND `tid` = ? AND `approved` = ?", array($title_seo, $topic_id, 1));

									$find_post = $db->count("SELECT `pid` FROM `posts` WHERE `pid` = ? AND `topic_id` = ?", array($post_id, $topic_id));

									if($find_topic == 0 OR $find_post == 0){

										$page = new page("Atbilde netika atrasta", array("Forums", "Atbilde netika atrasta"), false);

										page::alert("Atbilde netika atrasta!", "danger");

									}else{

										$post = $db->fetch("SELECT `author_id` FROM `posts` WHERE `pid` = ?", array($post_id));

										$topic = $db->fetch("SELECT `title`, `forum_id` FROM `topics` WHERE `tid` = ?", array($topic_id));

										if($post['author_id'] == $_SESSION['user_id'] OR user::hasFlag("mod")){

											$page = new page("Dzēst atbildi iekš " . $topic['title'], array("Forums", "Dzēst atbildi iekš <i>" . $topic['title'] . "</i>"), false);

											$db->update("UPDATE `posts` SET `approved` = ? WHERE `pid` = ?", array(2, $post_id));



											$latest_post = $db->fetch("SELECT `author_id`, `post_date` FROM `posts` WHERE `topic_id` = ? AND `approved` = ? ORDER BY `post_date` DESC LIMIT 1", array($topic_id, 1));

											if(empty($latest_post)){

												$db->update("UPDATE `topics` SET `last_poster_id` = `author_id`, `last_post_date` = `topic_date` WHERE `tid` = ?", array($topic_id));

											}else{

												$db->update("UPDATE `topics` SET `last_poster_id` = ?, `last_post_date` = ? WHERE `tid` = ?", array($latest_post['author_id'], $latest_post['post_date'], $topic_id));

											}

											page::alert("Atbilde veiksmīgi dzēsta!", "success");

											page::redirectTo($_SERVER['HTTP_REFERER'], array("external" => true, "time" => 3));

										}else{

											$page = new page("Tev nav pieejas!", array("Forums", "Tev nav pieejas!"), false);

											page::alert("Tev nav pieejas dzēst šo atbildi!", "danger");

										}

									}

								}else{

									$page = new page("Netika pieprasīta atbilde", array("Forums", "Netika pieprasīta atbilde"), false);

									page::alert("Netika pieprasīta neviena atbilde!", "danger");

								}

							}else{

								$page = new page("Darbība netika atrasta", array("Forums", "Darbība netika atrasta"), false);

								page::alert("Darbība netika atrasta!", "danger");

							}

						}else{

							$page = new page("Netika pieprasīta darbība", array("Forums", "Netika pieprasīta darbība"), false);

							page::alert("Netika pieprasīta neviena darbība!", "danger");

						}

					}else{

						$page = new page("Autorizējies, lai izveidotu tēmu!", array("Forums", "Autorizējies, lai izveidotu tēmu!"), false);

						page::alert("Lai izveidotu tēmu, autorizējies vai reģistrējies!", "danger");

					}

				}else{

					$page = new page("Forums", array("Forums"), false);

					?>

					<table class="forum">

						<tbody>

							<?php $main_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array(0)); ?>

							<?php foreach($main_cat as $main): ?>

								<tr class="title">

									<td colspan="5">

										<a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $main['id'] . "-" . $main['title_seo']; ?>"><?php echo $main['title']; ?></a>

									</td>

								</tr>

								<?php $sub_cat = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($main['id'])); ?>

								<tr class="top">

									<td width="32px"></td>

									<td>Sadaļa</td>

									<td width="75px" class="text-center">Tēmas</td>

									<td width="75px" class="text-center">Atbildes</td>

									<td width="150px" class="text-center">Pēdējais ieraksts</td>

								</tr>

								<?php foreach($sub_cat as $sub): ?>

									<tr>

										<td>

											<img src="<?php $c['url'] ?>/assets/images/icons/f_icon.png" class="middle">

										</td>

										<td>

											<a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $sub['id'] . "-" . $sub['title_seo']; ?>" style="font-weight: bold;"><?php echo $sub['title']; ?></a><br />

											<?php echo $sub['description']; ?>

											<?php $subforums = $db->fetchAll("SELECT * FROM `forums` WHERE `parent_id` = ? ORDER BY `position` ASC", array($sub['id'])); ?>

											<?php if(!empty($subforums)): ?>

												<div style="margin-left: 10px;">

													<?php foreach($subforums as $forum): ?>

														&#9679; <a href="<?php echo $c['url']; ?>/forum/cat/<?php echo $forum['id'] . "-" . $forum['title_seo']; ?>"><?php echo $forum['title']; ?></a>

													<?php endforeach; ?>

												</div>

											<?php endif; ?>

										</td>

										<td class="text-center"><?php echo forum::returnStatistics($sub['id'], "topics"); ?></td>

										<td class="text-center"><?php echo forum::returnStatistics($sub['id'], "fposts"); ?></td>

										<td width="200px">

											<?php if($sub['last_poster_id'] == 0): ?>

												<div class="text-center">Neviena jauna ieraksta</div>

											<?php else: ?>

												<div style="float: left; margin-top: 3px;">

													<?php echo user::returnAvatar($sub['last_poster_id'], false, 32, 32); ?>

												</div>

												<div style="margin-top: 4px; float: left; margin-left: 5px;">

													<?php $topic = $db->fetch("SELECT `tid`, `title`, `title_seo` FROM `topics` WHERE `tid` = ?", array($sub['last_topic_id'])); ?>

													<a href="<?php echo $c['url']; ?>/forum/topic/<?php echo $topic['tid'] . "-" . $topic['title_seo']; ?>" style="font-weight: bold;"><?php echo text::limit($topic['title'], 20); ?></a><br />

													<small>No <?php echo user::formatName($sub['last_poster_id'], false, true, false, 12); ?> <?php echo page::formatTime($sub['last_post_date']); ?></small>

												</div>

											<?php endif; ?>

										</td>

									</tr>

								<?php endforeach; ?>

							<?php endforeach; ?>

						</tbody>

					</table>

					<table>

						<tbody class="statistics">

							<?php

							$total_topics = $db->count("SELECT `tid` FROM `topics` WHERE `approved` = 1");

							$total_posts = $db->count("SELECT `pid` FROM `posts` WHERE `approved` = 1");

							$total_users = $db->count("SELECT `user_id` FROM `users`");

							$last_registered_user = $db->fetch("SELECT `user_id` FROM `users` ORDER BY `registration_date` DESC LIMIT 1");

							?>

							<tr class="title">

								<td colspan="2">

									<i class="fa fa-pie-chart"></i> Statistika

								</td>

							</tr>

							<tr>

								<td><i class="fa fa-pencil fa-lg"></i> Kopējais izveidoto tēmu skaits: <strong><?php echo $total_topics; ?></strong></td>

								<td><i class="fa fa-pencil fa-lg"></i> Kopējais atbilžu skaits: <strong><?php echo $total_posts; ?></strong></td>

							</tr>

							<tr>

								<td><i class="fa fa-users fa-lg"></i> Kopējais reģistrēto lietotāju skaits: <strong><?php echo $total_users; ?></strong></td>

								<td><i class="fa fa-user fa-lg"></i> Pēdējais reģistrētais lietotājs: <?php echo user::formatName($last_registered_user['user_id'], true, true); ?></td>

							</tr>

							<tr>

								<td colspan="2" style="text-align: left;" valign="top">

									<?php $sessions = $db->fetchAll("SELECT * FROM `sessions` WHERE `user_id` > 0"); ?>

									<i class="fa fa-eye fa-lg"></i> Pašlaik tiešsaistē(<?php echo count($sessions); ?>): 

									<?php if(empty($sessions)){ echo "<i>Neviens lietotājs pašlaik nav tiešsaistē!</i>"; }else{ ?>

									<?php $list = array(); ?>

									<?php foreach($sessions as $user): ?>

										<?php $list[] = user::formatName($user['user_id'], true, true); ?>

									<?php endforeach; ?>

									<?php echo implode(", ", $list); ?>

									<?php } ?>

								</td>

							</tr>

							<tr>

								<td colspan="2" style="text-align: left;" valign="top">

									<?php $online_today = $db->fetchAll("SELECT `user_id` FROM `statistics` WHERE `user_id` > 0 AND `date` = ?", array(date("d/m/Y"))); ?>

									<i class="fa fa-bar-chart fa-lg"></i> Šodien tiešsaistē bija(<?php echo count($online_today); ?>): 

									<?php if(empty($online_today)){ echo "<i>Neviens lietotājs šodien nav bijis tiešsaistē!</i>"; }else{ ?>

									<?php $list = array(); ?>

									<?php foreach($online_today as $user): ?>

										<?php $list[] = user::formatName($user['user_id'], true, true); ?>

									<?php endforeach; ?>

									<?php echo implode(", ", $list); ?>

									<?php } ?>

								</td>

							</tr>

						</tbody>

					</table>

					<?php

				}