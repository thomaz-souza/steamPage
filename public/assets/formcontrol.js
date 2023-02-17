var FormControl = function(d){
	let t = this;
	if(!d) return;
	t.setData(d);
	t.setFields();
	t.start();
}

FormControl.prototype.setData = function(d)
{
	if(d){
		this.rawData = Object.assign({},d);
		this.data = Object.assign({},d);
	}else{
		this.rawData = {};
		this.data = {};
	}
}

FormControl.prototype.setFields = function()
{
	t = this;
	t.fields = [];
	$('[data-bind-value]').each(function(){
		t.fields.push($(this).attr('data-bind-value'));
		$(this).on('input', function(v){ t.ch(this,t,v); });
		$(this).change(function(v){ t.ch(this,t,v); });
	});
	$('[data-bind-checked]').each(function(){
		t.fields.push($(this).prop('data-bind-checked'));
		$(this).change(function(v){ t.ch(this,t,v); });
	});
	$('[data-bind-text]').each(function(){
		$(this).html(t.get($(this).attr('data-bind-text')));
	});
}

FormControl.prototype.start = function()
{
	let t = this;
	for(let i=0; i< t.fields.length; i++)
	{
		let k = t.fields[i];
		if(t.rawData[k])
		{
			t.set(k, t.rawData[k]);
			continue;
		}
		
		if($('[data-bind-value=' + k + ']').length > 0)
			t.set(k, $('[data-bind-value=' + k + ']').val());
		t.set(k, $('[data-bind-checked=' + k + ']').prop('checked'));
	}
}

FormControl.prototype.get = function(f)
{
	let t = this;
	if(f) return t.data[f];
	return t.data;
}

FormControl.prototype.process = function(c,b,dl,ul,ol)
{
	t = this;
	fm = document.createElement('form');
	fd = t.getChangedFields();



	for(let i in fd)
	{
		k = fd[i];

		$('[data-bind-value=' + k + ']').each(function(){
			let e = this; e.name = k;
			fm.appendChild(e.cloneNode(true));	
		});
		
	}
	return request.parseForm(fm,c,b,dl,ul,ol);
}

FormControl.prototype.getChanged = function()
{
	t = this;
	l = {};
	for(let k in t.data)
	{
		if(t.changed(k))
			l[k] = t.data[k];
	}
	return l;
}

FormControl.prototype.getChangedFields = function()
{
	t = this;
	l = [];
	for(let k in t.data)
	{
		let e = $('[data-bind-value=' + k + ']');
		if(e.length==0)
			e = $('[data-bind-checked=' + k + ']');
		if(t.changed(k) || e.attr('type') == "hidden" )
			l.push(k);
	}
	return l;
}

FormControl.prototype.set = function(k,d)
{
	t.data[k] = d;
	$('[data-bind-value=' + k + ']').val(d);
	$('[data-bind-text=' + k + ']').text(d);
	$('[data-bind-checked=' + k + ']').prop('checked',d);
}

FormControl.prototype.ch = function(e,t,v)
{
	if($(e).attr('data-bind-checked'))
		return t.set($(e).attr('data-bind-checked'), $(e).prop('checked'));
	t.set($(e).attr('data-bind-value'), e.value);
}

FormControl.prototype.changed = function(c)
{
	if(this.rawData[c] != this.data[c])
		return !0;
	return !1;
}


var EditPicture = function (e,p)
{
	if(p)
		this.setUploadPath(p);
	
	this.e = $('#'+e);
	this.en = e;
	this.init();
}

EditPicture.prototype.init = function()
{	
	let el = $('#'+this.en);
	//Pegar atributos
	let attr_name = this.e.attr('name');

	this.id = attr_name + "_" + Math.ceil(Math.random()*10);

	//Adicionar campo externo
	let ec =
		'<input type="hidden" name="'+attr_name+'_changed" value="false" data-edtpic-input="'+this.id+'">' + 
		'<input type="file" style="display:none;" name="'+attr_name+'" data-edtpic-file="'+this.id+'">';
	let pr = $(this.e).parent();
	pr.html(pr.html() + ec);

	bt = '<div class="edtpic">' +
	'<div class="edtpic-bt" data-edtpic-upload="'+this.id+'">'+
		'<i class="fas fa-image"></i>'+
	'</div>'+
	'<div class="edtpic-bt" data-edtpic-trash="'+this.id+'">'+
		'<i class="far fa-trash-alt"></i>' + 
	'</div></div>';

	$('#'+this.en).css('position', 'relative');
	$('#'+this.en).attr('data-edtpic-pic', this.id);
	$('#'+this.en).append(bt);

	$('[data-edtpic-trash]').click(this.trash);
	$('[data-edtpic-upload]').click(this.upload);
	$('[data-edtpic-file]').change(this.changedUpload);	
}

EditPicture.prototype.trash = function ()
{
	let id = $(this).attr('data-edtpic-trash');
	$('[data-edtpic-input='+id+']').val('true');
	$('[data-edtpic-file='+id+']').val('');
	$('[data-edtpic-pic='+id+']').css('background-image', '');
}

EditPicture.prototype.upload = function ()
{
	id = $(this).attr('data-edtpic-upload');
	$('[data-edtpic-file='+id+']').click();
}

EditPicture.prototype.changedUpload = function ()
{
	id = $(this).e.attr('data-edtpic-file');

	console.log(id);

	$('[data-edtpic-input='+id+']').val('true');
	$('[data-edtpic-pic='+id+']').css('background-image', 'url(' + URL.createObjectURL(this.files[0]) + ')');
}
