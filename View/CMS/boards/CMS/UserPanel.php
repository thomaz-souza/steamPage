<?php
	$this->variables['pageTitle'] = $this->write("Users", 'admin');
	$this->using('CMSController\\User');
	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);
?>

<!-- Container -->

	<div class="row">

		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">				
				<!-- <div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span class="font-weight-bolder text-dark" su:write="Users" su:scope="admin"></span>
					</h3>
				</div> -->
				<form class="form">
					<div class="card-body pt-4">
						<ul id="userlist">
							<li class="userlist-item">
								<span><strong>$name</strong></span><br>
								<span>$login</span>
								<div class="userlist-item-btn-holder">
									<div class="userlist-item-btn" onclick="User.userform.loadKey('$id')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-user-edit"></i></div>
									<div class="userlist-item-btn" onclick="User.deleteUserConfirm('$id');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>
								</div>
							</li>								
						</ul>
					</div>
				</form>
				<div class="card-footer" id="userlist_page"></div>
			</div>
		</div>

		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-new-user">				
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Edit/Create user" su:scope="admin"></span>
					</h3>
				</div>
				<div class="card-body">
					<form class="form" id="userform" >
						<div class="kt-section kt-section--first">							
							<div class="form-group">
								<label su:write="Full name" su:scope="admin"></label>
								<input type="text" name="name" class="form-control">
							</div>

							<div class="form-group">
								<label su:write="Login" su:scope="admin"></label>
								<input type="text" name="login" class="form-control">
							</div>

							<div class="form-group">
								<label su:write="Password" su:scope="admin"></label>
								<input type="password" name="password" class="form-control">
							</div>

							<div class="form-group">
								<label><i class="far fa-window-maximize"></i> <su: write="Menu" scope="admin"></label>
								<ul id="permission-list">

								
									<?php foreach ($this->Main->Interface->getAllModulesIds(['sideMenu','topBarMenu','topMenu','userMenu']) as $module => $value): ?>
										
									<li <?php if(!empty($value['parent'])) echo 'style="border-left:1px dotted #c1c5d7;padding-left:11px;margin-left:5px;"';  ?>>
										<input type="checkbox" name="permissions[]" value="<?= $module ?>" id="permission-<?= $module ?>">
										<label for="permission-<?= $module ?>"><i class="<?= $value['icon'] ?>"></i> <?= $value['title'] ?></label>
									</li>
									
									<?php endforeach; ?>

								</ul>
							</div>

							<?php 

								$permissions = $this->Main->Interface->getAllModulesIds(['permissions']);
								if(count($permissions) > 0): 
									
							?>
							<div class="form-group">
								<label><i class="fas fa-lock-open"></i> <su: write="Permissions" scope="admin"></label>
								<ul id="permission-list">

									<?php foreach ($permissions as $module => $value): ?>
										
									<li <?php if(!empty($value['parent'])) echo 'style="border-left:1px dotted #c1c5d7;padding-left:11px;margin-left:5px;"';  ?>>
										<input type="checkbox" name="permissions[]" value="<?= $module ?>" id="permission-<?= $module ?>">
										<label for="permission-<?= $module ?>"><i class="<?= $value['icon'] ?>"></i> <?= $value['title'] ?></label>
									</li>
									
									<?php endforeach; ?>
								</ul>
							</div>
							<?php endif; ?>

							<?php 

								$events = $this->Main->Interface->getAllModulesIds(['events']);
								if(count($events) > 0): 

							?>
							<div class="form-group">
								<label><i class="fas fa-bell"></i> <su: write="Notifications" scope="admin"></label>
								<ul id="permission-list">

									<?php foreach ($events as $module => $value): ?>
										
									<li <?php if(!empty($value['parent'])) echo 'style="border-left:1px dotted #c1c5d7;padding-left:11px;margin-left:5px;"';  ?>>
										<input type="checkbox" name="permissions[]" value="<?= $module ?>" id="permission-<?= $module ?>">
										<label for="permission-<?= $module ?>"><i class="<?= $value['icon'] ?>"></i> <?= $value['title'] ?></label>
									</li>
									
									<?php endforeach; ?>

								</ul>
							</div>
							<?php endif; ?>


							<?php 

								$blocks = $this->Main->Interface->getAllModulesIds(['blocks']);
								if(count($blocks) > 0): 

							?>
							<div class="form-group">
								<label><i class="fas fa-border-all"></i> <su: write="Dashboard blocks" scope="admin"></label>
								<ul id="permission-list">

									<?php foreach ($blocks as $module => $value): ?>
										
									<li <?php if(!empty($value['parent'])) echo 'style="border-left:1px dotted #c1c5d7;padding-left:11px;margin-left:5px;"';  ?>>
										<input type="checkbox" name="permissions[]" value="<?= $module ?>" id="permission_block-<?= $module ?>">
										<label for="permission_block-<?= $module ?>"><i class="<?= $value['icon'] ?>"></i> <?= $value['title'] ?></label>
									</li>
									
									<?php endforeach; ?>

								</ul>
							</div>
							<?php endif; ?>


						</div>
					</form>
				</div>
				<div class="card-footer">
					<button type="submit" id="usersubmit" class="btn btn-primary" su:write="Submit" su:scope="admin"></button>
					<button id="userreset" class="btn btn-primary" onclick="User.userform.reset()" su:write="New user" su:scope="admin"></button>
				</div>				
			</div>
		</div>


		
	</div>

