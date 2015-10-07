<?= $this->Html->script('Acl.jquery-2.1.4.min'); ?>
<script>
    $(document).ready(function() {
        var group_or_user_id = $('#group_or_user_id').val();
        get_permissions(group_or_user_id);

        $('#group_or_user_id').change(function () {
            group_or_user_id = $(this).val();
            get_permissions(group_or_user_id);
        });
    });

    function get_permissions(group_or_user_id) {
        $.post( "<?= $this->Url->build(['controller'=>'UserGroupPermission','action'=>'getPermission']) ?>",
            { group_or_user_id: group_or_user_id } )
            .done(function(data) {
                if(data != 'fail') {
                    $.each(JSON.parse(data), function (i, item) {
                        $('#' + item.permission_id + ' .allow').prop("checked", item.allow);
                    });
                }
            });
    }
</script>
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
            echo $this->Form->select('group_or_user_id', $options_users, ['id' => 'group_or_user_id']);
            ?>
        </fieldset>
        <table cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?= __('Prefix/Controller/Action') ?></th>
                    <th><?= __('Allow/Deny') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $permission as $_permition ) :?>
                    <tr id="<?= $_permition->id ?>">
                        <td class="unique_string_td"><?= $_permition->unique_string ?></td>
                        <td class="allow_td"><?= $this->Form->input('allow', ['label'=>false, 'class'=>'allow']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
