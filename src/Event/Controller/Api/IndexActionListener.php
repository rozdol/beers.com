<?php
namespace App\Event\Controller\Api;

use App\Event\EventName;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\View\View;
use CsvMigrations\ConfigurationTrait;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

class IndexActionListener extends BaseActionListener
{
    /**
     * Include menus identifier
     */
    const FLAG_INCLUDE_MENUS = 'menus';

    /**
     * Property name for menu items
     */
    const MENU_PROPERTY_NAME = '_Menus';

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::API_INDEX_BEFORE_PAGINATE() => 'beforePaginate',
            (string)EventName::API_INDEX_AFTER_PAGINATE() => 'afterPaginate',
            (string)EventName::API_INDEX_BEFORE_RENDER() => 'beforeRender'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforePaginate(Event $event, Query $query)
    {
        $table = $event->subject()->{$event->subject()->name};
        $request = $event->subject()->request;

        if (!in_array($request->query('format'), [static::FORMAT_PRETTY])) {
            $query->contain($this->_getFileAssociations($table));
        }
        $this->_filterByConditions($query, $event);

        // This is a temporary solution for multi-column sort support,
        // until crud plugin adds relevant functionality.
        // @link https://github.com/FriendsOfCake/crud/issues/522
        // @link https://github.com/cakephp/cakephp/issues/7324
        $sort = $event->subject()->request->query('sort');
        $sort = explode(',', $sort);

        // add sort direction to all columns
        $order = array_fill_keys($sort, $event->subject()->request->query('direction'));
        $query->order($order);
    }

    /**
     * {@inheritDoc}
     */
    public function afterPaginate(Event $event, ResultSet $entities)
    {
        if ($entities->isEmpty()) {
            return;
        }

        $table = $event->subject()->{$event->subject()->name};
        $request = $event->subject()->request;

        foreach ($entities as $entity) {
            $this->_resourceToString($entity);
        }

        if (in_array($request->query('format'), [static::FORMAT_PRETTY])) {
            foreach ($entities as $entity) {
                $this->_prettify($entity, $table);
            }
        } else { // @todo temporary functionality, please see _includeFiles() method documentation.
            foreach ($entities as $entity) {
                $this->_restructureFiles($entity, $table);
            }
        }

        if ((bool)$event->subject()->request->query(static::FLAG_INCLUDE_MENUS)) {
            $this->_includeMenus($entities, $event);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event, ResultSet $entities)
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    protected function _getActionFields(Request $request, $action = null)
    {
        $fields = parent::_getActionFields($request, $action);

        if (empty($fields)) {
            return $fields;
        }

        foreach ($fields as &$field) {
            $field = current((array)$field);
        }

        return $fields;
    }

    /**
     * Method that retrieves and attaches menu elements to API response.
     *
     * @param  \Cake\ORM\ResultSet $entities Entities
     * @param  \Cake\Event\Event   $event    Event instance
     * @return void
     */
    protected function _includeMenus(ResultSet $entities, Event $event)
    {
        $appView = new View();
        $plugin = $event->subject()->request->plugin;
        $controller = $event->subject()->request->controller;
        $displayField = $event->subject()->{$event->subject()->name}->displayField();

        foreach ($entities as $entity) {
            $entity->{static::MENU_PROPERTY_NAME} = $appView->element('CsvMigrations.Menu/index_actions', [
                'plugin' => $event->subject()->request->plugin,
                'controller' => $event->subject()->request->controller,
                'displayField' => $displayField,
                'entity' => $entity,
                'user' => $event->subject()->Auth->user()
            ]);
        }
    }

    /**
     * Method that filters ORM records by provided conditions.
     *
     * @param  \Cake\ORM\Query   $query Query object
     * @param  \Cake\Event\Event $event The event
     * @return void
     */
    protected function _filterByConditions(Query $query, Event $event)
    {
        if (empty($event->subject()->request->query('conditions'))) {
            return;
        }

        $conditions = [];
        $tableName = $event->subject()->name;
        foreach ($event->subject()->request->query('conditions') as $k => $v) {
            if (false === strpos($k, '.')) {
                $k = $tableName . '.' . $k;
            }

            $conditions[$k] = $v;
        };

        $query->applyOptions(['conditions' => $conditions]);
    }
}
