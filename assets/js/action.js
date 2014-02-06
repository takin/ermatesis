(function($){

	$.fn.displayMessage = function(opt) {
		var message = null;
		var setup = $.extend({
			message: 'Mohon Menunggu...',
			code: 200
		},opt);

		return this.each(function(){
			$(this).fadeOut(function() {
				$(this).html(setup.message).fadeIn();
			});
		})
	}

	$.fn.clearMessage = function() {
		return this.each(function(){
			$(this).html(' ').fadeOut();
		});
	}

	$.fn.closeMessage = function() {
		return this.each(function(){
			$(this).fadeOut();
		})
	}

	$.fn.getData = function(options) {

		var setup = $.extend({
			url: null,
			method: 'GET',
			dataType: 'json',
			data: null,
			success: function(res) {
				console.log(res);
				if( res.code === 200 ){
					$('.body')
						.hide({
								effect:'drop',
								direction:'right',
								easing:'easeInQuint',
								complete:function() {
											$(this)
												.html(res.data)
												.show({
														effect:'drop',
														direction:'left',
														easing:'easeOutQuint',
														duration:800
												});
								}
						});
				}
			}
		},options);

		$.ajax(setup);
	}

})(jQuery);

$(document)
.on('submit','#cform',function(e){
		e.preventDefault();
		var iter = $('#iter');
		var iterasi = parseInt(iter.val());
		iter.attr('disabled','disabled');
		$(this).getData({url:$(this).attr('action'),data:{iterasi:iterasi}});
})
.on('click','.process',function(e){
		e.preventDefault();
		$(this).getData({url:$(this).attr('href')});
});