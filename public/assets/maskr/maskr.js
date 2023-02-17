const Maskr = {
	
	map: function(){
		$('[data-maskr]').each(function(){
			let e = $(this);
			let update = e.attr('data-maskr-update'), mask = e.attr('data-maskr');
			e.on(update ? update : 'input', function(){
				Maskr.call(e[0], mask);
			});
			if(!update)
				e.on('change', function(){
					Maskr.call(e[0], mask);
				});
		});
	},
	call: function(e, mask){
		Maskr[mask](e);
	},
	_isinput:function(e)
	{
		return (typeof e == 'object') && e.value !== null;
	},
	_val: function(e)
	{
		if(this._isinput(e))
			return e.value ? e.value : '';
		
		return e===null ? '' : e;
	},
	_set:function(v,e){
		if(this._isinput(e) && e.value != v)
		{
			e.value = v;
			e.dispatchEvent(new Event('input'));
		}
		else
		{
			return v;
		}
	},
	mask: function(v,m) {
		v = v.split("");
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
		return this._set(this.mask(v,m),e);
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

		v = this.mask(v,msk);
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
jQuery(document).ready(function(){  Maskr.map(); });