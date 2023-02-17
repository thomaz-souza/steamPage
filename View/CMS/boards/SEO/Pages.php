<?php
	$this->variables['pageTitle'] = $this->write('Pages', 'seo');
	$this->using('CMSController\\Edit');

	$this->addAsset('vue');
	$this->addAsset('tools');

?>

	<div class="row" id="main">

		<div class="col-lg-6">
			<div class="card card-custom card-stretch gutter-b">
				<div class="card-header align-items-center border-0 mt-4">
					<h3 class="card-title align-items-start flex-column">
						<span><su: scope="seo" write="Pages"></span>
					</h3>
				</div>
				<div class="card-body pt-4" data-bind="foreach: pages">
					<div class="d-flex align-items-center my-8">
						<!--begin::Symbol-->
			            
			            <!--end::Symbol-->					        
				        <div class="d-flex flex-column flex-grow-1 font-weight-bold">
			                <span class="font-size-lg" data-bind="text: id"></span>
			                <div data-bind="foreach: seo">
			                	<!-- ko if: getScore() == 0 -->
				                <i class="fas fa-circle icon-sm text-danger"></i>
				                <!-- /ko -->
				                <!-- ko if: getScore() == 1 || getScore() == 2 -->
				                <i class="fas fa-circle icon-sm text-warning"></i>
				                <!-- /ko -->
				                <!-- ko if: getScore() > 2 -->
				                <i class="fas fa-circle icon-sm text-success"></i>
				                <!-- /ko -->
				                <span class="font-size-sm text-muted font-weight-normal" data-bind="text: languageName()"></span>
			                </div>
			            </div>
			            <div class="dropdown dropdown-inline ml-2">
			             	<button class="btn btn-hover-light-primary btn-sm btn-icon" data-bind="click: $root.edit">
			                    <i class="fas fa-pen"></i>
			                </button>
			                <a class="btn btn-hover-light-primary btn-sm btn-icon" data-bind="attr:{ href: getURL() }" target="_blank">
			                    <i class="fas fa-link"></i>
			                </a>
			            </div>
			        </div>
				</div>
			</div>
		</div>


		<!-- Modal -->
	    <div class="modal fade" id="editSEOPageModal" tabindex="-1" role="dialog" aria-hidden="true">
	        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" id="editSEOPageModalDialog">
	        <div class="modal-content">
	          <div class="modal-header" data-bind="with: editing">
	          	
	            <h5 class="modal-title" su:write="Edit page SEO" su:scope="seo"></h5>
	            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	              <span aria-hidden="true"><i class="fas fa-times"></i></span>
	            </button>
	          </div>
	          <div class="modal-body" data-bind="with: editing">

	            <div class="row">

	            	<div class="col-lg-12 g-s-back mb-10 ph-15">

		            	<div class="g-s-holder">
		            		<div class="g-s-link" data-bind="text: getSEOURL()"></div>
		            		<a class="g-s-title" data-bind="text: editingSeo().metaTitleSearch, attr:{ href: getURL() }" target="_blank"></a>
		            		<div class="g-s-description" data-bind="text: editingSeo().metaDescriptionSearch"></div>
		            	</div>

		            </div>

	            	<div class="col-lg-12 mb-5 ph-2">
	            		<!-- ko foreach: seo -->
	            		<label class="btn btn-sm" 
	            			data-bind="click: $parent.toggleSeo, class: ($parent.editingSeo().language() == $data.language()) ? 'btn-light-primary btn-hover-light-primary' : 'btn-clean btn-hover-secundary' ">

	            			<!-- ko if: getScore() == 0 -->
			                <i class="fas fa-circle icon-sm text-danger"></i>
			                <!-- /ko -->
			                <!-- ko if: getScore() == 1 || getScore() == 2 -->
			                <i class="fas fa-circle icon-sm text-warning"></i>
			                <!-- /ko -->
			                <!-- ko if: getScore() > 2 -->
			                <i class="fas fa-circle icon-sm text-success"></i>
			                <!-- /ko -->
			                <span data-bind="text: languageName()"></span>

	            			</label>
	            		<!-- /ko -->
	            	</div>

		            <div class="col-lg-12" data-bind="with: editingSeo">	
			            <div class="col-lg-12 form-group">
		                	<label su:write="Page title" su:scope="seo"></label>
		                    <input type="text" class="form-control" data-bind="value: pageTitle, valueUpdate: 'input'">
		                	<span class="text-muted font-size-sm mt-2" su:write="If the page title is being created dynamically by the developer, your changes here can be changed or not appear as desired." su:scope="seo"></span>
		                </div>
		            </div>

		            <div class="col-lg-6" data-bind="with: editingSeo">		                

		                 <div class="col-lg-12 form-group">
		                	<label su:write="Meta title" su:scope="seo"></label>
		                    <input type="text" class="form-control" data-bind="value: metaTitle, valueUpdate: 'input'">
		                </div>

		                 <div class="col-lg-12 form-group">
		                	<label su:write="Meta keywords" su:scope="seo"></label>
		                    <input type="text" class="form-control" data-bind="value: metaKeywords, valueUpdate: 'input'">
		                </div>

		                 <div class="col-lg-12 form-group">
		                	<label su:write="Meta description" su:scope="seo"></label>
		                    <textarea class="form-control" data-bind="value: metaDescription, valueUpdate: 'input'"></textarea>
		                </div>

		            </div>

		            <div class="col-lg-6" data-bind="with: editingSeo">

		                <div class="col-lg-12 form-group">
		                	<label su:write="Open graph image (OG)" su:scope="seo"></label>

		                	<div class="text-center my-2 mx-auto">
		                		<!-- ko if: ogImage -->
		                		<img style="max-height: 150px; max-width: 150px;width: 100%; max-height: 100%;" data-bind="attr:{src: '<?= $this->getURL() ?>/' + ogImage()}">
		                		<!-- /ko -->
		                	</div>

		                	<div class="text-center my-2 mx-auto">		                		
		                		<!-- ko if: ogImageFile -->
		                		<button class="mr-1 btn btn-icon btn-sm btn-success" disabled="disabled">
		                			 <i class="fas fa-upload"></i>
		                		</button>
		                		<!-- /ko -->
		                		<!-- ko ifnot: ogImageFile -->
		                		<div class="mr-1 btn btn-icon btn-sm btn-hover-primary" 
		                			 data-bind="click: triggerUpload">
		                			 <i class="fas fa-upload"></i>
		                		</div>
		                		<!-- /ko -->
		                		<!-- ko if: ogImage() || ogImageFile() -->
		                		<div class="mr-1 btn btn-icon btn-sm btn-hover-danger" data-bind="click: removeImage"><i class="fas fa-trash"></i></div>
		                		<!-- /ko -->
		                	</div>

		                    <input type="file" class="form-control" accept="<?= implode(",", MimeLibrary::WebImage()) ?>" data-bind="event: { change: function() { ogImageFile($element.files[0]) } }" style="display: none" id="file_og">
		                </div>

		                 <div class="col-lg-12 form-group">
		                	<label su:write="Open graph title (OG)" su:scope="seo"></label>
		                    <input type="text" class="form-control" data-bind="value: ogTitle, valueUpdate: 'input'">
		                </div>

		                 <div class="col-lg-12 form-group">
		                	<label su:write="Open graph description (OG)" su:scope="seo"></label>
		                    <textarea class="form-control" data-bind="value: ogDescription, valueUpdate: 'input'"></textarea>
		                </div>

		            </div>

		            <div class="col-lg-12 row mt-2 border-top pt-4" data-bind="with: sitemap">

		            	<div class="col-lg-4 form-group">
		                	
		                	<label su:write="Enable sitemap" su:scope="seo"></label><br>
	                    	<span class="switch switch-success switch-icon">
							<label>
								<input type="checkbox" data-bind="checked: enabled">
								<span></span>
							</label>
						</span>
		                </div>

		                <div class="col-lg-4 form-group">
		                	<label su:write="Priority" su:scope="seo"></label>
		                    <input type="text" class="form-control" data-bind="value: priority, valueUpdate: 'input',
		                    	enable: enabled">
		                </div>

		                <div class="col-lg-4 form-group">
		                	<label su:write="Change frequency" su:scope="seo"></label>
		                    <select class="form-control" data-bind="options: changefreqList, 
		                    	value: changefreq,
		                    	optionsCaption: '<su: scope='seo' write='Choose frequency'>',
		                    	optionsValue: 'value',
		                    	optionsText: 'title',
		                    	enable: enabled">
		                    </select>
		                </div>

		            </div>

		        </div>
	           
	          </div>
	          <div class="modal-footer">
	            <button type="button" class="btn btn-secondary" data-dismiss="modal"><su: write="Cancel" scope="admin"></button>
	            <button type="button" class="btn btn-success" data-bind="click: $root.save"><su: write="Save" scope="admin"></button>
	          </div>
	        </div>
	      </div>
	    </div>
	</div>

	<style type="text/css">
		.g-s-back{
			background-color: #E7E7E7;
		}
		.g-s-holder{
			background-color: #FFF;
			width: 96%;
			margin: 10px auto;
			padding: 10px 15px;
			display: flex;
			flex-direction: column;
			max-width: 600px;
		}
		.g-s-title{
			font-size: 20px;
    		line-height: 1.3;
    		padding-top: 4px;
    		margin-bottom: 3px;
    		color: #1a0dab;
    		font-family: arial,sans-serif;
		}
		.g-s-title:hover{
			color: #1a0dab;
			text-decoration: underline !important;
		}
		.g-s-link{
			color: #5f6368;
			font-size: 14px;
		    line-height: 1.3;
    		font-family: arial,sans-serif;		    
		}
		.g-s-description{
    		font-family: arial,sans-serif;
    		color: #4d5156;
			font-size: 14px;
		    line-height: 1.3;
		}
	</style>

	<script type="text/javascript">

		const LanguagesLibrary = <?= json_encode($this->listLanguages()) ?>;
		
		var SitemapModel = function (i)
		{
			let self = this;

			self.enabled = ko.observable(i === false ? false : true);

			i = i ? i : {};

			self.priority = ko.observable(i.priority ? i.priority : '');
            self.changefreq = ko.observable(i.changefreq ? i.changefreq : '');

            self.changefreqList = [
            	{'value': 'never', 'title' : '<su: scope="seo" write="Never">'},
            	{'value': 'yearly', 'title' : '<su: scope="seo" write="Yearly">'},
            	{'value': 'monthly', 'title' : '<su: scope="seo" write="Monthly">'},
            	{'value': 'weekly', 'title' : '<su: scope="seo" write="Weekly">'},
            	{'value': 'daily', 'title' : '<su: scope="seo" write="Daily">'},
            	{'value': 'hourly', 'title' : '<su: scope="seo" write="Hourly">'},
            	{'value': 'always', 'title' : '<su: scope="seo" write="Always">'}
            ];
            
		}

		var DataModel = function (i,l)
		{
			let self = this;
			i = i ? i : {};
			l = l ? l : {};

			self.pageTitle = ko.observable(i.pageTitle ? i.pageTitle : '');
			self.metaTitle = ko.observable(i.metaTitle ? i.metaTitle : '');
			self.metaDescription = ko.observable(i.metaDescription ? i.metaDescription : '');
			self.metaKeywords = ko.observable(i.metaKeywords ? i.metaKeywords : '');

			self.ogTitle = ko.observable(i.ogTitle ? i.ogTitle : '');
			self.ogImage = ko.observable(i.ogImage ? i.ogImage : '');
			self.ogImageFile = ko.observable();
			self.ogDescription = ko.observable(i.ogDescription ? i.ogDescription : '');

			self.language = ko.observable(l.label ? l.label : (i.language ? i.language : ''));
			self.languageName = ko.observable(l.name ? l.name : '');

			self.removeImage = function(){
				self.ogImage('');
				self.ogImageFile(null);
			}

			self.triggerUpload = function(){
				$('#file_og').click();
			}

			trimSearch = function(maxLength, content, text)
			{
				if(content == "") return text;

				let newcontent = content.substr(0, maxLength);

				if(content.length > maxLength)
					newcontent += "...";

				return newcontent;
			}

			self.metaTitleSearch = ko.pureComputed(function(){

				let example = "<?= $this->write("Here goes the page's title", "seo"); ?>",
				content = self.metaTitle() == "" ? self.pageTitle() : self.metaTitle();

				return trimSearch(60, content, example);
			});

			self.metaDescriptionSearch = ko.pureComputed(function(){

				let example = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer quis est in dui dignissim dictum sed in ex.";

				return trimSearch(150, self.metaDescription(), example);
			});

			self.getScore = ko.pureComputed(function()
			{
				let score = 0;

				if(self.metaDescription() != null && self.metaDescription().length > 10)
					score++;
				
				if(self.metaTitle() != null && self.metaTitle().length > 10)
					score++;
				
				if(self.metaKeywords() != null && self.metaKeywords().length > 10)
					score++;

				return score;
			});

			//self.all = ko.observable(i.all ? i.all : i);
		}

		var PageModel = function (i)
		{
			let self = this;
			i = i ? i : {};

			self.id = ko.observable(i.id ? i.id : null);
			self.static = ko.observable(i.static ? i.static : '');
			self.sitemap = ko.observable(new SitemapModel(i.sitemap || i.sitemap === false ? i.sitemap : {}));

			self.seo = ko.observableArray();
			self.editingSeo = ko.observable();		

			self.toggleSeo = function(seo)
			{
				self.editingSeo(seo);
			}

			for(label in LanguagesLibrary)
			{
				let language = LanguagesLibrary[label],
					data = {};

				if(i.seo)
				{
					for(let seoLang in i.seo)
					{
						if(seoLang == label)
						{
							data = i.seo[seoLang];
							break;
						}
					}
				}
				self.seo.push(new DataModel(data, language));
			}

			self.editingSeo(self.seo()[0]);


			self.getURL = function(){
				return '<?= $this->getUrl() ?>/' + self.static();
			}

			self.getSEOURL = function(){
				let url = '<?= $this->getUrl() ?>'.replace(/http[s]{0,1}\:\/\//gi,"");
				if(self.static() != '')
					url += " › " + self.static().replace(/\/$/gi, "").replace(/\//gi, " › ");
				return url;
			}
		}

		var SEOViewModel = function ()
		{
			let self = this;

			self.pages = ko.observableArray();

			self.load = function ()
			{
				blockLoading();

				Edit.call({_call: "SEO/PageSEO::getPages"}, function(response){

					unblockLoading();
					if(response.status != 200)
						return toastr.error(response.error.message);

					self.pages(ko.utils.arrayMap(response.data, function(i){ return new PageModel(i) }));
				})
			}

			self.editing = ko.observable();

			self.edit = function (obj)
			{
				self.editing(obj);
				$('#editSEOPageModal').modal('show');
			}

			self.save = function ()
			{
				blockLoading('#editSEOPageModalDialog');
				let block = ko.toJS(self.editing());

				let params = {
					_call: "SEO/PageSEO::savePage",
					content: {
						id: self.editing().id(),
						seo: []
					}
				}

				params.content.sitemap = !block.sitemap.enabled ? false : {priority: block.sitemap.priority, changefreq: block.sitemap.changefreq};

				params = Object.toForm(params);
				
				let fileIndex = 0;

				for(i in self.editing().seo())
				{
					let values = self.editing().seo()[i],
						language = values.language();

					//params.append('content[seo][' + language + '][language]', values.language());
					params.append('content[seo][' + language + '][pageTitle]', values.pageTitle());
					params.append('content[seo][' + language + '][metaTitle]', values.metaTitle());
					params.append('content[seo][' + language + '][metaDescription]', values.metaDescription());
					params.append('content[seo][' + language + '][metaKeywords]', values.metaKeywords());
					params.append('content[seo][' + language + '][ogTitle]', values.ogTitle());
					params.append('content[seo][' + language + '][ogImage]', values.ogImage());
					params.append('content[seo][' + language + '][ogDescription]', values.ogDescription());

					if(values.ogImageFile())
					{
						let file = values.ogImageFile();
						params.append('content[seo][' + language + '][ogImageFile]', fileIndex);
						params.append('ogImageFile[' + fileIndex + ']', file, file['name']);
						fileIndex++;
					}
				}
				

				Edit.call(params, function(response){
					
					unblockLoading('#editSEOPageModalDialog');

					if(response.status != 200)
						return toastr.error(response.error.message);

					$('#editSEOPageModal').modal('hide');
					toastr.success('<su: scope="admin" write="Success">');
					self.load();
				});
			}

		}

		let view = new SEOViewModel();
		ko.applyBindings(view, document.getElementById('main'));
		view.load();



	</script>