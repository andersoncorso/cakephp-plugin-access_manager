<!-- Profile Image -->
<?php
	$url_img = ($this->request->session()->read('Auth.User.image'))?
					$this->Url->image('layout/user-icon.png', ['fullBase'=>true]):
					$this->Url->image('layout/user-icon.png', ['fullBase'=>true]);
?>

<?php if(!empty($user->profile)): ?>
	<!-- User data -->
	<div class="box box-default <?php if($this->request->is('mobile')) echo 'collapsed-box'; ?>">
		<?php if($this->request->is('mobile')): ?>
		<div class="box-header with-border">
			<h3 class="box-title">Perfil</h3>
			<div class="box-tools pull-right">
				<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
			</div>
		</div>
		<?php endif; ?>
		<div class="box-body box-profile" <?php if($this->request->is('mobile')) echo 'style="display: none;"'; ?>>
			<?php 
				echo $this->Html->image($url_img, 
					['class'=>'profile-user-img img-responsive img-circle', 'alt'=>'Foto do usuário']
				);
			?>
			<h3 class="profile-username text-center">
				<?= $user->profile->full_name ?>
			</h3>
			<p class="text-muted text-center"><?= $user->role->name ?></p>
		</div>
	</div>
<?php endif; ?>

<!-- Dados de Acesso -->
<div class="box box-default <?php if($this->request->is('mobile')) echo 'collapsed-box'; ?>">
	<?php if($this->request->is('mobile')): ?>
	<div class="box-header with-border">
		<h3 class="box-title">Dados de Acesso</h3>
		<div class="box-tools pull-right">
			<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
		</div>
	</div>
	<?php endif; ?>
	<div class="box-body" <?php if($this->request->is('mobile')) echo 'style="display: none;"'; ?>>
		<strong>
			<i class="fa fa-envelope margin-r-5"></i>
			<?= $user->email ?>
		</strong>
		<p class="text-muted">
			<small>Cadastrado em <?= $user->created ?></small>
		</p>
		<strong><i class="fa fa-clock-o margin-r-5"></i> Última alteração</strong>
		<p class="text-muted"><?= $user->modified ?></p>
		<div class="btn-group">
		<?php
			echo $this->Html->link('E-mail',
				['controller'=>'Users', 'action'=>'ChangeEmail', $user->id],
				['class'=>'btn btn-default']
			);
			echo $this->Html->link('Senha',
				['controller'=>'Users', 'action'=>'ChangePassword', $user->id],
				['class'=>'btn btn-default']
			);
		?>
		</div>
	</div>
</div>