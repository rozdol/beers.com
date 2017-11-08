describe('UsersController::login(): ', () => {
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

    it('Testing [successfull] login', (browser) => {
        browser
            .login(process.env.DEV_USER, process.env.DEV_PASS)
            .assert.elementPresent('nav');
    });
});
