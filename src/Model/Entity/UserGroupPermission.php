<?php
namespace Acl\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserGroupPermission Entity.
 *
 * @property int $id
 * @property \Acl\Model\Entity\GroupOrUser $group_or_user
 * @property int $group_or_user_id
 * @property int $permission_id
 * @property \Acl\Model\Entity\Permission $permission
 */
class UserGroupPermission extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
