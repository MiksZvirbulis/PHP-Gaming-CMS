<?php if(isset($path[2]) && !empty($path[2]) && $path[2] != "page"): ?>

<?php

	$request = $path[2];

	$explode = explode("-", $request);

	$remove_id = array_slice($explode, 1);

	$seostring = implode("-", $remove_id);

	$news_id = $explode[0];

	$find_news = $db->count("SELECT `news_id` FROM `news` WHERE `news_id` = ? AND `seostring` = ?", array($news_id, $seostring));

	if($find_news == 0){

		$page = new page("Jaunumi", array("Jaunumi", "Jaunumi netika atrasti"));

		echo page::alert("Jaunumi ar norādīto adresi netika atrasti!", "danger");

	}else{ ?>

<?php $new = $db->fetch("SELECT * FROM `news` WHERE `news_id` = ? AND `seostring` = ?", array($news_id, $seostring)); ?>

<?php $page = new page("Jaunumi", array("Jaunumi", $new['title'])); ?>

<?php forum::updateViews("news", $news_id); ?>

<div id="post">

    <div class="comment-count">Komentāri: <?php echo addons::returnCommentCount("news", $new['news_id']); ?></div>

    <img src="<?php echo $c['url']; ?>/uploads/news/<?php echo $new['image']; ?>">

    <div class="title"><?php echo $new['title']; ?></div>

    <div class="information">

        Rakstu iesūtīja <?php echo user::formatName($new['author_id'], false, true); ?> @
        <?php echo date("d/m/Y", $new['added']); ?> plkst. <?php echo date("H:i", $new['added']); ?>.

    </div>

    <div class="cont">

        <?php echo text::bbcode($new['text'], array("bbcode" => true, "emoticons" => true, "media" => true)); ?>

    </div>

</div>

<?php addons::returnComments("news", $new['news_id']); ?>

<?php } ?>

<?php else: ?>

<?php $page = new page("Jaunumi", array("Jaunumi")); ?>

<?php

	$news_count = $db->count("SELECT `news_id` FROM `news`");

	list($pager_template, $limit) = page::pagination(2, $news_count, $c['url'] . "/news/page/", 3);

	$news = $db->fetchAll("SELECT * FROM `news` ORDER BY `added` DESC $limit");

	?>

<?php foreach($news as $new): ?>
<div id="post">
    <div class="maintitle"><?php echo $new['title']; ?></div>
    <div class="comment-count">Komentāri: <?php echo addons::returnCommentCount("news", $new['news_id']); ?></div>

    <img src="<?php echo $c['url']; ?>/uploads/news/<?php echo $new['image']; ?>">


    <div class="information">

        Rakstu iesūtīja <?php echo user::formatName($new['author_id'], false, true); ?> @
        <?php echo date("d/m/Y", $new['added']); ?> plkst. <?php echo date("H:i", $new['added']); ?>.

    </div>

    <div class="cont">

        <?php echo text::stripBBCode(text::limit($new['text'], 600)); ?>

        <a href="<?php echo $c['url']; ?>/news/<?php echo $new['news_id'] . "-" . $new['seostring']; ?>/">
            <div class="read-more">Lasīt vairāk...</div>
        </a>

    </div>

</div>

<?php endforeach; ?>

<?php echo $pager_template; ?>

<?php endif; ?>

<div class="block">
    <div id="load_tweets"></div>
</div>