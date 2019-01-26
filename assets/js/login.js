$(document).ready(function(){
    var images = ["/assets/images/login/background.png", "/assets/images/login/background2.png", "/assets/images/login/background3.png", "/assets/images/login/background4.png", "/assets/images/login/background5.png", "/assets/images/login/background6.png", "/assets/images/login/background7.png", "/assets/images/login/background8.png"];
    var i = 0;
    function changeBackground() {
        $("html, body").stop().animate({opacity: 0.7}, 1000, function(){
            if(i >= images.length){
                i = 0;
            }
            $(this).css("background-image", "url(" + images[i++] + ")").animate({opacity: 1}, {duration: 1000});;
        });
    }
    changeBackground();
    setInterval(changeBackground, 10000);
    loadmonitors();
});

function loadmonitors(){
    $("#monitoring .server").each(function(){
        $(this).load("/sys/draw/login_cs_monitor.php?server="+$(this).attr("id")+"");
    });
    window.setTimeout("loadmonitors", 5000);
}