<?php
namespace App\Crud\Action;

use Cake\Core\App;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Crud\Action\BaseAction;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\SerializeTrait;
use Crud\Traits\ViewTrait;
use Crud\Traits\ViewVarTrait;
use InvalidArgumentException;

/**
 * Handles 'Related' Crud actions
 */
class RelatedAction extends BaseAction
{
    use FindMethodTrait;
    use SerializeTrait;
    use ViewTrait;
    use ViewVarTrait;

    /**
     * Default settings for 'related' actions
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'table',
        'findMethod' => 'all',
        'view' => null,
        'viewVar' => null,
        'serialize' => [],
        'api' => [
            'success' => [
                'code' => 200
            ],
            'error' => [
                'code' => 400
            ]
        ]
    ];

    /**
     * Generic handler for all HTTP verbs
     *
     * @param string $id Record id
     * @param string $associationName Association name
     * @return void
     */
    protected function _handle($id, $associationName)
    {
        $subject = $this->_subject([
            'success' => true,
            'query' => $this->getQuery($id, $associationName)
        ]);

        $this->_trigger('beforePaginate', $subject);
        $items = $this->_controller()->paginate($subject->query);
        $subject->set(['entities' => $items]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);
    }

    /**
     *
     * @param string $id Record id
     * @param string $associationName Association name
     * @return \Cake\Datasource\QueryInterface|null
     * @throws \InvalidArgumentException When reversed many-to-many association is not found
     */
    private function getQuery($id, $associationName)
    {
        $association = $this->getAssociation($associationName);

        if (is_null($association)) {
            throw new InvalidArgumentException(
                sprintf('%s has no "%s" association', $this->_table()->getAlias(), $associationName)
            );
        }

        if (Association::MANY_TO_MANY === $association->type()) {
            return $this->manyToManyQuery($association, $id);
        }

        if (Association::ONE_TO_MANY === $association->type()) {
            return $this->oneToManyQuery($association, $id);
        }

        throw new InvalidArgumentException(
            sprintf('Association of type "%s" is not supported', $association->type())
        );
    }

    /**
     * Association instance getter.
     *
     * @param string $associationName associations name
     *
     * @return \Cake\ORM\Association|null $result object
     */
    private function getAssociation($associationName)
    {
        foreach ($this->_table()->associations() as $association) {
            if ($association->name() !== $associationName) {
                continue;
            }

            return $association;
        }

        return null;
    }

    /**
     * Method that generates many-to-many associations query
     *
     * @param \Cake\ORM\Association $association Association object
     * @param string $id Record id
     * @return \Cake\Datasource\QueryInterface|null
     * @throws \InvalidArgumentException When reversed many-to-many association is not found
     */
    private function manyToManyQuery(Association $association, $id)
    {
        $table = TableRegistry::get(Inflector::camelize($association->table()));

        // pagination hack to modify alias
        $association->setTarget($association->getTarget())->setAlias($this->_controller()->name);

        $related = $this->getManyToManyAssociation($association->getTarget());
        if (is_null($related)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not associated with %s',
                App::shortName(get_class($association->getTarget()), 'Model/Table', 'Table'),
                $this->_table()->getAlias()
            ));
        }

        $query = $association->find('all')->innerJoinWith($related->name(), function ($q) use ($related, $id) {
            return $q->where([$related->aliasField($this->_table()->getPrimaryKey()) => $id]);
        });

        return $query;
    }

    /**
     * Method that generates one-to-many associations query
     *
     * @param \Cake\ORM\Association $association Association object
     * @param string $id Record id
     *
     * @return \Cake\Datasource\QueryInterface|null
     */
    private function oneToManyQuery(Association $association, $id)
    {
        // pagination hack to modify alias
        $association->setTarget($association->getTarget())->setAlias($this->_controller()->name);

        // $table = $association->getTarget();
        $query = $association->find('all')->where([
            $association->aliasField($association->getForeignKey()) => $id
        ]);

        return $query;
    }

    /**
     * Retrieves reversed many-to-many association instance, by matching
     * its class name with the current Controller's table class.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Association's table
     * @return \Cake\ORM\Association|null
     */
    private function getManyToManyAssociation(RepositoryInterface $table)
    {
        foreach ($table->associations() as $association) {
            if (Association::MANY_TO_MANY !== $association->type()) {
                continue;
            }

            if ($association->className() !== $this->_table()->getAlias()) {
                continue;
            }

            return $association;
        }

        return null;
    }
}
