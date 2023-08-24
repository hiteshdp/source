// call jRespond and add breakpoints
$(document).ready(function(){
    //Radio Click Content
    $('.radio-container label').addClass('inactive');
    $('.desc').hide();
    $('.desc:first').show();
        
    $('.radio-container label').click(function(){
        var t = $(this).attr('id');
    if($(this).hasClass('inactive')){ //this is the start of our condition 
        $('.radio-container label').addClass('inactive');           
        $(this).removeClass('inactive');
        
        $('.desc').hide();
        $('#'+ t + 'C').fadeIn('slow');
        $(this).parent().addClass('active').siblings().removeClass('active');
    }
    });
});
$(document).ready(function(){
	$('.slider-mob').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		dots: true,
		autoplay: true,
		autoplaySpeed: 2000,
		responsive: [
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: 1,
				dots: true,
				arrows: false,
				centerMode: false,
				centerPadding: '0px',
				slidesToShow: 1
			  }
			},
			{
			  breakpoint: 480,
			  settings: {
				slidesToShow: 1,
				arrows: false,
				centerMode: false,
				centerPadding: '0px',
				slidesToShow: 1
			  }
			}
		  ]
	});
	
  });
  $(document).ready(function(){
	$('.slider-ariticles-mob').slick({
		slidesToShow: 4,
		slidesToScroll: 1,
		dots: true,
		autoplay: true,
		autoplaySpeed: 2000,
		responsive: [
			{
			  breakpoint: 1199,
			  settings: {
				slidesToShow: 1,
				dots: true,
				arrows: false,
				centerMode: false,
				centerPadding: '0px',
				slidesToShow: 1
			  }
			},
			{
			  breakpoint: 480,
			  settings: {
				slidesToShow: 1,
				arrows: false,
				centerMode: false,
				centerPadding: '0px',
				slidesToShow: 1
			  }
			}
		  ]
	});
	
	$('.gallery-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		arrows: false,
		dots: true,
		adaptiveHeight: true
	});

	$('.slider-mobile').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		arrows: false,
		dots: true
	});

	$('.product-slider').slick({
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		arrows: false,
		dots: false,
		responsive: [
			{
				breakpoint: 1199,
				settings: {
				  slidesToShow: 3,
				  dots: false,
				  arrows: false
				}
			  },
			  {
				breakpoint: 1024,
				settings: {
				  slidesToShow: 3,
				  dots: false,
				  arrows: false
				}
			  },
			{
			  breakpoint: 767,
			  settings: {
				slidesToShow: 1,
				dots: true,
				arrows: false
			  }
			},
			{
			  breakpoint: 480,
			  settings: {
				slidesToShow: 1,
				arrows: false,
				dots: true
			  }
			}
		  ]
	});

	$('.resource-slider').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 2000,
		arrows: false,
		dots: true,
		adaptiveHeight: true
	});
  });
  

  // Init AOS
  function aos_init() {
    AOS.init({
      duration: 1000,
      once: true
    });
  }
  $(document).ready(function(){
    aos_init();
  });
  $("a[href^='#its']").click(function(e) {
	e.preventDefault();
	
	var position = $($(this).attr("href")).offset().top;

	$("body, html").animate({
		scrollTop: position
	} /* speed */ );
});