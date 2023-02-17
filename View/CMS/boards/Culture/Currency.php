<?php
	$this->variables['pageTitle'] = $this->write('Currency', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);
?>



<!-- Container -->

	<div class="row"id="main">
		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Currencies" su:scope="admin"></span>
					</h3>
				</div>
				<form class="form">
					<div class="card-body pt-4">
						<ul id="list">
							<li class="list-item">
								<span><strong>$name</strong></span><br>
								<span>$label - $symbol</span>
								<div class="list-item-btn-holder">
									<div class="list-item-btn" onclick="form.loadKey('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-pen"></i></div>
									<div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>
								</div>
							</li>								
						</ul>
					</div>
				</form>
			</div>
		</div>

		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Edit/Create" su:scope="admin"></span>
					</h3>
				</div>
				<div class="card-body pt-4">
					<form class="kt-form" id="form">
						<div class="kt-section kt-section--first" >
							<div class="form-group">
								<label su:write="Label" su:scope="admin"></label>
								<input type="text" name="label" class="form-control">
							</div>
							
							<div class="form-group">
								<label su:write="Name" su:scope="admin"></label>
								<input type="text" name="name" class="form-control">
							</div>

							<div class="row">
								<div class="form-group col-lg-6">
									<label su:write="Symbol" su:scope="admin"></label>
									<input type="text" name="symbol" class="form-control">
								</div>

								<div class="form-group col-lg-6">
									<label su:write="Symbol placing" su:scope="admin"></label>
									<input type="text" name="symbolPlacing" class="form-control">
								</div>
							</div>

							<div class="row">
								<div class="form-group col-lg-6">
									<label su:write="Decimal mark" su:scope="admin"></label>
									<input type="text" name="decimalMark" class="form-control">
								</div>

								<div class="form-group col-lg-6">
									<label su:write="Thousand mark" su:scope="admin"></label>
									<input type="text" name="thousandMark" class="form-control">
								</div>
							</div>

							<div class="form-group">
								<label su:write="Decimal places" su:scope="admin"></label>
								<input type="number" name="decimalPlaces" class="form-control">
							</div>	

						</div>
					</form>
				</div>
				<div class="card-footer">
					<button type="submit" id="submit" class="btn btn-primary" su:write="Submit" su:scope="admin"></button>
					<button class="btn btn-primary" onclick="window.form.toDefault()" su:write="Create new" su:scope="admin"></button>
				</div>				
			</div>
		</div>

	</div>

<style type="text/css">
	#list
	{
		display: flex;
		flex-wrap: wrap;
		flex-direction: column;
		list-style: none;
		padding: 0px;
	}
	.list-item{
		flex:0 0 100%;
		padding: 5px;
		margin: 5px;
		border-bottom: 1px dashed #E7E7E7;
		position: relative;
	}
	.list-item .list-item-btn-holder{
		display: flex;
		position: absolute;
		right: 0;
		top: 0;
		cursor: pointer;
		height: 100%;
		flex-direction: row;
		justify-content: center;
		align-items: center;
	}
	.list-item .list-item-btn
	{
		display: inline-block;
		text-align: center;
		padding: 5px;
		margin: 5px;
		/*border: 1px solid #E7E7E7;*/
		color: #CCC;
	}
	
	.list-item .list-item-btn:hover{
		color: #333;
		/*border: 1px solid #CCC;*/
	}
</style>

<script>

	list = $('#list').dynamic(
	{
		transaction: Edit.getConfigDynamic,
		filters: {
			'config': 'culture/currencies'
		},
		onData: function(d)
		{
			let list =  [];
			ko.utils.objectMap(d, function(i,e)
			{
				list.push(Object.assign(i,{key:e}));
			})
			return list;
		},
		onLoading: function(a)
		{
			if(a) blockLoading('#list')
			else unblockLoading('#list');
		},
		onPlot: function ()
		{
			$('[data-toggle="kt-tooltip"]').tooltip();
		}
	});

	form = $('#form').storm({
		transactionSelect: Edit.getConfigStorm,
		transaction: Edit.setConfigStorm,
		key: "label",
		filters:{
			'config': 'culture/currencies',
			'keySource': 'label'
			//'processor': 'Culture/CMS::Edit'
		},
		submitButton: $('#submit'),
		onLoading: function(a)
		{
			if(a) blockLoading()
			else unblockLoading();
		},
		onSubmitted: function()
		{
			list.reload();
			form.toDefault();
		},
		onDeleted: function()
		{
			list.reload();
		},
		onData: function()
		{
			$('[name=label]').prop('disabled', true);
		},
		onReset: function()
		{
			$('[name=label]').prop('disabled', false);
		}
	});

	form.deleteConfirm = function(key)
	{
		if(confirm('<su: write="Are you sure you want to delete?" scope="admin">'))
			this.delete(key);
	}

</script>