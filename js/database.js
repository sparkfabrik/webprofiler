/**
 * @file
 * Database panel app.
 */
(function (Drupal) {

  "use strict";

  const queryTpl = _.template(`
    <div>
        <dl>
            <dt>Time</dt>
            <dd><% print(time); %> ms</dd>
            <dt>Caller</dt>
            <dd><% print(caller); %></dd>
            <dt>Database</dt>
            <dd><% print(db); %></dd>
            <dt>Target</dt>
            <dd><% print(target); %></dd>
        </dl>
    </div>

    <% if (hasArgs == 1) {%><a class="wp-executable-toggle">Swap placeholders</a><%}%>
    <a class="wp-query-copy">Copy query</a>
    <% if (type == "SELECT") {%><a href="<% print(explainPath); %>" class="use-ajax">Explain</a><%}%>

    <div class="js--explain-target-<% print(qid); %>"></div>
  `);

  Drupal.behaviors.webprofiler_database = {
    attach: function (context) {
      hljs.configure({
        ignoreUnescapedHTML: true
      });

      once('db', '.wp-db-query').forEach(function (element) {
        let result =
          queryTpl({
            'time': element.dataset.wpTime,
            'caller': element.dataset.wpClass,
            'db': element.dataset.wpDb,
            'target': element.dataset.wpTarget,
            'hasArgs': element.dataset.wpHasArgs,
            'type': element.dataset.wpType,
            'qid': element.dataset.wpQid,
            'explainPath': element.dataset.wpExplainPath
          });

        element.innerHTML += result;

        element.querySelectorAll('code').forEach(function (code) {
          hljs.highlightElement(code);
        });

        // Swap placeholders.
        if (element.dataset.wpHasArgs === '1') {
          element.querySelector('.wp-executable-toggle').addEventListener('click', function (e) {
            element.querySelector('.wp-query-placeholder').classList.toggle('is-hidden');
            element.querySelector('.wp-query-executable').classList.toggle('is-hidden');
          });
        }

        // Copy to clipboard.
        if (navigator.clipboard && window.isSecureContext) {
          element.querySelector('.wp-query-copy').addEventListener('click', function (e) {
            let query = element.querySelector('.wp-query-executable').innerText;
            navigator.clipboard.writeText(query);
          });
        }
        else {
          element.querySelector('.wp-query-copy').classList.toggle('is-hidden');
        }
      });
    }
  }
})(Drupal);
