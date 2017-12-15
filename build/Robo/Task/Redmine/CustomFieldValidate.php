<?php

namespace Qobo\Robo\Task\Redmine;

use Robo\Result;

/**
 * Validates a RedMine custom fields
 *
 * ```php
 * <?php
 * $this->taskCustomFieldValidate()
 * ->name('My CustomField')
 * ->value('TestValue')
 * ->run();
 * ?>
 * ```
 */
class CustomFieldValidate extends \Qobo\Robo\AbstractApiTask
{
    /**
     * {@inheritdoc}
     */
    protected $data = [
        'name'              => null,
        'value'             => null
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredData = [
        'name',
        'value'
    ];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $result = parent::run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
 
        $this->printInfo("Validating {name} custom fields can accept {value} value", $this->data);
        try {
            $this->api->validateCustomFieldValue($this->data['name'], $this->data['value']);

            return Result::success($this, "Custom field value is valid");
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }

        return Result::error($this, "Failed to validate custom field");
    }
}
