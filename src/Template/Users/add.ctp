<!-- page content -->
<section class="content">
	<div class="row">

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<i class="fa fa-edit"></i>
					<h3 class="box-title">
						<?= __('Cadastrar Usuário') ?>
					</h3>
				</div>
				<div class="box-body">
					<?= $this->element('Forms/users-add') ?>
				</div>
			</div>
		</div>

	</div>
</section>

<?php
$this->Html->script([
	'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-show-password/1.0.3/bootstrap-show-password.min.js'
],
['block' => 'script']);
?>
<?php $this->start('scriptBottom'); ?>
<script>
	$(function () {

		//Password hide/show
		$("#password").password('hide');

	});
</script>
<?php $this->end(); ?>
