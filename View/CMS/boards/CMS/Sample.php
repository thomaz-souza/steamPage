<?php
	$this->variables['pageTitle'] = 'Page title';

	$this->using('CMSController\\Edit');
?>

	<div class="row"id="main">

		<!-- Primeiro padrão -->
		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span>Block title</span>
					</h3>
					<div class="card-toolbar">
                        <button class="btn btn-success"><i class="fas fa-pepper-hot"></i>Upper button</button>
                    </div>
				</div>
				<div class="card-body pt-4">
					<p>Content</p>
				</div>
				<div class="card-footer">
				</div>
			</div>
		</div>

		<!-- Bloco com botão acima -->
		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
			            <span class="card-label font-weight-bolder text-dark">Block title</span>
			            <span class="text-muted mt-3 font-weight-bold font-size-sm">Description below title</span>
			        </h3>
					<div class="card-toolbar">
                        <div class="card-toolbar">
				            <div class="dropdown dropdown-inline">
				                <a href="#" class="btn btn-clean btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				                    <i class="ki ki-bold-more-ver"></i>
				                </a>
				                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right" style="">
				                    <!--begin::Navigation-->
									<ul class="navi navi-hover">
									    <li class="navi-header font-weight-bold py-4">
									        <span class="font-size-lg">Choose Label:</span>
									        <i class="flaticon2-information icon-md text-muted" data-toggle="tooltip" data-placement="right" title="" data-original-title="Click to learn more..."></i>
									    </li>
									    <li class="navi-separator mb-3 opacity-70"></li>
									    <li class="navi-item">
									        <a href="#" class="navi-link">
									            <span class="navi-text">
									                <span class="label label-xl label-inline label-light-success">Customer</span>
									            </span>
									        </a>
									    </li>
									    <li class="navi-item">
										    <a href="#" class="navi-link">
									            <span class="navi-icon"><i class="flaticon2-list-3"></i></span>
									            <span class="navi-text">Contacts</span>
									        </a>
									    </li>
									    <li class="navi-separator mt-3 opacity-70"></li>
									    <li class="navi-footer py-4">
									        <a class="btn btn-clean font-weight-bold btn-sm" href="#">
									            <i class="ki ki-plus icon-sm"></i>
									            Add new
									        </a>
									    </li>
									</ul>
									<!--end::Navigation-->
				                </div>
				            </div>
				        </div>
                    </div>
				</div>
				<div class="card-body pt-4">
					<p>Content</p>
				</div>
				<div class="card-footer">

				</div>
			</div>
		</div>


		<!-- Colorido -->
		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header bg-danger align-items-center border-0">
					<h3 class="card-title text-white align-items-start flex-column">
						<span>Block title</span>
					</h3>
					<div class="card-toolbar">
                        <button class="btn btn-transparent-white"><i class="fas fa-pepper-hot"></i>Upper button</button>
                    </div>
				</div>
				<div class="card-body pt-4">
					<p>Content</p>
				</div>
			</div>
		</div>

		<!-- Sem título -->
		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-body pt-4">
					<div class="d-flex align-items-center justify-content-between card-spacer flex-grow-1">
			            <span class="symbol  symbol-50 symbol-light-secondary mr-2">
			                <span class="symbol-label">
			                    <span class="icon-danger"><i class="fas fa-pepper-hot"></i></span></span>
			            </span>
			            <div class="d-flex flex-column text-right">
			                <span class="text-dark-75 font-weight-bolder font-size-h3">750$</span>
			                <span class="text-muted font-weight-bold mt-2">Weekly Income</span>
			            </div>
			        </div>
				    <p>Content</p>
				</div>
			</div>
		</div>		

		<!-- Formulário -->
		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span>Form title</span>
					</h3>
					<div class="card-toolbar">
						<button class="btn btn-danger btn-icon"><i class="fas fa-pepper-hot"></i></button>
						<button class="btn btn-danger btn-icon btn-sm"><i class="fas fa-pepper-hot"></i></button>
					</div>
				</div>
				<div class="card-body pt-4">
					<form class="form" id="form">

						<div class="form-group">
							<label>Text field</label>
							<input type="text" class="form-control">
							<span class="form-text text-muted">Optional instructions for this field</span>
						</div>

						<div class="row">
							<div class="form-group col-lg-6">
								<label>Invalid field</label>
								<input class="form-control is-invalid" type="text">
							</div>

							<div class="form-group col-lg-6">
								<label>Valid Field</label>
								<input class="form-control is-valid" type="text">
							</div>
						</div>

						<div class="form-group">
							<label class="form-control-label">Icon</label>
							<div class="input-group">
								
								<div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-pepper-hot"></i></span></div>

								<input type="text" class="form-control" placeholder="Email">

								<div class="input-group-append"><span class="input-group-text"><i class="fas fa-pepper-hot"></i></span></div>

							</div>
						</div>
						
						<div class="row">
							<div class="form-group col-lg-6">
								<label>Date and Time</label>
								<input class="form-control" type="datetime-local">
							</div>

							<div class="form-group col-lg-6">
								<label>Select</label>
								<select class="form-control">
									<option>Option 1</option>
									<option>Option 2</option>
								</select>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-3 col-form-label">Switch 1</label>
							<div class="col-3">
								<span class="switch">								
									<label>
										<input type="checkbox">
										<span></span>
									</label>
								</span>
							</div>
							<label class="col-3 col-form-label">Switch 2</label>
							<div class="col-3">
								<span class="switch switch-success switch-icon">
									<label>
										<input type="checkbox" checked="checked">
										<span></span>
									</label>
								</span>
							</div>
						</div>
					
					</form>
				</div>
				<div class="card-footer">
					<button class="btn btn-primary" onclick="toastr.info('Primary')"> Primary</button>
					<button class="btn btn-warning" onclick="toastr.warning('Warning')"> Warning</button>
					<button class="btn btn-success" onclick="toastr.success('Success')"> Success</button>
					<button class="btn btn-secondary" onclick="toastr.info('Secondary')"> Secondary</button>
					<button class="btn btn-danger" onclick="toastr.error('Danger')"> Danger</button>
				</div>
			</div>
		</div>
	</div>