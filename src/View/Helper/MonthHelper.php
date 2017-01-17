<?php
namespace App\View\Helper;

use Cake\View\Helper;
use InvalidArgumentException;

/**
 * Helper that converts numeric month value into string representation.
 */
class MonthHelper extends Helper
{
    /**
     * Month names.
     *
     * @var array
     */
    protected $_names = [
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
    ];

    /**
     * Month short names.
     *
     * @var array
     */
    protected $_shortNames = [
        '01' => 'Jan.',
        '02' => 'Feb.',
        '03' => 'Mar.',
        '04' => 'Apr.',
        '05' => 'May',
        '06' => 'Jun.',
        '07' => 'Jul.',
        '08' => 'Aug.',
        '09' => 'Sept.',
        '10' => 'Oct.',
        '11' => 'Nov.',
        '12' => 'Dec.',
    ];

    /**
     * Get month name.
     *
     * @param  string $key Month numberic value
     * @return string
     */
    public function name($key)
    {
        $key = $this->_validateKey($key);

        if (!array_key_exists($key, $this->_names)) {
            return '';
        }

        return $this->_names[$key];
    }

    /**
     * Get short month name.
     *
     * @param  string $key Month numberic value
     * @return string
     */
    public function shortName($key)
    {
        $key = $this->_validateKey($key);

        if (!array_key_exists($key, $this->_shortNames)) {
            return '';
        }

        return $this->_shortNames[$key];
    }

    /**
     * Validate provided $key.
     *
     * @param  string $key Month numberic value
     * @return string
     * @throws InvalidArgumentException if $key value is not a string
     */
    protected function _validateKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException('[key] value must be a string');
        }

        // append leading zero if missing
        if (1 === strlen($key)) {
            $key = '0' . $key;
        }

        return $key;
    }
}
