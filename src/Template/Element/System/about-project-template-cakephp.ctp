<?php
//
// About project-template-cakephp
//
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">About project-template-cakephp</h3>
    </div>
    <div class="box-body">
        <p>
        <?php
            echo $this->Html->link(
                $this->Html->image('branding/qobo/logo.png', [
                            'alt' => 'Qobo',
                            'class' => 'img img-responsive',
                ]),
                'https://www.qobo.biz',
                [
                    'target' => '_blank',
                    'escape' => false
                ]
            );
        ?>
        </p>
        <p>
            This project is built with <b><?= $this->Html->link('project-template-cakephp', 'https://github.com/QoboLtd/project-template-cakephp/', ['target' => '_blank']) ?></b>.
            This template is developed by <?= $this->Html->link('Qobo', 'https://www.qobo.biz', ['target' => '_blank']) ?> and aims to assist web developers in rapidly creating
            new web applications, powered by <?= $this->Html->link('CakePHP', 'https://cakephp.org', ['target' => '_blank']) ?> framework.  It is also used in the award-winning
            <?= $this->Html->link('Qobrix', 'https://qobrix.com', ['target' => '_blank']) ?> platform.
        </p>
        <p>
            Here are some useful links for more information:
            <ul>
                <li><?= $this->Html->link('project-template-cakephp on GitHub', 'https://github.com/QoboLtd/project-template-cakephp/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo Website', 'https://www.qobo.biz', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobrix Website', 'https://qobrix.com', ['target' => '_blank']) ?></li>
            </ul>
        </p>
    </div>
</div>
