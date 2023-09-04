jQuery(function () {
    fullScreenContainer();
    animations();   
    //sliders();
    
    addclasstoimage();        
    utils();
    sliding();
    // contactForm();
    // map();
    // counters();
    // parallax();
    // demo();
});

jQuery(window).on('load',function () {
    windowWidth = jQuery(window).width();
    jQuery(this).alignElementsSameHeight();
    masonry();

});
jQuery(window).on('resize',function () {

    newWindowWidth = jQuery(window).width();

    if (windowWidth !== newWindowWidth) {
    setTimeout(function () {
        jQuery(this).alignElementsSameHeight();
        fullScreenContainer();
        waypointsRefresh();
    }, 205);
    windowWidth = newWindowWidth;
    }

});



/* =========================================
 * addingclasstoimage
 *  =======================================*/
 function addclasstoimage() {    
    /*For the round circle image of about me*/
    jQuery("#about .mt-big img").addClass('image img-circle img-responsive');
    /*Feature blog add class to title */
    jQuery(".services .col-md-4 h3").addClass('heading');
    jQuery(".field-feature-blog-info p").addClass('lead');
    jQuery(".field-feature-blog-contact-button a").addClass('btn btn-default btn-lg scrollTo');
    jQuery("#block-blogs h2").addClass('title');
    jQuery("#block-blogs h2").attr('data-animate','fadeInDown');

}

/* =========================================
 *  animations
 *  =======================================*/

function animations() {

    if (Modernizr.csstransitions) {
    delayTime = 0;
    console.log('animating soon');
    jQuery('[data-animate]').css({opacity: '0'});
    jQuery('[data-animate]').waypoint(function (direction) {
        delayTime += 150;
        jQuery(this).delay(delayTime).queue(function (next) {
        jQuery(this).toggleClass('animated');
        jQuery(this).toggleClass(jQuery(this).data('animate'));
        delayTime = 0;
        next();
        //jQuery(this).removeClass('animated');
        //jQuery(this).toggleClass(jQuery(this).data('animate'));
        });
    },
        {
            offset: '95%',
            triggerOnce: true
        });
    jQuery('[data-animate-hover]').hover(function () {
        jQuery(this).css({opacity: 1});
        jQuery(this).addClass('animated');
        jQuery(this).removeClass(jQuery(this).data('animate'));
        jQuery(this).addClass(jQuery(this).data('animate-hover'));
    }, function () {
        jQuery(this).removeClass('animated');
        jQuery(this).removeClass(jQuery(this).data('animate-hover'));
    });
    }else{
       jQuery('[data-animate]').css({opacity: '1'}); 
    }

}

/* =========================================
 *  masonry 
 *  =======================================*/

function masonry() {

    jQuery('#references-masonry').css({visibility: 'visible'});

    jQuery('#references-masonry').masonry({
    itemSelector: '.reference-item:not(.hidden)',
    isFitWidth: true,
    isResizable: true,
    isAnimated: true,
    animationOptions: {
        duration: 200,
        easing: 'linear',
        queue: true
    },
    gutter: 30
    });
    scrollSpyRefresh();
    waypointsRefresh();
}

/* =========================================
 * filter 
 *  =======================================*/

jQuery('#filter a').click(function (e) {
    e.preventDefault();



    jQuery('#filter li').removeClass('active');
    jQuery(this).parent('li').addClass('active');

    var categoryToFilter = jQuery(this).attr('data-filter');

    jQuery('.reference-item').each(function () {
    if (jQuery(this).data('category') === categoryToFilter || categoryToFilter === 'all') {
        jQuery(this).removeClass('hidden');
    }
    else {
        jQuery(this).addClass('hidden');
    }
    });

    if (jQuery('#detail').hasClass('open')) {
    closeReference();
    }
    else {
    jQuery('#references-masonry').masonry('reloadItems').masonry('layout');

    }

    scrollSpyRefresh();
    waypointsRefresh();
});

/* =========================================
 *  open reference 
 *  =======================================*/

jQuery('.reference-item').click(function (e) {
    e.preventDefault();

    var element = jQuery(this);
    var title = element.find('.reference-title').text();
    var description = element.find('.reference-description').html();

    images_list = element.find('.reference-description').data('images') ? element.find('.reference-description').data('images') : '0';
    if(images_list){
    images = images_list.split(',');    
    if (images.length > 0) {
    slider = '';
    for (var i = 0; i < images.length; ++i) {
        slider = slider + '<div class="item"><img src=' + images[i] + ' alt="" class="img-responsive"></div>';
    }
    }
    else {
    slider = '';
    }

    }
    

    


    jQuery('#detail-title').text(title);
    jQuery('#detail-content').html(description);
    jQuery('#detail-slider').html(slider);
    //console.log(jQuery('#detail-slider').html());
    openReference();

});

