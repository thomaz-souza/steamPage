//Correção para que um modal possa sobrepor o outro
jQuery(document).ready(function(){

	$(document).on('show.bs.modal', '.modal', function (event) {
        var zIndex = 1040 + (10 * $('.modal:visible').length);
        $(this).css('z-index', zIndex);
        setTimeout(function() {
            $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
        }, 0);
    });

	//Correção para um modal possa sobrepor o outro
    $(document).on('hidden.bs.modal', '.modal', function () {
	    $('.modal:visible').length && $(document.body).addClass('modal-open');
	});

	$(document).ready(function(){
		$('[data-toggle="kt-tooltip"]').tooltip();
	})
});