<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\OrcidBatch $orcidBatch
 */
?>
<?= $this->TinyMCE->editor(['theme' => 'modern', 'selector' => 'textarea']) ?>
<?= $this->Html->script('jquery-x.x.x.min'); ?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Orcid Batch'), ['action' => 'edit', $orcidBatch->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Orcid Batch'), ['action' => 'delete', $orcidBatch->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orcidBatch->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Orcid Batches'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Orcid Batch'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="orcidBatches view content">
            <h3><?= h($orcidBatch->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($orcidBatch->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= h($orcidBatch->subject) ?></td>
                </tr>
                <tr>
                    <th><?= __('From Name') ?></th>
                    <td><?= h($orcidBatch->from_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('From Addr') ?></th>
                    <td><?= h($orcidBatch->from_addr) ?></td>
                </tr>
                <tr>
                    <th><?= __('Reply To') ?></th>
                    <td><?= h($orcidBatch->reply_to) ?></td>
                </tr>
                <tr>
                    <th><?= __('Orcid Batch Creator') ?></th>
                    <td><?= $orcidBatch->has('orcid_batch_creator') ? $this->Html->link($orcidBatch->orcid_batch_creator->name, ['controller' => 'OrcidBatchCreators', 'action' => 'view', $orcidBatch->orcid_batch_creator->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($orcidBatch->id) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Body') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($orcidBatch->body)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Triggers Attached to this Template') ?></h4>
                <?php if (!empty($orcidBatch->orcid_batch_triggers)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Workflow Checkpoint') ?></th>
                            <th><?= __('Trigger Delay') ?></th>
                            <th><?= __('Group') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($orcidBatch->orcid_batch_triggers as $orcidBatchTriggers) : ?>
                        <tr>
                            <td><?= h($orcidBatchTriggers->name) ?></td>
                            <td><?= h($orcidBatchTriggers->orcid_status_type_id) ?></td>
                            <td><?= h($orcidBatchTriggers->trigger_delay) ?></td>
                            <td><?= h($orcidBatchTriggers->orcid_batch_group_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'OrcidBatchTriggers', 'action' => 'view', $orcidBatchTriggers->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'OrcidBatchTriggers', 'action' => 'edit', $orcidBatchTriggers->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'OrcidBatchTriggers', 'action' => 'delete', $orcidBatchTriggers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $orcidBatchTriggers->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