<style type="text/css">
	#permission-list{
		list-style: none;
		padding: 0px;
		background-color: #F7F7F7;
	    padding: 5px 12px;
	    max-height: 250px;
	    min-height: 150px;
	    overflow: auto;
	}
	#permission-list li{
		padding: 6px 0px;
	}
	#userlist
	{
		display: flex;
		flex-wrap: wrap;
		flex-direction: column;
		list-style: none;
		padding: 0px;
	}
	.userlist-item{
		flex:0 0 100%;
		padding: 5px;
		margin: 5px;
		border-bottom: 1px dashed #E7E7E7;
		position: relative;
	}
	.userlist-item .userlist-item-btn-holder{
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
	.userlist-item .userlist-item-btn
	{
		display: inline-block;
		text-align: center;
		padding: 5px;
		margin: 5px;
		/*border: 1px solid #E7E7E7;*/
		color: #CCC;
	}
	
	.userlist-item .userlist-item-btn:hover{
		color: #333;
		/*border: 1px solid #CCC;*/
	}
</style>

<script>

	User.loading = function (t)
	{
		if(t) blockLoading();
		else unblockLoading();
	}

	User.list = $('#userlist').dynamic({

		transaction: User.list,
	//	template: User.template,
		onLoading: User.loading,
		paginate: {
			onPage: $('#userlist_page'),
			currentPage: 1,
			perPage: 8
		},
		onPlot: function ()
		{
			$('[data-toggle="kt-tooltip"]').tooltip();
		}
	});

	User.deleteUserConfirm = function (id)
	{
		if(User.list._d.length < 2)
			return toastr.error('<su: write="You must keep at least one user" scope="admin">');

		if(id == '<?= $this->Main->getUser('id') ?>')
			return toastr.error('<su: write="You cannot delete your own profile" scope="admin">');
		
		if(confirm('<su: write="Are you sure you want to delete?" scope="admin">'))
		{
			User.userform.delete(id);
		}
	}

	User.userform = $('#userform').storm({
		transactionSelect: User.userSelect,
		transactionDelete: User.userDelete,
		transactionUpdate: User.userUpdate,
		transactionInsert: User.userInsert,
		key:'id',
		submitButton:$('#usersubmit'),
		onLoading: User.loading,
		onSubmitted: function(){
			User.list.reload();
			User.userform.reset();
			toastr.success('<su: write="Success" scope="admin">');
		},
		onData:function(data)
		{
			for(let i=0; i<data.permissions.length; i++)
				data[data.permissions[i]] = true;
			
			return data;
		},
		onDeleted: function(){
			User.list.reload();
		}
	})


</script>