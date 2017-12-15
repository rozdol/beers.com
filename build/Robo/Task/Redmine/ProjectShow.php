<?php

namespace Qobo\Robo\Task\Redmine;

use Robo\Result;
use Qobo\Utility\Hash;

/**
 * Shows a RedMine project(s)
 *
 * ```php
 * <?php
 * $this->taskProjectShow()
 * ->name('My Project')
 * ->parent('client-projects')
 * ->all(true)
 * ->run();
 * ?>
 * ```
 */
class ProjectShow extends \Qobo\Robo\AbstractApiTask
{
    /**
     * {@inheritdoc}
     */
    protected $data = [
        'name'          => null,
        'parent'        => null,
        'all'           => false
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

        if (!empty($this->data['parent'])) {
            $this->printInfo("Retrieving parent {parent} project ID", $this->data);
            $this->data['parent_id'] = $this->api->getProjectId($this->data['parent']);
            if (!$this->data['parent_id']) {
                return Result::error($this, "Failed to find parent project ID");
            }
        }
        $info = (!empty($this->data['name']))
            ? "Retrieving {name} project info"
            : "Retrieving all projects info";

        $this->printInfo($info, $this->data);

        try {
            $data = $this->api->get('projects');

        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }

        $data = array_filter($data, [$this, "filter"]);
        $data = Hash::combine($data, '{n}.identifier', '{n}');

        return Result::success($this, "Successfully retrieved project(s) info", $data);
    }

    /**
     * Filter function to reduce the results array according to our data
     */
    public function filter($item)
    {
        // filter out everything except exact record if name was given
        if ($this->data['name'] && $item['identifier'] != $this->data['name']) {
            return false;
        }

        // filter our everything except active projects unless all requested
        if (!$this->data['all'] && $item['status'] != 1) {
            return false;
        }

        // if parent given
        if ($this->data['parent_id']) {

            // fiter out all without parent
            if (!isset($item['parent'])) {
                return false;
            }

            // and filter all with different parent
            if ($this->data['parent_id'] != $item['parent']['id']) {
                return false;
            }
        }

        return true;
    }
}
