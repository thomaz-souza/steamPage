<?php
	$this->variables['pageTitle'] = $this->write("Compras");

	$this->addTagJS('https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', false);
	$this->addTagCSS('https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css', false);
?>

<script>
	
	var datatable,
	url = '<?= $this->getUrl(). "/transaction/Pagseguro/PagseguroAdmin/Compras"; ?>',
	statusList = <?= json_encode(Pagseguro\Notification::statusList()); ?>;

	jQuery(document).ready(function()
	{
		window.datatable = $('#necrologia_table').DataTable({
			serverSide: true,
			ajax: {
				url: url,
				type: 'POST',
				dataFilter: function(response)
				{
					let data = JSON.parse(response);
		            return JSON.stringify(data.data); // return JSON string
		        }
		    },
		    language: <?= json_encode(Datatable\Datatable::getDataTableLanguage()) ?>,
		    
		    columnDefs: [ {
		    	targets: 0,
		    	render: function (data) {
		    		return '<a href="./CompraIndividual?codigo='+data+'">' + data + '</a>';
		    	}
		    },
		    {
		    	targets: 4,
		    	render: function (data, a, b, c) {
		    		switch(data)
		    		{

		    		}
		    		return data;
		    	}
		    }],
		    order:[ [ 3, 'desc' ] ],
		    columns: [
			    { data: 'id' },
			    { data: 'nome_cliente' },
			    { data: 'nome_necrologia' },			    
			    { data: 'dateCreate', "orderSequence": [ "desc" ] },
			    { data: 'status' }
		    ],
		    searchDelay: 1500
		});


		window.datatable.on('preXhr.dt', function(a,b,c){
			blockLoading('.kt-portlet__body');
		});

		window.datatable.on('xhr.dt', function(a,b,c){
			unblockLoading('.kt-portlet__body');
		});

	})

</script>

<!-- begin:: Subheader -->
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Compras</h3>			
		</div>
	</div>
</div>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
	<div class="row">
		
		<div class="col-lg-12">
			
			<div class="card card-custom card-stretch gutter-b">
				
				<div class="card-header align-items-center border-0 mt-4">
					<div class="kt-portlet__head-label">
						<h3 class="kt-portlet__head-title">
							Pedidos realizados
						</h3>
					</div>
				</div>

			
				<div class="card-body pt-4">
					<div class="kt-section kt-section--first">
						<table id="necrologia_table">
							<thead>
								<th>Código</th>
								<th>Nome Comprador</th>								
								<th>Nome Falecido</th>
								<th>Data Compra</th>
								<th>Status</th>
							</thead>
							<tbody>
								
							</tbody>
						</table>
					</div>
				</div>
				<!-- <div class="kt-portlet__foot">
					<div class="kt-form__actions">
						<button type="submit" class="btn btn-primary"><write:ADMIN:Submit></button>
					</div>
				</div> -->
				
			</div>

		</div>	


	</div>
</div>
