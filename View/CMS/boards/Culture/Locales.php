<?php
	$this->variables['pageTitle'] = $this->write('Locales', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);

	$config = $this->_config('culture');

	function arr_opt (&$item, $key)
	{
		$item = ['id' => $item['label'], 'text' => $item['name'] . " ($item[label])"];
	}

	array_walk($config['languages'], 'arr_opt');
	array_walk($config['timezones'], 'arr_opt');
	array_walk($config['currencies'], 'arr_opt');

	$default = $config['default'];
	
?>


<!-- Container -->

	<div class="row" id="main">
		<div class="col-lg-6">			
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Locales" su:scope="admin"></span>
					</h3>
				</div>
				<form class="form">
					<div class="card-body pt-4">
						<ul id="list">
							<li class="list-item">
								<span><strong>$label</strong></span><br>
								<div class="list-item-btn-holder">
									$if(!$default){
									<div class="list-item-btn" onclick="setAsDefault('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Set as default' scope='admin'>"><i class="fas fa-key"></i></div> }
									<div class="list-item-btn" onclick="form.loadKey('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-pen"></i></div>
									<div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>
								</div>
							</li>								
						</ul>
					</div>
				</form>
				<div class="card-footer" id="list_page">
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-new-user">				
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Edit/Create" su:scope="admin"></span>
					</h3>
				</div>
				<div class="card-body pt-4">
					<form class="kt-form" id="form">
						<div class="kt-section kt-section--first">

							<div class="form-group">
								<label su:write="Label" su:scope="admin"></label>
								<input type="text" name="label" class="form-control">
							</div>
							
							<div class="form-group">
								<label su:write="Language" su:scope="admin"></label>
								<select name="language" class="form-control">
									<?= CMS\Forms::intoOptions($config['languages']); ?>
								</select>
							</div>

							<div class="form-group">
								<label su:write="Timezone" su:scope="admin"></label>
								<select name="timezone" class="form-control">
									<?= CMS\Forms::intoOptions($config['timezones']) ?>
								</select>
							</div>

							<div class="form-group">
								<label su:write="Currency" su:scope="admin"></label>
								<select name="currency" class="form-control">
									<?= CMS\Forms::intoOptions($config['currencies']) ?>
								</select>
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
	.list-item .list-item-btn span{
		margin-left: 5px;
		font-size: 10px;
		display: none;
	}
	.list-item .list-item-btn:hover{
		color: #333;
		/*border: 1px solid #CCC;*/
	}
	.list-item .list-item-btn:hover span{
		display: inline-block;
	}
</style>

<script>

	var defaultLocale = '<?= $default ?>';

	list = $('#list').dynamic(
	{
		transaction: Edit.getConfigDynamic,
		filters: {
			'config': 'culture/cultures'
		},
		onData: function(d)
		{
			let list =  [];
			ko.utils.objectMap(d, function(i,e)
			{
				i.key = e;
				i.default = (e == defaultLocale);
				list.push(i);
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
			'config': 'culture/cultures',
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

	setAsDefault = function (response, x)
	{
		if(!x && confirm('<su: write="Are you sure you want to set this locale as default?" scope="admin">'))
		{
			blockLoading();
			defaultLocale = response;
			return Edit.setConfig(
			{
				'config': 'culture',
				'key': 'default',
				'values': response
			}, setAsDefault);
		}
		if(!x) return;
		unblockLoading();

		if(response.status == 200)
			list.reload();		
	}


	form.deleteConfirm = function(key)
	{
		if(confirm('<su: write="Are you sure you want to delete?" scope="admin">'))
			this.delete(key);
	}

</script>