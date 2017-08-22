<?php
namespace App\Event\Plugin\Search\Model;

use App\Model\Table\UsersTable;
use Cake\Core\App;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\ORM\Table;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use InvalidArgumentException;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use RolesCapabilities\CapabilityTrait;
use Search\Event\EventName;

class SearchableFieldsListener implements EventListenerInterface
{
    use CapabilityTrait;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::MODEL_SEARCH_SEARCHABLE_FIELDS() => 'getSearchableFields',
            (string)EventName::MODEL_SEARCH_BASIC_SEARCH_FIELDS() => 'getBasicSearchFields',
            (string)EventName::MODEL_SEARCH_DISPLAY_FIELDS() => 'getDisplayFields'
        ];
    }

    /**
     * Method that retrieves target table searchable fields.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\Table $table Table instance
     * @param array $user User info
     * @return void
     */
    public function getSearchableFields(Event $event, Table $table, array $user)
    {
        list($plugin, $controller) = pluginSplit(App::shortName(get_class($table), 'Model/Table', 'Table'));
        $url = [
            'plugin' => $plugin,
            'controller' => $controller,
            'action' => 'search'
        ];
        if (!$this->_checkAccess($url, $user)) {
            return;
        }

        if ($table instanceof UsersTable) {
            $fields = ['first_name', 'last_name', 'username', 'email', 'created', 'modified'];
        } else {
            $method = 'getFieldsDefinitions';
            // skip if method cannot be called
            if (!method_exists($table, $method) || !is_callable([$table, $method])) {
                return;
            }

            $fields = $table->{$method}();
            if (empty($fields)) {
                return;
            }

            $fields = array_keys($fields);
        }

        $fhf = new FieldHandlerFactory();
        $result = [];
        foreach ($fields as $field) {
            $searchOptions = $fhf->getSearchOptions($table, $field);
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
            $mc = new ModuleConfig(ConfigType::MODULE(), $table->registryAlias());
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
            $mc = new ModuleConfig(ConfigType::VIEW(), $module, 'index');
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
