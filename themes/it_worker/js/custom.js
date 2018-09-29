jQuery(document).ready(function () {
    //animations();
    fullScreenContainer();
    
});

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
    jQuery("#intro, #intro-wrapper, .frontpage-banner").css({
	width: screenWidth,
	height: screenHeight
    });
}

/* =========================================
 *  animations
 *  =======================================*/

function animations() {

    if (Modernizr.csstransitions) {

    delayTime = 0;
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
    }

}
