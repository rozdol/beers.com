<div class="row">
    <div class="col-xs-12">
        <div class="media">
            <div class="media-left">
                <?= $this->Html->image(empty($user->avatar) ?
                    $avatarPlaceholder :
                    $user->avatar, ['width' => '180', 'height' => '180']
                ); ?>
            </div>
            <div class="media-body">
                <h3 class="media-heading"><?=
                    $this->Html->tag(
                        'span',
                        __d('Users', '{0} {1}', $user->first_name, $user->last_name),
                        ['class' => 'full_name']
                    );
                ?></h3>
                <dl>
                    <dt><?= __('Username') ?></dt>
                    <dd><?= h($user->username) ?></dd>
                    <dt><?= __('Email') ?></dt>
                    <dd><?= h($user->email) ?></dd>
                    <dd><?= $this->Html->link(__d('Users', 'Change Password'), [
                        'plugin' => 'CakeDC/Users',
                        'controller' => 'Users',
                        'action' => 'changePassword'
                    ]); ?></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
    <?php if (!empty($user->social_accounts)): ?>
        <h3><?= __d('Users', 'Social Accounts') ?></h3>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?= __d('Users', 'Avatar'); ?></th>
                    <th><?= __d('Users', 'Provider'); ?></th>
                    <th><?= __d('Users', 'Link'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            foreach ($user->social_accounts as $socialAccount):
                $escapedUsername = h($socialAccount->username);
                $linkText = empty($escapedUsername) ?
                    __d('Users', 'Link to {0}', h($socialAccount->provider)) :
                    h($socialAccount->username);
                ?>
                <tr>
                    <td><?=
                        $this->Html->image(
                            $socialAccount->avatar,
                            ['width' => '90', 'height' => '90']
                        ) ?>
                    </td>
                    <td><?= h($socialAccount->provider) ?></td>
                    <td><?=
                        $this->Html->link(
                            $linkText,
                            $socialAccount->link,
                            ['target' => '_blank']
                        ) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    </div>
</div>
