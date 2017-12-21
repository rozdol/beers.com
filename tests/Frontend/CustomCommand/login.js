exports.command = function(username, password, callback) {
    var self = this;
    var loginUrl = 'http://localhost:8000/login';

    this.frame(null)
        .url(loginUrl)
        .waitForElementPresent('input#username', 2000)
        .setValue('input#username', username)
        .waitForElementPresent('input#password', 2000)
        .setValue('input#password', password)
        .submitForm('form')
        .pause(3000)
        .assert.elementPresent('nav')

    if (typeof callback === "function") {
        callback.call(self);
    }

    return this;
};
