<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

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
        $tabs = Configure::read('SystemInfo.tabs');
        $this->set('tabs', $tabs);
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

    /**
     * Action responsible for listing all system searches.
     *
     * @return void
     */
    public function searches()
    {
        $table = TableRegistry::getTableLocator()->get('Search.SavedSearches');
        $query = $table->find()->where(['system' => true]);

        $this->set('entities', $query->all());
    }
}
