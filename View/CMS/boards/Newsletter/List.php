<?php
	
	$this->variables['pageTitle'] = 'Newsletter';
	
	$this->using('CMSController\\Edit');
?>

	<div class="row" id="main">
		<div class="col-lg-12">
			<!-- Portlet -->
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:var="pageTitle"></span>
					</h3>
					<div class="card-toolbar">

						<div class="row">
							<div class="form-group col-lg-7">
								<input id="search_name" class="form-control" placeholder="<?= $this->write('Search', 'admin') ?>">
							</div>
							<div class="form-group col-lg-5">								
                        		<button onclick="window.open('<?= $this->getRouteURL('newsletter-export');?>', '_blank')" class="btn btn-primary form-control"> <su: write="Export" scope="admin"/></button>
							</div>
						</div>
                    </div>
				</div>
				<div class="form">
					<div class="card-body">
						<div id="datatable_newsletter"></div>
					</div>
				</div>				
			</div>
		</div>
	</div>

<script>
	
	var datatable = $('#datatable_newsletter').KTDatatable({

		 data: {
            type: 'remote',
            source: {
                read: {
                    url:'<?= $this->getRouteURL("newsletter-table") ?>',
                    // sample custom headers
                    // headers: {'x-my-custom-header': 'some value', 'x-test-header': 'the value'},
                    map: function(response)
                    {
                        if(response)
                        	return response.data;
                        return [];
                    },
                },
            },
            pageSize: 10,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true,
            saveState:{
            	webstorage:false,
            	cookie:false
            }
        },
        sortable: true,

		pagination: true,
        
        layout: {
            scroll: false,
            footer: false,
            spinner:{
            	message: false
            }
        },
        search: {
            input: $('#search_name'),
            delay: 400,
        },
        columns: [
        	{
	            field: 'date_create',
	            title: 'Data',
	            sortable: 'desc',
	            width: 120,
	            textAlign: 'center'
	        },
	        {
	            field: 'email',
	            title: 'E-mail',
	            width:250,
	            textAlign: 'center'
	        },
	        {
	            field: 'name',
	            title: 'Nome',
	            width:250,
	            textAlign: 'center'
	        }
	        
       ],
	        
        translate:{
        	records:{
        		processing: '',
        		noRecords: '<su: write="No records found" scope="admin">'
        	},
        	toolbar:
        	{
        		pagination:{
        			items:{
        				default: {
						  first: '<su: write="First" scope="admin">',
						  prev: '<su: write="Previous" scope="admin">',
						  next: '<su: write="Next" scope="admin">',
						  last: '<su: write="Last" scope="admin">',
						  more: '<su: write="More pages" scope="admin">',
						  input: '<su: write="Page number" scope="admin">',
						  select: '<su: write="Select page size" scope="admin">'
						},
						info: '<su: write="Displaying {{start}} - {{end}} of {{total}} records" scope="admin">'
        			}
        		}
        	}        	
        }
	});
	
</script>