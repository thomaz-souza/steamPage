var request = {
	run:function(u,d,c,dl,ul,ol)
	{
		var x = new XMLHttpRequest();
		x.open('POST',u,!0);

		if(typeof d=='object'&& !(d instanceof FormData))
		{
			d = JSON.stringify(d);
			x.setRequestHeader("Content-type", "text/json");
		}
		
		x.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		x.setRequestHeader("Transaction-Token", this.tk);
		if(this.adm) x.setRequestHeader("Admin-Token", this.adm);

		if(typeof dl=="function")
			x.onprogress = dl;

		if(typeof ul=="function")
			x.upload.addEventListener("progress",ul,!1);

		if(typeof ol=="function")
			x.onload = ol;
		else
			x.onload=function(){
				var t = this.responseText;
				if(x.status===0) return request.genErr(523,c,x,t);
				try{t = JSON.parse(t); }catch(e){ console.error(e); return request.genErr(500,c,x,t); }
				if(c) c(t,x);
			}
		x.send(d);
		return x;
	},
	genErr:function(s,c,x,t){ 
		if(c) return c({status:s,error:{message:this.errList.genericError,details:t},local:!0},x); else return;
	},
	tran:function(s,m,a,d,c,dl,ul,ol)
	{
		let v = this.validate(a,d);
		if(v!==!0){ if(c) c(v); throw v; }
		return this.run(this.u+'/transaction/'+s+"/"+m,d,c,dl,ul,ol);
	},
	validate:function(a,d)
	{
		if(typeof a!='object') return this.validateError('invalidMap','','');
		if(a.length==0) return !0;

		if(d instanceof FormData)
		{
			let tmp={},i,nm;
			for(i of d.entries())
			{
				nm = i[0].replace(/\[\]/gi,'');

				if(tmp[nm] && typeof tmp[nm]!='object' || tmp[nm] && !tmp[nm].length ){
					tmp[nm] = [tmp[nm],i[1]];
				}else if(tmp[nm] && typeof tmp[nm]=='object'){
					tmp[nm].push(i[1]);
				}else{
					tmp[nm] = i[1];
				}
			}
			d = tmp;
		}
		for(i in a)
		{
			let av = this.avail(d[i],a[i],i);
			if(av!==!0) return av;
		}
		return true;
	},
	avail: function(d,r,i)
	{
		if(r['ignore']&&r['ignore']===!0)
			return true;

		fn = r['name'] ? r['name'] : i;
		if(r['mandatory'] && !d && !r['defaultValue'])
			return this.validateError('mandatory',fn,i);
		if(!r['mandatory'] && !d)
			return true;

		if(r['type'] && r['type'] == 'file')
			return this.availFile(d,r,i);

		if(r['type'] && typeof d != r['type'] )
			return this.validateError('type', fn, i);
		if(typeof d == 'object')
		{
			if(r['minArrayCount'] && d.length < r['minArrayCount'])
				return this.validateError('minArrayCount', fn, i);
			if(r['maxArrayCount'] && d.length > r['maxArrayCount'])
				return this.validateError('maxArrayCount', fn, i);
			if(r['map']){
				v = this.validate(r['map'], d);
				if(v!==true) return v;
			}
			return true;
		}
		if(typeof d!='string'||r['replaceBefore'])
			return true;
		if(r['match'] && !d.match(r['match']))
			return this.validateError('match', fn, i);
		if(r['notMatch'] && d.match(r['notMatch']))
			return this.validateError('notMatch', fn, i);
		if(r['minLength'] && d.length < r['minLength'])
			return this.validateError('minLength', fn, i);
		if(r['maxLength'] && d.length > r['maxLength'])
			return this.validateError('maxLength', fn, i);
		return true;
	},
	availFile: function(d,r,i)
	{
		if(r['ignore']&&r['ignore']===!0)
			return true;

		if(!d.length) d=[d];
		fn=r['name'] ? r['name'] : i;
		
		if(r['mandatory']&&d.length == 0)
			return this.validateError('mandatoryFile', fn, i);

		if(!r['mandatory']&&d.length == 0)
			return true;

		if(r['mutiple']&&r['mutiple']===false&&d.length > 1)
			return this.validateError('multiple', fn, i);
		
		if(r['maxMutiple']&&d.length > r['maxMutiple'])
			return this.validateError('maxMutiple', fn, i, r['maxMutiple']);

		if(r['minMutiple']&&d.length > r['minMutiple'])
			return this.validateError('minMutiple', fn, i, r['minMutiple']);

		fs=0;

		for(fl=0;fl<d.length;fl++)
		{
			if(r['maxSize']&&d[fl].size > r['maxSize'])
				return this.validateError('maxSize', fn, i, this.fileByte(r['maxSize']), d[fl].name);

			if(r['minSize']&&d[fl].size < r['minSize'])
				return this.validateError('minSize', fn, i, this.fileByte(r['minSize']), d[fl].name);
			
			fs+=d[fl].size;

			if(r['format'])
				if( (typeof r['format'] == "object" && r['format'].indexOf(d[fl].type) == -1) || (typeof r['format'] != "object" && r['format'] != d[fl].type) )
					return this.validateError('format', fn, i, null, d[fl].name);

			x = d[fl].name.match(/\.([^\.]+)$/gi);

			if(r['extension'])
				if ((!x || !x[0]) || ( typeof r['extension'] == "object" && r['extension'].indexOf(x[0].replace('.','')) == -1) || (typeof r['extension'] != "object"&&r['extension']!=x[0].replace('.','')))
					return this.validateError('extension', fn, i, null, d[fl].name);

		}
		if(r['maxSizeAll']&&fs > r['maxSizeAll'])
				return this.validateError('maxSizeAll', fn, i, this.fileByte(r['maxSizeAll']));

		if(r['minSizeAll']&&fs < r['minSizeAll'])
			return this.validateError('minSizeAll', fn, i, this.fileByte(r['minSizeAll']));
		
		return true;
	},
	fileByte:function(b)
	{
		if (b === 0) return '0B';
	    const k = 1024;
	    const dm = 2 < 0 ? 0 : 2;
	    const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
	    const i = Math.floor(Math.log(b) / Math.log(k));
	    return parseFloat((b / Math.pow(k, i)).toFixed(dm)) + sizes[i];
	},
	validateError:function(o,fn,i,v,fl)
	{
		ms=this.errList[o].replace('%s',v?v:fn);
		return {error: {message:ms,code:o,details:{details:i,object:fn,file:fl}},local:true,status: 400};
	},
	parseForm:function(f,c,t,dl,ul,ol)
	{
		if(event) event.preventDefault();
		let fd = new FormData();

		for(let i=0;i<f.elements.length;i++)
		{
			el = f.elements[i];
			if(el.type == 'file')
			{
				for(let l=0;l<el.files.length;l++)
					fd.append(el.name, el.files[l], el.files[l].name);
				continue;
			}

			if(el.type == 'checkbox'||el.type == 'radio')
			{
				if(el.checked)
					fd.append(el.name, el.value);

				if(!el.checked)
				{
					u = el.getAttribute('data-value-unchecked');
					if(typeof u == "string") fd.append(el.name, u);
				}
				continue;
			}

			if(el.type == 'select-multiple' || (el.type == 'select' && el.getAttribute('multiple')))
			{
				for(let o=0;o<el.options.length;o++)
				{
					opt = el.options[o];
					if(opt.selected)
						fd.append(el.name, (opt.value || opt.text));
					
				}
				continue;
			}

			fd.append(el.name, el.value);
		}

		if(t) t(fd,c,dl,ul,ol);
		else if(c && typeof c=="function") c(fd);
		else return fd;
		return false;
	},
	checkResponse: function (response)
	{
		if(response.status == 200)
			return true;
		if(response.error && response.error.details && response.error.details.object)
			this.showError(response.error.details.object, response.error.message);
		return false;
	},
	showError: function(name, error, scroll)
	{
		let e = $('[name="'+name+'"]');

		e.addClass('input-error');
		if(error)
			e.parent().append('<p class="input-error-msg">'+error+'</p>');
		
		let self = this;			
		let clear = function(){ self.clearError(name) };

		e.on('input', clear);
		e.on('change', clear);

		if((scroll !== false && window.innerWidth < 1000) || scroll === true)
			 $([document.documentElement, document.body]).animate({
		        scrollTop: e.offset().top - 200
		    }, 500);
	},
	clearError: function (name)
	{
		if(name)
		{
			let e = $('[name="'+name+'"]');
			e.removeClass('input-error');
			return e.parent().find('.input-error-msg').remove();
		}
		$('.input-error').removeClass('input-error');
		$('.input-error-msg').remove();
	},
	formatURL: function(url)
	{
		if(request.u)
		{
			return request.u + '/' + url
				.replace(/^\~/gi, '')			//Remove til inicial
				.replace(/^\//gi, '')			//Remove barra inicial
				.replace(/[\/]{2,}/gi, '/');	//Remove barras duplicadas
		}
		
		console.error("You must include a controller to use this resource");
		return url;
	},
	require: function(url)
	{
		//Buscar formato da URL (js ou css)
		let tag, format = /\.(js|css)([\?]{1}[\s\S]+?){0,}$/gi.exec(url);

		//Se não houver formato válido, ignorar
		if(!format) return console.error("Invalid file to include");

		if(format[1] == 'js')
		{
			tag = document.createElement('script');
			tag.src = url;
		}
		else if(format[1] == 'css')
		{
			tag = document.createElement('link');
			tag.rel = 'stylesheet';
			tag.href = url;
		}

		document.head.append(tag);
	},
	requirePublic: function(path)
	{
		let url = request.formatURL("public/" + path);
		return request.require(url);
	},
	requireAsset: function(asset)
	{
		return request.requirePublic("assets/" + asset);
	},
	TM:function(m,f,a)
	{
		var s = this;
		s.url = function(){
			return request.u + "/transaction/"+m+"/"+f;
		}
		s.method = function(){
			return m;
		}
		s.function = function(){
			return f;
		}
		s.map = function(){
			return a;
		}
		s.call = function(d,c,w,u,o){
			return request.transaction(m,f,a,d,c,w,u,o);
		}
		s.validate = function(d)
		{
			return request.validate(a,d);
		}
	}
}