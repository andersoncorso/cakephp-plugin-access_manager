<?php
namespace AccessManager\Model\Entity;

use Cake\ORM\Entity;

/**
 * Profile Entity
 *
 * @property int $id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $cpf
 * @property string $phone01
 * @property string $logradouro
 * @property int $num
 * @property string $complemento
 * @property string $setor
 * @property int $estado_id
 * @property int $municipio_id
 * @property string $cep
 * @property int $caixa_postal
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \AccessManager\Model\Entity\User $user
 * @property \AccessManager\Model\Entity\Estado $estado
 * @property \AccessManager\Model\Entity\Municipio $municipio
 */
class Profile extends Entity
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
		'user_id' => true,
		'first_name' => true,
		'last_name' => true,
		'cpf' => true,
		'phone01' => true,
		'logradouro' => true,
		'num' => true,
		'complemento' => true,
		'setor' => true,
		'estado_id' => true,
		'municipio_id' => true,
		'cep' => true,
		'caixa_postal' => true,
		'created' => true,
		'modified' => true,
		'user' => true,
		'estado' => true,
		'municipio' => true
	];

	protected $_virtual = ['full_name'];

	protected function _getFullName() {
		return $this->_properties['first_name'] . ' ' . $this->_properties['last_name'];
	}
}
