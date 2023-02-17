/**------------------------------------------------------------------- COMPARADOR IS */

const is = {

	undefined: function (v) {
		return (typeof v == 'undefined');
	},

	string: function(v){
		return (typeof v == 'string');
	},

	obj: function (v) {
		return (typeof v == 'object') && !is.null(v);
	},

	null: function (v){
		return v === null;
	},

	array: function (v) {
		return Object.prototype.toString.call(v) == '[object Array]';
	},

	object: function (v) {
		return Object.prototype.toString.call(v) == '[object Object]';
	},

	number: function (v){
		return (typeof v == 'number');
	},	

	function: function(v){
		return (typeof v == 'function');
	},

	DOMNode: function(v){
	 	return is.function(Node) ? (v instanceof Node) : (v && is.obj(v) && is.number(v.nodeType) && is.string(v.nodeName));	  
	},

	DOMElement: function(v){
	 	return is.function(HTMLElement) ? (v instanceof HTMLElement) : (v && is.obj(v) && v.nodeType === 1 && is.string(v.nodeName));	  
	},

	isoDate: function(v){
		if(!v) return false;
		return v.match(/^[0-9]{4}\-[0-1]{1}[0-9]{1}\-[0-3]{1}[0-9]{1}/gi) ? true : false;
	}
}

/**------------------------------------------------------------------- MASCARA (Maskr) */
const Maskr = {

	map: function(){
		for(let e of document.querySelectorAll('[data-maskr]'))
		{
			let update = e.getAttribute('data-maskr-update'), mask = e.getAttribute('data-maskr');
			e.addEventListener(update ? update : 'input', function(e){
				Maskr.call(e.target, mask);
			});
		};
	},
	call: function(e, mask, vue){
		Maskr._vue = vue ? true : false;
		if(Maskr[mask])
			return Maskr[mask](e);
		else
			return Maskr.mask(e,mask);
	},
	_isinput:function(e)
	{
		return is.DOMElement(e) && (typeof e.value !== 'undefined');
	},
	_val: function(e)
	{
		if(this._isinput(e))
			return e.value ? e.value : '';
		
		return e===null ? '' : e;
	},
	_set:function(v,e)
	{
		if(this._isinput(e) && e.value != v)
		{
			e.value = v;
			if(!Maskr._vue)
				e.dispatchEvent(new Event('input'));
		}
		Maskr._vue = false;
		return v;
	},
	_mask: function(vo,m) {
		v = vo.split("");
		m = m.split("");
		let n = '', i, c=0;

		for(i=0; i<m.length; i++)
		{
			if(c>v.length-1) break;

			if(m[i] == "#")
			{
				n += v[c];
				c++;
				continue;
			}
			else if(m[i] == "?")
			{
				n += vo.substr(c);
				break;
			}
			n += m[i];
		}
		return n;
	},
	_num: function(v) {

		return v=='' ? '' : v.replace(/[^\d]+/gi, "");
	},
	_apply: function(e,m,o)
	{
		let v = this._val(e);
		if(o) v = this[o](v);
		return this._set(this._mask(v,m),e);
	},
	mask: function (e,m,p)
	{
		return this._apply(e, m, p);
	},
	creditCard: function(e)
	{
		return this._apply(e, '#### #### #### ####', '_num');
	},
	monthYear: function(e)
	{
		return this._apply(e, '##/####', '_num');
	},
	date: function(e)
	{
		return this._apply(e, '##/##/####', '_num');
	},
	dateTime: function(e)
	{
		return this._apply(e, '##/##/#### ##:##:##', '_num');
	},
	amount: function(e)
	{
		let v = this._num(this._val(e)), msk = "#";

		v = v.replace(/^0{1,}/gi, "");

		if(v.length == 1)
			msk = "0,0#";

		else if(v.length == 2)
			msk = "0,##";

		else if(v.length > 2)
			msk = '#'.repeat(v.length-2) + ",##";

		v = this._mask(v,msk);
		return this._set(v,e);
	},
	number: function(e)
	{
		return this._set(this._num(this._val(e)),e);		
	},
	cnpj: function(e)
	{
		return this._apply(e, '##.###.###/####-##', '_num');
	},
	cpf: function(e)
	{
		return this._apply(e, '###.###.###-##', '_num');
	},
	cpfcnpj: function(e)
	{
		let v = this._num(this._val(e));
		if(v.length < 11)
			return this.number(e);

		return (v.length == 11) ? this.cpf(e) : this.cnpj(e);
	},
	cep: function(e){
		return this._apply(e, '#####-###', '_num');
	},
	chaveNf:function(e)
	{
		return this._apply(e, '#### #### #### #### #### #### #### #### #### #### ####', '_num');
	},
	phone: function(e)
	{
		let v = this._num(this._val(e));
		msk = (v.length < 11) ? '(##) ####-####' : '(##) # ####-####';
		return this._apply(e, msk, '_num');
	},
	completePhone: function(e)
	{
		let v = this._num(this._val(e));
		return this._apply(e, '+#?', '_num');
	},
	noSpace: function(e)
	{
		let v = this._val(e);
		return this._set(v.replace(/\s/g,''), e);
	},
	email: function(e)
	{
		let v = this._val(e);
		v = v.replace(/\s/g,'').toLowerCase();
		return this._set(v, e);
	}

};

