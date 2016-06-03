<?= $this->Html->script('changelog', ['block' => 'scriptBottom']) ?>
<a id="changelogBtn" class="btn btn-default" role="button" data-toggle="collapse" href="#collapseChangelog" data-id="<?= $recordId; ?>" data-url="/api/log_audit/changelog.json" aria-expanded="true" aria-controls="collapseChangelog">Changelog</a>
<div class="collapse" id="collapseChangelog" aria-expanded="false">
    <div class="body well">
    </div>
</div>