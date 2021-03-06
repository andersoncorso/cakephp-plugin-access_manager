<?php
namespace AccessManager\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Network\Session;
use Cake\Core\Configure;

/**
 * Groups Model
 *
 * @property \AccessManager\Model\Table\RolesTable|\Cake\ORM\Association\HasMany $Roles
 * @property \AccessManager\Model\Table\UsersTable|\Cake\ORM\Association\HasMany $Users
 *
 * @method \AccessManager\Model\Entity\Group get($primaryKey, $options = [])
 * @method \AccessManager\Model\Entity\Group newEntity($data = null, array $options = [])
 * @method \AccessManager\Model\Entity\Group[] newEntities(array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Group|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \AccessManager\Model\Entity\Group patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Group[] patchEntities($entities, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Group findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GroupsTable extends Table
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

		$this->setTable('groups');
		$this->setDisplayField('name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Acl.Acl', ['type' => 'requester']);
		$this->addBehavior('Timestamp');

		$this->hasMany('AccessManager.Roles', [
			'foreignKey' => 'group_id',
			'className' => 'AccessManager.Roles'
		]);
		$this->hasMany('AccessManager.Users', [
			'foreignKey' => 'group_id',
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
	 * beforeFind
	 *
	 * Controle de acesso por Group restrict
	 */
   public function beforeFind($event, $query, $options, $primary) {

   		if($primary) {

			// Group restrict
			if( Configure::read('AccessManager.Groups.restrict_data_by_group')===true ) {

				if($this->restrict_group != null) {

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
