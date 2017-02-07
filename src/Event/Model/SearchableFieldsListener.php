<?php
namespace App\Event\Model;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\DbField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

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
            'Search.Model.Search.searchabeFields' => 'getSearchableFields'
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
        $method = 'getFieldsDefinitions';
        // skip if method cannot be called
        if (!method_exists($table, $method) || !is_callable([$table, $method])) {
            return;
        }

        $this->_fhf = new FieldHandlerFactory();

        $allFields = $table->{$method}();
        if (empty($allFields)) {
            return;
        }

        $result = [];
        foreach ($allFields as $field => $definition) {
            $options = $this->_fhf->getSearchOptions($table, $field);
            if (!empty($options)) {
                $result = array_merge($result, $options);
            }
        }

        $event->result = $result;
    }
}
