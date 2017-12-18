<?php

namespace Qobo\Robo\Command\Bitbucket;

use \Qobo\Robo\AbstractCommand;
use \Qobo\Utility\Hash;
use \Consolidation\OutputFormatters\StructuredData\PropertyList;

class RepoTagsLatest extends AbstractCommand
{
     /**
     * Get latest repo tag
     *
     * @param string $repo Repository name
     *
     * @option string $format Output format (table, list, csv, json, xml)
     * @option string $fields Limit output to given fields, comma-separated
     *
     * @return PropertyList Info
     *
     * @field-labels:
     *   tag: tag
     */
    public function bitbucketRepoTagsLatest($repo, $opts = ['format' => 'table', 'fields' => ''])
    {
        $result = $this->taskBitbucketRepoTagsLatest()
            ->name($repo)
            ->run();

        if (!$result->wasSuccessful()) {
            $this->exitError("Failed to run the command");
        }

        $data = $result->getData();
        $data = new PropertyList($data);
        return $data;
    }
}
