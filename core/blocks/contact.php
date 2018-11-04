<?php global $BlockContact; // print_r($BlockContact); ?>
<div class="col-md-12">
	<div class="page-header">
		<h1><?php echo $BlockContact['block_title']; ?></h1>
		<p>
			<?php echo $BlockContact['block_description']; ?>
		</p>
		<div class="alert alert-danger text-center"><?php echo (!empty($BlockContact['called-from'])) ? $BlockContact['called-from'] : 'Block loader'; ?></div>
	</div>
	<div class="row">
		<form action="contact_submit" method="GET" accept-charset="utf-8">
			<div class="form-group form-row col-md-6">
				<input type="text" name="" value="" class="form-control">
			</div>
			<div class="form-group form-row col-md-6">
				<input type="submit" name="" value="send" class="form-control btn btn-success">
			</div>
		</form>
	</div>
</div>