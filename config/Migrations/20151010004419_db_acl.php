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
use Phinx\Migration\AbstractMigration;

class DbAcl extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('permission');
        $table->addColumn('prefix', 'string', ['null'=>true,'limit'=>100])
            ->addColumn('controller', 'string', ['null'=>false,'limit'=>20])
            ->addColumn('action', 'string', ['null'=>false,'limit'=>100])
            ->addColumn('unique_string', 'string', ['null'=>false,'limit'=>130])
            ->create();
        $table = $this->table('user_group_permission');
        $table->addColumn('group_or_user', 'string', ['null'=>false,'limit'=>20])
            ->addColumn('group_or_user_id', 'integer', ['null'=>false])
            ->addColumn('permission_id', 'integer', ['null'=>false])
            ->addColumn('allow', 'boolean', ['default'=>0])
            ->addForeignKey('permission_id', 'permission', 'id', ['delete'=>'CASCADE','update'=>'NO_ACTION'])
            ->create();
    }

}
