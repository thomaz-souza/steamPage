$.fn.picupload = function ()
{
	let self = this;

	self.init = function()
	{
		//Pegar nome
		let n = self.attr('name');

		//Criar id do campo
		self._id = n + "_" + Math.ceil(Math.random()*10);

		//Adicionar campo externo
		let ec =
			'<input type="text" style="display:none;" name="' + n + '_changed" value="false" data-picpuload-input="' + self._id + '">' + 
			'<input type="file" style="display:none;" name="'+n+'" data-picpuload-file="' + self._id + '">' +
			'<div class="picupload">' +
			'<div class="picupload-bt" title="Enviar imagem" data-picpuload-upload="'+self._id+'">'+
				'<i class="fas fa-image"></i>'+
			'</div>'+
			'<div class="picupload-bt" title="Retirar imagem" data-picpuload-trash="'+self._id+'">'+
				'<i class="far fa-trash-alt"></i>' + 
			'</div></div>';

		self.parent().append(ec);
		self.css('position', 'relative');
		self.attr('data-picpuload-pic', self._id);

		$('[data-picpuload-trash='+self._id+']').click(self.trash);
		$('[data-picpuload-upload='+self._id+']').click(self.upload);
		$('[data-picpuload-file='+self._id+']').change(self.changedUpload);	
	}

	self.trash = function ()
	{
		$('[data-picpuload-input='+self._id+']').val('true').trigger('change');
		$('[data-picpuload-file='+self._id+']').val('').trigger('change');
		$('[data-picpuload-pic='+self._id+']').css('background-image', '').trigger('change');
	}

	self.upload = function ()
	{
		$('[data-picpuload-file='+self._id+']').click();
	}

	self.changedUpload = function (e)
	{
		let i = $('[data-picpuload-file='+self._id+']');

		if(this.files.length==0) return;
		
		$('[data-picpuload-input='+self._id+']').val('true').trigger('change');
		$('[data-picpuload-pic='+self._id+']').css('background-image', 'url(' + URL.createObjectURL(this.files[0]) + ')').trigger('change');
		
	}

	self.init();
}