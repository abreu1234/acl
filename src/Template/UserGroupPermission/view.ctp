<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit User Group Permission'), ['action' => 'edit', $userGroupPermission->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete User Group Permission'), ['action' => 'delete', $userGroupPermission->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userGroupPermission->id)]) ?> </li>
        <li><?= $this->Html->link(__('List User Group Permission'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User Group Permission'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Permission'), ['controller' => 'Permission', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Permission'), ['controller' => 'Permission', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="userGroupPermission view large-9 medium-8 columns content">
    <h3><?= h($userGroupPermission->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Group Or User') ?></th>
            <td><?= h($userGroupPermission->group_or_user) ?></td>
        </tr>
        <tr>
            <th><?= __('Permission') ?></th>
            <td><?= $userGroupPermission->has('permission') ? $this->Html->link($userGroupPermission->permission->id, ['controller' => 'Permission', 'action' => 'view', $userGroupPermission->permission->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($userGroupPermission->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Group Or User Id') ?></th>
            <td><?= $this->Number->format($userGroupPermission->group_or_user_id) ?></td>
        </tr>
    </table>
</div>
