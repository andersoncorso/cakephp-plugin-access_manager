<!-- page header --> 
<section class="content-header">
	<h1>Meu login <small>E-mail</small></h1>
	<ol class="breadcrumb">
		<li>
			<?php 
				echo $this->Html->link('<i class="fa fa-angle-double-left"></i> '.__('Voltar'),
					'javascript:window.history.back()',
					['escape' => false]
				);
			?>
		</li>
	</ol>
</section>

<!-- page content -->
<section class="content">
	<div class="row">
		<div class="col-md-3">
			<?php echo $this->element('Layout/user_profile-aside_box', ['user'=>$user, 'profile'=>$user->profile]); ?>
		</div>
		<div class="col-md-9">
			<?php echo $this->element('Forms/users-change_email'); ?>
		</div>
	</div>
</section>