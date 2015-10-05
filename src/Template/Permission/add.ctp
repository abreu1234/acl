<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Permission'), ['action' => 'index']) ?></li>
    </ul>
</nav>
<div class="permission form large-9 medium-8 columns content">
    <fieldset>
        <legend><?= __('Add Permission') ?></legend>
        <?= $this->Form->postLink(__('Synchronize'), ['action' => 'synchronize']) ?>
    </fieldset>
</div>
