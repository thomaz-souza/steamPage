(function ( $ ) { 

	$.fn.dynamic = function (options)
	{
	let self = this;

	//Aplica skeleton Load
	self.skload = function (t)
	{
		let keep = (self._o.infiniteLoader||!self._p.prp),
		pagination = self.pagination(),
		prp = self._p.prp;
		
		if(!t){
			if(keep)
				self.children().slice((pagination ? pagination.totalResults() : self._p.prp)*-1).remove();
			if(!self._o.infiniteLoader)
				self.removeClass('skeleton-load');
			return;
		}
		
		if(!self._o.infiniteLoader)
			self.addClass('skeleton-load');
		
		if(!keep)
			self.clear();	
		
		if(pagination)
		{
			let offset = (pagination.size() - (pagination.currentPage() * pagination.itemsPerPage()));
			prp = (offset > pagination.itemsPerPage()) ? self._p.prp : offset;
		}else{
			prp = self._p.prp;
		}	

		for (let i = 0; i<prp; i++)
			self._template({}, false);
	}

	//Resgatar dados a serem enviados
	self._dt = function ()
	{
		let r = Object.assign({},self._f);
		if(self._p&&self._p.pag&&self._p.prp)
			r = Object.assign(r,self._p);

		return r;
	}

	//Resgata dados da transaction
	self.get = function ()
	{
		if(self._o.skeletonLoad===!0)
			self.skload(!0);

		if(self._o.onLoading)
			self._o.onLoading(!0);

		self._o.transaction(
			self._dt(),
			self.set,
			self._o.onDownload ? self._o.onDownload : null,
			self._o.onUpload ? self._o.onUpload : null,
			self._o.onLoad ? self._o.onLoad : null
		);
		return self;
	}

	self._mod = {

		gen: function(i){
			return '@'+i+'@';
		},
		var: function (html)
		{
			let regex = /\$([0-9a-z_-]+?)(?!\()([^0-9a-z_-])/gi, r, id, results = [];
			while(r = regex.exec(html))
			{
				id = this.gen(r.index);
				html = html.replace(r[0],id+r[2]);
				results.push({ id:id, v:r[1] });
			}
			return {r:results,h:html};
		},
		evl: function (html)
		{
			let regex = /\$\{([\s\S]+?)\}/gi, r, id, results = [];
			while(r = regex.exec(html))
			{
				id = this.gen(r.index);
				html = html.replace(r[0],id);
				results.push({ id:id, e:r[1] });
			}
			return {r:results,h:html};
		},
		eif_r:function()
		{
			return new RegExp(/\$if\(([\s\S]+?)\)\{([\s\S]+?)\}/,'gi')
		},
		eif: function (html)
		{
			let regex = /\$if\(([\s\S]+?)\)\{([\s\S]+?)\}/gi, r, id, results = [], h = html;

			while(r = regex.exec(html))
			{
				id = this.gen(r.index);
				h = h.replace(r[0],id);
				results.push({ id:id, e:r[1], v:r[2] });
			}
			return {r:results,h:h};
		},
		addVar: function(h,i)
		{
			let n;
			for(n of self._t.v.c)
				h = h.replace(n.id,i[n.v]);
			return h;
		},
		eval:function(e,i)
		{
			e = this.addVar(e,i);
			try{ r = eval(e) }catch(e){ console.error(e); r = e}
			return r;
		},
		addEvl: function(h,i)
		{
			let n;
			for(n of self._t.v.e)
				h = h.replace(n.id, this.eval(n.e,i));
			
			return h;
		},
		addEif: function(h,i)
		{
			let n;
			for(n of self._t.v.d)
			{
				r = this.eval(n.e,i) ? this.addVar(n.v,i) : '';
				h = h.replace(n.id, r);
			}
			return h;
		}
	};

	self._parseTemplate = function ()
	{
		let h =self.html(), v = {e:[],c:[],d:[]}, l;		
		//Buscar variáveis		
		var_p = self._mod.var(h);
		h = var_p.h;
		v.c = var_p.r;

		//Buscar funções de eval		
		var_p = self._mod.evl(h);
		h = var_p.h;
		v.e = var_p.r;

		//Buscar funções de eval		
		var_p = self._mod.eif(h);
		h = var_p.h;
		v.d = var_p.r;

		self._t={h:h,v:v};
		self.clear();
	}

	self._template = function (e,i)
	{
		if(!self._o.template)
		{
			let h = self._t.h;
			h = self._mod.addVar(h,e);

			h = self._mod.addEvl(h,e);

			h = self._mod.addEif(h,e);
			
			return self.append(h);
		}

		if(self._o.template)
			return self.append(self._o.template(e,i));
	}

	//Salva dados
	self.set = function (d,i)
	{
		//self.skload(!1);
		if(self._o.onLoading) self._o.onLoading(!1);
		if(d.status==200||i===!0)
		{
			if(self._o.onData)
			{
				newData = self._o.onData(d.data,self);
				if(newData)
					d.data = newData;
			}
			
			self._d = d.data;

			if(self._o.paginate)
			{
				self.pagination(new paginate(d.data,self));

				if(d.data.results)
				{
					self._d = d.data.results;
					delete d.data.results;
					self._p.tot = d.data;
				}

				if(self._o.paginate.onPage)
				{					
					if(typeof self._o.paginate.onPage=="function")
						self._o.paginate.onPage(self.pagination(),self);

					if(typeof self._o.paginate.onPage=="object")
						self.pagination().paginateOn(self._o.paginate.onPage);
				}
			}

			self.plot();
			return self;

			
		}
		if(self._o.onError)
			self._o.onError(d);
		else 
			console.error(d);
	}

	//Criar a iteração do conteúdo
	self.plot = function ()
	{
		if(!self._o.infiniteLoader) self.clear();

		if(self._o.skeletonLoad===!0)
			self.skload(!1);

		if(self._d.length==0&&self._o.onEmpty)
		{
			let m = typeof self._o.onEmpty == "function" ? self._o.onEmpty(self) : self._o.onEmpty;
			if(m) self.html(m);
			return self;
		}

		for(let i=0;i<self._d.length;i++)
			self._template(self._d[i], i);

		if(self._o.onPlot)
			self._o.onPlot(self);

		return self;
	}

	//Limpar e replotar
	self.refresh = function ()
	{
		self.clear();
		return self.plot();
	}

	//Limpar e recarregar
	self.reload = function ()
	{
		self.clear();
		return self.get();
	}

	//Limpa o container
	self.clear = function ()
	{
		self.html('');
		return self;
	}

	self.resetPage = function ()
	{
		self._p.pag = self._o.paginate ? self._o.paginate.currentPage : ((self._p.pag) ? self._p.pag : false);
		self._p.prp = self._o.paginate ? self._o.paginate.perPage : ((self._p.prp) ? self._p.prp : false);
		return self;
	}

	//Resgata página ou muda a página
	self._page = function (pag)
	{
		if(pag===undefined) return self._p.pag;
		self._p.pag = pag;
		return self;
	}

	//Resgata página ou muda a página
	self.page = function (pag)
	{
		let p = self._page(pag);
		if(typeof p == "object")	
			return self.get();
		return p;
	}

	self._nextPage = function ()
	{
		if(self._p.pag+1>self._p.tot.totalPages) return false;
		self._p.pag++;
		return self;
	}

	self.nextPage = function ()
	{
		if(self._nextPage())
			return self.get();
		return self;
	}

	self._previousPage = function ()
	{
		if(self._p.pag-1<1||self._o.infiniteLoader) return false;
		self._p.pag--;
		return self;
	}

	self.previousPage = function ()
	{
		if(self._previousPage())
			return self.get();
		return self;
	}

	self._lastPage = function ()
	{
		return self.page(self._p.tot.totalPages);
	}

	self.lastPage = function ()
	{
		self._lastPage()
		return self.get();
	}

	self._firstPage = function ()
	{
		return self.page(1);
	}

	self.firstPage = function ()
	{
		self._firstPage()
		return self.get();
	}

	self._shiftPage = function (n)
	{
		//Se a soma for maior, retornar última posição
		if(self._p.tot.currentPage+n>self._p.tot.totalPages) 
			return self._lastPage();

		//Se a soma for menor, retornar a primeira posição.
		if(self._p.tot.currentPage+n<1)
			return self._firstPage();

		return self.page(self._p.tot.currentPage + n);
	}

	self.pagination = function(pgnt)
	{
		if(pgnt)
			self._pgnt = pgnt;

		return self._pgnt;
	}

	self.shiftPage = function (n)
	{
		self._shiftPage(n);
		return self.get();
	}

	self._perPage = function (prp)
	{
		if(prp===undefined) return self._p.prp;
		self._p.prp = prp;
		return self;
	}

	self.perPage = function (prp)
	{
		self.resetPage();
		let p = self._perPage(prp);
		if(typeof p == "object")
		{
			return self.reload();
		}
		return p;
	}

	self._setInfiniteLoader = function (t)
	{
		self._o.infiniteLoader=t;			
		return self;
	}

	self.setInfiniteLoader = function (t)
	{
		self._setInfiniteLoader(t);	
		self.resetPage();
		return self.reload();
	}

	self._filter = function (id,val)
	{
		self._f[id] = val;
		if(val===null)
			delete self._f[id];
		return self;
	}

	self.filter = function (id,val)
	{
		self._filter(id,val);
		self.resetPage();
		return self.reload();
	}

	self._order = function (fld,sort)
	{
		let v = [fld,sort];
		if(!fld) v = null;
		return self._filter('order',v);
	}

	self.order = function (fld,sort)
	{
		self._order(fld,sort);
		self.resetPage();
		return self.reload();
	}

	self._search = function (fld,val)
	{	
		let v = [fld,val];
		if(!fld||val==='') v = null;
		return self._filter('search',v);
	}

	self.search = function (fld,val)
	{
		self._search(fld,val);
		self.resetPage();
		return self.reload();
	}

	//Salvar opções
	self._o = options;

	//Filtros
	self._f = self._o.filters ? self._o.filters : {};

	self._p = {
		pag: self._o.paginate && self._o.paginate.currentPage ? self._o.paginate.currentPage : false,
		prp: self._o.paginate && self._o.paginate.perPage ? self._o.paginate.perPage : false
	}

	if(!self._o.template && !self._t)
		self._parseTemplate();

	//Se já houver dados, repassar
	if(self._o.data)
		self.set(self._o,!0);

	//Se não houver dados, resgatá-los da transação
	else
		jQuery(document).ready(function(){ self.get(); });		

	return self;
}
}( jQuery ));