<?php
$this->variables['pageTitle'] = $this->write("Produtos");

$this->using('Pagseguro\\Data');

$this->addTagJS('https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js', false);
$this->addTagCSS('https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css', false);

?>

<script>

	var datatable,
	url = '<?= $this->getUrl(). "/transaction/Pagseguro/Data/AdminListagemProdutos"; ?>';

	jQuery(document).ready(function()
	{
		window.datatable = $('#produtos_table').DataTable({
			ajax: {
				url: url,
				dataFilter: function(data)
				{
					data = JSON.stringify(JSON.parse(data).data);
					console.log(data);
		            return data;
		        }
		    },
		    serverSide: true,
		    columnDefs: [ {
		    	targets: 0,
		    	render: function (data) {
		    		return '<a href="./ProdutosIndividual?codigo='+data+'">' + data + '</a>';
		    	}
		    }],
		    columns: [
			    { data: 'sku' },
			    { data: 'id' },
			    { data: 'descricao' },
			    { data: 'valor' },
			    { data: 'status' }
		    ]

		});
	})
	

</script>

<!-- begin:: Subheader -->
<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Produtos</h3>			
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
							Listagem de produtos
						</h3>
					</div>
					<div class="kt-form__actions">
						<button type="submit" class="btn btn-primary"><write:ADMIN:Create New></button>
					</div>
				</div>


				<div class="card-body pt-4">
					<div class="kt-section kt-section--first">
						<table id="produtos_table">
							<thead>
								<th>Código SKU</th>
								<th>Imagem</th>
								<th>Descrição</th>
								<th>Preço</th>
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
