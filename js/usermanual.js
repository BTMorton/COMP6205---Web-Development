function loadVid(id, filename) {
	var holder = document.getElementById(id);
 	embedcode = '<video id=' + id + '" controls autoplay><source src=' + filename + ' type="video/mp4"></video>';
  holder.innerHTML = embedcode;
}


$(function() {   
	$(window).scroll(function() {     
	 if ($(window).scrollTop() >= 165) {
      $('header').addClass('fixed');
   } else {
      $('header').removeClass('fixed');
   }
   
   if ($(window).scrollTop() > ($("#about").offset().top - $("#about h2").height() - 35)) {
   		$("header .nav a:not(.about).active").removeClass("active");
      $("header .nav .about").addClass("active");
   } else if ($(window).scrollTop() > ($("#stories").offset().top - $("#stories h2").height() - 35)) {
      $("header .nav a:not(.stories).active").removeClass("active");
      $("header .nav .stories").addClass("active");
   } else {
      $("header .nav a:not(.tuts).active").removeClass("active");
      $("header .nav .tuts").addClass("active");
   }
 });
});
