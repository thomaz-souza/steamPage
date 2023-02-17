<?php
	$this->variables['pageTitle'] = $this->write('Recipients', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);
?>


<!-- Container -->

<div class="row" id="main">
	<div class="col-lg-6">
		<!-- Portlet -->
		<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">

			<div class="card-header align-items-center border-0 mt-4">
				<h3 class="card-title align-items-start flex-column">
					<span su:write="Recipients groups"  su:scope="admin"></span>
				</h3>
			</div>
			<form class="form">
				<div class="card-body pt-4">
					
					<ul id="list">
						<li class="list-item">
							<span><strong>$title</strong></span><br>
							<span>$count <su: write="Recipient(s)" scope="admin"></span>
							<div class="list-item-btn-holder">
								<div class="list-item-btn" onclick="Form.editGroup('$order')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Manage addresses' scope='admin'>"><i class="fas fa-address-card"></i></div>
								<!-- <div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div> -->
							</div>
						</li>								
					</ul>
					
				</div>
			</form>
		</div>
	</div>

	<div class="col-lg-6">
		<!-- Portlet -->
		<div class="card card-custom card-stretch gutter-b" id="portlet-new-user" data-bind="visible: editing, with: editing">

			<div class="card-header align-items-center border-0 mt-4">
				<h3 class="card-title align-items-start flex-column">
					<span su:write="Edit addresses" su:scope="admin"></span>
				</h3>
			</div>

			<div class="card-body pt-4">
				<form class="kt-form" id="form">

					<div class="row">
						<div class="col-lg-6">
							<label><strong su:write="Email" su:scope="admin"></strong></label>
						</div>
						<div class="col-lg-5">
							<label><strong su:write="Name" su:scope="admin"></strong></label>
						</div>
					</div>

					<!-- ko foreach: addresses -->

					<div class="row form-group">
						<div class="col-lg-6">
							<input type="text" class="form-control" data-bind="value:email, style:{ 'border-color': error() ? '#e73d4a' : '' }">
						</div>
						<div class="col-lg-5">
							<input type="text" class="form-control" data-bind="value:name">
						</div>
						<div class="col-lg-1">
							<div class="list-item-btn-holder">
								<div data-bind="click: removeAddress" class="list-item-btn" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Remove address' scope='admin'>"><i class="fas fa-trash"></i></div>
							</div>
						</div>
					</div>

					<!-- /ko -->
					
					<button class="btn btn-secondary" data-bind="click: newAddress"><i class="fas fa-user-plus"></i> <su: write="New address" scope="admin"></button>
					
				</form>
			</div>
			<div class="card-footer">
				<button class="btn btn-secondary" data-bind="click: $root.close"><su: write="Cancel" scope="admin"/></button>
				<button class="btn btn-primary" data-bind="click: save"><su: write="Submit" scope="admin"/></button>
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
	.list-item-btn-holder{
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
	.list-item-btn
	{
		display: inline-block;
		text-align: center;
		padding: 5px;
		margin: 5px;
		/*border: 1px solid #E7E7E7;*/
		color: #CCC;
	}
	
	.list-item-btn:hover{
		color: #333;
		/*border: 1px solid #CCC;*/
	}
</style>

<script>

	list = $('#list').dynamic(
	{
		transaction: Edit.getConfigDynamic,
		filters: {
			'config': 'mail/recipients'
		},
		onData: function(d)
		{
			let list =  [];
			u = 0;
			ko.utils.objectMap(d, function(i,e)
			{
				i.count = Object.keys(i.addresses).length;
				i.order = u;
				u++;
				i.key = e;
				list.push(i);
			})

			Form.setGroups(list);
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



	Address = function (name, email)
	{
		let self = this;

		self.email = ko.observable(email ? email : '');

		self.email.subscribe(function(){
			self.error(false);
			self.email(self.email().replace(/\s/gi,""));
		});

		self.name  = ko.observable(name ? name : '');
		self.error = ko.observable(false);

		self.removeAddress = function()
		{
			let addr = Form.editing().addresses;
			let i = addr().indexOf(self);
			addr.splice(i,1);
		}
	}

	Group = function (item)
	{
		let self = this;

		self.key = item.key;
		self.title = ko.observable(item.title);
		self.addresses = ko.observableArray();

		setAddresses = function()
		{
			self.addresses([]);
			ko.utils.objectMap(item.addresses, function(i,e){
				self.addresses.push(new Address(i, e));
			});

			if(self.addresses().length == 0)
				self.newAddress();
		}
		
		self.newAddress = function()
		{
			self.addresses.push(new Address());
		}

		setAddresses();

		self.save = function()
		{
			let values = {};

			for(i of self.addresses())
			{
				if(i.email().replace(/\s/gi, "") == "")
				{
					i.error(true);
					return toastr.error('<su: write="The email address cannot be empty" scope="admin">');
				}

				if(!i.email().match(<?= MatchLibrary::EMAIL ?>))
				{
					i.error(true);
					return toastr.error('<su: write="One of the email addresses you entered is invalid" scope="admin">');
				}

				values[i.email()] = i.name();
			}

			blockLoading();

			params = {
				values: values,
				config:"mail/recipients/" + self.key,				
				key: "addresses"
			};

			let callback = function (response)
			{
				unblockLoading();
				if(response.status != 200)
					return toastr.error(response.error.message);

				toastr.success('<su: write="Successfully saved" scope="admin">');
				Form.close();
				list.reload();
			}

			Edit.setConfig(params, callback);
		}
	}

	FormModel = function ()
	{
		let self = this;
		self.groups = ko.observableArray();
		self.editing = ko.observable();

		self.setGroups = function(groups)
		{
			self.groups(ko.utils.arrayMap(groups, function(i){
				return new Group(i);
			}));
		}

		self.editGroup = function(g)
		{
			self.editing(self.groups()[g]);
			$('[data-toggle="kt-tooltip"]').tooltip();
		}

		self.close = function()
		{
			self.editing(false);
		}
	}


	var Form = new FormModel();
	ko.applyBindings(Form, document.getElementById('main'));
	
</script>