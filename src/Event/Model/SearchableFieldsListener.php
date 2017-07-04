<?php
namespace App\Event\Model;

use App\Model\Table\UsersTable;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\DbField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use InvalidArgumentException;
use Qobo\Utils\ModuleConfig\ModuleConfig;

class SearchableFieldsListener implements EventListenerInterface
{
    /**
     * Field handler factory instance.
     *
     * @var \CsvMigrations\FieldHandlers\FieldHandlerFactory
     */
    protected $_fhf;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Search.Model.Search.searchabeFields' => 'getSearchableFields',
            'Search.Model.Search.basicSearchFields' => 'getBasicSearchFields',
            'Search.Model.Search.displayFields' => 'getDisplayFields'
        ];
    }

    /**
     * Method that retrieves target table searchable fields.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\Table $table Table instance
     * @return void
     */
    public function getSearchableFields(Event $event, Table $table)
    {
        if ($table instanceof UsersTable) {
            $event->result = $this->_getUsersSearchableFields($table);

            return;
        }

        $method = 'getFieldsDefinitions';
        // skip if method cannot be called
        if (!method_exists($table, $method) || !is_callable([$table, $method])) {
            return;
        }

        $this->_fhf = new FieldHandlerFactory();

        $fields = $table->{$method}();
        if (empty($fields)) {
            return;
        }

        $result = [];
        foreach ($fields as $field => $definition) {
            $searchOptions = $this->_fhf->getSearchOptions($table, $field);
            if (empty($searchOptions)) {
                continue;
            }

            $options = [];
            foreach ($searchOptions as $k => $v) {
                $options[$table->aliasField($k)] = $v;
            }
            $result = array_merge($result, $options);
        }

        $event->result = $result;
    }

    /**
     * Returns searchable fields for Users module.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return array
     */
    protected function _getUsersSearchableFields(Table $table)
    {
        $searchFields = [
            'string' => [
                'fields' => [
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'username' => 'Username',
                    'email' => 'Email'
                ],
                'operators' => [
                    'contains' => [
                        'label' => 'contains',
                        'operator' => 'LIKE',
                        'pattern' => '%{{value}}%'
                    ]
                ]
            ],
            'datetime' => [
                'fields' => [
                    'created' => 'Created',
                    'modified' => 'Modified'
                ],
                'operators' => [
                    'is' => [
                        'label' => 'is',
                        'operator' => 'IN',
                    ]
                ]
            ]
        ];

        $result = [];
        foreach ($searchFields as $type => $properties) {
            foreach ($properties['fields'] as $k => $v) {
                $result[$table->aliasField($k)] = [
                    'type' => $type,
                    'label' => $v,
                    'operators' => $properties['operators']
                ];
            }
        }

        return $result;
    }

    /**
     * Method that retrieves target table basic search fields.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\Table $table Table instance
     * @return void
     */
    public function getBasicSearchFields(Event $event, Table $table)
    {
        $result = $this->_getBasicSearchFieldsFromConfig($table);

        if (empty($result)) {
            $result = $this->_getBasicSearchFieldsFromView($table);
        }

        foreach ($result as &$field) {
            $field = $table->aliasField($field);
        }

        $event->result = $result;
    }

    /**
     * Method that retrieves target table search funcionality display fields.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\Table $table Table instance
     * @return void
     */
    public function getDisplayFields(Event $event, Table $table)
    {
        $result = $this->_getBasicSearchFieldsFromView($table);

        foreach ($result as &$field) {
            $field = $table->aliasField($field);
        }

        $event->result = $result;
    }

    /**
     * Returns basic search fields from provided Table's configuration.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return array
     */
    protected function _getBasicSearchFieldsFromConfig(Table $table)
    {
        $config = [];
        try {
            $mc = new ModuleConfig(ModuleConfig::CONFIG_TYPE_MODULE, $table->registryAlias());
            $config = $mc->parse();
            $config = json_decode(json_encode($config), true);
        } catch (InvalidArgumentException $e) {
            Log::error($e);
        }

        $result = [];
        if (!empty($config['table']['basic_search_fields'])) {
            $result = array_filter(array_map('trim', $config['table']['basic_search_fields']), 'strlen');
        }

        return $result;
    }

    /**
     * Returns basic search fields from provided Table's index View csv fields.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return array
     */
    protected function _getBasicSearchFieldsFromView(Table $table)
    {
        $config = [];
        try {
            list($plugin, $module) = pluginSplit($table->registryAlias());
            $mc = new ModuleConfig(ModuleConfig::CONFIG_TYPE_VIEW, $module, 'index');
            $config = $mc->parse();
            $config = !empty($config->items) ? json_decode(json_encode($config->items), true) : [];
        } catch (InvalidArgumentException $e) {
            Log::error($e);
        }

        if (empty($config)) {
            return [];
        }

        $result = [];
        foreach ($config as $column) {
            $result[] = $column[0];
        }

        return $result;
    }
}
