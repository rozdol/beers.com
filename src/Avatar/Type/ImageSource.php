<?php
namespace App\Avatar\Type;

use App\Avatar\AvatarInterface;
use Cake\Core\Configure;

final class ImageSource implements AvatarInterface
{
    /**
     * Image default options.
     *
     * @var array
     */
    private $options = [
        'src' => '/img/user-image-160x160.png',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function get()
    {
        return sprintf('%s', $this->options['src']);
    }
}
