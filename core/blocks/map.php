<?php global $BlockMap; // print_r($BlockMap); ?>
<div class="col-md-12">
	<div class="page-header">
		<h1><?php echo $BlockMap['block_title']; ?></h1>
		<div class="alert alert-danger text-center"><?php echo (!empty($BlockMap['called-from'])) ? $BlockMap['called-from'] : 'Block loader'; ?></div>
	</div>
	<div class="row">
		<p>
			<?php echo $BlockMap['block_description']; ?>
		</p>
	</div>
</div>