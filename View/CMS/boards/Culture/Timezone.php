<?php
	$this->variables['pageTitle'] = $this->write('Timezone', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);
?>


<!-- Container -->

	<div class="row" id="main">
		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">				
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Timezones" su:scope="admin"></span>
					</h3>
				</div>
				<form class="form">
					<div class="card-body pt-4">
						<ul id="list">
							<li class="list-item">
								<span><strong>$name</strong></span><br>
								<span>$label - $timezone</span>
								<div class="list-item-btn-holder">
									<div class="list-item-btn" onclick="form.loadKey('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-pen"></i></div>
									<div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>
								</div>
							</li>								
						</ul>
					</div>
				</form>
				<div class="card-footer" id="list_page">
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-new-user">				
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Edit/Create" su:scope="admin"></span>
					</h3>
				</div>
				<div class="card-body pt-4">
					<form class="kt-form" id="form">
						<div class="kt-section kt-section--first" data-bind="with:current">
							<div class="form-group">
								<label su:write="Label" su:scope="admin"></label>
								<input type="text" name="label" data-bind="value: label" class="form-control">
							</div>
							
							<div class="form-group">
								<label su:write="Name" su:scope="admin"></label>
								<input type="text" name="name" data-bind="value: name" class="form-control">
							</div>

							<div class="form-group">
								<label su:write="Date format" su:scope="admin"></label>
								<input type="text" name="dateFormat" class="form-control">
							</div>

							<div class="form-group">
								<label su:write="Time format" su:scope="admin"></label>
								<input type="text" name="timeFormat" class="form-control">
							</div>

							<div class="form-group">
								<label su:write="Date & Time format" su:scope="admin"></label>
								<input type="text" name="dateTimeFormat" class="form-control">
							</div>


							<div class="form-group">
								<label su:write="Timezone" su:scope="admin"></label>
								<select name="timezone" id="timezones" class="form-control"></select>
							</div>
						</div>
					</form>
				</div>
				<div class="card-footer">
					<button type="submit" id="submit" class="btn btn-primary" su:write="Submit" su:scope="admin"></button>
					<button class="btn btn-primary" onclick="window.form.toDefault()" su:write="Create new" su:scope="admin"></button>
				</div>				
			</div>
		</div>

	</div>

<style type="text/css">
	#list
	{
		display: flex;
		flex-wrap: wrap;
		flex-direction: column;
		list-style: none;
		padding: 0px;
	}
	.list-item{
		flex:0 0 100%;
		padding: 5px;
		margin: 5px;
		border-bottom: 1px dashed #E7E7E7;
		position: relative;
	}
	.list-item .list-item-btn-holder{
		display: flex;
		position: absolute;
		right: 0;
		top: 0;
		cursor: pointer;
		height: 100%;
		flex-direction: row;
		justify-content: center;
		align-items: center;
	}
	.list-item .list-item-btn
	{
		display: inline-block;
		text-align: center;
		padding: 5px;
		margin: 5px;
		/*border: 1px solid #E7E7E7;*/
		color: #CCC;
	}
	
	.list-item .list-item-btn:hover{
		color: #333;
		/*border: 1px solid #CCC;*/
	}
</style>

