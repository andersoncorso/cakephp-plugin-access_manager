<?php
namespace AccessManager\Controller;

use AccessManager\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;

/**
 * Users Controller
 *
 * @property \AccessManager\Model\Table\UsersTable $Users
 *
 * @method \AccessManager\Model\Entity\User[] paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{

	public function initialize() {
		parent::initialize();
		
		$this->Auth->allow(['login', 'logout', 'recoverPassword']);

		// IDs do usuário logado
		$this->group_id = $this->Auth->user('group_id');
		$this->role_id = $this->Auth->user('role_id');
		$this->user_id = $this->Auth->user('id');
	}

/**
 ==== FUNÇÕES DE CRUD ====
 */
	public function index() {

		$this->paginate = [
			'contain' => ['Groups', 'Roles']
		];
		$users = $this->paginate($this->Users);

		$this->set(compact('users'));
	}

	public function view($id = null) {

		if( !$this->Users->exists(['Users.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$user = $this->Users->get($id, [
			'contain' => ['Groups', 'Roles', 'Profiles'],
		]);

		$this->set('user', $user);
	}

	public function add() {

		$user = $this->Users->newEntity();
		if ($this->request->is('post')) {

			$user = $this->Users->patchEntity($user, $this->request->getData());
			if ($this->Users->save($user)) {

				$this->Flash->success(__('Os dados foram salvos.'));
				return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
			}
			$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
		}
		$groups = $this->Users->Groups->find('list', ['limit' => 200]);
		$roles = $this->Users->Roles->find('list', ['limit' => 200]);
		$this->set(compact('user', 'groups', 'roles'));
	}

	public function edit($id = null) {

		if( !$this->Users->exists(['Users.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$user = $this->Users->get($id);
		if ($this->request->is(['patch', 'post', 'put'])) {

			$user = $this->Users->patchEntity($user, $this->request->getData());
			if ($this->Users->save($user)) {

				$this->Flash->success(__('Os dados foram salvos.'));

				return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
			}
			$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
		}

		$groups = $this->Users->Groups->find('list', ['limit' => 200]);
		$roles = $this->Users->Roles->find('list', ['limit' => 200]);
		$this->set(compact('user', 'groups', 'roles'));
	}

	public function delete($id = null) {

		if( !$this->Users->exists(['Users.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$this->request->allowMethod(['post', 'delete']);
		
		$user = $this->Users->get($id);
		if ($this->Users->delete($user)) {

			$this->Flash->success(__('O registro foi excluido.'));
		} 
		else {
			
			$this->Flash->error(__('Erro ao excluir o registro. Por favor, tente novamente ou entre em contato.'));
		}

		return $this->redirect(['action' => 'index', 'plugin'=>'AccessManager']);
	}



/**
 ==== FUNÇÕES DE USUARIO ====
 */
	public function profile($user_id=null){

		if( !$this->Users->exists(['Users.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$user = $this->Users->get($user_id, [
			'contain'=>['Profiles', 'Roles']
		]);
		$Municipios = null;

		if ($this->request->is(['patch', 'post', 'put'])) {

			$user = $this->Users->patchEntity($user, $this->request->getData(), ['associated' => ['Profiles']]);
			if ($this->Users->save($user)) {
				
				$this->Flash->success(__('Os dados foram salvos.', 'Profile'));
				return $this->redirect(['action'=>$this->request->params['action'], $user->id]);
			}
			else {
				
				$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.', 'Profile'));
			}
		}
		$this->set(compact('user'));

		// Estados/Município
		$Estados = TableRegistry::get('Places.Estados')->find('list');
		$Estados = $Estados->toArray();
		$this->set(compact('Estados', 'Municipios'));
	}

	public function userProfile(){
		
		// ID do usuário logado
		$user_id = $this->Auth->user('id');

		if( !$this->Users->exists(['Users.id'=>$id]) ) {

			$this->Flash->raw(__('Nenhum registro encontrado.'));
			return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
		}

		$user = $this->Users->get($user_id, [
			'contain'=>['Profiles', 'Roles']
		]);

		if ($this->request->is(['patch', 'post', 'put'])) {

			$user = $this->Users->patchEntity($user, $this->request->getData(), ['associated'=>['Profiles'], 'validate'=>'withprofile']);

			if ($this->Users->save($user)) {

				$this->__authUpdateSession(); // atualiza dados da sessão do usuário

				$this->Flash->success(__('Os dados foram salvos.'));
				return $this->redirect(['action'=>$this->request->params['action']]);
			}
			else {
				
				$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.', 'Profile'));
			}
		}
	
		$this->set(compact('user'));

		// Estados/Município
		$Estados = TableRegistry::get('Places.Estados')->find('list');
		$Estados = $Estados->toArray();

		$Municipios = null;
		if(!is_null($user['profile']['estado_id'])){

			$Municipios =
				TableRegistry::get('Places.Municipios')->find('list', ['limit' => 1024])
					->where(['estado_id'=>$user['profile']['estado_id']]);
		}
		$this->set(compact('Estados', 'Municipios'));
	}



/**
 ==== FUNÇÕES DE ACESSO ====
 */

	public function login() {

		if(!empty($this->request->session()->read('Auth.User'))){

			return $this->redirect($this->Auth->redirectUrl());
		}
		else{
			
			if ($this->request->is('post')) {
				$user = $this->Auth->identify();
				if ($user) {

					// Menus for user roles (bootstrap in line 282)
					$user['menu'] = Configure::read('Menu.'.$user['role_id']);

					$this->Auth->setUser($user);
					return $this->redirect($this->Auth->redirectUrl());
				}
				$this->Flash->error(__('Falha no login. Por favor, forneça um nome de usuário e senha válidos.'));
			}
		}
	}

	public function logout() {
		return $this->redirect($this->Auth->logout());
	}

	public function changeEmail($user_id=null) {

		if(!empty($user_id)){

			if( !$this->Users->exists(['Users.id'=>$user_id]) ) {

				$this->Flash->raw(__('Nenhum registro encontrado.'));
				return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
			}

			$user = $this->Users->get($user_id, [
				'contain'=>['Profiles', 'Roles']
			]);

			if ($this->request->is(['patch', 'post', 'put'])) {

				$user = $this->Users->patchEntity($user, $this->request->getData());
				if ($this->Users->save($user)) {

					$this->__authUpdateSession(); // atualiza dados da sessão do usuário

					$this->Flash->success(__('Os dados foram salvos.'));
					return $this->redirect(['action'=>$this->request->params['action'], $user->id, 'plugin'=>'AccessManager']);
				}
				else {

					$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
				}
			}
		}
		else{

			$this->Flash->error(__('Registro não idenficado.'));
			return $this->redirect(['controller'=>'Dashboards', 'action'=>'index', 'plugin'=>false]);
		}

		$this->set(compact('user'));
	}

	public function userChangeEmail(){
		
		// ID do usuário logado
		$user_id = $this->Auth->user('id');
		return $this->changeEmail($user_id);
	}

	public function changePassword($user_id=null) {
		
		if(!empty($user_id)){

			if( !$this->Users->exists(['Users.id'=>$user_id]) ) {

				$this->Flash->raw(__('Nenhum registro encontrado.'));
				return $this->redirect(['controller'=>'Pages', 'action'=>'display', 'message', 'plugin'=>false]);
			}

			$user = $this->Users->get($user_id, [
				'contain'=>['Profiles', 'Roles']
			]);

			if ($this->request->is(['patch', 'post', 'put'])) {

				$user = $this->Users->patchEntity($user, $this->request->getData());
				if ($this->Users->save($user)) {

					$this->Flash->success(__('Os dados foram salvos.'));
					return $this->redirect(['action'=>$this->request->params['action'], $user->id, 'plugin'=>'AccessManager']);
				}
				else {

					$this->Flash->error(__('Erro ao salvar os dados. Por favor, verifique e tente novamente ou entre em contato.'));
				}
			}
		}
		else{

			$this->Flash->error(__('Registro não idenficado.'));
			return $this->redirect(['controller'=>'Dashboards', 'action'=>'index', 'plugin'=>false]);
		}

		$this->set(compact('user'));
	}

	public function userChangePassword($id=null){
		
		// ID do usuário logado
		$user_id = $this->Auth->user('id');
		return $this->changePassword($user_id);
	}

	public function recoverPassword(){

		// $user = $this->Users->newEntity();

		// if ($this->request->is(['post']) and $this->request->getData('email')) {

		// 	// encontra o registro na tabela
		// 	$email = $this->request->getData('email');
		// 	$query = $this->Users->findByEmail($email);
		// 	$query
		// 		->where(['active'=>true]);
			
		// 	if (!$query->isEmpty()) {
			
		// 		$user = $query->first();
				
		// 		// gera uma nova hash de recuperação
		// 		$salt = Configure::read('Security.salt');
		// 		$rand = mt_rand(1,999999999);
		// 		$rand_v2 = mt_rand(1,999999999);
		// 		$user->recovery_hash = hash('sha256', $email.$rand.$salt.$rand_v2);

		// 		// update Field recovery_hash
		// 		if ($this->Users->save($user)) {
					
		// 			// // envia e-mail
		// 			$Email = new Email('default');
		// 			$Email
		// 				->emailFormat('html')
		// 				->template('AccessManager.recoveryPassword')
		// 				->to($user->email)
		// 				->subject('IRRIGO - Recuperar senha')
		// 				->set([
		// 					'recovery_hash' => $user->recovery_hash
		// 				]);						
		// 			if ( $Email->send() ) {

		// 				$this->Flash->raw(__('Foi enviado um e-mail com o link para alteração da sua senha!'));
		// 				$this->redirect(['controller'=>'pages', 'action'=>'display', 'message', 'plugin'=>false]);
		// 			}
		// 		}
		// 		else{

		// 			$this->Flash->error(__('Falha na solicitação. Por favor, verifique e tente novamente ou entre em contato.'));
		// 		}
		// 	}
		// 	else{

		// 		$this->Flash->error(__('Nenhum registro encontrado com esse e-mail!'));
		// 	}
		// }

		// $this->set(compact('user'));
	}

	public function changePasswordByHash($hash=null) {

		// 
	}


	

/**
 ==== FUNÇÕES AUXILIARES ====
 */
	protected function __authUpdateSession(){

		// ID do usuário logado
		$user_id = $this->Auth->user('id');

		$user = $this->Users->get($user_id, [
			'contain'=>['Profiles', 'Roles']
		]);

		if ($user) {

			// Menus for user roles (bootstrap in line 282)
			$user['menu'] = Configure::read('Menu.'.$user['role_id']);
			$this->Auth->setUser($user);

			return true;
		}
		else {

			return false;
		}
	}

}
