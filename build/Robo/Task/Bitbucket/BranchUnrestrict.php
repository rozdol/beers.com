<?php

namespace Qobo\Robo\Task\Bitbucket;

use Robo\Result;

/**
 * Removes branch restrictions from a BitBucket repo branch.
 *
 * ```php
 * <?php
 * $this->taskBranchUnrestrict($bitbucketApi)
 * ->team('myteam')
 * ->repo('foobar.com')
 * ->branch('master')
 * ->run();
 * ```
 */
class BranchUnrestrict extends \Qobo\Robo\AbstractApiTask
{
    /**
     * {@inheritdoc}
     */
    protected $data = [
        'repo'      => null,
        'branch'    => 'master'
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredData = [
        'repo',
        'branch'
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

        $this->printInfo("Getting list of branch restrictions on {branch} branch of {repo} repo", $this->data);
        try {
            $restrictions = $this->api->getBranchRestrictions($this->data['repo'], $this->data['branch']);

            foreach ($restrictions as $kind) {
                $this->printInfo("Removing {kind} restriction", $kind);
                $this->api->deleteBranchRestriction($this->data['repo'], $kind['id']);
            }
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }

        return Result::success($this, "Successfully removed branch restrictions", $restrictions);
    }
}
