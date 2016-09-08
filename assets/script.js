(function(){

  var wrapper =jQuery('.uxgal-slideshow-wrapper');

  var total =  wrapper.find('img').length;

  jQuery('.slideshow-total').html(total);

  var slider = wrapper.slick({
    arrows: false,
    dots: false,
    infinite: false
  });

  jQuery('.nav-next').click(function(e){
      e.preventDefault();
      wrapper.slick('slickNext');
   });

   jQuery('.nav-prev').click(function(e){
       e.preventDefault();
       wrapper.slick('slickPrev');
    });

    wrapper.on('afterChange', function(slick, currentSlide){
      jQuery('.uxgal-slideshow-current').html(currentSlide.currentSlide+1);
    });

    jQuery('#slideshow_skip a').on('focus', function(){
      var that = jQuery(this);
      that.parent().addClass('active');
    });
}());
