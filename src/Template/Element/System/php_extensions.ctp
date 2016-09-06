<?php
//
// PHP extensions
//
$extensions = get_loaded_extensions();
?>
<h4>PHP Extensions</h4>
<p>There are currently <strong><?php echo count($extensions); ?> PHP extensions</strong> loaded.</p>
<ul>
<?php
    foreach ($extensions as $extension) {
        print '<li>' . $extension . '</li>';
    }
?>
</ul>
