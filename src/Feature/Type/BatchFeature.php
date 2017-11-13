<?php
namespace App\Feature\Type;

use App\Feature\AbstractFeature;
use Cake\Core\Configure;

class BatchFeature extends AbstractFeature
{
    /**
     * {@inheritDoc}
     */
    public function enable()
    {
        Configure::write([
            'CsvMigrations.batch.active' => true,
            'Search.batch.active' => true
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function disable()
    {
        Configure::write([
            'CsvMigrations.batch.active' => false,
            'Search.batch.active' => false
        ]);
    }
}
