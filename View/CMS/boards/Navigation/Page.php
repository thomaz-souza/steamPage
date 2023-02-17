<?php
	$this->variables['pageTitle'] = $this->write("ADMIN:Pages and Blocks");


?>

<script>
	
	jQuery(document).ready(function(){
		$('.select2').select2();
		$('.select2-tags').select2({tags: true});
		var datatable = $('#table').KTDatatable();
	})


</script>

<!-- begin:: Subheader -->
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title"><write:ADMIN:Pages and Blocks/></h3>			
		</div>
	</div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		
		<div class="col-lg-5">
			
			<div class="card card-custom card-stretch gutter-b">
				
				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							<write:ADMIN:Create/Edit/>
						</h3>
					</div>
				</div>

				<!--begin::Form-->
				<!-- <form class="kt-form">
					<div class="card-body pt-4">
						<div class="kt-section kt-section--first">
							<div class="form-group">
								<label><write:ADMIN:Block/Page Name></label>
								<input type="text" name="id" class="form-control">
								<span class="form-text text-muted"><write:ADMIN:Use only characters, numbers - or _, with no spaces or special characters></span>
							</div>							
							<div class="form-group">
								<label><write:ADMIN:Response Mime-Type></label>
								<input type="text" name="" class="form-control">
								<span class="form-text text-muted"><write:ADMIN:The Default value is 'text/html'></span>
							</div>
							<div class="form-group">
								<label><write:ADMIN:Static URL Path></label>
								<input type="text" name="static" class="form-control">
							</div>
							<div class="form-group">
								<label><write:ADMIN:URL Matching Expression></label>
								<input type="text" name="match" class="form-control">
							</div>

							<div class="form-group">
								<label><write:ADMIN:Pattern URL></label>
								<input type="text" name="pattern" class="form-control">
							</div>

							<div class="form-group">
								<label><write:ADMIN:URL Variables></label>
								<select type="text" name="variables" class="form-control select2-tags" multiple="multiple"></select>
							</div>
							
							<div class="form-group">
								<label><write:ADMIN:Before blocks></label>
								<select name="beforeBlocks" class="form-control select2" multiple="multiple">
								
								</select>
							</div>
							<div class="form-group">
								<label><write:ADMIN:After blocks></label>
								<select name="afterBlocks" class="form-control select2" multiple="multiple">
								</select>
							</div>
							<div class="form-group">
								<label><write:ADMIN:Redirect to page></label>
								<input type="text" name="redirect" class="form-control">								
							</div>
							<div class="form-group">
								<label><write:ADMIN:Redirect code></label>
								<input type="text" name="redirectCode" class="form-control">								
							</div>
						</div>
					</div>
					<div class="kt-portlet__foot">
						<div class="kt-form__actions">
							<button type="submit" class="btn btn-primary"><write:ADMIN:Submit></button>
						</div>
					</div>
				</form> -->

				<!--end::Form-->
			</div>

		</div>

		<div class="col-lg-7">

			<div class="card card-custom card-stretch gutter-b">
			
				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							<write:ADMIN:Elements list/>
						</h3>
					</div>
				</div>

				
				<div class="card-body pt-4">
					
					<div id="table">
					</div>

				</div>
			</div>
		</div>

		


	</div>
</div>
