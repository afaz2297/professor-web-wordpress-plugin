/* Professor Web — settings screen: tabs, colour pickers, range readouts */
jQuery(function ($) {
  var KEY = 'pwSettingsTab';
  var $tabs = $('.pw-tab');
  var $panels = $('.pw-panel');

  function show(id) {
    $tabs.removeClass('is-active').filter('[data-tab="' + id + '"]').addClass('is-active');
    $panels.removeClass('is-active').filter('[data-panel="' + id + '"]').addClass('is-active');
    try { sessionStorage.setItem(KEY, id); } catch (e) { /* storage blocked */ }
  }

  $tabs.on('click', function () { show($(this).data('tab')); });

  var saved = null;
  try { saved = sessionStorage.getItem(KEY); } catch (e) { /* storage blocked */ }
  if (saved && $tabs.filter('[data-tab="' + saved + '"]').length) show(saved);

  $('.pw-color').wpColorPicker();

  $('.pw-range input').on('input', function () {
    $(this).next('output').text(this.value + '%');
  });
});
