<?php
namespace App\Event\Controller\Api;

use App\Event\EventName;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;

class IndexActionListener extends BaseActionListener
{
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
    public function beforePaginate(Event $event, QueryInterface $query)
    {
        $request = $event->subject()->request;

        if (static::FORMAT_PRETTY !== $event->subject()->request->getQuery('format')) {
            $query->contain(
                $this->_getFileAssociations($event->subject()->{$event->subject()->name})
            );
        }

        $this->filterByConditions($query, $event);

        $query->order($this->getOrderClause(
            $event->getSubject()->request,
            $event->getSubject()->{$event->getSubject()->name}
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function afterPaginate(Event $event, ResultSetInterface $resultSet)
    {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function beforeRender(Event $event, ResultSetInterface $resultSet)
    {
        if ($resultSet->isEmpty()) {
            return;
        }

        $table = $event->getSubject()->{$event->getSubject()->name};

        foreach ($resultSet as $entity) {
            $this->_resourceToString($entity);
        }

        if (static::FORMAT_PRETTY === $event->getSubject()->request->getQuery('format')) {
            foreach ($resultSet as $entity) {
                $this->_prettify($entity, $table);
            }
        }

        // @todo temporary functionality, please see _includeFiles() method documentation.
        if (static::FORMAT_PRETTY !== $event->getSubject()->request->getQuery('format')) {
            foreach ($resultSet as $entity) {
                $this->_restructureFiles($entity, $table);
            }
        }

        if ((bool)$event->getSubject()->request->getQuery(static::FLAG_INCLUDE_MENUS)) {
            $this->attachMenu($resultSet, $table, $event->getSubject()->Auth->user());
        }
    }

    /**
     * Method that filters ORM records by provided conditions.
     *
     * @param \Cake\Datasource\QueryInterface $query Query object
     * @param \Cake\Event\Event $event The event
     * @return void
     */
    private function filterByConditions(QueryInterface $query, Event $event)
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
