<?php
namespace AccessManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;
use Cake\Core\Configure;

/**
 * Roles Model
 *
 * @property \AccessManager\Model\Table\GroupsTable|\Cake\ORM\Association\BelongsTo $Groups
 * @property \AccessManager\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \AccessManager\Model\Entity\Role get($primaryKey, $options = [])
 * @method \AccessManager\Model\Entity\Role newEntity($data = null, array $options = [])
 * @method \AccessManager\Model\Entity\Role[] newEntities(array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Role|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \AccessManager\Model\Entity\Role patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Role[] patchEntities($entities, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Role findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class RolesTable extends Table
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

		$this->setTable('roles');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Acl.Acl', ['type' => 'requester']);
		$this->addBehavior('Timestamp');

		$this->belongsTo('AccessManager.Groups', [
			'foreignKey' => 'group_id',
			'className' => 'AccessManager.Groups'
		]);
		$this->hasMany('AccessManager.Users', [
			'foreignKey' => 'role_id',
			'className' => 'AccessManager.Users'
		]);
		
		$session = new Session();

		// IDs do usuário logado
		$User = $session->read('Auth.User');
		$this->group_id = $User['group_id'];
		$this->role_id = $User['role_id'];
		$this->user_id = $User['id'];
		$this->restrict_group = $User['group']['restrict'];
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
			->integer('id')
			->allowEmpty('id', 'create');

		$validator
			->scalar('name')
			->requirePresence('name', 'create')
			->notEmpty('name');

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
		$rules->add($rules->existsIn(['group_id'], 'Groups'));

		return $rules;
	}

	/**
	 * beforeFind
	 *
	 * Controle de acesso por GroupID
	 */
	public function beforeFind($event, $query, $options, $primary) {

		if($primary) {

			// Group restrict
			if( Configure::read('AccessManager.Roles.restrict_data_by_group')===true ) {

				if($this->restrict_group != null) {

					$query->contain(['Groups']);
					$query->where([
						'OR' => [
							'Groups.restrict >'=>$this->restrict_group
						]
					]);
				}
			}
		}

		return $query;      
	}
}
