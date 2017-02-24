<?php
namespace App\Shell;

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Faker\Factory;
use Faker\Generator;
use Faker\ORM\CakePHP\Populator;
use ReflectionClass;
use ReflectionMethod;

class FakerShell extends Shell
{
    /**
     * {@inheritDoc}
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->description('Generate fake data.');
        $parser->addArgument('model', [
            'help' => 'Model name to generate fake data for.',
            'required' => true
        ]);
        $parser->addOption('number', [
            'short' => 'n',
            'help' => 'Number of fake records to create.',
            'default' => 10
        ]);

        return $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function main()
    {
        $tableName = $this->args[0];

        $this->out('Generating fake data for [' . $tableName . '] model.');
        $this->hr();

        // get table
        $table = TableRegistry::get($tableName);

        $columns = $table->schema()->columns();

        if (empty($columns)) {
            $this->abort('Table [' . $tableName . '] has no columns.');
        }

        $msg = 'Please select field(s) index number(s) you want to generate data for. Use comma to select multiple fields.';
        $fields = $this->in($this->_appendOptions($msg, $columns));

        if (empty($fields)) {
            $this->abort('Aborting, no columns selected.');
        }

        $fields = $this->_extractFields($fields, $columns);

        if (empty($fields)) {
            $this->abort('Aborting, no columns selected.');
        }

        $fields = $this->_setFieldFormatter($fields);

        $total = $this->param('number');
        $count = $this->_generateFakeData($table, $fields);
        if ($count < $total) {
            $this->err('Only ' . $count . ' out of target ' . $total . ' fake records were created.');
        } else {
            $this->success($count . ' fake records have been created successfully.');
        }
    }

    protected function _extractFields($selection, array $columns)
    {
        $result = [];

        $selection = trim($selection);
        $fields = explode(',', $selection);

        if (empty($fields)) {
            return $result;
        }

        foreach ($fields as $field) {
            $field = trim($field) - 1;
            // skip invalid fields
            if (!array_key_exists($field, $columns)) {
                continue;
            }

            $result[$columns[$field]] = [];
        }

        return $result;
    }

    protected function _setFieldFormatter(array $fields)
    {
        if (empty($fields)) {
            return $fields;
        }

        $providers = $this->_getProviders();

        if (empty($providers)) {
            $this->abort('Aborting, no providers found.');
        }

        foreach ($fields as $field => &$formatter) {
            $this->hr();
            $this->out('Setting faker options for [' . $field . '] field.');
            $this->hr();

            $options = array_keys($providers);
            sort($options);
            $provider = $this->in($this->_appendOptions('What category applies to:', $options), $options);
            $className = $providers[$provider]['className'];
            $class = new ReflectionClass($className);

            $methods = [];
            foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                if (!stripos($method->class, $providers[$provider]['shortName'])) {
                    continue;
                }
                $methods[Inflector::underscore($method->name)] = $method->name;
            }

            $options = array_keys($methods);
            sort($options);
            $formatter = $this->in('What type of field is:', $options);
            $formatter = $methods[$formatter];
        }

        return $fields;
    }

    protected function _appendOptions($message, array $options)
    {
        $result = $message;
        foreach ($options as $k => $v) {
            $result .= "\n" . ($k + 1) . ': ' . $v;
        }

        return $result;
    }

    protected function _getProviders()
    {
        $generator = Factory::create();

        $result = [];
        foreach ($generator->getProviders() as $provider) {
            $fullClassName = get_class($provider);
            $className = explode('\\', $fullClassName);
            $className = end($className);
            $result[Inflector::underscore($className)] = [
                'className' => $fullClassName,
                'shortName' => $className
            ];
        }

        return $result;
    }

    protected function _generateFakeData(Table $table, array $fields)
    {
        $result = 0;
        $total = $this->param('number');
        for ($i = 0; $i < $total; $i++) {
            $faker = Factory::create();
            $data = [];
            foreach ($fields as $k => $v) {
                $data[$k] = $faker->{$v};
            }
            $entity = $table->newEntity();
            $entity = $table->patchEntity($entity, $data);
            if ($table->save($entity)) {
                $result++;
            }
        }

        return $result;
    }
}
