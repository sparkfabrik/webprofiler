/**
 * @file
 * Database panel app.
 */
(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.behaviors.webprofiler_database = {
    attach: function (context) {
      // Swap placeholders.
      $('.js--executable-toggle').once().click(function (e) {
        let qid = e.target.dataset.webprofilerQid;
        $('.js--placeholder-query-' + qid).toggleClass('is-hidden');
        $('.js--executable-query-' + qid).toggleClass('is-hidden');
      });

      // Show placeholders.
      $('.js--placeholder-toggle').once().click(function (e) {
        let qid = e.target.dataset.webprofilerQid;
        $('.js--placeholder-target-' + qid).toggleClass('is-hidden');
      });

      // Copy to clipboard.
      $('.js--query-copy').once().click(function (e) {
        let qid = e.target.dataset.webprofilerQid;
        let query = $('.js--executable-query-' + qid)[0].innerText;
        navigator.clipboard.writeText(query);
      });

      // Highlight queries.
      if (typeof hljs != "undefined") {
        hljs.configure({
          ignoreUnescapedHTML: true
        });
        hljs.highlightAll();
      }
    }
  }
})(jQuery, Drupal, drupalSettings);
