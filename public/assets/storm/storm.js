$.fn.storm = function (options)
{
	let self = this;

	self.get = function ()
	{
		self.tran('select', self.set);		
	}

	self.set = function (d)
	{
		if(d.status==200)
		{
			if(self._o.onData)
			{
				newData = self._o.onData(d.data,self);
				if(newData) d.data = newData;
			}
			self._d = d.data;
			self.plot();
			return self;
		}
		else
		{
			if(self._o.onError)
				self._o.onError(d, self);
			console.error(d);
		}
	}

	self.plot = function()
	{
		let i,e;
		for(i in self._d)
		{
			//Verificar se os resultados são arrays
			if(typeof self._d[i] == "array" || typeof self._d[i] == "object")
			{
				//Verificar se existem campos prontos para receber Arrays
				if(!self._e[i+"[]"]) continue;
				e = self._e[i+"[]"];				

				if(e.type == "checkbox" || e.type == "radio")

					$(e.element).each(function(n,ee){
						if(self._d[i].indexOf(ee.value)!=-1)
						{
							$(ee).prop('checked', true);
							$(ee).prop('selected', true);
						}else
						{
							$(ee).prop('checked', false);
							$(ee).prop('selected', false);
						}
					});
				
				$(e.element).val(self._d[i]);

				continue;
			}

			if(!self._e[i]) continue;
			
			e = self._e[i];
			
			if(e.type=='checkbox')
				if(e.element.value==self._d[i])
					e.element.checked = true;
				else
					e.element.checked = false;

			else if(e.type=='radio')
			{
				let rd = self[0].querySelectorAll('input[type=radio][name='+i+']');

				for(k=0;k<rd.length;k++)
				{
					if(rd[k].value == self._d[i])
						rd[k].checked = true;
				}
			}


			else if(e.type == 'file' && $(e.element).attr('data-picpuload-file'))
				$('[data-picpuload-pic='+$(e.element).attr('data-picpuload-file')+']').css('background-image','url('+self._d[i]+')');

			else if(e.type == 'file');

			else
				$(e.element).val(self._d[i]);
		}
		self.parseElements();
		if(self._o.onSet) self._o.onSet(!1);
		if(self._o.onLoading) self._o.onLoading(!1);
	}

	self.getChanged = function (k)
	{
		let l = {};
		for(i in self._e)
			if(self.hasChanged(i))
				l[i] = self._e[i];
		return l;		
	}

	self.hasChanged = function (k)
	{
		let e = self._e[k],
		v = self.getValue(e.element),
		t = typeof v;

		if(t == "string" || t=="boolean")
			return e.value != v;
		
		else if(t == "array"||t == "object")
			return JSON.stringify(v)!=JSON.stringify(e.value);
	}

	self.getValue = function (e)
	{
		let t = $(e).prop('type'), n = $(e).prop('name');

		if(t=="checkbox" && n.indexOf('[]') !=-1 )
		{
			var l = [];
			$('[name="'+n+'"]').each(function(i,ee){
				if(ee.checked) l.push(ee.value);
			});
			return l;
		}

		if(t=="checkbox")
		{
			let u = $(e).attr('data-value-unchecked');
			return e.checked ? e.value : (u?u:!1);
		}
		if(t=="radio")
		{
			rd = self[0].querySelectorAll('input[type=radio][name='+n+']:checked');
			return (rd.length>0) ? rd[0].value : '';
		}
		return $(e).val();
	}

	self.parseElements = function ()
	{
		//Elementos do formulário
		let e = self[0].elements, d = null, t = null;
		self._e = self._e ? self._e : {};
		for(i of e)
		{
			$(i).removeClass('hasError');
			$(i).on('input', self.watch);
			$(i).change(self.watch);

			let element = i;

			if(i.name.indexOf('[]') != -1)
			{
				if(self._e[i.name])
				{
					self._e[i.name].element.push(i);
					continue;
				}
				else
				{
					element = [i];
				}
			}
			let v = self.getValue(i);
			self._e[i.name] = {	
				'type':$(i).prop('type'),
				'element': element,
				'value': v,
				'default': self._e[i.name] ? self._e[i.name].default : v
			}
		}
		self.watch();
	}

	self.toDefault = function ()
	{
		let l = {}, e;
		for(let i in self._e)
		{
			e = self._e[i];
			i = i.replace(/\[\]$/gi,'');
			l[i] = e.default;
		}
		self._o.keyValue = null;
		self._d = l;
		self.plot();
		if(self._o.onReset) self._o.onReset(self);
	}

	self.watch = function ()
	{
		if(Object.keys(self.getChanged()).length>0)
		{
			if(self._o.submitButton)
				self._o.submitButton.prop('disabled',!1);
		}
		else
		{
			if(self._o.submitButton)
				self._o.submitButton.prop('disabled',!0);
		}
	}

	self.submit = function ()
	{
		if(self._o.submitButton) self._o.submitButton.prop('disabled',!1);
		if(self._o.onLoading) self._o.onLoading(!0);
		/*let f = document.createElement('form');
		for(i in self._e)
			$(f).append($(self._e[i].element).clone());*/
		
		let fd = request.parseForm(self[0]);
		
		if(typeof self._o.keyValue == "string" || typeof self._o.keyValue == "number")
		{
			fd.append(self._o.key, self._o.keyValue);
			fd.append("_key", self._o.key);
			fd.append("_action", "update");
			
			self.tran('update', self.submitted, fd);
		}
		else
		{
			fd.append("_action", "insert");
			self.tran('insert', self.submitted, fd);
		}
	}

	self.submitted = function (response)
	{
		self.parseElements();
		if(self._o.onLoading) self._o.onLoading(!1);
		if(response.status!=200)
		{
			if(self._o.submitButton) self._o.submitButton.prop('disabled',!1);
			if(self._o.onError) return self._o.onError(response, self);
			if(response.error&&response.error.details&&response.error.details.details)
			{
				$.growlUI(response.error.message);
				$('[name="'+response.error.details.details+'"]').addClass('hasError');
			}

			return;
		}
		if(self._o.onSubmitted) self._o.onSubmitted(response,self);
	}

	self.delete = function (kv)
	{
		if(typeof kv == "string") self._o.keyValue = kv;

		if(typeof self._o.keyValue != "string")
			return false;

		return self.tran('delete', self.deleted);
	}

	self.deleted = function (response)
	{
		self.reset();
		if(self._o.onLoading) self._o.onLoading(!1);
		if(self._o.onDeleted) self._o.onDeleted(response,self);
	}

	self.loadKey = function (k)
	{
		self.reset();
		self._o.keyValue = k;
		self.get();
	}

	self.refresh = function ()
	{
		if(self._o.onLoading) self._o.onLoading(!0);
		self.plot();
	}

	self.reload = function ()
	{
		self.get();
	}

	self.reset = function ()
	{
		self._o.keyValue = null;
		for(n=0;n<Object.keys(self._e).length;n++)
		{
			k = Object.keys(self._e)[n];
			e = self._e[k];

			if(e.type=='checkbox')
				$(e.element).prop('checked',false);

			else if(e.type == 'file' && $(e.element).attr('data-picpuload-file'))
				$('[data-picpuload-trash='+$(e.element).attr('data-picpuload-file')+']').click();

			else if(e.type == 'file');

			else
				e.element.value = '';
		}
		self.parseElements();
		if(self._o.onReset) self._o.onReset(self);
	}

	self.tran = function (y,c,fd)
	{
		let d;

		if(fd)
		{
			d = fd;
			if(self._o.filters)
				for(f in self._o.filters)
					d.append('_filters['+f+']', self._o.filters[f]);	
		}else{
			d = {
				"_key": self._o.key,
				"_action" : y,
				[self._o.key]: self._o.keyValue,
				"_filters": self._o.filters ? self._o.filters : {}
			}
		}

		let tran = self._o.transaction;

		if(y == "delete" && self._o.transactionDelete)
			tran = self._o.transactionDelete;

		else if(y == "select" && self._o.transactionSelect)
			tran = self._o.transactionSelect;

		else if(y == "update" && self._o.transactionUpdate)
			tran = self._o.transactionUpdate;

		else if(y == "insert" && self._o.transactionInsert)
			tran = self._o.transactionInsert;

		if(self._o.onLoading) self._o.onLoading(!0);

		tran(
			d,
			c,
			self._o.onDownload ? self._o.onDownload : null,
			self._o.onUpload ? self._o.onUpload : null,
			self._o.onLoad ? self._o.onLoad : null
		);
	}

	//Opções do formulário
	self._o = options;

	self.parseElements();

	if(self._o.submitButton)
		self._o.submitButton.click(self.submit);

	//Se já houver dados, incluir
	if(self._o.data) self.set(self._o.data);

	//Senão, puxá-los do backend
	else if(self._o.keyValue)
		self.get();

	return self;
}