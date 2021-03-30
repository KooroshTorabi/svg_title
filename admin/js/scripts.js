(function($) {
	'use strict';
	var makerjs = require('makerjs');

	$(function() {
		var welcomePanel = $('#welcome-panel');
		var updateWelcomePanel;

		function svgtUpdateWelcomePanel(visible) {
			$.post(ajaxurl, {
				action: 'svgt-update-welcome-panel',
				visible: visible,
				welcomepanelnonce: $('#welcomepanelnonce').val()
			});
		};

		$('a.welcome-panel-close', welcomePanel).click(function(event) {
			event.preventDefault();
			welcomePanel.addClass('hidden');
			svgtUpdateWelcomePanel(0);
		});

		if ($('#title').val() === '') {
			$('#title').focus();
		}

		$('#svgt-admin-form-element').submit( function() {
			if (this.action.value != 'copy') {
				$(window).off('beforeunload');
			}

			if (this.action.value == 'save') {
				$('#publishing-action .spinner').addClass('is-active');
			}
		});

		$('.color-field').wpColorPicker({
	    	change: function(e, u) {
	    		var t = u.color._color.toString(16);
	    		if (t.length < 6) {
	    			t = '0'.repeat(6 - t.length) + t;
	    		}
	    		$('#' + e.target.id).val('#' + t);
	    		redraw();
	    	},
	    	clear: function(e) {
	    		$('#' + e.target.id).val('#000000');
	    		$('#' + e.target.id).val('transparent');
	    	},
	    	mode: 'hsv'
	    });
	    if ($('#font').length > 0) getFonts();
	    titleHint();
	});

	function titleHint() {
		var $title = $('#title');
		var $titleprompt = $('#title-prompt-text');

		if ($title.val() === '') {
			$titleprompt.removeClass('screen-reader-text');
		}

		$titleprompt.click(function() {
			$(this).addClass('screen-reader-text');
			$title.focus();
		});

		$title.blur(function() {
			if ($(this).val() === '') {
				$titleprompt.removeClass('screen-reader-text');
			} else {
				redraw();
			}
		}).focus(function() {
			$titleprompt.addClass('screen-reader-text');
		}).keydown(function(e) {
			$titleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
	};

	$('[type="text"]').keydown(function(e){
		if(e.which == 13) {
			$(this).blur();
			return false;
		}
	})

	$('#size, .aspeed, #strokew').keydown(function(e){
		if((e.which >= 48 && e.which <= 57)
			|| (e.which >= 96 && e.which <= 105)
			|| e.which == 37
			|| e.which == 39
			|| e.which == 46
			|| e.which == 8
			|| e.which == 116
			|| e.which == 9
		) {} else {
			if (e.which == 190 || e.which == 110) {
				if ($(this).val().indexOf('.') != -1) {
					return false;
				}
			} else {
				return false;
			}
		}
	})
	$('#size').blur(function(){
		if($(this).val() == '') $(this).val('16');
		else checkZero($(this));
	})
	$('.aspeed').blur(function(){
		if($(this).val() == '') $(this).val('0');
		else checkZero($(this));
	})
	$('#strokew').blur(function(){
		if($(this).val() == '') $(this).val('1');
		else checkZero($(this));
	})

    $('#font').change(function() {
    	getVariant(0);
    });

    $('#variant, #size, .aspeed, #strokew').change(function(){
    	checkZero($(this));
    	redraw();
    })

    $('#subset').change(function(){
    	if ($(this).val() != 'all') {
    		$('#font option').hide();
    		$('#font option').each(function(e) {
	    		if($(this).attr('data-subsets').indexOf($('#subset').val()) != -1)$(this).show();
    		});
    	} else {
    		$('#font option').show();
    	}
    })

    function checkZero(el)
    {
		if (el.val().indexOf('.') == 0) {
			el.val('0' + el.val());
		}
		el.val(parseFloat(el.val()));
    }

	var fontList = '';
	var subsets = new Array('all');
	var linePathLgt = '100%';
    function getFonts() {
    	var apiKey = config.MY_API_KEY;
    	$.get('https://www.googleapis.com/webfonts/v1/webfonts', {key: apiKey}, function(data) {//console.log(data);
    		fontList = data;
    		for (var i in fontList.items) {
    			$('#font').append('<option value="' + i + '" data-subsets="' + fontList.items[i].subsets + '">' + fontList.items[i].family + '</option>');
				for(var j in fontList.items[i].subsets) {
					if ($.inArray(fontList.items[i].subsets[j], subsets) == -1) {
						subsets.push(fontList.items[i].subsets[j]);
					};
				}
    		}
    		$('#font').val($('#saved-font').val());
    		getVariant($('#saved-variant').val());
    		getSubset();
    	})
    }

    function getVariant(e) {
    	var curelm = $('#font').val();
    	$('#variant').html('');
    	for (var i in fontList.items[curelm].variants) {
    		$('#variant').append('<option value="' + i + '">' + fontList.items[curelm].variants[i] + '</option>');
    	}
    	$('#variant').val(e);
    	redraw();
    }

    function getSubset() {
    	$('#subset').html('');
    	for (var i in subsets) {
    		$('#subset').append('<option value="' + subsets[i] + '">' + subsets[i] + '</option>');
    	}
    }

    function redraw() {
		if ( $('#title').val().length > 0) {
			var aspeed = [$('#aspeed1').val(), $('#aspeed2').val(), $('#aspeed3').val(), $('#aspeed4').val()];

	        var f = fontList.items[$('#font').val()];
	        var v = f.variants[$('#variant').val()];
	        var url = f.files[v];

	        opentype.load(url, function (err, font) {

				var svgdata = correctSVG(font);
				svgdata.setAttribute('id', 'svgId-' + $('#post_ID').val());

				if(aspeed[1] * 1 > 0) {
					var anim = document.createElementNS("http://www.w3.org/2000/svg", 'animate');
					anim.setAttribute('id', 'p1');
					anim.setAttribute('attributeName', 'stroke-dashoffset');
					anim.setAttribute('begin', aspeed[0] + 's');
					anim.setAttribute('values', linePathLgt + '; 0%;');
					anim.setAttribute('dur', aspeed[1] + 's');
					anim.setAttribute('repeatCount', '1');
					anim.setAttribute('fill', 'freeze');
					anim.setAttribute('calcMode', 'linear');
					svgdata.querySelector('g').querySelector('path').appendChild(anim);
					svgdata.setAttribute('data-aspeed', aspeed[1]);

					if(aspeed[3] * 1 > 0) {
						var anifill = document.createElementNS("http://www.w3.org/2000/svg", 'animate');
						anifill.setAttribute('id', 'p2');
						anifill.setAttribute('attributeName', 'fill-opacity');
						//anifill.setAttribute('begin', 'p1.end-' + (aspeed / 2.0) + 's');
						anifill.setAttribute('begin', aspeed[2] + 's');
						anifill.setAttribute('values', '0; 1;');
						anifill.setAttribute('dur', aspeed[3] + 's');
						anifill.setAttribute('repeatCount', '1');
						anifill.setAttribute('fill', 'freeze');
						anifill.setAttribute('calcMode', 'linear');

						svgdata.querySelector('g').querySelector('path').setAttribute('fill-opacity', '0%');
						svgdata.querySelector('g').querySelector('path').appendChild(anifill);
					} else {
						svgdata.querySelector('g').querySelector('path').setAttribute('fill-opacity', '100%');
					}
				} else {
					svgdata.querySelector('g').querySelector('path').setAttribute('fill-opacity', '100%');
					svgdata.querySelector('g').querySelector('path').setAttribute('stroke-dashoffset', "0%");
				}
	            var strdata = new XMLSerializer().serializeToString(svgdata);

	            $('#svg-render').html('');
	            $('#svg-render').append(svgdata);
	            $('#data').text(strdata);
	        });
	    }
    }

    function correctSVG(font) {

		var title = $('#title').val();
		var size = $('#size').val();
		var stroke = $('#color1').val();
		var strokew = $('#strokew').val();
		var fill = $('#color2').val();

		var textModel = new makerjs.models.Text(font, title, size, false, false);
        var svg = makerjs.exporter.toSVG(textModel, {stroke: stroke});

        var oParser = new DOMParser();
		var ss = oParser.parseFromString(svg, "image/svg+xml");
		var svgroot = ss.documentElement;

        var wdt = svgroot.getAttribute('width') * 1 + strokew * 1;
        var hgt = svgroot.getAttribute('height') * 1 + strokew * 1;

		svgroot.setAttribute('width', wdt);
		svgroot.setAttribute('height', hgt);
		svgroot.setAttribute('viewBox', (-strokew / 2) + ' ' + (-strokew / 2) + ' ' + wdt + ' ' + hgt);

        svgroot.querySelector('g').removeAttribute('style');
		svgroot.querySelector('g').removeAttribute('font-size');
		svgroot.querySelector('g').removeAttribute('stroke-width');
		svgroot.querySelector('g').removeAttribute('fill');
		svgroot.querySelector('g').removeAttribute('id');

		//svgroot.querySelector('g').removeAttribute('fill-rule');

        svgroot.querySelector('g').querySelector('path').setAttribute('stroke-width', strokew);

		svgroot.querySelector('g').querySelector('path').setAttribute('stroke', stroke);

        svgroot.querySelector('g').querySelector('path').setAttribute('fill', fill);

        linePathLgt = svgroot.querySelector('g').querySelector('path').getTotalLength();

		svgroot.querySelector('g').querySelector('path').setAttribute('stroke-dasharray', linePathLgt);
		svgroot.querySelector('g').querySelector('path').setAttribute('stroke-dashoffset', linePathLgt);

		var t = document.createElementNS("http://www.w3.org/2000/svg", 'title');
		var txt1 = document.createTextNode(title);
		t.appendChild(txt1);
		svgroot.appendChild(t);
		var d = document.createElementNS("http://www.w3.org/2000/svg", 'desc');
		var txt2 = document.createTextNode(title);
		d.appendChild(txt2);
		svgroot.appendChild(d);

		return svgroot;
    }
})(jQuery);
