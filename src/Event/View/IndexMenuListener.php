<?php
namespace App\Event\View;

class IndexMenuListener extends BaseMenuListener
{
    /**
     * Implemented Events
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'CsvMigrations.Index.topMenu.beforeRender' => 'beforeRenderFlatMenu',
            'CsvMigrations.Index.actionsMenu.beforeRender' => 'beforeRenderFlatMenu'
        ];
    }
}
