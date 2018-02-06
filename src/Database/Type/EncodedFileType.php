<?php
namespace App\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use InvalidArgumentException;
use PDO;

/**
 * Base64 Encoded file type converter.
 *
 * Use to convert base64 eoncoded file data between PHP and the database types.
 */
class EncodedFileType extends Type
{
    /**
     * {@inheritDoc}
     */
    public function toDatabase($value, Driver $driver)
    {
        if (! is_array($value)) {
            throw new InvalidArgumentException('Encoded file value must be an array');
        }

        if (! isset($value['type'])) {
            throw new InvalidArgumentException('Encoded file "type" is not defined');
        }

        if (! isset($value['tmp_name'])) {
            throw new InvalidArgumentException('Encoded file "tmp_name" is not defined');
        }

        return sprintf('data:%s;base64,%s', $value['type'], base64_encode(file_get_contents($value['tmp_name'])));
    }

    /**
     * {@inheritDoc}
     */
    public function toPHP($value, Driver $driver)
    {
        if (is_resource($value)) {
            return stream_get_contents($value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function toStatement($value, Driver $driver)
    {
        return PDO::PARAM_STR;
    }

    /**
     * {@inheritDoc}
     */
    public function marshal($value)
    {
        return $value;
    }
}
