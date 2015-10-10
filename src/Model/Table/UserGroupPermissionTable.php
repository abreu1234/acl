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
namespace Acl\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserGroupPermission Model
 *
 * @property \Cake\ORM\Association\BelongsTo $GroupOrUsers
 * @property \Cake\ORM\Association\BelongsTo $Permission
 */
class UserGroupPermissionTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('user_group_permission');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->belongsTo('Permission', [
            'foreignKey' => 'permission_id',
            'joinType' => 'INNER',
            'className' => 'Acl.Permission'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['permission_id'], 'Permission'));
        $rules->add($rules->isUnique(['permission_id', 'group_or_user', 'group_or_user_id']));

        return $rules;
    }
    
    public function findUserGroupPermission(array $fields, $group_or_user_id, $group_or_user, $permision_id) 
    {
        return $this->find()
            ->select($fields)
            ->where(
                [
                    'group_or_user_id' => $group_or_user_id,
                    'group_or_user' => $group_or_user,
                    'permission_id' => $permision_id
                ]
            );
    }

}
