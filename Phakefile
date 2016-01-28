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

			$command = 'clear_cache all';
			doShellCommand([getenv('CAKE_CONSOLE'), $command]);
		});

		desc('Runs CakePHP app update related tasks');
		task('run', ':cakephp:update:clear_cache', function($app) {
			printSeparator();
			printInfo('All CakePHP tasks are completed');
		});

	});

});

# vi:ft=php
?>
