<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Routing\Router;

if (Configure::read('debug')):
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?= Debugger::dump($error->params) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')):
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-danger">
            <div class="panel-heading">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <b><?= __d('cake', 'Error') ?> <?= h($code) ?>: <?= h($message) ?></b>
            </div>
            <div class="panel-body">
                <?php echo 'There was a problem processing your request.  Please notify your system administrator.'; ?>
            </div>
            <div class="panel-footer">
                <b>URL: </b><?= h(Router::url($url, true)) ?>
            </div>
        </div>
    </div>
</div>
