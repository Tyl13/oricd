<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\OrcidBatchTrigger $orcidBatchTrigger
 * @var string[]|\Cake\Collection\CollectionInterface $orcidStatusTypes
 * @var string[]|\Cake\Collection\CollectionInterface $orcidBatches
 * @var string[]|\Cake\Collection\CollectionInterface $orcidBatchGroups
 */
?>
<div class="row">
    <div class="column-responsive column-80">
        <div class="orcidBatchTriggers form content">
            <?= $this->Form->create($orcidBatchTrigger) ?>
            <fieldset>
                <legend><?= __('Edit Orcid Batch Trigger') ?></legend>
                <?php
                    echo $this->Form->control('NAME');
                    echo $this->Form->control('ORCID_BATCH_GROUP_ID', ['label' => 'Group', 'options' => $orcidBatchGroups, 'empty' => [0 => '']]);
                    echo $this->Form->control('ORCID_STATUS_TYPE_ID', ['label' => 'Workflow Checkpoint', 'options' => $orcidStatusTypes]);
                    echo $this->Form->control('ORCID_BATCH_ID', ['label' => 'Email Batch', 'options' => $orcidBatches]);
                    echo $this->Form->control('TRIGGER_DELAY', ['label' => 'Trigger Delay (in days)', 'default' => 0]);
                    echo $this->Form->control('REPEAT', ['label' => 'Repeat Every (in days, 0 for never)', 'default' => 0]);
                    echo $this->Form->control('MAXIMUM_REPEAT', ['label' => 'Repeat Limit (in times, 0 for no limit)', 'default' => 0]);
                    echo $this->Form->control('BEGIN_DATE');
		            echo $this->Form->control('REQUIRE_BATCH_ID', ['label' => 'Require Prior Batch', 'options' => $reqBatches]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<div class="navigation actions">
    <h3 class="heading"><?= __('Actions') ?></h3>
        <?= $this->Html->link(__('View'), ['action' => 'view', $orcidBatchTrigger->ID]) ?>
        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $orcidBatchTrigger->ID], ['confirm' => __('Are you sure you want to delete # {0}?', $orcidBatchTrigger->ID)]) ?>
    <h3 class="heading"><?= __('Navigation') ?></h3>
        <?= $this->Html->link(__('List Triggers'), ['action' => 'index']) ?>
        <?= $this->Html->link(__('Home'), ['controller' => '', 'action' => 'admin', 'prefix' => false]) ?>
</div>