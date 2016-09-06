<?php
//
// Installed composer libraries (from composer.lock file)
//
$composerLock = ROOT . DS . 'composer.lock';
$composer = null;
if (is_readable($composerLock)) {
    $composer = json_decode(file_get_contents($composerLock), true);
}
?>
<h4>Composer Libraries</h4>
<p>There are currently <strong><?php echo count($composer['packages']); ?> composer libraries</strong> installed.</p>
<table class="table table-striped table-hover table-condensed">
    <thead>
        <tr>
            <th>Name</th>
            <th>Version</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($composer['packages'] as $package) {
                print '<tr>';
                print '<td>' . $package['name'] . '</td>';
                print '<td>' . $package['version'] . '</td>';
                print '<td>' . (empty($package['description']) ? '&nbsp;' : $package['description']) . '</td>';
                print '</td>';
                print '</tr>';
            }
        ?>
    </tbody>
</table>
