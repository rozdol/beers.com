project-template-cakephp
========================

[![Build Status](https://travis-ci.org/QoboLtd/project-template-cakephp.svg?branch=master)](https://travis-ci.org/QoboLtd/project-template-cakephp)
[![Latest Stable Version](https://poser.pugx.org/qobo/project-template-cakephp/v/stable)](https://packagist.org/packages/qobo/project-template-cakephp)
[![Total Downloads](https://poser.pugx.org/qobo/project-template-cakephp/downloads)](https://packagist.org/packages/qobo/project-template-cakephp)
[![Latest Unstable Version](https://poser.pugx.org/qobo/project-template-cakephp/v/unstable)](https://packagist.org/packages/qobo/project-template-cakephp)
[![License](https://poser.pugx.org/qobo/project-template-cakephp/license)](https://packagist.org/packages/qobo/project-template-cakephp)
[![codecov](https://codecov.io/gh/QoboLtd/project-template-cakephp/branch/master/graph/badge.svg)](https://codecov.io/gh/QoboLtd/project-template-cakephp)

About
-----

This is a template for the new CakePHP projects.

Developed by [Qobo](https://www.qobo.biz), used in [Qobrix](https://qobrix.com).

Install
-------

There are two ways to install and start using this project template.

### Composer Project

You can create a new project from this template using composer.

```bash
composer create-project qobo/project-template-cakephp example.com
cd example.com
git init
git add .
git commit -m "Initial commit"
./bin/build app:install DB_NAME=my_app,PROJECT_NAME="My Project",PROJECT_VERSION="v1.0.0"
```

### Git

Alternatively, you can start using this project by cloning the git repository
and changing remote origin.

```bash
git clone https://github.com/QoboLtd/project-template-cakephp.git example.com
cd example.com
./bin/build app:install DB_NAME=my_app,PROJECT_NAME="My Project",PROJECT_VERSION="v1.0.0"
git remote remove origin
git remote add origin git@github.com:YOUR-VENDOR/YOUR-REPOSITORY.git
git push origin master
```

Update
------

When you want to update your project with the latest
and greatest project-template-cakephp, do the following:

```
cd exmample.com
git pull https://github.com/QoboLtd/project-template-cakephp
```

The above will only work if you installed it via the git clone.  For composer
project installations you might need to add `--allow-unrelated-histories`
parameter.

Usage
-----

### Quick

Now that you have the project template installed, check that it works
before you start working on your changes.  Fire up the PHP web server:

```
./bin/phpserv
```

Or run it on the alternative port:

```
./bin/phpserv -H localhost -p 9000
```

In your browser navigate to [http://localhost:8000](http://localhost:8000).
You should see the standard `phpinfo()` page.  If you do, all parts
are in place.


Now you can develop your PHP project as per usual, but with the following
advantages:

* Support for [PHP built-in web server](http://php.net/manual/en/features.commandline.webserver.php)
* Per-environment configuration using `.env` file, which is ignored by git
* Powerful build system ([Robo](http://robo.li/)) integrated
* Composer integrated with `vendor/` folder added to `.gitignore` .
* PHPUnit integrated with `tests/` folder and example unit tests.
* Sensible defaults for best practices - favicon.ico, robots.txt, MySQL dump, Nginx configuration, GPL, etc.

For example, you can easily automate the build process of your application
by modifying the included Robo files in `build/` folder.  Run the following
command to examine available targets:

```
./bin/build
```

As you can see, there are already some placeholders for your application's build
process.  By default, it is suggested that you have these:

* `app:install` - for installation process of your application,
* `app:update` - for the update process of the already installed application, and
* `app:remove` - for the application removal process and cleanup.

You can, of course, add your own, remove these, or change them any way you want.  Have a look at
[Robo](http://robo.li) documentation for more information on how
to use these targets and pass runtime configuration parameters.


Test
----

### PHPUnit

project-template-cakephp brings quite a bit of setup for testing your projects.  The
first part of this setup is [PHPUnit](https://phpunit.de/).  To try it out,
runt the following command (don't worry if it fails, we'll get to it shortly):

```
./vendor/bin/phpunit
```

If it didn't work for you, here are some of the things to try:

* If `phpunit` command wasn't found, try `composer install` and then run the command again.  Chances are phpunit was removed during the `app:install`, which runs composer with `--no-dev` parameter.
* If you had some other issue, please [let us know](https://github.com/QoboLtd/project-template-cakephp/issues/new).

### Travis CI

Continious Integration is a tool that helps to run your tests whenever you do any
changes on your code base (commit, merge, etc).  There are many tools that you can
use, but project-template-cakephp provides an example integration with [Travis CI](https://travis-ci.org/).

Have a look at `.travis.yml` file, which describes the environment matrix, project installation
steps and ways to run the test suite.  For your real project, based on project-template-cakephp, you'd probably
want to remove the example tests from the file.

### Examples

project-template-cakephp provides a few examples of how to write and organize unit tests.  Have a look
in the `tests/` folder.  Now you have **NO EXCUSE** for not testing your applications!

Known Issues
------------

### MySQL 5.7 / MariaDB 10

Running this project template with MySQL 5.7 or MariaDB 10 sometimes causes fatal
errors with SQL queries.  We are looking for better ways to catch and fix those, but
if you encounter them in your environment, please adjust your MySQL / MariaDB
configuration for SQL mode.

SQL mode can be adjusted by either setting it in the `etc/my.cnf` file like so:

```
[mysqld]
sql-mode="NO_ZERO_IN_DATE,NO_ZERO_DATE"
```

Or, alternatively, via an SQL query, like so:

```
mysql > SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
```

See [this StackOverflow
thread](https://stackoverflow.com/questions/23921117/disable-only-full-group-by) for
more information.

Here are a few examples of the issues and how to fix them.

```
Error: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime
value: '2017-11-30T13:30:48+02:00' for column 'timestamp' at row 1
```

If you encounter the above error (often seen on Fedora 27 or above), make sure
your SQL mode includes "NO_ZERO_IN_DATE,NO_ZERO_DATE".

```
Error: SQLSTATE[42000]: Syntax error or access violation: 1055 Expression #1 of
SELECT list is not in GROUP BY clause and contains nonaggregated column ...
```

If you encounter the above error (often seen on Ubuntu 16.05), make sure
your SQL mode DOES NOT include "ONLY_FULL_GROUP_BY".


## Custom installation

```bash
git clone https://github.com/QoboLtd/project-template-cakephp.git beers.com
cd beers.com
composer install
./bin/build app:install DB_NAME=beers_com,PROJECT_NAME="Beers",PROJECT_VERSION="v1.0.0",CHOWN_USER=admin,CHGRP_GROUP=staff,DEBUG=1
#./bin/build app:remove #rollback DB and .env if error above

./bin/phpserv

./bin/cake bake csv_module Beers

#edit ./config/Modules/Beers/...
./bin/cake validate

./bin/cake bake csv_migration Beers

./bin/cake migrations migrate
./bin/build app:update

open -e /usr/local/etc/my.cnf
## add sql-mode="NO_ZERO_IN_DATE,NO_ZERO_DATE"

./bin/cake bake csv_module Bars
./bin/cake validate

./bin/cake bake csv_migration Bars
./bin/cake migrations migrate
./bin/build app:update

./bin/cake bake csv_relation 

./bin/cake bake csv_migration BarsBeers

./bin/cake migrations migrate



./vendor/bin/phpcs
./vendor/bin/phpcbf

./vendor/bin/phpunit
```

Admin->Settings->System_search (Remove dates, add fielsd, test, save)

add to `/usr/local/etc/my.cnf`
```conf
[mysqld]
sql-mode="NO_ZERO_IN_DATE,NO_ZERO_DATE"
```

```bash
mysql.server start
#git remote remove origin
#git remote add origin git@github.com:YOUR-VENDOR/YOUR-REPOSITORY.git
#git push origin master
```

### API button

1.`src/Radom/Test.php`
2. Add func-call from Controller (Request->Router->Controller->Model->Ext.Lib->Model)->Controller->View->Responce)


```bash
curl -H "Content-Type: application/json" -H "Accept: application/json" -X POST -d '{"username":"alex","password":"1234"}' http://localhost:8000/api/users/token.json

curl -H "Content-Type: application/json" -H "Accept: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJmNzk1MDM2Ny1mY2IxLTQyMWYtYWU5ZC1jY2FkODhjYmM0OTYiLCJleHAiOjE1Mjg1NTg2MTV9.kLURIJ3VGgb-Veg9twI-kAg-MVm2gTokcZ_DnUXjvdU" -X GET http://localhost:8000/api/beers.json

curl -H "Content-Type: application/json" -H "Accept: application/json" -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJmNzk1MDM2Ny1mY2IxLTQyMWYtYWU5ZC1jY2FkODhjYmM0OTYiLCJleHAiOjE1Mjg1NTg2MTV9.kLURIJ3VGgb-Veg9twI-kAg-MVm2gTokcZ_DnUXjvdU" -X GET http://localhost:8000/api/beers/get-date.json
```

### Compose library

```bash
mkdir numop
cd numop
composer init
git init
git add composer.json
git commit -m 'Initial Composer'
mkdir src
```

Copy `src`

```bash
git add src
git commit -m 'Added initial source'
```

add to `composer.json`

```
	"autoload": {
        "psr-4": {
            "Rozdol\\": "src"
        }
    }
```

```bash
git add composer.json
git commit -m 'add autoloader'
git push origin master
```
Create github repo

```bash
git remote add origin git@github.com:rozdol/numop.git
git push origin master
```


Login to https://packagist.org/
Submit https://github.com/rozdol/numop

on github add new release (v1.0.0)
On packagist Update Package


cd to beers.com


```bash
composer require rozdol/numop:"v1.*"
```

in Table
```php
use Rozdol\Number\Test;
```


#### Connecting Github to Packagist

In Github->Settings->Integrations..->Add->Packagist
user: packagist user
api_key: packagist->User->Profile->Show API KEY
Domain: https://packagist.org

Test: New reslease in Github and check the version in Packagist


### Unit Tests

install local phpunit
```bash
composer require --dev phpunit/phpunit ^6
```
```bash
mkdir tests
cd tests
mkdir TestCase
cd TestCase
mkdir Number
```
edit `TestTest.php`

`cd beers.com`
```bash
composer update rozdol/numop
```

To test
```bash
./vendor/bin/phpunit tests/
```

### Travis CI

```bash
git checkout -b travis
git add .travis.yml
git push origin travis
```

##### Detactach from original source
```bash
git remote -v
git remote remove origin
git remote add origin git@github.com:rozdol/beers.com.git
git push origin random_changes
git push origin random_changes
```


# Update to latest project-template-cakephp

git remote -v
git log master..HEAD
git checkout master
git checkout -b updating
git remote -v
git remote add upstream --no-tags https://github.com/QoboLtd/project-template-cakephp.git
git remote -v
git remote update
git pull https://github.com/QoboLtd/project-template-cakephp.git v39.2.1
composer update
git status
git add composer.lock
git commit -m 'Explain'
bin/build app:update


Got err
./bin/cake 'validate'
(find and correct error)
bin/cake bake csv_migration BarsBeers
bin/build app:update
git add config
git commit -m 'fix validation error'

git checkout master
git merge updating
git status
git branch -d updating
git checkout random_changes
git status
git merge master
(got conflicts)
git status
rm composer.lock
composer update

git add composer.lock
git commit
:wq

4 ways to solve conflicts:
1. git checkout --ours composer.lock # Use our version
2. git checkout --theirs composer.lock # Use their version
3. edit composer.lock # manual conflict resolution
4. rm composer.lock && composer update # only for automatic gen files.
git add composer.lock
git commit
:wq










