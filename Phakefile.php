<?php
require_once 'vendor/qobo/phake-builder/Phakefile.php';

/**
 * Get project version
 *
 * @param array $app Application variables
 * @return string
 */
function getProjectVersion($app = null)
{
    $result = null;

    // If we have $app variables, try to figure out version
    if (!empty($app)) {
        // Use GIT_BRANCH variable ...
        $result = getValue('GIT_BRANCH', $app);
        // ... if empty, use git hash
        if (empty($result)) {
            try {
                $git = new \PhakeBuilder\Git(getValue('SYSTEM_COMMAND_GIT', $app));
                $result = doShellCommand($git->getCurrentHash(), null, true);
            } catch (\Exception $e) {
                // ignore
            }
        }
    }

    // ... if empty, use default
    if (empty($result)) {
        $result = 'Unknown';
    }

    return $result;
}

group('app', function () {

    desc('Install application');
    task('install', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: app:install (Install application)");
    });
    task('install', ':dotenv:create', ':dotenv:reload', ':file:process');
    task('install', ':mysql:database-create');
    task('install', ':cakephp:install');

    desc('Update application');
    task('update', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: app:update (Update application)");
    });
    task('update', ':dotenv:create', ':dotenv:reload', ':file:process', ':letsencrypt:symlink');
    task('update', ':cakephp:update');

    desc('Remove application');
    task('remove', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: app:remove (Update application)");
    });
    task('remove', ':dotenv:delete');
    task('remove', ':cakephp:test-database-drop');
    task('remove', ':mysql:database-drop');

    //
    // Save version that we are deploying, both before and after
    //

    after(':builder:init', function ($app) {
        $version = getProjectVersion($app);
        // Save the version that we are deploying
        if (file_exists('build/version')) {
            rename('build/version', 'build/version.bak');
        }
        file_put_contents('build/version', $version);
    });

    after('install', function ($app) {
        $version = getProjectVersion($app);
        // Save the version that we have deployed
        if (file_exists('build/version.ok')) {
            rename('build/version.ok', 'build/version.ok.bak');
        }
        file_put_contents('build/version.ok', $version);
    });

    after('update', function ($app) {
        $version = getProjectVersion($app);
        // Save the version that we have deployed
        if (file_exists('build/version.ok')) {
            rename('build/version.ok', 'build/version.ok.bak');
        }
        file_put_contents('build/version.ok', $version);
    });
});

/**
 * Grouped CakePHP related tasks
 */
