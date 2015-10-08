<?= $this->Html->script('Acl.jquery-2.1.4.min'); ?>
<script>
    $(document).ready(function() {
        var permissions = new Array();
        var group_or_user_id = $('#group_or_user_id').val();
        getPermissions(group_or_user_id);

        $('#group_or_user_id').change(function () {
            group_or_user_id = $(this).val();
            getPermissions(group_or_user_id);
        });
        
        $('#ck_all').change(function(){
            var allow = $(this).prop( "checked" );
            $('.allow').each(function(){                   
                if ($(this).prop( "checked") != allow) {
                    $(this).prop("checked", allow);
                    var permission_id = $(this).closest('tr').attr('id');
                    enqueuePermissions(permission_id, allow);
                }
            });
        });
        
        $('.allow').change(function() {
            var allow = $(this).prop( "checked" );
            var permission_id = $(this).closest('tr').attr('id');
            enqueuePermissions(permission_id, allow);
        });
        
        $('form').submit(function(event) {
            event.preventDefault();
            if( permissions.length == 0 ) 
                return false;
            
            var permissions_json = JSON.stringify(permissions);
            var group_or_user_id = $('#group_or_user_id').val();
            var group_or_user_a = group_or_user_id.split('-');
            
            $.post( "<?= $this->Url->build(['controller'=>'UserGroupPermission','action'=>'addAjax']) ?>",
                { group_or_user: group_or_user_a[0], group_or_user_id: group_or_user_a[1], permissions: permissions_json } )
                .done(function() {
                    window.location.href = "<?= $this->Url->build(['action'=>'index']) ?>";
                });
            
        });
        
        function enqueuePermissions(permission_id, allow) {
            var index = objIndexOf(permissions, 'id', permission_id);
            
            if( index == -1 ) {
                permissions.push({id:permission_id, allow:allow});
            } else if( permissions[index].allow != allow ) {
                permissions.splice(index, 1);
            }
        }
    });
    
    function objIndexOf(array, attr, value) {
        for(var i = 0; i < array.length; i += 1) {
            if(array[i][attr] === value) {
                return i;
            }
        }
        return -1;
    }

    function getPermissions(group_or_user_id) {
        $('.allow').prop("checked", false);
        var group_or_user_a = group_or_user_id.split('-');
        
        $.post( "<?= $this->Url->build(['controller'=>'UserGroupPermission','action'=>'getPermission']) ?>",
            { group_or_user: group_or_user_a[0], group_or_user_id: group_or_user_a[1] } )
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
                <tr>
                    <td><?= $this->Form->label(__('All')) ?></td>
                    <td><?= $this->Form->checkbox('all', ['id'=>'ck_all']) ?></td>
                </tr>
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
