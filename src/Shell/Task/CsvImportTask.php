<?php
namespace App\Shell\Task;

use Cake\Console\Shell;

class CsvImportTask extends Shell
{
    public function main()
    {
    }

    /**
     * Create CSV import template file
     *
     * @throws RuntimeException When cannot open file for writing
     * @param array $columns Columns to save into the template
     * @param string $path Path to write the template file to
     * @return int Number of bytes written to the file
     */
    public function createTemplate(array $columns, $path)
    {
        $result = 0;

        if (empty($columns) || empty($path)) {
            return $result;
        }
        $fh = fopen($path, 'w');
        if (!is_resource($fh)) {
            throw new \RuntimeException("Failed to open file for writing: $path");
        }
        $result = fputcsv($fh, $columns);
        fclose($fh);

        return $result;
    }
}
