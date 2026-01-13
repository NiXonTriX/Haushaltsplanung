$(function () {
  const $panel = $("#filterPanel");
  const $btn = $("#toggleFilters");

  $panel.on("shown.bs.collapse", function () {
    $btn.attr("aria-expanded", "true");
    $btn.html('<i class="fas fa-filter mr-1" aria-hidden="true"></i> Filter ausblenden');
  });

  $panel.on("hidden.bs.collapse", function () {
    $btn.attr("aria-expanded", "false");
    $btn.html('<i class="fas fa-filter mr-1" aria-hidden="true"></i> Filter einblenden');
  });
});
