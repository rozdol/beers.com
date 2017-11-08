describe('UsersController::register(): ', () => {
    var registerUrl = 'http://localhost:8000/users/register';

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

    it('Register form is present', (browser) => {
        browser
            .url(registerUrl)
            .assert.elementPresent('form', 2000)
            .assert.attributeEquals('form', 'action', registerUrl);
    });

    it('Submitting empty register form', (browser) => {
        browser
            .url(registerUrl)
            .assert.elementPresent('form')
            .submitForm('form')
            .assert.elementPresent('section.content-header .alert-danger')
    });
});
