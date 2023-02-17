<?php
	$this->variables['pageTitle'] = $this->write('Accounts', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);
?>


<!-- Modal -->
    <div class="modal fade" id="testEmailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" su:write="Sending test" su:scope="admin"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fas fa-times"></i></span>
            </button>
          </div>
          <div class="modal-body">

            <div class="row">
            	<div class="col-lg-12">
            		<p su:write="Attention! If you are using localhost some servers can deny your connection. Sometimes, if your settings are not correct, it can take a long time to test the connection." su:scope="admin"></p>
            	</div>

                <div class="col-lg-12 form-group">
                	<label su:write="Enter a email address to receive a test message" su:scope="admin"></label>
                    <input type="text" id="testEmail" class="form-control">
                </div>

                <div class="col-lg-12" id="testResponse">

                </div>
            </div>
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><su: write="Cancel" scope="admin"></button>
            <button type="button" class="btn btn-success" onclick="runTest()"><su: write="Send" scope="admin"></button>
          </div>
        </div>
      </div>
    </div>

<!-- Container -->

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="main">
	<div class="row">
		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">				
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Accounts" su:scope="admin"></span>
					</h3>
				</div>
				<form class="kt-form">
					<div class="card-body pt-4">
						<div class="kt-section kt-section--first">
							<ul id="list">
								<li class="list-item">
									<span><strong>$fromName</strong></span><br>
									<span>$fromEmail</span><br>
									<span>$host</span>
									<div class="list-item-btn-holder">
										<div class="list-item-btn" onclick="openSendingTest('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Sending test' scope='admin'>"><i class="fas fa-location-arrow"></i></div>
										<div class="list-item-btn" onclick="form.loadKey('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-pen"></i></div>
										<div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>										
									</div>
								</li>								
							</ul>
						</div>
					</div>
				</form>
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
					<form class="form" id="form">
						<div class="form-group">
							<label su:write="Label" su:scope="admin"></label>
							<input type="text" name="label" class="form-control">
						</div>
						
						<div class="form-group">
							<label su:write="Sender Name" su:scope="admin"></label>
							<input type="text" name="fromName" class="form-control">
						</div>

						<div class="form-group">
							<label su:write="Sender Email" su:scope="admin"></label>
							<input type="text" name="fromEmail" class="form-control">
						</div>

						<div class="row">
							<div class="form-group col-lg-2">
								<label su:write="Port" su:scope="admin"></label>
								<input type="text" name="port" class="form-control">
							</div>

							<div class="form-group col-lg-6">
								<label su:write="Host" su:scope="admin"></label>
								<input type="text" name="host" class="form-control">
							</div>

							<div class="form-group col-lg-4">
								<label su:write="Security" su:scope="admin"></label>
								<select name="SMTPSecure" class="form-control">
									<option value="0" su:write="No security" su:scope="admin"></option>
									<option value="ssl" su:write="SSL" su:scope="admin"></option>
									<option value="tls" su:write="TLS" su:scope="admin"></option>
									<option value="ssl-special" su:write="Special SSL" su:scope="admin"></option>
									<option value="tls-special" su:write="Special TLS" su:scope="admin"></option>
								</select>	
							</div>
						</div>

						<div class="row">
							<div class="form-group col-lg-6">
								<label su:write="Username" su:scope="admin"></label>
								<input type="text" name="username" class="form-control">
							</div>

							<div class="form-group col-lg-6">
								<label su:write="Password" su:scope="admin"></label>
								<input type="text" name="password" class="form-control">
							</div>
						</div>

						<div class="row">
							<div class="form-group col-lg-6">
								<input type="checkbox" id="SMTPAuth" name="SMTPAuth" value="1" data-value-unchecked="0">
								<label for="SMTPAuth" su:write="Requires authentication" su:scope="admin"></label>
							</div>
						</div>

						<div class="row">
							<div class="form-group col-lg-6">
								<label su:write="Charset" su:scope="admin"></label>
								<input type="text" name="charSet" value="UTF-8" class="form-control">
							</div>

							<div class="form-group col-lg-6">
								<label su:write="Encoding" su:scope="admin"></label>
								<input type="text" name="encoding" value="base64" class="form-control">
							</div>
						</div>

						<div class="form-group">
							<label su:write="Debug" su:scope="admin"></label>
							<select name="SMTPDebug" class="form-control">
								<option value="0" su:write="No debug" su:scope="admin"></option>
								<option value="1" su:write="Handshake" su:scope="admin"></option>
								<option value="2" su:write="Connection" su:scope="admin"></option>
								<option value="3" su:write="Detailed connection" su:scope="admin"></option>
							</select>
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
			'config': 'mail/accounts'
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
			'config': 'mail/accounts',
			'keySource': 'label'
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


	list.sendingTestFrom = null;

	function openSendingTest (key)
	{
		$('#testResponse').html('');
		$('#testEmailModal').modal('show');
		list.sendingTestFrom = key;
	}

	function runTest ()
	{
		$('#testResponse').html('');
		blockLoading('.modal-content');

		Edit.call({
			 _call: "Mail/CMS::sendingTestEmail",
            account: list.sendingTestFrom,
            email: $('#testEmail').val()
		}, function(response){
			
			unblockLoading('.modal-content');

			if(response.status == 200)
			{
				toastr.success('<su: scope="admin" write="Message successfully sent">');
				return $('#testEmailModal').modal('hide');
			}

			$('#testResponse').html(response.error.message);		

		});
	}

</script>