function openReference() {

    jQuery('#detail').addClass('open');
    jQuery('#references-masonry').animate({opacity: 0}, 300);
    jQuery('#detail').animate({opacity: 1}, 300);

    setTimeout(function () {
    jQuery('#detail').slideDown();
    jQuery('#references-masonry').slideUp();

        if (jQuery('#detail-slider').html() !== '') {
            jQuery('#detail-slider').owlCarousel({
                slideSpeed: 300,
                paginationSpeed: 400,
                autoPlay: true,
                stopOnHover: true,
                singleItem: true,
                afterInit: ''
            });
        }
    }, 300);

    setTimeout(function () {
    jQuery('body').scrollTo(jQuery('#detail'), 1000, {offset: -80});
    }, 500);

}

function closeReference() {

    jQuery('#detail').removeClass('open');
    jQuery('#detail').animate({'opacity': 0}, 300);

    setTimeout(function () {
    jQuery('#detail').slideUp();
    if(jQuery('#detail-slider').data('owlCarousel'))
    {
        jQuery('#detail-slider').data('owlCarousel').destroy();    
    }
    
    jQuery('#references-masonry').slideDown().animate({'opacity': 1}, 300).masonry('reloadItems').masonry();

    }, 300);

    setTimeout(function () {
    jQuery('body').scrollTo(jQuery('#filter'), 1000, {offset: -110});
    }, 500);


    setTimeout(function () {
    jQuery('#references-masonry').masonry('reloadItems').masonry();
    }, 800);

}

jQuery('#detail .close').click(function () {
    closeReference(true);
})

/* =========================================
 * full screen intro
 *  =======================================*/
 function fullScreenContainer() {
    var screenWidth = jQuery(window).width() + "px";
    var screenHeight = '';
    if (jQuery(window).height() > 500) {
       screenHeight = jQuery(window).height() + "px";
   }
   else {
       screenHeight = "500px";
   }
   jQuery("#intro,#block-latte-introduction-2 .field--name-field-greetings-image img").css({
       width: screenWidth,
       height: screenHeight
   });
   jQuery("#banner-wrapper, #block-bloglistbanner .field--name-field-greetings-image img").css({
       width: screenWidth,
       height: '200px'
   });
}



/* =========================================
 * sliding 
 *  =======================================*/

function sliding() {
    jQuery('.scrollTo, #navigation a').click(function (event) {
    event.preventDefault();
    var full_url = this.href;
    var parts = full_url.split("#");
    var trgt = parts[1];

    jQuery('body').scrollTo(jQuery('#' + trgt), 800, {offset: -80});

    });
}

/* =========================================
 *  UTILS
 *  =======================================*/

function utils() {

    /* tooltips */

    jQuery('[data-toggle="tooltip"]').tooltip();

    /* external links in new window*/

    jQuery('.external').on('click', function (e) {

    e.preventDefault();
    window.open(jQuery(this).attr("href"));
    });
    /* animated scrolling */

}

jQuery.fn.alignElementsSameHeight = function () {
    jQuery('.same-height-row').each(function () {

    var maxHeight = 0;
    var children = jQuery(this).find('.same-height');
    children.height('auto');
    if (jQuery(window).width() > 768) {
        children.each(function () {
        if (jQuery(this).innerHeight() > maxHeight) {
            maxHeight = jQuery(this).innerHeight();
        }
        });
        children.innerHeight(maxHeight);
    }

    maxHeight = 0;
    children = jQuery(this).find('.same-height-always');
    children.height('auto');
    children.each(function () {
        if (jQuery(this).height() > maxHeight) {
        maxHeight = jQuery(this).innerHeight();
        }
    });
    children.innerHeight(maxHeight);
    });
}

/* refresh scrollspy */
function scrollSpyRefresh() {
    setTimeout(function () {
    jQuery('body').scrollspy('refresh');
    }, 1000);
}

/* refresh waypoints */
function waypointsRefresh() {
    setTimeout(function () {
    jQuery.waypoints('refresh');
    }, 1000);
}