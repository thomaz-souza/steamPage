const urlr = {
	getParams: function(obj)
	{
		let params = new URLSearchParams(location.search);
		if(!obj) return params;
		let list = [];

		for(i of params.entries())
			list[i[0]] = i[1];

		return list;
	},
	getParam: function(key) {		
		return this.getParams().get(key);
	},
	setParam: function(key, value)
	{
		let params = this.getParams();
		params.set(key, value);
		return this.update(params);
	},
	removeParam: function(key)
	{
		if(!this.getParam(key)) return;
		let params = this.getParams();
		params.delete(key);
		return this.update(params);
	},
	update: function(params) {
		params = params ? params.toString() : null;
		window.history.replaceState({}, '', params ? `${location.pathname}?${params}` : location.pathname);
	},
	_serialize: function (form, params, parent)
	{
		parent = parent ? parent : [];

		for (const key in params) {
			if (params.hasOwnProperty(key)) {

				if(typeof params[key] == 'object')
				{
					form = this._serialize(form, params[key], parent.concat(key));
					continue;
				}
			
				let name = parent.concat([key]);
				let hiddenField = document.createElement('input');
				hiddenField.type = 'hidden';
				hiddenField.name = name.map(function(a, i){ return i == 0 ? a : '[' + a + ']'; }).join('');
				hiddenField.value = params[key];
				form.appendChild(hiddenField);
			}
		}

		return form;
	},
	form: function (path, params, method, target)
	{
		let form = document.createElement('form');
		form.action = path;
		form.method = method ? method : 'post';
		form.target = target ? target : '_blank';

		if(params)
			form = this._serialize(form, params);

		document.body.appendChild(form);
		return form.submit();
	},
	post: function (path, params, target)
	{
		return urlr.form(path, params, 'post', target);
	}
}