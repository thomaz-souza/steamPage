<?php
	$this->variables['pageTitle'] = $this->write("Bug tracker", 'admin');

	$bugTracker = new CMS\BugTracker();
	$parsedLog = $bugTracker->getParsedLog();

	$this->variables['subheader'] = $this->write("%s error in log", 'admin', count($parsedLog));
?>

<style>
	
	table{
		width: 100%;
	}
	tr td, tr th{
		padding: 8px 10px;
		border-bottom: 1px dotted #E7E7E7;
	}

</style>


<!-- Container -->

	<div class="row">
		<div class="col-lg-12">

			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b">				
				<div class="card-body pt-4">
					<div class="kt-section kt-section--first">
						<table>
							<thead>
								<tr>
									<th su:write="Date" su:scope="admin"></th>
									<th>IP</th>
									<th su:write="Error" su:scope="admin"></th>
								</tr>
							</thead>
							<tbody>
								<?php

									if(count($parsedLog) == 0)
										echo "<tr><td colspan=\"3\">" . $this->write("No errors registered", 'admin') . "</td></tr>";
									else{

										foreach ($parsedLog as $line)
										{
											echo "<tr>".
												"<td>$line[3] $line[0]</td>" .
												"<td>$line[2]</td>" .
												"<td>$line[1]</td>" .
												 "</tr>";
										}
									}
								?>
							</tbody>
							
						</table>
						

					</div>
				</div>
				<div class="card-footer">
				</div>
			</div>


		</div>
	</div>