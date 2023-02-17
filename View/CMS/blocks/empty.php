<div class="card card-custom card-stretch gutter-b">
	<div class="card-header align-items-center border-0 mt-4">
		<h3 class="card-title align-items-start flex-column">
			<span class="font-weight-bolder text-danger"><i class="fas fa-ban text-danger"></i> <su: write="Block not found" scope="admin"/></span>
		</h3>
	</div>
	<div class="card-body pt-4">
		<p class="text-muted"><?= !empty($block['title']) ? $block['title'] : $id; ?></p>
	</div>
</div>