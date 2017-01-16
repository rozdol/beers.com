<div class="row">
    <div class="col-xs-12">
        <p class="text-right">
            <?= $this->Html->link(__('Add User'), ['action' => 'add'], ['class' => 'btn btn-primary']); ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                <th><?= $this->Paginator->sort('username') ?></th>
                <th><?= $this->Paginator->sort('email') ?></th>
                <th><?= $this->Paginator->sort('first_name') ?></th>
                <th><?= $this->Paginator->sort('last_name') ?></th>
                <th class="actions"><?= __d('Users', 'Actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($Users as $user): ?>
                    <tr>
                        <td><?= h($user->username) ?></td>
                        <td><?= h($user->email) ?></td>
                        <td><?= h($user->first_name) ?></td>
                        <td><?= h($user->last_name) ?></td>
                        <td class="actions">
                            <?= $this->Html->link('', ['action' => 'view', $user->id], ['title' => __('View'), 'class' => 'btn btn-default fa fa-eye']) ?>
                            <?= $this->Html->link('', ['action' => 'edit', $user->id], ['title' => __('View'), 'class' => 'btn btn-default fa fa-pencil']) ?>
                            <?= $this->Html->link('', ['action' => 'change-user-password', $user->id], ['title' => __('Change User Password'), 'class' => 'btn btn-default fa fa-lock']) ?>
                            <?= $this->Form->postLink('', ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id), 'title' => __('Delete'), 'class' => 'btn btn-default fa fa-trash']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __d('Users', 'previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__d('Users', 'next') . ' >') ?>
    </ul>
    <p><?= $this->Paginator->counter() ?></p>
</div>
