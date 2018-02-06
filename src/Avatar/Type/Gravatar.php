<?php
namespace App\Avatar\Type;

use App\Avatar\AvatarInterface;
use Cake\Core\Configure;

final class Gravatar implements AvatarInterface
{
    /**
     * Gravatar default options.
     *
     * @var array
     */
    private $options = [
        '{{email}}' => '',
        '{{size}}' => 160,
        '{{default}}' => 'mm',
        '{{rating}}' => 'g'
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options)
    {
        $this->options = array_merge($this->options, $options);

        $this->options['{{email}}'] = md5(strtolower(trim($this->options['{{email}}'])));
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return str_replace(
            array_keys($this->options),
            array_values($this->options),
            'https://www.gravatar.com/avatar/{{email}}?size={{size}}&default={{default}}&rating={{rating}}'
        );
    }
}
