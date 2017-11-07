const chromedriver = require('chromedriver');

module.exports = {
  before: function(done) {
    chromedriver.start();
    require('dotenv').config();

    done();
  },

  after: function(done) {
    chromedriver.stop();

    done();
  }
};
