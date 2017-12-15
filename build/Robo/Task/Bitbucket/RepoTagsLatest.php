<?php

namespace Qobo\Robo\Task\Bitbucket;

use Robo\Result;

/**
 * Get latest tag of a BitBucket repo.
 *
 * ```php
 * <?php
 * $this->taskRepoTagsLatest()
 * ->name('foobar.com')
 * ->run();
 * ?>
 * ```
 */
class RepoTagsLatest extends \Qobo\Robo\AbstractApiTask
{
    /**
     * {@inheritdoc}
     */
    protected $data = [
        'name'          => null,
    ];

    /**
     * {@inheritdoc}
     */
    protected $requiredData = [
        'name',
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

        $this->printInfo("Checking that {name} repo exists", $this->data);
        try {
            $list = $this->api->getRepos();

            if (!isset($list[$this->data['name']])) {
                return Result::error($this, "Repo doesn't exists", $this->data);
            }
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }

        $this->printInfo("Getting latest tag for {name} repo", $this->data);
        try {
            $tag = $this->api->getTagLatest(
                $this->data['name']
            );
        } catch (\Exception $e) {
            return Result::fromException($this, $e);
        }

        return Result::success($this, "Latest repository tag successfully retrieved", ['tag' => $tag]);
    }
}
