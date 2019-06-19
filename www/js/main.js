$(function() {
  var scroll_btn = $('#scroll-to-top');
  var body = $('html, body');

  showScrollBtn(body, scroll_btn);
  scrolToTop(body, scroll_btn);
});

function scrolToTop(body, scroll_btn) {
  scroll_btn.bind('click', function() {
    body.animate({ scrollTop: 0}, 'fast');
  });
}

function showScrollBtn(body, scrollBtn) {
  $(window).scroll( function() {
    if (body.scrollTop() > 20) {
      scrollBtn.fadeIn();
    } else {
      scrollBtn.fadeOut();
    }
  });
}