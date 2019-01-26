<div class="clear"></div>
<div id="footermenu">
	<div class="menu">
		<div class="tab">
			<div class="title"><span class="glyphicon glyphicon-file" style="opacity: 0.4;"></span> Kārtībai & Zināšanai</div>
			<li><a href="<?php echo $c['url']; ?>/rules">Noteikumu sadaļa</a></li>
			<li><a href="<?php echo $c['url']; ?>/about">Par POISE.LV</a></li>
			<li><a href="mailto:info@poise.lv"><span style="color: orange;">Brīva vieta Tavam projektam!</span></a></li>
		</div>
		<div class="tab">
			<div class="title"><span class="glyphicon glyphicon-play-circle" style="opacity: 0.4;"></span> Counter-Strike</div>
			<li><a href="<?php echo $c['url']; ?>/forum/topic/8-paraugs-ka-nosudzet-parkapeju">Pamanīji pārkāpēju mūsu serverī?</a></li>
			<li><a href="<?php echo $c['url']; ?>/forum/topic/9-paraugs-bana-apelacija">Vēlies apsūdzēt savu banu?</a></li>
			<li><a href="<?php echo $c['url']; ?>/forum/topic/10-paraugs-sudziba-par-adminu">Admins nepilda savus pienākumus?</a></li>
		</div>
		<div class="tab">
			<div class="title"><span class="glyphicon glyphicon-globe" style="opacity: 0.4;"></span> Sociālie tīkli</div>
			<li><a href="http://twitter.com/poise_lv" target="_blank">Seko mums @ Twitter</a></li>
			<li><a href="http://draugiem.lv" target="_blank">Seko mums @ Draugiem</a></li>					
			<li><a href="http://ask.fm/poise_lv" target="_blank">Uzdod jautājumu @ Ask.fm</a></li>
		</div>
		<div class="tab">
			<div class="title"><span class="glyphicon glyphicon-phone-alt" style="opacity: 0.4;"></span> Sazināties ar administrāciju</div>
			<li>E-pasts: <a href="mailto:info@poise.lv">info@poise.lv</a></li>
			<li>Projekta daļa (Skype): <a href="skype:arvilszb?chat">NunCe</a></li>
			<li>Tehniskā daļa (Skype): <a href="skype:miksrolands?chat">LuckyBeer</a></li>
		</div>
		<div class="clear"></div>
	</div>
</div>
</div>

<div id="footer">
	<div class="left">
		© 2014 - <?php echo date("Y"); ?>. Tiesības tiek paturētas.
	</div>
	<div class="right">
		<?php if(user::isLoggedIn()): ?>
			<a class="pointer" style="color:#f1f1f1;" data-toggle="modal" data-target="#reportBugWindow">Redzi kļūdu vai vēlies kaut ko ieteikt?</a>
		<?php endif; ?>
		<?php
		$time = microtime();
		$time = explode(" ", $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $start), 4);
		?>
		lapa ielādēta <?php echo $total_time; ?> sekundēs		</div>		<br /><br />	
		<div class="goToTop"></div>
	</div>

	<?php if(user::isLoggedIn()): ?>
		<div class="modal fade" id="sendMessageWindow" tabindex="-1" role="dialog" aria-labelledby="sendMessageLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Atcelt"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="sendMessageLabel"></h4>
					</div>
					<div class="modal-body">
						<div class="form modal-form">
							<div id="messageErrors"></div>
							<form method="POST" id="sendMessage">
								<label for="name" class="required">Saņēmējs</label>
								<input type="hidden" name="receiver_id" id="receiver_id">
								<input type="text" class="control" placeholder="Saņēmējs" id="receiver" disabled>

								<label for="title" class="required">Ziņas temats</label>
								<input type="text" name="title" class="control" placeholder="Ziņas temats">

								<label for="message" class="required">Ziņa</label>
								<textarea type="text" name="message" id="editor" class="control" placeholder="Ziņa" rows="10"></textarea>

								<button type="submit" class="blue">Sūtīt ziņu</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="reportBugWindow" tabindex="-1" role="dialog" aria-labelledby="reportBugLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Atcelt"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="reportBugLabel"></h4>
					</div>
					<div class="modal-body">
						<div class="form modal-form">
							<div id="reportBugErrors"></div>
							<form method="POST" id="reportBug">
								<label for="description" class="required">Kļūdas vai ieteikuma apraksts</label>
								<textarea type="text" name="description" id="editor" class="control" placeholder="Kļūdas vai ieteikuma apraksts..." rows="10"></textarea>

								<button type="submit" class="blue">Nosūtīt ziņojumu</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="warningsWindow" tabindex="-1" role="dialog" aria-labelledby="warningsLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Atcelt"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="warningsLabel"></h4>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<div id="vote">
		<script src="http://wos.lv/d.php?33375"></script>
	</div>
	<script src="<?php echo $c['url']; ?>/assets/js/jquery.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/bootstrap.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/fancybox.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/editor.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/cookie.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/browser.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/iframe-auto-height.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/tinycolor.min.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/bootstrap.colorpickersliders.min.js"></script>
	<script src="<?php echo $c['url']; ?>/assets/js/page.js"></script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-61315138-1', 'auto');
		ga('send', 'pageview');
	</script>
</body>
</html>