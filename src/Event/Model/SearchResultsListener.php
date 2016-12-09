<?php
namespace App\Event\Model;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\ResultSet;
use Cake\ORM\Table;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

class SearchResultsListener implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Search.Model.Search.afterFind' => 'afterFind'
        ];
    }

    /**
     * Method that adds elements to index View top menu.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\ResultSet $entities the ResultSet
     * @param \Cake\ORM\Table $table  Table instance
     * @return \Cake\ORM\ResultSet
     */
    public function afterFind(Event $event, ResultSet $entities, Table $table)
    {
        $fhf = new FieldHandlerFactory();

        foreach ($entities as $entity) {
            $properties = $entity->visibleProperties();
            foreach ($properties as $property) {
                $entity->{$property} = $fhf->renderValue($table, $property, $entity->{$property});
            }
        }

        $event->result = $entities;

        return $event->result;
    }
}
