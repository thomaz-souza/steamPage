
<!--begin::Subheader-->
<div class="subheader py-2 py-lg-4  subheader-solid " id="kt_subheader">
	<div class=" container-fluid  d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">

		<!--begin::Details-->
		<div class="d-flex align-items-center flex-wrap mr-2">

			<!--begin::Title-->
			<h5 class="text-dark font-weight-bold mt-2 mb-2 mr-5" su:var="pageTitle"></h5>

			<!--end::Title-->
			<?php if(isset($this->variables['subheader'])): ?>
			<!--begin::Separator-->
			<div class="subheader-separator subheader-separator-ver mt-2 mb-2 mr-5 bg-gray-200"></div>

			<!--end::Separator-->

			<!--begin::Search Form-->
			<div class="d-flex align-items-center" id="kt_subheader_search">
				<span class="text-dark-50 font-weight-bold" id="kt_subheader_total">
					<?= $this->variables['subheader']; ?>
				</span>
			</div>
			<?php endif;?>

			<!--end::Search Form-->
		</div>

		<!--end::Details-->

		<!--begin::Toolbar-->
		<?php if(isset($this->variables['subheader_toolbar'])):  ?>
		<div class="d-flex align-items-center">
			<?= $this->variables['subheader_toolbar'] ?>
		</div>
		<?php endif; ?>
		<!--end::Toolbar-->
		
	</div>
</div>

<!--end::Subheader-->