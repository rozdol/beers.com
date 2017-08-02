<?php

$localModificationsCommand = $this->SystemInfo->getLocalModificationsCommand();
$localModifications = $this->SystemInfo->getLocalModifications();
?>
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
        <?php if (empty($localModifications)) : ?>
            <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Local modifications</span>
                <span class="info-box-number">0</span>
            </div>
        <?php else : ?>
            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Local modifications</span>
                <span class="info-box-number"><?php echo number_format(count($localModifications)); ?></span>
            </div>
        <?php endif; ?>
        </div>
    </div>
    <div class="col-md-9">
        <?php if (!empty($localModifications)) : ?>
            <pre><?php
                echo $localModificationsCommand . "\n";
                echo implode("\n", $localModifications);
            ?></pre>
        <?php else : ?>
            All good, no local modifications found.
        <?php endif; ?>

    </div>
</div>
