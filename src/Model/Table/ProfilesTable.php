<?php
namespace AccessManager\Model\Table;

// for beforeMarshal callback
use Cake\Event\Event;
use ArrayObject;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * @property \AccessManager\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \AccessManager\Model\Table\EstadosTable|\Cake\ORM\Association\BelongsTo $Estados
 * @property \AccessManager\Model\Table\MunicipiosTable|\Cake\ORM\Association\BelongsTo $Municipios
 *
 * @method \AccessManager\Model\Entity\Profile get($primaryKey, $options = [])
 * @method \AccessManager\Model\Entity\Profile newEntity($data = null, array $options = [])
 * @method \AccessManager\Model\Entity\Profile[] newEntities(array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Profile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \AccessManager\Model\Entity\Profile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Profile[] patchEntities($entities, array $data, array $options = [])
 * @method \AccessManager\Model\Entity\Profile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ProfilesTable extends Table
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

		$this->setTable('profiles');
		$this->setDisplayField('first_name');
		$this->setPrimaryKey('id');

		$this->addBehavior('Timestamp');

		$this->belongsTo('AccessManager.Users', [
			'foreignKey' => 'user_id',
			'className' => 'AccessManager.Users'
		]);
		$this->belongsTo('Places.Estados', [
			'foreignKey' => 'estado_id',
			'className' => 'Places.Estados'
		]);
		$this->belongsTo('Places.Municipios', [
			'foreignKey' => 'municipio_id',
			'className' => 'Places.Municipios'
		]);
	}

	public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {   
		// CPF
		if (isset($data['cpf'])) {
			$data['cpf'] = preg_replace('/[^0-9]/', '', $data['cpf']);
		}
		// Phone 01
		if (isset($data['phone01'])) {
			$data['phone01'] = preg_replace('/[^0-9]/', '', $data['phone01']);
		}
		// CEP
		if (isset($data['cep'])) {
			$data['cep'] = preg_replace('/[^0-9]/', '', $data['cep']);
		}
	}

	/**
	 * Default validation rules.
	 *
	 * @param \Cake\Validation\Validator $validator Validator instance.
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator) {
		$validator
			->integer('id')
			->allowEmpty('id', 'create');

		$validator
				->add('user_id', 'unique', [
						'rule'=>'validateUnique',
						'provider'=>'table',
						'message'=>'Este usuário já tem um perfil cadastrado!'
					]
				);

		$validator
			->requirePresence('first_name', 'create')
			->notEmpty('first_name', 'Campo obrigatório*');

		$validator
			->requirePresence('last_name', 'create')
			->notEmpty('first_name', 'Campo obrigatório*');

		$validator
			->requirePresence('cpf', 'create')
			->notEmpty('cpf', 'Campo obrigatório*')
			->numeric('cpf', 'Informe apenas números.')
			->add('cpf', 'unique', [
					'rule' => ['validateUnique'],
					'provider'=>'table',
					'message'=>'Este CPF já está sendo usado por outro cadastro.'
				]
			)
			->add('cpf', 'minLength', [
					'rule' => ['minLength', 11],
					'last' => true,
					'message' => 'CPF inválido.'
				]
			)
			->add('cpf', 'maxLength', [
					'rule' => ['maxLength', 11],
					'last' => true,
					'message' => 'CPF inválido.'
				]
			);

		$validator
			->requirePresence('phone01', 'create')
			->numeric('phone01', 'Informe apenas números.')
			->allowEmpty('phone01')
			->add('phone01', 'minLength', [
					'rule' => ['minLength', 10],
					'last' => true,
					'message' => 'Número de telefone inváldo.'
				]
			)
			->add('phone01', 'maxLength', [
					'rule' => ['maxLength', 11],
					'last' => true,
					'message' => 'Número de telefone inváldo.'
				]
			);

		$validator
			->requirePresence('logradouro', 'create')
			->notEmpty('logradouro', 'Campo obrigatório*');

		$validator
			->numeric('num', 'Informe apenas números.')
			->allowEmpty('num');

		$validator
			->allowEmpty('complemento');

		$validator
			->requirePresence('setor', 'create')
			->notEmpty('setor', 'Campo obrigatório*');

		$validator
			->requirePresence('municipio_id', 'create')
			->notEmpty('municipio_id', 'Campo obrigatório*');

		$validator
			->numeric('cep', 'Informe apenas números.')
			->allowEmpty('cep')
			->add('cep', 'minLength', [
					'rule' => ['minLength', 8],
					'last' => true,
					'message' => 'CEP inváldo.'
				]
			)
			->add('cep', 'maxLength', [
					'rule' => ['maxLength', 8],
					'last' => true,
					'message' => 'CEP inváldo.'
				]
			);

		$validator
			->allowEmpty('caixa_postal')
			->numeric('caixa_postal', 'Informe apenas números.');

		$validator
			->notEmpty('associado', 'Campo obrigatório*')
			->numeric('cpf', 'Informe apenas números.')
			->add('associado', 'boolean', [
					'rule'=>'boolean',
					'message'=>'Informe "Sim" ou "Não".'
				]
			);

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
		$rules->add($rules->existsIn(['user_id'], 'Users'));
		$rules->add($rules->existsIn(['estado_id'], 'Estados'));
		$rules->add($rules->existsIn(['municipio_id'], 'Municipios'));

		return $rules;
	}

	/**
	 * Personalized Finder for find Profiles
	 */
	public function findProfile(\Cake\ORM\Query $query, array $options) {
		$query->select([
			'id',
			'user_id',
			'first_name',
			'last_name',
			'full_name' => $this->query()->func()->concat([
				'first_name' => 'literal',
				'last_name' => 'literal'
			]),
			'cpf',
			'phone01',
			'logradouro',
			'num',
			'complemento',
			'setor',
			'estado_id',
			'municipio_id',
			'cep',
			'caixa_postal'
			// ...
		]);
		return $query;
	}

}
