<?php
require_once 'vendor/qobo/phake-builder/Phakefile';

group('app', function() {

	desc('Install application');
	task('install', ':builder:init', function($app) {
		printSeparator();
		printInfo("Installing application");
	});
	task('install', ':git:pull', ':git:checkout');
	task('install', ':dotenv:create', ':dotenv:reload', ':file:process');


	desc('Update application');
	task('update', ':builder:init', function($app) {
		printSeparator();
		printInfo("Updating application");
	});
	task('update', ':git:pull', ':git:checkout');
	task('update', ':composer:install');
	task('update', ':dotenv:create', ':dotenv:reload', ':file:process');
	task('update', ':cakephp:update:run');


	desc('Remove application');
	task('remove', ':builder:init', function($app) {
		printSeparator();
		printInfo("Removing application");
	});
	task('remove', ':dotenv:delete');

});

/**
 * Grouped CakePHP related tasks
 */
group('cakephp', function() {

	/**
	 * 'Grouped CakePHP app update related tasks
	 */
	group('update', function() {

		desc('Runs CakePHP clear cache task');
		task('clear_cache', function() {
			printSeparator();
			printInfo('Running CakePHP clear cache task');

			$command = getenv('CAKE_CONSOLE') . ' clear_cache all';
			doShellCommand($command);
		});

		desc('Runs CakePHP migrations task');
		task('migrations', function() {
			printSeparator();
			printInfo('Running CakePHP migrations task');

			/**
			 * shell command for running application migrations
			 * @var string
			 */
			$command = getenv('CAKE_CONSOLE') . ' migrations migrate';
			doShellCommand($command);

			/**
			 * shell command for running loaded plugins migrations
			 * @var string
			 */
			$command = getenv('CAKE_CONSOLE') . ' qobo_plugin migrate';
			doShellCommand($command);
		});

		desc('Runs CakePHP app update related tasks');
		task(
			'run',
			':cakephp:update:clear_cache',
			':cakephp:update:migrations',
			function($app) {
				printSeparator();
				printInfo('All CakePHP tasks are completed');
			}
		);

	});

});

# vi:ft=php
?>
