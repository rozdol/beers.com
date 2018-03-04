<?php
//
// About Qobo
//
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Qobo</h3>
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
            <b><?= $this->Html->link('Qobo', 'https://www.qobo.biz', ['target' => '_blank']) ?></b> is a software
            development company, specializing in business applications.
        </p>
        <p>
            Here are some useful links for more information:
            <ul>
                <li><?= $this->Html->link('Qobo Website', 'https://www.qobo.biz', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo Blog', 'https://qobo.biz/blog/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo Careers', 'https://qobo.biz/careers/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo on GitHub', 'https://github.com/QoboLtd', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo on Facebook', 'https://www.facebook.com/Qobo.biz/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link('Qobo on LinkedIn', 'https://www.linkedin.com/company/3241664/', ['target' => '_blank']) ?></li>
            </ul>
            Feel free to contact Qobo via the <?= $this->Html->link('Contact Us', 'https://qobo.biz/contact/', ['target' => '_blank']) ?> page.
        </p>
    </div>
</div>
