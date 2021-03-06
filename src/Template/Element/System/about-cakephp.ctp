<?php
//
// About CakePHP
//
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">CakePHP</h3>
    </div>
    <div class="box-body">
        <p>
        <?php
            echo $this->Html->link(
                $this->Html->image('branding/cakephp/cakephp.jpg', [
                            'alt' => 'Baked with CakePHP',
                            'class' => 'img img-responsive',
                ]),
                'https://cakephp.org',
                [
                    'target' => '_blank',
                    'escape' => false
                ]
            );
        ?>
        </p>
        <p>
            This project is built with <b><?= $this->Html->link('CakePHP', 'https://cakephp.org', ['target' => '_blank']) ?></b> framework.
            CakePHP is free, open source, rapid web development framework for PHP.
        </p>
        <p>
            Here are some useful links for more information:
            <ul>
                <li><?= $this->Html->link('CakePHP Website', 'https://cakephp.org', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP Cookbook', 'https://book.cakephp.org/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP API Documentation', 'https://api.cakephp.org/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP Forum', 'https://discourse.cakephp.org/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP on GitHub', 'https://github.com/cakephp/cakephp', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP on Stack Overflow', 'https://stackoverflow.com/tags/cakephp', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('CakePHP on Wikipedia', 'https://en.wikipedia.org/wiki/CakePHP', ['target' => '_blank']) ?></li>
            </ul>
            Commercial support for CakePHP is available via <?= $this->Html->link('CakeDC', 'https://cakedc.com', ['target' => '_blank']) ?>.
        </p>
    </div>
</div>
