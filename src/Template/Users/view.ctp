<!-- page header --> 
<section class="content-header">
	<h1><?= __('Usuário').' #'.$user->id ?></h1>
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

		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<?= $this->element('Views/users-view') ?>
				</div>
			</div>
		</div>

	</div>
</section>
