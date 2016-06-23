<?php
require_once 'vendor/qobo/phake-builder/Phakefile.php';

group('app', function() {

	desc('Install application');
	task('install', ':builder:init', function($app) {
		printSeparator();
		printInfo("Installing application");
	});
	task('install', ':dotenv:create', ':dotenv:reload', ':file:process');
	task('install', ':mysql:database-create');
	task('install', ':cakephp:test-database-create');
	task('install', ':cakephp:test-database-migrate');
	task('install', ':cakephp:install');

	desc('Update application');
	task('update', ':builder:init', function($app) {
		printSeparator();
		printInfo("Updating application");
	});
	task('update', ':dotenv:create', ':dotenv:reload', ':file:process');
	task('update', ':cakephp:update');

	desc('Remove application');
	task('remove', ':builder:init', function($app) {
		printSeparator();
		printInfo("Removing application");
	});
	task('remove', ':dotenv:delete');
	task('remove', ':mysql:database-drop');
	task('remove', ':cakephp:test-database-drop');

});

/**
 * Grouped CakePHP related tasks
 */
group('cakephp', function() {

	desc('Setting folder permissions');
	task('set-folder-permissions', ':builder:init', function($app) {
		$dirMode = getValue('CHMOD_DIR_MODE', $app);
		$fileMode = getValue('CHMOD_FILE_MODE', $app);
		$user = getValue('CHOWN_USER', $app);
		$group = getValue('CHGRP_GROUP', $app);

		$paths = [
			'tmp',
			'logs',
			'webroot/uploads',
		];
		foreach($paths as $path) {
			$path = __DIR__ . DS . $path;
			if (file_exists($path)) {
				$result = \PhakeBuilder\FileSystem::chmodPath($path, $dirMode, $fileMode);
				if (!$result) {
					throw new \RuntimeException("Failed to change permissions");
				}
				$result = \PhakeBuilder\FileSystem::chownPath($path, $user);
				if (!$result) {
					throw new \RuntimeException("Failed to change user ownership");
				}
				$result = \PhakeBuilder\FileSystem::chownPath($path, $group);
				if (!$result) {
					throw new \RuntimeException("Failed to change group ownership");
				}
			}
		}
		printInfo('Set folder permissions has been completed.');
	});

	desc('Creates CakePHP test database');
	task('test-database-create', ':builder:init', function($app) {
		printSeparator();
		printInfo('Creating test database.');

		$dbTestName = requireValue('DB_NAME', $app) . '_test';
		$query = "CREATE DATABASE " . $dbTestName;
		doMySQLCommand($app, $query, false, true);
	});

	desc('Deletes the existing CakePHP test database');
	task('test-database-drop', ':builder:init', function($app) {
		printSeparator();
		printInfo('Dropping test database.');

		$dbTestName = requireValue('DB_NAME', $app) . '_test';
		$query = "DROP DATABASE " . $dbTestName;
		doMySQLCommand($app, $query, false, true);
	});

	desc('Migrates migrations to the test database');
	task('test-database-migrate', ':builder:init', function($app) {
		printSeparator();
		printInfo('Migrating to the test database.');

		$command = getenv('CAKE_CONSOLE') . ' migrations migrate --connection=test';
		doShellCommand($command);
	});


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
		$command = getenv('CAKE_CONSOLE') . ' plugin migrations migrate';
		doShellCommand($command);
	});

	desc('Create dev user');
	task('dev-user-create', ':builder:init', function() {
		printSeparator();
		printInfo('Creating dev user');

		$command  = getenv('CAKE_CONSOLE') . ' users addUser';
		$command .= ' --username=' . getenv('DEV_USER');
		$command .= ' --password=' . getenv('DEV_PASS');
		$command .=' --email=' . getenv('DEV_EMAIL');
		doShellCommand($command);
	});

	desc('Runs CakePHP clear cache task');
	task('clear-cache', ':builder:init', function() {
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
		':cakephp:clear-cache',
		':cakephp:migrations',
		':cakephp:set-folder-permissions',
		function($app) {
			printSeparator();
			printInfo('All CakePHP app:update related tasks are completed');
		}
	);

	/**
	 * 'Grouped CakePHP app install related tasks
	 */
	desc('Runs CakePHP app install related tasks');
	task(
		'install',
		':builder:init',
		':cakephp:migrations',
		':cakephp:dev-user-create',
		':cakephp:set-folder-permissions',
		function($app) {
			printSeparator();
			printInfo('All CakePHP app:install related tasks are completed');
		}
	);

});
