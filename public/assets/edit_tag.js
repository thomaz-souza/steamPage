var editTag = {

	list:[],
	listBkp: [],

	start: function ()
	{
		editTag.listBkp = editTag.listBkp.concat(editTag.list);

		let div = document.createElement('div');
		div.className = "edittag-box";
		div.innerHTML = 
			'<div class="edittag-title">Editor de Conteúdo</div>' + 
			'<div class="edittag-box-button edittag-button-reset">Resetar</div>' + 
			'<div class="edittag-box-button edittag-button-editor">Abrir editor</div>' + 
			'<div class="edittag-box-button edittag-button-save">Salvar alterações</div>' + 
			'<div class=""><input type="checkbox" id="edittag_check_highlight" onclick="editTag.highlight(this.checked)"><label for="edittag_check_highlight">Destacar</label></div>';

		$(document.body).append(div);
	},

	highlight : function (check)
	{
		if(check === true)
			return $('[data-editable=true]').addClass('edittag-editablecontent-highlight');

		$('[data-editable=true]').removeClass('edittag-editablecontent-highlight');
	},

	edit : function ()
	{
		event.preventDefault();
		let e = $(this);

		if(e.html().match('edittag-textarea'))
			return;

		let id = e.attr('data-editid');
		let code = e.attr('data-editpagecode');

		let inline_css = 
			'font-size:' + e.css('font-size') + ';' + 
			'color:' + e.css('color') + ';' + 
			'font-weight:' + e.css('font-weight') + ';' + 
			'background:' + e.css('background') + ';' + 
			'background-color:' + e.css('background-color') + ';' + 
			'width:' + (e.width()) + 'px;' + 
			'height:' + (e.height()) + 'px;' + 
			'line-height:' + e.css('line-height') + ';' + 
			//'border:' + e.css('border') + ';' + 
			'font-family:' + e.css('font-family') + ';';

		e.html('<textarea class="edittag-textarea" style=\'' + inline_css + '\' onkeydown="editTag.save(this)" data-editingId="' + id + '" data-editingCode="' + code + '">' + editTag.list[code][id].content + '</textarea>');

		$('[data-editingId=' + id + '][data-editingCode=' + code + ']').focus();

		return false;
	}
	,
	save : function(textarea)
	{
		console.log(event.keyCode);

		if(event.keyCode == 13 || event.keyCode == 27 || event.keyCode == 113)
		{
			event.preventDefault();
			let id = $(textarea).attr('data-editingId');
			let code = $(textarea).attr('data-editingCode');
			
			if(event.keyCode == 13)
				editTag.list[code][id].content = textarea.value;

			if(event.keyCode == 113 && confirm('Tem certeza que deseja voltar ao conteúdo original?'))
				editTag.list[code][id].content = editTag.listBkp[code][id].content;				

			$('[data-editable=true][data-editid='+id+']').html(editTag.list[code][id].content);
		}
	}
}

jQuery(document).ready(function(){
	editTag.start();
	$('[data-editable=true]').contextmenu(editTag.edit);
	$('[data-editable=true]').addClass('edittag-editablecontent');
});

