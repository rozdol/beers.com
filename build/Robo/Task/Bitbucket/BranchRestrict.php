<?php

namespace Qobo\Robo\Task\Bitbucket;

use Robo\Result;

/**
 * Applies branch restrictions on a BitBucket repo branch.
 *
 * ```php
 * <?php
 * $this->taskBranchRestrict($bitbucketApi)
 * ->repo('foobar.com')
 * ->branch('master')
 * ->action('force')
 * ->action('push')
 * ->action('delete')
 * ->run();
 * ```
 */
class BranchRestrict extends \Qobo\Robo\AbstractApiTask
{
    /**
     * {@inheritdoc}
     */
    protected $data = [
        'repo'          => null,
        'branch'        => 'master',
        'users'         => [],
        'groups'        => []
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredData = [
        'repo',
        'branch'
    ];

    /**
     * @var array actions
     */
    protected $actions = [];

    /**
     * add actions
     *
     * @param string $action
     *
     * @return $this
     */
    public function action($action)
    {
        $this->actions []= $action;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $result = parent::run();
        if (!$result->wasSuccessful()) {
            return $result;
        }
 
        $this->printInfo("Setting restrictions on {branch} branch of {repo} repo", $this->data);
        if (isset($data['users']) && !is_array($data['users'])) {
            $data['users'] = explode(",", $data['users']);
        }
        if (isset($data['groups']) && !is_array($data['groups'])) {
            $data['groups'] = explode(",", $data['groups']);
        }

        $result = [];
        try {

           foreach ($this->actions as $kind) {

                $this->printInfo("Restricting {kind} action", ['kind' => $kind]);
                $result []= $this->api->setBranchRestriction(
                    $this->data['repo'],
                    $this->data['branch'],
                    $kind,
                    $this->data['users'],
                    $this->data['groups']
                );
            }

        } catch (\Exception $e) {
            return Result::fromExcetion($this, $e);
        }

        return Result::success($this, "Successfully applied branch restrictions", $result);
    }
}