<script>

	
	timezones = ["UTC", "Africa/Abidjan", "Africa/Accra", "Africa/Addis_Ababa", "Africa/Algiers", "Africa/Asmara", "Africa/Bamako", "Africa/Bangui", "Africa/Banjul", "Africa/Bissau", "Africa/Blantyre", "Africa/Brazzaville", "Africa/Bujumbura", "Africa/Cairo", "Africa/Casablanca", "Africa/Ceuta", "Africa/Conakry", "Africa/Dakar", "Africa/Dar_es_Salaam", "Africa/Djibouti", "Africa/Douala", "Africa/El_Aaiun", "Africa/Freetown", "Africa/Gaborone", "Africa/Harare", "Africa/Johannesburg", "Africa/Juba", "Africa/Kampala", "Africa/Khartoum", "Africa/Kigali", "Africa/Kinshasa", "Africa/Lagos", "Africa/Libreville", "Africa/Lome", "Africa/Luanda", "Africa/Lubumbashi", "Africa/Lusaka", "Africa/Malabo", "Africa/Maputo", "Africa/Maseru", "Africa/Mbabane", "Africa/Mogadishu", "Africa/Monrovia", "Africa/Nairobi", "Africa/Ndjamena", "Africa/Niamey", "Africa/Nouakchott", "Africa/Ouagadougou", "Africa/Porto-Novo", "Africa/Sao_Tome", "Africa/Tripoli", "Africa/Tunis", "Africa/Windhoek", "America/Adak", "America/Anchorage", "America/Anguilla", "America/Antigua", "America/Araguaina", "America/Argentina/Buenos_Aires", "America/Argentina/Catamarca", "America/Argentina/Cordoba", "America/Argentina/Jujuy", "America/Argentina/La_Rioja", "America/Argentina/Mendoza", "America/Argentina/Rio_Gallegos", "America/Argentina/Salta", "America/Argentina/San_Juan", "America/Argentina/San_Luis", "America/Argentina/Tucuman", "America/Argentina/Ushuaia", "America/Aruba", "America/Asuncion", "America/Atikokan", "America/Bahia", "America/Bahia_Banderas", "America/Barbados", "America/Belem", "America/Belize", "America/Blanc-Sablon", "America/Boa_Vista", "America/Bogota", "America/Boise", "America/Cambridge_Bay", "America/Campo_Grande", "America/Cancun", "America/Caracas", "America/Cayenne", "America/Cayman", "America/Chicago", "America/Chihuahua", "America/Costa_Rica", "America/Creston", "America/Cuiaba", "America/Curacao", "America/Danmarkshavn", "America/Dawson", "America/Dawson_Creek", "America/Denver", "America/Detroit", "America/Dominica", "America/Edmonton", "America/Eirunepe", "America/El_Salvador", "America/Fort_Nelson", "America/Fortaleza", "America/Glace_Bay", "America/Godthab", "America/Goose_Bay", "America/Grand_Turk", "America/Grenada", "America/Guadeloupe", "America/Guatemala", "America/Guayaquil", "America/Guyana", "America/Halifax", "America/Havana", "America/Hermosillo", "America/Indiana/Indianapolis", "America/Indiana/Knox", "America/Indiana/Marengo", "America/Indiana/Petersburg", "America/Indiana/Tell_City", "America/Indiana/Vevay", "America/Indiana/Vincennes", "America/Indiana/Winamac", "America/Inuvik", "America/Iqaluit", "America/Jamaica", "America/Juneau", "America/Kentucky/Louisville", "America/Kentucky/Monticello", "America/Kralendijk", "America/La_Paz", "America/Lima", "America/Los_Angeles", "America/Lower_Princes", "America/Maceio", "America/Managua", "America/Manaus", "America/Marigot", "America/Martinique", "America/Matamoros", "America/Mazatlan", "America/Menominee", "America/Merida", "America/Metlakatla", "America/Mexico_City", "America/Miquelon", "America/Moncton", "America/Monterrey", "America/Montevideo", "America/Montserrat", "America/Nassau", "America/New_York", "America/Nipigon", "America/Nome", "America/Noronha", "America/North_Dakota/Beulah", "America/North_Dakota/Center", "America/North_Dakota/New_Salem", "America/Ojinaga", "America/Panama", "America/Pangnirtung", "America/Paramaribo", "America/Phoenix", "America/Port-au-Prince", "America/Port_of_Spain", "America/Porto_Velho", "America/Puerto_Rico", "America/Punta_Arenas", "America/Rainy_River", "America/Rankin_Inlet", "America/Recife", "America/Regina", "America/Resolute", "America/Rio_Branco", "America/Santarem", "America/Santiago", "America/Santo_Domingo", "America/Sao_Paulo", "America/Scoresbysund", "America/Sitka", "America/St_Barthelemy", "America/St_Johns", "America/St_Kitts", "America/St_Lucia", "America/St_Thomas", "America/St_Vincent", "America/Swift_Current", "America/Tegucigalpa", "America/Thule", "America/Thunder_Bay", "America/Tijuana", "America/Toronto", "America/Tortola", "America/Vancouver", "America/Whitehorse", "America/Winnipeg", "America/Yakutat", "America/Yellowknife", "Antarctica/Casey", "Antarctica/Davis", "Antarctica/DumontDUrville", "Antarctica/Macquarie", "Antarctica/Mawson", "Antarctica/McMurdo", "Antarctica/Palmer", "Antarctica/Rothera", "Antarctica/Syowa", "Antarctica/Troll", "Antarctica/Vostok", "Arctic/Longyearbyen", "Asia/Aden", "Asia/Almaty", "Asia/Amman", "Asia/Anadyr", "Asia/Aqtau", "Asia/Aqtobe", "Asia/Ashgabat", "Asia/Atyrau", "Asia/Baghdad", "Asia/Bahrain", "Asia/Baku", "Asia/Bangkok", "Asia/Barnaul", "Asia/Beirut", "Asia/Bishkek", "Asia/Brunei", "Asia/Chita", "Asia/Choibalsan", "Asia/Colombo", "Asia/Damascus", "Asia/Dhaka", "Asia/Dili", "Asia/Dubai", "Asia/Dushanbe", "Asia/Famagusta", "Asia/Gaza", "Asia/Hebron", "Asia/Ho_Chi_Minh", "Asia/Hong_Kong", "Asia/Hovd", "Asia/Irkutsk", "Asia/Jakarta", "Asia/Jayapura", "Asia/Jerusalem", "Asia/Kabul", "Asia/Kamchatka", "Asia/Karachi", "Asia/Kathmandu", "Asia/Khandyga", "Asia/Kolkata", "Asia/Krasnoyarsk", "Asia/Kuala_Lumpur", "Asia/Kuching", "Asia/Kuwait", "Asia/Macau", "Asia/Magadan", "Asia/Makassar", "Asia/Manila", "Asia/Muscat", "Asia/Nicosia", "Asia/Novokuznetsk", "Asia/Novosibirsk", "Asia/Omsk", "Asia/Oral", "Asia/Phnom_Penh", "Asia/Pontianak", "Asia/Pyongyang", "Asia/Qatar", "Asia/Qostanay", "Asia/Qyzylorda", "Asia/Riyadh", "Asia/Sakhalin", "Asia/Samarkand", "Asia/Seoul", "Asia/Shanghai", "Asia/Singapore", "Asia/Srednekolymsk", "Asia/Taipei", "Asia/Tashkent", "Asia/Tbilisi", "Asia/Tehran", "Asia/Thimphu", "Asia/Tokyo", "Asia/Tomsk", "Asia/Ulaanbaatar", "Asia/Urumqi", "Asia/Ust-Nera", "Asia/Vientiane", "Asia/Vladivostok", "Asia/Yakutsk", "Asia/Yangon", "Asia/Yekaterinburg", "Asia/Yerevan", "Atlantic/Azores", "Atlantic/Bermuda", "Atlantic/Canary", "Atlantic/Cape_Verde", "Atlantic/Faroe", "Atlantic/Madeira", "Atlantic/Reykjavik", "Atlantic/South_Georgia", "Atlantic/St_Helena", "Atlantic/Stanley", "Australia/Adelaide", "Australia/Brisbane", "Australia/Broken_Hill", "Australia/Currie", "Australia/Darwin", "Australia/Eucla", "Australia/Hobart", "Australia/Lindeman", "Australia/Lord_Howe", "Australia/Melbourne", "Australia/Perth", "Australia/Sydney", "Europe/Amsterdam", "Europe/Andorra", "Europe/Astrakhan", "Europe/Athens", "Europe/Belgrade", "Europe/Berlin", "Europe/Bratislava", "Europe/Brussels", "Europe/Bucharest", "Europe/Budapest", "Europe/Busingen", "Europe/Chisinau", "Europe/Copenhagen", "Europe/Dublin", "Europe/Gibraltar", "Europe/Guernsey", "Europe/Helsinki", "Europe/Isle_of_Man", "Europe/Istanbul", "Europe/Jersey", "Europe/Kaliningrad", "Europe/Kiev", "Europe/Kirov", "Europe/Lisbon", "Europe/Ljubljana", "Europe/London", "Europe/Luxembourg", "Europe/Madrid", "Europe/Malta", "Europe/Mariehamn", "Europe/Minsk", "Europe/Monaco", "Europe/Moscow", "Europe/Oslo", "Europe/Paris", "Europe/Podgorica", "Europe/Prague", "Europe/Riga", "Europe/Rome", "Europe/Samara", "Europe/San_Marino", "Europe/Sarajevo", "Europe/Saratov", "Europe/Simferopol", "Europe/Skopje", "Europe/Sofia", "Europe/Stockholm", "Europe/Tallinn", "Europe/Tirane", "Europe/Ulyanovsk", "Europe/Uzhgorod", "Europe/Vaduz", "Europe/Vatican", "Europe/Vienna", "Europe/Vilnius", "Europe/Volgograd", "Europe/Warsaw", "Europe/Zagreb", "Europe/Zaporozhye", "Europe/Zurich", "Indian/Antananarivo", "Indian/Chagos", "Indian/Christmas", "Indian/Cocos", "Indian/Comoro", "Indian/Kerguelen", "Indian/Mahe", "Indian/Maldives", "Indian/Mauritius", "Indian/Mayotte", "Indian/Reunion", "Pacific/Apia", "Pacific/Auckland", "Pacific/Bougainville", "Pacific/Chatham", "Pacific/Chuuk", "Pacific/Easter", "Pacific/Efate", "Pacific/Enderbury", "Pacific/Fakaofo", "Pacific/Fiji", "Pacific/Funafuti", "Pacific/Galapagos", "Pacific/Gambier", "Pacific/Guadalcanal", "Pacific/Guam", "Pacific/Honolulu", "Pacific/Kiritimati", "Pacific/Kosrae", "Pacific/Kwajalein", "Pacific/Majuro", "Pacific/Marquesas", "Pacific/Midway", "Pacific/Nauru", "Pacific/Niue", "Pacific/Norfolk", "Pacific/Noumea", "Pacific/Pago_Pago", "Pacific/Palau", "Pacific/Pitcairn", "Pacific/Pohnpei", "Pacific/Port_Moresby", "Pacific/Rarotonga", "Pacific/Saipan", "Pacific/Tahiti", "Pacific/Tarawa", "Pacific/Tongatapu", "Pacific/Wake", "Pacific/Wallis"];

	$.map(timezones, function(e,i){
		$('#timezones').append('<option value='+e+'>'+e+'</option>');
	});

	list = $('#list').dynamic(
	{
		transaction: Edit.getConfigDynamic,
		filters: {
			'config': 'culture/timezones'
		},
		onData: function(d)
		{
			let list =  [];
			ko.utils.objectMap(d, function(i,e)
			{
				list.push(Object.assign(i,{key:e}));
			})
			return list;
		},
		onLoading: function(a)
		{
			if(a) blockLoading('#list')
			else unblockLoading('#list');
		},
		onPlot: function ()
		{
			$('[data-toggle="kt-tooltip"]').tooltip();
		}
	});

	form = $('#form').storm({
		transactionSelect: Edit.getConfigStorm,
		transaction: Edit.setConfigStorm,
		key: "label",
		filters:{
			'config': 'culture/timezones',
			'keySource': 'label'
			//'processor': 'Culture/CMS::Edit'
		},
		submitButton: $('#submit'),
		onLoading: function(a)
		{
			if(a) blockLoading()
			else unblockLoading();
		},
		onSubmitted: function()
		{
			list.reload();
			form.toDefault();
		},
		onDeleted: function()
		{
			list.reload();
		},
		onData: function()
		{
			$('[name=label]').prop('disabled', true);
		},
		onReset: function()
		{
			$('[name=label]').prop('disabled', false);
		}
	});

	form.deleteConfirm = function(key)
	{
		if(confirm('<su: write="Are you sure you want to delete?" scope="admin">'))
			this.delete(key);
	}

</script>