group('cakephp', function () {

    desc('Setting folder permissions');
    task('set-folder-permissions', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: cakephp:set-folder-permissions (Setting folder permissions)");
        $dirMode = getValue('CHMOD_DIR_MODE', $app);
        $fileMode = getValue('CHMOD_FILE_MODE', $app);
        $user = getValue('CHOWN_USER', $app);
        $group = getValue('CHGRP_GROUP', $app);

        $paths = [
            'tmp',
            'logs',
            'webroot/uploads',
        ];
        $failures = 0;
        foreach ($paths as $path) {
            $path = __DIR__ . DS . $path;
            if (!file_exists($path)) {
                continue;
            }

            if ($dirMode && $fileMode) {
                try {
                    $result = \PhakeBuilder\FileSystem::chmodPath($path, $dirMode, $fileMode);
                    if (!$result) {
                        throw new \RuntimeException("Failed to change permissions to [$dirMode, $fileMode] on [$path]");
                    }
                } catch (\Exception $e) {
                    $failures++;
                    printWarning($e->getMessage());
                }
            }
            if ($user) {
                try {
                    $result = \PhakeBuilder\FileSystem::chownPath($path, $user);
                    if (!$result) {
                        throw new \RuntimeException("Failed to change user ownership to [$user] on [$path]");
                    }
                } catch (\Exception $e) {
                    $failures++;
                    printWarning($e->getMessage());
                }
            }
            if ($group) {
                try {
                    $result = \PhakeBuilder\FileSystem::chgrpPath($path, $group);
                    if (!$result) {
                        throw new \RuntimeException("Failed to change group ownership to [$group] on [$path]");
                    }
                } catch (\Exception $e) {
                    $failures++;
                    printWarning($e->getMessage());
                }
            }
        }
        printInfo("Set folder permissions has been completed with " . (int)$failures . " warnings.");
    });

    desc('Create CakePHP test database');
    task('test-database-create', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: cakephp:test-database-create (Create CakePHP test database)");

        $dsn = [
            'host' => getValue('DB_HOST', $app),
            'user' => getValue('DB_ADMIN_USER', $app),
            'pass' => getValue('DB_ADMIN_PASS', $app),
        ];

        $mysql = new \PhakeBuilder\MySQL(requireValue('SYSTEM_COMMAND_MYSQL', $app));
        $mysql->setDSN($dsn);
        $command = $mysql->query('CREATE DATABASE IF NOT EXISTS ' . requireValue('DB_NAME', $app) . '_test');
        $secureStrings = ['DB_PASS', 'DB_ADMIN_PASS'];
        doShellCommand($command, $secureStrings);
    });

    desc('Drop CakePHP test database');
    task('test-database-drop', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: cakephp:test-database-drop (Drop CakePHP test database)");

        $dsn = [
            'host' => getValue('DB_HOST', $app),
            'user' => getValue('DB_ADMIN_USER', $app),
            'pass' => getValue('DB_ADMIN_PASS', $app),
        ];

        $mysql = new \PhakeBuilder\MySQL(requireValue('SYSTEM_COMMAND_MYSQL', $app));
        $mysql->setDSN($dsn);
        $command = $mysql->query('DROP DATABASE IF EXISTS ' . requireValue('DB_NAME', $app) . '_test');
        $secureStrings = ['DB_PASS', 'DB_ADMIN_PASS'];
        doShellCommand($command, $secureStrings);
    });

    desc('Run migrations for the test database');
    task('test-migrations', ':builder:init', function ($app) {
        printSeparator();
        printInfo("Task: cakephp:test-migrations (Run migrations for the test database)");

        // setup database
        $dsn = [
            'host' => getValue('DB_HOST', $app),
            'user' => getValue('DB_ADMIN_USER', $app),
            'pass' => getValue('DB_ADMIN_PASS', $app),
        ];

        $mysql = new \PhakeBuilder\MySQL(requireValue('SYSTEM_COMMAND_MYSQL', $app));
        $mysql->setDSN($dsn);

        // drop test database
        printInfo("Dropping test database");
        $command = $mysql->query('DROP DATABASE IF EXISTS ' . requireValue('DB_NAME', $app) . '_test');
        $secureStrings = ['DB_PASS', 'DB_ADMIN_PASS'];
        doShellCommand($command, $secureStrings);

        // create test database
        printInfo("Creating test database");
        $command = $mysql->query('CREATE DATABASE IF NOT EXISTS ' . requireValue('DB_NAME', $app) . '_test');
        $secureStrings = ['DB_PASS', 'DB_ADMIN_PASS'];
        doShellCommand($command, $secureStrings);

        // Run plugin migrations separately for each plugin
        printInfo("Testing plugin migrations");
        $command = getenv('CAKE_CONSOLE') . ' plugin loaded';
        $result = doShellCommand($command);
        $loadedPlugins = (explode("\n", $result));
        foreach ($loadedPlugins as $plugin) {
            printInfo("Testing migration for plugin $plugin");
            $command = getenv('CAKE_CONSOLE') . " migrations migrate --quiet -p $plugin --connection=test";
            printInfo("Command: $command");
            doShellCommand($command);
        }
        // Run app migrations
        printInfo("Testing application migrations");
        $command = getenv('CAKE_CONSOLE') . ' migrations migrate --quiet --connection=test';
        printInfo("Command: $command");
        doShellCommand($command);

        // drop test database
        printInfo("Dropping test database");
        $command = $mysql->query('DROP DATABASE IF EXISTS ' . requireValue('DB_NAME', $app) . '_test');
        $secureStrings = ['DB_PASS', 'DB_ADMIN_PASS'];
        doShellCommand($command, $secureStrings);
    });

    desc('Run CakePHP migrations task');
    task('migrations', ':builder:init', function () {
        printSeparator();
        printInfo("Task: cakephp:migrations (Run CakePHP migrations task)");

        // Run plugin migrations separately for each loaded plugin
        printInfo("Running plugin migrations");
        $command = getenv('CAKE_CONSOLE') . ' plugin loaded';
        printInfo("Command: $command");
        $result = doShellCommand($command);
        $loadedPlugins = (explode("\n", $result));
        foreach ($loadedPlugins as $plugin) {
            printInfo("Running migration for plugin $plugin");
            $command = getenv('CAKE_CONSOLE') . " migrations migrate --quiet -p $plugin";
            printInfo("Command: $command");
            doShellCommand($command);
        }
        // Run app migrations
        printInfo("Running application migrations");
        $command = getenv('CAKE_CONSOLE') . ' migrations migrate --quiet';
        printInfo("Command: $command");
        doShellCommand($command);
    });

    desc('Run CakePHP shell scripts task');
    task('shell-scripts', ':builder:init', function () {
        printSeparator();
        printInfo("Task: cakephp:shell-scripts (Run CakePHP shell scripts task)");

        $scripts = [
            'upgrade',
            'group import',
            'group assign',
            'role import',
            'capability assign',
            'menu import',
            'add_dblist_permissions',
            'dblists_add',
            'validate' // run after dblists are populated
        ];

        foreach ($scripts as $script) {
            $command = getenv('CAKE_CONSOLE') . ' ' . $script . ' --quiet';
            printInfo("Command: $command");
            doShellCommand($command);
        }
    });

    desc('Create dev user');
    task('dev-user-create', ':builder:init', function () {
        printSeparator();
        printInfo("Task: cakephp:dev-user-create (Create dev user)");

        $command = getenv('CAKE_CONSOLE') . ' users addSuperuser';
        $command .= ' --username=' . getenv('DEV_USER');
        $command .= ' --password=' . getenv('DEV_PASS');
        $command .= ' --email=' . getenv('DEV_EMAIL');
        doShellCommand($command);
    });

    desc('Run CakePHP clear cache task');
    task('clear-cache', ':builder:init', function () {
        printSeparator();
        printInfo("Task: cakephp:clear-cache (Run CakePHP clear cache task)");

        $command = getenv('CAKE_CONSOLE') . ' cache clear_all';
        doShellCommand($command);
    });

    /**
     * Grouped CakePHP app install related tasks
     */
    desc('Runs CakePHP app install related tasks');
    task(
        'install',
        ':builder:init',
        ':cakephp:test-migrations',
        ':cakephp:test-database-create',
        ':cakephp:migrations',
        ':cakephp:dev-user-create',
        ':cakephp:shell-scripts',
        ':cakephp:set-folder-permissions',
        function ($app) {
            printSeparator();
            printInfo("Task: cakephp:install (Run CakePHP app install related tasks)");
        }
    );

    /**
     * Grouped CakePHP app update related tasks
     */
    desc('Run CakePHP app update related tasks');
    task(
        'update',
        ':builder:init',
        ':cakephp:test-migrations',
        ':cakephp:test-database-create',
        ':cakephp:clear-cache',
        ':cakephp:migrations',
        ':cakephp:shell-scripts',
        ':cakephp:set-folder-permissions',
        function ($app) {
            printSeparator();
            printInfo("Task: cakephp:update (Run CakePHP app update related tasks)");
        }
    );
});
