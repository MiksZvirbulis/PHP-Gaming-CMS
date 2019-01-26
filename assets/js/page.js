/* Miks Zvirbulis */
$(document).ready(function(){
	var url = window.location;
	reloadmonitors();
	load_tweets();
	$(".fancybox").fancybox({
		"transitionIn" : "fade",
		"transitionOut" : "fade",
		"speedIn" : 100,
		"speedOut" : 100,
		"type": "image"

	}); 
	$("#nav ul a[href='"+ url +"']").parent().addClass("active");
	$("#nav ul li").filter(function() {
		return this.href == url;
	}).parent().addClass("active");
	var larger_li = $("li.larger").width();
	$("li.larger").css("width", larger_li + 10);
	if(window.location.hash){
		$("html, body").animate({ scrollTop: $(window.location.hash).offset().top }, 1000);
	}

	var emoticons = {":)":"smile",":D":"laugh",":P":"tongue",";)":"wink",":(":"sad",";(":"cry",":B":"cool",":@":"angry",":*":"kiss","(xmas)":"xmas","(flag_lv)":"flag_lv","(devil)":"devil","(sun)":"sun","(pool)":"pool","(giggle)":"giggle","(happy)":"happy","(run)":"run","(tree)":"tree","(tiger)":"tiger","(sik)":"sick","(punch)":"punch","(love)":"love","(hug)":"hug","(ninja)":"ninja","(halloween)":"halloween","(fool)":"fool","(cool)":"cool","(cash)":"cash","(valdis)":"valdis","(pray)":"pray","(music)":"music","(hot)":"hot","(heart)":"heart","(gaper)":"gaper","(car)":"car","(bat)":"bat"};
	var emoticonArray = [];
	for(var key in emoticons){
		if(emoticons.hasOwnProperty(key)){
			emoticonArray.push({img: '<img src="/assets/images/emoticons/' + emoticons[key] + '.gif" class="sm">', bbcode: key});
		}
	}

	var bbOptions = {
		buttons: "bold,italic,underline,|,img,link,|,justifyleft,justifycenter,justifyright,bullist,fontcolor,smilebox,custom_quote",
		smileList: emoticonArray
	}
	$("#editor").wysibb(bbOptions);

	loadShouts();

	window.setInterval(function(){
		loadShouts();
	}, 15000);

	checkMessages();

	window.setInterval(function(){
		checkMessages();
	}, 30000);

	loadInfo();

	window.setInterval(function(){
		loadInfo();
	}, 30000);

	loader();

	jQuery("iframe.auto").iframeAutoHeight({minHeight: 1000});

	if(window.location.hash && window.location.hash == "#reportBug"){
		$("#reportBugWindow").modal("toggle")
	}

	$("input#smallhsv").ColorPickerSliders({
		size: "sm",
		placement: "right",
		swatches: false,
		sliders: false,
		hsvpanel: true
	});

	$("input#smallhsv").on("propertychange", function() {
		alert($(this).val());
	});
});

$.fn.scrollView = function(){
	return this.each(function(){
		$("html, body").animate({
			scrollTop: $(this).offset().top
		}, 500);
	});
}

function loadInfo(){
	$.getJSON("/public/ajax/info.php", function (data){
		$.each(data, function(index, item){
			$(".info_bar .text").hide().html(item.Text).fadeIn("slow");
			$(".info_bar .updated").hide().html("Atjaunoja " + item.Author + " " + item.Updated).fadeIn("slow");
		});
	});
}

$("#sendMessageWindow").on("show.bs.modal", function (event){
	var button = $(event.relatedTarget)
	var receiver_id = button.data("receiver")
	var receiver_name = button.data("name")
	var modal = $(this)
	modal.find(".modal-title").text("Sūtīt ziņu lietotājam " + receiver_name)
	modal.find(".modal-body input#receiver").val(receiver_name)
	modal.find(".modal-body input#receiver_id").val(receiver_id)
});

$("#reportBugWindow").on("show.bs.modal", function (event){
	var button = $(event.relatedTarget)
	var modal = $(this)
	modal.find(".modal-title").text("Reportēt kļūdu")
});

