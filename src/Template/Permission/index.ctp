<?php
/**
 * Copyright (c) Rafael Abreu
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.rafaelabreu.eti.br CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List User Group Permission'), ['controller' => 'UserGroupPermission', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Permission'), ['controller' => 'Permission', 'action' => 'index']) ?></li>
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
