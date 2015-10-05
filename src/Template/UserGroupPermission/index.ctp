<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New User Group Permission'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Permission'), ['controller' => 'Permission', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Permission'), ['controller' => 'Permission', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="userGroupPermission index large-9 medium-8 columns content">
    <h3><?= __('User Group Permission') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('group_or_user') ?></th>
                <th><?= $this->Paginator->sort('group_or_user_id') ?></th>
                <th><?= $this->Paginator->sort('permission_id') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($userGroupPermission as $userGroupPermission): ?>
            <tr>
                <td><?= $this->Number->format($userGroupPermission->id) ?></td>
                <td><?= h($userGroupPermission->group_or_user) ?></td>
                <td><?= $this->Number->format($userGroupPermission->group_or_user_id) ?></td>
                <td><?= $userGroupPermission->has('permission') ? $this->Html->link($userGroupPermission->permission->id, ['controller' => 'Permission', 'action' => 'view', $userGroupPermission->permission->id]) : '' ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $userGroupPermission->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $userGroupPermission->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $userGroupPermission->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userGroupPermission->id)]) ?>
                </td>
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
