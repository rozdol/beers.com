describe('Testing login UsersController::login() method', () => {
    var loginUrl = 'http://localhost:8000/login';

    before(function(browser, done) {
      done();
    });

    after(function(browser, done) {
      browser.end(function() {
        done();
      });
    });

    afterEach(function(browser, done) {
      done();
    });

    beforeEach(function(browser, done) {
      done();
    });

    it('gets [login] page', (browser) => {

        browser
            .url(loginUrl)
            .waitForElementVisible('.login-box').present;
    });

    it('trying to [login]', (browser) => {
        browser
            .url(loginUrl)
            .waitForElementVisible('.login-box', 2000)
            .assert.elementPresent('input#username')
            .setValue('input#username', 'qobo')
            .assert.elementPresent('input#password')
            .setValue('input#password', 'qobo')
            .submitForm('form')
            .pause(2000)
            .assert.elementPresent('nav');
    });
});