//Mapear todos os dados
document.addEventListener("DOMContentLoaded", function(){ Maskr.map(); });


/**------------------------------------------------------------------- CONVERSOR DE FORMATOS (Convert) */

const Convert = {

	currency: {
		number: function(n,d){
			let nu = Convert.number.string(n);
			nu = nu.replace(/[^\d\.\,]/gi, "");
			let reg = new RegExp("([^0-9]+)[0-9]{"+(d?d:'1,')+"}$", "gi");
			let match = reg.exec(nu);

			if(match && match[1])
				nu = nu.replace(match[1], "&").replace(/[^\d\&]+/gi, "").replace("&", ".");

			return Convert.string.number(nu);
		}
	},

	number: {
		currency: function(n,y,d,l){
			return Number(n).toLocaleString(l, {minimumFractionDigits: d ? d : 2, maximumFractionDigits: d ? d : 2, currency:y?y:'BRL', style:'currency', currencyDisplay: 'symbol'});
		},
		string: function(n)
		{
			return n ? n.toString() : '0';
		}
	},

	string: {

		number: function(n){
			return Number(n);
		},

		date: function (n){
			let s = n.replace(/[^\d]/g, "-").replace("--", "-");
			while(s.match(/[-]{2}/gi))
				s = s.replace("--", "-");
			s = s.split("-");
    		return new Date(s[0], s[1]-1, s[2], s[3] ? s[3] : 0, s[4] ? s[4] : 0, s[5] ? s[5] : 0);
		}
	},

	localDateTime: {

		date: function(n) {
			let s = n.replace(/[^\d]/g, "-").replace("--", "-");
			while(s.match(/[-]{2}/gi))
				s = s.replace("--", "-");
			s = s.split("-");
    		return new Date(s[2], s[1]-1, s[0], s[3] ? s[3] : 0, s[4] ? s[4] : 0, s[5] ? s[5] : 0);			
		}

	},

	date: {

		_n: function(n)
		{
			return is.string(n) ? Convert.string.date(n) : n;
		},

		localDate: function(d, local, options){
			return Convert.date._n(d).toLocaleDateString(local, options);
		},

		localTime: function(d, local, options){
			return Convert.date._n(d).toLocaleTimeString(local, options);
		},

		localDateTime: function(d, local, options){
			return Convert.date._n(d).toLocaleString(local, options);
		},

	}
}

/**------------------------------------------------------------------- MANIPULADOR DE URL (URLR) */

const Urlr = {
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

//Normalizador
const urlr = Urlr;

/**------------------------------------------------------------------- MASCARA (Maskr) */

if(typeof Vue !== 'undefined')
{
		
	const action = function(b){
		const handler = function(e)
		{
			Maskr.call(e.target, b, true);
		}
		return handler;		
	};

	//Criar diretiva
	Vue.directive('maskr', {

		bind: function(element, binding)
		{
			let handler = function(e)
			{
				Maskr.call(e.target, binding.value, true);
			}

			element.addEventListener('input', handler, true);
			Maskr.call(element, binding.value, true);
		},
		update: function(element, binding)
		{
			Maskr.call(element, binding.value, true);
		}

	});

	//Criar filtro
	Vue.filter('Maskr', function(value, mask){
		return Maskr[mask](value);
	});

	//Cria diretiva para incorporar URL
	Vue.directive('url', 
	{
		bind (el, options) 
		{
			let url = request.formatURL(options.value);
			for(prop in options.modifiers)
				el.setAttribute(prop, url);
		},
		componentUpdated (el, options) 
		{
			let url = request.formatURL(options.value);
			for(prop in options.modifiers)
			{
				if(el.getAttribute(prop) != url)
					el.setAttribute(prop, url);
			}
		}
	});

/**------------------------------------------------------------------- CONVERSOR DE FORMATOS (Convert) */

	//Criar diretiva
	Vue.directive('convert', {

		bind: function(element, binding)
		{
			let func = Convert, name = binding.value.split('.'), n;

			for(n of name)
				func = func[n];

			element.value = func(element.value)
		}

	});

	//Criar filtro
	Vue.filter('Convert', function(value, converter)
	{
		let func, n, c;

		if(!is.array(converter))
			converter = [converter];

		for(c of converter)
		{
			func = Convert;

			for(n of c.split('.'))
				func = func[n];

			value = func(value);
		}

		return value;
	});

}

/**------------------------------------------------------------------- CÁLCULO DE HORA E TEMPO */

Date.prototype.addDate = function(days, month, year)
{
    var date = new Date(this.valueOf());
    if(days)
    	date.setDate(date.getDate() + days);
   	if(month)
    	date.setMonth(date.getMonth() + month);
    if(year)
    	date.setFullYear(date.getFullYear() + year);
    return date;
}

Date.prototype.addTime = function(hour, minute, second)
{
    var date = new Date(this.valueOf());
    if(hour)
    	date.setHours(date.getHours() + hour);
   	if(minute)
    	date.setMinutes(date.getMinutes() + minute);
    if(second)
    	date.setSeconds(date.getSeconds() + second);
    return date;
}