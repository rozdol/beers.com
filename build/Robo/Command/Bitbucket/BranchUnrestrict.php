<?php

namespace Qobo\Robo\Command\Bitbucket;

use \Qobo\Robo\AbstractCommand;
use \Qobo\Utility\Hash;
use \Qobo\Robo\Formatter\RowsOfFields;

class BranchUnrestrict extends AbstractCommand
{
    /**
     * Remove branch restriction from repo
     *
     * @param string $repo Repository name
     * @param string $branch Branch name
     *
     * @option string $format Output format (table, list, csv, json, xml)
     * @option string $fields Limit output to given fields, comma-separated
     *
     * @return RowsOfFields Info about removed restrictions for possible later reuse
     *
     * @field-labels:
     *   id: id
     *   kind: kind
     *   pattern: branch
     *   users: users
     *   groups: groups
     */
    public function bitbucketBranchUnrestrict($repo, $branch = 'master', $opts = ['format' => 'table', 'fields' => ''])
    {
        $result = $this->taskBitbucketBranchUnrestrict()
                        ->repo($repo)
                        ->branch($branch)
                        ->run();

        if (!$result->wasSuccessful()) {
            $this->exitError("Failed to run the command");
        }

        $data = Hash::extract($result->getData(), '{n}');
        $data = new RowsOfFields($data);

        return $data;
    }

}
