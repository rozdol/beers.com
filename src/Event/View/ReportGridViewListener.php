<?php
namespace App\Event\View;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Network\Exception\ForbiddenException;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

class ReportGridViewListener implements EventListenerInterface
{
    /**
     * @return array of implemented events for sets module
     */
    public function implementedEvents()
    {
        return [
            'Search.Dashboard.Widget.GridElement' => 'processFieldValue',
        ];
    }

    /**
     *  processFieldValue method
     *
     *  Returns back processed field's value as per its DB type
     *
     * @param Cake\Event\Event $event of the current request
     * @param string $model model name
     * @param string $field field name
     * @param string $value field value
     * @return string processed field value
     */
    public function processFieldValue(Event $event, $model, $field, $value, $options = [])
    {
        $result = '';

        $fhf = new FieldHandlerFactory();
        $result = $fhf->renderValue($model, $field, $value);

        return $result;
    }
}
