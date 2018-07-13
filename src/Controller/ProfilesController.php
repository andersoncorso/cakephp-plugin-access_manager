<?php
namespace AccessManager\Controller;

use AccessManager\Controller\AppController;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Profiles Controller
 *
 * @property \AccessManager\Model\Table\ProfilesTable $Profiles
 *
 * @method \AccessManager\Model\Entity\Profile[] paginate($object = null, array $settings = [])
 */
class ProfilesController extends AppController
{

	public function initialize() {
		parent::initialize();
	}

/**
 ==== FUNÇÕES DE CRUD ====
 */
	public function index() {

		$this->paginate = [
			'sortWhitelist' => [
				'id',
				'user_id',
				'first_name',
				'last_name',
				'full_name'
			],
			'finder' => 'Profile',
			'order' => [
				'Profile.full_name' => 'asc'
			],
			'contain' => ['Users', 'Estados', 'Municipios'],
			'limit'=>10000
		];

		$query = $this->Profiles->find('all')
			->contain([
				'Estados' => function ($q) {
					return $q
						->select(['nome']);
				},
				'Municipios' => function ($q) {
					return $q
						->select(['nome']);
				},
			]);

		$profiles = $this->paginate($query);
		$this->set(compact(['profiles']));
	}

	public function view($id = null) {

		if( !$this->Profiles->exists(['Profiles.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$profile = $this->Profiles->get($id, [
			'contain' => ['Users', 'Estados', 'Municipios']
		]);

		$this->set('profile', $profile);
	}

	public function edit($id = null) {

		if( !$this->Profiles->exists(['Profiles.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$profile = $this->Profiles->get($id, [
			'contain' => ['Users']
		]);

		if ($this->request->is(['patch', 'post', 'put'])) {

			$profile = $this->Profiles->patchEntity($profile, $this->request->getData());
			if ($this->Profiles->save($profile)) {

				$this->Flash->success(__('Os dados foram salvos.'));

				return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
			}
			$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
		}

		$this->set(compact('profile'));

		// Usuários
		$exception = array($profile['user_id']); // para exibir o usuário atual na lista de $Users
		$Users = $this->__listUsersToProfile($exception);

		// Estados/Município
		$Estados = TableRegistry::get('Places.Estados')->find('list');
		$Estados = $Estados->toArray();

		$Municipios = null;
		if(!is_null($profile['estado_id'])){

			$Municipios =
				TableRegistry::get('Places.Municipios')->find('list', ['limit' => 1024])
					->where(['estado_id'=>$profile['estado_id']]);
		}
		$this->set(compact('Estados', 'Municipios', 'Users'));
	}

	public function add() {

		$profile = $this->Profiles->newEntity();
		if ($this->request->is('post')) {

			$profile = $this->Profiles->patchEntity($profile, $this->request->getData());
			if ($this->Profiles->save($profile)) {

				$this->Flash->success(__('Os dados foram salvos.'));
				return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
			}
			$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
		}

		$this->set(compact('profile'));

		// Usuários
		$Users = $this->__listUsersToProfile();

		// Estados/Município
		$Estados = TableRegistry::get('Places.Estados')->find('list');
		$Estados = $Estados->toArray();

		$Municipios = null;
		if(!is_null($profile['estado_id'])){

			$Municipios =
				TableRegistry::get('Places.Municipios')->find('list', ['limit' => 1024])
					->where(['estado_id'=>$profile['estado_id']]);
		}
		$this->set(compact('Estados', 'Municipios', 'Users'));
	}

	public function delete($id = null) {

		$this->request->allowMethod(['post', 'delete']);

		if( !$this->Profiles->exists(['Profiles.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$profile = $this->Profiles->get($id);
		if ($this->Profiles->delete($profile)) {

			$this->Flash->success(__('O registro foi excluido.'));
		} 
		else {
			
			$this->Flash->error(__('Erro ao excluir o registro. Por favor, tente novamente ou entre em contato.'));
		}

		return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
	}

	/**
	 * Lista de Usuários que não tem Perfil cadastrado
	 * 
	 * @param array $exception : Lista de excessóes ID
	 * @return array $users
	 */
	protected function __listUsersToProfile($exception=null){

		$matchingProfiles = $this->Profiles->find()
			->select(['user_id'])
			->distinct();

		if(!is_null($exception) and is_array($exception))
			$matchingProfiles->where(['id NOT IN' => $exception]);

		$query = TableRegistry::get('AccessManager.Users')->find('list')
			->where(['id NOT IN' => $matchingProfiles]);

		return $query->toArray();
	}
}
