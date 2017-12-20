<?php
namespace App\Controller;

use CakeDC\Users\Controller\Traits\CustomUsersTableTrait;
use Cake\ORM\TableRegistry;

/**
 * Controller Trait responsible for changelog functionality.
 */
trait ChangelogTrait
{
    use CustomUsersTableTrait;

    /**
     * Table name for Log Audit model.
     *
     * @var string
     */
    protected $_tableLog = 'LogAudit';

    /**
     * Element to be used as View template.
     *
     * @var string
     */
    protected $_elementView = '/Element/changelog';

    /**
     * Return log audit results for specific record.
     *
     * @param  string $id Record id
     * @return void
     */
    public function changelog($id)
    {
        /*
        ideally we want to group by user and timestamp, but having user in the meta information makes this non-trivial
        for now we are using just the timestamp assuming that different will edit the same record at the same time is
        very unlikely.
         */
        $query = TableRegistry::get($this->_tableLog)->findByPrimaryKey($id)
            ->order([$this->_tableLog . '.timestamp' => 'DESC'])
            ->group($this->_tableLog . '.timestamp');

        $modelAlias = $this->{$this->name}->alias();
        $methodName = 'moduleAlias';
        if (method_exists($this->{$this->name}, $methodName) && is_callable([$this->{$this->name}, $methodName])) {
            $modelAlias = $this->{$this->name}->{$methodName}();
        }

        $entity = $this->{$this->name}->findById($id)->firstOrFail();

        $this->set('changelog', $this->paginate($query));
        $this->set('modelAlias', $modelAlias);
        $this->set('displayField', $this->{$this->name}->displayField());
        $this->set('usersTable', $this->getUsersTable());
        $this->set('entity', $entity);

        $this->render($this->_elementView);

        $this->set('_serialize', ['changelog']);
    }
}
