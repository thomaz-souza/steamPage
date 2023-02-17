<?php
	$this->variables['pageTitle'] = $this->write('Language', 'admin');
	$this->using('CMSController\\Edit');

	Dynamic\Dynamic::addTag($this);
	Storm\Storm::addTag($this);

	$this->addTagHeadJS("https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js", false);
	$this->addTagHeadCSS("https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css", false);

?>

<!-- Container -->

	<div class="row" id="main">
		<div class="col-lg-6">
			<!-- Portlet -->
			<div class="card card-custom card-stretch gutter-b" id="portlet-user-list">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span su:write="Languages" su:scope="admin"></span>
					</h3>
				</div>
				<form class="form">
					<div class="card-body pt-4">
						<ul id="list">
							<li class="list-item">
								<span><strong>$name</strong></span><br>
								<span>$label</span>
								<div class="list-item-btn-holder">
									<div class="list-item-btn" onclick="window.location.href='../Culture/EditLanguage?lang=$key'"><i class="fas fa-feather-alt" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit translation' scope='admin'>"></i></div>
									<div class="list-item-btn" onclick="form.loadKey('$key')" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Edit' scope='admin'>"><i class="fas fa-pen"></i></div>
									<div class="list-item-btn" onclick="form.deleteConfirm('$key');" data-toggle="kt-tooltip" data-placement="bottom" data-original-title="<su: write='Delete' scope='admin'>"><i class="fas fa-trash"></i></div>
								</div>
							</li>								
						</ul>
					</div>
				</form>
				<div class="kt-portlet__foot">
					<div class="kt-form__actions" id="list_page">
					</div>
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
					<form class="form" id="form">
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
								<label su:write="Charset" su:scope="admin"></label>
								<input type="text" name="charset" data-bind="value: charset" class="form-control" value="UTF-8">
							</div>

							<div class="form-group">
								<label su:write="Writing direction" su:scope="admin"></label>
								<select data-bind="value: direction" name="direction" class="form-control">
									<option value="ltr" su:write="Left-to-right (LTR)" su:scope="admin" selected="selected"></option>
									<option value="rtl" su:write="Right-to-left (RTL)" su:scope="admin"></option>
								</select>
							</div>

							<div class="form-group">
								<label su:write="Automatic detection for" su:scope="admin"></label>
								<select name="suitableFor[]" id="suitableFor" style="height: 200px;" class="form-control select2-tags" multiple="multiple"></select>
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

	

	setSelect2 = function()
	{
		

		$('.select2-tags').select2({tags: true, data:langcode});
	}


	langcode = [{id:"af", text:"[af] Afrikaans"},{id:"af-ZA", text:"[af-ZA] Afrikaans (South Africa)"},{id:"ar", text:"[ar] Arabic"},{id:"ar-AE", text:"[ar-AE] Arabic (U.A.E.)"},{id:"ar-BH", text:"[ar-BH] Arabic (Bahrain)"},{id:"ar-DZ", text:"[ar-DZ] Arabic (Algeria)"},{id:"ar-EG", text:"[ar-EG] Arabic (Egypt)"},{id:"ar-IQ", text:"[ar-IQ] Arabic (Iraq)"},{id:"ar-JO", text:"[ar-JO] Arabic (Jordan)"},{id:"ar-KW", text:"[ar-KW] Arabic (Kuwait)"},{id:"ar-LB", text:"[ar-LB] Arabic (Lebanon)"},{id:"ar-LY", text:"[ar-LY] Arabic (Libya)"},{id:"ar-MA", text:"[ar-MA] Arabic (Morocco)"},{id:"ar-OM", text:"[ar-OM] Arabic (Oman)"},{id:"ar-QA", text:"[ar-QA] Arabic (Qatar)"},{id:"ar-SA", text:"[ar-SA] Arabic (Saudi Arabia)"},{id:"ar-SY", text:"[ar-SY] Arabic (Syria)"},{id:"ar-TN", text:"[ar-TN] Arabic (Tunisia)"},{id:"ar-YE", text:"[ar-YE] Arabic (Yemen)"},{id:"az", text:"[az] Azeri"},{id:"az-Cyrl-AZ", text:"[az-Cyrl-AZ] Azeri (Cyrillic, Azerbaijan)"},{id:"az-Latn-AZ", text:"[az-Latn-AZ] Azeri (Latin, Azerbaijan)"},{id:"be", text:"[be] Belarusian"},{id:"be-BY", text:"[be-BY] Belarusian (Belarus)"},{id:"bg", text:"[bg] Bulgarian"},{id:"bg-BG", text:"[bg-BG] Bulgarian (Bulgaria)"},{id:"bs-Latn-BA", text:"[bs-Latn-BA] Bosnian (Bosnia and Herzegovina)"},{id:"ca", text:"[ca] Catalan"},{id:"ca-ES", text:"[ca-ES] Catalan (Catalan)"},{id:"cs", text:"[cs] Czech"},{id:"cs-CZ", text:"[cs-CZ] Czech (Czech Republic)"},{id:"cy-GB", text:"[cy-GB] Welsh (United Kingdom)"},{id:"da", text:"[da] Danish"},{id:"da-DK", text:"[da-DK] Danish (Denmark)"},{id:"de", text:"[de] German"},{id:"de-AT", text:"[de-AT] German (Austria)"},{id:"de-DE", text:"[de-DE] German (Germany)"},{id:"de-CH", text:"[de-CH] German (Switzerland)"},{id:"de-LI", text:"[de-LI] German (Liechtenstein)"},{id:"de-LU", text:"[de-LU] German (Luxembourg)"},{id:"dv", text:"[dv] Divehi"},{id:"dv-MV", text:"[dv-MV] Divehi (Maldives)"},{id:"el", text:"[el] Greek"},{id:"el-GR", text:"[el-GR] Greek (Greece)"},{id:"en", text:"[en] English"},{id:"en-029", text:"[en-029] English (Caribbean)"},{id:"en-AU", text:"[en-AU] English (Australia)"},{id:"en-BZ", text:"[en-BZ] English (Belize)"},{id:"en-CA", text:"[en-CA] English (Canada)"},{id:"en-GB", text:"[en-GB] English (United Kingdom)"},{id:"en-IE", text:"[en-IE] English (Ireland)"},{id:"en-JM", text:"[en-JM] English (Jamaica)"},{id:"en-NZ", text:"[en-NZ] English (New Zealand)"},{id:"en-PH", text:"[en-PH] English (Republic of the Philippines)"},{id:"en-TT", text:"[en-TT] English (Trinidad and Tobago)"},{id:"en-US", text:"[en-US] English (United States)"},{id:"en-ZA", text:"[en-ZA] English (South Africa)"},{id:"en-ZW", text:"[en-ZW] English (Zimbabwe)"},{id:"es", text:"[es] Spanish"},{id:"es-AR", text:"[es-AR] Spanish (Argentina)"},{id:"es-BO", text:"[es-BO] Spanish (Bolivia)"},{id:"es-CL", text:"[es-CL] Spanish (Chile)"},{id:"es-CO", text:"[es-CO] Spanish (Colombia)"},{id:"es-CR", text:"[es-CR] Spanish (Costa Rica)"},{id:"es-DO", text:"[es-DO] Spanish (Dominican Republic)"},{id:"es-EC", text:"[es-EC] Spanish (Ecuador)"},{id:"es-ES", text:"[es-ES] Spanish (Spain)"},{id:"es-GT", text:"[es-GT] Spanish (Guatemala)"},{id:"es-HN", text:"[es-HN] Spanish (Honduras)"},{id:"es-MX", text:"[es-MX] Spanish (Mexico)"},{id:"es-NI", text:"[es-NI] Spanish (Nicaragua)"},{id:"es-PA", text:"[es-PA] Spanish (Panama)"},{id:"es-PE", text:"[es-PE] Spanish (Peru)"},{id:"es-PR", text:"[es-PR] Spanish (Puerto Rico)"},{id:"es-PY", text:"[es-PY] Spanish (Paraguay)"},{id:"es-SV", text:"[es-SV] Spanish (El Salvador)"},{id:"es-UY", text:"[es-UY] Spanish (Uruguay)"},{id:"es-VE", text:"[es-VE] Spanish (Venezuela)"},{id:"et", text:"[et] Estonian"},{id:"et-EE", text:"[et-EE] Estonian (Estonia)"},{id:"eu", text:"[eu] Basque"},{id:"eu-ES", text:"[eu-ES] Basque (Basque)"},{id:"fa", text:"[fa] Persian"},{id:"fa-IR", text:"[fa-IR] Persian (Iran)"},{id:"fi", text:"[fi] Finnish"},{id:"fi-FI", text:"[fi-FI] Finnish (Finland)"},{id:"fo", text:"[fo] Faroese"},{id:"fo-FO", text:"[fo-FO] Faroese (Faroe Islands)"},{id:"fr", text:"[fr] French"},{id:"fr-BE", text:"[fr-BE] French (Belgium)"},{id:"fr-CA", text:"[fr-CA] French (Canada)"},{id:"fr-FR", text:"[fr-FR] French (France)"},{id:"fr-CH", text:"[fr-CH] French (Switzerland)"},{id:"fr-MC", text:"[fr-MC] French (Principality of Monaco)"},{id:"fr-LU", text:"[fr-LU] French (Luxembourg)"},{id:"gl", text:"[gl] Galician"},{id:"gl-ES", text:"[gl-ES] Galician (Galician)"},{id:"gu", text:"[gu] Gujarati"},{id:"gu-IN", text:"[gu-IN] Gujarati (India)"},{id:"he", text:"[he] Hebrew"},{id:"he-IL", text:"[he-IL] Hebrew (Israel)"},{id:"hi", text:"[hi] Hindi"},{id:"hi-IN", text:"[hi-IN] Hindi (India)"},{id:"hr", text:"[hr] Croatian"},{id:"hr-BA", text:"[hr-BA] Croatian (Bosnia and Herzegovina)"},{id:"hr-HR", text:"[hr-HR] Croatian (Croatia)"},{id:"hu", text:"[hu] Hungarian"},{id:"hu-HU", text:"[hu-HU] Hungarian (Hungary)"},{id:"hy", text:"[hy] Armenian"},{id:"hy-AM", text:"[hy-AM] Armenian (Armenia)"},{id:"id", text:"[id] Indonesian"},{id:"id-ID", text:"[id-ID] Indonesian (Indonesia)"},{id:"is", text:"[is] Icelandic"},{id:"is-IS", text:"[is-IS] Icelandic (Iceland)"},{id:"it", text:"[it] Italian"},{id:"it-CH", text:"[it-CH] Italian (Switzerland)"},{id:"it-IT", text:"[it-IT] Italian (Italy)"},{id:"ja", text:"[ja] Japanese"},{id:"ja-JP", text:"[ja-JP] Japanese (Japan)"},{id:"ka", text:"[ka] Georgian"},{id:"ka-GE", text:"[ka-GE] Georgian (Georgia)"},{id:"kk", text:"[kk] Kazakh"},{id:"kk-KZ", text:"[kk-KZ] Kazakh (Kazakhstan)"},{id:"kn", text:"[kn] Kannada"},{id:"kn-IN", text:"[kn-IN] Kannada (India)"},{id:"ko", text:"[ko] Korean"},{id:"kok", text:"[kok] Konkani"},{id:"kok-IN", text:"[kok-IN] Konkani (India)"},{id:"ko-KR", text:"[ko-KR] Korean (Korea)"},{id:"ky", text:"[ky] Kyrgyz"},{id:"ky-KG", text:"[ky-KG] Kyrgyz (Kyrgyzstan)"},{id:"lt", text:"[lt] Lithuanian"},{id:"lt-LT", text:"[lt-LT] Lithuanian (Lithuania)"},{id:"lv", text:"[lv] Latvian"},{id:"lv-LV", text:"[lv-LV] Latvian (Latvia)"},{id:"mi-NZ", text:"[mi-NZ] Maori (New Zealand)"},{id:"mk", text:"[mk] Macedonian"},{id:"mk-MK", text:"[mk-MK] Macedonian (Former Yugoslav Republic of Macedonia)"},{id:"mn", text:"[mn] Mongolian"},{id:"mn-MN", text:"[mn-MN] Mongolian (Cyrillic, Mongolia)"},{id:"mr", text:"[mr] Marathi"},{id:"mr-IN", text:"[mr-IN] Marathi (India)"},{id:"ms", text:"[ms] Malay"},{id:"ms-BN", text:"[ms-BN] Malay (Brunei Darussalam)"},{id:"ms-MY", text:"[ms-MY] Malay (Malaysia)"},{id:"mt-MT", text:"[mt-MT] Maltese (Malta)"},{id:"nb-NO", text:"[nb-NO] Norwegian, Bokmal (Norway)"},{id:"nl", text:"[nl] Dutch"},{id:"nl-BE", text:"[nl-BE] Dutch (Belgium)"},{id:"nl-NL", text:"[nl-NL] Dutch (Netherlands)"},{id:"nn-NO", text:"[nn-NO] Norwegian, Nynorsk (Norway)"},{id:"no", text:"[no] Norwegian"},{id:"ns-ZA", text:"[ns-ZA] Northern Sotho (South Africa)"},{id:"pa", text:"[pa] Punjabi"},{id:"pa-IN", text:"[pa-IN] Punjabi (India)"},{id:"pl", text:"[pl] Polish"},{id:"pl-PL", text:"[pl-PL] Polish (Poland)"},{id:"pt", text:"[pt] Portuguese"},{id:"pt-BR", text:"[pt-BR] Portuguese (Brazil)"},{id:"pt-PT", text:"[pt-PT] Portuguese (Portugal)"},{id:"quz-BO", text:"[quz-BO] Quechua (Bolivia)"},{id:"quz-EC", text:"[quz-EC] Quechua (Ecuador)"},{id:"quz-PE", text:"[quz-PE] Quechua (Peru)"},{id:"ro", text:"[ro] Romanian"},{id:"ro-RO", text:"[ro-RO] Romanian (Romania)"},{id:"ru", text:"[ru] Russian"},{id:"ru-RU", text:"[ru-RU] Russian (Russia)"},{id:"sa", text:"[sa] Sanskrit"},{id:"sa-IN", text:"[sa-IN] Sanskrit (India)"},{id:"se-FI", text:"[se-FI] Sami (Northern) (Finland)"},{id:"se-NO", text:"[se-NO] Sami (Northern) (Norway)"},{id:"se-SE", text:"[se-SE] Sami (Northern) (Sweden)"},{id:"sk", text:"[sk] Slovak"},{id:"sk-SK", text:"[sk-SK] Slovak (Slovakia)"},{id:"sl", text:"[sl] Slovenian"},{id:"sl-SI", text:"[sl-SI] Slovenian (Slovenia)"},{id:"sma-NO", text:"[sma-NO] Sami (Southern) (Norway)"},{id:"sma-SE", text:"[sma-SE] Sami (Southern) (Sweden)"},{id:"smj-NO", text:"[smj-NO] Sami (Lule) (Norway)"},{id:"smj-SE", text:"[smj-SE] Sami (Lule) (Sweden)"},{id:"smn-FI", text:"[smn-FI] Sami (Inari) (Finland)"},{id:"sms-FI", text:"[sms-FI] Sami (Skolt) (Finland)"},{id:"sq", text:"[sq] Albanian"},{id:"sq-AL", text:"[sq-AL] Albanian (Albania)"},{id:"sr", text:"[sr] Serbian"},{id:"sr-Cyrl-BA", text:"[sr-Cyrl-BA] Serbian (Cyrillic) (Bosnia and Herzegovina)"},{id:"sr-Cyrl-CS", text:"[sr-Cyrl-CS] Serbian (Cyrillic, Serbia)"},{id:"sr-Latn-BA", text:"[sr-Latn-BA] Serbian (Latin) (Bosnia and Herzegovina)"},{id:"sr-Latn-CS", text:"[sr-Latn-CS] Serbian (Latin, Serbia)"},{id:"sv", text:"[sv] Swedish"},{id:"sv-FI", text:"[sv-FI] Swedish (Finland)"},{id:"sv-SE", text:"[sv-SE] Swedish (Sweden)"},{id:"sw", text:"[sw] Kiswahili"},{id:"sw-KE", text:"[sw-KE] Kiswahili (Kenya)"},{id:"syr", text:"[syr] Syriac"},{id:"syr-SY", text:"[syr-SY] Syriac (Syria)"},{id:"ta", text:"[ta] Tamil"},{id:"ta-IN", text:"[ta-IN] Tamil (India)"},{id:"te", text:"[te] Telugu"},{id:"te-IN", text:"[te-IN] Telugu (India)"},{id:"th", text:"[th] Thai"},{id:"th-TH", text:"[th-TH] Thai (Thailand)"},{id:"tn-ZA", text:"[tn-ZA] Tswana (South Africa)"},{id:"tr", text:"[tr] Turkish"},{id:"tr-TR", text:"[tr-TR] Turkish (Turkey)"},{id:"tt", text:"[tt] Tatar"},{id:"tt-RU", text:"[tt-RU] Tatar (Russia)"},{id:"uk", text:"[uk] Ukrainian"},{id:"uk-UA", text:"[uk-UA] Ukrainian (Ukraine)"},{id:"ur", text:"[ur] Urdu"},{id:"ur-PK", text:"[ur-PK] Urdu (Islamic Republic of Pakistan)"},{id:"uz", text:"[uz] Uzbek"},{id:"uz-Cyrl-UZ", text:"[uz-Cyrl-UZ] Uzbek (Cyrillic, Uzbekistan)"},{id:"uz-Latn-UZ", text:"[uz-Latn-UZ] Uzbek (Latin, Uzbekistan)"},{id:"vi", text:"[vi] Vietnamese"},{id:"vi-VN", text:"[vi-VN] Vietnamese (Vietnam)"},{id:"xh-ZA", text:"[xh-ZA] Xhosa (South Africa)"},{id:"zh-CN", text:"[zh-CN] Chinese (People's Republic of China)"},{id:"zh-HK", text:"[zh-HK] Chinese (Hong Kong S.A.R.)"},{id:"zh-CHS", text:"[zh-CHS] Chinese (Simplified)"},{id:"zh-CHT", text:"[zh-CHT] Chinese (Traditional)"},{id:"zh-MO", text:"[zh-MO] Chinese (Macao S.A.R.)"},{id:"zh-SG", text:"[zh-SG] Chinese (Singapore)"},{id:"zh-TW", text:"[zh-TW] Chinese (Taiwan)"},{id:"zu-ZA", text:"[zu-ZA] Zulu (South Africa)"}];

	$.map(langcode, function(e,i){
		$('#suitableFor').append('<option value='+e.id+'>'+e.text+'</option>');
	});

	list = $('#list').dynamic(
	{
		transaction: Edit.getConfigDynamic,
		filters: {
			'config': 'culture/languages'
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
			'config': 'culture/languages',
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