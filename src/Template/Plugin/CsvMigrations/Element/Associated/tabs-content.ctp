<?php
use Cake\Utility\Inflector;
use RolesCapabilities\Access\AccessFactory;

$accessFactory = new AccessFactory();

$url = $this->Url->build([
    'prefix' => 'api',
    'controller' => $this->request->param('controller'),
    'action' => 'related'
]);
?>
<div class="tab-content">
    <?php $active = 'active'; ?>
    <?php foreach ($associations as $association) : ?>
        <?php $containerId = Inflector::underscore($association->getAlias()); ?>
        <div role="tabpanel" class="tab-pane <?= $active ?>" id="<?= $containerId ?>">
            <?php
            list($plugin, $controller) = pluginSplit($association->className());
            $accessUrl = ['plugin' => $plugin, 'controller' => $controller, 'action' => 'add'];
            if (in_array($association->type(), ['manyToMany']) && $accessFactory->hasAccess($accessUrl, $user)) {
                echo $this->element('CsvMigrations.Embedded/lookup', ['association' => $association, 'user' => $user]);
            } ?>
            <?= $this->element('CsvMigrations.Associated/tab-content', [
                'association' => $association, 'table' => $table, 'url' => $url, 'factory' => $factory
            ]) ?>
        </div>
        <?php $active = ''; ?>
    <?php endforeach; ?>
</div> <!-- .tab-content -->