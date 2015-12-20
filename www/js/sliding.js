/* Navigation sliding effect */

//$('.slideToggle').find('ul').slideUp();

$('.slideToggle').bind('click', function(e){
    e.preventDefault();
    $(this).find('ul').slideToggle();
});





