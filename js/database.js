/**
 * @file
 * Database panel app.
 */
(function (Drupal) {

  "use strict";

  Drupal.behaviors.webprofiler_database = {
    attach: function (context) {
      // Swap placeholders.
      once('executable-toggle', '[data-webprofiler-executable-toggle]', context).forEach(function (element) {
        element.addEventListener('click', function (e) {
          let qid = e.target.dataset.webprofilerQid;
          document.querySelector("[data-webprofiler-placeholder-query='"+qid+"']").classList.toggle('is-hidden');
          document.querySelector("[data-webprofiler-executable-query='"+qid+"']").classList.toggle('is-hidden');
        });
      });

      // Show placeholders.
      once('placeholder-toggle', '[data-webprofiler-placeholder-toggle]', context).forEach(function (element) {
        element.addEventListener('click', function (e) {
          let qid = e.target.dataset.webprofilerQid;
          document.querySelector("[data-webprofiler-placeholder-target='"+qid+"']").classList.toggle('is-hidden');
        });
      });

      // Copy to clipboard.
      if (navigator.clipboard && window.isSecureContext) {
        once('query-copy', '[data-webprofiler-copy]', context).forEach(function (element) {
          element.addEventListener('click', function (e) {
            let qid = e.target.dataset.webprofilerQid;
            let query = document.querySelector("[data-webprofiler-executable-query='" + qid + "']").innerText;
            navigator.clipboard.writeText(query);
          });
        });
      }
      else {
        once('query-copy', '[data-webprofiler-copy]', context).forEach(function (element) {
          element.classList.toggle('is-hidden');
        });
      }
    }
  }
})(Drupal);
