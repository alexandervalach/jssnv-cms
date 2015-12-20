/* Navigation sliding effect */

//$('.slideToggle').find('ul').slideUp();

$(document).ready(function(){
    $('li.slideToggle').bind('click', function(){
        $(this).find('ul').slideToggle();
        return false;
    });
    $('li.slideToggle ul').bind('click', function(e){
        e.stopPropagation();
    });
});