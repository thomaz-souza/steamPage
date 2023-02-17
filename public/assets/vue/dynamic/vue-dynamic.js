const VueDynamic = function(options)
{

	this.data = function () {

		return {

			dynamic: {
				items: (options.data) ? options.data : [],

				options: options,

				//Variáveis de estado
				error: false,
				loading: false,
				loaded: (options.data) ? true : false,
			},

			filters: options.filters ? options.filters : {}
			
		}
	}

	let isFunction = function (fun)
	{
		return (typeof fun == "function");
	}

	this.methods = {

		_set: function(data)
		{
			//Interceptação pelo onData
			if(isFunction(this.dynamic.options.onData))
			{
				let ret = this.dynamic.options.onData(data);
				if(ret) data = ret;
			}

			//Verifica se foi solicitado um dataModel
			if(isFunction(this.dynamic.options.dataModel))
			{
				data = (isFunction(data.map))
					? data.map(i => new this.dynamic.options.dataModel(i))
					: this.dynamic.options.dataModel(data);
			}

			if(this.dynamic.options.infiniteLoader)
			{
				if(this.dynamic.options.skeletonLoad)
				{
					for(let i = this.dynamic.items.length - 1; i>=0; i--)
					{
						if(!this.dynamic.items[i]._skeleton)
							break;

						this.dynamic.items.splice(i,1);
					}
				}

				
				this.dynamic.items = this.dynamic.items.concat(data);
			}
			else
			{
				this.dynamic.items = data;
			}
		},

		_fakePopulate: function()
		{
			let arr = [], pag = (this.hasPagination) ? this.pagination : false;
			let len = (this.dynamic.options.fakeItems) ? this.dynamic.options.fakeItems : (pag) ? pag.itemsPerPage : 0;

			if(this.hasPagination && pag.size && (pag.itemsPerPage * pag.currentPage) > pag.size)	
				len = pag.size - (pag.itemsPerPage * (pag.currentPage - 1));

		  	for (var i = 0; i < len; i++)
		    	arr.push({_skeleton: true});
		    
			this._set(arr);
		},

		reload: function()
		{
			this.dynamic.items = [];

			if(this.pagination && this.pagination.currentPage && this.pagination.currentPage > 1)
				return this.pagination.currentPage = 1;
			
			return this.load(true);
		},

		//Função responsável por carregar o conteúdo
		load: function(ignoreInfiniteLoader) {

			this.dynamic.loading = true;
			this.dynamic.error = false;

			if(this.dynamic.options.infiniteLoader && ignoreInfiniteLoader === true)
				this.dynamic.items = [];
			
			//Se foi solicitado skeletonLoad
			if(this.dynamic.options.skeletonLoad)
				this._fakePopulate();			

			//Dados a serem enviados
			let params = this.filters, 
				self = this;

			if(this.hasPagination)
			{
				params.pag = this.pagination.currentPage
				params.prp = this.pagination.itemsPerPage
			}

			//Caso tenha sido enviado uma transação
			if(isFunction(this.dynamic.options.source))
			{
				this.dynamic.options.source(params, function(response) {

					self.dynamic.loading = false;

					//Se houve erro
					if(response.status != 200)
					{
						self.dynamic.loaded = true;
						self.dynamic.error = response.error;
						return;
					}
					
					//Dados
					let data = response.data;

					//Se houver "results" indica que tem paginação
					if(data.results)
					{
						data = response.data.results;
						delete response.data.results;
						self.pagination = Object.assign(self.pagination, response.data);
					}
					else{
						self.pagination = {};
					}

					self._set(data);
					self.dynamic.loaded = true;
				})
			}

			//Caso tenha enviado uma url
			else if(typeof this.dynamic.options.source == 'string')
			{
				request.run(this.dynamic.options.source, params, function(response){
					self.dynamic.loading = false;
					self._set(response);
					self.dynamic.loaded = true;
				});
			}

		},

		_filter: function(filter, value)
		{
			if(value !== undefined && value !== null)
				return this.filters[filter] = value;
			
			delete this.filters[filter];
		},

		filter: function(filter, value) {

			let self = this;

			if(typeof filter == "array")
				filter.forEach((f,v) => self._filter(f,v));
			else
				this._filter(filter, value);			
			
			this.load(true);
		},

		_order: function(column, order){
			this._filter('order', [column, order ? order : 'ASC']);
		},

		order: function(column, order){
			this._order(column, order);
			this.load(true);
		}

	}

	this.watch = {

		'pagination.currentPage' : function(){
			this.load();
		},

		'pagination.itemsPerPage' : function(){
			this.pagination.currentPage = 1;
			this.load();
		}

	}

	this.computed = {

		isLoading: function(){
			return this.dynamic.loading;
		},

		hasError: function(){
			return this.dynamic.error ? true : false;
		},

		isEmpty: function(){
			return (!this.isLoading && !this.hasError && this.dynamic.items && this.dynamic.items.length == 0)
		},

		getData: function(){
			return this.dynamic.items ? this.dynamic.items : [];
		},

		hasPagination: function(){
			return this.pagination && this.pagination.currentPage ? true : false;
		},

	}

	if(options.components)
		this.components = options.components;

	if(options.el)
		this.el = options.el;

	if(options.mixins)
		this.mixins = options.mixins;

	//Ao montar, carregar os dados
	this.mounted = function(){
		if(!this.dynamic.loaded)
			this.load();
	}
}