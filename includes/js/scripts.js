jQuery(function($){
	var svgT = $('.svgt-wrapper');
	svgT.each(function(e){
		checkView($(this));
	});

	$(window).scroll(function(){
		svgT.each(function(e){
			checkView($(this));
		})
	})

	function checkView(el) {
		if(!el.hasClass('started')) {
			var elTop = el.offset().top;
			var elBottom = elTop + el.outerHeight();
			var viewTop = $(window).scrollTop();
			var viewBottom = viewTop + $(window).height();
			if(elBottom > viewTop && elTop < viewBottom) {
				el.addClass('started');
				startAnim(el);
			}
		}
	}

	function startAnim(el) {
		//console.log('Opacity to ' + el.find('svg').attr('id'));
		var t = el.find('svg').clone();
		el.html('');
		t.css('opacity', '1');
		el.append(t);
	}
})