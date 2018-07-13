<?php 
	$iconEdit = '<i class="fa fa-pencil"></i>';
	$iconUpdate = '<i class="fa fa-pencil-square-o"></i>';
	$iconDel = '<i class="fa fa-trash"></i>';
?>
<!-- page content -->
<section class="content">
	<div class="row">

		<div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header with-border">
					<i class="fa fa-list"></i>
					<h3 class="box-title">
						<?= __('Grupo') ?>
					</h3>
				</div>
				<div class="box-body">
					<?= $this->element('Views/groups-view') ?>
				</div>
				<div class="box-footer text-right">
				<?php
					echo $this->Html->link(
						$iconEdit.' editar',
						['action'=>'edit', $group->id, 'plugin'=>'AccessManager'],
						['escape'=>false, 'class'=>'btn btn-default']
					);
					echo '&nbsp;&nbsp;';
					echo $this->Form->postLink(
						$iconDel.' excluir',
						['action'=>'delete', $group->id, 'plugin'=>'AccessManager'],
						['confirm'=>__("Confirma a exclusão?"), 'escape'=>false, 'class'=>'btn btn-default']
					);
				?>
				</div>
			</div>
		</div>

	</div>
</section>
