$(function() {
	$(window).scroll(function() {
        if ($(window).scrollTop() >= 165) {
            $('header').addClass('fixed');
        } else {
            $('header').removeClass('fixed');
        }
        
        /*if ($(window).scrollTop() > ($("#about").offset().top - $("#about h2").height() - 35)) {
            $("header .nav a:not(.about).active").removeClass("active");
            $("header .nav .about").addClass("active");
        } else if ($(window).scrollTop() > ($("#stories").offset().top - $("#stories h2").height() - 35)) {
            $("header .nav a:not(.stories).active").removeClass("active");
            $("header .nav .stories").addClass("active");
        } else if ($(window).scrollTop() > ($("#tutorials").offset().top - $("#tutorials h2").height() - 35)) {
            $("header .nav a:not(.tuts).active").removeClass("active");
            $("header .nav .tuts").addClass("active");
        } else {
            $("header .nav a:not(.intro).active").removeClass("active");
            $("header .nav .intro").addClass("active");
        }*/
        $("#wrapper .container").each(function() {
            if ($(window).scrollTop() > ($(this).offset().top - $("h2", this).height() - 35)) {
                $("header .nav a:not([href=#" + this.id + "_jump]).active").removeClass("active");
                $("header .nav a[href=#" + this.id + "_jump]").addClass("active");
            }
        });
    });

    $(".stories .video").click(function() {
        if ($("video", this).length <= 0) {
            $(this).html('<video controls autoplay><source src="' + $(this).attr('data-filename') + '" type="video/mp4" /></video>');
            $("video", this).on('ended', function() {
                $(this).remove();
            });
        } else if ($("video", this)[0].paused == false) {
            $("video", this)[0].pause();
        } else {
            $("video", this)[0].play();
        }
    });
});
