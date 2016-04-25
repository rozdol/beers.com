<div class="row">
    <div class="col-xs-12">
        <?= $this->Form->create($Users) ?>
        <fieldset>
            <legend><?= __('Add User') ?></legend>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">&nbsp;</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('username'); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('password'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('first_name'); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('last_name'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('email'); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->input('active', ['type' => 'checkbox']); ?>
                            </div>
                        </div>
                    </div>
                </div>
        </fieldset>
        <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Form->end() ?>
    </div>
</div>
