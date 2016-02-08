$(document).ready(function() {
  $('.extension').typeahead(null, {
    name: 'extensions',
    source:  new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        url: 'extension-suggest.php?q=%QUERY',
        wildcard: '%QUERY'
      },
    }),
    suggestion: Handlebars.compile('<div><strong>{{extension}}</strong>@{{domain_name}}</div>')
  });
});
