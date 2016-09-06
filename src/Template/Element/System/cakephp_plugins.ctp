<?php
//
// Installed CakePHP plugins
//
$plugins = \Cake\Core\Plugin::loaded();
?>
<h4>CakePHP Plugins</h4>
<p>There are currently <strong><?php echo count($plugins); ?> plugins</strong> installed.</p>
<ul>
<?php
    foreach ($plugins as $plugin) {
        print '<li>' . $plugin . '</li>';
    }
?>
</ul>
