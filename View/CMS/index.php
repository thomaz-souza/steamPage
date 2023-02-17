<?php
	
	$permissions = $this->Main->Interface->getUserPermissions();

	$currentModule = $this->Main->Interface->getModuleId($this->variables['module'], $this->variables['block']);

	if(!empty($currentModule) && !in_array($currentModule['id'], $permissions))
	{
		$this->redirectTo($this->getRouteURL('cms'));
		die();
	}

	//Iniciar a captura de dados
	ob_start();

	//Incorporar arquivo
	require_once($this->Main->getContentBlock($this->variables));

	//Resgatar conteúdo
	$content = ob_get_contents();

	//Limpar os dados de saída
	ob_end_clean();
?>

<script type="text/javascript">
	jQuery(document).ready(function()
		{
			let currentModule = <?= json_encode($currentModule); ?>;

			let currentModuleID = currentModule.id ? currentModule.id : "dashboard";

			//console.log(currentModule);

			$('li[data-id=' + currentModuleID + "]").addClass('menu-item-active');

			if(currentModule.values && currentModule.values.parent)
				$('li[data-id=' + currentModule.values.parent + "]")
					.addClass('menu-item-active')
					.addClass("menu-item-open");

		});

		var blockLoading = function(block)
		{
			let options = {
				css:{
					backgroundColor : 'transparent',
					border: 'none'
				},
				message: '<img src="~<?= $this->Main->getLoadingImage() ?>"/>',
				overlayCSS:  {
					opacity: 0.8,
			        backgroundColor: '#FFF'
			    },
			    fadeIn: 180,
			    fadeOut: 180,
			    baseZ: 90
			};
			if(block) return $(block).block(options);
			$.blockUI(options);
		}

		var unblockLoading = function(block)
		{
			if(block) return $(block).unblock();
			$.unblockUI();
		}

		var growl = function (message)
		{
			$.growlUI()
		}

		toastr.options = {
		  "closeButton": true,
		  "debug": false,
		  "newestOnTop": false,
		  "progressBar": false,
		  "positionClass": "toast-bottom-right",
		  "preventDuplicates": false,
		  "onclick": null,
		  "showDuration": "300",
		  "hideDuration": "1000",
		  "timeOut": "5000",
		  "extendedTimeOut": "1000",
		  "showEasing": "swing",
		  "hideEasing": "linear",
		  "showMethod": "fadeIn",
		  "hideMethod": "fadeOut",
		  "rtl": <?= $this->getLanguageDirection() == 'rtl' ? 'true' : 'false' ?>
		};

	</script>

<!--begin::Body-->
	<body id="kt_body" class="header-static header-mobile-fixed subheader-enabled aside-enabled aside-fixed aside-minimize-hoverable page-loading">

		
		<!--begin::Main-->

		<?= $this->block('n-cms-partial-menu-mobile'); ?>

		<div class="d-flex flex-column flex-root">

			<!--begin::Page-->
			<div class="d-flex flex-row flex-column-fluid page">

				<?= $this->block('n-cms-partial-aside'); ?>

				<!--begin::Wrapper-->
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

					<?= $this->block('n-cms-partial-header'); ?>

					<!--begin::Content-->
					<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">

						<?= $this->block('n-cms-partial-subheader'); ?> 
						
						<!--begin::Entry-->
						<div class="d-flex flex-column-fluid">

							<!--begin::Container-->
							<div class=" container ">

								<?= $content ?>

							</div>

							<!--end::Container-->
						</div>

						<!--end::Entry-->

						<!--[html-partial:include:{"file":"partials/_content.html"}]/-->
					</div>

					<!--end::Content-->

					<?= $this->block('n-cms-partial-footer'); ?>
				</div>

				<!--end::Wrapper-->
			</div>

			<!--end::Page-->
		</div>

		<!--end::Main-->

		