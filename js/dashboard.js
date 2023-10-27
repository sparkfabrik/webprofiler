/**
 * @file
 * Main dashboard script.
 */
(function (Drupal, drupalSettings) {
  Drupal.behaviors.webprofiler_dashboard = {
    attach(context) {
      // Automatically open the panel if the URL contains the query parameter.
      once('opener', '.webprofiler__collectors', context).forEach(function () {
        const { path } = drupalSettings;

        if (path.currentQuery && 'panel' in path.currentQuery) {
          const { panel } = path.currentQuery;
          const panel_link = document.querySelector(
            `.webprofiler__collectors [data-collector-name='${panel}']`,
          );
          panel_link.click();
          panel_link.parentNode.className += ' active';
        }
      });
    },
  };
})(Drupal, drupalSettings);
