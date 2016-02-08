function extensionSuggestions(q, cb) {
  $.get("extension-suggest.php").then(function(result) {
    var out = [];
    result.forEach(function(extension) {
      out.push(extension.extension + "@" + extension.domain_name);
    });
  });
}

$('.extension').typeahead({
  hint: true,
  highlight: true,
  minLength: 1
}, {
  name: 'extensions',
  source: extensionSuggestions
});
