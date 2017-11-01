<div class="box box-primary">

	<div class="box-header with-border">
		<h3 class="box-title"><i class="fa fa-envelope"></i>&nbsp;&nbsp;<?= __('alterar e-mail') ?></h3>
	</div>
	
	<?= $this->Form->create($user, array('role' => 'form', 'class'=>'form-horizontal')) ?>
		<div class="box-body">
			<br>
			<!-- E-mail -->
			<div class="form-group">
				<label for="email" class="col-sm-2 control-label">E-mail</label>
				<div class="col-sm-6">
					<?php 
						echo $this->Form->input('email', 
							['type'=>'email', 'placeholder'=>'joao@gmail.com', 'label'=>false, 'class'=>'form-control', 'required'=>true]
						);
					?>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-2"></div>
				<div class="col-sm-10">
					<br>
					<button type="submit" class="btn btn-lg btn-primary btn-flat pull-left">Atualizar</button>
				</div>
			</div>
		</div>
	<?= $this->Form->end() ?>
	
</div>
