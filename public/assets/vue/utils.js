//Cria várias instâncias de um model baseado num conjunto (array) de valores
function iterate (values, model)
{
	let array = [];
	for(let v in values)
		array.push(new model(values[v]));
	return array;
}

//Insere os dados de uma fonte em uma nova instância de um model
function define (model, source, vars)
{
	source = is.object(source) ? source : {};

	for(let v in vars)
	{
		let def = vars[v], und = !is.undefined(source[v]);
		
		if(is.function(def))
			model[v] = und ? new def(source[v]) : new def();

		else if(is.array(def) && def.length == 1 && is.function(def[0]))
			model[v] = und ? iterate(source[v], def[0]) : [];

		else if(is.array(def))
			model[v] = und ? (is.array(source[v]) ? source[v] : [source[v]]) : def;

		else
			model[v] = und ? source[v] : def;
	}

	if(!model._original && is.object(model))
		Object.defineProperty(model, '_original', {
			get: function() { return source }
		});
}

Object.parametrize = function(object){
	return JSON.parse(JSON.stringify(object));
}

Object.toForm = function(object, form, scope)
{
	if(!form)
		form = new FormData();

	for(let e in object)
	{
		let val = object[e];
		
		if(scope)
			e = scope + '[' + e + ']';

		if(is.function(val) || (val === null)) continue;
		
		if(val instanceof FileList)
			form.append(e, val[0], val[0].name);

		else if(is.array(val) || is.object(val))
			form = Object.toForm(val, form, e);

		else
			form.append(e, val);
	}

	return form;
}