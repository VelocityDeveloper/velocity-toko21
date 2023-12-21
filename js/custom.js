jQuery(function($) {
    $( ".tombols" ).click(function() {
        $("#searchform2").toggle();
        $(".tombols").toggleClass( "collapsed" );
    });

    $(window).scroll(function() {    
        var scroll = $(window).scrollTop();
        if (scroll >= 200) {
            $(".scroll-header").addClass("fixedHeader");
        } else {
            $(".scroll-header").removeClass("fixedHeader");
        }
    });
});

function openNav() {
    document.getElementById("left-nav").style.width = "320px";
    document.getElementsByTagName('body')[0].style.marginLeft = "320px";
}

function closeNav() {
    document.getElementById("left-nav").style.width = "0";
    document.getElementsByTagName("body")[0].style.marginLeft= "0";
}