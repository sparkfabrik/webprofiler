module.exports = {
  '@tags': ['webprofiler'],
  before(browser) {
    browser
      .drupalInstall()
      .drupalInstallModule('webprofiler', true)
      .setWindowSize(2000, 2000) // Required otherwise the toolbar may be in front of the permission checkbox.
      .drupalCreateUser({
        name: 'user',
        password: '123',
        permissions: ['view webprofiler toolbar'],
      })
      .drupalLogin({ name: 'user', password: '123' });
  },
  after(browser) {
    browser
      .drupalUninstall();
  },
  'The toolbar is visible on the front page with status 200': (browser) => {
    browser
      .drupalRelativeURL('/')
      .waitForElementVisible('.sf-toolbar', 1000)
      .assert.textContains('.sf-toolbar-status', '200');
  },
  'The toolbar is visible on a not found page with status 404': (browser) => {
    browser
      .drupalRelativeURL('/page-not-found')
      .waitForElementVisible('.sf-toolbar', 1000)
      .assert.textContains('.sf-toolbar-status', '404');
  },
};
