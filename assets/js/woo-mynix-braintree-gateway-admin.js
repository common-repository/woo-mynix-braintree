(function($) {
$(document).ready(function() {
('function' == typeof $.fn.chosen) && $(".woocommerce-select,.multiselect").chosen();
var selector = $('.form-table .nav-tab-wrapper .nav-tab');
selector.click(function(e) {
selector.removeClass('nav-tab-active');
$(this).toggleClass('nav-tab-active');
$('.mynix-tab').hide();
var anchor = $(this).attr('href'), jumper = anchor.substr(anchor.indexOf('#'));
$(jumper).show();
$('input[name = "active-mynix-tab"]').val(jumper);
return false;
});
$('.nav-tab.nav-mynix-tab').hover(function() {
$('#nav-tab-description').text($(this).attr('data-title'));
}, function() {
$('#nav-tab-description').text('');
});
});
})(jQuery);