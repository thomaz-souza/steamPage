<?php
	$this->variables['pageTitle'] = $this->write("Mail log", 'admin');

	$mail = new Mail\CMS();
	$log = $mail->getLog();

	$this->variables['subheader'] = $this->write("%s failed message(s)", 'admin', count($log));
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

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid" id="main">
	<div class="row">
		<div class="col-lg-12">

			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-body pt-4">
					<table>
						<thead>
							<tr>
								<th su:write="Date" su:scope="admin"></th>
								<th su:write="Sender" su:scope="admin"></th>
								<th su:write="Subject" su:scope="admin"></th>
								<th></th>
							</tr>
						</thead>
						<tbody data-bind="foreach: log">
							<tr>
								<td data-bind="text: date"></td>
								<td data-bind="text: sender"></td>
								<td data-bind="text: subject"></td>
								<td><button class="btn btn-secondary" data-bind="click: open"><i class="fas fa-eye"></i></button></td>
							</tr>
							<tr data-bind="visible: opened">
								<td colspan="4">
									<div class="row">
										<div class="col-lg-3">
											<label><strong su:write="Language" su:scope="admin"></strong></label>
											<p data-bind="text: language"></p>
											<label><strong su:write="Timezone" su:scope="admin"></strong></label>
											<p data-bind="text: timezone"></p>
										</div>
										<div class="col-lg-4">
											<label><strong su:write="Recipients" su:scope="admin"></strong></label>
											<ul data-bind="foreach: {data: recipients, as: 'address'}">
												<li data-bind="text: address"></li>
											</ul>
										</div>
										<div class="col-lg-5" style="word-break: break-all;">
											<label><strong su:write="Debug" su:scope="admin"></strong></label>
											<p data-bind="visible: !debugAvailable()"><su: write="Unavailable" scope="admin"/></p>
											<ol data-bind="foreach: {data: debug, as: 'line'}, visible: debugAvailable()">
												<li data-bind="html: line"></li>
											</ol>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
						
					</table>
				</div>
			</div>


		</div>
	</div>
</div>
<script type="text/javascript">
	
	Line = function (i)
	{
		let self = this;

		self.date = ko.observable(i.d);
		self.timezone = ko.observable(i.z);
		self.language = ko.observable(i.l);
		self.subject = ko.observable(i.s);
		self.debug = ko.observableArray(i.o);
		self.debugAvailable = ko.observable(i.o.length > 0);
		self.sender = ko.observable(i.f);
		self.recipients = ko.observableArray(i.n);

		self.opened = ko.observable(false);
		self.open = function()
		{
			self.opened(true);
		}
	}

	LogModel = function ()
	{
		let self = this;

		self.log = ko.observableArray();

		self.setLog = function (log)
		{
			self.log(ko.utils.arrayMap(log, function(i){
				return new Line(i);
			}));
		}

	}

	var log = new LogModel();
	log.setLog(<?= json_encode($log); ?>);
	ko.applyBindings(log, document.getElementById('main'));	

</script>