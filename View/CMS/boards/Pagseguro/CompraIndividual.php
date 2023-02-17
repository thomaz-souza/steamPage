<?php

$codigo = addslashes($_GET['codigo']);

$this->using('Pagseguro\\PagseguroAdmin');

$compra = $this->PagseguroAdmin->compra($codigo);

$this->variables['pageTitle'] = "Compra - " . $codigo;

$this->addTagJS('assets/formcontrol.js');
$this->addTagHeadJS('assets/default.js');


?>
<!-- begin:: Subheader -->
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Registro de Compra<span></span></h3>			
		</div>
	</div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		
		<div class="col-lg-8">			
			<div class="card card-custom card-stretch gutter-b">				
				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							DETALHES DO CLIENTE
						</h3>
					</div>
				</div>
				<div class="card-body pt-4">
					<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
						<div class="row col-lg-12 order-lg-12">
							<div class="col-lg-2 order-lg-2">
								<label><strong>Nome</strong></label>
								<p data-bind-text="cliente">Lucas de Lima</p>
							</div>	
						</div>
					</div>
				</div>

				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							DETALHES DA COMPRA
						</h3>
					</div>
				</div>
				<div class="card-body pt-4">
					<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
						<div class="row col-lg-12 order-lg-12">
							<div class="col-lg-2 order-lg-2">
								<label><strong>Nome</strong></label>
								<p data-bind-text="cliente">Lucas de Lima</p>
							</div>	
						</div>
					</div>
				</div>

				<div class="kt-portlet__foot">
					<div class="kt-form__actions">
						<input type="hidden" data-bind-value="id">
						<button type="submit" id="btn-sumit-form" onclick="salvar();" class="btn btn-primary"><write:ADMIN:Submit></button>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">			
			<div class="card card-custom card-stretch gutter-b">				
				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							ANDAMENTO DO PEDIDO
						</h3>
					</div>
				</div>
				<div class="card-body pt-4">
					<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

						<div class="kt-list-timeline">
		                    <div class="kt-list-timeline__items" id="timeline-items">
		                    </div>
		                </div>		                
					</div>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
		
	var data = <?= json_encode($compra); ?>,
	statusList = <?= json_encode(Pagseguro\Notification::statusList()); ?>;

	function statusModel (c)
	{
		let statusData = statusList[c.status];
		return '<div class="kt-list-timeline__item">' +
            '<span class="kt-list-timeline__badge kt-list-timeline__badge--' + statusData.badge + '"></span>' +
            '<span class="kt-list-timeline__text" title="' + statusData.description + '">' + statusData.title + '</span>' +
            '<span class="kt-list-timeline__time">' + assembler.formatDate(c.dateCreate, 'd/m/Y h:i:s') + '</span>' +
        '</div>';
	}

	assembler.plotter(data.status, statusModel, '#timeline-items');

	/*Incluir lista de status*/
	



	//window.form = new FormControl(data);
	



</script>