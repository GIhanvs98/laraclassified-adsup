
jQuery(document).ready(function(
	) {

	// Scroll to start
	jQuery('.scrolltotop').click(function(){
		jQuery('html').animate({'scrollTop' : '0px'}, 300);
		return false;
	});

	jQuery(window).scroll(function(){
		var upto = jQuery(window).scrollTop();
		if(upto > 500) {
			jQuery('.scrolltotop').fadeIn();
		} else {
			jQuery('.scrolltotop').fadeOut();
		}
	});

// Scroll to end

 //collapse the sidebar
     $('.menu-btn').click(function(){
       $('.sidebar-menu').addClass('active');
     });

     $('.close-btn').click(function(){
       $('.sidebar-menu').removeClass('active');
     });


     //show-and-less
     $(".toggle_btn").click(function(){
     $(this).toggleClass("active");
    $(".wrapper .text-box").toggleClass("active");

    if($(".toggle_btn").hasClass("active")){
      $(".toggle_text").text("Show Less");
    }
    else{
      $(".toggle_text").text("Show More");
    }
  });


  // carousel-1
	$("#owl-csel1").owlCarousel({
		items: 4,
		autoplay: true,
		autoplayTimeout: 4000,
		startPosition: 0,
		rtl: false,
		loop: true,
		margin: 15,
		dots: true,
		nav: true,
		autoplayHoverPause: false,
		navText: [
			'<i class="fa fa-angle-left" aria-hidden="true"></i>',
			'<i class="fa fa-angle-right" aria-hidden="true"></i>'
				],
		navContainer: '.main-content1 .custom-nav',
		responsive:{
			0: {
				items: 1,
									
			},
			767: {
				items: 1,

			},
			991: {
				items: 1,		
					
			},
			1199: {
				items: 1,		
					
			},
			1200: {
				items: 1,

			}
		}

	});

   





//main
});