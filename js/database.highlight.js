/**
 * @file
 * Database panel app.
 */
(function (Drupal) {

  "use strict";

  Drupal.behaviors.webprofiler_database_highlight = {
    attach: function (context) {
      // Dynamically download highlightjs.
      once('database', '.webprofiler__panel').forEach(function (element) {
        loadjs(
          [
            'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.0/highlight.min.js',
            'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.0/styles/default.min.css'
          ],
          'highlight'
        );

        loadjs.ready('highlight', function() {
          // Highlight queries.
          if (typeof hljs != "undefined") {
            hljs.configure({
              ignoreUnescapedHTML: true
            });
            hljs.highlightAll();
          }
        });
      });
    }
  }
})(Drupal);
