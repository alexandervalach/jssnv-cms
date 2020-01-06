/* Navigation sliding effect */

//$('.slideToggle').find('ul').slideUp();

$(document).ready(function(){
    $('li.slideToggle').each(function (){
        $(this).find('ul').css("display", "none");
    });
    
    $('li.slideToggle').bind('click', function(){
        $(this).find('ul').not(':animated').slideToggle();
        return false;
    });
    $('li.slideToggle ul').bind('click', function(e){
        e.stopPropagation();
    });
});