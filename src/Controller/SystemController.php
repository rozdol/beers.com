<?php
namespace App\Controller;

/**
 * System Controller
 */
class SystemController extends AppController
{
    /**
     * Display system information
     *
     * This action displays a variety of useful system
     * information, like project name, URL, version,
     * installed plugins, composer libraries, PHP version,
     * PHP configurations, server environment, etc.
     *
     * @return void
     */
    public function info()
    {
    }

    /**
     * Error method
     *
     * Default redirect method for loggedin users
     * in case the system throws an error on switched off
     * debug. Otherwise, it'll use native Cake Error pages.
     *
     * @return void
     */
    public function error()
    {
    }
}
