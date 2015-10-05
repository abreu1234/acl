<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $userGroupPermission->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $userGroupPermission->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List User Group Permission'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Permission'), ['controller' => 'Permission', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Permission'), ['controller' => 'Permission', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="userGroupPermission form large-9 medium-8 columns content">
    <?= $this->Form->create($userGroupPermission) ?>
    <fieldset>
        <legend><?= __('Edit User Group Permission') ?></legend>
        <?php
            echo $this->Form->input('group_or_user');
            echo $this->Form->input('group_or_user_id', ['type' => 'text', 'label' => 'Group or User id']);
            echo $this->Form->input('permission_id', ['options' => $permission]);
            echo $this->Form->input('allow');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
