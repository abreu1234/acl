<?php
namespace Acl\Model\Table;

use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Permission Model
 *
 */
class PermissionTable extends Table
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

        $this->table('permission');
        $this->displayField('id');
        $this->primaryKey('id');

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

        $validator
            ->requirePresence('controller', 'create')
            ->notEmpty('controller');

        $validator
            ->requirePresence('action', 'create')
            ->notEmpty('action');

        $validator->add('controller_action', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table'
            ])->allowEmpty('controller_action', 'create');

        return $validator;
    }

    public function beforeSave(Event $event, Entity $entity, \ArrayObject $options)
    {
        $entity->set('controller_action', trim($entity->controller.'->'.$entity->action));

        $validator = new Validator();
        $validator->provider('table', $this);
        $validator->add('controller_action', 'unique', [
            'rule' => 'validateUnique',
            'provider' => 'table'
        ]);

        $data = ['controller_action' => $entity->controller_action];
        $errors = $validator->errors($data);

        return empty($errors);
    }

}
