<?php
namespace App\Model\Table;

use CakeDC\Users\Model\Table\UsersTable as Table;
use Cake\Database\Schema\TableSchema;
use Cake\Validation\Validator;
use CsvMigrations\ConfigurationTrait;
use CsvMigrations\FieldTrait;

/**
 * Users Model
 */
class UsersTable extends Table
{
    use ConfigurationTrait;
    use FieldTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        // set table/module configuration
        $this->setConfig($this->table());
    }

    /**
     * {@inheritDoc}
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->columnType('image', 'base64');

        return $schema;
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator = parent::validationDefault($validator);

        $validator->add('username', 'validRegex', [
            'rule' => ['custom', '/^[\w\d\@\-\_\s\.]+$/Du'],
            'message' => 'The provided value is invalid (alphanumeric, dot, dash, at, underscore, space)'
        ]);

        $validator->add('first_name', 'validRegex', [
            // \p is used for targeting unicode character properties, in this case L which means all letters
            // @link http://php.net/manual/en/regexp.reference.unicode.php
            'rule' => ['custom', '/^[\pL\-\s\.]+$/Du'],
            'message' => 'The provided value is invalid (letter, dot, dash, space)'
        ]);

        $validator->add('last_name', 'validRegex', [
            'rule' => ['custom', '/^[\pL\-\s\.]+$/Du'],
            'message' => 'The provided value is invalid (letter, dot, dash, space)'
        ]);

        return $validator;
    }
}
