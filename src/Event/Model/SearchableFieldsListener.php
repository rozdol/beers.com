<?php
namespace App\Event\Model;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Table;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\DbField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

class SearchableFieldsListener implements EventListenerInterface
{
    /**
     * Searchable parameter name
     */
    const PARAM_NON_SEARCHABLE = 'non-searchable';

    /**
     * Skipped field types
     *
     * @var array
     */
    protected $_skipTypes = ['uuid'];

    protected $_combinedTypes = [
        'money',
        'metric'
    ];

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
        $result = [];
        $method = 'getFieldsDefinitions';
        // skip if method cannot be called
        if (!method_exists($table, $method) || !is_callable([$table, $method])) {
            return;
        }

        $fhf = new FieldHandlerFactory();

        foreach ($table->{$method}($table->alias()) as $field) {
            // skip non-searchable fields and fields with type defined in _skipTypes array
            if ($field[static::PARAM_NON_SEARCHABLE] || in_array($field['type'], $this->_skipTypes)) {
                continue;
            }

            $csvField = new CsvField($field);
            $dbFields = $fhf->fieldToDb($csvField);

            // get field names from DbField objects
            foreach ($dbFields as $dbField) {
                $type = $csvField->getType();
                $propMethod = '_get' . ucfirst($type) . 'Properties';
                if (method_exists($this, $propMethod)) {
                    $result[$dbField->getName()] = $this->{$propMethod}($table, $csvField, $dbField);
                } else {
                    $result[$dbField->getName()] = ['type' => $type];
                }
            }
        }

        $event->result = $result;
    }

    /**
     * Method that retrieves list field searchable properties.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @param \CsvMigrations\FieldHandlers\CsvField $csvField CsvField instance
     * @param \CsvMigrations\FieldHandlers\DbField $dbField DbField instance
     * @return array
     */
    protected function _getListProperties(Table $table, CsvField $csvField, DbField $dbField)
    {
        return [
            'type' => $csvField->getType(),
            'fieldOptions' => $table->_getSelectOptions($csvField->getLimit())
        ];
    }
}
