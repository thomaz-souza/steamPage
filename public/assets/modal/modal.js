(function ( $ ) { 
	$.fn.modal = function()
	{
		let self = this;

		this.open = function(){
			$(self).css('opacity', 1);
			$(self).css('display', 'flex');
		}

		this.close = function(){
			$(self).css('opacity', 0);
			setTimeout(function(){ $(self).hide(); }, 210);
		}

		$(this).find('[data-modal="close"]').click(function(){
			self.close();
		});

		this.block = function(){
			$(self).addClass('blocked');
		}

		this.unblock = function(){
			$(self).removeClass('blocked');
		}

		if(this.attr('id'))
			$('[data-modal-open="' + this.attr('id') + '"]').click(function(){
				self.open();
			});
		return self;
	}
}( jQuery ));

$('.modal').each(function(){ $(this).modal() });