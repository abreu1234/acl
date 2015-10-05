<?php
namespace Acl\Model\Table;

use Acl\Model\Entity\UserGroupPermission;
use Cake\ORM\Query;
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
        return $rules;
    }

}