$("form#reportBug").on("submit", function(e) {
	var dataString = $("form#reportBug").serialize();
	$.ajax({
		type: "POST",
		url: "/public/ajax/reportBug.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned) {
		if(returned == "success"){
			$("form#reportBug").hide();
			$("div.form.modal-form div#reportBugErrors").html('<div class="alert alert-success" style="font-size: 11px;">Kļūda reportēta!</div>');
			setTimeout(function(){
				$("#reportBugWindow").modal("hide");
				$("form#reportBug").show();
				$("form#reportBug input").val("");
				$("form#reportBug textarea").val("");
				$("div.form.modal-form div#reportBugErrors").hide();
			}, 3000); 
		}else{
			$("div.form.modal-form div#reportBugErrors").html(returned).fadeIn("slow");
		}
	});
	return false;
});

$("span#warnings").click(function(){
	var user_id = $(this).data("user")
	var dataString = { user_id: user_id }; 
	$.ajax({
		type: "POST",
		url: "/public/ajax/loadWarnings.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned){
		var modal = $("#warningsWindow")
		modal.find(".modal-title").text("Brīdinājuma punkti")
		modal.find(".modal-body").html(returned)
		$("#warningsWindow").modal({ show: true });
		refreshForms();
	});
});

$("form#sendMessage").on("submit", function(e) {
	var dataString = $("form#sendMessage").serialize();
	$.ajax({
		type: "POST",
		url: "/public/ajax/sendMessage.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned) {
		if(returned == "success"){
			$("form#sendMessage").hide();
			$("div.form.modal-form div#messageErrors").html('<div class="alert alert-success" style="font-size: 11px;">Ziņa veiksmīgi nosūtīta!</div>');
			setTimeout(function(){
				$("#sendMessageWindow").modal("hide");
				$("form#sendMessage").show();
				$("form#sendMessage input").val("");
				$("form#sendMessage textarea").val("");
				$("div.form.modal-form div#messageErrors").hide();
			}, 3000); 
		}else{
			$("div.form.modal-form div#messageErrors").html(returned).fadeIn("slow");
		}
	});
	return false;
});

function refreshForms(){
	$("form#addWarning").on("submit", function(e) {
		var dataString = $("form#addWarning").serialize();
		$.ajax({
			type: "POST",
			url: "/public/ajax/addWarning.php",
			data: dataString,
			cache: false,
			async: false
		}).done(function(returned) {
			if(returned == "success"){
				$("form#addWarning").hide();
				$("div.form.modal-form div#warningErrors").html('<div class="alert alert-success" style="font-size: 11px;">Lietotājs veiksmīgi brīdināts! Uzgaidi...</div>');
				setTimeout(function(){
					location.reload();
				}, 3000); 
			}else{
				$("div.form.modal-form div#warningErrors").html(returned).fadeIn("slow");
			}
		});
		return false;
	});
}

$("#monitor-side .refresh").click(function(){
	reloadmonitors();
});

$(".goToTop").click(function(){
	$("html, body").animate({scrollTop: 0}, "slow");
});

$("a.confirm").on("click", function () {
	return confirm("Vai esi pārliecināts, ka vēlies turpināt?");
});

function quote(author_name, post_id){
	html_content = $("#editor").htmlcode();
	$("#editor").htmlcode(html_content + '<blockquote><div id="quote2"><div class="head">' + author_name + ' rakstīja:</div><div class="quote">' + $("div#post_" + post_id).html() + '</div></div></blockquote>');
	$("#reply").scrollView();
}

function loader(){
	$(".load").show().fadeOut(3000);
}

function reloadmonitors(){
	$("#monitoring .server").html('<center style="height: 44px; line-height: 44px;"><img src="/assets/images/loading.gif"></center>').each(function(){
		$(this).load("/system/draw/monitor.php?server="+$(this).attr("id")+"");
	});
	window.setTimeout('reloadmonitors', 5000);
}

function load_tweets(){
	$("#load_tweets").html('<center style="height: 44px; line-height: 44px;"><img src="/assets/images/loading.gif"></center>').each(function(){
		$(this).load("/system/draw/tweets.php");
	});
	window.setTimeout('load_tweets', 5000);
}

function highlight(field){
	field.focus();
	field.select();
	return true;
}

$("#duplicate-answer").click(function(){
	var answers = $(".answer").length;
	if(answers < 10){
		$("#answer").clone().attr("id", "answer" + $(".answer").length).val("").insertAfter(".answer:last");
	}else{
		$(".alert.alert-danger.acpinfo").html("Atļautais atbilžu skaits ir 10!").show();
	}
});

