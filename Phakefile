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
	task('install', ':cakephp:install');


	desc('Update application');
	task('update', ':builder:init', function($app) {
		printSeparator();
		printInfo("Updating application");
	});
	task('update', ':git:pull', ':git:checkout');
	task('update', ':composer:install');
	task('update', ':dotenv:create', ':dotenv:reload', ':file:process');
	task('update', ':cakephp:update');


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

	desc('Runs CakePHP migrations task');
	task('migrations', ':builder:init', function() {
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
		$command = getenv('CAKE_CONSOLE') . ' plugin migrate';
		doShellCommand($command);
	});

	desc('Runs CakePHP clear cache task');
	task('clear_cache', ':builder:init', function() {
		printSeparator();
		printInfo('Running CakePHP clear cache task');

		$command = getenv('CAKE_CONSOLE') . ' clear_cache all';
		doShellCommand($command);
	});

	/**
	 * 'Grouped CakePHP app update related tasks
	 */
	desc('Runs CakePHP app update related tasks');
	task(
		'update',
		':builder:init',
		':cakephp:clear_cache',
		':cakephp:migrations',
		function($app) {
			printSeparator();
			printInfo('All CakePHP app:update related tasks are completed');
		}
	);

	/**
	 * 'Group CakePHP app install related tasks
	 */
	desc('Runs CakePHP app install related tasks');
	task(
		'install',
		':builder:init',
		':cakephp:migrations',
		function($app) {
			printSeparator();
			printInfo('All CakePHP app:install related tasks are completed');
		}
	);

});

# vi:ft=php
?>
