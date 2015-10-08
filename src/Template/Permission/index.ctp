<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Sync Permission'), ['action' => 'sync']) ?></li>
    </ul>
</nav>
<div class="permission index large-9 medium-8 columns content">
    <h3><?= __('Permission') ?></h3>
    <?= $this->Form->postLink(__('Synchronize permissions'), ['action' => 'synchronize']) ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('prefix') ?></th>
                <th><?= $this->Paginator->sort('controller') ?></th>
                <th><?= $this->Paginator->sort('action') ?></th>
                <th><?= $this->Paginator->sort('unique_string') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permission as $permission): ?>
            <tr>
                <td><?= $this->Number->format($permission->id) ?></td>
                <td><?= h($permission->prefix) ?></td>
                <td><?= h($permission->controller) ?></td>
                <td><?= h($permission->action) ?></td>
                <td><?= h($permission->unique_string) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
