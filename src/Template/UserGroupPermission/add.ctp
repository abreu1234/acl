<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List User Group Permission'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Permission'), ['controller' => 'Permission', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Permission'), ['controller' => 'Permission', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="userGroupPermission form large-9 medium-8 columns content">
    <?= $this->Form->create($userGroupPermission) ?>
    <fieldset>
        <legend><?= __('Add User Group Permission') ?></legend>
        <?php            
            foreach ( $users as $user ) 
                $options_users['Users']['user-'.$user->id] = $user->email;
            echo $this->Form->label('group_or_user_id');
            echo $this->Form->select('group_or_user_id', $options_users);
            
            foreach ( $permission as $_permition )
                $options_per[$_permition->id] = $_permition->unique_string;
            echo $this->Form->label('permission_id');
            echo $this->Form->select('permission_id', $options_per);
            
            echo $this->Form->input('allow');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
