jQuery(function() {
  var scrollBtn = $('#scroll-to-top');
  var body = $('html, body');

  showScrollBtn(body, scrollBtn);
  scrolToTop(body, scrollBtn);
});

function scrolToTop(body, scrollBtn) {
  scrollBtn.bind('click', function() {
    body.animate({ scrollTop: 0}, 'fast');
  });
}

function showScrollBtn(body, scrollBtn) {
  console.log(scrollBtn)
  if (!scrollBtn || scrollBtn === null) {
    return
  }

  jQuery(window).scroll( function() {
    if (body.scrollTop() > 20) {
      scrollBtn.fadeIn();
    } else {
      scrollBtn.fadeOut();
    }
  });
}