$("a.back").click(function(){
	var $link = document.referrer;
	document.location.assign($link);
});

$("input#poll_reload").click(function(){
	setTimeout(function(){
		location.reload();
	}, 1000);
});

function at(element){
	var name = $(element).html();
	var original_text = $("textarea").val();
	$("textarea").focus().val(original_text + name + " ");
}

function avatarType(type){
	if($(type).val() == "custom"){
		$("#custom").show();
	}else{
		$("#custom").hide();
	}
}

function checkMessages(){
	$.ajax({
		type: "POST",
		url: "/public/ajax/checkMessages.php",
		cache: false
	}).done(function(returned) {
		if(returned == "yes"){
			$("span#checkMessages").addClass("blink_me");
		}
	});
}

$("form#addShout").on("submit", function(e) {
	e.preventDefault()
	var dataString = $("form#addShout").serialize();
	$.ajax({
		type: "POST",
		url: "/public/ajax/addShout.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned) {
		if(returned == "success"){
			loadShouts();
			$("div#shoutError").fadeOut("slow").html("");
			$("form#addShout input").val("");
		}else{
			$("div#shoutError").html(returned).fadeIn("slow");
		}
	});
	return false;
});

function loadShouts(){
	if($("div#shoutbox").length != 0){
		var dataString = { safe: true }; 
		$.ajax({
			type: "POST",
			url: "/public/ajax/loadShouts.php",
			data: dataString,
			cache: false,
			async: false
		}).done(function(returned) {
			$("div#shoutbox").html(returned).fadeIn("slow");
		});
		loader();
	}
}

function deleteShout(shout_id){
	if(confirm("Esi pārliecināts?") == true){
		var dataString = { shout_id: shout_id };
		$.ajax({
			type: "POST",
			url: "/public/ajax/deleteShout.php",
			data: dataString,
			cache: false,
			async: false
		}).done(function(x) {
			loadShouts();
		});
	}
}

function shoutAt(username){
	var original_text = $("input[name=shout]").val();
	$("input[name=shout]").focus().val(original_text + "@" + username + " ");
}

function addEmoticon(emoticon){
	var original_text = $("form#addShout input").val();
	$("form#addShout input").focus().val(original_text + " " + emoticon + " ");
}

function toggleEmoticons(){
	$("span#emoticonBox").toggleClass("show");
}

function loadBan(bid){
	var dataString = { bid: bid };
	$.ajax({
		type: "POST",
		url: "/public/ajax/loadBan.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(x) {
		$("div#ban").html(x);
	});
}

function deleteBan(bid){
	if(confirm("Vai tiešām vēlies noņemt šo banu?")){
		var dataString = { bid: bid };
		$.ajax({
			type: "POST",
			url: "/public/ajax/deleteBan.php",
			data: dataString,
			cache: false,
			async: false
		}).done(function(x) {
			if(x == "success"){
				location.reload();
			}else{
				alert(x);
			}
		});
	}else{
		return false;
	}
}

function selectAll(element){
	$(element).select();
}

function updatePrice(element){
	value = $(element).val();
	$("div#price").html("Lai saņemtu atslēgas kodu, sūti kodu <strong>BTM" + value + "</strong> uz numuru <strong>144</strong>");
}

$("form#checkAdmin").on("submit", function(e) {
	var dataString = $("form#checkAdmin").serialize();
	$.ajax({
		type: "POST",
		url: "/public/ajax/checkAdmin.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned) {
		$("div#results").html(returned);
	});
	return false;
});

$("form#editAdmin").on("submit", function(e) {
	e.preventDefault();
	button = $(this).find("button");
	var dataString = $("form.editAdmin_" + button.data("id")).serialize();
	$.ajax({
		type: "POST",
		url: "/public/ajax/editAdmin.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(returned) {
		$("div#alert_" + button.data("id")).html(returned);
	});
	return false;
});

function loadRules(id){
	var dataString = { id: id };
	$.ajax({
		type: "POST",
		url: "/public/ajax/loadRules.php",
		data: dataString,
		cache: false,
		async: false
	}).done(function(x) {
		$("div#rules").html(x);
	});
}